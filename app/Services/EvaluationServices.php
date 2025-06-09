<?php

namespace App\Services;

use App\Models\IKW;
use App\Models\IKWRevision;
use App\Models\RKI;
use App\Models\Training;
use App\Models\User;
use App\Models\UserJobCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EvaluationServices extends BaseServices
{
    protected $training;
    protected $user;
    protected $rki;
    protected $ikwRevision;
    protected $ikw;
    protected $userJobCode;

    public function __construct()
    {

        $this->training = Training::with('trainee', 'trainer', 'assessor', 'ikwRevision');
        $this->user = User::with('company', 'department', 'userEmployeeNumber', 'userServiceYear', 'userJobCode', 'certificates', 'training');
        $this->rki = RKI::with('ikw');
        $this->ikwRevision = IKWRevision::with('ikw', 'ikwMeeting', 'ikwPosition');
        $this->ikw = IKW::with('department', 'jobTask', 'ikwRevision');
        $this->userJobCode = UserJobCode::with('user', 'jobCode', 'userStructureMapping');
    }

    public function getDataEvaluation(Request $request)
    {
        $evaluation = $this->training->get();


        return $evaluation;
    }

    public function getDataEvaluationPagination(Request $request)
    {
        $start = (int) $request->start;
        $size = (int) $request->size ?? 6;
        $filters = json_decode($request->filters, true) ?? [];
        $sorting = json_decode($request->sorting, true) ?? [];
        $globalFilter = $request->globalFilter ?? '';

        $user = $this->user->where(function ($query) use ($filters, $globalFilter) {
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
                    $query->where('name', 'LIKE',  "%{$globalFilter}%")
                        ->orWhere('code', 'LIKE',  "%{$globalFilter}%");
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
            ->orderBy('id', 'ASC')
            ->get();


        $user = $user->map(function ($data) {
            $nip = $data->userEmployeeNumber()->where('status', 1)->first()->employee_number ?? '';
            $role_position_code = $data->userJobCode->where('status', 1)->first() ? $data->userJobCode()->where('status', 1)->first()->jobCode->full_code ?? "" . ' - ' . $data->userJobCode()->where('status', 1)->first()->position_code_structure ?? "" : '';
            $roleCode =  $data->userJobCode()->where('status', 1)->first()->jobCode->full_code ?? "";
            $group = $data->userJobCode()->where('status', 1)->latest()->first()->group ?? "";
            return [
                'id'                  => $data->id,
                'name'                => $data->name ?? '',
                'nip'                 => $nip,
                'department'          => $data->department->code ?? "",
                'roleCode'            => $roleCode,
                'group'               => $group,
                'identity_card'       => $data->identity_card ?? '',
                'role_position_code'  => $role_position_code,
            ];
        });

        return $user;
    }

    public function getTrainingPlanning($id)
    {
        $result = DB::table('trainings')
            ->where('trainee_id', $id)
            ->select(
                // Total planned trainings
                DB::raw('COALESCE(COUNT(training_plan_date), 0) as planning'),
                // Trainings with a realisation date
                DB::raw('COALESCE(SUM(CASE WHEN training_realisation_date IS NOT NULL THEN 1 ELSE 0 END), 0) as realisation'),
                // Cancelled: realised trainings with working days difference > 7 (excluding weekends)
                DB::raw("COALESCE(SUM(
                CASE 
                    WHEN training_realisation_date IS NULL 
                         AND (DATEDIFF(CURDATE(), training_plan_date) 
                              - FLOOR(DATEDIFF(CURDATE(), training_plan_date) / 7) * 2) > 7 
                    THEN 1 ELSE 0 
                END
            ), 0) as cancel"),
                // On progress: not realised trainings where working days from plan to today <= 7 (excluding weekends)
                DB::raw("COALESCE(SUM(
                CASE 
                    WHEN training_realisation_date IS NULL 
                         AND training_plan_date <= CURDATE() 
                         AND (DATEDIFF(CURDATE(), training_plan_date)
                              - FLOOR(DATEDIFF(CURDATE(), training_plan_date) / 7) * 2) <= 7 
                    THEN 1 ELSE 0 
                END
            ), 0) as on_progress"),
                // Total planned trainings
                DB::raw('COALESCE(COUNT(assessment_plan_date), 0) as planning_assesment'),
                // Trainings with a realisation date
                DB::raw('COALESCE(SUM(CASE WHEN assessment_realisation_date IS NOT NULL THEN 1 ELSE 0 END), 0) as realisation_assesment'),
                // Cancelled: realised trainings with working days difference > 7 (excluding weekends)
                DB::raw("COALESCE(SUM(
                  CASE 
                       WHEN assessment_realisation_date IS NULL 
                           AND (DATEDIFF(CURDATE(), assessment_plan_date) 
                               - FLOOR(DATEDIFF(CURDATE(), assessment_plan_date) / 7) * 2) > 7 
                       THEN 1 ELSE 0 
                   END
               ), 0) as cancel_assesment"),
                // On progress: not realised trainings where working days from plan to today <= 7 (excluding weekends)
                DB::raw("COALESCE(SUM(
                   CASE 
                       WHEN assessment_realisation_date IS NULL 
                           AND assessment_plan_date <= CURDATE() 
                           AND (DATEDIFF(CURDATE(), assessment_plan_date)
                               - FLOOR(DATEDIFF(CURDATE(), assessment_plan_date) / 7) * 2) <= 7 
                       THEN 1 ELSE 0 
                   END
               ), 0) as on_progress_assesment"),
                DB::raw("COALESCE(SUM(CASE WHEN assessment_realisation_date IS NOT NULL AND assessment_result = 'K' THEN 1 ELSE 0 END), 0) as competent_assessment"),
                DB::raw("COALESCE(SUM(CASE WHEN assessment_realisation_date IS NOT NULL AND assessment_result = 'BK' THEN 1 ELSE 0 END), 0) as not_competent_assessment")


            )
            ->first();

        return [
            'planning'                 => $result->planning,
            'realisation'              => $result->realisation,
            'cancel'                   => $result->cancel,
            'on_progress'              => $result->on_progress,
            'planning_assesment'       => $result->planning_assesment,
            'realisation_assesment'    => $result->realisation_assesment,
            'cancel_assesment'         => $result->cancel_assesment,
            'on_progress_assesment'    => $result->on_progress_assesment,
            'competent_assessment'     => $result->competent_assessment ?? 0,
            'not_competent_assessment' => $result->not_competent_assessment ?? 0,
        ];
    }


    public function getTrainingPlanningRKI($id)
    {
        $employee = $this->user->firstWhere('id', $id);
        $structureCode =  $employee->userJobCode()->where('status', 1)->first()?->jobCode->full_code . '-' . $employee->userJobCode()->where('status', 1)->first()?->position_code_structure;


        $dataRki = $this->rki->where('position_job_code', $structureCode)->pluck('ikw_id');

        if ($dataRki) {
            $dataIkwRevision = $this->ikwRevision->whereIn('ikw_id', $dataRki)->pluck('id');
            $result = DB::table('trainings')
                ->where('trainee_id', $id)
                ->whereIn('ikw_revision_id', $dataIkwRevision)
                ->select(
                    // Total planned trainings
                    DB::raw('COALESCE(COUNT(training_plan_date), 0) as planning'),
                    // Trainings with a realisation date
                    DB::raw('COALESCE(SUM(CASE WHEN training_realisation_date IS NOT NULL THEN 1 ELSE 0 END), 0) as realisation'),
                    // Cancelled: realised trainings with working days difference > 7 (excluding weekends)
                    DB::raw("COALESCE(SUM(
                        CASE 
                            WHEN training_realisation_date IS NULL 
                                AND (DATEDIFF(CURDATE(), training_plan_date) 
                                    - FLOOR(DATEDIFF(CURDATE(), training_plan_date) / 7) * 2) > 7 
                            THEN 1 ELSE 0 
                        END
                    ), 0) as cancel"),
                    // On progress: not realised trainings where working days from plan to today <= 7 (excluding weekends)
                    DB::raw("COALESCE(SUM(
                        CASE 
                            WHEN training_realisation_date IS NULL 
                                AND training_plan_date <= CURDATE() 
                                AND (DATEDIFF(CURDATE(), training_plan_date)
                                    - FLOOR(DATEDIFF(CURDATE(), training_plan_date) / 7) * 2) <= 7 
                            THEN 1 ELSE 0 
                        END
                    ), 0) as on_progress"),
                    DB::raw('COALESCE(COUNT(assessment_plan_date), 0) as planning_assesment'),
                    // Trainings with a realisation date
                    DB::raw('COALESCE(SUM(CASE WHEN assessment_realisation_date IS NOT NULL THEN 1 ELSE 0 END), 0) as realisation_assesment'),
                    // Cancelled: realised trainings with working days difference > 7 (excluding weekends)
                    DB::raw("COALESCE(SUM(
                      CASE 
                          WHEN assessment_realisation_date IS NULL 
                              AND (DATEDIFF(CURDATE(), assessment_plan_date) 
                                  - FLOOR(DATEDIFF(CURDATE(), assessment_plan_date) / 7) * 2) > 7 
                          THEN 1 ELSE 0 
                      END
                  ), 0) as cancel_assesment"),
                    // On progress: not realised trainings where working days from plan to today <= 7 (excluding weekends)
                    DB::raw("COALESCE(SUM(
                    CASE 
                        WHEN assessment_realisation_date IS NULL 
                            AND assessment_plan_date <= CURDATE() 
                            AND (DATEDIFF(CURDATE(), assessment_plan_date)
                                - FLOOR(DATEDIFF(CURDATE(), assessment_plan_date) / 7) * 2) <= 7 
                        THEN 1 ELSE 0 
                    END
                    ), 0) as on_progress_assesment"),
                    DB::raw("COALESCE(SUM(CASE WHEN assessment_realisation_date IS NOT NULL AND assessment_result = 'K' THEN 1 ELSE 0 END), 0) as competent_assessment"),
                    DB::raw("COALESCE(SUM(CASE WHEN assessment_realisation_date IS NOT NULL AND assessment_result = 'BK' THEN 1 ELSE 0 END), 0) as not_competent_assessment")
                )
                ->first();
        }

        return [
            'planning'                 => $result->planning ?? 0,
            'realisation'              => $result->realisation ?? 0,
            'cancel'                   => $result->cancel ?? 0,
            'on_progress'              => $result->on_progress ?? 0,
            'planning_assesment'       => $result->planning_assesment ?? 0,
            'realisation_assesment'    => $result->realisation_assesment ?? 0,
            'cancel_assesment'         => $result->cancel_assesment ?? 0,
            'on_progress_assesment'    => $result->on_progress_assesment ?? 0,
            'competent_assessment'     => $result->competent_assessment ?? 0,
            'not_competent_assessment' => $result->not_competent_assessment ?? 0,
        ];
    }

    public function getDetailRKI($id)
    {
        $employee = $this->user->firstWhere('id', $id);
        $jobCodeRecord = $employee->userJobCode()->where('status', 1)->first();
        if (!$jobCodeRecord) {
            return [];
        }

        $positionCode =  $employee->userJobCode()->where('status', 1)->first()?->jobCode->full_code . '-' . $employee->userJobCode()->where('status', 1)->first()?->position_code_structure;

        $ikwIds =  $this->rki->where('position_job_code', $positionCode)
            ->pluck('ikw_id');
        if ($ikwIds->isEmpty()) {
            return [];
        }

        $revisions = $this->ikwRevision->whereIn('ikw_id', $ikwIds)
            ->select('id', 'ikw_id', 'revision_no')
            ->orderBy('revision_no')
            ->get();
        if ($revisions->isEmpty()) {
            return [];
        }

        $trainingResults = $this->training->where('trainee_id', $id)
            ->whereIn('ikw_revision_id', $revisions->pluck('id'))
            ->select('ikw_revision_id', 'assessment_result')
            ->get();

        $table = [];
        foreach ($trainingResults as $training) {
            $ikwId = $training->ikwRevision->ikw_id;

            if (isset($table[$ikwId])) {
                $table[$ikwId]['revisions'][] = [
                    'revision_no'        => $training->ikwRevision->revision_no
                ];
            } else {
                $table[$ikwId] = [
                    'ikw_name'           => $training->ikwRevision->ikw->name ?? "",
                    'ikw_code'           => $training->ikwRevision->ikw->code ?? "",
                    'revisions'          => [
                        ['revision_no'   => $training->ikwRevision->revision_no]
                    ],
                    'assessment_result'  => $training->assessment_result,
                ];
            }
        }

        $resultArray = array_values($table);

        return $resultArray;
    }

    public function getDataVisualization($request)
    {
        $baseQuery = $this->training->with(['trainee', 'ikwRevision.ikw'])
            ->whereNotNull('training_realisation_date')
            ->whereHas('trainee', function ($query) use ($request) {
                if ($request->department_id) {
                    $query->where('department_id', $request->department_id);
                }
                if ($request->gender) {
                    $query->where('gender', $request->gender);
                }
            })
            ->whereHas('ikwRevision.ikw', function ($query) use ($request) {
                if ($request->ikw_id) {
                    $query->where('id', $request->ikw_id);
                }
            });

        // Single query for all main metrics
        $mainMetrics = $baseQuery->selectRaw('
            COUNT(CASE WHEN assessment_result = "K" THEN 1 END) as total_competent,
            COUNT(CASE WHEN assessment_result = "BK" THEN 1 END) as total_non_competent,
            COUNT(CASE WHEN assessment_result = "RK" THEN 1 END) as total_remedial_competent,
            COUNT(CASE WHEN assessment_plan_date IS NOT NULL THEN 1 END) as total_assessment,
            SUM(
                CASE 
                    WHEN assessment_result NOT IN ("BK", "K", "RK") 
                    AND assessment_plan_date <= CURDATE() 
                    AND (DATEDIFF(CURDATE(), assessment_plan_date) - 
                        FLOOR(DATEDIFF(CURDATE(), assessment_plan_date) / 7) * 2) <= 7 
                    THEN 1 
                    ELSE 0 
                END
            ) as total_in_progress_assessment,
            SUM(
                CASE 
                    WHEN assessment_result NOT IN ("BK", "K", "RK") 
                    AND assessment_plan_date <= CURDATE() 
                    AND (DATEDIFF(CURDATE(), assessment_plan_date) - 
                        FLOOR(DATEDIFF(CURDATE(), assessment_plan_date) / 7) * 2) > 7 
                    THEN 1 
                    ELSE 0 
                END
            ) as cancel_assessment
        ')->first();

        // Single query for monthly trends
        $monthlyTrends = $baseQuery
            ->selectRaw('
                YEAR(assessment_realisation_date) as year,
                MONTH(assessment_realisation_date) as month,
                COUNT(*) as total_assessments,
                SUM(assessment_result = "K") as competent,
                SUM(assessment_result = "RK") as remedial,
                SUM(assessment_result = "BK") as non_competent,
                AVG(DATEDIFF(assessment_realisation_date, training_realisation_date)) as avg_days_to_assessment
            ')
            ->whereNotNull('assessment_realisation_date')
            ->groupByRaw('YEAR(assessment_realisation_date), MONTH(assessment_realisation_date)')
            ->orderByRaw('year ASC, month ASC')
            ->get();

        // Single query for training efficiency
        $trainingEfficiency = $baseQuery->selectRaw('
            SUM(assessment_result = "K" AND DATEDIFF(assessment_realisation_date, training_realisation_date) <= 30) as effective_trainings,
            COUNT(*) as total_trainings
        ')->first();

        return [
            // Main metrics
            'total_competent'              => (int)$mainMetrics->total_competent,
            'total_non_competent'          => (int)$mainMetrics->total_non_competent,
            'total_remedial_competent'     => (int)$mainMetrics->total_remedial_competent,
            'total_in_progress_assessment' => (int)$mainMetrics->total_in_progress_assessment,
            'total_assessment'             => (int)$mainMetrics->total_assessment,
            'cancel_assessment'            => (int)$mainMetrics->cancel_assessment,
            'monthly_trends'               => $monthlyTrends,
            'training_efficiency' => [
                'effective'  => (int)$trainingEfficiency->effective_trainings,
                'total'      => (int)$trainingEfficiency->total_trainings,
                'percentage' => $trainingEfficiency->total_trainings > 0
                    ? round(($trainingEfficiency->effective_trainings / $trainingEfficiency->total_trainings) * 100, 2)
                    : 0
            ]
        ];
    }

    public function getEligibleIKWByTrainer($request)
    {
        $trainer = $this->user->firstWhere('uuid', $request->trainer_id);
        $positionCode =  $trainer->userJobCode()->where('status', 1)->first()?->jobCode->full_code . '-' . $trainer->userJobCode()->where('status', 1)->first()?->position_code_structure;
        $rki = $this->rki->where('position_job_code',  $positionCode)->pluck('ikw_id');

        $ikwRevision = $this->ikwRevision->whereIn('ikw_id', $rki)
            ->orderBy('ikw_id')
            ->orderByDesc('revision_no')
            ->get()
            ->unique('ikw_id')
            ->pluck('id');

        // check if trainer is competent for the latest IKW
        $data = $this->ikw->whereHas('ikwRevision', function ($query) use ($request, $ikwRevision) {
            $query->whereHas('training', function ($query) use ($request) {
                $query->where('assessment_result', 'K')
                    ->whereHas('trainee', function ($query) use ($request) {
                        $query->where('uuid', $request->trainer_id);
                    });
            })->whereIn('id', $ikwRevision);
        })
            ->where('status_document', 'IKW FINISH')
            ->get();

        $data = $data->map(function ($data) {
            return [
                'id'                           => $data->id,
                'job_task_id'                  => $data->job_task_id,
                'department_id'                => $data->department_id,
                'code'                         => $data->code,
                'name'                         => $data->name,
                'total_page'                   => $data->total_page,
                'registration_date'            => $data->registration_date,
                'print_by_back_office_date'    => $data->print_by_back_office_date,
                'submit_to_department_date'    => $data->submit_to_department_date,
                'ikw_return_date'              => $data->ikw_return_date,
                'ikw_creation_duration'        => $data->ikw_creation_duration,
                'status_document'              => $data->status_document,
                'last_update_date'             => $data->last_update_date,
                'description'                  => $data->description,
            ];
        });


        return $data;
    }

    public function getTraineeByTrainerIKW($request)
    {
        $start = (int) $request->start ?? 0;
        $size = (int) $request->size ?? 5;
        $filters = json_decode($request->filters, true) ?? [];
        $sorting = json_decode($request->sorting, true) ?? [];
        $globalFilter = $request->globalFilter ?? '';

        $query = $this->userJobCode
            ->whereHas('user', function ($query) use ($request) {
                if ($request->department_id) {
                    $query->where('department_id', $request->department_id);
                }

                $query->whereHas('training', function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $query->where('assessment_result', 'BK')
                            ->whereHas('ikwRevision', function ($query) use ($request) {
                                if ($request->ikw_id) {
                                    $query->where('ikw_id', $request->ikw_id);
                                }
                            })->orWhere(function ($query) use ($request) {
                                $query->whereHas('ikwRevision', function ($query) use ($request) {
                                    if ($request->ikw_id) {
                                        $query->where('ikw_id', $request->ikw_id);
                                    }
                                    $query->whereRaw(
                                        'revision_no <> COALESCE((
                                                SELECT MAX(sub.revision_no)
                                                FROM ikw_revisions AS sub
                                                JOIN trainings AS t ON t.ikw_revision_id = sub.id
                                                WHERE sub.ikw_id = ikw_revisions.ikw_id AND t.assessment_result = ?
                                            ), 0)',
                                        ['K']
                                    );
                                });
                            });
                    });
                });
            })
            ->orderBy('position_code_structure', 'ASC')
            ->get()
            ->flatMap(function ($userJobCode) use ($request) {
                return $userJobCode->rki()
                    ->where('ikw_id', $request->ikw_id)
                    ->get()
                    ->map(function ($rki) use ($userJobCode) {

                        return [
                            'position_job_code'   => $rki->position_job_code,
                            'ikw_id'              => $rki->ikw_id,
                            'employee_name'       => $userJobCode->user->name ?? '',
                            'employee_type'       => $userJobCode->user->employee_type ?? '',
                            'employee_department' => $userJobCode->user->department->code ?? '',
                            'ikw_name'            => $rki->ikw->name ?? '',
                            'ikw_code'            => $rki->ikw->code ?? '',
                            'training_time'       => $rki->training_time,
                            'assessment_result'   => 0,
                        ];
                    });
            });

        $totalCount = $query->count();
        $result = $query->slice($start, $size)->values();

        return [
            'totalCount'  => $totalCount,
            'data'        => $result
        ];
    }

    public function getIKWToTrainForTrainee($request)
    {
        $start = (int) $request->start ?? 0;
        $size = (int) $request->size ?? 5;
        $filters = json_decode($request->filters, true) ?? [];
        $sorting = json_decode($request->sorting, true) ?? [];
        $globalFilter = $request->globalFilter ?? '';

        $query = $this->userJobCode
            ->whereHas('user', function ($query) use ($request) {
                if ($request->department_id) {
                    $query->where('department_id', $request->department_id);
                }
                $query->whereHas('training', function ($query) {
                    $query->where(function ($query) {
                        $query->where('assessment_result', 'BK')->whereHas('ikwRevision')
                            ->orWhere(function ($query) {
                                $query->whereHas('ikwRevision', function ($query) {
                                    $query->whereRaw(
                                        'revision_no <> COALESCE((
                                                SELECT MAX(sub.revision_no)
                                                FROM ikw_revisions AS sub
                                                JOIN trainings AS t ON t.ikw_revision_id = sub.id
                                                WHERE sub.ikw_id = ikw_revisions.ikw_id AND t.assessment_result = ?
                                            ), 0)',
                                        ['K']
                                    );
                                });
                            });
                    });
                });
            })
            ->orderBy('position_code_structure', 'ASC')
            ->get()
            ->flatMap(function ($userJobCode) {
                return $userJobCode->rki()->get()->map(function ($rki) use ($userJobCode) {

                    $training = $userJobCode->user->training->first(function ($training) use ($rki) {
                        return $training->ikwRevision && $training->ikwRevision->ikw_id == $rki->ikw_id;
                    });

                    return [
                        'position_job_code'   => $rki->position_job_code,
                        'ikw_id'              => $rki->ikw_id,
                        'employee_name'       => $userJobCode->user->name ?? '',
                        'employee_type'       => $userJobCode->user->employee_type ?? '',
                        'employee_department' => $userJobCode->user->department->code ?? '',
                        'ikw_name'            => $rki->ikw->name ?? '',
                        'ikw_code'            => $rki->ikw->code ?? '',
                        'training_time'       => $rki->training_time,
                        'status_assessment'   => $training ? 1 : 0
                    ];
                });
            });

        $totalCount = $query->count();
        $result = $query->slice($start, $size)
            ->values();

        return [
            'totalCount'  => $totalCount,
            'data'        => $result
        ];
    }
}
