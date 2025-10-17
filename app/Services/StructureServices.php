<?php

namespace App\Services;

use App\Jobs\ImportUserJobCodeJob;
use App\Models\User;
use App\Models\UserJobCode;
use App\Models\UserStructureMapping;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StructureServices extends BaseServices
{
    protected $user;
    protected $userStructureMapping;
    protected $userJobCode;

    public function __construct()
    {
        $this->user = User::with('company', 'department', 'userEmployeeNumber', 'userJobCode');
        $this->userStructureMapping = UserStructureMapping::with('department', 'jobCode', 'userJobCode');
        $this->userJobCode = UserJobCode::with('user', 'jobCode');
    }

    public function importUserJobCodeExcel(Request $request)
    {
        $file = $request->file;
        $filepath = $file->storeAs('temp', $request->file->getClientOriginalName(), 'public');
        $query =  ImportUserJobCodeJob::dispatch($filepath);

        if ($query) {
            return true;
        }

        return false;
    }

    public function storeStructure(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data structure ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            $user = User::firstWhere('uuid', $request->uuid);

            if ($user) {
                $mapping = $this->userStructureMapping->where('id', $request->user_structure_mapping_id)->first();

                $parentId = 0;

                if ($mapping && $mapping->parent && $mapping->parent->userJobCode) {
                    $match = $mapping->parent->userJobCode->firstWhere('group', 'LIKE', "%{$request->group[0]}%");
                    $parentId = $match ? $match->id : 0;
                }

                if ($request->employeeStructures) {
                    $formattedRequest = array_map(function ($employeeStructure, $index) use ($user) {
                        return [
                            'user_id'       => $user->id,
                            'job_code_id'   => $employeeStructure['job_code_id'],
                            'group'         => $employeeStructure['group'] ?? null,
                            'status'        => $index === 0 ? 1 : 0
                        ];
                    }, $request->employeeStructures, array_keys($request->employeeStructures));
                } else {
                    $formattedRequest = [
                        'user_id'                    => $user->id,
                        'parent_id'                  => $parentId,
                        'job_code_id'                => $this->userStructureMapping->where('id', $request->user_structure_mapping_id)->first() ? $this->userStructureMapping->where('id', $request->user_structure_mapping_id)->first()->job_code_id : null,
                        'user_structure_mapping_id'  => $request->user_structure_mapping_id ?? null,
                        'id_structure'               => $request->id_structure ?? null,
                        'id_staff'                   => $request->id_staff ?? null,
                        'position_code_structure'    => $request->position_code_structure,
                        'group'                      => $request->group ?? null,
                        'assign_date'                => date('Y-m-d', strtotime($request->assign_date)),
                        'status'                     => 1,
                        'status_slot'                => 1
                    ];
                }

                UserJobCode::insert($formattedRequest);

                $this->setLog('info', 'New data structure' . json_encode($request->all()));
                DB::commit();
                $this->setLog('info', 'End');
                return true;
            } else {
                DB::rollBack();
                return false;
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data structure = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data structure = ' . $exception->getLine());
            $this->setLog('error', 'Error store data structure = ' . $exception->getFile());
            $this->setLog('error', 'Error store data structure = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function requestNewEmployee(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data structure ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            $formattedRequest = [
                'job_code_id'                => $this->userStructureMapping->where('id', $request->user_structure_mapping_id)->first() ? $this->userStructureMapping->where('id', $request->user_structure_mapping_id)->first()->job_code_id : null,
                'user_structure_mapping_id'  => $request->user_structure_mapping_id ?? null,
                'id_structure'               => $request->id_structure ?? null,
                'position_code_structure'    => $request->position_code_structure,
                'group'                      => $request->group ?? null,
                'assign_date'                => date('Y-m-d', strtotime($request->assign_date)),
                'status'                     => 0,
                'status_slot'                => 0

            ];

            UserJobCode::insert($formattedRequest);

            $this->setLog('info', 'New data structure' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data structure = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data structure = ' . $exception->getLine());
            $this->setLog('error', 'Error store data structure = ' . $exception->getFile());
            $this->setLog('error', 'Error store data structure = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function getDataStructure($uuid = NULL)
    {
        if (!empty($uuid)) {
            $queryData =  $this->user->firstWhere('uuid', $uuid)->userJobCode;
        } else {
            $queryData = $this->user->get();
            $queryData = $queryData->map(function ($data) {
                return [
                    'uuid'               => $data->uuid,
                    'name'               => $data->name,
                    'company_name'       => $data->company ? $data->company->name : '',
                    'company_id'         => $data->company_id,
                    'department_name'    => $data->department ? $data->department->name : '',
                    'employee_number'    => $data->userEmployeeNumber()->where('status', 1)->first()->employee_number ?? "",
                    'department_id'      => $data->department_id,
                    'jobCode'            => $data->userJobCode()->where('status', 1)->first()->jobCode->full_code ?? "",
                    'group'              => $data->userJobCode()->where('status', 1)->first()->group ?? "",
                ];
            });
        }

        return $queryData;
    }


    public function getDataStructurePagination(Request $request)
    {
        $start = (int) $request->start;
        $size = (int)$request->size;
        $filters = json_decode($request->filters, true) ?? [];
        $sorting = json_decode($request->sorting, true) ?? [];
        $globalFilter = $request->globalFilter ?? '';

        $queryData = $this->user->where(function ($query) use ($request, $filters, $globalFilter) {
            if ($request->id_department) {
                $query->where('department_id', $request->id_department);
            }

            if ($globalFilter) {
                $query->where(function ($query) use ($globalFilter) {
                    $query->where('name', 'LIKE',  "%$globalFilter%")
                        ->orWhere('date_of_birth', 'LIKE',  "%$globalFilter%")
                        ->orWhere('identity_card', 'LIKE',  "%$globalFilter%")
                        ->orWhere('gender', 'LIKE',  "%$globalFilter%")
                        ->orWhere('religion', 'LIKE',  "%$globalFilter%")
                        ->orWhere('email', 'LIKE',  "%$globalFilter%")
                        ->orWhere('address', 'LIKE',  "%$globalFilter%")
                        ->orWhere('phone', 'LIKE',  "%$globalFilter%")
                        ->orWhere('education', 'LIKE',  "%$globalFilter%")
                        ->orWhere('position_code', 'LIKE',  "%$globalFilter%")
                        ->orWhere('status_twiji', 'LIKE',  "%$globalFilter%")
                        ->orWhere('schedule_type', 'LIKE',  "%$globalFilter%");
                })->orWhereHas('department', function ($query) use ($globalFilter) {
                    $query->where('name', 'LIKE',  "%$globalFilter%");
                })->orWhereHas('company', function ($query) use ($globalFilter) {
                    $query->where('name', 'LIKE',  "%$globalFilter%");
                })->orWhereHas('userEmployeeNumber', function ($query) use ($globalFilter) {
                    $query->where('employee_number', 'LIKE',  "%$globalFilter%");
                })->orWhereHas('userJobCode', function ($query) use ($globalFilter) {
                    $query->whereHas('jobCode', function ($query) use ($globalFilter) {
                        $query->where('position', 'LIKE',  "%$globalFilter%");
                    });
                });
            }

            foreach ($filters as $filter) {
                $query->where($filter['id'], $filter['value']);
            }
        });

        foreach ($sorting as $sort) {
            if (isset($sort['id'])) {
                $queryData->orderBy($sort['id'], $sort['desc'] ? 'DESC' : 'ASC');
            }
        }

        $queryData = $queryData->skip($start)
            ->take($size)
            ->get();

        $queryData = $queryData->map(function ($data) {
            return [
                'uuid'               => $data->uuid,
                'name'               => $data->name,
                'company_name'       => $data->company ? $data->company->name : 'NaN',
                'company_id'         => $data->company_id,
                'department_name'    => $data->department ? $data->department->name : 'NaN',
                'employee_number'    => $data->userEmployeeNumber()->where('status', 1)->latest()->first()->employee_number ?? "",
                'department_id'      => $data->department_id,
                'roleCode'           => $data->userJobCode()->where('status', 1)->latest()->first()->jobCode->full_code ?? "",
                'group'              => $data->userJobCode()->where('status', 1)->latest()->first()->group ?? "",
                'id_staff'           => $data->userJobCode()->where('status', 1)->latest()->first()->id_staff ?? "",
                'id_structure'       => $data->userJobCode()->where('status', 1)->latest()->first()->id_structure ?? "",
                'position_code'      => $data->userJobCode()->where('status', 1)->latest()->first()->position_code_structure ?? "",
                'sub_position'       => $data->userJobCode()->where('status', 1)->latest()->first()->userStructureMapping->name ?? "",
            ];
        });

        return $queryData;
    }

    public function getDataUserJobCode()
    {
        $queryData = $this->userJobCode
            ->select(
                'user_job_code.position_code_structure',
                'job_codes.full_code'
            )
            ->join('job_codes', 'user_job_code.job_code_id', '=', 'job_codes.id')
            ->groupBy('user_job_code.position_code_structure', 'job_codes.full_code')
            ->orderBy('user_job_code.position_code_structure', 'ASC')
            ->get();

        return $queryData;
    }


    public function updateStructure(Request $request, $uuid)
    {
        try {
            $this->setLog('info', 'Request update data structure ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $user = User::firstWhere('uuid', $uuid);

            if ($user) {
                foreach ($request->employeeStructures as $key => $structure) {
                    $data = [
                        'user_id'     => $user->id,
                        'job_code_id' => $structure['job_code_id'],
                        'group'       => $structure['group'],
                        'status'      => $key == 0 ? 1 : 0
                    ];

                    if (!isset($structure['id'])) {
                        UserJobCode::create($data);
                    } else {
                        UserJobCode::where('user_id', $user->id)
                            ->where('id', $structure['id'])
                            ->update($data);
                    }
                }
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'updated data structure ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error update data structure = ' . $exception->getMessage());
            $this->setLog('error', 'Error update data structure = ' . $exception->getLine());
            $this->setLog('error', 'Error update data structure = ' . $exception->getFile());
            $this->setLog('error', 'Error update data structure = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateStructureStatus($id_user_job_code)
    {
        try {
            $this->setLog('info', 'Request update data structure');
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $userJobCode = UserJobCode::find($id_user_job_code);
            $now = Carbon::now()->format('Y-m-d');
            if ($userJobCode) {
                $userJobCode->update([
                    'status'        => 0,
                    'reassign_date' => $now

                ]);
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'updated data structure');
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error update data employee number = ' . $exception->getMessage());
            $this->setLog('error', 'Error update data employee number = ' . $exception->getLine());
            $this->setLog('error', 'Error update data employee number = ' . $exception->getFile());
            $this->setLog('error', 'Error update data employee number = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function destroyStructure(Request $request, $id)
    {
        try {
            $this->setLog('info', 'Request delete data User job code ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();
            $userJobCode = UserJobCode::find($id);

            if ($userJobCode) {
                $userJobCode->delete();
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
            $this->setLog('error', 'Error delete user job code = ' . $exception->getMessage());
            $this->setLog('error', 'Error delete user job code = ' . $exception->getLine());
            $this->setLog('error', 'Error delete user job code = ' . $exception->getFile());
            $this->setLog('error', 'Error delete user job code = ' . $exception->getTraceAsString());
            return null;
        }
    }
}
