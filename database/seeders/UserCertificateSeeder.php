<?php

namespace Database\Seeders;

use App\Models\UserCertificate;
use Illuminate\Database\Seeder;

class UserCertificateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userCertificates = array(
            array(
                'user_id'            => 1,
                'certificate_id'     => 1,
                'description'        => 'Pemadam kebakaran kelas A dan C',
                'expiration_date'    => '2026-10-13',
            ),
            array(
                'user_id'            => 1,
                'certificate_id'     => 2,
                'description'        => 'Penyelia Halal',
                'expiration_date'    => '2027-12-13',
            ),
            array(
                'user_id'            => 2,
                'certificate_id'     => 3,
                'description'        => 'SIO FORKLIFT',
                'expiration_date'    => '2025-11-13',
            ),
        );

        foreach ($userCertificates as $userCertificate) {
            UserCertificate::insert([
                'user_id'         => $userCertificate['user_id'],
                'certificate_id'  => $userCertificate['certificate_id'],
                'description'     => $userCertificate['description'],
                'expiration_date' => $userCertificate['expiration_date'],
            ]);
        }
    }
}
