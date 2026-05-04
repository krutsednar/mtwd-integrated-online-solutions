<?php

namespace App\Imports;

use App\Models\Account;
use App\Models\SmsReport;
use App\Models\Statement;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use \DateTimeInterface;

class StatementsImport implements ToModel, ShouldQueue, WithBatchInserts, WithChunkReading, WithHeadingRow, WithValidation, SkipsOnFailure, WithUpserts, WithEvents
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        // C6 fix: use match() — original used = (assignment) so every row became RESIDENTIAL
        $classification = match ((int) $row['consumertype']) {
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

        // W-D fix: default 0 prevents undefined-variable warning for unknown meter sizes
        $mf = match ((int) $row['metersize']) {
            1       => 20,
            2       => 30,
            3       => 40,
            4       => 60,
            5, 8, 9 => 80,
            default => 0,
        };

        $address = $row['address'];

        if (Str::endsWith($address, '-S-') && $row['cum'] <= 30) {
            $scd = $row['billamount'] * 0.05;
            $ft  = ($row['billamount'] - ($row['billamount'] * 0.05)) * 0.02;
        } else {
            $scd = 0;
            $ft  = $row['billamount'] * 0.02;
        }

        if ($row['arrears'] >= 0) {
            $arrears        = $row['arrears'];
            $advancepayment = 0;
            $penalty        = ($row['billamount'] - $scd) * 0.15;
            $beforedue      = $row['billamount'] + $mf + $ft + $arrears + $row['othercharges'] - abs($scd);
            $afterdue       = $beforedue + $penalty;
        } else {
            $arrears        = 0;
            $advancepayment = $row['arrears'];
            $penalty        = 0;
            $beforedue      = 0;
            $afterdue       = $row['billamount'] + $mf + $ft + $row['othercharges'] - abs($scd) - abs($advancepayment);
        }

        // Recompute $abd consistently (matches original semantics where beforedue=0 on advance payment)
        $abd = $row['billamount'] + $mf + $ft + $arrears + $row['othercharges'] - abs($scd) - abs($advancepayment);

        // W-A fix: FK checks moved to registerEvents() — removed from per-row loop
        Statement::updateOrCreate(
            ['account_number' => $row['accountno']],
            [
                'account_name'            => $row['name'],
                'address'                 => $address,
                'classification'          => $classification,
                'reading_date'            => $row['billdate']
                    ? Carbon::createFromFormat('m/d/Y', $row['billdate'])->format('Y-m-d')
                    : null,
                'due_date'                => $row['duedate']
                    ? Carbon::createFromFormat('m/d/Y', $row['duedate'])->format('Y-m-d')
                    : null,
                'previous_reading_cum'    => $row['prevrdg'],
                'present_reading_cum'     => $row['presrdg'],
                'consumption_cum'         => $row['cum'],
                'current_bill'            => $row['billamount'],
                'maintenance_fee'         => $mf,
                'franchise_tax'           => $ft,
                'arrears'                 => $arrears,
                'other_charges'           => $row['othercharges'],
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

        // W-B fix: single Account query per row (was 4 redundant queries)
        $mobile = Account::where('accmasterlist', $row['accountno'])
            ->whereNotNull('mobile')
            ->value('mobile');

        if ($mobile && strlen($mobile) === 10 && $abd >= 0) {
            $dueDate = $row['duedate']
                ? Carbon::createFromFormat('m/d/Y', $row['duedate'])
                : null;

            SmsReport::create([
                'account_number'    => $row['accountno'],
                'mobile'            => $mobile,
                'amount_before_due' => $abd,
                'due_date'          => $dueDate
                    ? ($row['arrears'] > 0
                        ? $dueDate->subDays(10)->format('m/d/Y')
                        : $dueDate->format('m/d/Y'))
                    : null,
                'status'            => 'Unsent',
            ]);
        }
    }

    // W-A fix: FK checks disabled/enabled once per import, not once per row
    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function () {
                DB::connection('kitdb')->statement('SET FOREIGN_KEY_CHECKS=0;');
            },
            AfterImport::class => function () {
                DB::connection('kitdb')->statement('SET FOREIGN_KEY_CHECKS=1;');
            },
        ];
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
