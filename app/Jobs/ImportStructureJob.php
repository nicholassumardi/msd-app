<?php

namespace App\Jobs;

use App\Models\Department;
use App\Models\JobCode;
use App\Models\User;
use App\Models\UserEmployeeNumber;
use App\Models\UserJobCode;
use App\Models\UserStructureMapping;
use App\Models\UserStructureMappingHistories;
use Illuminate\Bus\Queueable;
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

            foreach ($reader->getSheetIterator() as $i => $sheet) {
                $sheetCollections[$i] = LazyCollection::make(function () use ($sheet) {
                    foreach ($sheet->getRowIterator() as $key => $row) {
                        if ($key != 1) {
                            yield $row->toArray();
                        }
                    }
                });
            }

            if (isset($sheetCollections[1])) {
                $sheetCollections[1]->chunk(200)->each(function ($rows) use (&$dataStructure, &$dataUserJobCode, &$dataUserNotFound) {

                    foreach ($rows as $row) {
                        $dataUser = $this->saveDataUserJobCode($dataUserJobCode, $dataUserNotFound, $row);

                        $dataStructure = $this->saveDataStructureMapping($dataStructure, $row);
                        $dataUserJobCode =  $dataUser['dataUserJobCode'];
                        $dataUserNotFound = $dataUser['dataUserNotFound'];
                    }

                    // clean data so duplicate data will be rejected
                    $dataDuplicate = $this->removeDuplicate($dataUserJobCode, $dataUserNotFound);
                    $dataUserJobCode = $dataDuplicate['dataUserJobCode'];

                    $this->insertChunkStructure($dataStructure, $dataUserJobCode);
                    $dataStructure = [];
                    $dataUserJobCode = [];
                });

                if (count($dataStructure) != 0) {
                    $dataDuplicate = $this->removeDuplicate($dataUserJobCode, $dataUserNotFound);
                    $dataUserJobCode = $dataDuplicate['dataUserJobCode'];

                    $this->insertChunkStructure($dataStructure, $dataUserJobCode);
                    $dataStructure = [];
                    $dataUserJobCode = [];
                }

                $dataDuplicate = $this->removeDuplicate($dataUserJobCode, $dataUserNotFound);
                $dataUserNotFound = $dataDuplicate['dataUserNotFound'];
                $filePathExportData =  $this->exportData($dataUserNotFound);
                Cache::put($this->cacheKey, $filePathExportData, now()->addMinutes(10));
            }


            if (!isset($sheetCollections[1])) {
                Cache::put(
                    $this->cacheKey,
                    [
                        'status' => 500,
                        'message' => 'Sheet not found please make sure you have the correct format!',
                    ],
                    now()->addMinutes(3)
                );
                Storage::delete($this->filepath);
                DB::rollBack();

                return false;
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


    private function saveDataStructureMapping($dataStructure, $row)
    {
        $companyName = $row[0];
        $departmentName = $row[1];
        $name = $row[15];
        $parentName = $row[8];
        $jobCode = $row[10];
        $structureType = $row[22];

        $parentId = 0;
        if ($parentName) {
            $parent = $this->findUserStructureByName($parentName);
            if ($parent) {
                $parentId = $parent->id;
            }
        }

        if (isset($dataStructure[$row[15]])) {
            $dataStructure[$row[15]]['quota'] = $dataStructure[$row[15]]['quota'] + 1;
        } else {
            $dataStructure[$row[15]] = [
                'department_id'            => $this->findDataDepartment($companyName, $departmentName)->id ?? null,
                'parent_name'              => $parentName ?? null,
                'parent_id'                => $parentId,
                'position_code_structure'  => $row[11],
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
        $userEmployeeNumber = $this->findDataByEmployeeNumber($row[18]);
        $identity_card = $this->findDataByEmployeeNIK($row[19]);
        $user = $this->findDataUser($row[20]);
        $jobCode = $this->jobCode->firstWhere('full_code', $row[10]) ? $this->jobCode->firstWhere('full_code', $row[10])->id : NULL;
        $jobCodeParent =  $this->jobCode->firstWhere('full_code', $row[3]) ? $this->jobCode->firstWhere('full_code', $row[3])->id : NULL;
        $parentName = $jobCodeParent . "-" . $row[4] . "-" . $row[5];

        $parentId = 0;
        if ($parentName) {
            $parent = $this->findUserSuperior($parentName);
            if ($parent) {
                $parentId = $parent->id;
            }
        }

        if (!$identity_card) {
            if (!$userEmployeeNumber) {
                $dataUserNotFound[] = $row;
            } elseif (!$user) {
                $dataUserNotFound[] = $row;
            }
        }


        if ($userEmployeeNumber || $user) {
            $dataUserJobCode[] = [
                'pt'                            => $row[0],
                'dept'                          => $row[1],
                'id_structure_parent'           => $row[2],
                'job_code_parent'               => $row[3],
                'position_code_structure'       => $row[4],
                'group_parent'                  => $row[5],
                'parent_suffix'                 => $row[6],
                'code_ip_parent'                => $row[7],
                'sub_position_parent'           => $row[8],
                'id_structure'                  => $row[9],
                'parent_suffix'                 => $row[10],
                'position_code_structure'       => $row[11],
                'group'                         => $row[12],
                'suffix'                        => $row[13],
                'code_ip'                       => $row[14],
                'user_structure_mapping_name'   => $row[15],
                'id_staff'                      => $row[16],
                'employee_number'               => $row[17],
                'employee_number_new'           => $row[18],
                'identity_card'                 => $row[19],
                'name'                          => $row[20],
                'non_active_date'               => $row[21],
                'employee_type'                 => $row[22],
                'user_id'                       => $userEmployeeNumber ? $userEmployeeNumber->user_id : ($user ? $user->id : NULL),
                'job_code_id'                   => $jobCode,
                'parent_name'                   => $parentName,
                'parent_id'                     => $parentId,
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
    }

    private function removeDuplicate(&$dataUserJobCode, &$dataUserNotFound)
    {
        // clean data so duplicate data will be rejected
        $duplicates = collect($dataUserJobCode)
            ->groupBy(function ($item) {
                return $item['user_id'];
            })
            ->filter(function ($group) {
                return $group->count() > 1;
            })
            ->flatten(1)
            ->values()->map(function ($item) {
                return [
                    $item['pt'] ?? '',
                    $item['dept'] ?? '',
                    $item['id_structure_parent'] ?? '',
                    $item['job_code_parent'] ?? '',
                    $item['position_code_structure'] ?? '',
                    $item['group_parent'] ?? '',
                    $item['parent_suffix'] ?? '',
                    $item['code_ip_parent'] ?? '',
                    $item['sub_position_parent'] ?? '',
                    $item['id_structure'] ?? '',
                    $item['parent_suffix'] ?? '',
                    $item['position_code_structure'] ?? '',
                    $item['parent_suffix'] ?? '',
                    $item['group'] ?? '',
                    $item['code_ip'] ?? '',
                    $item['user_structure_mapping_name'] ?? '',
                    $item['id_staff'] ?? '',
                    $item['employee_number'] ?? '',
                    $item['employee_number_new'] ?? '',
                    $item['identity_card'] ?? '',
                    $item['name'] ?? '',
                    $item['non_active_date'] ?? '',
                    $item['employee_type'] ?? '',
                ];
            })->toArray();


        $dataUserNotFound = array_merge($dataUserNotFound, $duplicates);

        $dataUserJobCode = collect($dataUserJobCode)
            ->groupBy('user_id')
            ->filter(fn($group) => $group->count() === 1)
            ->flatten(1)
            ->values()
            ->all();

        return  [
            'dataUserJobCode'  => $dataUserJobCode,
            'dataUserNotFound' => $dataUserNotFound,
        ];
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
