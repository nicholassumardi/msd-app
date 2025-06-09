<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\Department;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Reader\XLSX\Reader;

class ImportDepartmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filepath;
    protected $company;

    public function __construct($filepath)
    {
        $this->filepath = $filepath;
        $this->company = Company::all();
    }

    public function handle()
    {
        try {
            DB::beginTransaction();

            $reader = new Reader();
            $reader->open(storage_path('app/public/' . $this->filepath));
            $dataArrayDepartment = [];
            $dataChunk = 200;

            foreach ($reader->getSheetIterator() as $i => $sheet) {
                if ($i == 2) {

                    foreach ($sheet->getRowIterator() as $key => $row) {
                        if ($key != 1) {
                            // dd($this->findDepartment($row->getCells()[1]->getValue()));
                            $data = [
                                'company_id'  => $this->company->firstWhere('code', $row->getCells()[0]->getValue()) ? $this->company->firstWhere('code', $row->getCells()[0]->getValue())->id : null,
                                'parent_id'   => $this->findDepartment($row->getCells()[1]->getValue()) ?   $this->findDepartment($row->getCells()[1]->getValue())->id : 0,
                                'name'        => $row->getCells()[1]->getValue(),
                                'code'        => $row->getCells()[2]->getValue(),
                            ];


                            $dataArrayDepartment[] = $data;
                            if (count($dataArrayDepartment) == $dataChunk) {
                                $this->insertChunk($dataArrayDepartment);
                                $dataArrayDepartment = [];
                            }
                        }
                    }
                }
            }


            if (count($dataArrayDepartment) != 0) {
                $this->insertChunk($dataArrayDepartment);
                $dataArrayDepartment = [];
            }

            $reader->close();

            Storage::delete($this->filepath);

            DB::commit();

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }

    public function findDepartment($search)
    {
        return Department::where('name', $search)->first();
    }

    public function insertChunk($dataArrayDepartment)
    {
        Department::insert($dataArrayDepartment);
    }
}
