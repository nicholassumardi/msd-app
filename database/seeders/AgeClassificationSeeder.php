<?php

namespace Database\Seeders;

use App\Models\AgeClassification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgeClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $age_classfications = array(
            array(
                'rule'   => '17',
                'label'  => '17-20 Tahun',
            ),
            array(
                'rule'   => '21',
                'label'  => '21-30 Tahun',
            ),
            array(
                'rule'   => '31',
                'label'  => '31-40 Tahun',
            ),
            array(
                'rule'   => '41',
                'label'  => '41-50 Tahun',
            ),
            array(
                'rule'   => '51',
                'label'  => '51-60 Tahun',
            ),
            array(
                'rule'   => '61',
                'label'  => '61-70 Tahun',
            ),
            array(
                'rule'   => '71',
                'label'  => '71-80 Tahun',
            ),
        );


        foreach ($age_classfications as $ageclassification) {
            AgeClassification::insert([
                'rule'  => $ageclassification['rule'],
                'label' => $ageclassification['label'],
            ]);
        }
    }
}
