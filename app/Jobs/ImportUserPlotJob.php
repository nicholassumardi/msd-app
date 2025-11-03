<?php

namespace App\Jobs;

use App\Models\JobCode;
use App\Models\Structure;
use App\Models\StructurePlot;
use App\Models\User;
use App\Models\UserEmployeeNumber;
use App\Models\UserPlot;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;

class ImportUserPlotJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filepath;
    protected $cacheKey;
    protected $user;
    protected $jobCode;
    protected $structure;

    public function __construct($filepath, $cacheKey)
    {
        $this->filepath = $filepath;
        $this->cacheKey = $cacheKey;
        $this->user = User::all();
        $this->jobCode = JobCode::all();
        $this->structure = Structure::all();
    }

    public function handle()
    {
        try {
            DB::beginTransaction();

            $reader = new Reader();
            $reader->open(storage_path('app/public/' . $this->filepath));
            $dataUserPlot = [];
            $dataUserNotFound = [];

            foreach ($reader->getSheetIterator() as $i => $sheet) {
                $sheetCollections[$i] = LazyCollection::make(function () use ($sheet) {
                    foreach ($sheet->getRowIterator() as $key => $row) {
                        if ($key != 1) {
                            $cells = [];
                            foreach ($row->getCells() as $index => $cell) {
                                $value = $cell->getValue(); // will be cached value or formula string
                                $cells[$index] = $value;
                                if ($index == 23) {
                                    $cells[$index] = $cell->getComputedValue(); // cached result if available
                                }
                            }

                            yield $cells;
                        }
                    }
                });
            }

            if (isset($sheetCollections[1])) {
                $sheetCollections[1]->chunk(200)->each(function ($rows) use (&$dataUserPlot, &$dataUserNotFound) {
                    foreach ($rows as $row) {
                        $dataUser = $this->saveDataUserPlot($dataUserPlot, $dataUserNotFound, $row);
                        $dataUserPlot =  $dataUser['dataUserPlot'];
                        $dataUserNotFound = $dataUser['dataUserNotFound'];
                    }

                    $dataDuplicate = $this->removeDuplicate($dataUserPlot, $dataUserNotFound);
                    $dataUserPlot = $dataDuplicate['dataUserPlot'];

                    $this->insertChunkUser($dataUserPlot);
                    $dataUserPlot = [];
                });

                if (count($dataUserPlot) != 0) {
                    $dataDuplicate = $this->removeDuplicate($dataUserPlot, $dataUserNotFound);
                    $dataUserPlot = $dataDuplicate['dataUserPlot'];

                    $this->insertChunkUser($dataUserPlot);
                    $dataUserPlot = [];
                }

                $dataDuplicate = $this->removeDuplicate($dataUserPlot, $dataUserNotFound);
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

    private function saveDataUserPlot($dataUserPlot, $dataUserNotFound, $row)
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
            $dataUserPlot[] = [
                'pt'                            => $row[0],
                'dept'                          => $row[1],
                'id_structure_parent'           => $row[2],
                'job_code_parent'               => $row[3],
                'position_code_parent'          => $row[4],
                'group_parent'                  => $row[5],
                'parent_suffix'                 => $row[6],
                'code_ip_parent'                => $row[7],
                'structur_name_parent'          => $row[8],
                'id_structure'                  => $row[23],
                'job_code'                      => $row[10],
                'position_code_structure'       => $row[11],
                'group'                         => $row[12],
                'suffix'                        => $row[13],
                'code_ip'                       => $row[14],
                'sub_position'                  => $row[15],
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
            'dataUserPlot'     => $dataUserPlot,
            'dataUserNotFound' => $dataUserNotFound,
        ];
    }

    public function insertChunkUser($dataUserPlot)
    {
        $existingPairs = UserPlot::orderBy('id', 'ASC')
            ->get()
            ->map(function ($item) {
                return $item->user_id . '_' . $item->structure_plot_id . '_' . $item->id_staff;
            })->toArray();
        $updatedUserPlot = [];
        foreach ($dataUserPlot as $data) {
            $structurePlot = $this->findStructurePlot($data['id_structure']);
            if ($structurePlot) {
                $updatedUserPlot[] = [
                    'structure_plot_id'             => $structurePlot->id,
                    'user_id'                       => $data['user_id'],
                    'parent_id'                     => $data['parent_id'],
                    'id_staff'                      => $data['id_staff'],
                    'employee_type'                 => $data['employee_type'],
                    'assign_date'                   => $data['assign_date'],
                    'reassign_date'                 => $data['reassign_date'],
                    'status'                        => $data['status'],
                ];
            }
        }

        $newDataIn = collect($updatedUserPlot);

        if ($newDataIn->isNotEmpty()) {
            UserPlot::whereNotIn('user_id', $newDataIn->pluck('user_id'))
                ->whereIn('structure_plot_id', $newDataIn->pluck('structure_plot_id'))
                ->update([
                    'status'  => 0,
                ]);
        }

        $inserteddataUserPlot = $newDataIn
            ->filter(function ($item) use ($existingPairs) {
                $pair = $item['user_id'] . '_' . $item['structure_plot_id'] .  '_' . $item['id_staff'];
                return !in_array($pair, $existingPairs);
            })
            ->all();


        UserPlot::insert($inserteddataUserPlot);


        $updates = [];
        foreach ($dataUserPlot as $data) {
            $name =  $data['job_code_id'] . '-' . $data['position_code_structure'] . '-' . $data['group'];

            $childRecord = $this->findUserSuperior($name);
            $parentRecord = $this->findUserSuperior($data['parent_name']);


            if (($childRecord && $parentRecord && $data['parent_id'] == 0) || ($childRecord && $data['parent_id'] != 0)) {
                $updates[] = [
                    'id'        => $childRecord->id,
                    'parent_id' => $parentRecord->id,
                ];
            }
        }

        if (!empty($updates)) {
            $updateQuery = UserPlot::query();
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

    private function findDataUser($search)
    {
        return User::whereFuzzy('name', $search)->first();
    }

    private function findUserSuperior($parentName)
    {
        $data = explode('-', $parentName);
        $arg1 = $data[0]; //job code
        $arg2 = $data[1]; // position code
        $arg3 = $data[2]; // group

        return UserPlot::whereHas('structurePlot', function ($query) use ($arg1, $arg2, $arg3) {
            $query->whereHas('structure', function ($query) use ($arg1) {
                $query->whereHas('jobCode', function ($query) use ($arg1) {
                    $query->where('id', $arg1);
                });
            })->where('position_code_structure', $arg2)
                ->where('group', $arg3);
        })
            ->first();
    }

    public function findDataByEmployeeNumber($search)
    {
        return UserEmployeeNumber::where('employee_number', $search)
            ->where('status', 1)
            ->first();
    }

    private function findStructurePlot($arg)
    {
        return StructurePlot::where('id_structure', "$arg")->first();
    }


    public function findDataByEmployeeNIK($search)
    {
        return User::where('identity_card', $search)
            ->first();
    }

    private function removeDuplicate(&$dataUserPlot, &$dataUserNotFound)
    {
        // clean data so duplicate data will be rejected
        $duplicates = collect($dataUserPlot)
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
                    $item['position_code_parent'] ?? '',
                    $item['group_parent'] ?? '',
                    $item['parent_suffix'] ?? '',
                    $item['code_ip_parent'] ?? '',
                    $item['structur_name_parent'] ?? '', //sub_position_parent
                    $item['id_structure'] ?? '',
                    $item['job_code'] ?? '',
                    $item['position_code_structure'] ?? '',
                    $item['group'] ?? '',
                    $item['suffix'] ?? '',
                    $item['code_ip'] ?? '',
                    $item['structure_name'] ?? '', //sub_position_parent
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

        $dataUserPlot = collect($dataUserPlot)
            ->groupBy('user_id')
            ->filter(fn($group) => $group->count() === 1)
            ->flatten(1)
            ->values()
            ->all();

        return  [
            'dataUserPlot'  => $dataUserPlot,
            'dataUserNotFound' => $dataUserNotFound,
        ];
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
