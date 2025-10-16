<?php

namespace App\Services;

use App\Jobs\ImportStructureJob;
use App\Models\UserJobCode;
use App\Models\UserStructureMapping;
use App\Models\UserStructureMappingHistories;
use App\Models\UserStructureMappingRequest;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserStructureMappingServices extends BaseServices
{
    protected $userMapping;
    protected $userMappingRaw;
    protected $userJobCode;
    protected $userStructureMappingHistories;
    protected $user;
    public function __construct()
    {
        $this->userMapping =  UserStructureMapping::with('department', 'jobCode', 'children')->with([
            'userJobCode.user.userEmployeeNumber' => function ($query) {
                $query->where('status', 1);
            }
        ]);

        $this->userJobCode = UserJobCode::with('UserStructureMapping', 'jobCode')->with([
            'user.userEmployeeNumber' => function ($query) {
                $query->where('status', 1);
            }
        ]);

        $this->userStructureMappingHistories = UserStructureMappingHistories::with('userStructureMapping');

        $this->userMappingRaw = UserStructureMapping::get();

        $this->user = User::query();
    }

    public function importStructureExcel(Request $request, $cacheKey)
    {
        $file = $request->file;
        $filepath = $file->storeAs('temp', $request->file->getClientOriginalName(), 'public');
        $query =  ImportStructureJob::dispatch($filepath, $cacheKey);

        if ($query) {
            return true;
        }

        return false;
    }

    public function storeUserMapping(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data user mapping ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            $usm =  UserStructureMapping::create([
                'department_id'           => $request->department_id,
                'position_code_structure' => $request->position_code_structure ?? null,
                'job_code_id'             => $request->job_code_id ?? null,
                'parent_id'               => $request->parent_id ?? 0,
                'name'                    => $request->name,
                'quota'                   => $request->quota,
                'structure_type'          => $request->structure_type,
            ]);

            $now = Carbon::now();

            if ($usm) {
                UserStructureMappingHistories::create([
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
                    'logs'                      => "",
                ]);
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'store data user mapping ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data user mapping = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data user mapping = ' . $exception->getLine());
            $this->setLog('error', 'Error store data user mapping = ' . $exception->getFile());
            $this->setLog('error', 'Error store data user mapping = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function storeUserMappingRequest(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data user mapping ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            // create empty userjobcode with no user that will be filled later when user is assigned
            $userJobCode =  UserJobCode::create([
                'user_id'                    => null,
                'parent_id'                  => 0,
                'job_code_id'                => $this->userMapping->where('id', $request->user_structure_mapping_id)->first() ? $this->userMapping->where('id', $request->user_structure_mapping_id)->first()->job_code_id : null,
                'user_structure_mapping_id'  => (int) $request->user_structure_mapping_id ?? null,
                'id_structure'               => $request->id_structure,
                'id_staff'                   => null,
                'position_code_structure'    => $this->userMapping->where('id', $request->user_structure_mapping_id)->first() ? $this->userMapping->where('id', $request->user_structure_mapping_id)->first()->position_code_structure : null,
                'group'                      => $request->group ?? null,
                'assign_date'                => date('Y-m-d', strtotime($request->assign_date)),
                'status'                     => 0,
            ]);

            if ($userJobCode) {
                UserStructureMappingRequest::create([
                    'user_job_code_id' => $userJobCode->id,
                    'group'            => $request->group,
                    'description'      => $request->description,
                    'request_date'     => date('Y-m-d', strtotime($request->assign_date)),
                    'status_slot'      => 0,
                ]);
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'updated data user mapping ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data user mapping = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data user mapping = ' . $exception->getLine());
            $this->setLog('error', 'Error store data user mapping = ' . $exception->getFile());
            $this->setLog('error', 'Error store data user mapping = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateUserMapping(Request $request, $id_user_mapping)
    {
        try {
            $this->setLog('info', 'Request store data user mapping ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            $userMapping = UserStructureMapping::find($id_user_mapping);

            if ($userMapping) {

                $logMessages = [];

                $fieldsToCheck = [
                    'department_id'           => 'Department',
                    'position_code_structure' => 'Position Code Structure',
                    'job_code_id'             => 'Job Code',
                    'parent_id'               => 'Parent',
                    'name'                    => 'Name',
                    'structure_type'          => 'Structure Type',
                    'quota'                   => 'Quota',
                ];

                $hasChanges = false;

                foreach ($fieldsToCheck as $field => $label) {
                    $oldValue = $userMapping->$field;
                    $newValue = $request->$field ?? ($field === 'parent_id' ? 0 : null);

                    if ($newValue != $oldValue) {
                        $hasChanges = true;

                        if ($field === 'quota') {
                            if ($newValue > $oldValue) {
                                $logMessages[] = "$label has been increased from $oldValue to $newValue";
                            } else {
                                $logMessages[] = "$label has been decreased from $oldValue to $newValue";
                            }
                        } else {
                            $logMessages[] = "$label has been changed from '$oldValue' to '$newValue'";
                        }
                    }
                }

                $userMapping->update([
                    'department_id'            => $request->department_id,
                    'position_code_structure'  => $request->position_code_structure,
                    'job_code_id'              => $request->job_code_id ?? null,
                    'parent_id'                => $request->parent_id ?? 0,
                    'name'                     => $request->name,
                    'quota'                    => $request->quota,
                    'structure_type'           => $request->structure_type,
                ]);

                $logMessage = implode('; ', $logMessages);


                $now = Carbon::now();

                if ($hasChanges) {
                    UserStructureMappingHistories::create([
                        'user_structure_mapping_id' => $userMapping->id,
                        'revision_no'               =>  $this->userStructureMappingHistories->where('user_structure_mapping_id', $userMapping->id)->max('revision_no') + 1,
                        'valid_date'                => $request->valid_date ? date('Y-m-d', strtotime($request->valid_date)) : $now,
                        'updated_date'              => $request->updated_date ? date('Y-m-d', strtotime($request->updated_date)) : $now,
                        'authorized_date'           => $request->authorized_date ? date('Y-m-d', strtotime($request->authorized_date)) : $now,
                        'approval_date'             => $request->approval_date ? date('Y-m-d', strtotime($request->approval_date)) : $now,
                        'acknowledged_date'         => $request->acknowledged_date ?  date('Y-m-d', strtotime($request->acknowledged_date)) : $now,
                        'created_date'              => $request->created_date ? date('Y-m-d', strtotime($request->created_date)) : $now,
                        'distribution_date'         => $request->distribution_date ? date('Y-m-d', strtotime($request->distribution_date)) : $now,
                        'withdrawal_date'           => $request->withdrawal_date ? date('Y-m-d', strtotime($request->withdrawal_date)) : $now,
                        'logs'                      => $logMessage,
                    ]);
                }
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'updated data user mapping ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data user mapping = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data user mapping = ' . $exception->getLine());
            $this->setLog('error', 'Error store data user mapping = ' . $exception->getFile());
            $this->setLog('error', 'Error store data user mapping = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateBulkUserMapping(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data user mapping ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            foreach ($request->bulk_edit as $item) {

                // Use array access
                $userMapping = $this->userMappingRaw->firstWhere('id', $item['id']);

                if ($userMapping) {

                    $oldQuota = $userMapping->quota;
                    $newQuota = $item['quota'];

                    $logMessage = '';

                    if ($newQuota > $oldQuota) {
                        $logMessage = "Quota has been added from $oldQuota to $newQuota";
                    } elseif ($newQuota < $oldQuota) {
                        $logMessage = "Quota has been decreased from $oldQuota to $newQuota";
                    }

                    $userMapping->update([
                        'department_id'   => (int) $item['department_id'] ?? null,
                        'job_code_id'     => (int) $item['job_code_id'] ?? null,
                        'parent_id'       => (int) $item['parent_id'] ?? null,
                        'name'            => $item['name'],
                        'quota'           => (int) $item['quota'],
                        'structure_type'  => $item['structure_type'],
                    ]);

                    if ($newQuota != $oldQuota) {
                        UserStructureMappingHistories::create([
                            'user_structure_mapping_id' => (int) $userMapping->id ?? null,
                            'revision_no'               => $this->userStructureMappingHistories->where('user_structure_mapping_id', $userMapping->id)->max('revision_no') + 1 ?? 0,
                            'valid_date'                => $request->valid_date ? date('Y-m-d', strtotime($request->valid_date)) : null,
                            'updated_date'              => $request->updated_date ? date('Y-m-d', strtotime($request->updated_date)) : null,
                            'authorized_date'           => $request->authorized_date ? date('Y-m-d', strtotime($request->authorized_date)) : null,
                            'approval_date'             => $request->approval_date ? date('Y-m-d', strtotime($request->approval_date)) : null,
                            'acknowledged_date'         => $request->acknowledged_date ? date('Y-m-d', strtotime($request->acknowledged_date)) : null,
                            'created_date'              => $request->created_date ? date('Y-m-d', strtotime($request->created_date)) : null,
                            'distribution_date'         => $request->distribution_date ? date('Y-m-d', strtotime($request->distribution_date)) : null,
                            'withdrawal_date'           => $request->withdrawal_date ? date('Y-m-d', strtotime($request->withdrawal_date)) : null,
                            'logs'                      => $logMessage,
                        ]);
                    }
                } else {
                    DB::rollBack();
                    return false;
                }
            }

            $this->setLog('info', 'updated data user mapping ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data user mapping = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data user mapping = ' . $exception->getLine());
            $this->setLog('error', 'Error store data user mapping = ' . $exception->getFile());
            $this->setLog('error', 'Error store data user mapping = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateUserMappingRequest(Request $request, $id_user_job_code)
    {
        try {
            $this->setLog('info', 'Request update data user mapping ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();


            $user = $this->user->firstWhere('uuid', $request->uuid);
            // check if user already have/ or inside another structure
            // need to work on this
            $exist = UserJobCode::where('user_id', $user->id)->update([
                'status'   => 0,
            ]);

            $mapping = $this->userMapping->where('id', $request->user_structure_mapping_id)->first();
            $parentId = 0;

            if ($mapping && $mapping->parent && $mapping->parent->userJobCode) {
                $match = $mapping->parent->userJobCode->firstWhere('group', 'LIKE', "%{$request->group}%");
                $parentId = $match ? $match->id : 0;
            }

            $userJobCode =  UserJobCode::where('id', $id_user_job_code)->update([
                'user_id'                    => $user->id ?? null,
                'parent_id'                  => $parentId ?? 0,
                'job_code_id'                => $this->userMapping->where('id', $request->user_structure_mapping_id)->first() ? $this->userMapping->where('id', $request->user_structure_mapping_id)->first()->job_code_id : null,
                'user_structure_mapping_id'  => (int) $request->user_structure_mapping_id ?? null,
                'id_structure'               => $request->id_structure ?? null,
                'id_staff'                   => $request->id_staff ?? null,
                'position_code_structure'    => $this->userMapping->where('id', $request->user_structure_mapping_id)->first() ? $this->userMapping->where('id', $request->user_structure_mapping_id)->first()->position_code_structure : null,
                'group'                      => $request->group ?? null,
                'assign_date'                => $request->assign_date ? date('Y-m-d', strtotime($request->assign_date)) : null,
                'employee_type'              => $mapping->structure_type == "Staff" ? "Staff" : "Non Staff",
                'status'                     => 1,
            ]);


            if ($userJobCode) {
                UserStructureMappingRequest::where('user_job_code_id', $id_user_job_code)
                    ->update([
                        'user_job_code_id' => $id_user_job_code,
                        'group'            => $request->group,
                        'status_slot'      => 1,
                    ]);
            } else {
                DB::rollBack();
                return false;
            }


            $this->setLog('info', 'updated data user mapping ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error update data user mapping = ' . $exception->getMessage());
            $this->setLog('error', 'Error update data user mapping = ' . $exception->getLine());
            $this->setLog('error', 'Error update data user mapping = ' . $exception->getFile());
            $this->setLog('error', 'Error update data user mapping = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function moveUserMappingRequest(Request $request, $id)
    {
        try {
            $this->setLog('info', 'Request store data user mapping ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            $userMappingRequest = UserStructureMappingRequest::where('id', $id)->update([
                'status_slot'      => 0,
            ]);

            if ($userMappingRequest) {
                UserStructureMappingRequest::create([
                    'user_job_code_id' => $request->user_job_code_id,
                    'group'            => $request->group,
                    'description'      => $request->description,
                    'request_date'     => $request->request_date,
                    'status_slot'      => $request->status_slot,
                ]);
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'updated data user mapping ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data user mapping = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data user mapping = ' . $exception->getLine());
            $this->setLog('error', 'Error store data user mapping = ' . $exception->getFile());
            $this->setLog('error', 'Error store data user mapping = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function getDataUserMapping($id_user_mapping = NULL)
    {
        if (!empty($id_user_mapping)) {
            $userMapping = $this->userMapping
                ->withCount([
                    'userJobCode as totalAssignedEmployee' => function ($query) {
                        $query->where('status', 1);
                    }
                ])
                ->findOrFail($id_user_mapping);

            $userMapping->userJobCode->transform(function ($item) {
                $item->uuid = $item->user->uuid ?? null;
                $item->employee_name = $item->user->name ?? null;
                return $item;
            });

            $userMapping->employee_number = '';
            $userMapping->superior = $userMapping->parent->name ?? 'None';
            $userMapping->company_name = $userMapping->department->company->name ?? 'None';
            $userMapping->hasChildren = $userMapping->children()->exists();
        } else {
            $userMapping = $this->userMapping;
        }

        return $userMapping;
    }

    public function getDataUserMappingByDepartment($request)
    {
        $currentPage = (int) $request->current_page ? $request->current_page : 1;
        $perPage = (int) $request->per_page ? $request->per_page : 5;

        $query = $this->userMapping;
        if ($request->id_department) {
            $query = $query->where('department_id', $request->id_department);
        }

        $query = $query
            ->withCount(['userJobCode as totalAssignedEmployee' => function ($query) {
                $query->where('status', 1);
            }])->where(function ($query) use ($request) {
                if ($request->globalFilter) {
                    $query->where('name', 'LIKE', "%$request->globalFilter%");
                }
            })
            ->orderBy('id');

        $totalCount = $query->count();


        $userMapping = $query
            ->skip(($currentPage - 1) * $perPage)
            ->take($perPage)
            ->get();

        return [
            'data'        => $userMapping,
            'totalCount'  => $totalCount
        ];
    }

    // for hierarchy chart purpose (dormamu chart)
    public function getDataAllMappingHierarchy(Request $request, $id)
    {
        function mapChildren($data, $includeRelationship = false, $request)
        {

            if ($data->parent_id != 0) {
                if ($request->employee_type && (!isset($data->jobCode) || $data->jobCode->level < 4)) {
                    return null;
                }


                if ($request->position && (!isset($data->jobCode) || $data->jobCode->level < 2)) {
                    return null;
                }
            }
            $result = [
                'id'         => $data->id,
                'name'       => $data->name,
                'jobCode'    => $data->jobCode->full_code ?? "",
                'level'      => $data->jobCode->level ?? "",
                'childCount' => $data->parent ? $data->parent->children->count() : 0,
                'desc'       => $data->userJobCode()
                    ->where('status', 1)
                    ->orderByRaw('LEFT(`group`, 1)')
                    ->get()
                    ->map(function ($d) {
                        return [
                            "id"                       => $d->id,
                            "pic"                      => $d->user->name ?? "",
                            "employee_number"          => $d->user->userEmployeeNumber()->where('status', 1)->first()->employee_number ?? "",
                            "job_code_id"              => $d->job_code_id,
                            "id_structure"             => $d->id_structure,
                            "id_staff"                 => $d->id_staff,
                            "position_code_structure"  => $d->position_code_structure ?? "",
                            "group"                    => $d->group,
                            'employee_type'            => $d->employee_type ?? "",
                            "assign_date"              => $d->assign_date ?? ""
                        ];
                    }),
            ];

            if ($includeRelationship) {
                $result['relationship'] = '00';
            }

            // Recursively map children, and filter out any that return null.
            $result['children'] = $data->children->map(function ($child) use ($request) {
                return mapChildren($child, false, $request);
            })->filter()->values();

            return $result;
        }


        $userMapping = $this->userMapping->where(function ($query) use ($id) {
            if ($id) {
                $query->where('department_id', $id);
            }
        })->where('parent_id', 0)
            ->with(['children.jobCode', 'children.userJobCode', 'jobCode', 'userJobCode'])
            ->get()
            ->map(fn($data) => mapChildren($data, true, $request));


        return $userMapping;
    }

    // hierarchy chart (unicef/ somkid)
    public function getMappingHierarchyUser($id)
    {
        $userJobCode = $this->userJobCode
            ->where('user_id', $id)
            ->where('status', 1)
            ->first();

        if (!$userJobCode) {
            return [];
        }

        $userJobCode =
            [
                'id'         => $userJobCode->id,
                'parentId'   => $userJobCode->parent_id,
                'person'     =>
                [
                    'id'            => $userJobCode->id,
                    'avatar'        => '/images/pepe-tired.jpg',
                    'department'    => $userJobCode->userStructureMapping->department->name . " (" . $userJobCode->userStructureMapping->department->code . ") " .  $userJobCode->userStructureMapping->group,
                    'name'          => $userJobCode->user->name ?? "-",
                    'employee_type' => $userJobCode->user->employee_type ?? "-",
                    'address'       => $userJobCode->user->address ?? "-",
                    'date_of_birth' => $this->parseDateMdY($userJobCode->user->date_of_birth) ?? "-",
                    'age'           => Carbon::parse($userJobCode->user->date_of_birth)->age,
                    'color'         => $this->getColor($userJobCode->jobCode->level),
                    'title'         => $userJobCode->user->userEmployeeNumber()->where('status', 1)->first()->employee_number ?? "",
                    'position'      => $userJobCode->userStructureMapping->name,
                    'position_code' =>  $userJobCode->userStructureMapping->position_code_structure,
                    'totalReports'  => count($userJobCode->children()->get()),
                ],
                'children'  => [],
                'hasChild'  => count($userJobCode->children()->get()) > 0 ? true : false,
                'hasParent' => count($userJobCode->parent()->get()) > 0 ? true : false,
            ];





        return $userJobCode;
    }

    public function getMappingHierarchyParent($parent_id)
    {
        $result = [];
        $userJobCode = $this->userJobCode
            ->where('id', $parent_id)
            ->first();
        if (!$userJobCode) {
            return [];
        }

        $result =
            [
                'id'         => $userJobCode->id,
                'parentId'   => $userJobCode->parent_id,
                'person'     =>
                [
                    'id'            => $userJobCode->id,
                    'avatar'        => '/images/pepe-tired.jpg',
                    'department'    => $userJobCode->userStructureMapping->department->name . " (" . $userJobCode->userStructureMapping->department->code . ") " .  $userJobCode->userStructureMapping->group,
                    'name'          => $userJobCode->user->name ?? "-",
                    'employee_type' => $userJobCode->user->employee_type ?? "-",
                    'address'       => $userJobCode->user->address ?? "-",
                    'date_of_birth' => $this->parseDateMdY($userJobCode->user->date_of_birth) ?? "-",
                    'age'           => Carbon::parse($userJobCode->user->date_of_birth)->age,
                    'color'         => $this->getColor($userJobCode->jobCode->level),
                    'title'         => $userJobCode->user->userEmployeeNumber()->where('status', 1)->first()->employee_number ?? "",
                    'position'      => $userJobCode->userStructureMapping->name,
                    'position_code' => $userJobCode->userStructureMapping->position_code_structure,
                    'totalReports'  => count($userJobCode->children()->get()),
                ],
                'hasChild'  => count($userJobCode->children()->get()) > 0 ? true : false,
                'hasParent' => count($userJobCode->parent()->get()) > 0 ? true : false,
            ];

        foreach ($userJobCode->children as $data) {
            $result['children'][] = [
                'id'         => $data->id,
                'parentId'   => $data->parent_id,
                'person'     =>
                [
                    'id'            => $data->id,
                    'avatar'        => '/images/pepe-tired.jpg',
                    'department'    =>  $data->userStructureMapping->department ? $data->userStructureMapping->department->name . " (" . $data->userStructureMapping->department->code . ") " .  $data->userStructureMapping->group : "-",
                    'name'          => $data->user->name ?? "-",
                    'employee_type' => $data->user->employee_type ?? "-",
                    'address'       => $data->user->address ?? "-",
                    'date_of_birth' => $this->parseDateMdY($data->user->date_of_birth) ?? "-",
                    'age'           => Carbon::parse($data->user->date_of_birth)->age,
                    'color'         => $this->getColor($data->jobCode->level),
                    'title'         => $data->user->userEmployeeNumber()->where('status', 1)->first()->employee_number ?? "",
                    'position'      => $data->userStructureMapping->name,
                    'position_code' => $data->userStructureMapping->position_code_structure,
                    'totalReports'  => count($data->children()->get()),
                ],
                'children'  => [],
                'hasChild'  => count($data->children()->get()) > 0 ? true : false,
                'hasParent' => count($data->parent()->get()) > 0 ? true : false,
            ];
        }

        return $result;
    }

    public function getMappingHierarchyChildren($id)
    {
        $userJobCode = $this->userJobCode
            ->where('id', $id)
            ->first();

        if (!$userJobCode) {
            return [];
        }

        $result = [];
        foreach ($userJobCode->children as $data) {
            $result[] = [
                'id'         => $data->id,
                'parentId'   => $data->parent_id,
                'person'     =>
                [
                    'id'            => $data->id,
                    'avatar'        => '/images/pepe-tired.jpg',
                    'department'    =>  $data->userStructureMapping->department ? $data->userStructureMapping->department->name . " (" . $data->userStructureMapping->department->code . ") " .  $data->userStructureMapping->group : "-",
                    'name'          => $data->user->name ?? "-",
                    'employee_type' => $data->user->employee_type ?? "-",
                    'address'       => $data->user->address ?? "-",
                    'date_of_birth' => $this->parseDateMdY($data->user->date_of_birth) ?? "-",
                    'age'           => Carbon::parse($data->user->date_of_birth)->age,
                    'color'         => $this->getColor($data->jobCode->level),
                    'title'         => $data->user->userEmployeeNumber()->where('status', 1)->first()->employee_number ?? "",
                    'position'      => $data->userStructureMapping->name,
                    'position_code' => $data->userStructureMapping->position_code_structure,
                    'totalReports'  => count($data->children()->get()),
                ],
                'children'  => [],
                'hasChild'  => count($data->children()->get()) > 0 ? true : false,
                'hasParent' => count($data->parent()->get()) > 0 ? true : false,
            ];
        }


        return $result;
    }

    public function destroyUserMapping(Request $request, $id_user_mapping)
    {
        try {
            $this->setLog('info', 'Request delete data User mapping ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            $userMapping = UserStructureMapping::find($id_user_mapping);

            // Check if user mapping exists & has children, move childrent to usermapping parent_id
            if ($userMapping->children()->exist()) {
                foreach ($userMapping->children as $user) {
                    $user->update([
                        'parent_id' => $userMapping->parent_id,
                    ]);
                }
            }

            if ($request->isAllowed) {
                $userMapping->delete();
            } else {
                DB::rollBack();
                return false;
            }


            $this->setLog('info', 'deleted  user mapping data' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error delete user mapping = ' . $exception->getMessage());
            $this->setLog('error', 'Error delete user mapping = ' . $exception->getLine());
            $this->setLog('error', 'Error delete user mapping = ' . $exception->getFile());
            $this->setLog('error', 'Error delete user mapping = ' . $exception->getTraceAsString());
            return null;
        }
    }
}
