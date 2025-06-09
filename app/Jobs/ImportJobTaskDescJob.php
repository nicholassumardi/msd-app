<?php

namespace App\Jobs;

use App\Models\IKW;
use App\Models\IKWRevision;
use App\Models\JobCode;
use App\Models\Training;
use App\Models\User;
use App\Models\UserEmployeeNumber;
use Carbon\CarbonImmutable;
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

            $ikw = IKW::pluck('id', 'code')->all();
            dd($ikw);
            $jobCode = JobCode::pluck('id', 'full_code')->all();
            $departments = User::pluck('department_id', 'id')->all();
            $ikwCache = [];
            $reader = new Reader();
            $reader->open(storage_path('app/public/' . $this->filepath));
            $dataTraning = [];


            foreach ($reader->getSheetIterator() as $key => $sheet) {
                $sheetCollections[$key] = LazyCollection::make(function () use ($sheet) {
                    foreach ($sheet->getRowIterator() as $key => $row) {
                        if ($key != 1) {
                            yield $row->toArray();
                        }
                    }
                });
            }


            if (isset($sheetCollections[1])) {
                $sheetCollections[1]->chunk(200)->each(function ($rows) use (&$dataTraning, $ikw,  $jobCode, $departments, $ikwCache) {

                    foreach ($rows as $row) {
                        $dataTraning = $this->saveDataTraining($dataTraning, $row, $ikw,  $jobCode, $departments, $ikwCache);
                    }

                    $this->insertChunkTraining($dataTraning);
                    $dataTraning = [];
                });

                if (count($dataTraning) != 0) {
                    $this->insertChunkTraining($dataTraning);
                    $dataTraning = [];
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

    public function saveDataTraining($dataTraning, $row, $ikw,  $jobCode, $departments, $ikwCache)
    {
        $traineeId = $ikw[$row[3]] ?? ($jobCode[$row[2]] ?? null);
        $trainerId = $ikw[$row[11]] ?? ($jobCode[$row[10]] ?? null);
        $assessorId = $ikw[$row[18]] ?? ($jobCode[$row[17]] ?? null);
        $ikwId = null;

        if ($traineeId) {
            $departmentId = $departments[$traineeId] ?? null;
            $ikwKey = $row[7] . '-' . $departmentId;
            if (isset($ikwCache[$ikwKey])) {
                $ikwId = $ikwCache[$ikwKey];
            } else {
                $ikwRevision = $this->findIkw($row[7], $departmentId, $row[8]);
                $ikwId = $ikwRevision ? $ikwRevision->id : null;
                $ikwCache[$ikwKey] = $ikwId;
            }
        }


        $dataTraning[] = [
            'no_training'                    => $row[0],
            'trainee_id'                     => $traineeId,
            'trainer_id'                     => $trainerId,
            'assessor_id'                    => $assessorId,
            'ikw_revision_id'                => $ikwId,
            'training_plan_date'             => $this->parseDate($row[13]),
            'training_realisation_date'      => $this->parseDate($row[14]),
            'training_duration'              => (int) $row[15],
            'ticket_return_date'             => $this->parseDate($row[16]),
            'assessment_plan_date'           => $this->parseDate($row[20]),
            'assessment_realisation_date'    => $this->parseDate($row[22]),
            'assessment_duration'            => (int) $row[23],
            'status_fa_print'                => (int) $row[21],
            'assessment_result'              => $row[24],
            'status'                         => $row[25] == 'DONE' ? 1 : 0,
            'description'                    => $row[26],
            'status_active'                  => $row[27] == 'AKTIF' ? 1 : 0,
        ];

        return $dataTraning;
    }

    public function insertChunkTraining($dataTraning)
    {
        Training::upsert($dataTraning, ['no_training', 'trainee_id'], [
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

    public function findIkw($arg1, $arg2, $arg3)
    {
        return IKWRevision::whereHas('ikw', function ($query) use ($arg1, $arg2) {
            $query->where('code', $arg1)->whereHas('department', function ($query) use ($arg2) {
                $query->where('id', $arg2);
            });
        })
            ->where('revision_no', (int) $arg3)
            ->first();
    }

    public function parseDate($date)
    {
        $isDateTime = $date instanceof \DateTimeImmutable;

        return  $isDateTime ? CarbonImmutable::instance($date)->format('Y-m-d') : NULL;
    }
}
