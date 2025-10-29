<?php

namespace App\Jobs;

use App\Models\IKW;
use App\Models\JobDescDetail;
use App\Models\JobDescription;
use App\Models\JobTask;
use App\Models\JobTaskDetail;
use App\Models\structure;
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
            $dataJobTaskDetail = [];
            $dataJobDescDetail = [];
            $jobCode = null;
            $jobDescCode = null;
            $userStructure = null;

            foreach ($reader->getSheetIterator() as $key => $sheet) {
                $sheetCollections[$key] = LazyCollection::make(function () use ($sheet) {
                    foreach ($sheet->getRowIterator() as $key => $row) {
                        if ($key != 1) {
                            yield $row->toArray();
                        }
                    }
                });
            }


            $totalSheets = count($sheetCollections);
            for ($sheetIndex = 0; $sheetIndex < $totalSheets; $sheetIndex++) {
                if (isset($sheetCollections[$sheetIndex])) {
                    $sheetCollections[$sheetIndex]->chunk(200)->each(function ($rows) use (&$dataJobTask, &$dataJobDesc, &$dataJobTaskDetail, &$dataJobDescDetail, &$jobCode, &$jobDescCode, &$userStructure) {
                        foreach ($rows as $row) {
                            $data = $this->saveDataJobTaskDesc($dataJobTask, $dataJobDesc, $dataJobTaskDetail, $dataJobDescDetail,  $row, $jobCode, $jobDescCode, $userStructure);

                            $dataJobTask =  $data["jobTask"];
                            $dataJobDesc =  $data["jobDesc"];
                            $dataJobTaskDetail =  $data["jobTaskDetail"];
                            $dataJobDescDetail =  $data["jobDescDetail"];
                        }


                        $this->insertChunkJobDesc($dataJobDesc, $dataJobDescDetail);
                        $this->insertChunkJobTask($dataJobTask, $dataJobTaskDetail);

                        $dataJobTask = [];
                        $dataJobDesc = [];
                        $dataJobTaskDetail = [];
                        $dataJobDescDetail = [];
                    });
                    if (!empty($dataJobDesc)) {
                        $this->insertChunkJobDesc($dataJobDesc, $dataJobDescDetail);
                        $dataJobDesc = [];
                        $dataJobDescDetail = [];
                    }


                    if (!empty($dataJobTask)) {
                        $this->insertChunkJobTask($dataJobTask, $dataJobTaskDetail);
                        $dataJobTask = [];
                        $dataJobTaskDetail = [];
                    }
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

    public function saveDataJobTaskDesc($dataJobTask, $dataJobDesc, $dataJobTaskDetail, $dataJobDescDetail,  $row, &$jobCode, &$jobDescCode, &$userStructure)
    {
        $unique = null;


        // if excel cell is blank then use previous data
        $jobCode       = $row[2] !== '' ? $row[2] : $jobCode;
        $jobDescCode   = $row[3] !== '' ? $row[3] : $jobDescCode;

        // Determine user structure and build unique key
        $userStructure = $row[1] !== '' ? $row[1] : $userStructure;
        $unique        = sprintf("%s-%s", $userStructure, $jobDescCode);

        // Common identifier lookups
        $structure = $this->findstructure($userStructure);

        $ikwId = $this->findIKW($row[6] ?? null)->id ?? null;
        $taskDescription = $row[5] ?? null;
        $descDescription = $row[4] ?? null;


        if (is_null($ikwId)) {
            return [
                'jobTask'       => $dataJobTask,
                'jobDesc'       => $dataJobDesc,
                'jobTaskDetail' => $dataJobTaskDetail,
                'jobDescDetail' => $dataJobDescDetail,
            ];
        }

        $dataJobDesc[$unique] = [
            'code'                       => $jobDescCode,
            'description'                => $descDescription,
        ];


        if ($taskDescription) {
            $dataJobTask[$taskDescription] = [
                'structure_id'               => $structure->id ?? null,
                'description'                => $taskDescription,
                'code'                       => $jobDescCode,
            ];
        }

        // GET IKW & Job Task & Job Desc Relationship
        if ($ikwId) {
            $dataJobDescDetail[] = [
                'structure_id'               => $structure->id ?? null,
                'ikw_id'                     => $ikwId,
                'code'                       => $jobDescCode,
            ];

            $dataJobTaskDetail[] = [
                'structure_id'               => $structure->id ?? null,
                'ikw_id'                     => $ikwId,
                'description'                => $taskDescription,
            ];
        }

        return [
            'jobDesc'       => $dataJobDesc,
            'jobTask'       => $dataJobTask,
            'jobDescDetail' => $dataJobDescDetail,
            'jobTaskDetail' => $dataJobTaskDetail,
        ];
    }

    public function insertChunkJobDesc($dataJobDesc, $dataJobDescDetail)
    {
        $data = array_values($dataJobDesc);

        JobDescription::upsert($data, ['code']);

        $this->insertChunkJobDescDetail($dataJobDescDetail);
    }


    public function insertChunkJobTask($dataJobTask, $dataJobTaskDetail)
    {

        $dataCleaned = array_values($dataJobTask);
        $insertedData = [];

        foreach ($dataCleaned as $data) {

            $job_description = $this->findJobDescription($data['code']);
            $insertedData[] = [
                'job_description_id'  => $job_description->id,
                'description'         => $data['description'],
            ];
        }


        JobTask::upsert($insertedData, ['job_desc_id_non_null', 'description']);

        $this->insertChunkJobTaskDetail($dataJobTaskDetail);
    }

    public function insertChunkJobDescDetail($dataJobDescDetail)
    {
        $insertedData = [];

        foreach ($dataJobDescDetail as $data) {

            $job_description = $this->findJobDescription($data['code']);
            $unique = sprintf("%s-%s", $job_description->id ?? "None", $data['ikw_id']);
            $insertedData[$unique] = [
                'structure_id'              => $data['structure_id'],
                'job_description_id'        => $job_description->id ?? null,
                'ikw_id'                    => $data['ikw_id'],
            ];
        }

        $cleanedData = array_values($insertedData);

        JobDescDetail::insert($cleanedData);
    }

    public function insertChunkJobTaskDetail($dataJobTaskDetail)
    {
        $insertedData = [];

        foreach ($dataJobTaskDetail as $data) {

            $job_task = $this->findJobTask($data['description']);
            $unique = sprintf("%s-%s", $job_task->id ?? "None", $data['ikw_id']);
            $insertedData[$unique] = [
                'job_task_id' => $job_task->id ?? null,
                'ikw_id'      => $data['ikw_id'],
            ];
        }

        $cleanedData = array_values($insertedData);

        JobTaskDetail::insert($cleanedData);
    }


    private function findIKW($arg1)
    {
        return IKW::where('code', $arg1)
            ->first();
    }

    private function findstructure($arg1)
    {
        return structure::whereFuzzy('name', $arg1)
            ->first();
    }

    private function findJobDescription($arg1)
    {
        return JobDescription::where('code', $arg1)
            ->first();
    }

    private function findJobTask($arg1)
    {
        return JobTask::whereFuzzy('description', $arg1)
            ->first();
    }
}
