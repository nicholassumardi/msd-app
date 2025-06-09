<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call([
            AgeClassificationSeeder::class,
            EmploymentDurationClassificationSeeder::class,
            GeneralClassificationSeeder::class,
            CertificateSeeder::class,
            CompanySeeder::class,
            DepartmentSeeder::class,
            // UserSeeder::class,
            // UserServiceYearSeeder::class,
            // UserEmployeeNumberSeeder::class,
            // UserCertificateSeeder::class,
        ]);
    }
}
