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
                $currentChunk = [];
                $processedUsers = [];
                $dataChunk = 200;
                foreach ($reader->getSheetIterator() as $i =>  $sheet) {
                    if ($i == 1) {
                        foreach ($sheet->getRowIterator() as $key => $row) {
                            if ($key != 1) {
                                $identityCard = $row->getCells()[6]->getValue();

                                $currentChunk[] = [
                                    'identity' => $identityCard,
                                    'user'     => $this->prepareUserData($row),
                                    'numbers'  => $this->prepareEmployeeNumberData($row),
                                    'service'  => $this->prepareServiceYearData($row)
                                ];

                                if (count($currentChunk) == $dataChunk) {
                                    $this->insertChunk($currentChunk, $processedUsers);
                                    $currentChunk = [];
                                }
                            }
                        }
                    }
                }
                if (count($currentChunk) != 0) {
                    $this->insertChunk($currentChunk, $processedUsers);
                    $currentChunk = [];
                }
            } else {
                $identityCard = [];
                $newData = [];
                $processedUsers = [];
                foreach ($reader->getSheetIterator() as $i => $sheet) {
                    if ($i == 1) {
                        foreach ($sheet->getRowIterator() as $key => $row) {
                            if ($key != 1) {
                                $identityCard[] =  $row->getCells()[6]->getValue();
                                $newData[] = [
                                    'employee_number'   => $row->getCells()[1]->getValue() ? $row->getCells()[1]->getValue() : "",
                                    'name'              => $row->getCells()[2]->getValue(),
                                    'company_id'        => $row->getCells()[3]->getValue(),
                                    'department_id'     => $row->getCells()[4]->getValue(),
                                    'date_of_birth'     => CarbonImmutable::instance($row->getCells()[5]->getValue())->format('Y-m-d'),
                                    'identity_card'     => $row->getCells()[6]->getValue(),
                                    'gender'            => $row->getCells()[11]->getValue(),
                                    'religion'          => $row->getCells()[12]->getValue(),
                                    'education'         => $row->getCells()[13]->getValue(),
                                    'status'            => $row->getCells()[8]->getValue(),
                                    'marital_status'    => $row->getCells()[14]->getValue(),
                                    'address'           => $row->getCells()[15]->getValue(),
                                    'phone'             => $row->getCells()[16]->getValue(),
                                    'employee_type'     => $row->getCells()[17]->getValue(),
                                    'section'           => $row->getCells()[18]->getValue(),
                                    'position_code'     => $row->getCells()[19]->getValue(),
                                    'schedule_type'     => $row->getCells()[22]->getValue(),
                                    'status_twiji'      => $row->getCells()[23]->getValue(),
                                    'join_date'         => $row->getCells()[28]->getValue() ? CarbonImmutable::instance($row->getCells()[28]->getValue())->format('Y-m-d') : null,
                                    'leave_date'        => $row->getCells()[29]->getValue() ? CarbonImmutable::instance($row->getCells()[29]->getValue())->format('Y-m-d') : null,

                                ];
                            }
                        }
                    }
                }

                $employeeNumber = Arr::pluck($newData, 'employee_number');
                $missingData = collect();
                $oldData = collect();

                if (!empty($identityCard)) {
                    $missingData =  $this->user->whereNotIn('identity_card', $identityCard)->values();
                }

                // if fallback check by employee_number
                if ($missingData) {
                    // get missing data inside db to compare old data vs new
                    $missingIdentityCards = $missingData->pluck('identity_card')->toArray();
                    $identityCard = array_merge($identityCard, $missingIdentityCards);
                    $identityCard = array_unique($identityCard);

                    $missingData = User::query()
                        ->whereHas('userEmployeeNumber', fn($q) => $q->where('status', 1))
                        ->whereDoesntHave('userEmployeeNumber', function ($q) use ($employeeNumber) {
                            $q->where('status', 1)
                                ->whereIn('employee_number', $employeeNumber);
                        })
                        ->get();
                }


                $missingData = $missingData->map(function ($data, $i) {
                    return [
                        'no'                 => $i + 1,
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

                $oldData =  $this->user->whereIn('identity_card', $identityCard);

                $oldData = $oldData->mapWithKeys(function ($user) {
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

                // dd($missingData, $oldData);
                $dataDiff = $this->getDifferenceData($oldData, $newData, $processedUsers);
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


    protected function insertChunk(&$chunkData, &$processedUsers)
    {
        // Group entries by identity card
        $groupedData = [];
        $employeeNumbers = [];
        $serviceYears = [];
        foreach ($chunkData as $entry) {
            $identity = $entry['identity'];
            $groupedData[$identity] ??= [
                'latest_user' => null,
                'numbers'     => [],
                'services'    => []
            ];

            // Always keep the latest user data
            $groupedData[$identity]['latest_user'] = $entry['user'];
            // Collect all number and service entries
            $groupedData[$identity]['numbers'][] = $entry['numbers'];
            $groupedData[$identity]['services'][] = $entry['service'];
        }


        // Process each identity group
        foreach ($groupedData as $identity => $group) {
            // Find or create user
            $user = User::where('identity_card', $identity)->first();

            if (!$user) {
                // Create new user with UUID
                $userData = $group['latest_user'];
                $userData['uuid'] = Str::uuid();
                $user = User::create($userData);
            } else {
                // Update existing user with latest data
                $user->update($group['latest_user']);
            }

            // Deactivate all existing numbers and services
            if (UserEmployeeNumber::firstWhere('user_id', $user->id)) {
                UserEmployeeNumber::where('user_id', $user->id)->update(['status' => 0]);
            }
            if (UserServiceYear::firstWhere('user_id', $user->id)) {
                UserServiceYear::where('user_id', $user->id)->update(['status' => 0]);
            }


            // Prepare relationship data
            $numbersCount = count($group['numbers']);
            foreach ($group['numbers'] as $index => $numberData) {
                $employeeNumbers[] = array_merge($numberData, [
                    'user_id'       => $user->id,
                    'registry_date' => Carbon::today()->format('Y-m-d'),
                    'status'        => ($index === $numbersCount - 1) ? 1 : 0
                ]);
            }

            foreach ($group['services'] as $index => $serviceData) {
                $serviceYears[] = array_merge($serviceData, [
                    'user_id' => $user->id,
                ]);
            }

            // Bulk insert relationships
            UserEmployeeNumber::upsert($employeeNumbers, ['user_id', 'employee_number']);
            UserServiceYear::upsert($serviceYears, ['user_id', 'join_date']);
        }
        // Keep track of processed users 
        $processedUsers = array_merge(
            $processedUsers,
            User::whereIn('identity_card', array_keys($groupedData))->get()->all()
        );

        // Clear chunk data
        $chunkData = [];
    }

    public function getDifferenceData($oldData, $newData, &$processedUsers)
    {
        $differences = [];
        $currentChunk = [];
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
            $employee_number = $new['employee_number'];
            $oldDataCheck = $oldData[$identityCard] ?? null;

            // check if fallback by employee_number
            if (!isset($oldDataCheck)) {
                $oldDataCheck = collect($oldData)->firstWhere('employee_number', $employee_number);
            }

            if (!$oldDataCheck) {
                $newArray = array_diff_key($new, array_flip(['employee_number', 'join_date', 'leave_date']));

                $newArray['uuid'] = Str::uuid();
                $newArray['company_id'] = $this->company->firstWhere('code', $newArray['company_id'])?->id;
                $newArray['department_id'] = $this->department->firstWhere('code', $newArray['department_id'])?->id;
                $newArray['gender'] = $newArray['gender']  == 'L' ? 'male' : 'female';
                $newArray['status'] = $newArray['status'] == 'AKTIF' ? 1 : 0;
                $newArray['password'] = 'asd1234';
                $newArray['status_account'] = 1;


                $dataArrayEmployeeNumber = [
                    'employee_number'  => $new['employee_number'],
                ];

                $dataArrayUserService = [
                    'join_date'        => $new['join_date'],
                    'leave_date'       => $new['leave_date'],
                ];

                $currentChunk[] = [
                    'identity' => $identityCard,
                    'user'     => $newArray,
                    'numbers'  => $dataArrayEmployeeNumber,
                    'service'  => $dataArrayUserService
                ];

                // if (count($currentChunk) == $dataChunk) {
                //     $this->insertChunk($currentChunk, $processedUsers);
                // }
            } else {
                $old = $oldDataCheck;
                foreach ($new as $key => $value) {
                    if (strtolower($value ?? '') != strtolower($old[$key] ?? '')) {
                        $differences[] = [
                            'identity_card' => $old['identity_card'],
                            'name'          => $old['name'],
                            'column'        => $mappingColumns[$key],
                            'old_data'      => $old[$key],
                            'new_data'      => $value,
                        ];
                    }
                }
            }
        }

        // if (count($currentChunk) != 0) {
        //     $this->insertChunk($currentChunk, $processedUsers);
        // }

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
            'No',
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

    private function prepareUserData($row)
    {
        return [
            'name'            => $row->getCells()[2]->getValue(),
            'company_id'      => $this->company->firstWhere('code', $row->getCells()[3]->getValue())?->id,
            'department_id'   => $this->department->firstWhere('code', $row->getCells()[4]->getValue())?->id,
            'date_of_birth'   => CarbonImmutable::instance($row->getCells()[5]->getValue())->format('Y-m-d'),
            'identity_card'   => $row->getCells()[6]->getValue(),
            'gender'          => $row->getCells()[11]->getValue() == 'L' ? 'male' : 'female',
            'religion'        => $row->getCells()[12]->getValue(),
            'education'       => $row->getCells()[13]->getValue(),
            'status'          => $row->getCells()[8]->getValue() == "AKTIF" ? 1 : 0,
            'marital_status'  => $row->getCells()[14]->getValue(),
            'address'         => $row->getCells()[15]->getValue(),
            'phone'           => $row->getCells()[16]->getValue(),
            'employee_type'   => $row->getCells()[17]->getValue(),
            'section'         => $row->getCells()[18]->getValue(),
            'position_code'   => $row->getCells()[19]->getValue(),
            'schedule_type'   => $row->getCells()[22]->getValue(),
            'status_twiji'    => $row->getCells()[23]->getValue(),
            'password'        => 'asdqweqwe123',
            'status_account'  => 1,
        ];
    }

    private function prepareEmployeeNumberData($row)
    {
        return [
            'employee_number' => $row->getCells()[1]->getValue() ?: "",
        ];
    }

    private function prepareServiceYearData($row)
    {
        return [
            'join_date'  => $row->getCells()[28]->getValue() ? CarbonImmutable::instance($row->getCells()[28]->getValue())->format('Y-m-d') : null,
            'leave_date' => $row->getCells()[29]->getValue() ? CarbonImmutable::instance($row->getCells()[29]->getValue())->format('Y-m-d') : null,
        ];
    }
}
