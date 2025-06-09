<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use App\Models\UserEmployeeNumber;
use App\Models\UserServiceYear;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Reader\XLSX\Reader;
use Illuminate\Support\Str;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;

class ImportUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filepath;
    protected $cacheKey;
    protected $user;
    protected $company;
    protected $department;

    public function __construct($filepath, $cacheKey)
    {
        $this->filepath = $filepath;
        $this->cacheKey = $cacheKey;
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
            $filePathExportData = '';

            if ($this->user->isEmpty()) {
                $dataArrayParent = [];
                $dataArrayEmployeeNumber = [];
                $dataArrayEmployeeServiceYear = [];
                $dataChunk = 200;
                foreach ($reader->getSheetIterator() as $i =>  $sheet) {
                    if ($i == 1) {
                        foreach ($sheet->getRowIterator() as $key => $row) {
                            if ($key != 1) {
                                $data = [
                                    'uuid'              => Str::uuid(),
                                    'name'              => $row->getCells()[2]->getValue(),
                                    'company_id'        => $this->company->firstWhere('code',  $row->getCells()[3]->getValue()) ? $this->company->firstWhere('code',  $row->getCells()[3]->getValue())->id : null,
                                    'department_id'     => $this->department->firstWhere('code',  $row->getCells()[4]->getValue()) ? $this->department->firstWhere('code',  $row->getCells()[4]->getValue())->id : null,
                                    'date_of_birth'     => CarbonImmutable::instance($row->getCells()[5]->getValue())->format('Y-m-d'),
                                    'identity_card'     => $row->getCells()[6]->getValue(),
                                    'gender'            => $row->getCells()[11]->getValue() == 'L' ? 'male' : 'female',
                                    'religion'          => $row->getCells()[12]->getValue(),
                                    'education'         => $row->getCells()[13]->getValue(),
                                    'status'            => $row->getCells()[8]->getValue() == "AKTIF" ? 1 : 0,
                                    'marital_status'    => $row->getCells()[14]->getValue(),
                                    'address'           => $row->getCells()[15]->getValue(),
                                    'phone'             => $row->getCells()[16]->getValue(),
                                    'employee_type'     => $row->getCells()[17]->getValue(),
                                    'section'           => $row->getCells()[18]->getValue(),
                                    'position_code'     => $row->getCells()[19]->getValue(),
                                    'schedule_type'     => $row->getCells()[22]->getValue(),
                                    'status_twiji'      => $row->getCells()[23]->getValue(),
                                    'password'          => 'asdqweqwe123',
                                    'status_account'    => 1,
                                ];


                                $dataEmployeeNumber = [
                                    'user_id'          => NULL,
                                    'employee_number'  => $row->getCells()[1]->getValue() ? $row->getCells()[1]->getValue() : "",
                                    'registry_date'    => Carbon::today()->format('Y-m-d'),
                                    'status'           => 1,
                                ];

                                $dataEmployeeServiceYear = [
                                    'user_id'          => NULL,
                                    'join_date'         => $row->getCells()[28]->getValue() ? CarbonImmutable::instance($row->getCells()[28]->getValue())->format('Y-m-d') : null,
                                    'leave_date'        => $row->getCells()[29]->getValue() ? CarbonImmutable::instance($row->getCells()[29]->getValue())->format('Y-m-d') : null,
                                ];


                                $dataArrayParent[] = $data;
                                $dataArrayEmployeeNumber[] = $dataEmployeeNumber;
                                $dataArrayEmployeeServiceYear[] = $dataEmployeeServiceYear;

                                if (count($dataArrayParent) == $dataChunk) {
                                    $this->insertChunk($dataArrayParent, $dataArrayEmployeeNumber, $dataArrayEmployeeServiceYear);
                                    $dataArrayParent = [];
                                    $dataArrayEmployeeNumber = [];
                                    $dataArrayEmployeeServiceYear = [];
                                }
                            }
                        }
                    }
                }

                if (count($dataArrayParent) != 0) {
                    $this->insertChunk($dataArrayParent, $dataArrayEmployeeNumber,  $dataArrayEmployeeServiceYear);
                }
            } else {
                $identityCard = [];
                $newData = [];
                $sheetConfigs = [
                    1 => [
                        'name' => 1,
                        'company_id' => ['code' => 'KAS'],
                        'department_id' => 4,
                        'date_of_birth' => 13,
                        'identity_card' => 9,
                        'gender' => 7,
                        'religion' => 5,
                        'education' => 10,
                        'status' => 1,
                        'marital_status' => 12,
                        'address' => 6,
                        'section' => 3,
                        'status_account' => 1,
                        'employee_number' => 0,
                        'join_date' => null,
                        'leave_date' => null,
                    ],
                    2 => [
                        'name' => 1,
                        'company_id' => 1,
                        'department_id' => 4,
                        'date_of_birth' => 13,
                        'identity_card' => 9,
                        'gender' => 7,
                        'religion' => 5,
                        'education' => 10,
                        'status' => 1,
                        'marital_status' => 12,
                        'address' => 6,
                        'section' => 3,
                        'status_account' => 1,
                        'employee_number' => 3,
                        'join_date' => 28,
                        'leave_date' => 29,
                    ],
                    3 => [
                        'name' => 3,
                        'company_id' => 6,
                        'department_id' => 4,
                        'date_of_birth' => 9,
                        'identity_card' => 2,
                        'gender' => 11,
                        'status' => 1,
                        'section' => 5,
                        'status_account' => 1,
                        'employee_number' => 1,
                        'join_date' => 10,
                        'leave_date' => null,
                    ],
                    4 => [
                        'name' => 4,
                        'company_id' => 1,
                        'department_id' => 5,
                        'date_of_birth' => 11,
                        'identity_card' => 12,
                        'gender' => 13,
                        'religion' => 14,
                        'education' => 15,
                        'status' => 1,
                        'marital_status' => 16,
                        'address' => 17,
                        'section' => 6,
                        'phone' => 18,
                        'position_code' => 7,
                        'schedule_type' => 8,
                        'status_account' => 1,
                        'employee_number' => 3,
                        'join_date' => 19,
                        'leave_date' => 20,
                    ],
                    5 => [
                        'name' => 4,
                        'company_id' => 1,
                        'department_id' => 5,
                        'date_of_birth' => 12,
                        'identity_card' => 13,
                        'gender' => 11,
                        'education' => 15,
                        'status' => 1,
                        'marital_status' => 16,
                        'address' => 14,
                        'section' => 6,
                        'phone' => 15,
                        'position_code' => 7,
                        'schedule_type' => 8,
                        'status_account' => 1,
                        'employee_number' => 3,
                        'join_date' => 16,
                        'leave_date' => 17,
                    ],
                    6 => [
                        'name' => 4,
                        'company_id' => 1,
                        'department_id' => 5,
                        'date_of_birth' => 12,
                        'identity_card' => 13,
                        'gender' => 11,
                        'status' => 1,
                        'address' => 14,
                        'section' => 6,
                        'phone' => 15,
                        'position_code' => 7,
                        'schedule_type' => 8,
                        'status_account' => 1,
                        'employee_number' => 3,
                        'join_date' => 28,
                        'leave_date' => 29,
                    ],
                    7 => [
                        'name' => 4,
                        'company_id' => 1,
                        'department_id' => 8,
                        'date_of_birth' => 7,
                        'identity_card' => 16,
                        'gender' => 14,
                        'religion' => 6,
                        'education' => 5,
                        'status' => 1,
                        'marital_status' => 7,
                        'address' => 17,
                        'section' => 9,
                        'phone' => 15,
                        'position_code' => 10,
                        'schedule_type' => 11,
                        'status_account' => 1,
                        'employee_number' => 3,
                        'join_date' => 31,
                        'leave_date' => 32,
                    ],
                    8 => [
                        'name' => 4,
                        'company_id' => 1,
                        'department_id' => 5,
                        'date_of_birth' => 12,
                        'identity_card' => 13,
                        'gender' => 11,
                        'education' => 5,
                        'status' => 1,
                        'address' => 14,
                        'section' => 6,
                        'phone' => 15,
                        'position_code' => 7,
                        'status_account' => 1,
                        'employee_number' => 3,
                        'join_date' => 28,
                        'leave_date' => 29,
                    ],
                    9 => [
                        'name' => 4,
                        'company_id' => 1,
                        'department_id' => 5,
                        'date_of_birth' => 12,
                        'identity_card' => 13,
                        'gender' => 11,
                        'religion' => 18,
                        'education' => 19,
                        'status' => 1,
                        'marital_status' => 21,
                        'address' => 14,
                        'section' => 6,
                        'phone' => 15,
                        'position_code' => 7,
                        'schedule_type' => 8,
                        'status_account' => 1,
                        'employee_number' => 3,
                        'join_date' => 16,
                        'leave_date' => 17,
                    ],
                ];

                foreach ($reader->getSheetIterator() as $i => $sheet) {
                    if ($sheet->getName() == "Data Karyawan") {
                        foreach ($sheet->getRowIterator() as $key => $row) {
                            if ($key != 1) {
                                $identityCard[] =  $row->getCells()[6]->getValue();
                                $config = $sheetConfigs[$i] ?? [];
                                $processedData = $this->processRow($row, $config);
                                $newData[] = $processedData;
                                // $newData[] = [
                                //     'employee_number'   => $row->getCells()[1]->getValue() ? $row->getCells()[1]->getValue() : "",
                                //     'name'              => $row->getCells()[2]->getValue(),
                                //     'company_id'        => $row->getCells()[3]->getValue(),
                                //     'department_id'     => $row->getCells()[4]->getValue(),
                                //     'date_of_birth'     => CarbonImmutable::instance($row->getCells()[5]->getValue())->format('Y-m-d'),
                                //     'identity_card'     => $row->getCells()[6]->getValue(),
                                //     'gender'            => $row->getCells()[11]->getValue(),
                                //     'religion'          => $row->getCells()[12]->getValue(),
                                //     'education'         => $row->getCells()[13]->getValue(),
                                //     'status'            => $row->getCells()[8]->getValue(),
                                //     'marital_status'    => $row->getCells()[14]->getValue(),
                                //     'address'           => $row->getCells()[15]->getValue(),
                                //     'phone'             => $row->getCells()[16]->getValue(),
                                //     'employee_type'     => $row->getCells()[17]->getValue(),
                                //     'section'           => $row->getCells()[18]->getValue(),
                                //     'position_code'     => $row->getCells()[19]->getValue(),
                                //     'schedule_type'     => $row->getCells()[22]->getValue(),
                                //     'status_twiji'      => $row->getCells()[23]->getValue(),
                                //     'join_date'         => $row->getCells()[28]->getValue() ? CarbonImmutable::instance($row->getCells()[28]->getValue())->format('Y-m-d') : null,
                                //     'leave_date'        => $row->getCells()[29]->getValue() ? CarbonImmutable::instance($row->getCells()[29]->getValue())->format('Y-m-d') : null,

                                // ];
                            }
                        }
                    }
                }

                $missingData = $this->user->whereNotIn('identity_card', $identityCard)->map(function ($data) {
                    return [
                        'employee_number'    => $data->userEmployeeNumber()->where('status', 1)->first()->employee_number ?? "",
                        'name'               => $data->name,
                        'company_name'       => $data->company ? $data->company->code : '',
                        'department_name'    => $data->department ? $data->department->code : '',
                        'date_of_birth'      => date('d-M-y', strtotime($data->date_of_birth)),
                        'identity_card'      => $data->identity_card,
                        'unicode'            => $data->name . " - " . $data->identity_card,
                        'status'             => $data->status == 1 ? "AKTIF" : "OUT",
                        'gender'             => $data->gender == "female" ? "P" : "L",
                        'religion'           => $data->religion,
                        'education'          => $data->education,
                        'marital_status'     => strtoupper($data->marital_status),
                        'address'            => $data->address,
                        'phone'              => $data->phone,
                        'employee_type'      => $data->employee_type,
                        'section'            => $data->section,
                        'position_code'      => $data->position_code,
                        'roleCode'           => $data->userJobCode()->where('status', 1)->first()->jobCode->full_code ?? "",
                        'group'              => $data->userJobCode()->where('status', 1)->first()->jobCode->group ?? "",
                        'schedule_type'      => $data->schedule_type,
                        'status_twiji'       => $data->status_twiji,
                        'join_date'          => $data->userServiceYear ? ($data->userServiceYear->join_date ? date('d-M-y', strtotime($data->userServiceYear->join_date)) : null) : null,
                        'leave_date'         => $data->userServiceYear ? ($data->userServiceYear->leave_date ? date('d-M-y', strtotime($data->userServiceYear->leave_date)) : null) : null,
                    ];
                })->toArray();

                $oldData = $this->user
                    ->whereIn('identity_card', $identityCard)
                    ->mapWithKeys(function ($user) {
                        $data = $user->toArray();

                        $data['company_id'] = $user->company ?  $user->company->code : null;
                        $data['department_id'] = $user->department ?  $user->department->code : null;
                        $data['status'] = $user->status == '1' ? "AKTIF" : "OUT";
                        $data['gender'] = $user->gender == "female" ? 'P' : 'L';


                        $data['employee_number'] = $user->userEmployeeNumber()
                            ->latest()
                            ->first(['employee_number'])
                            ->employee_number ?? "";

                        $data['join_date']  = $user->userServiceYear->join_date ??  null;
                        $data['leave_date']  = $user->userServiceYear->leave_date ??  null;


                        return [$user->identity_card => $data];
                    })
                    ->toArray();
                $dataDiff = $this->getDifferenceData($oldData, $newData);
                $filePathExportData =  $this->exportData($missingData, $dataDiff);
                Cache::put($this->cacheKey, $filePathExportData, now()->addMinutes(10));
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

    // public function handle()
    // {
    //     try {
    //         DB::beginTransaction();

    //         $reader = new Reader();
    //         $reader->open(storage_path('app/public/' . $this->filepath));
    //         $filePathExportData = '';

    //         if ($this->user->isEmpty()) {
    //             $dataArrayParent = [];
    //             $dataArrayEmployeeNumber = [];
    //             $dataArrayEmployeeServiceYear = [];
    //             $dataChunk = 200;
    //             foreach ($reader->getSheetIterator() as $i =>  $sheet) {
    //                 if ($i == 1) {
    //                     foreach ($sheet->getRowIterator() as $key => $row) {
    //                         if ($key != 1) {
    //                             $data = [
    //                                 'uuid'              => Str::uuid(),
    //                                 'name'              => $row->getCells()[1]->getValue(),
    //                                 'company_id'        => $this->company->firstWhere('code', 'KAS') ? $this->company->firstWhere('code', 'KAS')->id : null,
    //                                 'department_id'     => $this->department->firstWhere('code',  $row->getCells()[4]->getValue()) ? $this->department->firstWhere('code',  $row->getCells()[4]->getValue())->id : null,
    //                                 'date_of_birth'     => date('Y-m-d', strtotime($row->getCells()[13]->getValue())),
    //                                 'identity_card'     => $row->getCells()[9]->getValue(),
    //                                 'gender'            => $row->getCells()[7]->getValue() == 'L' ? 'male' : 'female',
    //                                 'religion'          => $row->getCells()[5]->getValue(),
    //                                 'education'         => $row->getCells()[10]->getValue(),
    //                                 'status'            => 1,
    //                                 'marital_status'    => $row->getCells()[12]->getValue(),
    //                                 'address'           => $row->getCells()[6]->getValue(),
    //                                 'section'           => $row->getCells()[3]->getValue(),
    //                                 // 'phone'             => $row->getCells()[16]->getValue(),
    //                                 // 'employee_type'     => $row->getCells()[17]->getValue(),
    //                                 // 'position_code'     => $row->getCells()[19]->getValue(),
    //                                 // 'schedule_type'     => $row->getCells()[22]->getValue(),
    //                                 // 'status_twiji'      => $row->getCells()[23]->getValue(),
    //                                 'password'          => 'asdqweqwe123',
    //                                 'status_account'    => 1,
    //                             ];

    //                             $dataEmployeeNumber = [
    //                                 'user_id'          => NULL,
    //                                 'employee_number'  => $row->getCells()[1]->getValue() ? $row->getCells()[0]->getValue() : "",
    //                                 'registry_date'    => Carbon::today()->format('Y-m-d'),
    //                                 'status'           => 1,
    //                             ];

    //                             $dataEmployeeServiceYear = [
    //                                 'user_id'          => NULL,
    //                                 'join_date'         => $row->getCells()[28]->getValue() ? CarbonImmutable::instance($row->getCells()[28]->getValue())->format('Y-m-d') : null,
    //                                 'leave_date'        => $row->getCells()[29]->getValue() ? CarbonImmutable::instance($row->getCells()[29]->getValue())->format('Y-m-d') : null,
    //                             ];

    //                             // old
    //                             // $data = [
    //                             //     'uuid'              => Str::uuid(),
    //                             //     'name'              => $row->getCells()[2]->getValue(),
    //                             //     'company_id'        => $this->company->firstWhere('code',  $row->getCells()[3]->getValue()) ? $this->company->firstWhere('code',  $row->getCells()[3]->getValue())->id : null,
    //                             //     'department_id'     => $this->department->firstWhere('code',  $row->getCells()[4]->getValue()) ? $this->department->firstWhere('code',  $row->getCells()[4]->getValue())->id : null,
    //                             //     'date_of_birth'     => CarbonImmutable::instance($row->getCells()[5]->getValue())->format('Y-m-d'),
    //                             //     'identity_card'     => $row->getCells()[6]->getValue(),
    //                             //     'gender'            => $row->getCells()[11]->getValue() == 'L' ? 'male' : 'female',
    //                             //     'religion'          => $row->getCells()[12]->getValue(),
    //                             //     'education'         => $row->getCells()[13]->getValue(),
    //                             //     'status'            => $row->getCells()[8]->getValue() == "AKTIF" ? 1 : 0,
    //                             //     'marital_status'    => $row->getCells()[14]->getValue(),
    //                             //     'address'           => $row->getCells()[15]->getValue(),
    //                             //     'phone'             => $row->getCells()[16]->getValue(),
    //                             //     'employee_type'     => $row->getCells()[17]->getValue(),
    //                             //     'section'           => $row->getCells()[18]->getValue(),
    //                             //     'position_code'     => $row->getCells()[19]->getValue(),
    //                             //     'schedule_type'     => $row->getCells()[22]->getValue(),
    //                             //     'status_twiji'      => $row->getCells()[23]->getValue(),
    //                             //     'password'          => 'asdqweqwe123',
    //                             //     'status_account'    => 1,
    //                             // ];


    //                             // $dataEmployeeNumber = [
    //                             //     'user_id'          => NULL,
    //                             //     'employee_number'  => $row->getCells()[1]->getValue() ? $row->getCells()[1]->getValue() : "",
    //                             //     'registry_date'    => Carbon::today()->format('Y-m-d'),
    //                             //     'status'           => 1,
    //                             // ];

    //                             // $dataEmployeeServiceYear = [
    //                             //     'user_id'          => NULL,
    //                             //     'join_date'         => $row->getCells()[28]->getValue() ? CarbonImmutable::instance($row->getCells()[28]->getValue())->format('Y-m-d') : null,
    //                             //     'leave_date'        => $row->getCells()[29]->getValue() ? CarbonImmutable::instance($row->getCells()[29]->getValue())->format('Y-m-d') : null,
    //                             // ];


    //                             $dataArrayParent[] = $data;
    //                             $dataArrayEmployeeNumber[] = $dataEmployeeNumber;
    //                             $dataArrayEmployeeServiceYear[] = $dataEmployeeServiceYear;

    //                             if (count($dataArrayParent) == $dataChunk) {
    //                                 $this->insertChunk($dataArrayParent, $dataArrayEmployeeNumber, $dataArrayEmployeeServiceYear);
    //                                 $dataArrayParent = [];
    //                                 $dataArrayEmployeeNumber = [];
    //                                 $dataArrayEmployeeServiceYear = [];
    //                             }
    //                         }
    //                     }
    //                 } else if ($i == 2) {
    //                     foreach ($sheet->getRowIterator() as $key => $row) {
    //                         if ($key != 1) {
    //                             $data = [
    //                                 'uuid'              => Str::uuid(),
    //                                 'name'              => $row->getCells()[1]->getValue(),
    //                                 'company_id'        => $this->company->firstWhere('code', 'KAS') ? $this->company->firstWhere('code', 'KAS')->id : null,
    //                                 'department_id'     => $this->department->firstWhere('code',  $row->getCells()[4]->getValue()) ? $this->department->firstWhere('code',  $row->getCells()[4]->getValue())->id : null,
    //                                 'date_of_birth'     => date('Y-m-d', strtotime($row->getCells()[13]->getValue())),
    //                                 'identity_card'     => $row->getCells()[9]->getValue(),
    //                                 'gender'            => $row->getCells()[7]->getValue() == 'L' ? 'male' : 'female',
    //                                 'religion'          => $row->getCells()[5]->getValue(),
    //                                 'education'         => $row->getCells()[10]->getValue(),
    //                                 'status'            => 1,
    //                                 'marital_status'    => $row->getCells()[12]->getValue(),
    //                                 'address'           => $row->getCells()[6]->getValue(),
    //                                 'section'           => $row->getCells()[3]->getValue(),
    //                                 // 'phone'             => $row->getCells()[16]->getValue(),
    //                                 // 'employee_type'     => $row->getCells()[17]->getValue(),
    //                                 // 'position_code'     => $row->getCells()[19]->getValue(),
    //                                 // 'schedule_type'     => $row->getCells()[22]->getValue(),
    //                                 // 'status_twiji'      => $row->getCells()[23]->getValue(),
    //                                 'password'          => 'asdqweqwe123',
    //                                 'status_account'    => 1,
    //                             ];

    //                             $dataEmployeeNumber = [
    //                                 'user_id'          => NULL,
    //                                 'employee_number'  => $row->getCells()[1]->getValue() ? $row->getCells()[0]->getValue() : "",
    //                                 'registry_date'    => Carbon::today()->format('Y-m-d'),
    //                                 'status'           => 1,
    //                             ];

    //                             $dataEmployeeServiceYear = [
    //                                 'user_id'          => NULL,
    //                                 'join_date'         => $row->getCells()[28]->getValue() ? CarbonImmutable::instance($row->getCells()[28]->getValue())->format('Y-m-d') : null,
    //                                 'leave_date'        => $row->getCells()[29]->getValue() ? CarbonImmutable::instance($row->getCells()[29]->getValue())->format('Y-m-d') : null,
    //                             ];


    //                             $dataArrayParent[] = $data;
    //                             $dataArrayEmployeeNumber[] = $dataEmployeeNumber;
    //                             $dataArrayEmployeeServiceYear[] = $dataEmployeeServiceYear;

    //                             if (count($dataArrayParent) == $dataChunk) {
    //                                 $this->insertChunk($dataArrayParent, $dataArrayEmployeeNumber, $dataArrayEmployeeServiceYear);
    //                                 $dataArrayParent = [];
    //                                 $dataArrayEmployeeNumber = [];
    //                                 $dataArrayEmployeeServiceYear = [];
    //                             }
    //                         }
    //                     }
    //                 } else if ($i == 3) {
    //                     // GOWA
    //                     foreach ($sheet->getRowIterator() as $key => $row) {
    //                         if ($key != 1) {
    //                             $data = [
    //                                 'uuid'              => Str::uuid(),
    //                                 'name'              => $row->getCells()[3]->getValue(),
    //                                 'company_id'        => $this->company->firstWhere('code',  $row->getCells()[6]->getValue()) ? $this->company->firstWhere('code',  $row->getCells()[6]->getValue())->id : null,
    //                                 'department_id'     => $this->department->firstWhere('code',  $row->getCells()[4]->getValue()) ? $this->department->firstWhere('code',  $row->getCells()[4]->getValue())->id : null,
    //                                 'date_of_birth'     => date('Y-m-d', strtotime($row->getCells()[9]->getValue())),
    //                                 'identity_card'     => $row->getCells()[2]->getValue(),
    //                                 'gender'            => $row->getCells()[11]->getValue() == 'L' ? 'male' : 'female',
    //                                 // 'religion'          => $row->getCells()[5]->getValue(),
    //                                 // 'education'         => $row->getCells()[10]->getValue(),
    //                                 'status'            => 1,
    //                                 // 'marital_status'    => $row->getCells()[12]->getValue(),
    //                                 // 'address'           => $row->getCells()[6]->getValue(),
    //                                 'section'           => $row->getCells()[5]->getValue(),
    //                                 // 'phone'             => $row->getCells()[16]->getValue(),
    //                                 // 'employee_type'     => $row->getCells()[17]->getValue(),
    //                                 // 'position_code'     => $row->getCells()[19]->getValue(),
    //                                 // 'schedule_type'     => $row->getCells()[22]->getValue(),
    //                                 // 'status_twiji'      => $row->getCells()[23]->getValue(),
    //                                 'password'          => 'asdqweqwe123',
    //                                 'status_account'    => 1,
    //                             ];

    //                             $dataEmployeeNumber = [
    //                                 'user_id'          => NULL,
    //                                 'employee_number'  => $row->getCells()[1]->getValue() ? $row->getCells()[1]->getValue() : "",
    //                                 'registry_date'    => Carbon::today()->format('Y-m-d'),
    //                                 'status'           => 1,
    //                             ];

    //                             $dataEmployeeServiceYear = [
    //                                 'user_id'          => NULL,
    //                                 'join_date'         => $row->getCells()[10]->getValue() ? date('Y-m-d', strtotime($row->getCells()[10]->getValue())) : null,
    //                                 'leave_date'        =>  null,
    //                             ];


    //                             $dataArrayParent[] = $data;
    //                             $dataArrayEmployeeNumber[] = $dataEmployeeNumber;
    //                             $dataArrayEmployeeServiceYear[] = $dataEmployeeServiceYear;

    //                             if (count($dataArrayParent) == $dataChunk) {
    //                                 $this->insertChunk($dataArrayParent, $dataArrayEmployeeNumber, $dataArrayEmployeeServiceYear);
    //                                 $dataArrayParent = [];
    //                                 $dataArrayEmployeeNumber = [];
    //                                 $dataArrayEmployeeServiceYear = [];
    //                             }
    //                         }
    //                     }
    //                 } else if ($i == 4) {
    //                     // MDU
    //                     foreach ($sheet->getRowIterator() as $key => $row) {
    //                         if ($key != 1) {
    //                             $data = [
    //                                 'uuid'              => Str::uuid(),
    //                                 'name'              => $row->getCells()[4]->getValue(),
    //                                 'company_id'        => $this->company->firstWhere('code',  $row->getCells()[1]->getValue()) ? $this->company->firstWhere('code',  $row->getCells()[1]->getValue())->id : null,
    //                                 'department_id'     => $this->department->firstWhere('code',  $row->getCells()[5]->getValue()) ? $this->department->firstWhere('code',  $row->getCells()[5]->getValue())->id : null,
    //                                 'date_of_birth'     => date('Y-m-d', strtotime($row->getCells()[11]->getValue())),
    //                                 'identity_card'     => $row->getCells()[12]->getValue(),
    //                                 'gender'            => $row->getCells()[13]->getValue() == 'L' ? 'male' : 'female',
    //                                 'religion'          => $row->getCells()[14]->getValue(),
    //                                 'education'         => $row->getCells()[15]->getValue(),
    //                                 'status'            => 1,
    //                                 'marital_status'    => $row->getCells()[16]->getValue(),
    //                                 'address'           => $row->getCells()[17]->getValue(),
    //                                 'section'           => $row->getCells()[6]->getValue(),
    //                                 'phone'             => $row->getCells()[18]->getValue(),
    //                                 // 'employee_type'     => $row->getCells()[17]->getValue(),
    //                                 'position_code'     => $row->getCells()[7]->getValue(),
    //                                 'schedule_type'     => $row->getCells()[8]->getValue(),
    //                                 // 'status_twiji'      => $row->getCells()[23]->getValue(),
    //                                 'password'          => 'asdqweqwe123',
    //                                 'status_account'    => 1,
    //                             ];

    //                             $dataEmployeeNumber = [
    //                                 'user_id'          => NULL,
    //                                 'employee_number'  => $row->getCells()[3]->getValue() ? $row->getCells()[3]->getValue() : "",
    //                                 'registry_date'    => Carbon::today()->format('Y-m-d'),
    //                                 'status'           => 1,
    //                             ];

    //                             $dataEmployeeServiceYear = [
    //                                 'user_id'           => NULL,
    //                                 'join_date'         => $row->getCells()[19]->getValue() ? date('Y-m-d', strtotime($row->getCells()[19]->getValue())) : null,
    //                                 'leave_date'        =>  $row->getCells()[20]->getValue() ? date('Y-m-d', strtotime($row->getCells()[20]->getValue())) : null,
    //                             ];


    //                             $dataArrayParent[] = $data;
    //                             $dataArrayEmployeeNumber[] = $dataEmployeeNumber;
    //                             $dataArrayEmployeeServiceYear[] = $dataEmployeeServiceYear;

    //                             if (count($dataArrayParent) == $dataChunk) {
    //                                 $this->insertChunk($dataArrayParent, $dataArrayEmployeeNumber, $dataArrayEmployeeServiceYear);
    //                                 $dataArrayParent = [];
    //                                 $dataArrayEmployeeNumber = [];
    //                                 $dataArrayEmployeeServiceYear = [];
    //                             }
    //                         }
    //                     }
    //                 } else if ($i == 5) {
    //                     // EPE, NPA, CES
    //                     foreach ($sheet->getRowIterator() as $key => $row) {
    //                         if ($key != 1) {
    //                             $data = [
    //                                 'uuid'              => Str::uuid(),
    //                                 'name'              => $row->getCells()[4]->getValue(),
    //                                 'company_id'        => $this->company->firstWhere('code',  $row->getCells()[1]->getValue()) ? $this->company->firstWhere('code',  $row->getCells()[1]->getValue())->id : null,
    //                                 'department_id'     => $this->department->firstWhere('code',  $row->getCells()[5]->getValue()) ? $this->department->firstWhere('code',  $row->getCells()[5]->getValue())->id : null,
    //                                 'date_of_birth'     => date('Y-m-d', strtotime($row->getCells()[12]->getValue())),
    //                                 'identity_card'     => $row->getCells()[13]->getValue(),
    //                                 'gender'            => $row->getCells()[11]->getValue() == 'L' ? 'male' : 'female',
    //                                 // 'religion'          => $row->getCells()[14]->getValue(),
    //                                 'education'         => $row->getCells()[15]->getValue(),
    //                                 'status'            => 1,
    //                                 'marital_status'    => $row->getCells()[16]->getValue(),
    //                                 'address'           => $row->getCells()[14]->getValue(),
    //                                 'section'           => $row->getCells()[6]->getValue(),
    //                                 'phone'             => $row->getCells()[15]->getValue(),
    //                                 // 'employee_type'     => $row->getCells()[17]->getValue(),
    //                                 'position_code'     => $row->getCells()[7]->getValue(),
    //                                 'schedule_type'     => $row->getCells()[8]->getValue(),
    //                                 // 'status_twiji'      => $row->getCells()[23]->getValue(),
    //                                 'password'          => 'asdqweqwe123',
    //                                 'status_account'    => 1,
    //                             ];

    //                             $dataEmployeeNumber = [
    //                                 'user_id'          => NULL,
    //                                 'employee_number'  => $row->getCells()[3]->getValue() ? $row->getCells()[3]->getValue() : "",
    //                                 'registry_date'    => Carbon::today()->format('Y-m-d'),
    //                                 'status'           => 1,
    //                             ];

    //                             $dataEmployeeServiceYear = [
    //                                 'user_id'           => NULL,
    //                                 'join_date'         => $row->getCells()[16]->getValue() ? date('Y-m-d', strtotime($row->getCells()[16]->getValue())) : null,
    //                                 'leave_date'        =>  $row->getCells()[17]->getValue() ? date('Y-m-d', strtotime($row->getCells()[17]->getValue())) : null,
    //                             ];


    //                             $dataArrayParent[] = $data;
    //                             $dataArrayEmployeeNumber[] = $dataEmployeeNumber;
    //                             $dataArrayEmployeeServiceYear[] = $dataEmployeeServiceYear;

    //                             if (count($dataArrayParent) == $dataChunk) {
    //                                 $this->insertChunk($dataArrayParent, $dataArrayEmployeeNumber, $dataArrayEmployeeServiceYear);
    //                                 $dataArrayParent = [];
    //                                 $dataArrayEmployeeNumber = [];
    //                                 $dataArrayEmployeeServiceYear = [];
    //                             }
    //                         }
    //                     }
    //                 } else if ($i == 6) {
    //                     // HAS
    //                     foreach ($sheet->getRowIterator() as $key => $row) {
    //                         if ($key != 1) {
    //                             $data = [
    //                                 'uuid'              => Str::uuid(),
    //                                 'name'              => $row->getCells()[4]->getValue(),
    //                                 'company_id'        => $this->company->firstWhere('code',  $row->getCells()[1]->getValue()) ? $this->company->firstWhere('code',  $row->getCells()[1]->getValue())->id : null,
    //                                 'department_id'     => $this->department->firstWhere('code',  $row->getCells()[5]->getValue()) ? $this->department->firstWhere('code',  $row->getCells()[5]->getValue())->id : null,
    //                                 'date_of_birth'     => date('Y-m-d', strtotime($row->getCells()[12]->getValue())),
    //                                 'identity_card'     => $row->getCells()[13]->getValue(),
    //                                 'gender'            => $row->getCells()[11]->getValue() == 'L' ? 'male' : 'female',
    //                                 // 'religion'          => $row->getCells()[14]->getValue(),
    //                                 // 'education'         => $row->getCells()[15]->getValue(),
    //                                 'status'            => 1,
    //                                 // 'marital_status'    => $row->getCells()[16]->getValue(),
    //                                 'address'           => $row->getCells()[14]->getValue(),
    //                                 'section'           => $row->getCells()[6]->getValue(),
    //                                 'phone'             => $row->getCells()[15]->getValue(),
    //                                 // 'employee_type'     => $row->getCells()[17]->getValue(),
    //                                 'position_code'     => $row->getCells()[7]->getValue(),
    //                                 'schedule_type'     => $row->getCells()[8]->getValue(),
    //                                 // 'status_twiji'      => $row->getCells()[23]->getValue(),
    //                                 'password'          => 'asdqweqwe123',
    //                                 'status_account'    => 1,
    //                             ];

    //                             $dataEmployeeNumber = [
    //                                 'user_id'          => NULL,
    //                                 'employee_number'  => $row->getCells()[3]->getValue() ? $row->getCells()[3]->getValue() : "",
    //                                 'registry_date'    => Carbon::today()->format('Y-m-d'),
    //                                 'status'           => 1,
    //                             ];

    //                             $dataEmployeeServiceYear = [
    //                                 'user_id'           => NULL,
    //                                 'join_date'         => $row->getCells()[28]->getValue() ? date('Y-m-d', strtotime($row->getCells()[28]->getValue())) : null,
    //                                 'leave_date'        =>  $row->getCells()[29]->getValue() ? date('Y-m-d', strtotime($row->getCells()[29]->getValue())) : null,
    //                             ];


    //                             $dataArrayParent[] = $data;
    //                             $dataArrayEmployeeNumber[] = $dataEmployeeNumber;
    //                             $dataArrayEmployeeServiceYear[] = $dataEmployeeServiceYear;

    //                             if (count($dataArrayParent) == $dataChunk) {
    //                                 $this->insertChunk($dataArrayParent, $dataArrayEmployeeNumber, $dataArrayEmployeeServiceYear);
    //                                 $dataArrayParent = [];
    //                                 $dataArrayEmployeeNumber = [];
    //                                 $dataArrayEmployeeServiceYear = [];
    //                             }
    //                         }
    //                     }
    //                 } else if ($i == 7) {
    //                     // HAS
    //                     foreach ($sheet->getRowIterator() as $key => $row) {
    //                         if ($key != 1) {
    //                             $data = [
    //                                 'uuid'              => Str::uuid(),
    //                                 'name'              => $row->getCells()[4]->getValue(),
    //                                 'company_id'        => $this->company->firstWhere('code',  $row->getCells()[1]->getValue()) ? $this->company->firstWhere('code',  $row->getCells()[1]->getValue())->id : null,
    //                                 'department_id'     => $this->department->firstWhere('code',  $row->getCells()[8]->getValue()) ? $this->department->firstWhere('code',  $row->getCells()[8]->getValue())->id : null,
    //                                 'date_of_birth'     => date('Y-m-d', strtotime($row->getCells()[7]->getValue())),
    //                                 'identity_card'     => $row->getCells()[16]->getValue(),
    //                                 'gender'            => $row->getCells()[14]->getValue() == 'L' ? 'male' : 'female',
    //                                 'religion'          => $row->getCells()[6]->getValue(),
    //                                 'education'         => $row->getCells()[5]->getValue(),
    //                                 'status'            => 1,
    //                                 'marital_status'    => $row->getCells()[7]->getValue(),
    //                                 'address'           => $row->getCells()[17]->getValue(),
    //                                 'section'           => $row->getCells()[9]->getValue(),
    //                                 'phone'             => $row->getCells()[15]->getValue(),
    //                                 // 'employee_type'     => $row->getCells()[17]->getValue(),
    //                                 'position_code'     => $row->getCells()[10]->getValue(),
    //                                 'schedule_type'     => $row->getCells()[11]->getValue(),
    //                                 // 'status_twiji'      => $row->getCells()[23]->getValue(),
    //                                 'password'          => 'asdqweqwe123',
    //                                 'status_account'    => 1,
    //                             ];

    //                             $dataEmployeeNumber = [
    //                                 'user_id'          => NULL,
    //                                 'employee_number'  => $row->getCells()[3]->getValue() ? $row->getCells()[3]->getValue() : "",
    //                                 'registry_date'    => Carbon::today()->format('Y-m-d'),
    //                                 'status'           => 1,
    //                             ];

    //                             $dataEmployeeServiceYear = [
    //                                 'user_id'           => NULL,
    //                                 'join_date'         => $row->getCells()[31]->getValue() ? date('Y-m-d', strtotime($row->getCells()[31]->getValue())) : null,
    //                                 'leave_date'        =>  $row->getCells()[32]->getValue() ? date('Y-m-d', strtotime($row->getCells()[32]->getValue())) : null,
    //                             ];


    //                             $dataArrayParent[] = $data;
    //                             $dataArrayEmployeeNumber[] = $dataEmployeeNumber;
    //                             $dataArrayEmployeeServiceYear[] = $dataEmployeeServiceYear;

    //                             if (count($dataArrayParent) == $dataChunk) {
    //                                 $this->insertChunk($dataArrayParent, $dataArrayEmployeeNumber, $dataArrayEmployeeServiceYear);
    //                                 $dataArrayParent = [];
    //                                 $dataArrayEmployeeNumber = [];
    //                                 $dataArrayEmployeeServiceYear = [];
    //                             }
    //                         }
    //                     }
    //                 } else if ($i == 8) {
    //                     // KIAS
    //                     foreach ($sheet->getRowIterator() as $key => $row) {
    //                         if ($key != 1) {
    //                             $data = [
    //                                 'uuid'              => Str::uuid(),
    //                                 'name'              => $row->getCells()[4]->getValue(),
    //                                 'company_id'        => $this->company->firstWhere('code',  $row->getCells()[1]->getValue()) ? $this->company->firstWhere('code',  $row->getCells()[1]->getValue())->id : null,
    //                                 'department_id'     => $this->department->firstWhere('code',  $row->getCells()[5]->getValue()) ? $this->department->firstWhere('code',  $row->getCells()[5]->getValue())->id : null,
    //                                 'date_of_birth'     => date('Y-m-d', strtotime($row->getCells()[12]->getValue())),
    //                                 'identity_card'     => $row->getCells()[13]->getValue(),
    //                                 'gender'            => $row->getCells()[11]->getValue() == 'L' ? 'male' : 'female',
    //                                 // 'religion'          => $row->getCells()[6]->getValue(),
    //                                 'education'         => $row->getCells()[5]->getValue(),
    //                                 'status'            => 1,
    //                                 // 'marital_status'    => $row->getCells()[7]->getValue(),
    //                                 'address'           => $row->getCells()[14]->getValue(),
    //                                 'section'           => $row->getCells()[6]->getValue(),
    //                                 'phone'             => $row->getCells()[15]->getValue(),
    //                                 // 'employee_type'     => $row->getCells()[17]->getValue(),
    //                                 'position_code'     => $row->getCells()[7]->getValue(),
    //                                 // 'schedule_type'     => $row->getCells()[11]->getValue(),
    //                                 // 'status_twiji'      => $row->getCells()[23]->getValue(),
    //                                 'password'          => 'asdqweqwe123',
    //                                 'status_account'    => 1,
    //                             ];

    //                             $dataEmployeeNumber = [
    //                                 'user_id'          => NULL,
    //                                 'employee_number'  => $row->getCells()[3]->getValue() ? $row->getCells()[3]->getValue() : "",
    //                                 'registry_date'    => Carbon::today()->format('Y-m-d'),
    //                                 'status'           => 1,
    //                             ];

    //                             $dataEmployeeServiceYear = [
    //                                 'user_id'           => NULL,
    //                                 'join_date'         => $row->getCells()[28]->getValue() ? date('Y-m-d', strtotime($row->getCells()[28]->getValue())) : null,
    //                                 'leave_date'        =>  $row->getCells()[29]->getValue() ? date('Y-m-d', strtotime($row->getCells()[29]->getValue())) : null,
    //                             ];


    //                             $dataArrayParent[] = $data;
    //                             $dataArrayEmployeeNumber[] = $dataEmployeeNumber;
    //                             $dataArrayEmployeeServiceYear[] = $dataEmployeeServiceYear;

    //                             if (count($dataArrayParent) == $dataChunk) {
    //                                 $this->insertChunk($dataArrayParent, $dataArrayEmployeeNumber, $dataArrayEmployeeServiceYear);
    //                                 $dataArrayParent = [];
    //                                 $dataArrayEmployeeNumber = [];
    //                                 $dataArrayEmployeeServiceYear = [];
    //                             }
    //                         }
    //                     }
    //                 } else if ($i == 9) {
    //                     // SEAS
    //                     foreach ($sheet->getRowIterator() as $key => $row) {
    //                         if ($key != 1) {
    //                             $data = [
    //                                 'uuid'              => Str::uuid(),
    //                                 'name'              => $row->getCells()[4]->getValue(),
    //                                 'company_id'        => $this->company->firstWhere('code',  $row->getCells()[1]->getValue()) ? $this->company->firstWhere('code',  $row->getCells()[1]->getValue())->id : null,
    //                                 'department_id'     => $this->department->firstWhere('code',  $row->getCells()[5]->getValue()) ? $this->department->firstWhere('code',  $row->getCells()[5]->getValue())->id : null,
    //                                 'date_of_birth'     => date('Y-m-d', strtotime($row->getCells()[12]->getValue())),
    //                                 'identity_card'     => $row->getCells()[13]->getValue(),
    //                                 'gender'            => $row->getCells()[11]->getValue() == 'L' ? 'male' : 'female',
    //                                 'religion'          => $row->getCells()[18]->getValue(),
    //                                 'education'         => $row->getCells()[19]->getValue(),
    //                                 'status'            => 1,
    //                                 'marital_status'    => $row->getCells()[21]->getValue(),
    //                                 'address'           => $row->getCells()[14]->getValue(),
    //                                 'section'           => $row->getCells()[6]->getValue(),
    //                                 'phone'             => $row->getCells()[15]->getValue(),
    //                                 // 'employee_type'     => $row->getCells()[17]->getValue(),
    //                                 'position_code'     => $row->getCells()[7]->getValue(),
    //                                 'schedule_type'     => $row->getCells()[8]->getValue(),
    //                                 // 'status_twiji'      => $row->getCells()[23]->getValue(),
    //                                 'password'          => 'asdqweqwe123',
    //                                 'status_account'    => 1,
    //                             ];

    //                             $dataEmployeeNumber = [
    //                                 'user_id'          => NULL,
    //                                 'employee_number'  => $row->getCells()[3]->getValue() ? $row->getCells()[3]->getValue() : "",
    //                                 'registry_date'    => Carbon::today()->format('Y-m-d'),
    //                                 'status'           => 1,
    //                             ];

    //                             $dataEmployeeServiceYear = [
    //                                 'user_id'           => NULL,
    //                                 'join_date'         => $row->getCells()[16]->getValue() ? date('Y-m-d', strtotime($row->getCells()[16]->getValue())) : null,
    //                                 'leave_date'        =>  $row->getCells()[17]->getValue() ? date('Y-m-d', strtotime($row->getCells()[17]->getValue())) : null,
    //                             ];


    //                             $dataArrayParent[] = $data;
    //                             $dataArrayEmployeeNumber[] = $dataEmployeeNumber;
    //                             $dataArrayEmployeeServiceYear[] = $dataEmployeeServiceYear;

    //                             if (count($dataArrayParent) == $dataChunk) {
    //                                 $this->insertChunk($dataArrayParent, $dataArrayEmployeeNumber, $dataArrayEmployeeServiceYear);
    //                                 $dataArrayParent = [];
    //                                 $dataArrayEmployeeNumber = [];
    //                                 $dataArrayEmployeeServiceYear = [];
    //                             }
    //                         }
    //                     }
    //                 }
    //             }

    //             if (count($dataArrayParent) != 0) {
    //                 $this->insertChunk($dataArrayParent, $dataArrayEmployeeNumber,  $dataArrayEmployeeServiceYear);
    //             }
    //         } else {
    //             $identityCard = [];
    //             $newData = [];

    //             foreach ($reader->getSheetIterator() as $sheet) {
    //                 if ($sheet->getName() == "Data Karyawan") {
    //                     foreach ($sheet->getRowIterator() as $key => $row) {
    //                         if ($key != 1) {
    //                             $identityCard[] =  $row->getCells()[6]->getValue();
    //                             $newData[] = [
    //                                 'employee_number'   => $row->getCells()[1]->getValue() ? $row->getCells()[1]->getValue() : "",
    //                                 'name'              => $row->getCells()[2]->getValue(),
    //                                 // 'pt'                =>  $row->getCells()[3]->getValue(),
    //                                 // 'dept'              =>  $row->getCells()[4]->getValue(),
    //                                 'company_id'        => $row->getCells()[3]->getValue(),
    //                                 'department_id'     => $row->getCells()[4]->getValue(),
    //                                 'date_of_birth'     => CarbonImmutable::instance($row->getCells()[5]->getValue())->format('Y-m-d'),
    //                                 'identity_card'     => $row->getCells()[6]->getValue(),
    //                                 'gender'            => $row->getCells()[11]->getValue(),
    //                                 'religion'          => $row->getCells()[12]->getValue(),
    //                                 'education'         => $row->getCells()[13]->getValue(),
    //                                 'status'            => $row->getCells()[8]->getValue(),
    //                                 'marital_status'    => $row->getCells()[14]->getValue(),
    //                                 'address'           => $row->getCells()[15]->getValue(),
    //                                 'phone'             => $row->getCells()[16]->getValue(),
    //                                 'employee_type'     => $row->getCells()[17]->getValue(),
    //                                 'section'           => $row->getCells()[18]->getValue(),
    //                                 'position_code'     => $row->getCells()[19]->getValue(),
    //                                 'schedule_type'     => $row->getCells()[22]->getValue(),
    //                                 'status_twiji'      => $row->getCells()[23]->getValue(),
    //                                 'join_date'         => $row->getCells()[28]->getValue() ? CarbonImmutable::instance($row->getCells()[28]->getValue())->format('Y-m-d') : null,
    //                                 'leave_date'        => $row->getCells()[29]->getValue() ? CarbonImmutable::instance($row->getCells()[29]->getValue())->format('Y-m-d') : null,

    //                             ];
    //                         }
    //                     }
    //                 }
    //             }

    //             $missingData = $this->user->whereNotIn('identity_card', $identityCard)->map(function ($data) {
    //                 return [
    //                     'employee_number'    => $data->userEmployeeNumber()->where('status', 1)->first()->employee_number ?? "",
    //                     'name'               => $data->name,
    //                     'company_name'       => $data->company ? $data->company->code : '',
    //                     'department_name'    => $data->department ? $data->department->code : '',
    //                     'date_of_birth'      => date('d-M-y', strtotime($data->date_of_birth)),
    //                     'identity_card'      => $data->identity_card,
    //                     'unicode'            => $data->name . " - " . $data->identity_card,
    //                     'status'             => $data->status == 1 ? "AKTIF" : "OUT",
    //                     'gender'             => $data->gender == "female" ? "P" : "L",
    //                     'religion'           => $data->religion,
    //                     'education'          => $data->education,
    //                     'marital_status'     => strtoupper($data->marital_status),
    //                     'address'            => $data->address,
    //                     'phone'              => $data->phone,
    //                     'employee_type'      => $data->employee_type,
    //                     'section'            => $data->section,
    //                     'position_code'      => $data->position_code,
    //                     'roleCode'           => $data->userJobCode()->where('status', 1)->first()->jobCode->full_code ?? "",
    //                     'group'              => $data->userJobCode()->where('status', 1)->first()->jobCode->group ?? "",
    //                     'schedule_type'      => $data->schedule_type,
    //                     'status_twiji'       => $data->status_twiji,
    //                     'join_date'          => $data->userServiceYear ? ($data->userServiceYear->join_date ? date('d-M-y', strtotime($data->userServiceYear->join_date)) : null) : null,
    //                     'leave_date'         => $data->userServiceYear ? ($data->userServiceYear->leave_date ? date('d-M-y', strtotime($data->userServiceYear->leave_date)) : null) : null,
    //                 ];
    //             })->toArray();

    //             $oldData = $this->user
    //                 ->whereIn('identity_card', $identityCard)
    //                 ->mapWithKeys(function ($user) {
    //                     $data = $user->toArray();

    //                     $data['company_id'] = $user->company ?  $user->company->code : null;
    //                     $data['department_id'] = $user->department ?  $user->department->code : null;
    //                     $data['status'] = $user->status == '1' ? "AKTIF" : "OUT";
    //                     $data['gender'] = $user->gender == "female" ? 'P' : 'L';


    //                     $data['employee_number'] = $user->userEmployeeNumber()
    //                         ->latest()
    //                         ->first(['employee_number'])
    //                         ->employee_number ?? "";

    //                     $data['join_date']  = $user->userServiceYear->join_date ??  null;
    //                     $data['leave_date']  = $user->userServiceYear->leave_date ??  null;


    //                     return [$user->identity_card => $data];
    //                 })
    //                 ->toArray();
    //             $dataDiff = $this->getDifferenceData($oldData, $newData);
    //             $filePathExportData =  $this->exportData($missingData, $dataDiff);
    //             Cache::put($this->cacheKey, $filePathExportData, now()->addMinutes(10));
    //         }

    //         $reader->close();

    //         Storage::delete($this->filepath);

    //         DB::commit();

    //         return true;
    //     } catch (\Exception $exception) {
    //         DB::rollBack();
    //         return false;
    //     }
    // }

    public function insertChunk($dataArrayParent, $dataArrayEmployeeNumber, $dataArrayUserService)
    {
        User::insert($dataArrayParent);

        $lastInsertId = DB::getPdo()->lastInsertId();

        $insertedParentIds = User::where('id', '>=', $lastInsertId)
            ->orderBy('id', 'ASC')
            ->take(count($dataArrayParent))
            ->pluck('id')
            ->toArray();

        foreach ($dataArrayParent as $key => &$parent) {
            if (isset($insertedParentIds[$key])) {
                $parentId = $insertedParentIds[$key];
                $dataArrayEmployeeNumber[$key]['user_id'] = $parentId;
                $dataArrayUserService[$key]['user_id'] = $parentId;
            }
        }

        UserEmployeeNumber::insert($dataArrayEmployeeNumber);
        UserServiceYear::insert($dataArrayUserService);
    }

    public function getDifferenceData($oldData, $newData)
    {
        $differences = [];
        $dataArrayParent = [];
        $dataArrayEmployeeNumber = [];
        $dataArrayUserService = [];
        $dataChunk = 200;
        $mappingColumns = [
            'name'              =>  'Nama',
            'company_id'        =>  'PT',
            'department_id'     =>  'DEPT',
            'date_of_birth'     =>  'TANGGAL LAHIR',
            'identity_card'     =>  'No KTP',
            'status'            =>  'Status',
            'gender'            =>  'Gender',
            'religion'          =>  'Agama',
            'education'         =>  'Pendidikan',
            'marital_status'    =>  'Status Pernikahan',
            'address'           =>  'Alamat',
            'phone'             =>  'No HP',
            'employee_type'     =>  'Klasifikasi',
            'section'           =>  'Bagian',
            'position_code'     =>  'Kode Posisi',
            'schedule_type'     =>  'Status Shift',
            'status_twiji'      =>  'Status TWIJI',
            'employee_number'   =>  'NOPEG',
            'join_date'         =>  'Tanggal Masuk',
            'leave_date'        =>  'Tanggal Out',
        ];


        foreach ($newData as $new) {
            $identityCard = $new['identity_card'];
            if (!isset($oldData[$identityCard])) {
                $newArray = array_diff_key($new, array_flip(['employee_number', 'join_date', 'leave_date']));

                $newArray['uuid'] = Str::uuid();
                $newArray['company_id'] = $this->company->firstWhere('code', $newArray['company_id'])?->id;
                $newArray['department_id'] = $this->department->firstWhere('code', $newArray['department_id'])?->id;
                $newArray['gender'] = $newArray['gender']  == 'L' ? 'male' : 'female';
                $newArray['status'] = $newArray['status'] == 'AKTIF' ? 1 : 0;
                $newArray['password'] = 'asd1234';
                $newArray['status_account'] = 1;

                $dataArrayParent[] = $newArray;

                $dataArrayEmployeeNumber[] = [
                    'user_id'          => null,
                    'employee_number'  => $new['employee_number'],
                    'registry_date'    => Carbon::today()->format('Y-m-d'),
                    'status'           => 1
                ];

                $dataArrayUserService[] = [
                    'user_id'          => null,
                    'join_date'        => $new['join_date'],
                    'leave_date'       => $new['leave_date'],
                ];


                if (count($dataArrayParent) == $dataChunk) {
                    $this->insertChunk($dataArrayParent, $dataArrayEmployeeNumber, $dataArrayUserService);
                    $dataArrayParent = [];
                    $dataArrayEmployeeNumber = [];
                    $dataArrayUserService = [];
                }
            } else {
                $old = $oldData[$identityCard];
                foreach ($new as $key => $value) {
                    if (strtolower($value ?? '') != strtolower($old[$key] ?? '')) {
                        $differences[] = [
                            'identity_card' => $identityCard,
                            'name'          => $old['name'],
                            'column'        => $mappingColumns[$key],
                            'old_data'      => $old[$key],
                            'new_data'      => $value,
                        ];
                    }
                }
            }
        }

        if (count($dataArrayParent) != 0) {
            $this->insertChunk($dataArrayParent, $dataArrayEmployeeNumber, $dataArrayUserService);
            $dataArrayParent = [];
            $dataArrayEmployeeNumber = [];
            $dataArrayUserService = [];
        }


        return $differences;
    }

    public function exportData($missingData, $dataDiff)
    {
        $filepath = storage_path('app/public/temp/msd-data-karyawan.xlsx');
        $writer  = new Writer();
        $writer->setCreator("MSD TEAM");
        $writer->openToFile($filepath);

        // 1st SHEET
        $style = new Style();
        $style->setBackgroundColor(Color::RED);
        $styleHeader = new Style();
        $styleHeader->setBackgroundColor(Color::RED);
        $styleHeader->setFontBold();

        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Missing Data Karyawan');
        $sheet->setColumnWidthForRange(23, 1, 25);
        $sheet->setColumnWidth(60, 13);

        $row = Row::fromValues([
            'No Pegawai',
            'Nama',
            'PT',
            'DEPT',
            'TANGGAL LAHIR',
            'No KTP',
            'Unicode Data Diri',
            'Status (Aktif/ Non Aktif)',
            'Jenis Kelamin',
            'Agama',
            'Pendidikan',
            'Status Pernikahan',
            'Alamat',
            'No HP',
            'Klasifikasi',
            'Bagian',
            'Kode Posisi',
            'Kode Jabatan',
            'Grup',
            'Status Shift',
            'Status TWIJI',
            'Tanggal Masuk',
            'Tanggal Out',
        ], $styleHeader);

        $writer->addRow($row);

        foreach ($missingData as $data) {
            $row = Row::fromValues($data, $style);
            $writer->addRow($row);
        }

        // 2nd SHEET
        $styleHeaderNewSheet = new Style();
        $styleHeaderNewSheet->setFontBold();

        $newSheet = $writer->addNewSheetAndMakeItCurrent();
        $newSheet->setName('Compare Data Karyawan');
        $newSheet->setColumnWidthForRange(23, 1, 6);

        $row = Row::fromValues(['NIK', 'NAMA', 'KLASIFIKASI KOLOM', 'DATA LAMA', 'DATA BARU', 'DATA FINAL'], $styleHeaderNewSheet);
        $writer->addRow($row);

        foreach ($dataDiff as $diff) {
            $row = Row::fromValues($diff);
            $writer->addRow($row);
        }


        $writer->close();

        return $filepath;
    }

    public function checkNIP($nip)
    {
        if (preg_match('/^000000/', $nip)) {
            return "Staff";
        }

        if (preg_match('/^[a-zA-Z]/', $nip)) {
            return "OS";
        }

        return "Karyawan";
    }

    public function processRow($row, $config)
    {
        $data = [];
        foreach ($config as $key => $cellIndex) {
            if (is_array($cellIndex)) {
                $data[$key] = $cellIndex['code'];
            } elseif (in_array($key, ['date_of_birth', 'join_date', 'leave_date'])) {
                $value = $row->getCells()[$cellIndex]->getValue();
                $data[$key] = $value ? date('Y-m-d', strtotime($value)) : null;
            } else {
                $data[$key] = $row->getCells()[$cellIndex]->getValue();
            }
        }

        return $data;
    }

    // public function processRow($row, $config){
    //       // foreach ($config as $key => $cellIndex) {
    //     //     if (is_array($cellIndex)) {
    //     // $data[$key] = $this->company->firstWhere('code', $cellIndex['code']) ?
    //     //     $this->company->firstWhere('code', $cellIndex['code'])->id : null;
    //     //     } elseif ($key == 'gender') {
    //     //         $data[$key] = $row->getCells()[$cellIndex]->getValue() == 'L' ? 'male' : 'female';
    //     //     } elseif (in_array($key, ['date_of_birth', 'join_date', 'leave_date'])) {
    //     //         $value = $row->getCells()[$cellIndex]->getValue();
    //     //         $data[$key] = $value ? date('Y-m-d', strtotime($value)) : null;
    //     //     } else {
    //     //         $data[$key] = $row->getCells()[$cellIndex]->getValue();
    //     //     }
    //     // }

    //     // $data['uuid'] = Str::uuid();
    //     // $data['status'] = 1; // Default status
    //     // $data['password'] = 'asdqweqwe123'; // Default password
    //     // $data['status_account'] = 1; // Default account status

    //     // return [
    //     //     'parent' => $data,
    //     //     'employee_number' => [
    //     //         'user_id' => NULL,
    //     //         'employee_number' => $row->getCells()[$config['employee_number'] ?? 0]->getValue() ?: "",
    //     //         'registry_date' => Carbon::today()->format('Y-m-d'),
    //     //         'status' => 1,
    //     //     ],
    //     //     'service_year' => [
    //     //         'user_id' => NULL,
    //     //         'join_date' => $data['join_date'] ?? null,
    //     //         'leave_date' => $data['leave_date'] ?? null,
    //     //     ],
    //     // ];
    // }

}
