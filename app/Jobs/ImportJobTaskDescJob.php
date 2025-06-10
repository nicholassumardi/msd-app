<?php

namespace App\Jobs;

use App\Models\IKW;
use App\Models\IkwJobDesc;
use App\Models\IkwJobTask;
use App\Models\JobCode;
use App\Models\JobDescription;
use App\Models\JobTask;
use App\Models\UserStructureMapping;
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
            $dataIKWJobTask = [];
            $dataIKWJobDesc = [];
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
                    $sheetCollections[$sheetIndex]->chunk(200)->each(function ($rows) use (&$dataJobTask, &$dataJobDesc, &$dataIKWJobTask, &$dataIKWJobDesc, &$jobCode, &$jobDescCode, &$userStructure) {
                        foreach ($rows as $row) {
                            $data = $this->saveDataJobTaskDesc($dataJobTask, $dataJobDesc, $dataIKWJobTask, $dataIKWJobDesc,  $row, $jobCode, $jobDescCode, $userStructure);


                            $dataJobTask =  $data["jobTask"];
                            $dataJobDesc =  $data["jobDesc"];
                            $dataIKWJobTask =  $data["ikwJobTask"];
                            $dataIKWJobDesc =  $data["ikwJobDesc"];
                        }

                        // dd(array_values($dataJobTask));
                        // dd(array_values($dataJobDesc));
                        // dd(array_values($dataJobDesc));

                        $this->insertChunkJobTask($dataJobTask, $dataIKWJobTask);
                        $this->insertChunkJobDesc($dataJobDesc, $dataIKWJobDesc);

                        $dataJobTask = [];
                        $dataJobDesc = [];
                        $dataIKWJobTask = [];
                        $dataIKWJobDesc = [];
                    });

                    if (!empty($dataJobTask)) {
                        $this->insertChunkJobTask($dataJobTask, $dataIKWJobTask);
                        $dataJobTask = [];
                        $dataIKWJobTask = [];
                    }

                    if (!empty($dataJobDesc)) {
                        $this->insertChunkJobDesc($dataJobDesc, $dataIKWJobDesc);
                        $dataJobDesc = [];
                        $dataIKWJobDesc = [];
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

    public function saveDataJobTaskDesc($dataJobTask, $dataJobDesc, $dataIKWJobTask, $dataIKWJobDesc,  $row, &$jobCode, &$jobDescCode, &$userStructure)
    {
        $unique = null;

        $jobCode       = $row[2] !== '' ? $row[2] : $jobCode;

        // Determine job description code
        $jobDescCode = $row[3] !== '' ? $row[3] : $jobDescCode;

        // Determine user structure and build unique key
        $userStructure = $row[1] !== '' ? $row[1] : $userStructure;
        $unique        = sprintf("%s-%s", $userStructure, $jobDescCode);

        // Common identifier lookups
        $userStructureMapping = $this->findUserStructureMapping($userStructure);

        // dd($userStructureMapping);
        $ikwId = $this->findIKW($row[6] ?? null)->id ?? null;
        $taskDescription = $row[5] ?? null;
        $descDescription = $row[4] ?? null;

        if ($taskDescription) {
            $dataJobTask[$taskDescription] = [
                'user_structure_mapping_id'  => $userStructureMapping->id ?? null,
                'description'                => $taskDescription,
            ];
        }

        $dataJobDesc[$unique] = [
            'user_structure_mapping_id'  => $userStructureMapping->id ?? null,
            'code'                       => $jobDescCode,
            'description'                => $descDescription,
        ];

        if ($ikwId) {
            $dataIKWJobTask[] = [
                'user_structure_mapping_id'  => $userStructureMapping->id ?? null,
                'ikw_id'                     => $ikwId,
                'description'                => $taskDescription,
            ];

            $dataIKWJobDesc[] = [
                'user_structure_mapping_id'  => $userStructureMapping->id ?? null,
                'ikw_id'                     => $ikwId,
                'code'                       => $jobDescCode,
            ];
        }

        return [
            'jobTask'    => $dataJobTask,
            'jobDesc'    => $dataJobDesc,
            'ikwJobTask' => $dataIKWJobTask,
            'ikwJobDesc' => $dataIKWJobDesc,
        ];
    }

    public function insertChunkJobTask($dataJobTask, $dataIKWJobTask)
    {
        $data = array_values($dataJobTask);
        JobTask::insert($data);


        $this->insertChunkIKWJobTask($dataIKWJobTask);
    }

    public function insertChunkJobDesc($dataJobDesc, $dataIKWJobDesc)
    {
        $data = array_values($dataJobDesc);
        JobDescription::insert($data);

        $this->insertChunkIKWJobDesc($dataIKWJobDesc);
    }

    public function insertChunkIKWJobTask($dataIKWJobTask)
    {
        $insertedData = [];

        foreach ($dataIKWJobTask as $data) {

            $job_task = $this->findJobTask($data['user_structure_mapping_id'], $data['description']);
            $insertedData[] = [
                'job_task_id' => $job_task->id ?? null,
                'ikw_id'      => $data['ikw_id'],
            ];
        }

        // dd($insertedData);

        IkwJobTask::insert($insertedData);
    }

    public function insertChunkIKWJobDesc($dataIKWJobDesc)
    {
        $insertedData = [];

        foreach ($dataIKWJobDesc as $data) {

            $job_description = $this->findJobDescription($data['user_structure_mapping_id'], $data['code']);
            $insertedData[] = [
                'job_description_id' => $job_description->id ?? null,
                'ikw_id'             => $data['ikw_id'],
            ];
        }

        // dd($insertedData);

        IkwJobDesc::insert($insertedData);
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

    private function findUserStructureMapping($arg1)
    {
        return UserStructureMapping::whereFuzzy('name', $arg1)
            ->first();
    }

    private function findJobDescription($arg1, $arg2)
    {
        return JobDescription::where('user_structure_mapping_id', $arg1)
            ->where('code', $arg2)
            ->first();
    }

    private function findJobTask($arg1, $arg2)
    {
        return JobTask::where('user_structure_mapping_id', $arg1)->where('description', $arg2)
            ->first();
    }
}
