<?php

namespace App\Services;

use App\Models\IKW;
use App\Models\IKWRevision;
use App\Models\RKI;
use App\Models\Training;
use App\Models\User;
use App\Models\UserPlot;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EvaluationServices extends BaseServices
{
    protected $training;
    protected $user;
    protected $rki;
    protected $ikwRevision;
    protected $ikw;
    protected $userPlot;

    public function __construct()
    {

        $this->training = Training::with('trainee.department', 'trainer', 'assessor', 'ikwRevision.ikw');
        $this->user = User::with('company', 'department', 'userEmployeeNumber', 'userServiceYear', 'userPlot', 'certificates', 'training');
        $this->rki = RKI::with('ikw');
        $this->ikwRevision = IKWRevision::with('ikw', 'ikwMeeting', 'ikwPosition');
        $this->ikw = IKW::with('department', 'jobTaskDetail', 'ikwRevision');
        $this->userPlot = UserPlot::with('user', 'structurePlot.structure.jobCode', 'structurePlot.structure');
    }

    public function getDataEvaluation(Request $request)
    {
        $evaluation = $this->training->get();


        return $evaluation;
    }

    public function getDataEvaluationPagination(Request $request)
    {
        $start = (int) $request->start ? (int) $request->start : 0;
        $size = (int) $request->size ? (int) $request->size : 6;
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
                })->orWhereHas('userPlot', function ($query) use ($globalFilter) {
                    $query->whereHas('structurePlot', function ($query) use ($globalFilter) {
                        $query->whereHas('structure', function ($query) use ($globalFilter) {
                            $query->whereHas('jobCode', function ($query) use ($globalFilter) {
                                $query->where('full_code', 'LIKE',  "%{$globalFilter}%");
                            });
                        });
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
            $role_position_code = $data->userPlot->where('status', 1)->first() ? $data->userPlot()->where('status', 1)->first()->structurePlot->structure->jobCode->full_code ?? "" . ' - ' . $data->userPlot()->where('status', 1)->first()->structurePlot->position_code_structure ?? "" : '';
            $roleCode =  $data->userPlot()->where('status', 1)->first()->structurePlot->structure->jobCode->full_code ?? "";
            $group = $data->userPlot()->where('status', 1)->latest()->first()->structurePlot->group ?? "";
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
        $structureCode =  $employee->userPlot()->where('status', 1)->first()?->structurePlot->structure_id;


        $revisionIDs = $this->rki
            ->where('structure_id', $structureCode)
            ->whereHas('ikw.ikwRevision')
            ->with(['ikw.ikwRevision' => fn($q) => $q->orderByDesc('revision_no')])
            ->get()
            ->flatMap(fn($rki) => optional($rki->ikw->ikwRevision->sortByDesc('revision_no')->first())->id ? [$rki->ikw->ikwRevision->sortByDesc('revision_no')->first()->id] : []);

        if ($revisionIDs) {
            $result = DB::table('trainings')
                ->where('trainee_id', $id)
                ->whereIn('ikw_revision_id', $revisionIDs)
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
        $jobCodeRecord = $employee->userPlot()->where('status', 1)->first();
        if (!$jobCodeRecord) {
            return [];
        }

        $structureID =  $employee->userPlot()->where('status', 1)->first()?->structurePlot->structure_id;

        $ikwIds =  $this->rki->where('structure_id', $structureID)
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

        $departmentStats = (clone $baseQuery)
            ->join('users', 'users.id', '=', 'trainings.trainee_id')
            ->join('departments', 'departments.id', '=', 'users.department_id')
            ->selectRaw('
                departments.name as department_name,
                COUNT(CASE WHEN assessment_result = "K" THEN 1 END) as total_competent,
                COUNT(CASE WHEN assessment_result = "BK" THEN 1 END) as total_non_competent,
                SUM(assessment_result IN ("BK", "K")) as total_assessment,
                ROUND(
                    (COUNT(CASE WHEN assessment_result = "K" THEN 1 END) / 
                    NULLIF(SUM(assessment_result IN ("BK", "K")), 0)) * 100, 2
                ) as percent_competent,
                ROUND(
                    (COUNT(CASE WHEN assessment_result = "BK" THEN 1 END) / 
                    NULLIF(SUM(assessment_result IN ("BK", "K")), 0)) * 100, 2
                ) as percent_non_competent
            ')
            ->groupBy('departments.name')
            ->get();


        // Single query for all main metrics
        $mainMetrics = (clone $baseQuery)->selectRaw('
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
            ) as cancel_assessment,
             AVG(
                CASE
                    WHEN assessment_plan_date IS NOT NULL 
                    AND assessment_result != "-" 
                    THEN assessment_duration
                    ELSE NULL
                END
            ) AS avg_assessment_duration
        ')->first();

        $durationVsResult = (clone $baseQuery)
            ->whereNotNull('assessment_plan_date')
            ->selectRaw('
                assessment_result,
                COUNT(*) as total_participants,
                AVG(assessment_duration) as avg_duration,
                MIN(assessment_duration) as min_duration,
                MAX(assessment_duration) as max_duration
            ')
            ->whereIn('assessment_result', ['BK', 'K'])
            ->groupBy('assessment_result')
            ->get();

        // Single query for monthly trends
        $monthlyTrends = (clone $baseQuery)
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
        $trainingEfficiency = (clone $baseQuery)->selectRaw('
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
            'avg_time_assessment'          => (int)$mainMetrics->avg_assessment_duration,
            'department_stats'             => $departmentStats,
            'duration_result'              => $durationVsResult,
            'monthly_trends'               => $monthlyTrends,
            'training_efficiency' => [
                'effective'  => (int)$trainingEfficiency->effective_trainings,
                'total'      => (int)$trainingEfficiency->total_trainings,
                'percentage' => $trainingEfficiency->total_trainings > 0
                    ? round(($trainingEfficiency->effective_trainings / $trainingEfficiency->total_trainings) * 100, 2)
                    : 0
            ],

        ];
    }


    // get IKW Data Competent by trainer
    public function getEmployeeTrainingHistory($request)
    {
        $start = (int) $request->start ? (int) $request->start :  0;
        $startNonCompetent = (int) $request->startNonCompetent ? (int) $request->startNonCompetent :  0;
        $size = (int) $request->size ? (int) $request->size :  5;
        // 1) find user early
        $user = $this->user->firstWhere('uuid', $request->uuid);
        if (! $user) {
            return ['data' => null, 'totalCount' => 0];
        }

        // 2) base query for this trainee (re-usable)
        $base = $this->training->newQuery()
            ->whereHas('trainee', function ($q) use ($request) {
                $q->where('uuid', $request->uuid);
            });

        // 3) compute business-days threshold for "more than 7 business days ago"
        $thresholdDate = $this->skipWeekend(7); // returns Carbon instance (date)

        // 4) counts (cloning builder to avoid modifying base)
        $totalTraining     = (clone $base)->count();
        // on-progress = no assessment_result AND assessment_realisation_date <= threshold (older than 7 business days)
        $onProgressCount   = (clone $base)
            ->whereNull('assessment_result')
            ->whereDate('assessment_realisation_date', '<=', $thresholdDate->toDateString())
            ->count();
        $competentCount    = (clone $base)->where('assessment_result', 'K')->count();
        $nonCompetentCount = (clone $base)->where('assessment_result', 'BK')->count();
        $remedialCount     = (clone $base)->where('assessment_result', 'RK')->count();
        $otherCount = (clone $base)
            ->whereNotIn('assessment_result', ['K', 'BK', 'RK'])
            ->count();

        // 5) fetch the training rows for display (with eager loads, pagination if needed)
        $trainingCompetent = (clone $base)
            ->where('assessment_result', 'K')
            ->skip(($start - 1) * $size)
            ->take($size)
            ->with(['trainee']) // eager load relationships you will display - add others if necessary
            ->orderByDesc('assessment_realisation_date')
            ->get();

        $anotherTrainingResult = (clone $base)
            ->where('assessment_result', "!=", 'K')
            ->skip(($startNonCompetent  - 1) * $size)
            ->take($size)
            ->with(['trainee']) // eager load relationships you will display - add others if necessary
            ->orderByDesc('assessment_realisation_date')
            ->get();

        // 6) BI metrics: percentages and success rate
        $pct = function ($n) use ($totalTraining) {
            return $totalTraining ? round($n / $totalTraining * 100, 2) : 0.0;
        };

        $percentCompetent    = $pct($competentCount);
        $percentNonCompetent = $pct($nonCompetentCount);
        $percentRemedial     = $pct($remedialCount);
        $percentOnProgress   = $pct($onProgressCount);
        $percentOther        = $pct($otherCount);

        // Success rate: I present two sensible definitions and compute both
        $assessedCount = $totalTraining - ($onProgressCount + $otherCount); // trainings that are "assessed" (not still on-progress by our rule)
        $successRateByAssessed = $assessedCount ? round($competentCount / $assessedCount * 100, 2) : null;
        $successRateOverall    = $totalTraining ? round($competentCount / $totalTraining * 100, 2) : null;

        // 7) Extra BI: trend in last 30 days and average time-to-assessment (if fields exist)
        $last30Trainings = (clone $base)
            ->whereDate('assessment_realisation_date', '>=', Carbon::now()->subDays(30)->toDateString())
            ->get();

        // daily counts grouped by date (collection grouping to keep simple)
        $dailyTrend = $last30Trainings->groupBy(function ($t) {
            $d = $t->assessment_realisation_date ? Carbon::parse($t->assessment_realisation_date)->format('Y-m-d') : 'unknown';
            return $d;
        })->map->count()->toArray();

        // average time (days) from assessment_realisation_date -> assessment_plan_date (if assessment_plan_date exists)
        $avgTimeToAssess = null;


        // 8) Most Frequent Trainer 
        $mostFrequentTrainer = (clone $base)
            ->where('trainer_id', "!=", NULL)
            ->select('trainer_id', DB::raw("count(*) as count"))
            ->groupBy('trainer_id')
            ->orderByDesc('count')
            ->first();


        $table = $this->training->getModel()->getTable();

        if (Schema::hasColumn($table, 'assessment_plan_date')) {
            $times = (clone $this->training)
                ->whereNotNull('assessment_plan_date')
                ->get()
                ->map(function ($t) {
                    try {
                        $start = $t->assessment_realisation_date ? Carbon::parse($t->assessment_realisation_date) : null;
                        $end   = $t->assessment_plan_date ? Carbon::parse($t->assessment_plan_date) : null;
                        return ($start && $end) ? $end->diffInDays($start) : null;
                    } catch (\Throwable $e) {
                        return null;
                    }
                })
                ->filter();

            $avgTimeToAssess = $times->count() ? round($times->avg(), 2) : null;
        }

        $result = [
            'total_training'            => $totalTraining,
            'on_progress'               => $onProgressCount,
            'competent'                 => $competentCount,
            'non_competent'             => $nonCompetentCount,
            'remedial'                  => $remedialCount,
            'other'                     => $otherCount,
            'mostFrequentTrainer'       => $mostFrequentTrainer->trainer?->name ?? "Unknown",
            'percent' => [
                'competent'   => $percentCompetent,
                'non'         => $percentNonCompetent,
                'remedial'    => $percentRemedial,
                'on_progress' => $percentOnProgress,
                'other'       => $percentOther,
            ],
            'success_rate' => [
                'by_assessed' => $successRateByAssessed, // competent / assessed (excluding on-progress)
                'overall'     => $successRateOverall,    // competent / total
            ],
            'avg_time_to_assess_days' => $avgTimeToAssess,
            'trend_last_30_days'      => $dailyTrend,   // simple daily counts map
            'training_competent'      => $trainingCompetent,
            'another_training_result' => $anotherTrainingResult,
            'employee'                => $user,
        ];

        return [
            'data'       => $result,
            'totalCount' => count($result),
        ];
    }

    // get IKW Data Competent by trainer
    public function getEligibleIKWByTrainer($request)
    {
        $trainer = $this->user->firstWhere('uuid', $request->trainer_id);
        $structure_id =  $trainer?->userPlot() ?  $trainer->userPlot()->where('status', 1)->first()?->structurePlot->structure_id : null;

        $rki = $this->rki->where('structure_id',  $structure_id)->pluck('ikw_id');


        $ikwRevision = $this->ikwRevision->whereIn('ikw_id', $rki)
            ->orderBy('ikw_id')
            ->orderByDesc('revision_no')
            ->get()
            ->unique('ikw_id')
            ->pluck('ikw_id');

        // check if trainer is competent for the latest IKW
        $data = $this->ikw->whereHas('ikwRevision', function ($query) use ($request, $ikwRevision) {
            $query->whereHas('training', function ($query) use ($request) {
                $query->where('assessment_result', 'K')
                    ->whereHas('trainee', function ($query) use ($request) {
                        $query->where('uuid', $request->trainer_id);
                    });
            })->whereIn('id', $ikwRevision);
        })
            ->get();



        $dataTraining = $this->training
            ->whereHas('trainer', function ($query) use ($request) {
                $query->where('uuid', $request->trainer_id);
            })
            ->get();


        $data = $data->map(function ($data) {
            return [
                'id'                           => $data->id,
                'department_id'                => $data->department_id,
                'department_name'              => $data->department->name ?? '',
                'code'                         => $data->code,
                'name'                         => $data->name,
                'revision_no'                  => $data->ikwRevision->max('revision_no') ?? '',
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


        return [
            'dataIKW'      => $data,
            'dataTraining' => $dataTraining
        ];
    }

    // get Employee Data that has Competent in IKW
    public function getEligibleEmployeeByIKW($request)
    {
        $start = (int) $request->start ? (int) $request->start :  0;
        $size = (int) $request->size ? (int) $request->size :  5;

        $ikwRevision = $this->ikwRevision->where('ikw_id', $request->ikw_id)
            ->orderByDesc('revision_no')
            ->first();

        $training = $this->training->where('ikw_revision_id', $ikwRevision->id)
            ->where('assessment_result', 'K')
            ->with('trainee')
            ->get();


        $query = $training->map(function ($data) {
            return [
                'id'                          => $data->trainee->id ?? '',
                'uuid'                        => $data->trainee->uuid ?? null,
                'no_training'                 => $data->no_training ?? '',
                'name'                        => $data->trainee->name ?? '',
                'trainer_name'                => $data->trainer->name ?? 'Unknown Data',
                'assessor_name'               => $data->assessor->name ?? 'Unknown Data',
                'employee_type'               => $data->trainee->employee_type ?? '',
                'department'                  => $data->trainee->department->code ?? '',
                'assessment_realisation_date' =>  date('Y-m-d', strtotime($data->assessment_realisation_date)) ?? '',
                'assessment_result'           => $data->assessment_result ?? '',
            ];
        });


        $query = $query->sortBy('department')->values();

        $totalCount = $query->count();
        $result = $query->slice(($start - 1) * $size, $size)->values();

        return [
            'totalCount'  => $totalCount,
            'data'        => $result
        ];
    }

    // get Trainee Data that hasn't Competent in IKW by trainer
    public function getTraineeByTrainerIKW($request)
    {
        $start = (int) $request->start ? $request->start :  0;
        $size = (int) $request->size ? $request->size :  5;
        $filters = json_decode($request->filters, true) ?? [];
        $sorting = json_decode($request->sorting, true) ?? [];
        $globalFilter = $request->globalFilter ?? '';

        $query = $this->userPlot
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
            ->flatMap(function ($userPlot) use ($request) {
                return $userPlot->rki()
                    ->where('ikw_id', $request->ikw_id)
                    ->get()
                    ->map(function ($rki) use ($userPlot) {
                        return [
                            'structure_id'        => $rki->structure_id,
                            'ikw_id'              => $rki->ikw_id,
                            'employee_name'       => $userPlot->user->name ?? '',
                            'employee_type'       => $userPlot->user->employee_type ?? '',
                            'employee_department' => $userPlot->user->department->code ?? '',
                            'ikw_name'            => $rki->ikw->name ?? '',
                            'ikw_code'            => $rki->ikw->code ?? '',
                            'training_time'       => $rki->training_time,
                            'assessment_result'   => 0,
                        ];
                    });
            });

        $totalCount = $query->count();
        $result = $query->slice(($start - 1) * $size, $size)->values();

        return [
            'totalCount'  => $totalCount,
            'data'        => $result
        ];
    }

    // get Trainee Data that hasn't Competent in IKW
    public function getIKWToTrainForTrainee($request)
    {
        $start = (int) $request->start ? $request->start :  0;
        $size = (int) $request->size ? $request->size :  5;
        $filters = json_decode($request->filters, true) ?? [];
        $sorting = json_decode($request->sorting, true) ?? [];
        $globalFilter = $request->globalFilter ?? '';

        $query = $this->userPlot
            ->whereHas('user', function ($query) use ($request) {
                if ($request->department_id) {
                    $query->where('department_id', $request->department_id);
                }
                $query->whereHas('training', function ($query) {
                    $query->where(function ($query) {
                        $query->where('assessment_result', 'BK')
                            ->whereHas('ikwRevision')
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
            ->flatMap(function ($userPlot) {
                return $userPlot->rki()->get()->map(function ($rki) use ($userPlot) {

                    $training = $userPlot->user->training->first(function ($training) use ($rki) {
                        return $training->ikwRevision && $training->ikwRevision->ikw_id == $rki->ikw_id;
                    });

                    return [
                        'structure_id'   => $rki->structure_id,
                        'ikw_id'              => $rki->ikw_id,
                        'employee_name'       => $userPlot->user->name ?? '',
                        'employee_type'       => $userPlot->user->employee_type ?? '',
                        'employee_department' => $userPlot->user->department->code ?? '',
                        'ikw_name'            => $rki->ikw->name ?? '',
                        'ikw_code'            => $rki->ikw->code ?? '',
                        'training_time'       => $rki->training_time,
                        'status_assessment'   => $training ? 1 : 0
                    ];
                });
            });

        $totalCount = $query->count();
        $result = $query
            ->slice(($start - 1) * $size, $size)
            ->values();


        return [
            'totalCount'  => $totalCount,
            'data'        => $result
        ];
    }

    // get Trainer's Subordinate
    public function getTrainerSubordinate($request)
    {
        $result = [];
        $trainer = $this->user->firstWhere('uuid', $request->trainer_id);
        $trainer_structure =  $trainer?->userPlot() ?  $trainer->userPlot()->where('status', 1)->with('structurePlot.structure')->first() : null;

        if ($trainer_structure) {
            foreach ($trainer_structure->children as $child) {
                $result[] = [
                    'uuid'  => $child->user->uuid ?? '',
                    'name'  => $child->user->name ?? '',
                    'department' => $child->user->department->code ?? '',
                ];
            }
        }

        return $result;
    }
}
