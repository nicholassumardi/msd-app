<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;


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

    public function userJobCode()
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
        $jobCode = $this->userJobCode()->where('status', 1)->first();
        if (!$jobCode) {
            return null;
        }

        $parentJobCode = $jobCode->parent->user->name ?? "-";

        return $parentJobCode;
    }


    public function getTotalMemberStructure()
    {

        if (!$this->userJobCode()->where('status', 1)->first()) {
            return null;
        }

        $query = UserJobCode::where('user_structure_mapping_id', $this->userJobCode()->where('status', 1)->first()->user_structure_mapping_id)->where('status', 1)->count();

        return $query;
    }

    public function getDetailIKWTrained()
    {
        $jobCodeRecord = $this->userJobCode()->where('status', 1)->first();
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

    public function getDetailRKI()
    {
        $jobCodeRecord = $this->userJobCode()->where('status', 1)->first();
        if (!$jobCodeRecord) {
            return [];
        }

        $trainingResults = Training::where('trainee_id', $this->id)
            ->pluck('assessment_result', 'ikw_revision_id')
            ->all();

        $rkiResults = RKI::where('user_structure_mapping_id', $jobCodeRecord->user_structure_mapping_id)
            ->get();

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

        return $resultArray;
    }
}
