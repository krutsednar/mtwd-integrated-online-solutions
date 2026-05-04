<?php

namespace App\Imports;

use App\Models\Statement;
use App\Models\Account;
use App\Models\SmsReport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use DateTimeInterface;

class StatementsImport implements ToModel, WithBatchInserts, WithChunkReading, WithHeadingRow, WithValidation, SkipsOnFailure, WithUpserts
{
    use Importable, SkipsFailures;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // 1. Pre-fetch Account to avoid N+1 database queries
        $account = Account::where('accmasterlist', $row['accountno'])->first();
        $mobile = $account?->mobile;

        // 2. Determine Classification
        $classification = match((int) $row['consumertype']) {
            1       => 'RESIDENTIAL',
            2       => 'COMMERCIAL',
            3       => 'COMMERCIAL A',
            4       => 'COMMERCIAL B',
            5       => 'COMMERCIAL C',
            6       => 'INDUSTRIAL',
            7       => 'GOVERNMENT',
            8       => 'TEMPORARY SERVICE',
            default => 'RESIDENTIAL',
        };

        // 3. Calculate Maintenance Fee (mf)
        $msize = (int) $row['metersize'];
        $mf = match($msize) {
            1       => 20,
            2       => 30,
            3       => 40,
            4       => 60,
            5, 8, 9 => 80,
            default => 0,
        };

        // 4. Senior Citizen Discount (scd) and Franchise Tax (ft)
        $address = $row['address'];
        if (Str::endsWith($address, '-S-') && $row['cum'] <= 30) {
            $scd = $row['billamount'] * 0.05;
            $ft = ($row['billamount'] - $scd) * 0.02;
        } else {
            $scd = 0;
            $ft = $row['billamount'] * 0.02;
        }

        // 5. Arrears, Advance Payments, and Penalties
        $arrears = $row['arrears'] >= 0 ? $row['arrears'] : 0;
        $advancepayment = $row['arrears'] < 0 ? $row['arrears'] : 0;

        // Re-calculating penalty based on current bill amount minus discount
        $penalty = ($row['billamount'] - $scd) * 0.15;

        // Calculate Totals
        $beforedue = $row['billamount'] + $mf + $ft + $arrears + abs($row['othercharges']) - abs($scd) - abs($advancepayment);

        // If arrears are negative (advance), the 'after due' is usually not applicable/zero
        $afterdue = ($row['arrears'] < 0) ? 0 : ($beforedue + $penalty);

        // 6. Persistence Logic
        DB::connection('kitdb')->statement('SET FOREIGN_KEY_CHECKS=0;');

        Statement::updateOrCreate(
            ['account_number' => $row['accountno']],
            [
                'account_name'            => $row['name'],
                'address'                 => $address,
                'classification'          => $classification,
                'reading_date'            => $row['billdate'] ? Carbon::createFromFormat('m/d/Y', $row['billdate'])->format('m/d/Y') : null,
                'due_date'                => $row['duedate'] ? Carbon::createFromFormat('m/d/Y', $row['duedate'])->format('m/d/Y') : null,
                'previous_reading_cum'    => $row['prevrdg'],
                'present_reading_cum'     => $row['presrdg'],
                'consumption_cum'         => $row['cum'],
                'current_bill'            => $row['billamount'],
                'maintenance_fee'         => $mf,
                'franchise_tax'           => $ft,
                'arrears'                 => $arrears,
                'other_charges'           => abs($row['othercharges']),
                'advance_payment'         => abs($advancepayment),
                'senior_citizen_discount' => abs($scd),
                'amount_before_due_date'  => $beforedue,
                'penalty'                 => $penalty,
                'amount_after_due_date'   => $afterdue,
                'months_in_arrears'       => $row['arrearcount'],
                'paid'                    => 'UNPAID',
                'transmitted'             => 'NO',
            ]
        );

        DB::connection('kitdb')->statement('SET FOREIGN_KEY_CHECKS=1;');

        // 7. SMS Notification Staging
        // Only stage if mobile exists, is 10 digits, and there is a balance to pay
        if (!empty($mobile) && strlen($mobile) === 10 && $beforedue > 0) {
            SmsReport::create([
                'account_number'    => $row['accountno'],
                'mobile'            => $mobile,
                'amount_before_due' => $beforedue,
                'due_date'          => $row['duedate'] ? Carbon::createFromFormat('m/d/Y', $row['duedate'])->format('m/d/Y') : null,
                'status'            => 'Unsent',
            ]);
        }

        return null; // ToModel expects a model or null if manually handled
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
