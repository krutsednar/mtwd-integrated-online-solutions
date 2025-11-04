<?php

namespace App\Imports;

use App\Models\Statement;
use App\Models\Account;
use App\Models\SmsReport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Validation\Rule;
use \DateTimeInterface;
use DateTime;
use DB;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Illuminate\Support\Facades\Http;

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
        //classification
        $classification = match ((int) $row['consumertype']) {
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

        //maintenance fee
        $mf = match ((int) $row['metersize']) {
            1 => 20,
            2 => 30,
            3 => 40,
            4 => 60,
            5, 8, 9 => 80,
            default => 0,
        };

        //senior discount and franchise tax
        $address = $row['address'];
        if (Str::endsWith($address, '-S-') && ($row['cum'] <= 30)) {
            $scd = $row['billamount'] * 0.05;
            $ft = ($row['billamount'] - $scd) * 0.02;
        } else {
            $scd = 0;
            $ft = $row['billamount'] * 0.02;
        }

        //arrears, penalties, after dues, before dues and advance payments

        if ($row['arrears'] >= 0){
            $arrears = $row['arrears'];
            $advancepayment = 0;
            $penalty = ($row['billamount'] - $scd) * .15;
            $afterdue = $row['billamount'] + $mf + $ft + $arrears + $row['othercharges'] - abs($scd) - abs($advancepayment) + $penalty;
            $beforedue = $row['billamount'] + $mf + $ft + $arrears + $row['othercharges'] - abs($scd) - abs($advancepayment);
        } elseif ($row['arrears'] < 0){
            $arrears = 0;
            $advancepayment = $row['arrears'];
            $penalty = 0;
            $beforedue = 0;
            $afterdue = 0;
        }

        //penalties
        $penalty = ($row['billamount'] - $scd) * .15;

        DB::connection('kitdb')->statement('SET FOREIGN_KEY_CHECKS=0;');
        $statement = Statement::updateOrCreate(
            ['account_number' => $row['accountno']],
            [
                    'account_name'              => $row['name'],
                    'address'                   => $address,
                    'classification'            => $classification,
                    'reading_date'              => $row['billdate'] ? \Carbon\Carbon::createFromFormat('m/d/Y', $row['billdate'])->format('m/d/Y') : null,
                    'due_date'                  => $row['duedate'] ? \Carbon\Carbon::createFromFormat('m/d/Y', $row['duedate'])->format('m/d/Y') : null,
                    'previous_reading_cum'      => $row['prevrdg'],
                    'present_reading_cum'       => $row['presrdg'],
                    'consumption_cum'           => $row['cum'],
                    'current_bill'              => $row['billamount'],
                    'maintenance_fee'           => $mf,
                    'franchise_tax'             => $ft,
                    'arrears'                   => $arrears,
                    'other_charges'             => $row['othercharges'],
                    'advance_payment'           => abs($advancepayment),
                    'senior_citizen_discount'   => abs($scd),
                    'amount_before_due_date'    => $row['billamount'] + $mf + $ft + $arrears + $row['othercharges'] - abs($scd) - abs($advancepayment),
                    'penalty'                   => $penalty,
                    'amount_after_due_date'     => $afterdue,
                    'months_in_arrears'         => $row['arrearcount'],
                    'paid'                      => trim(strtoupper('UNPAID')),
                    'transmitted'               => trim(strtoupper('NO')),
                ]);
                DB::connection('kitdb')->statement('SET FOREIGN_KEY_CHECKS=1;');

                $abd = $row['billamount'] + $mf + $ft + $arrears + $row['othercharges'] - abs($scd) - abs($advancepayment);

                if((!empty(Account::where('accmasterlist', $row['accountno'])->value('mobile')) OR !is_null(Account::where('accmasterlist', $row['accountno'])->value('mobile'))) AND ($abd >= 0) AND ($row['arrears'] <= 0))
                {
                    if(strlen(Account::where('accmasterlist', $row['accountno'])->whereNotNull('mobile')->value('mobile')) === 10)
                    {
                        SmsReport::create(
                            [
                                    'account_number'          => $row['accountno'],
                                    'mobile'                => Account::where('accmasterlist', $row['accountno'])->whereNotNull('mobile')->value('mobile'),
                                    'amount_before_due'     => $abd,
                                    'due_date'              => $row['duedate'] ? \Carbon\Carbon::createFromFormat('m/d/Y', $row['duedate'])->format('m/d/Y') : null,
                                    'status'                => 'Unsent',
                                ]);
                    }
                } elseif ((!empty(Account::where('accmasterlist', $row['accountno'])->value('mobile')) OR !is_null(Account::where('accmasterlist', $row['accountno'])->value('mobile'))) AND ($abd >= 0) AND ($row['arrears'] > 0))
                {
                    if(strlen(Account::where('accmasterlist', $row['accountno'])->whereNotNull('mobile')->value('mobile')) === 10)
                    {
                    SmsReport::create(
                        [
                                'account_number'          => $row['accountno'],
                                'mobile'                => Account::where('accmasterlist', $row['accountno'])->whereNotNull('mobile')->value('mobile'),
                                'amount_before_due'     => $abd,
                                'due_date'              => $row['duedate'] ? \Carbon\Carbon::createFromFormat('m/d/Y', $row['duedate'])->subDays(10)->format('m/d/Y') : null,
                                'status'                => 'Unsent',
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
