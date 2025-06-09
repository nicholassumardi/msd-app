<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use App\Models\UserEmployeeNumber;
use App\Models\UserServiceYear;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use OpenSpout\Reader\XLSX\Reader;

class ImportUpdatedUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $filepath;
    protected $user;
    protected $company;
    protected $department;
    public function __construct($filepath)
    {
        $this->filepath = $filepath;
        $this->user = User::all();
        $this->company =  Company::all();
        $this->department =  Department::all();
    }

    public function handle()
    {
        try {
            DB::beginTransaction();

            $reader = new Reader();
            $reader->open(storage_path('app/public/' . $this->filepath));

            $dataArrayParent = [];
            $dataArrayEmployeeNumber = [];
            $dataArrayEmployeeServiceYear = [];
            $mappingColumns = [
                'Nama'                      => 'name',
                'PT'                        => 'company_id',
                'DEPT'                      => 'department_id',
                'TANGGAL LAHIR'             => 'date_of_birth',
                'No KTP'                    => 'identity_card',
                'Status'                    => 'status',
                'Gender'                    => 'gender',
                'Agama'                     => 'religion',
                'Pendidikan'                => 'education',
                'Status Pernikahan'         => 'marital_status',
                'Alamat'                    => 'address',
                'No HP'                     => 'phone',
                'Klasifikasi'               => 'employee_type',
                'Bagian'                    => 'section',
                'Kode Posisi'               => 'position_code',
                'Status Shift'              => 'schedule_type',
                'Status TWIJI'              => 'status_twiji',
                'NOPEG'                     => 'employee_number',
                'Tanggal Masuk'             => 'join_date',
                'Tanggal Out'               => 'leave_date',
            ];
            $dataChunk = 200;

            foreach ($reader->getSheetIterator() as $i => $sheet) {
                if ($i == 2) {
                    foreach ($sheet->getRowIterator() as $key => $row) {

                        if ($key != 1) {
                            $cellValue = $mappingColumns[$row->getCells()[2]->getValue()];
                            $excludedFields = ['employee_number', 'join_date', 'leave_date'];
                            $conditions = [
                                'company_id' => function () use ($row) {
                                    $checkCell = $row->getCells()[5]->getValue();
                                    return empty($checkCell) ? null : ($this->company->firstWhere('code', $checkCell) ? $this->company->firstWhere('code', $checkCell)->id : null);
                                },
                                'department_id' => function () use ($row) {
                                    $checkCell = $row->getCells()[5]->getValue();
                                    return empty($checkCell) ? null : ($this->department->firstWhere('code', $checkCell) ? $this->department->firstWhere('code', $checkCell)->id : null);
                                },
                                'gender' => function () use ($row) {
                                    $checkCell = $row->getCells()[5]->getValue();
                                    return empty($checkCell) ? 'L' : ($checkCell == 'L' ? 'male' : 'female');
                                },
                                'status' => function () use ($row) {
                                    $checkCell = $row->getCells()[5]->getValue();
                                    return empty($checkCell) ? 1 : ($checkCell == "AKTIF" ? 1 : 0);
                                }
                            ];

                            $userId = $this->user->firstWhere('identity_card', $row->getCells()[0]->getValue()) ? $this->user->firstWhere('identity_card', $row->getCells()[0]->getValue())->id : null;

                            if (!in_array($cellValue, $excludedFields)) {
                                $data = [
                                    'id'        => $userId,
                                    $cellValue  => isset($conditions[$cellValue]) ? $conditions[$cellValue]() : $row->getCells()[5]->getValue()
                                ];
                            } else {
                                if ($cellValue == 'employee_number') {
                                    $dataChildEmployeeNumber = [
                                        'user_id'   => $userId,
                                        $cellValue  => $row->getCells()[5]->getValue()
                                    ];
                                } elseif (in_array($cellValue, ['join_date', 'leave_date'])) {
                                    $dataChildEmployeeServiceYear = [
                                        'user_id'   => $userId,
                                        $cellValue  => $row->getCells()[5]->getValue()
                                    ];
                                }
                            }

                            $dataArrayParent[] = $data ?? [];
                            $dataArrayEmployeeNumber[] = $dataChildEmployeeNumber ?? [];
                            $dataArrayEmployeeServiceYear[] = $dataChildEmployeeServiceYear ?? [];

                            if (count($dataArrayParent) == $dataChunk) {
                                $this->updateChunk($dataArrayParent, $dataArrayEmployeeNumber, $dataArrayEmployeeServiceYear);
                                $dataArrayParent = [];
                                $dataArrayEmployeeNumber = [];
                                $dataArrayEmployeeServiceYear = [];
                            }
                        }
                    }

                    if (count($dataArrayParent) != 0) {
                        $this->updateChunk($dataArrayParent, $dataArrayEmployeeNumber, $dataArrayEmployeeServiceYear);
                    }
                } else if ($sheet->getName() == "Missing Data Karyawan") {
                    foreach ($sheet->getRowIterator() as $key => $row) {
                        if ($key != 1) {

                            $dataArrayParent[] = $this->prepareUserData($row);
                            $dataArrayEmployeeNumber[] =  $this->prepareEmployeeNumberData($row);
                            $dataArrayEmployeeServiceYear[] = $this->prepareServiceYearData($row);


                            if (count($dataArrayParent) == $dataChunk) {
                                $this->updateChunk($dataArrayParent, $dataArrayEmployeeNumber, $dataArrayEmployeeServiceYear);
                                $dataArrayParent = [];
                                $dataArrayEmployeeNumber = [];
                                $dataArrayEmployeeServiceYear = [];
                            }
                        }
                    }
                    if (count($dataArrayParent) != 0) {
                        $this->updateChunk($dataArrayParent, $dataArrayEmployeeNumber, $dataArrayEmployeeServiceYear);
                    }
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    private function prepareUserData($row)
    {
        return [
            'id'              => $this->user->firstWhere('identity_card', $row->getCells()[6]->getValue())?->id,
            'name'            => $row->getCells()[2]->getValue(),
            'company_id'      => $this->company->firstWhere('code', $row->getCells()[3]->getValue())?->id ?? null,
            'department_id'   => $this->department->firstWhere('code', $row->getCells()[4]->getValue())?->id ?? null,
            'date_of_birth'   => Carbon::createFromFormat('d-M-y', $row->getCells()[5]->getValue())->format('Y-m-d'),
            'identity_card'   => $row->getCells()[6]->getValue(),
            'gender'          => $row->getCells()[9]->getValue() == 'L' ? 'male' : 'female',
            'religion'        => $row->getCells()[10]->getValue(),
            'education'       => $row->getCells()[11]->getValue(),
            'status'          => $row->getCells()[8]->getValue() == "AKTIF" ? 1 : 0,
            'marital_status'  => $row->getCells()[12]->getValue() ?? "",
            'address'         => $row->getCells()[13]->getValue() ?? "",
            'phone'           => $row->getCells()[14]->getValue() ?? "",
            'employee_type'   => $row->getCells()[15]->getValue(),
            'section'         => $row->getCells()[16]->getValue(),
            'position_code'   => $row->getCells()[17]->getValue(),
            'schedule_type'   => $row->getCells()[20]->getValue(),
            'status_twiji'    => $row->getCells()[21]->getValue(),
        ];
    }

    private function prepareEmployeeNumberData($row)
    {
        return [
            'user_id'         => $this->user->firstWhere('identity_card', $row->getCells()[6]->getValue())?->id,
            'employee_number' => $row->getCells()[1]->getValue() ?: "",
            'status'          => 1
        ];
    }

    private function prepareServiceYearData($row)
    {
        return [
            'user_id'    => $this->user->firstWhere('identity_card', $row->getCells()[6]->getValue())?->id,
            'join_date'  => $row->getCells()[22]->getValue() ? Carbon::createFromFormat('d-M-y', $row->getCells()[22]->getValue())->format('Y-m-d') : null,
            'leave_date' => $row->getCells()[23]->getValue() ? Carbon::createFromFormat('d-M-y', $row->getCells()[23]->getValue())->format('Y-m-d') : null,
        ];
    }

    public function updateChunk($dataArrayParent, $dataArrayChildEmployeeNumber, $dataArrayChildUserServiceYear)
    {
        User::upsert($dataArrayParent, ['id']);
        UserEmployeeNumber::upsert($dataArrayChildEmployeeNumber, ['user_id', 'employee_number']);
        UserServiceYear::upsert($dataArrayChildUserServiceYear, ['user_id', 'join_date']);
    }
}
