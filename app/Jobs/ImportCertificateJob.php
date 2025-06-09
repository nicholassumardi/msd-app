<?php

namespace App\Jobs;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Reader\XLSX\Reader;

class ImportCertificateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $filepath;

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
            $dataArrayCertificate = [];
            $dataChunk = 200;

            foreach ($reader->getSheetIterator() as $i => $sheet) {
                if ($i == 0) {
                    foreach ($sheet->getRowIterator() as $key => $row) {
                        if ($key != 1) {
                            $data = [
                                'name'        => $row->getCells()[0]->getValue(),
                            ];

                            $dataArrayCertificate[] = $data;

                            if (count($dataArrayCertificate) == $dataChunk) {
                                $this->insertChunk($dataArrayCertificate);
                                $dataArrayCertificate = [];
                            }
                        }
                    }
                }
            }

            if (count($dataArrayCertificate) != 0) {
                $this->insertChunk($dataArrayCertificate);
                $dataArrayCertificate = [];
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

    public function insertChunk($dataArrayCertificate)
    {
        Certificate::insert($dataArrayCertificate);
    }
}
