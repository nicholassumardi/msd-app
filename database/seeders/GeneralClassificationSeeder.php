<?php

namespace Database\Seeders;

use App\Models\GeneralClassification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $generalClassifications = array(
            array(
                'rule'   => '1946',
                'label'  => 'Baby Boomers',
            ),
            array(
                'rule'   => '1965',
                'label'  => 'Gen X',
            ),
            array(
                'rule'   => '1977',
                'label'  => 'Gen Y',
            ),
            array(
                'rule'   => '1995',
                'label'  => 'Gen Z',
            ),
            array(
                'rule'   => '2012',
                'label'  => 'Gen Alpha',
            ),
        );

        foreach ($generalClassifications as $generalClassification) {
            GeneralClassification::insert([
                'rule'   => $generalClassification['rule'],
                'label'  => $generalClassification['label'],
            ]);
        }
    }
}
