<?php

namespace Database\Seeders;

use App\Models\UserServiceYear;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserServiceYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $userserviceyears = array(
            array(
                'user_id'    => 1,
                'join_date'  => '2022-10-12',
            ),
            array(
                'user_id'    => 2,
                'join_date'  => '2019-05-02',
            ),
        );

        foreach ($userserviceyears as $userserviceyear) {
            UserServiceYear::insert([
                'user_id'    => $userserviceyear['user_id'],
                'join_date'  => $userserviceyear['join_date'],
            ]);
        }
    }
}
