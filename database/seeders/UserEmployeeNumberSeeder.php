<?php

namespace Database\Seeders;

use App\Models\UserEmployeeNumber;
use Illuminate\Database\Seeder;

class UserEmployeeNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employeeNumbers = array(
            array(
                'user_id'         => 1,
                'employee_number' => '0000000275',
                'status'          => 1,
                'registry_date'   => '2022-07-12'
            ),
            array(
                'user_id'         => 2,
                'employee_number' => 'J1901012221',
                'status'          => 1,
                'registry_date'   => '2023-12-12'
            ),
            array(
                'user_id'         => 2,
                'employee_number' => 'J2301012233',
                'status'          => 1,
                'registry_date'   => '2022-07-12'
            ),
        );

        foreach ($employeeNumbers as $employeeNumber) {
            UserEmployeeNumber::insert([
                'user_id'           => $employeeNumber['user_id'],
                'employee_number'   => $employeeNumber['employee_number'],
                'status'            => $employeeNumber['status'],
                'registry_date'     => $employeeNumber['registry_date'],
            ]);
        }
    }
}
