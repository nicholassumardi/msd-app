<?php

namespace App\Jobs;

use App\Models\Department;
use App\Models\JobCode;
use App\Models\User;
use App\Models\UserEmployeeNumber;
use App\Models\UserJobCode;
use App\Models\UserStructureMapping;
use App\Models\UserStructureMappingHistories;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;

class ImportStructureJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filepath;
    protected $cacheKey;
    protected $jobCode;
    public function __construct($filepath, $cacheKey)
    {
        $this->filepath = $filepath;
        $this->cacheKey = $cacheKey;
        $this->jobCode = JobCode::all();
    }

    public function handle()
    {
        try {
            DB::beginTransaction();

            $reader = new Reader();
            $reader->open(storage_path('app/public/' . $this->filepath));
            $dataStructure = [];
            $dataUserJobCode = [];
            $dataUserNotFound = [];
            $dataChunk = 200;

            foreach ($reader->getSheetIterator() as $i => $sheet) {
                if ($i == 1) {
                    foreach ($sheet->getRowIterator() as $key => $row) {
                        if ($key != 1) {

                            $dataUser = $this->saveDataUserJobCode($dataUserJobCode, $dataUserNotFound, $row);

                            $dataStructure = $this->saveDataStructureMapping($dataStructure, $row);
                            $dataUserJobCode =  $dataUser['dataUserJobCode'];
                            $dataUserNotFound = $dataUser['dataUserNotFound'];


                            if (count($dataStructure) == $dataChunk) {
                                $this->insertChunkStructure($dataStructure, $dataUserJobCode);
                                $dataStructure = [];
                            }
                        }
                    }


                    if (count($dataStructure) != 0) {
                        $this->insertChunkStructure($dataStructure, $dataUserJobCode);
                    }

                    $filePathExportData =  $this->exportData($dataUserNotFound);
                    Cache::put($this->cacheKey, $filePathExportData, now()->addMinutes(10));
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

    // public function handle()
    // {
    //     try {
    //         DB::beginTransaction();

    //         $reader = new Reader();
    //         $reader->open(storage_path('app/public/' . $this->filepath));
    //         $dataStructure = [];
    //         $dataUserJobCode = [];
    //         $dataUserNotFound = [];
    //         $dataChunk = 200;

    //         foreach ($reader->getSheetIterator() as $i => $sheet) {
    //             if ($i == 1) {

    //                 LazyCollection::make(function () use ($sheet) {
    //                     foreach ($sheet->getRowIterator() as $key => $row) {
    //                         if ($key != 1) {
    //                             yield $row->toArray();
    //                         }
    //                     }
    //                 })->chunk($dataChunk)->each(function ($row) use ($dataUserJobCode, $dataUserNotFound, $dataStructure, $dataChunk) {
    //                     dd($row);
    //                     $dataUSer = $this->saveDataUserJobCode($dataUserJobCode, $dataUserNotFound, $row);

    //                     $dataStructure = $this->saveDataStructureMapping($dataStructure, $row);
    //                     $dataUserJobCode =  $dataUSer['dataUserJobCode'];
    //                     $dataUserNotFound = $dataUSer['dataUserNotFound'];


    //                     if (count($dataStructure) == $dataChunk) {
    //                         $this->insertChunkStructure($dataStructure, $dataUserJobCode);
    //                         $dataStructure = [];
    //                     }
    //                 });


    //                 if (count($dataStructure) != 0) {
    //                     $this->insertChunkStructure($dataStructure, $dataUserJobCode);
    //                 }

    //                 $filePathExportData =  $this->exportData($dataUserNotFound);
    //                 Cache::put($this->cacheKey, $filePathExportData, now()->addMinutes(10));
    //             }
    //         }

    //         $reader->close();
    //         Storage::delete($this->filepath);


    //         DB::commit();

    //         return true;
    //     } catch (\Exception $e) {
    //         echo $e->getMessage();
    //         DB::rollBack();
    //         return false;
    //     }
    // }


    private function saveDataStructureMapping($dataStructure, $row)
    {
        $companyName = $row->getCells()[0]->getValue();
        $departmentName = $row->getCells()[1]->getValue();
        $name = $row->getCells()[15]->getValue();
        $parentName = $row->getCells()[8]->getValue();
        $jobCode = $row->getCells()[10]->getValue();
        $structureType = $row->getCells()[22]->getValue();

        $parentId = 0;
        if ($parentName) {
            $parent = $this->findUserStructureByName($parentName);
            if ($parent) {
                $parentId = $parent->id;
            }
        }

        if (isset($dataStructure[$row->getCells()[15]->getValue()])) {
            $dataStructure[$row->getCells()[15]->getValue()]['quota'] = $dataStructure[$row->getCells()[15]->getValue()]['quota'] + 1;
        } else {
            $dataStructure[$row->getCells()[15]->getValue()] = [
                'department_id'            => $this->findDataDepartment($companyName, $departmentName)->id ?? null,
                'parent_name'              => $parentName ?? null,
                'parent_id'                => $parentId,
                'position_code_structure'  => $row->getCells()[11]->getValue(),
                'job_code_id'              => $this->jobCode->firstWhere('full_code', $jobCode)->id ?? null,
                'name'                     => $name,
                'quota'                    => 1,
                'structure_type'           => $structureType,
            ];
        }

        return $dataStructure;
    }

    private function saveDataUserJobCode($dataUserJobCode, $dataUserNotFound, $row)
    {
        $userEmployeeNumber = $this->findDataByEmployeeNumber($row->getCells()[18]->getValue());
        $identity_card = $this->findDataByEmployeeNIK($row->getCells()[19]->getValue());
        $user = $this->findDataUser($row->getCells()[20]->getValue());
        $jobCode = $this->jobCode->firstWhere('full_code', $row->getCells()[10]->getValue()) ? $this->jobCode->firstWhere('full_code', $row->getCells()[10]->getValue())->id : NULL;
        $jobCodeParent =  $this->jobCode->firstWhere('full_code', $row->getCells()[3]->getValue()) ? $this->jobCode->firstWhere('full_code', $row->getCells()[3]->getValue())->id : NULL;
        $parentName = $jobCodeParent . "-" . $row->getCells()[4]->getValue() . "-" . $row->getCells()[5]->getValue();

        $parentId = 0;
        if ($parentName) {
            $parent = $this->findUserSuperior($parentName);
            if ($parent) {
                $parentId = $parent->id;
            }
        }

        if (!$identity_card) {
            if (!$userEmployeeNumber) {
                $dataUserNotFound[] = $row->toArray();
            } elseif (!$user) {
                $dataUserNotFound[] = $row->toArray();
            }
        } else {
            // Example: track duplicates by a unique key
            $rowKey = $row->getCells()[19]->getValue() . '|' . $row->getCells()[18]->getValue();
            static $seenRows = [];
            if (isset($seenRows[$rowKey])) {
                $dataUserNotFound[] = $row->toArray(); // add duplicate to not found
            } else {
                $seenRows[$rowKey] = true;
            }
        }


        if ($userEmployeeNumber || $user) {
            $dataUserJobCode[] = [
                'user_id'                       => $userEmployeeNumber ? $userEmployeeNumber->user_id : ($user ? $user->id : NULL),
                'job_code_id'                   => $jobCode,
                'parent_name'                   => $parentName,
                'parent_id'                     => $parentId,
                'user_structure_mapping_name'   => $row->getCells()[15]->getValue(),
                'id_structure'                  => $row->getCells()[9]->getValue(),
                'id_staff'                      => $row->getCells()[16]->getValue(),
                'position_code_structure'       => $row->getCells()[11]->getValue(),
                'group'                         => $row->getCells()[12]->getValue(),
                'employee_type'                 => $row->getCells()[20]->getValue(),
                'assign_date'                   => NULL,
                'reassign_date'                 => NULL,
                'status'                        => 1,
            ];
        }

        return [
            'dataUserJobCode'  => $dataUserJobCode,
            'dataUserNotFound' => $dataUserNotFound,
        ];
    }

    public function insertChunkStructure($dataStructure, $dataUserJobCode)
    {
        $insertedData =  array_values(array_map(function ($item) {
            unset($item['parent_name']);
            return $item;
        }, $dataStructure));

        UserStructureMapping::upsert(
            $insertedData,
            ['job_code_id_non_null', 'name'],
            ['department_id', 'quota', 'parent_id', 'position_code_structure', 'structure_type']
        );

        $updates = [];
        foreach ($dataStructure as $name => $data) {
            $childRecord = $this->findUserStructureByName($name);
            $parentRecord = $this->findUserStructureByName($data['parent_name']);
            if ($childRecord && $parentRecord && $data['parent_id'] == 0) {
                $updates[] = [
                    'id'        => $childRecord->id,
                    'parent_id' => $parentRecord->id,
                ];
            }
        }

        if (!empty($updates)) {
            $updateQuery = UserStructureMapping::query();
            foreach ($updates as $update) {
                $updateQuery->orWhere('id', $update['id']);
            }

            $updateQuery->update([
                'parent_id' => DB::raw('CASE ' .
                    implode(' ', array_map(function ($update) {
                        return 'WHEN id = ' . $update['id'] . ' THEN ' . $update['parent_id'];
                    }, $updates)) .
                    ' ELSE parent_id END')
            ]);
        }

        $keys = collect($insertedData)
            ->map(fn($row) => [
                'quota' => $row['quota'],
                'name'                 => $row['name'],
            ]);


        $usmRecords = UserStructureMapping::query()
            ->where(function ($q) use ($keys) {
                foreach ($keys as $key) {
                    $q->orWhere(function ($q2) use ($key) {
                        $q2->where('quota', $key['quota'])
                            ->where('name', $key['name']);
                    });
                }
            })
            ->get(['id']);

        $now = Carbon::now()->format('Y-m-d');

        // TO RECORD HISTORIES OF STRUCTURE REVISION
        $histories = $usmRecords->map(function ($usm) use ($now) {
            return [
                'user_structure_mapping_id' => $usm->id,
                'revision_no'               => 0,
                'valid_date'                => $now,
                'updated_date'              => $now,
                'authorized_date'           => $now,
                'approval_date'             => $now,
                'acknowledged_date'         => $now,
                'created_date'              => $now,
                'distribution_date'         => $now,
                'withdrawal_date'           => null,
                'logs'                      => '',
            ];
        })->all();

        // 5) bulk‐insert all the history rows in one shot
        UserStructureMappingHistories::upsert($histories, ['revision_no', 'user_structure_mapping_id'], [
            'valid_date',
            'updated_date',
            'authorized_date',
            'approval_date',
            'acknowledged_date',
            'created_date',
            'distribution_date',
            'withdrawal_date',
            'logs',
        ]);

        $this->insertChunkUser($dataUserJobCode);
    }

    public function insertChunkUser($dataUserJobCode)
    {
        $existingPairs = UserJobCode::orderBy('id', 'ASC')
            ->get()
            ->map(function ($item) {
                return $item->user_id . '_' . $item->user_structure_mapping_id . '_' . $item->group;
            })->toArray();
        $updatedUserJobCode = [];
        foreach ($dataUserJobCode as $data) {
            $userStructureMapping = $this->findUserStructureByName($data['user_structure_mapping_name']);
            if ($userStructureMapping) {
                $updatedUserJobCode[] = [
                    'user_id'                       => $data['user_id'],
                    'job_code_id'                   => $data['job_code_id'],
                    'parent_id'                     => $data['parent_id'],
                    'user_structure_mapping_id'     => $userStructureMapping->id,
                    'id_structure'                  => $data['id_structure'],
                    'id_staff'                      => $data['id_staff'],
                    'position_code_structure'       => $data['position_code_structure'],
                    'group'                         => $data['group'],
                    'employee_type'                 => $data['employee_type'],
                    'assign_date'                   => $data['assign_date'],
                    'reassign_date'                 => $data['reassign_date'],
                    'status'                        => $data['status'],
                ];
            }
        }

        $newDataIn = collect($updatedUserJobCode);

        if ($newDataIn->isNotEmpty()) {
            UserJobCode::whereNotIn('user_id', $newDataIn->pluck('user_id'))
                ->whereIn('user_structure_mapping_id', $newDataIn->pluck('user_structure_mapping_id'))
                ->whereIn('id_structure', $newDataIn->pluck('id_structure'))
                ->update([
                    'status'  => 0,
                ]);
        }

        $insertedDataUserJobCode = $newDataIn
            ->filter(function ($item) use ($existingPairs) {
                $pair = $item['user_id'] . '_' . $item['user_structure_mapping_id'] .  '_' . $item['group'];
                return !in_array($pair, $existingPairs);
            })
            ->all();

        UserJobCode::insert($insertedDataUserJobCode);



        $updates = [];
        foreach ($dataUserJobCode as $data) {
            $name =  $data['job_code_id'] . '-' . $data['position_code_structure'] . '-' . $data['group'];

            $childRecord = $this->findUserSuperior($name);
            $parentRecord = $this->findUserSuperior($data['parent_name']);


            if ($childRecord && $parentRecord && $data['parent_id'] == 0) {
                $updates[] = [
                    'id'        => $childRecord->id,
                    'parent_id' => $parentRecord->id,
                ];
            }
        }

        if (!empty($updates)) {
            $updateQuery = UserJobCode::query();
            foreach ($updates as $update) {
                $updateQuery->orWhere('id', $update['id']);
            }

            $updateQuery->update([
                'parent_id' => DB::raw('CASE ' .
                    implode(' ', array_map(function ($update) {
                        return 'WHEN id = ' . $update['id'] . ' THEN ' . $update['parent_id'];
                    }, $updates)) .
                    ' ELSE parent_id END')
            ]);
        }
    }

    private function findUserStructureByName($name)
    {
        return UserStructureMapping::where('name', "$name")->first();
    }

    private function findUserSuperior($parentName)
    {
        $data = explode('-', $parentName);
        $arg1 = $data[0]; //job code
        $arg2 = $data[1]; // position code
        $arg3 = $data[2]; // group

        return UserJobCode::whereHas('jobCode', function ($query) use ($arg1) {
            $query->where('id', $arg1);
        })
            ->where('position_code_structure', $arg2)
            ->where('group', $arg3)->first();
    }

    private function findDataDepartment($arg1, $arg2)
    {
        return Department::whereHas('company', function ($query) use ($arg1) {
            $query->where('code', $arg1);
        })
            ->where('code', $arg2)
            ->first();
    }

    private function findDataUser($search)
    {
        return User::whereFuzzy('name', $search)->first();
        // return User::where(DB::raw('LOWER(name)'), '=', strtolower($search))
        //     ->first();
    }

    public function findDataByEmployeeNumber($search)
    {
        return UserEmployeeNumber::where('employee_number', $search)
            ->where('status', 1)
            ->first();
    }

    public function findDataByEmployeeNIK($search)
    {
        return User::where('identity_card', $search)
            ->first();
    }

    public function exportData($dataUserNotFound)
    {
        $filepath = storage_path('app/public/temp/msd-data-lo.xlsx');
        $writer  = new Writer();
        $writer->setCreator("MSD TEAM");
        $writer->openToFile($filepath);

        // 1st SHEET
        $style = new Style();
        $styleHeader = new Style();
        $styleHeader->setBackgroundColor(Color::DARK_BLUE);
        $styleHeader->setFontBold();

        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Missing Data Karyawan');
        $sheet->setColumnWidthForRange(23, 1, 25);
        $sheet->setColumnWidth(60, 13);

        $row = Row::fromValues([
            'PT',
            'DEPT',
            'ID STRUKTUR ATASAN',
            'KODE JABATAN ATASAN',
            'KODE POSISI ATASAN',
            'KODE GROUP ATASAN',
            'KODE SUFFIX',
            'KODE IP ATASAN',
            'SUB POSISI ATASAN',
            'ID STRUKTUR',
            'KODE JABATAN',
            'KODE POSISI',
            'KODE GROUP',
            'KODE SUFFIX',
            'KODE IP',
            'SUB POSISI',
            'ID STAFF',
            'NIP',
            'NIP BARU',
            'No KTP',
            'NAMA',
            'TGL NON AKTIF',
            'LEVEL'
        ], $styleHeader);

        $writer->addRow($row);

        foreach ($dataUserNotFound as $data) {
            $row = Row::fromValues($data, $style);
            $writer->addRow($row);
        }

        $writer->close();

        if (count($dataUserNotFound) > 0) {
            return $filepath;
        }
    }
}
