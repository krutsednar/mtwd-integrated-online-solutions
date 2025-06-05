<?php

namespace App\Imports;

use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new User([
            'employee_number' => $row['employee_number'],
            // 'name' => $row['name'],
            'first_name' => $row['first_name'],
            'middle_name' => $row['middle_name'],
            'last_name' => $row['last_name'],
            'suffix' => $row['suffix'],
            'birthday' => $row['birthday'],
            'mobile_number' => $row['mobile_number'],
            'address' => $row['address'],
            'is_approved' => 1,
            'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'password' => bcrypt($row['password']),


        ]);
    }
}
