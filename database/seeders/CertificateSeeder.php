<?php

namespace Database\Seeders;

use App\Models\Certificate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CertificateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $certificates = array(
            array(
                'name' => 'Sertifikasi Internal Auditor',
            ),
            array(
                'name' =>  'Sertifikasi Umum',
            ),
            array(
                'name' =>  'Sertifikasi Kendaraan & Civil',
            ),
            array(
                'name' => 'Sertifikasi Tanggap Darurat',
            ),
        );


        foreach ($certificates as $certificate) {
            Certificate::insert([
                'name' => $certificate['name'],
            ]);
        }
    }
}
