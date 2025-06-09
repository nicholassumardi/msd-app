<?php

namespace App\Jobs;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Reader\XLSX\Reader;

class ImportCompanyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filepath;
    protected $company;

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
            $dataArrayCompany = [];
            $dataChunk = 200;

            foreach ($reader->getSheetIterator() as $i => $sheet) {
                if ($i == 1) {
                    foreach ($sheet->getRowIterator() as $key => $row) {
                        if ($key != 1) {
                            $data = [
                                'name'        => $row->getCells()[0]->getValue(),
                                'unique_code' => $row->getCells()[1]->getValue(),
                                'code'        => $row->getCells()[2]->getValue(),
                            ];


                            $dataArrayCompany[] = $data;

                            if (count($dataArrayCompany) == $dataChunk) {
                                $this->insertChunk($dataArrayCompany);
                                $dataArrayCompany = [];
                            }
                        }
                    }
                }
            }

            if (count($dataArrayCompany) != 0) {
                $this->insertChunk($dataArrayCompany);
                $dataArrayCompany = [];
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

    public function insertChunk($dataArrayCompany)
    {
        Company::insert($dataArrayCompany);
    }
}
