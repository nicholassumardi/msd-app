<?php

namespace Database\Seeders;

use App\Models\EmploymentDurationClassification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmploymentDurationClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employmentDurationClassifications = array(
            array(
                'rule'   => '0',
                'label'  => 'G (0-1 Tahun)',
            ),
            array(
                'rule'   => '1',
                'label'  => 'F (1-5 Tahun)',
            ),
            array(
                'rule'   => '5',
                'label'  => 'E (5-10 Tahun)',
            ),
            array(
                'rule'   => '10',
                'label'  => 'D (10-15 Tahun)',
            ),
            array(
                'rule'   => '15',
                'label'  => 'C (15-20 Tahun)',
            ),
            array(
                'rule'   => '20',
                'label'  => 'B (20-25 Tahun)',
            ),
            array(
                'rule'   => '25',
                'label'  => 'A (25 Tahun ke atas)',
            ),
        );

        foreach ($employmentDurationClassifications as $employmentDurationClassification) {
            EmploymentDurationClassification::insert([
                'rule'  => $employmentDurationClassification['rule'],
                'label' => $employmentDurationClassification['label']
            ]);
        }
    }
}
