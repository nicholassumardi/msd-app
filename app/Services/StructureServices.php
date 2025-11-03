<?php

namespace App\Services;

use App\Jobs\ImportStructureJob;
use App\Models\Structure;
use App\Models\StructureHistories;
use App\Models\StructurePlot;
use App\Models\User;
use App\Models\UserPlot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StructureServices extends BaseServices
{
    protected $structure;
    protected $structureRaw;
    protected $structurePlot;
    protected $userPlot;
    protected $structureHistories;
    protected $user;

    public function __construct()
    {
        $this->structure =  Structure::with([
            'department',
            'jobCode',
            'children',
            'structureHistories',
            'structurePlot.userPlot.user.userEmployeeNumber' => function ($query) {
                $query->where('status', 1);
            },
        ]);

        $this->structurePlot = StructurePlot::with('structure')->with([
            'userPlot.user.userEmployeeNumber' => function ($query) {
                $query->where('status', 1);
            }
        ]);

        $this->userPlot = UserPlot::with('user', 'structurePlot.structure.department')->with([
            'user.userEmployeeNumber' => function ($query) {
                $query->where('status', 1);
            }
        ]);

        $this->structureHistories = StructureHistories::with('structure');

        $this->structureRaw = Structure::get();

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

    public function storeStructure(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data structure ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            $structure =  Structure::create([
                'department_id'           => $request->department_id,
                'position_code_structure' => $request->position_code_structure ?? null,
                'job_code_id'             => $request->job_code_id ?? null,
                'parent_id'               => $request->parent_id ?? 0,
                'name'                    => $request->name,
                'quota'                   => $request->quota,
                'structure_type'          => $request->structure_type,
            ]);

            $now = Carbon::now()->format('Y-m-d');

            if ($structure) {
                if ($request->structure_plot && is_array($request->structure_plot)) {
                    $groupSuffixCount = [];

                    foreach ($request->structure_plot as $plot) {
                        $group = $plot['group'] ?? null;
                        $mainGroup = $group ? $group[0] : null;

                        if (!isset($groupSuffixCount[$mainGroup])) {
                            $groupSuffixCount[$mainGroup] = 1;
                        } else {
                            $groupSuffixCount[$mainGroup]++;
                        }

                        $suffix = $groupSuffixCount[$mainGroup];

                        StructurePlot::create([
                            'structure_id'            => $structure->id,
                            'parent_id'               => $plot['parent_id'] ?? 0,
                            'id_structure'            => $plot['id_structure'] ?? null,
                            'position_code_structure' => $plot['position_code_structure'] ?? null,
                            'suffix'                  => $suffix,
                            'group'                   => $group,
                        ]);
                    }
                }

                StructureHistories::create([
                    'structure_id'              => $structure->id,
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

            $this->setLog('info', 'store data structure ' . json_encode($request->all()));
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

    public function updateStructure(Request $request, $id_structure)
    {
        try {
            $this->setLog('info', 'Request store data structure ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            $structure = Structure::find($id_structure);

            if ($structure) {

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
                    $oldValue = $structure->$field;
                    $newValue = $request->$field ?? ($field === 'parent_id' ? 0 : null);

                    if ($newValue != $oldValue) {
                        $hasChanges = true;

                        $logMessages[] = "$label has been changed from '$oldValue' to '$newValue'";
                    }
                }

                $structure->update([
                    'department_id'            => $request->department_id,
                    'position_code_structure'  => $request->position_code_structure,
                    'job_code_id'              => $request->job_code_id ?? null,
                    'parent_id'                => $request->parent_id ?? 0,
                    'name'                     => $request->name,
                    'quota'                    => $request->quota,
                    'structure_type'           => $request->structure_type,
                ]);

                if ($request->structure_plot && is_array($request->structure_plot)) {
                    $groupSuffixCount = [];

                    $existingPlots = StructurePlot::where('structure_id', $id_structure)->get();
                    $existingByIdStructure = $existingPlots->keyBy('id_structure');

                    $seenIdStructures = [];

                    foreach ($request->structure_plot as $plot) {
                        $group = $plot['group'] ?? null;
                        $mainGroup = $group ? $group[0] : null;

                        if (!isset($groupSuffixCount[$mainGroup])) {
                            $lastSuffix = StructurePlot::where('structure_id', $id_structure)
                                ->whereRaw('LEFT(`group`, 1) = ?', [$mainGroup])
                                ->max('suffix');
                            $groupSuffixCount[$mainGroup] = $lastSuffix ? $lastSuffix : 0;
                        }

                        $existing = $existingByIdStructure->get($plot['id_structure'] ?? null);

                        if ($existing) {
                            $changes = [
                                'parent_id'               => $plot['parent_id'] ?? 0,
                                'id_structure'            => $plot['id_structure'] ?? null,
                                'position_code_structure' => $plot['position_code_structure'] ?? null,
                                'group'                   => $plot['group'] ?? null,
                            ];

                            if (collect($changes)->diffAssoc($existing->only(array_keys($changes)))->isNotEmpty()) {
                                $existing->update($changes);
                            }

                            $seenIdStructures[] = $existing->id_structure;
                        } else {
                            $groupSuffixCount[$mainGroup]++;
                            $suffix = $groupSuffixCount[$mainGroup];

                            $newPlot = StructurePlot::create([
                                'structure_id'            => $id_structure,
                                'parent_id'               => $plot['parent_id'] ?? 0,
                                'id_structure'            => $plot['id_structure'] ?? null,
                                'position_code_structure' => $plot['position_code_structure'] ?? null,
                                'suffix'                  => $suffix,
                                'group'                   => $group,
                            ]);

                            $seenIdStructures[] = $newPlot->id_structure;
                        }
                    }

                    $existingPlots
                        ->whereNotIn('id_structure', $seenIdStructures)
                        ->each
                        ->delete();
                }

                $logMessage = implode('; ', $logMessages);


                $now = Carbon::now()->format('Y-m-d');

                if ($hasChanges) {
                    StructureHistories::create([
                        'structure_id'              => $structure->id,
                        'revision_no'               => $this->structureHistories->where('structure_id', $structure->id)->max('revision_no') + 1,
                        'valid_date'                => $request->valid_date ? $this->parseDateUTC($request->valid_date) : $now,
                        'updated_date'              => $request->updated_date ? $this->parseDateUTC($request->updated_date) : $now,
                        'authorized_date'           => $request->authorized_date ? $this->parseDateUTC($request->authorized_date) : $now,
                        'approval_date'             => $request->approval_date ? $this->parseDateUTC($request->approval_date) : $now,
                        'acknowledged_date'         => $request->acknowledged_date ?  $this->parseDateUTC($request->acknowledged_date) : $now,
                        'created_date'              => $request->created_date ? $this->parseDateUTC($request->created_date) : $now,
                        'distribution_date'         => $request->distribution_date ? $this->parseDateUTC($request->distribution_date) : $now,
                        'withdrawal_date'           => $request->withdrawal_date ? $this->parseDateUTC($request->withdrawal_date) : $now,
                        'logs'                      => $logMessage,
                    ]);
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
            $this->setLog('error', 'Error store data structure = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data structure = ' . $exception->getLine());
            $this->setLog('error', 'Error store data structure = ' . $exception->getFile());
            $this->setLog('error', 'Error store data structure = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateBulkStructure(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data structure ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            foreach ($request->bulk_edit as $item) {
                $structure = $this->structureRaw->firstWhere('id', $item['id']);

                if ($structure) {

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
                        $oldValue = $structure->$field;
                        $newValue = $request->$field ?? ($field === 'parent_id' ? 0 : null);

                        if ($newValue != $oldValue) {
                            $hasChanges = true;

                            $logMessages[] = "$label has been changed from '$oldValue' to '$newValue'";
                        }
                    }

                    $logMessage = implode('; ', $logMessages);

                    $structure->update([
                        'department_id'   => (int) $item['department_id'] ?? null,
                        'job_code_id'     => (int) $item['job_code_id'] ?? null,
                        'parent_id'       => (int) $item['parent_id'] ?? null,
                        'name'            => $item['name'],
                        'quota'           => (int) $item['quota'],
                        'structure_type'  => $item['structure_type'],
                    ]);

                    if ($item['structurePlots'] && is_array($item['structurePlots'])) {
                        $groupSuffixCount = [];

                        $existingPlots = StructurePlot::where('structure_id', $item->id)->get();
                        $existingByIdStructure = $existingPlots->keyBy('id_structure');

                        $seenIdStructures = [];

                        foreach ($item['structurePlots'] as $plot) {
                            $group = $plot['group'] ?? null;
                            $mainGroup = $group ? $group[0] : null;

                            if (!isset($groupSuffixCount[$mainGroup])) {
                                $lastSuffix = StructurePlot::where('structure_id', $item->id)
                                    ->whereRaw('LEFT(`group`, 1) = ?', [$mainGroup])
                                    ->max('suffix');
                                $groupSuffixCount[$mainGroup] = $lastSuffix ? $lastSuffix : 0;
                            }

                            $existing = $existingByIdStructure->get($plot['id_structure'] ?? null);

                            if ($existing) {
                                $changes = [
                                    'parent_id'               => $plot['parent_id'] ?? 0,
                                    'id_structure'            => $plot['id_structure'] ?? null,
                                    'position_code_structure' => $plot['position_code_structure'] ?? null,
                                    'group'                   => $plot['group'] ?? null,
                                ];

                                if (collect($changes)->diffAssoc($existing->only(array_keys($changes)))->isNotEmpty()) {
                                    $existing->update($changes);
                                }

                                $seenIdStructures[] = $existing->id_structure;
                            } else {
                                $groupSuffixCount[$mainGroup]++;
                                $suffix = $groupSuffixCount[$mainGroup];

                                $newPlot = StructurePlot::create([
                                    'structure_id'            => $item->id,
                                    'parent_id'               => $plot['parent_id'] ?? 0,
                                    'id_structure'            => $plot['id_structure'] ?? null,
                                    'position_code_structure' => $plot['position_code_structure'] ?? null,
                                    'suffix'                  => $suffix,
                                    'group'                   => $group,
                                ]);

                                $seenIdStructures[] = $newPlot->id_structure;
                            }
                        }

                        $existingPlots
                            ->whereNotIn('id_structure', $seenIdStructures)
                            ->each
                            ->delete();
                    }

                    if ($hasChanges) {
                        StructureHistories::create([
                            'structure_id'              => (int) $structure->id ?? null,
                            'revision_no'               => $this->structureHistories->where('structure_id', $structure->id)->max('revision_no') + 1 ?? 0,
                            'valid_date'                => $request->valid_date ? $this->parseDateUTC($request->valid_date) : null,
                            'updated_date'              => $request->updated_date ? $this->parseDateUTC($request->updated_date) : null,
                            'authorized_date'           => $request->authorized_date ? $this->parseDateUTC($request->authorized_date) : null,
                            'approval_date'             => $request->approval_date ? $this->parseDateUTC($request->approval_date) : null,
                            'acknowledged_date'         => $request->acknowledged_date ?  $this->parseDateUTC($request->acknowledged_date) : null,
                            'created_date'              => $request->created_date ? $this->parseDateUTC($request->created_date) : null,
                            'distribution_date'         => $request->distribution_date ? $this->parseDateUTC($request->distribution_date) : null,
                            'withdrawal_date'           => $request->withdrawal_date ? $this->parseDateUTC($request->withdrawal_date) : null,
                            'logs'                      => $logMessage,
                        ]);
                    }
                } else {
                    DB::rollBack();
                    return false;
                }
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

    public function getDataStructure($id_structure = NULL)
    {
        if (!empty($id_structure)) {
            $structure = $this->structure
                ->addSelect([
                    'totalAssignedEmployee' => UserPlot::selectRaw('COUNT(*)')
                        ->where('status', 1)
                        ->whereIn('structure_plot_id', function ($sub) {
                            $sub->select('id')
                                ->from('structure_plots')
                                ->whereColumn('structure_plots.structure_id', 'structures.id');
                        })
                ])
                ->findOrFail($id_structure);

            $structure->structurePlot->flatMap(function ($plot) {
                return $plot->userPlot;
            })->transform(function ($item) {
                $item->uuid = $item->user->uuid ?? null;
                $item->employee_name = $item->user->name ?? null;
                return $item;
            });


            $structure->employee_number = '';
            $structure->superior = $structure->parent->name ?? 'None';
            $structure->company_name = $structure->department->company->name ?? 'None';
            $structure->hasChildren = $structure->children()->exists();
        } else {
            $structure = $this->structure;
        }

        return $structure;
    }

    public function getDataStructurePlot($id_structure_plot = NULL)
    {
        if (!empty($id_structure_plot)) {
            $structurePlot = $this->structurePlot->firstWhere('id', $id_structure_plot);
        } else {
            $structurePlot = $this->structurePlot->get();
        }

        return $structurePlot;
    }

    public function getDataStructurePlotPagination($request)
    {
        $currentPage = (int) $request->current_page ? $request->current_page : 1;
        $perPage = (int) $request->per_page ? $request->per_page : 5;

        $query = $this->structurePlot;

        if ($request->globalFilter) {
            $query = $query->where('name', 'LIKE', "%$request->globalFilter%");
        }

        $query = $query->orderBy('id');

        $totalCount = $query->count();

        $structurePlot = $query
            ->skip(($currentPage - 1) * $perPage)
            ->take($perPage)
            ->get();

        return [
            'data'       => $structurePlot,
            'totalCount' => $totalCount,
        ];
    }


    public function getDataStructureByDepartment($request)
    {
        $currentPage = (int) $request->current_page ? $request->current_page : 1;
        $perPage = (int) $request->per_page ? $request->per_page : 5;

        $query = $this->structure;
        if ($request->id_department) {
            $query = $query->where('department_id', $request->id_department);
        }

        $query = $query
            ->addSelect([
                'totalAssignedEmployee' => UserPlot::selectRaw('COUNT(*)')
                    ->where('status', 1)
                    ->whereIn('structure_plot_id', function ($sub) {
                        $sub->select('id')
                            ->from('structure_plots')
                            ->whereColumn('structure_plots.structure_id', 'structures.id');
                    })
            ])->where(function ($query) use ($request) {
                if ($request->globalFilter) {
                    $query->where('name', 'LIKE', "%$request->globalFilter%");
                }
            })
            ->orderBy('id');

        $totalCount = $query->count();


        $structure = $query
            ->skip(($currentPage - 1) * $perPage)
            ->take($perPage)
            ->get();

        return [
            'data'        => $structure,
            'totalCount'  => $totalCount
        ];
    }

    // for hierarchy chart purpose (dormamu chart)
    public function getDataAllStructureHierarchy(Request $request, $id)
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
                'desc' => $data->structurePlot()
                    ->with(['userPlot' => function ($q) {
                        $q->where('status', 1);
                    }])
                    ->orderByRaw('LEFT(`group`, 1)')
                    ->get()
                    ->flatMap(function ($structurePlot) {
                        return $structurePlot->userPlot->map(function ($userPlot) use ($structurePlot) {
                            return [
                                'id'                      => $userPlot->id,
                                'pic'                     => $userPlot->user->name ?? "",
                                'employee_number'         => $userPlot->user->userEmployeeNumber()->where('status', 1)->first()->employee_number ?? "",
                                'job_code_id'             => $structurePlot->job_code_id,
                                'id_structure'            => $structurePlot->id_structure,
                                'position_code_structure' => $structurePlot->position_code_structure ?? "",
                                'group'                   => $structurePlot->group,
                                'id_staff'                => $userPlot->id_staff,
                                'employee_type'           => $userPlot->employee_type ?? "",
                                'assign_date'             => $userPlot->assign_date ?? "",
                            ];
                        });
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


        $structure = $this->structure->where(function ($query) use ($id) {
            if ($id) {
                $query->where('department_id', $id);
            }
        })->where('parent_id', 0)
            ->with(['children.jobCode', 'children.structurePlot.userPlot', 'jobCode'])
            ->get()
            ->map(fn($data) => mapChildren($data, true, $request));


        return $structure;
    }

    // hierarchy chart (unicef/ somkid)
    public function getStructureHierarchyUser($id)
    {
        $userPlot =  $this->userPlot
            ->where('user_id', $id)
            ->where('status', 1)
            ->first();

        if (!$userPlot) {
            return [];
        }

        $userPlot =
            [
                'id'         => $userPlot->id,
                'parentId'   => $userPlot->parent_id,
                'person'     =>
                [
                    'id'            =>  $userPlot->id,
                    'avatar'        => '/images/pepe-tired.jpg',
                    'department'    =>  $userPlot->structurePlot->structure->department->name . " (" . $userPlot->structurePlot->structure->department->code . ") " .    $userPlot->structurePlot->structure->group,
                    'name'          =>  $userPlot->user->name ?? "-",
                    'employee_type' =>  $userPlot->user->employee_type ?? "-",
                    'address'       =>  $userPlot->user->address ?? "-",
                    'date_of_birth' => $this->parseDateMdY($userPlot->user->date_of_birth) ?? "-",
                    'age'           => Carbon::parse($userPlot->user->date_of_birth)->age,
                    'color'         => $this->getColor($userPlot->jobCode->level),
                    'title'         =>  $userPlot->user->userEmployeeNumber()->where('status', 1)->first()->employee_number ?? "",
                    'position'      =>  $userPlot->structurePlot->structure->name,
                    'position_code' =>  $userPlot->structurePlot->structure->position_code_structure,
                    'totalReports'  => count($userPlot->children()->get()),
                ],
                'children'  => [],
                'hasChild'  => count($userPlot->children()->get()) > 0 ? true : false,
                'hasParent' => count($userPlot->parent()->get()) > 0 ? true : false,
            ];



        return  $userPlot;
    }

    public function getStructureHierarchyParent($parent_id)
    {
        $result = [];
        $userPlot = $this->userPlot
            ->where('id', $parent_id)
            ->first();
        if (!$userPlot) {
            return [];
        }

        $result =
            [
                'id'         => $userPlot->id,
                'parentId'   => $userPlot->parent_id,
                'person'     =>
                [
                    'id'            =>  $userPlot->id,
                    'avatar'        => '/images/pepe-tired.jpg',
                    'department'    =>  $userPlot->structurePlot->structure->department->name . " (" . $userPlot->structurePlot->structure->department->code . ") " .    $userPlot->structurePlot->structure->group,
                    'name'          =>  $userPlot->user->name ?? "-",
                    'employee_type' =>  $userPlot->user->employee_type ?? "-",
                    'address'       =>  $userPlot->user->address ?? "-",
                    'date_of_birth' => $this->parseDateMdY($userPlot->user->date_of_birth) ?? "-",
                    'age'           => Carbon::parse($userPlot->user->date_of_birth)->age,
                    'color'         => $this->getColor($userPlot->jobCode->level),
                    'title'         =>  $userPlot->user->userEmployeeNumber()->where('status', 1)->first()->employee_number ?? "",
                    'position'      =>  $userPlot->structurePlot->structure->name,
                    'position_code' =>  $userPlot->structurePlot->structure->position_code_structure,
                    'totalReports'  => count($userPlot->children()->get()),
                ],
                'hasChild'  => count($userPlot->children()->get()) > 0 ? true : false,
                'hasParent' => count($userPlot->parent()->get()) > 0 ? true : false,
            ];

        foreach ($userPlot->children as $data) {
            $result['children'][] = [
                'id'         => $data->id,
                'parentId'   => $data->parent_id,
                'person'     =>
                [
                    'id'            => $data->id,
                    'avatar'        => '/images/pepe-tired.jpg',
                    'department'    =>  $data->structurePlot->structure->department ? $data->structurePlot->structure->department->name . " (" . $data->structurePlot->structure->department->code . ") " .  $data->structurePlot->structure->group : "-",
                    'name'          => $data->user->name ?? "-",
                    'employee_type' => $data->user->employee_type ?? "-",
                    'address'       => $data->user->address ?? "-",
                    'date_of_birth' => $this->parseDateMdY($data->user->date_of_birth) ?? "-",
                    'age'           => Carbon::parse($data->user->date_of_birth)->age,
                    'color'         => $this->getColor($data->jobCode->level),
                    'title'         => $data->user->userEmployeeNumber()->where('status', 1)->first()->employee_number ?? "",
                    'position'      => $data->structurePlot->structure->name,
                    'position_code' => $data->structurePlot->structure->position_code_structure,
                    'totalReports'  => count($data->children()->get()),
                ],
                'children'  => [],
                'hasChild'  => count($data->children()->get()) > 0 ? true : false,
                'hasParent' => count($data->parent()->get()) > 0 ? true : false,
            ];
        }

        return $result;
    }

    public function getStructureHierarchyChildren($id)
    {
        $userPlot =  $this->userPlot
            ->where('id', $id)
            ->first();

        if (!$userPlot) {
            return [];
        }

        $result = [];
        foreach ($userPlot->children as $data) {
            $result[] = [
                'id'         => $data->id,
                'parentId'   => $data->parent_id,
                'person'     =>
                [
                    'id'            => $data->id,
                    'avatar'        => '/images/pepe-tired.jpg',
                    'department'    =>  $data->structurePlot->structure->department ? $data->structurePlot->structure->department->name . " (" . $data->structurePlot->structure->department->code . ") " .  $data->structurePlot->structure->group : "-",
                    'name'          => $data->user->name ?? "-",
                    'employee_type' => $data->user->employee_type ?? "-",
                    'address'       => $data->user->address ?? "-",
                    'date_of_birth' => $this->parseDateMdY($data->user->date_of_birth) ?? "-",
                    'age'           => Carbon::parse($data->user->date_of_birth)->age,
                    'color'         => $this->getColor($data->jobCode->level),
                    'title'         => $data->user->userEmployeeNumber()->where('status', 1)->first()->employee_number ?? "",
                    'position'      => $data->structurePlot->structure->name,
                    'position_code' => $data->structurePlot->structure->position_code_structure,
                    'totalReports'  => count($data->children()->get()),
                ],
                'children'  => [],
                'hasChild'  => count($data->children()->get()) > 0 ? true : false,
                'hasParent' => count($data->parent()->get()) > 0 ? true : false,
            ];
        }


        return $result;
    }

    public function destroyStructure(Request $request, $id_structure)
    {
        try {
            $this->setLog('info', 'Request delete data structure ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            $structure = Structure::find($id_structure);

            // Check if structure exists & has children, move children to structure parent_id
            if ($structure->children()->exist()) {
                foreach ($structure->children as $user) {
                    $user->update([
                        'parent_id' => $structure->parent_id,
                    ]);
                }
            }

            if ($request->isAllowed) {
                $structure->delete();
            } else {
                DB::rollBack();
                return false;
            }


            $this->setLog('info', 'deleted  structure data' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error delete structure = ' . $exception->getMessage());
            $this->setLog('error', 'Error delete structure = ' . $exception->getLine());
            $this->setLog('error', 'Error delete structure = ' . $exception->getFile());
            $this->setLog('error', 'Error delete structure = ' . $exception->getTraceAsString());
            return null;
        }
    }
}
