<?php

namespace App\Jobs;

use App\Models\IKW;
use App\Models\JobCode;
use App\Models\JobDescription;
use App\Models\JobTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use OpenSpout\Reader\XLSX\Reader;

class ImportJobTaskDescJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filepath;
    protected $cacheKey;
    protected $user;
    public function __construct($filepath, $cacheKey)
    {
        $this->filepath = $filepath;
        $this->cacheKey = $cacheKey;
    }


    public function handle()
    {
        try {
            DB::beginTransaction();
            
            $reader = new Reader();
            $reader->open(storage_path('app/public/' . $this->filepath));
            $dataJobTask = [];
            $dataJobDesc = [];
            $jobCode = null;
            $jobCodeId = null;

            foreach ($reader->getSheetIterator() as $key => $sheet) {
                $sheetCollections[$key] = LazyCollection::make(function () use ($sheet) {
                    foreach ($sheet->getRowIterator() as $key => $row) {
                        if ($key != 1) {
                            yield $row->toArray();
                        }
                    }
                });
            }
            dd(count($sheetCollections));
            if (isset($sheetCollections[2])) {
                $sheetCollections[2]->chunk(200)->each(function ($rows) use (&$dataJobTask, &$dataJobDesc, &$jobCode, &$jobCodeId) {
                    foreach ($rows as $row) {
                        $dataJobTask = $this->saveDataJobTaskDesc($dataJobTask, $dataJobDesc, $row, $jobCode, $jobCodeId)["jobTask"];
                        $dataJobDesc = $this->saveDataJobTaskDesc($dataJobTask, $dataJobDesc, $row, $jobCode, $jobCodeId)["jobDesc"];
                    }

                    dd($dataJobDesc);
                    // $this->insertChunkJobTaskDesc($dataTraning);
                    $dataJobTask = [];
                    $dataJobDesc = [];
                });

                if (count($dataJobTask) != 0) {
                    // $this->insertChunkJobTaskDesc($dataJobTask);
                    $dataJobTask = [];
                }

                if (count($dataJobDesc) != 0) {
                    // $this->insertChunkJobTaskDesc($dataJobTask);
                    $dataJobDesc = [];
                }
            }

            $reader->close();
            Storage::delete($this->filepath);


            DB::commit();

            return true;
        } catch (\Exception $e) {
            echo $e->getMessage();
            DB::rollBack();
            return false;
        }
    }

    public function saveDataJobTaskDesc($dataJobTask, $dataJobDesc, $row, &$jobCode, $jobCodeId)
    {
        if ($row[2] == '') {
            $jobCodeId = $this->findJobCode($jobCode);
        }

        if ($row[2] != '') {
            $jobCode = $row[2] ?? null;
            $jobCodeId = $this->findJobCode($jobCode);
        }

        $dataJobTask[] = [
            'job_code_id'  => $jobCodeId->id ?? null,
            'ikw_id'       => $this->findIKW($row[6] ?? null)->id ?? null,
            'description'  => $row[5] ?? null,
        ];

        $dataJobDesc[] = [
            'job_code_id'  => $jobCodeId->id ?? null,
            'ikw_id'       => $this->findIKW($row[6] ?? null)->id ?? null,
            'code'         => $row[3] ?? null,
            'description'  => $row[4] ?? null,
        ];

        return [
            'jobTask' => $dataJobTask,
            'jobDesc' => $dataJobDesc,
        ];
    }

    public function insertChunkJobTask($dataJobTask)
    {
       JobTask::upsert($dataJobTask, ['no_training', 'trainee_id'], [
            'trainer_id',
            'assessor_id',
            'ikw_revision_id',
            'training_plan_date',
            'training_realisation_date',
            'training_duration',
            'ticket_return_date',
            'assessment_plan_date',
            'assessment_realisation_date',
            'assessment_duration',
            'status_fa_print',
            'assessment_result',
            'status',
            'description',
            'status_active',
        ]);
    }
    
    public function insertChunkJobDesc($dataJobDesc)
    {
       JobDescription::upsert($dataJobDesc, ['no_training', 'trainee_id'], [
            'trainer_id',
            'assessor_id',
            'ikw_revision_id',
            'training_plan_date',
            'training_realisation_date',
            'training_duration',
            'ticket_return_date',
            'assessment_plan_date',
            'assessment_realisation_date',
            'assessment_duration',
            'status_fa_print',
            'assessment_result',
            'status',
            'description',
            'status_active',
        ]);
    }

    private function findJobCode($arg1)
    {
        return JobCode::where('full_code', $arg1)
            ->first();
    }

    private function findIKW($arg1)
    {
        return IKW::where('code', $arg1)
            ->first();
    }
}
