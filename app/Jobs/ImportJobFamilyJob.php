<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\JobCode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Reader\XLSX\Reader;

class ImportJobFamilyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filepath;
    protected $jobCode;

    public function __construct($filepath)
    {
        $this->filepath = $filepath;
    }

    public function handle()
    {
        try {
            DB::beginTransaction();

            $reader = new Reader();
            $reader->open(storage_path('app/public/' . $this->filepath));
            $dataArrayCategory = [];
            $dataArrayJobCode = [];
            $dataChunk = 200;
            foreach ($reader->getSheetIterator() as $i => $sheet) {
                if ($i == 1) {
                    foreach ($sheet->getRowIterator() as $key => $row) {
                        if ($key != 1) {

                            if (!isset($dataArrayCategory[$row->getCells()[5]->getValue()])) {
                                $dataArrayCategory[$row->getCells()[5]->getValue()] = [
                                    'name'  => $row->getCells()[5]->getValue()
                                ];
                            }

                            $fullCode =  $row->getCells()[0]->getValue() . '' . $row->getCells()[1]->getValue() . '' . $row->getCells()[2]->getValue();

                            $dataArrayJobCode[] = [
                                'category_name'  => $row->getCells()[5]->getValue(),
                                'org_level'      => $row->getCells()[0]->getValue(),
                                'job_family'     => $row->getCells()[1]->getValue(),
                                'code'           => $row->getCells()[2]->getValue(),
                                'full_code'      => $fullCode,
                                'position'       => $row->getCells()[6]->getValue(),
                                'level'          => $row->getCells()[7]->getValue(),
                            ];



                            if (count($dataArrayCategory) == $dataChunk) {
                                $this->insertChunkCategory($dataArrayCategory, $dataArrayJobCode);
                                $dataArrayCategory = [];
                                $dataArrayJobCode = [];
                            }
                        }
                    }
                }
            }

            if (count($dataArrayCategory) != 0 || count($dataArrayJobCode) != 0) {
                $this->insertChunkCategory($dataArrayCategory, $dataArrayJobCode);
                $dataArrayCategory = [];
                $dataArrayJobCode = [];
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


    public function insertChunkCategory($dataArrayCategory, $dataArrayJobCode)
    {
        $insertedData = array_values($dataArrayCategory);
        Category::insert($insertedData);

        $this->insertChunkJobCode($dataArrayJobCode);
    }

    public function insertChunkJobCode($dataArrayJobCode)
    {
        $updatedJobCode = [];
        foreach ($dataArrayJobCode as $jobCode) {
            $category_id = $this->findCategory($jobCode['category_name'])->id;
            if ($category_id) {
                $updatedJobCode[] = [
                    'category_id'    => (int)$category_id,
                    'org_level'      => $jobCode['org_level'],
                    'job_family'     => $jobCode['job_family'],
                    'code'           => $jobCode['code'],
                    'full_code'      => $jobCode['full_code'],
                    'level'          => (int)$jobCode['level'],
                    'position'       => $jobCode['position'],
                ];
            }
        }

        JobCode::insert($updatedJobCode);
    }

    public function findCategory($search)
    {
        return Category::where('name', $search)->first();
    }
}
