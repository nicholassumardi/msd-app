<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $companies = array(
            array(
                'name'         => 'Karunia Alam Segar',
                'unique_code'  => '02',
                'code'         => 'KAS',
            ),
            array(
                'name'         => 'Karya Indah Alam Sejahtera',
                'unique_code'  => '01',
                'code'         => 'KIAS',
            ),
            array(
                'name'         => 'Harum Alam Segar',
                'unique_code'  => '03',
                'code'         => 'HAS',
            ),
        );

        foreach ($companies as $company) {
            Company::insert([
                'name'        => $company['name'],
                'unique_code' => $company['unique_code'],
                'code'        => $company['code'],
            ]);
        }
    }
}
