<?php

namespace App\Services;

use App\Jobs\ExportUserJob;
use App\Jobs\ImportUpdatedUserJob;
use App\Jobs\ImportUserJob;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use OpenSpout\Reader\XLSX\Reader;

class UserServices extends BaseServices
{
    protected $user;

    public function __construct()
    {
        $this->user = User::with('company', 'department', 'userEmployeeNumber', 'certificates', 'userServiceYear', 'training');
    }

    public function importUserExcel(Request $request, $cachekey)
    {
        $file = $request->file;
        $filepath = $file->storeAs('temp', $request->file->getClientOriginalName(), 'public');

        $reader = new Reader();
        $reader->open(storage_path('app/public/' . $filepath));

        $sheetIterator = $reader->getSheetIterator();
        $firstSheet = $sheetIterator->current(); // Get the first sheet

        if ($firstSheet && $firstSheet->getName() === "Missing Data Karyawan") {
            $query = ImportUpdatedUserJob::dispatch($filepath);
        } else {
            $query = ImportUserJob::dispatch($filepath, $cachekey);
        }

        if ($query) {
            return true;
        }

        return false;
    }

    public function importUpdatedUserExcel(Request $request)
    {
        $file = $request->file;
        $filepath = $file->storeAs('temp', $request->file->getClientOriginalName(), 'public');
        $query =  ImportUpdatedUserJob::dispatch($filepath);

        if ($query) {
            return true;
        }

        return false;
    }

    public function exportDataUserExcel(Request $request, $cachekey)
    {
        $uuid = json_decode($request->uuid, true) ?? [];
        $user =  $this->user;

        if ($uuid) {
            $user->whereIn('uuid', array_keys($uuid));
        }

        $user = $user
            ->orderBy('id', 'ASC')
            ->get()
            ->map(function ($user, $key) {
                return [
                    'no'                               => $key + 1,
                    'employee_number'                  => $user->userEmployeeNumber()->where('status', 1)->first()->employee_number ?? "",
                    'name'                             => strtoupper($user->name),
                    'company_name'                     => $user->company ? $user->company->code : '',
                    'department_name'                  => $user->department->code ?? '',
                    'date_of_birth'                    => date('d-M-y', strtotime($user->date_of_birth)),
                    'identity_card'                    => $user->identity_card,
                    'unicode'                          => strtoupper($user->name) . " - " . $user->identity_card,
                    'status'                           => $user->status == 1 ? "AKTIF" : "OUT",
                    'gender'                           => $user->gender == "female" ? 'P' : 'L',
                    'religion'                         => strtoupper($user->religion),
                    'education'                        => strtoupper($user->education),
                    'marital_status'                   => strtoupper($user->marital_status),
                    'address'                          => strtoupper($user->address),
                    'phone'                            => $user->phone,
                    'employee_type'                    => $user->employee_type,
                    'section'                          => $user->section,
                    'position_code'                    => $user->position_code,
                    'roleCode'                         => $user->userJobCode()->where('status', 1)->first()->jobCode->full_code ?? "",
                    'group'                            => $user->userJobCode()->where('status', 1)->first()->group ?? "",
                    'group'                            => $user->userJobCode()->where('status', 1)->first()->group ?? "",
                    'schedule_type'                    => $user->schedule_type,
                    'status_twiji'                     => $user->status_twiji,
                    'join_date'                        => $user->userServiceYear->join_date ? date('d-M-y', strtotime($user->userServiceYear->join_date)) : '',
                    'leave_date'                       => $user->userServiceYear->leave_date ?  date('d-M-y', strtotime($user->userServiceYear->leave_date)) : '',
                    'service_year_full'                => $this->getServiceYearFull($user->userServiceYear->join_date),
                    'service_year'                     => $this->getServiceYear($user->userServiceYear->join_date),
                    'working_duration_classification'  => $this->workingDurationClassification($user->userServiceYear->join_date),
                    'real_age_in_month'                => $this->getRealAgeInMonth($user->date_of_birth),
                    'age'                              => Carbon::parse($user->date_of_birth)->age,
                    'year'                             => Carbon::parse($user->date_of_birth)->year,
                    'general_classification'           => $this->generalClassification($user->date_of_birth),
                ];
            });

        $query =  ExportUserJob::dispatch($cachekey, $user);

        if ($query) {
            return true;
        }

        return false;
    }

