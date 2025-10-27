<?php

namespace App\Services;

use App\Models\AgeClassification;
use App\Models\Company;
use App\Models\EmploymentDurationClassification;
use App\Models\GeneralClassification;
use App\Models\User;
use Carbon\Carbon;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Support\Facades\Log;

class BaseServices
{
    public function setLog($type, $messages)
    {
        if ($type == 'info') {
            Log::info($messages);
        } else {
            Log::error($messages);
        }
    }

    public function getDataDashboard($company_id)
    {
        $query = User::where('company_id', $company_id);
        $response = [
            'totalUser'       => User::count(),
            'totalUserPerCompany' => (clone $query)->count(),
            'activeUser'      => (clone $query)->where('status', 1)->count(),
            'inActiveUser'    => (clone $query)->where('status', 0)->count(),
            'totalUserMale'   => (clone $query)->where('gender', 'male')->count(),
            'totalUserFemale' => (clone $query)->where('gender', 'female')->count(),
            'dataUserCompany' => Company::withCount('user')->get(),
            'totalCompany'    => Company::count(),

        ];

        return $response;
    }

    public function ageClassification($date_of_birth)
    {
        $age = Carbon::parse($date_of_birth)->age;
        $classifierRules = AgeClassification::orderBy('rule', 'DESC')->get();

        foreach ($classifierRules as $rule) {
            if ($age >= $rule['rule']) {
                return $rule['label'];
            }
        }

        return "No Data yet";
    }

    public function generalClassification($date_of_birth)
    {
        $year = Carbon::parse($date_of_birth)->year;
        $classifierRules = GeneralClassification::orderBy('rule', 'DESC')->get();

        foreach ($classifierRules as $rule) {
            if ($year >= $rule['rule']) {
                return $rule['label'];
            }
        }

        return "No Data yet";
    }

    public function workingDurationClassification($join_date)
    {
        $workingDuration = Carbon::parse($join_date)->diffInYears(Carbon::now());
        $classifierRules = EmploymentDurationClassification::orderBy('rule', 'DESC')->get();

        foreach ($classifierRules as $rule) {
            if ($workingDuration >= $rule['rule']) {
                return $rule['label'];
            }
        }

        return "No Data yet";
    }

    public function getServiceYearFull($join_date)
    {
        $difference =  Carbon::parse($join_date)->diff(Carbon::now());
        $years = $difference->y;
        $month = $difference->m;
        $date = $difference->d;


        return "{$years} Tahun, {$month} Bulan, {$date} Hari";
    }

    public function getServiceYear($join_date)
    {
        $difference =  Carbon::parse($join_date)->diff(Carbon::now());
        $years = $difference->y;


        return "{$years}";
    }

    public function getRealAgeInMonth($age)
    {
        $difference =  Carbon::parse($age)->diff(Carbon::now());
        $years = $difference->y;
        $month = $difference->m;

        return "{$years} Tahun, {$month} Bulan";
    }

    public function getUserByUUID($search)
    {
        $uuid = User::firstWhere('uuid', $search)->id ?? null;

        return $uuid;
    }

    public function getDaydiff($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);


        $days =  $start->diffInDaysFiltered(function (Carbon $date) {
            return !$date->isWeekend();
        }, $end);

        return $days;
    }

    public function parseDateTime($arg)
    {
        return (new DateTimeImmutable($arg))->setTimezone(new DateTimeZone("UTC"))->format('Y-m-d\TH:i:s');
    }

    public function parseDateUTC($arg)
    {
        return $arg
            ? Carbon::parse($arg)->setTimezone('Asia/Jakarta')->format('Y-m-d')
            : null;
    }

    protected function parseDateYMD($value)
    {
        if (empty($value)) return null;

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function parseDateMdY($arg)
    {
        $date = Carbon::parse($arg)->format('M d, Y');

        return $date;
    }

    public function skipWeekend(int $businessDays): Carbon
    {
        $d = Carbon::now()->startOfDay();
        $daysToGo = $businessDays;
        while ($daysToGo > 0) {
            $d = $d->subDay();

            if (! in_array($d->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
                $daysToGo--;
            }
        }
        return $d;
    }

    public function getColor($arg)
    {
        if (!$arg) return "#8FC9A4";
        $colors = [
            "#8FD0C2", // 1
            "#8FC3DE", // 2
            "#AC99D6", // 3
            "#E797B8", // 4
            "#F8D472", // 5
            "#F6AE7B", // 6
            "#EF8B86", // 7
            "#DB7A91", // 8
        ];


        return $colors[$arg] ?? "#8FC9A4";
    }
}
