<?php

namespace App\Jobs;

use App\Models\IKW;
use App\Models\RKI;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Reader\XLSX\Reader;

class ImportRkiJob implements ShouldQueue
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
            $dataArrayRKI = [];
            $dataChunk = 200;

            foreach ($reader->getSheetIterator() as $i => $sheet) {
                if ($i == 1) {
                    foreach ($sheet->getRowIterator() as $key => $row) {
                        if ($key != 1) {
                            $ikw = $this->findIkw($row->getCells()[2]->getValue(), $row->getCells()[3]->getValue(), $row->getCells()[7]->getComputedValue());

                            $data = [
                                'position_job_code'  => $row->getCells()[1]->getValue() ?? NULL,
                                'ikw_id'             => $ikw->id ?? NULL,
                                'training_time'      => (int) $row->getCells()[5]->getValue() ?? NULL,
                            ];

                            $dataArrayRKI[] = $data;

                            if (count($dataArrayRKI) == $dataChunk) {
                                $this->insertChunk($dataArrayRKI);
                                $dataArrayRKI = [];
                            }
                        }
                    }
                }
            }
            // dd($dataArrayRKI);

            if (count($dataArrayRKI) != 0) {
                $this->insertChunk($dataArrayRKI);
                $dataArrayRKI = [];
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

    public function findIkw($arg1, $arg2, $arg3)
    {
        return IKW::where('code', $arg1)
            ->where('name', 'LIKE', "%$arg2%")
            ->whereHas('department', function ($query) use ($arg3) {
                $query->where('code', $arg3);
            })->first();
    }
    public function insertChunk($dataArrayRKI)
    {
        RKI::upsert($dataArrayRKI, ['position_job_code', 'ikw_id_non_null'], ['ikw_id', 'training_time']);
    }
}