    public function storeUser(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data User ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            $user = User::create([
                'uuid'           => Str::uuid(),
                'name'           => $request->name,
                'company_id'     => $request->company_id,
                'department_id'  => $request->department_id,
                'date_of_birth'  => date('Y-m-d', strtotime($request->date_of_birth)),
                'identity_card'  => str_replace("-", "",  $request->identity_card),
                'gender'         => $request->gender,
                'religion'       => $request->religion,
                'email'          => $request->email,
                'photo'          => $request->photo ? $request->photo : '',
                'education'      => $request->education,
                'status'         => $request->status,
                'marital_status' => $request->marital_status,
                'address'        => $request->address,
                'phone'          => $request->phone,
                'employee_type'  => $request->employee_type,
                'section'        => $request->section,
                'position_code'  => $request->position_code,
                'status_twiji'   => $request->status_twiji,
                'schedule_type'  => $request->schedule_type,
                'password'       => Hash::make('abcd1234567'),
                'status_account' => 1,
            ]);

            $this->setLog('info', 'New data User' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');
            return $user;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data User = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data User = ' . $exception->getLine());
            $this->setLog('error', 'Error store data User = ' . $exception->getFile());
            $this->setLog('error', 'Error store data User = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateUser(Request $request, $uuid)
    {
        try {
            $this->setLog('info', 'Request update data User ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $user = User::firstWhere('uuid', $uuid);

            if (!$user) {
                DB::rollBack();
                return false;
            }

            // Define the list of fields that can be updated
            $updatableFields = [
                'name',
                'company_id',
                'department_id',
                'date_of_birth',
                'identity_card',
                'gender',
                'religion',
                'email',
                'photo',
                'education',
                'status',
                'marital_status',
                'address',
                'phone',
                'employee_type',
                'section',
                'position_code',
                'status_twiji',
                'schedule_type',
            ];

            // Filter the request data to include only the fields present in the request
            $data = array_filter(
                $request->only($updatableFields),
                function ($value) {
                    return !is_null($value);
                }
            );

            // Apply necessary transformations
            if (isset($data['date_of_birth'])) {
                $data['date_of_birth'] = date('Y-m-d', strtotime($data['date_of_birth']));
            }

            if (isset($data['identity_card'])) {
                $data['identity_card'] = str_replace("-", "", $data['identity_card']);
            }

            if (isset($data['photo'])) {
                $data['photo'] = $data['photo'] ?: '';
            }

            $user->update($data);

            $this->setLog('info', 'Updated data User ' . json_encode($data));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error update data User = ' . $exception->getMessage());
            $this->setLog('error', 'Error update data User = ' . $exception->getLine());
            $this->setLog('error', 'Error update data User = ' . $exception->getFile());
            $this->setLog('error', 'Error update data User = ' . $exception->getTraceAsString());
            return null;
        }
    }


    public function getDataUser($uuid = NULL, $request)
    {
        if (!empty($uuid)) {
            $user = $this->user->firstWhere('uuid', $uuid);
            $user = [
                'uuid'                             => $user->uuid,
                'id'                               => $user->id,
                'name'                             => $user->name,
                'company_id'                       => $user->company->id ?? null,
                'company_name'                     => $user->company ? $user->company->name . " (" . $user->company->code . ")" : '',
                'companies'                        => $user->company,
                'department_id'                    => $user->department->id ??  null,
                'department_name'                  => $user->department->name ?? '',
                'department_code'                  => $user->department->code ?? '',
                'employee_number'                  => $user->userEmployeeNumber()->where('status', 1)->first()->employee_number ?? "",
                'employee_numbers'                 => $user->userEmployeeNumber ?? null,
                'date_of_birth'                    => Carbon::parse($user->date_of_birth)->format("d M Y"),
                'identity_card'                    => $user->identity_card,
                'unicode'                          => $user->name . " - " . $user->identity_card,
                'gender'                           => strtoupper($user->gender),
                'religion'                         => strtoupper($user->religion),
                'email'                            => $user->email,
                'photo'                            => $user->photo,
                'education'                        => $user->education,
                'status'                           => $user->status == 1 ? "Aktif" : "Non Aktif",
                'marital_status'                   => $user->marital_status,
                'address'                          => $user->address,
                'phone'                            => $user->phone,
                'employee_type'                    => $user->employee_type,
                'section'                          => $user->section,
                'position_code'                    => $user->position_code,
                'id_staff'                         => $user->userJobCode()->where('status', 1)->first()->id_staff ?? "",
                'id_structure'                     => $user->userJobCode()->where('status', 1)->first()->id_structure ?? "",
                'group'                            => $user->userJobCode()->where('status', 1)->first()->group ?? "",
                'roleCode'                         => $user->userJobCode()->where('status', 1)->first()->jobCode->full_code ?? "",
                'positionCode'                     => $user->userJobCode()->where('status', 1)->first()->position_code_structure ?? "",
                'employee_superior'                => $user->getSuperiorName(),
                'employeeStructure'                => $user->userJobCode()->where('status', 1)->first() ? $user->userJobCode()->where('status', 1)->first()->userStructureMapping()->first() : null,
                'roleCodes'                        => $user->userJobCode()->get(),
                'status_twiji'                     => $user->status_twiji,
                'schedule_type'                    => $user->schedule_type,
                'user_certificates'                => $user->certificates,
                'join_date'                        => $user->userServiceYear->join_date,
                'employeeStructures'               => $user->userJobCode()->with('userStructureMapping')->get() ?? null,
                'totalMemberStructure'             => $user->getTotalMemberStructure(),
                'totalSubordinates'                => $user->getTotalSubordinate(),
                'employeeIKWSTrained'              => $user->getDetailIKWTrained(),
                'getDetailRKI'                     => $user->getDetailRKI()['data'] ?? null,
                'getTotalIKWTrained'               => $user->getDetailRKI()['totalIKW'] ?? null,
                'getTotalIKWCompetent'             => $user->getDetailRKI()['totalIKWCompetent'] ?? null,
                'age'                              => Carbon::parse($user->date_of_birth)->age,
                'year'                             => Carbon::parse($user->date_of_birth)->year,
                'service_year'                     => $this->getServiceYearFull($user->userServiceYear->join_date),
                'age_classification'               => $this->ageClassification($user->date_of_birth),
                'general_classification'           => $this->generalClassification($user->date_of_birth),
                'working_duration_classification'  => $this->workingDurationClassification($user->userServiceYear->join_date),
            ];
        } else {
            $user = $this->user;
            if ($request->id_department) {
                $user->where('department_id', $request->id_department);
            }

            if ($request->id_company) {
                $user->where('company_id', $request->id_company);
            }

            $user = $user->get()->map(function ($data) {
                return [
                    'uuid'                             => $data->uuid,
                    'id'                               => $data->id,
                    'name'                             => $data->name,
                    'company_id'                       => $data->company->id ?? null,
                    'company_name'                     => $data->company ? $data->company->name . " (" . $data->company->code . ")" : '',
                    'companies'                        => $data->company,
                    'department_id'                    => $data->department->id ??  null,
                    'department_name'                  => $data->department->name ?? '',
                    'department_code'                  => $data->department->code ?? '',
                    'employee_number'                  => $data->userEmployeeNumber()->where('status', 1)->first()->employee_number ?? "",
                    'employee_numbers'                 => $data->userEmployeeNumber ?? null,
                    'date_of_birth'                    => $data->date_of_birth,
                    'identity_card'                    => $data->identity_card,
                    'unicode'                          => $data->name . " - " . $data->identity_card,
                    'gender'                           => strtoupper($data->gender),
                    'religion'                         => strtoupper($data->religion),
                    'email'                            => $data->email,
                    'photo'                            => $data->photo,
                    'education'                        => $data->education,
                    'status'                           => $data->status == 1 ? "Aktif" : "Non Aktif",
                    'marital_status'                   => $data->marital_status,
                    'address'                          => $data->address,
                    'phone'                            => $data->phone,
                    'employee_type'                    => $data->employee_type,
                    'section'                          => $data->section,
                    'position_code'                    => $data->position_code,
                    'id_staff'                         => $data->userJobCode()->where('status', 1)->first()->id_staff ?? "",
                    'id_structure'                     => $data->userJobCode()->where('status', 1)->first()->id_structure ?? "",
                    'group'                            => $data->userJobCode()->where('status', 1)->first()->group ?? "",
                    'roleCode'                         => $data->userJobCode()->where('status', 1)->first()->jobCode->full_code ?? "",
                    'positionCode'                     => $data->userJobCode()->where('status', 1)->first()->position_code_structure ?? "",
                    'employee_superior'                => $data->getSuperiorName(),
                    'employeeStructure'                => $data->userJobCode()->where('status', 1)->first() ? ($data->userJobCode()->where('status', 1)->first()->userStructureMapping() ? $data->userJobCode()->where('status', 1)->first()->userStructureMapping()->first() : "") : "",
                    'roleCodes'                        => $data->userJobCode()->get(),
                    'status_twiji'                     => $data->status_twiji,
                    'schedule_type'                    => $data->schedule_type,
                    'user_certificates'                => $data->certificates,
                    'join_date'                        => $data->userServiceYear->join_date,
                    'employeeStructures'               => $data->userJobCode()->with('userStructureMapping')->get() ?? null,
                    'totalMemberStructure'             => $data->getTotalMemberStructure(),
                    'totalSubordinates'              => $data->getTotalSubordinate(),
                    'employeeIKWSTrained'              => $data->getDetailIKWTrained(),
                    'getDetailRKI'                     => $data->getDetailRKI() ?? null,
                    'age'                              => Carbon::parse($data->date_of_birth)->age,
                    'year'                             => Carbon::parse($data->date_of_birth)->year,
                    'service_year'                     => $this->getServiceYearFull($data->userServiceYear->join_date),
                    'age_classification'               => $this->ageClassification($data->date_of_birth),
                    'general_classification'           => $this->generalClassification($data->date_of_birth),
                    'working_duration_classification'  => $this->workingDurationClassification($data->userServiceYear->join_date),
                ];
            });
        }

        return $user;
    }

    public function getDataUserPagination(Request $request)
    {
        $start = (int) $request->start ? (int)$request->start : 0;
        $size = (int)$request->size ?  (int)$request->size : 5;
        $filters = json_decode($request->filters, true) ?? [];
        $sorting = json_decode($request->sorting, true) ?? [];
        $globalFilter = $request->globalFilter ?? '';

        $user = $this->user->where(function ($query) use ($request, $filters, $globalFilter) {
            if ($request->id_department) {
                $query->where('department_id', $request->id_department);
            }
            if ($request->id_company) {
                $query->where('company_id', $request->id_company);
            }

            if ($globalFilter) {
                $query->where(function ($query) use ($globalFilter) {
                    $query->where('name', 'LIKE',  "%{$globalFilter}%")
                        ->orWhere('date_of_birth', 'LIKE',  "%{$globalFilter}%")
                        ->orWhere('identity_card', 'LIKE',  "%{$globalFilter}%")
                        ->orWhere('gender', 'LIKE',  "%{$globalFilter}%")
                        ->orWhere('religion', 'LIKE',  "%{$globalFilter}%")
                        ->orWhere('email', 'LIKE',  "%{$globalFilter}%")
                        ->orWhere('address', 'LIKE',  "%{$globalFilter}%")
                        ->orWhere('phone', 'LIKE',  "%{$globalFilter}%")
                        ->orWhere('education', 'LIKE',  "%{$globalFilter}%")
                        ->orWhere('position_code', 'LIKE',  "%{$globalFilter}%")
                        ->orWhere('status_twiji', 'LIKE',  "%{$globalFilter}%")
                        ->orWhere('schedule_type', 'LIKE',  "%{$globalFilter}%");
                })->orWhereHas('department', function ($query) use ($globalFilter) {
                    $query->where('name', 'LIKE',  "%{$globalFilter}%");
                })->orWhereHas('company', function ($query) use ($globalFilter) {
                    $query->where('name', 'LIKE',  "%{$globalFilter}%");
                })->orWhereHas('userEmployeeNumber', function ($query) use ($globalFilter) {
                    $query->where('employee_number', 'LIKE',  "%{$globalFilter}%");
                })->orWhereHas('userJobCode', function ($query) use ($globalFilter) {
                    $query->whereHas('jobCode', function ($query) use ($globalFilter) {
                        $query->where('full_code', 'LIKE',  "%{$globalFilter}%");
                    });
                });
            }

            foreach ($filters as $filter) {
                $query->where($filter['id'], $filter['value']);
            }
        });

        foreach ($sorting as $sort) {
            if (isset($sort['id'])) {
                $user->orderBy($sort['id'], $sort['desc'] ? 'DESC' : 'ASC');
            }
        }

        $user = $user
            ->skip($start)
            ->take($size)
            ->get();


        $user = $user->map(function ($data) {
            return [
                'uuid'                             => $data->uuid,
                'id'                               => $data->id,
                'name'                             => $data->name,
                'company_id'                       => $data->company->id ?? null,
                'company_name'                     => $data->company ? $data->company->name . " (" . $data->company->code . ")" : '',
                'companies'                        => $data->company,
                'department_id'                    => $data->department->id ??  null,
                'department_name'                  => $data->department->name ?? '',
                'department_code'                  => $data->department->code ?? '',
                'employee_number'                  => $data->userEmployeeNumber()->where('status', 1)->first()->employee_number ?? "",
                'employee_numbers'                 => $data->userEmployeeNumber ?? null,
                'date_of_birth'                    => $data->date_of_birth,
                'identity_card'                    => $data->identity_card,
                'unicode'                          => $data->name . " - " . $data->identity_card,
                'gender'                           => strtoupper($data->gender),
                'religion'                         => strtoupper($data->religion),
                'email'                            => $data->email,
                'photo'                            => $data->photo,
                'education'                        => $data->education,
                'status'                           => $data->status == 1 ? "Aktif" : "Non Aktif",
                'marital_status'                   => $data->marital_status,
                'address'                          => $data->address,
                'phone'                            => $data->phone,
                'employee_type'                    => $data->employee_type,
                'section'                          => $data->section,
                'position_code'                    => $data->position_code,
                'id_staff'                         => $data->userJobCode()->where('status', 1)->first()->id_staff ?? "",
                'id_structure'                     => $data->userJobCode()->where('status', 1)->first()->id_structure ?? "",
                'group'                            => $data->userJobCode()->where('status', 1)->first()->group ?? "",
                'roleCode'                         => $data->userJobCode()->where('status', 1)->first()->jobCode->full_code ?? "",
                'positionCode'                     => $data->userJobCode()->where('status', 1)->first()->position_code_structure ?? "",
                'employee_superior'                => $data->getSuperiorName(),
                'employeeStructure'                => $data->userJobCode()->where('status', 1)->first() ? ($data->userJobCode()->where('status', 1)->first()->userStructureMapping() ? $data->userJobCode()->where('status', 1)->first()->userStructureMapping()->first() : "") : "",
                'roleCodes'                        => $data->userJobCode()->get(),
                'status_twiji'                     => $data->status_twiji,
                'schedule_type'                    => $data->schedule_type,
                'user_certificates'                => $data->certificates,
                'join_date'                        => $data->userServiceYear->join_date,
                'employeeStructures'               => $data->userJobCode()->with('userStructureMapping')->get() ?? null,
                'totalMemberStructure'             => $data->getTotalMemberStructure() ?? null,
                'totalSubordinates'                => $data->getTotalSubordinate(),
                'employeeIKWSTrained'              => $data->getDetailIKWTrained() ?? null,
                'getDetailRKI'                     => $data->getDetailRKI() ?? null,
                'age'                              => Carbon::parse($data->date_of_birth)->age ?? null,
                'year'                             => Carbon::parse($data->date_of_birth)->year ?? null,
                'service_year'                     => $this->getServiceYearFull($data->userServiceYear->join_date) ?? null,
                'age_classification'               => $this->ageClassification($data->date_of_birth) ?? null,
                'general_classification'           => $this->generalClassification($data->date_of_birth) ?? null,
                'working_duration_classification'  => $this->workingDurationClassification($data->userServiceYear->join_date) ?? null,
            ];
        });

        return $user;
    }

    public function getDataUserByDepartment($id_department)
    {
        $user = $this->user->where('department_id', $id_department)->get();
        $user = $user->map(function ($data) {
            return [
                'uuid'               => $data->uuid,
                'name'               => $data->name,
                'company_name'       => $data->company ? $data->company->name : '',
                'company_id'         => $data->company_id,
                'department_name'    => $data->department ? $data->department->name : '',
                'employee_number'    => $data->userEmployeeNumber()->where('status', 1)->first()->employee_number ?? "",
                'department_id'      => $data->department_id,
                'date_of_birth'      => $data->date_of_birth,
                'identity_card'      => $data->identity_card,
                'unicode'            => $data->name . " - " . $data->identity_card,
                'gender'             => strtoupper($data->gender),
                'religion'           => $data->religion,
                'email'              => $data->email,
                'photo'              => $data->photo,
                'education'          => $data->education,
                'status'             => $data->status == 1 ? "Aktif" : "Non Aktif",
                'marital_status'     => $data->marital_status,
                'address'            => $data->address,
                'phone'              => $data->phone,
                'employee_type'      => $data->employee_type,
                'section'            => $data->section,
                'position_code'      => $data->position_code,
                'roleCode'           => $data->userJobCode()->where('status', 1)->first()->jobCode->full_code ?? "",
                'status_twiji'       => $data->status_twiji,
                'schedule_type'      => $data->schedule_type,
            ];
        });

        return $user;
    }

    public function destroyUser(Request $request, $id_user)
    {
        try {
            $this->setLog('info', 'Request delete data User ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();
            $user = User::find($id_user);

            if ($user) {
                $user->delete();
            } else {
                DB::rollBack();
                return false;
            }


            $this->setLog('info', 'deleted  user data' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error delete user = ' . $exception->getMessage());
            $this->setLog('error', 'Error delete user = ' . $exception->getLine());
            $this->setLog('error', 'Error delete user = ' . $exception->getFile());
            $this->setLog('error', 'Error delete user = ' . $exception->getTraceAsString());
            return null;
        }
    }
}
