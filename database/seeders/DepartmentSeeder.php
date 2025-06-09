<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $departments = array(
            array(
                'company_id'     => '1',
                'parent_id'      => '0',
                'name'           => 'Quality Control',
                'code'           => 'QC',
            ),
            array(
                'company_id'     => '1',
                'parent_id'      => '0',
                'name'           => 'REF',
                'code'           => 'REF',
            ),
            array(
                'company_id'     => '2',
                'parent_id'      => '0',
                'name'           => 'TEK',
                'code'           => 'TEK',
            ),
        );

        foreach ($departments as $department) {
            Department::insert([
                'company_id'  => $department['company_id'],
                'parent_id'   => $department['parent_id'],
                'name'        => $department['name'],
                'code'        => $department['code'],
            ]);
        }
    }
}
