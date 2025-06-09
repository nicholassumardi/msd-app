<?php

namespace App\Services;

use App\Jobs\ImportJobFamilyJob;
use App\Models\JobCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobCodeServices extends BaseServices
{
    protected $jobCode;

    public function __construct()
    {
        $this->jobCode = JobCode::with('category');
    }

    public function importJobFamilyExcel(Request $request)
    {
        $file = $request->file;
        $filepath = $file->storeAs('temp', $request->file->getClientOriginalName(), 'public');
        $query =  ImportJobFamilyJob::dispatch($filepath);

        if ($query) {
            return true;
        }

        return false;
    }

    public function storeJobCode(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data Job code ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            $fullCode = $request->org_level . '' . $request->job_family . '' . $request->code;

            JobCode::create([
                'category_id'   => $request->category_id,
                'org_level'     => $request->org_level,
                'job_family'    => $request->job_family,
                'code'          => $request->code,
                'full_code'     => $fullCode,
                'position'      => $request->position,
            ]);

            $this->setLog('info', 'New data Job code' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data Job code = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data Job code = ' . $exception->getLine());
            $this->setLog('error', 'Error store data Job code = ' . $exception->getFile());
            $this->setLog('error', 'Error store data Job code = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateJobCode(Request $request, $id_job_code)
    {
        try {
            $this->setLog('info', 'Request update data Job code ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $jobCode = JobCode::find($id_job_code);

            if ($jobCode) {
                $jobCode->update([
                    'category_id'   => $request->category_id,
                    'position'      => $request->position,
                    'code'          => $request->code
                ]);
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'updated data Job code ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error update data Job code = ' . $exception->getMessage());
            $this->setLog('error', 'Error update data Job code = ' . $exception->getLine());
            $this->setLog('error', 'Error update data Job code = ' . $exception->getFile());
            $this->setLog('error', 'Error update data Job code = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function getDataJobCode($id_job_code = NULL)
    {

        if (!empty($id_job_code)) {
            $jobCode =  $this->jobCode->firstWhere('id', $id_job_code);
        } else {
            $jobCode =  $this->jobCode->get();
            $jobCode = $jobCode->map(function ($data) {
                return [
                    'id'              => $data->id,
                    'position'        => $data->position,
                    'code'            => $data->full_code,
                    'category_id'     => $data->category_id,
                    'category_name'   => $data->category ? $data->category->name : "",
                ];
            });
        }

        return $jobCode;
    }


    public function getDataJobCodePagination(Request $request)
    {
        $start = (int) $request->start;
        $size = (int)$request->size;
        $filters = json_decode($request->filters, true) ?? [];
        $sorting = json_decode($request->sorting, true) ?? [];
        $globalFilter = $request->globalFilter ?? '';

        $queryData = $this->jobCode->where(function ($query) use ($request, $filters, $globalFilter) {
            if ($request->id_department) {
                $query->where('department_id', $request->id_department);
            }

            if ($globalFilter) {
                $query->where(function ($query) use ($globalFilter) {
                    $query->where('position', 'LIKE',  "%$globalFilter%")
                        ->orWhere('full_code', 'LIKE',  "%$globalFilter%");
                })->orWhereHas('category', function ($query) use ($globalFilter) {
                    $query->where('name', 'LIKE',  "%$globalFilter%");
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
                'id'              => $data->id,
                'position'        => $data->position,
                'code'            => $data->full_code,
                'category_id'     => $data->category_id,
                'category_name'   => $data->category ? $data->category->name : "",
            ];
        });

        return $queryData;
    }

    public function destroyJobCode(Request $request, $id_job_code)
    {
        try {
            $this->setLog('info', 'Request delete data Job code ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();
            $jobCode = JobCode::find($id_job_code);

            if ($jobCode) {
                $jobCode->delete();
            } else {
                DB::rollBack();
                return false;
            }


            $this->setLog('info', 'deleted  JobCode data' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error delete JobCode = ' . $exception->getMessage());
            $this->setLog('error', 'Error delete JobCode = ' . $exception->getLine());
            $this->setLog('error', 'Error delete JobCode = ' . $exception->getFile());
            $this->setLog('error', 'Error delete JobCode = ' . $exception->getTraceAsString());
            return null;
        }
    }
}
