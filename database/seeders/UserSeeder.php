<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = array(
            array(
                'uuid'              => Str::uuid(),
                'name'              => 'Abigail Soleman',
                'company_id'        => 1,
                'department_id'     => 1,
                'date_of_birth'     => '1993-08-27',
                'identity_card'     => '3213032708930010',
                'gender'            => 'female',
                'religion'          => 'Katolik',
                'email'             => 'wings@wings.com',
                'education'         => 'S1',
                'status'            => 1,
                'marital_status'    => "Belum Menikah",
                'address'           => 'BTN PUSKOPAD SUKAJAYA, RT/RW 59/17, CIGADUNG, SUBANG',
                'phone'             => '08993663995',
                'employee_type'     => 'Staff',
                'section'           => 'PRD',
                'position_code'     => 'KRB',
                'status_twiji'      => 'INTIJI',
                'schedule_type'     => 'Shift',
                'password'          => Hash::make('test123'),
                'status_account'    => 1,

            ),
            array(
                'uuid'              => Str::uuid(),
                'name'              => 'ROBBY DWI YANTO',
                'company_id'        => 2,
                'department_id'     => 3,
                'date_of_birth'     => '1963-12-12',
                'identity_card'     => '3506021212690005',
                'gender'            => 'male',
                'religion'          => 'Islam',
                'email'             => 'wings1@wings.com',
                'education'         => 'SMA',
                'status'            => 1,
                'marital_status'    => "Menikah",
                'address'           => 'KLASMAN RT 001 RW 005 DS. REJOTENGAH KEC. DEKET KAB. GRESIK',
                'phone'             => '081236914562',
                'employee_type'     => 'Staff',
                'section'           => 'QC',
                'position_code'     => 'SPV',
                'status_twiji'      => 'GUTEJI',
                'schedule_type'     => 'Shift',
                'password'          => Hash::make('test123'),
                'status_account'    => 1,
            ),
        );

        foreach ($users as $user) {
            User::insert([
                'uuid'              => $user['uuid'],
                'name'              => $user['name'],
                'company_id'        => $user['company_id'],
                'department_id'     => $user['department_id'],
                'date_of_birth'     => $user['date_of_birth'],
                'identity_card'     => $user['identity_card'],
                'gender'            => $user['gender'],
                'religion'          => $user['religion'],
                'email'             => $user['email'],
                'education'         => $user['education'],
                'status'            => $user['status'],
                'marital_status'    => $user['marital_status'],
                'address'           => $user['address'],
                'phone'             => $user['phone'],
                'employee_type'     => $user['employee_type'],
                'section'           => $user['section'],
                'position_code'     => $user['position_code'],
                'status_twiji'      => $user['status_twiji'],
                'schedule_type'     => $user['schedule_type'],
                'password'          => $user['password'],
                'status_account'    => $user['status_account'],
            ]);
        }
    }
}
