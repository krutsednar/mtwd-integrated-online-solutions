<?php

namespace App\Imports;

use App\Models\Statement;
use App\Models\Account;
use App\Models\SmsReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DateTimeInterface;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Validation\Rule;

class StatementsImport implements
    ToModel,
    WithBatchInserts,
    WithChunkReading,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure,
    WithUpserts,
    ShouldQueue
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        // ✅ Fix: use == instead of =
        $class = $row['consumertype'] ?? null;
        $classification = match ((int)$class) {
            1 => 'RESIDENTIAL',
            2 => 'COMMERCIAL',
            3 => 'COMMERCIAL A',
            4 => 'COMMERCIAL B',
            5 => 'COMMERCIAL C',
            6 => 'INDUSTRIAL',
            7 => 'GOVERNMENT',
            8 => 'TEMPORARY SERVICE',
            default => 'UNKNOWN',
        };

        // ✅ Maintenance fee by meter size
        $msize = (int)($row['metersize'] ?? 0);
        $mf = match ($msize) {
            1 => 20,
            2 => 30,
            3 => 40,
            4 => 60,
            5, 8, 9 => 80,
            default => 0,
        };

        // ✅ Senior discount and franchise tax
        $address = $row['address'] ?? '';
        $billAmount = (float)($row['billamount'] ?? 0);
        $cum = (float)($row['cum'] ?? 0);

        if (Str::endsWith($address, '-S-') && $cum <= 30) {
            $scd = $billAmount * 0.05;
            $ft = ($billAmount - $scd) * 0.02;
        } else {
            $scd = 0;
            $ft = $billAmount * 0.02;
        }

        // ✅ Handle arrears, advance payment, penalty, due amounts
        $arrearsVal = (float)($row['arrears'] ?? 0);
        $otherCharges = (float)($row['othercharges'] ?? 0);
        $penalty = ($billAmount - $scd) * 0.15;

        if ($arrearsVal >= 0) {
            $arrears = $arrearsVal;
            $advancePayment = 0;
        } else {
            $arrears = 0;
            $advancePayment = abs($arrearsVal);
        }

        $beforeDue = $billAmount + $mf + $ft + $arrears + $otherCharges - $scd - $advancePayment;
        $afterDue = $beforeDue + ($arrearsVal >= 0 ? $penalty : 0);

        // ✅ Convert dates safely
        $readingDate = !empty($row['billdate']) ? Carbon::createFromFormat('m/d/Y', $row['billdate'])->format('Y-m-d') : null;
        $dueDate = !empty($row['duedate']) ? Carbon::createFromFormat('m/d/Y', $row['duedate'])->format('Y-m-d') : null;

        // ✅ Save Statement
        DB::connection('kitdb')->statement('SET FOREIGN_KEY_CHECKS=0;');

        $statement = Statement::updateOrCreate(
            ['account_number' => $row['accountno']],
            [
                'account_name'              => $row['name'],
                'address'                   => $address,
                'classification'            => $classification,
                'reading_date'              => $readingDate,
                'due_date'                  => $dueDate,
                'previous_reading_cum'      => $row['prevrdg'] ?? 0,
                'present_reading_cum'       => $row['presrdg'] ?? 0,
                'consumption_cum'           => $cum,
                'current_bill'              => $billAmount,
                'maintenance_fee'           => $mf,
                'franchise_tax'             => $ft,
                'arrears'                   => $arrears,
                'other_charges'             => $otherCharges,
                'advance_payment'           => $advancePayment,
                'senior_citizen_discount'   => $scd,
                'amount_before_due_date'    => $beforeDue,
                'penalty'                   => $penalty,
                'amount_after_due_date'     => $afterDue,
                'months_in_arrears'         => $row['arrearcount'] ?? 0,
                'paid'                      => 'UNPAID',
                'transmitted'               => 'NO',
            ]
        );

        DB::connection('kitdb')->statement('SET FOREIGN_KEY_CHECKS=1;');

        // ✅ Create SMS Report (skip duplicates)
        $account = Account::where('accmasterlist', $row['accountno'])->first();
        $mobile = $account?->mobile;

        if ($mobile && strlen($mobile) === 10) {
            $smsDueDate = $dueDate;

            if ($arrearsVal > 0 && $dueDate) {
                $smsDueDate = Carbon::parse($dueDate)->subDays(10)->format('m/d/Y');
            }

            // Prevent duplicate Unsent entries for same account + due date
            $exists = SmsReport::where('account_number', $row['accountno'])
                ->where('status', 'Unsent')
                ->whereDate('due_date', Carbon::parse($smsDueDate))
                ->exists();

            if (!$exists) {
                SmsReport::create([
                    'account_number'    => $row['accountno'],
                    'mobile'            => $mobile,
                    'amount_before_due' => $beforeDue,
                    'due_date'          => $smsDueDate,
                    'status'            => 'Unsent',
                ]);
            }
        }
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function rules(): array
    {
        return [
            'co_activity' => Rule::in(['Read']),
        ];
    }

    public function uniqueBy()
    {
        return 'account_number';
    }
}
