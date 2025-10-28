<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable;


    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $dates      = ['deleted_at'];
    protected $guarded = [];
    protected $fillable   = [
        'uuid',
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
        'password',
        'status_account',
        'contract_start_date',
        'contract_end_date',
        'resign_date',
        'contract_status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function userEmployeeNumber()
    {
        return $this->hasMany(UserEmployeeNumber::class);
    }

    public function userServiceYear()
    {
        return $this->hasOne(UserServiceYear::class);
    }

    public function userPlot()
    {
        return $this->hasMany(UserJobCode::class)->with('jobCode');
    }

    public function certificates()
    {
        return $this->belongstoMany(Certificate::class, 'user_certificate')->withPivot('description', 'expiration_date');
    }

    public function training()
    {
        return $this->hasMany(Training::class, 'trainee_id', 'id');
    }

    public function getSuperiorName()
    {
        $jobCode = $this->userPlot()->where('status', 1)->first();
        if (!$jobCode) {
            return null;
        }

        $parentJobCode = $jobCode->parent->user->name ?? "-";

        return $parentJobCode;
    }


    public function getTotalMemberStructure()
    {

        if (!$this->userPlot()->where('status', 1)->first()) {
            return null;
        }

        $query = UserPlot::where('structure_plot_id', $this->userPlot()->where('status', 1)->first()->structure_plot_id)->where('status', 1)->count();

        return $query ?? 0;
    }

    public function getTotalSubordinate()
    {

        if (!$this->userPlot()->where('status', 1)->first()) {
            return null;
        }

        $query = $this->userPlot()->where('status', 1)->first()->children()->count();

        return $query ?? 0;
    }

    public function getDetailIKWTrained()
    {
        $jobCodeRecord = $this->userPlot()->where('status', 1)->first();
        if (!$jobCodeRecord) {
            return [];
        }

        $trainingResults = Training::where('trainee_id', $this->id)
            ->select('ikw_revision_id', 'assessment_result')
            ->get();

        $table = [];
        foreach ($trainingResults as $training) {
            $ikwId = $training->ikwRevision->ikw_id ?? null;

            if (isset($ikwId)) {
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
        }

        $resultArray = array_values($table);

        return $resultArray;
    }

    public function getDetailRKI($request)
    {
        $globalFilter =  $request->globalFilter ? strtolower($request->globalFilter) : "";
        $filterCompetent = filter_var($request->filter['competent'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $filterNonCompetent = filter_var($request->filter['nonCompetent'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $start = (int)$request->start ?  (int)$request->start : 0;
        $jobCodeRecord = $this->userPlot()->where('status', 1)->first();

        if (!$jobCodeRecord) {
            return [];
        }

        $trainingResults = Training::where('trainee_id', $this->id)
            ->pluck('assessment_result', 'ikw_revision_id')
            ->all();

        $rkiResults = RKI::where('structure_id', $jobCodeRecord->structure_id)
            ->get();

        if (!$rkiResults) {
            return [];
        }

        $resultArray = [];
        foreach ($rkiResults as $rki) {
            if (!$rki->ikw) continue;

            $result = $trainingResults[$rki->ikw->ikwRevision()
                ->where('revision_no', function ($query) {
                    $query->selectRaw('MAX(revision_no)');
                })->first()->id] ?? "-";

            $resultArray[] = [
                'ikw_id'                  => $rki->ikw->id ?? "",
                'ikw_code'                => $rki->ikw->code ?? "",
                'ikw_name'                => $rki->ikw->name ?? "",
                'ikw_page'                => $rki->ikw->total_page ?? "",
                'result'                  => $result,
            ];
        }

        $filtered = array_filter($resultArray, function ($item) use ($globalFilter, $filterCompetent, $filterNonCompetent) {
            $match = true;

            if ($globalFilter) {
                $match = $match && (
                    str_contains(strtolower($item['ikw_code']), $globalFilter) ||
                    str_contains(strtolower($item['ikw_name']), $globalFilter)
                );
            }

            if ($filterCompetent) {
                $match = $match && $item['result'] === 'K';
            }

            if ($filterNonCompetent) {
                $match = $match && $item['result'] !== 'K';
            }

            return $match;
        });

        $filtered = array_values($filtered);


        return [
            'data'                    => array_slice($filtered, (($start - 1) * 10), 10),
            'totalIKWCompetent'       => collect($resultArray)->where('result', 'K')->count(),
            'totalIKW'                => count($resultArray),
            'totalCount'              => ceil(count($filtered) / 10)
        ];
    }
}
