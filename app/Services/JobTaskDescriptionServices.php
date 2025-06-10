<?php

namespace App\Services;

use App\Jobs\ImportJobTaskDescJob;
use App\Models\JobDescription;
use App\Models\JobTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobTaskDescriptionServices extends BaseServices
{
    protected $jobDescription;

    public function __construct()
    {
        $this->jobDescription =  JobDescription::with('jobCode');
    }

    public function importJobTaskDescExcel(Request $request, $cacheKey)
    {
        $file = $request->file;
        $filepath = $file->storeAs('temp', $request->file->getClientOriginalName(), 'public');
        $query =  ImportJobTaskDescJob::dispatch($filepath, $cacheKey);

        if ($query) {
            return true;
        }

        return false;
    }


    public function storeJobDescription(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data Job Description ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            JobDescription::create([
                'job_code_id'    => $request->job_code_id,
                'code'           => $request->code,
                'description'    => $request->description
            ]);

            $this->setLog('info', 'New data Job Description' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data Job Description = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data Job Description = ' . $exception->getLine());
            $this->setLog('error', 'Error store data Job Description = ' . $exception->getFile());
            $this->setLog('error', 'Error store data Job Description = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateJobDescription(Request $request, $id_job_description)
    {
        try {
            $this->setLog('info', 'Request update data Job Description ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $JobDescription = JobDescription::find($id_job_description);

            if ($JobDescription) {
                $JobDescription->update([
                    'job_task_id' => $request->job_task_id,
                    'code'        => $request->code,
                    'description' => $request->description
                ]);
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'updated data Job Description ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error update data Job Description = ' . $exception->getMessage());
            $this->setLog('error', 'Error update data Job Description = ' . $exception->getLine());
            $this->setLog('error', 'Error update data Job Description = ' . $exception->getFile());
            $this->setLog('error', 'Error update data Job Description = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function getDataJobDescription($id_job_description = NULL)
    {

        if (!empty($id_job_description)) {
            $JobDescription = $this->jobDescription->firstWhere('id', $id_job_description);
        } else {
            $JobDescription = $this->jobDescription->get();
            $JobDescription = $JobDescription->map(function ($data) {
                return [
                    'id'              => $data->id,
                    'code'            => $data->code,
                    'description'     => $data->description,
                    'job_code_id'     => $data->job_code_id,
                    'job_code_code'   => $data->jobCode ? $data->jobCode->full_code : "",
                ];
            });
        }

        return $JobDescription;
    }

    public function destroyJobDescription(Request $request, $id_job_description)
    {
        try {
            $this->setLog('info', 'Request delete data Job Description ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();
            $JobDescription = JobDescription::find($id_job_description);

            if ($JobDescription) {
                $JobDescription->delete();
            } else {
                DB::rollBack();
                return false;
            }


            $this->setLog('info', 'deleted  JobDescription data' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error delete JobDescription = ' . $exception->getMessage());
            $this->setLog('error', 'Error delete JobDescription = ' . $exception->getLine());
            $this->setLog('error', 'Error delete JobDescription = ' . $exception->getFile());
            $this->setLog('error', 'Error delete JobDescription = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function storeJobTask(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data Job Task ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            JobTask::create([
                'job_code_id'    => $request->job_code_id,
                'description'    => $request->description
            ]);

            $this->setLog('info', 'New data Job Task' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data Job Task = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data Job Task = ' . $exception->getLine());
            $this->setLog('error', 'Error store data Job Task = ' . $exception->getFile());
            $this->setLog('error', 'Error store data Job Task = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateJobTask(Request $request, $id_job_task)
    {
        try {
            $this->setLog('info', 'Request update data Job Task ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $JobTask = JobTask::find($id_job_task);

            if ($JobTask) {
                $JobTask->update([
                    'job_code_id' => $request->job_code_id,
                    'code'        => $request->code
                ]);
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'updated data Job Task ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error update data Job Task = ' . $exception->getMessage());
            $this->setLog('error', 'Error update data Job Task = ' . $exception->getLine());
            $this->setLog('error', 'Error update data Job Task = ' . $exception->getFile());
            $this->setLog('error', 'Error update data Job Task = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function getDataJobTask($id_job_task = NULL)
    {

        if (!empty($id_job_task)) {
            $JobTask = JobTask::with('jobCode')->where('id', $id_job_task)->first();
        } else {
            $JobTask = JobTask::with('jobCode')->get();
            $JobTask = $JobTask->map(function ($data) {
                return [
                    'id'              => $data->id,
                    'job_code_id'     => $data->job_code_id,
                    'job_code_code'   => $data->jobCode ? $data->jobCode->full_code : "",
                    'description'     => $data->description,
                ];
            });
        }

        return $JobTask;
    }

    public function destroyJobTask(Request $request, $id_job_task)
    {
        try {
            $this->setLog('info', 'Request delete data Job Task ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();
            $JobTask = JobTask::find($id_job_task);

            if ($JobTask) {
                $JobTask->delete();
            } else {
                DB::rollBack();
                return false;
            }


            $this->setLog('info', 'deleted  JobTask data' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error delete JobTask = ' . $exception->getMessage());
            $this->setLog('error', 'Error delete JobTask = ' . $exception->getLine());
            $this->setLog('error', 'Error delete JobTask = ' . $exception->getFile());
            $this->setLog('error', 'Error delete JobTask = ' . $exception->getTraceAsString());
            return null;
        }
    }
}
