<?php

namespace App\Imports;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToModel, ShouldQueue, WithHeadingRow, WithChunkReading, WithBatchInserts, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return User::updateOrCreate(
            ['employee_number' => $row['employee_number']],
            [
                'first_name'        => $row['first_name'],
                'middle_name'       => $row['middle_name'] ?? null,
                'last_name'         => $row['last_name'],
                'suffix'            => $row['suffix'] ?? null,
                'birthday'          => $row['birthday'] ?? null,
                'mobile_number'     => $row['mobile_number'] ?? null,
                'address'           => $row['address'] ?? null,
                'division_id'       => $row['division_id'] ?? null,
                'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'password'          => Hash::make($row['password']),
                // is_approved intentionally omitted — admin reviews imported users via approval workflow
            ]
        );
    }

    public function rules(): array
    {
        return [
            'employee_number' => 'required|string',
            'first_name'      => 'required|string',
            'last_name'       => 'required|string',
            'password'        => 'required|string|min:8',
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function batchSize(): int
    {
        return 500;
    }
}
