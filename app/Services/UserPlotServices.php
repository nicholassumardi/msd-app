<?php

namespace App\Services;

use App\Jobs\ImportUserPlotJob;
use App\Models\Structure;
use App\Models\StructurePlot;
use App\Models\User;
use App\Models\UserPlot;
use App\Models\UserPlotRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserPlotServices extends BaseServices
{
    protected $user;
    protected $userPlot;
    protected $structurePlot;
    protected $structure;

    public function __construct()
    {
        $this->user = User::with('company', 'department', 'userEmployeeNumber', 'userPlot');
        $this->userPlot = UserPlot::with('user', 'structurePlot');
        $this->structurePlot = StructurePlot::with('user', 'structure');
        $this->structure = Structure::with('department', 'jobCode', 'structurePlot', 'structureHistories');
    }

    public function ImportUserPlotExcel(Request $request, $cacheKey)
    {
        $file = $request->file;
        $filepath = $file->storeAs('temp', $request->file->getClientOriginalName(), 'public');
        $query =  ImportUserPlotJob::dispatch($filepath, $cacheKey);

        if ($query) {
            return true;
        }

        return false;
    }

    public function storeUserPlot(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data structure ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            $user = User::firstWhere('uuid', $request->uuid);

            if ($user) {
                $structurePlot = $this->structurePlot->where('id', $request->structurePlot)->first();

                $parentId = 0;

                if ($structurePlot && $structurePlot->parent && $structurePlot->parent->userPlot) {
                    $match = $structurePlot->parent->userPlot->firstWhere('group', 'LIKE', "%{$request->group[0]}%");
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
                        'job_code_id'                => $this->structure->where('id', $request->structure_id)->first() ? $this->structure->where('id', $request->structure_id)->first()->job_code_id : null,
                        'structure_id'               => $request->structure_plot_id ?? null,
                        'id_staff'                   => $request->id_staff ?? null,
                        'assign_date'                => $this->parseDateUTC($request->assign_date),
                        'status'                     => 1,
                    ];
                }

                UserPlot::insert($formattedRequest);

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

    public function storeUserPlotRequest(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data structure ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            // create empty userplot with no user that will be filled later when user is assigned
            $userPlot =  UserPlot::create([
                'user_id'                    => null,
                'parent_id'                  => 0,
                'structure_plot_id'          => (int) $request->structure_plot_id ?? null,
                'id_staff'                   => null,
                'assign_date'                => $this->parseDateUTC($request->assign_date),
                'status'                     => 0,
            ]);

            if ($userPlot) {
                UserPlotRequest::create([
                    'user_plot_id'     => $userPlot->id,
                    'group'            => $request->group,
                    'description'      => $request->description,
                    'request_date'     => $this->parseDateUTC($request->assign_date),
                    'status_slot'      => 0,
                ]);
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
            $this->setLog('error', 'Error store data structure = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data structure = ' . $exception->getLine());
            $this->setLog('error', 'Error store data structure = ' . $exception->getFile());
            $this->setLog('error', 'Error store data structure = ' . $exception->getTraceAsString());
            return null;
        }
    }


    public function updateUserPlot(Request $request, $uuid)
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
                        UserPlot::create($data);
                    } else {
                        UserPlot::where('user_id', $user->id)
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

    public function updateUserPlotRequest(Request $request, $id_user_plot)
    {
        try {
            $this->setLog('info', 'Request update data structure ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            $now = Carbon::now()->format('Y-m-d');
            $user = $this->user->firstWhere('uuid', $request->uuid);
            // check if user already have/ or inside another structure
            // need to work on this
            $exist = UserPlot::where('user_id', $user->id)->update([
                'status'        => 0,
                'reassign_date' => $now,
            ]);

            $structure = $this->structure->where('id', $request->structure_id)->first();
            $parentId = 0;

            if ($structure && $structure->parent && $structure->parent->structurePlot) {
                $match = $structure->parent->structurePlot->firstWhere('group', 'LIKE', "%{$request->group}%");
                $parentId = $match ? $match->id : 0;
            }

            $userPlot =  UserPlot::where('id', $id_user_plot)->update([
                'user_id'                    => $user->id ?? null,
                'parent_id'                  => $parentId ?? 0,
                'job_code_id'                => $this->structure->where('id', $request->structure_id)->first() ? $this->structure->where('id', $request->structure_id)->first()->job_code_id : null,
                'structure_id'               => (int) $request->structure_id ?? null,
                'id_structure'               => $request->id_structure ?? null,
                'id_staff'                   => $request->id_staff ?? null,
                'position_code_structure'    => $this->structure->where('id', $request->structure_id)->first() ? $this->structure->where('id', $request->structure_id)->first()->position_code_structure : null,
                'group'                      => $request->group ?? null,
                'assign_date'                => $request->assign_date ? $this->parseDateUTC($request->assign_date) : null,
                'employee_type'              => $structure->structure_type == "Staff" ? "Staff" : "Non Staff",
                'status'                     => 1,
            ]);


            if ($userPlot) {
                UserPlotRequest::where('user_plot_id', $id_user_plot)
                    ->update([
                        'user_plot_id'     => $id_user_plot,
                        'group'            => $request->group,
                        'status_slot'      => 1,
                    ]);
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

    public function updateUserPlotStatus($id_user_plot)
    {
        try {
            $this->setLog('info', 'Request update data structure');
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $userPlot = UserPlot::find($id_user_plot);
            $now = Carbon::now()->format('Y-m-d');
            if ($userPlot) {
                $userPlot->update([
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


    public function moveUserPlotRequest(Request $request, $id)
    {
        try {
            $this->setLog('info', 'Request store data structure ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            $structureRequest = UserPlotRequest::where('id', $id)->update([
                'status_slot'      => 0,
            ]);

            if ($structureRequest) {
                UserPlotRequest::create([
                    'user_plot_id'     => $request->user_plot_id,
                    'group'            => $request->group,
                    'description'      => $request->description,
                    'request_date'     => $request->request_date,
                    'status_slot'      => $request->status_slot,
                ]);
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
            $this->setLog('error', 'Error store data structure = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data structure = ' . $exception->getLine());
            $this->setLog('error', 'Error store data structure = ' . $exception->getFile());
            $this->setLog('error', 'Error store data structure = ' . $exception->getTraceAsString());
            return null;
        }
    }


    public function getDataUserPlot($uuid = NULL)
    {
        if (!empty($uuid)) {
            $queryData =  $this->user->firstWhere('uuid', $uuid)->userPlot;
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
                    'jobCode'            => $data->userPlot()->where('status', 1)->first()->jobCode->full_code ?? "",
                    'group'              => $data->userPlot()->where('status', 1)->first()->group ?? "",
                ];
            });
        }

        return $queryData;
    }

    public function getDataUserPlotPagination(Request $request)
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
                })->orWhereHas('userPlot', function ($query) use ($globalFilter) {
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
                'roleCode'           => $data->userPlot()->where('status', 1)->latest()->first()->structurePlot->structure->jobCode->full_code ?? "",
                'group'              => $data->userPlot()->where('status', 1)->latest()->first()->structurePlot->group ?? "",
                'id_staff'           => $data->userPlot()->where('status', 1)->latest()->first()->id_staff ?? "",
                'id_structure'       => $data->userPlot()->where('status', 1)->latest()->first()->structurePlot->id_structure ?? "",
                'position_code'      => $data->userPlot()->where('status', 1)->latest()->first()
                    ->structurePlot->position_code_structure ?? "",
                'sub_position'       => $data->userPlot()->where('status', 1)->latest()->first()->structurePlot->structure->name ?? "",
            ];
        });

        return $queryData;
    }

    public function getDataUserPlotPosition()
    {
        $queryData = $this->userPlot
            ->select(
                'structure_plots.position_code_structure',
                'job_codes.full_code'
            )
            ->join('structure_plots', 'user_plots.structure_id', '=', 'structure_plots.id')
            ->join('job_codes', 'structure_plots.job_code_id', '=', 'job_codes.id')
            ->groupBy('structure_plots.position_code_structure', 'job_codes.full_code')
            ->orderBy('structure_plots.position_code_structure', 'ASC')
            ->get();

        return $queryData;
    }

    public function destroyUserPlot(Request $request, $id)
    {
        try {
            $this->setLog('info', 'Request delete data User job code ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();
            $userPlot = UserPlot::find($id);

            if ($userPlot) {
                $userPlot->update([
                    'status' => 0
                ]);
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
