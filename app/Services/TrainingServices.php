<?php

namespace App\Services;

use App\Jobs\ExportTrainingJob;
use App\Jobs\ImportTrainingJob;
use App\Models\IKW;
use App\Models\IKWRevision;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainingServices extends BaseServices
{
    protected $training;
    protected $ikw;
    protected $ikwRevision;
    public function __construct()
    {
        $this->training = Training::with('trainee', 'trainer', 'assessor', 'ikwRevision');
        $this->ikw = IKW::with('ikwRevision.training');
        $this->ikwRevision = IKWRevision::with('ikw');
    }

    public function importTrainingExcel(Request $request, $cacheKey)
    {
        $file = $request->file;
        $filepath = $file->storeAs('temp', $request->file->getClientOriginalName(), 'public');
        $query =  ImportTrainingJob::dispatch($filepath, $cacheKey);

        if ($query) {
            return true;
        }

        return false;
    }

    public function exportDataTrainingExcel(Request $request, $cachekey)
    {
        $trainings = json_decode($request->id_trainings, true) ?? [];
        $training =  $this->training;

        if ($trainings) {
            $training->whereIn('id', array_keys($trainings));
        }

        $training = $training
            ->orderBy('id', 'ASC')
            ->get()
            ->map(function ($training, $key) {
                $nip_trainee = $training->trainee ? $training->trainee->userEmployeeNumber()->where('status', 1)->first()->employee_number : '';
                $nip_trainer = $training->trainer ? $training->trainer->userEmployeeNumber()->where('status', 1)->first()->employee_number : '';
                $nip_assessor = $training->assessor ? $training->assessor->userEmployeeNumber()->where('status', 1)->first()->employee_number : '';
                $ikw_name =  $training->ikwRevision->ikw->code ?? '';
                $nip_ikw_trainee = $nip_trainee . '/' . $ikw_name;
                $role_position_code_trainee =  $training->assessor ? $training->assessor->userJobCode()->where('status', 1)->first()->jobCode->full_code . ' - ' . $training->assessor->userJobCode()->where('status', 1)->first()->position_code_structure : '';
                return [
                    'no'   => $key + 1,
                    'no_training'                    => $training->no_training,
                    'nip_ikw_trainee'                => $nip_ikw_trainee,
                    'nip_trainee'                    => $nip_trainee,
                    'trainee_identity_card'          => $training->trainee->identity_card ?? '',
                    'trainee_name'                   => $training->trainee->name ?? '',
                    'trainee_department'             => $training->trainee->department->name ?? '',
                    'role_position_code_trainee'     => $role_position_code_trainee,
                    'ikw_name'                       => $ikw_name,
                    'ikw_revision'                   => $training->ikwRevision->revision_no ?? '',
                    'ikw_module_no'                  => $training->ikwRevision->ikw->module_no ?? '',
                    'nip_trainer'                    => $nip_trainer,
                    'trainer_identity_card'          => $training->trainer->identity_card ?? '',
                    'trainer_name'                   => $training->trainer->name ?? '',
                    'assessor_name'                  => $training->assessor->name ?? '',
                    'assessor_identity_card'         => $training->assessor->identity_card ?? '',
                    'training_plan_date'             => date('d/m/y', strtotime($training->training_plan_date)),
                    'training_realisation_date'      => date('d/m/y', strtotime($training->training_realisation_date)),
                    'training_duration'              => $training->training_duration,
                    'ticket_return_date'             => $training->ticket_return_date,
                    'nip_assessor'                   => $nip_assessor,
                    'assessment_plan_date'           => date('d/m/y', strtotime($training->assessment_plan_date)),
                    'assessment_realisation_date'    => date('d/m/y', strtotime($training->assessment_realisastion_date)),
                    'assessment_duration'            => $training->assessment_duration,
                    'status_fa_print'                => $training->status_fa_print,
                    'assessment_result'              => $training->assessment_result,
                    'status'                         => $training->status == 1 ? 'DONE' : '',
                    'description'                    => $training->description,
                    'status_active'                  => $training->status_active == 1 ? 'ACTIVE' : 'NON ACTIVE',
                ];
            });

        $query = ExportTrainingJob::dispatch($cachekey, $training);

        if ($query) {
            return true;
        }

        return false;
    }

    public function storeTraining(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data Training ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            Training::create([
                'no_training'                    => (Training::max('no_training') ?? 0) + 1,
                'trainee_id'                     => $this->getUserByUUID($request->trainee_id) ?? NULL,
                'trainer_id'                     => $this->getUserByUUID($request->trainer_id) ?? NULL,
                'assessor_id'                    => $this->getUserByUUID($request->assessor_id) ?? NULL,
                'ikw_revision_id'                => $request->ikw_revision_id,
                'training_plan_date'             => date('Y-m-d', strtotime($request->training_plan_date)),
                'training_realisation_date'      => date('Y-m-d', strtotime($request->training_realisation_date)),
                'training_duration'              => $request->training_duration,
                'ticket_return_date'             => date('Y-m-d', strtotime($request->ticket_return_date)),
                'assessment_plan_date'           => date('Y-m-d', strtotime($request->assessment_plan_date)),
                'assessment_realisation_date'    => date('Y-m-d', strtotime($request->assessment_realisation_date)),
                'assessment_duration'            => $request->assessment_duration,
                'status_fa_print'                => $request->status_fa_print,
                'assessment_result'              => $request->assessment_result,
                'status'                         => $request->status,
                'description'                    => $request->description,
                'status_active'                  => $request->status_active,
            ]);

            $this->setLog('info', 'New data Training' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data Training = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data Training = ' . $exception->getLine());
            $this->setLog('error', 'Error store data Training = ' . $exception->getFile());
            $this->setLog('error', 'Error store data Training = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateTraining(Request $request, $id_training)
    {
        try {
            $this->setLog('info', 'Request update data Training ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $training = Training::find($id_training);
            $ikw_revision = $this->ikwRevision->where('ikw_id', $request->ikw_id)->orderBy('revision', 'DESC')->first();

            if ($training) {
                $training->update([
                    'no_training'                    => (Training::max('no_training') ?? 0) + 1,
                    'trainee_id'                     => $this->getUserByUUID($request->trainee_id) ?? NULL,
                    'trainer_id'                     => $this->getUserByUUID($request->trainer_id) ?? NULL,
                    'assessor_id'                    => $this->getUserByUUID($request->assessor_id) ?? NULL,
                    'ikw_revision_id'                => $ikw_revision->id,
                    'training_plan_date'             => date('Y-m-d', strtotime($request->training_plan_date)),
                    'training_realisation_date'      => date('Y-m-d', strtotime($request->training_realisation_date)),
                    'training_duration'              => $request->training_duration,
                    'ticket_return_date'             => date('Y-m-d', strtotime($request->ticket_return_date)),
                    'assessment_plan_date'           => date('Y-m-d', strtotime($request->assessment_plan_date)),
                    'assessment_realisation_date'    => date('Y-m-d', strtotime($request->assessment_realisation_date)),
                    'assessment_duration'            => $request->assessment_duration,
                    'status_fa_print'                => $request->status_fa_print,
                    'assessment_result'              => $request->assessment_result,
                    'status'                         => $request->status,
                    'description'                    => $request->description,
                    'status_active'                  => $request->status_active,
                ]);
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'updated data Training ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error update data Training = ' . $exception->getMessage());
            $this->setLog('error', 'Error update data Training = ' . $exception->getLine());
            $this->setLog('error', 'Error update data Training = ' . $exception->getFile());
            $this->setLog('error', 'Error update data Training = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function getDataTraining($id_training = NULL)
    {
        if (!empty($id_training)) {
            $training =  $this->training->firstWhere('id', $id_training);
            $training = [
                'no_training'                    => $training->no_training,
                'trainee_id'                     => $training->trainee->uuid ?? NULL,
                'trainer_id'                     => $training->trainer->uuid ?? NULL,
                'assessor_id'                    => $training->assessor->uuid ?? NULL,
                'ikw_revision_id'                => $training->ikw_revision_id,
                'training_plan_date'             => $training->training_plan_date,
                'training_realisation_date'      => $training->training_realisation_date,
                'training_duration'              => $training->training_duration,
                'ticket_return_date'             => $training->ticket_return_date,
                'assessment_plan_date'           => $training->assessment_plan_date,
                'assessment_realisation_date'    => $training->assessment_realisation_date,
                'assessment_duration'            => $training->assessment_duration,
                'status_fa_print'                => $training->status_fa_print,
                'assessment_result'              => $training->assessment_result,
                'status'                         => $training->status,
                'description'                    => $training->description,
                'status_active'                  => $training->status_active,
            ];
        } else {
            $training = $this->training->get();
            $training = $training->map(function ($data) {
                return [
                    'no_training'                    => $data->no_training,
                    'trainee_id'                     => $data->trainee_id,
                    'trainer_id'                     => $data->trainer_id,
                    'assessor_id'                    => $data->assessor_id,
                    'ikw_revision_id'                => $data->ikw_revision_id,
                    'training_plan_date'             => $data->training_plan_date,
                    'training_realisation_date'      => $data->training_realisation_date,
                    'training_duration'              => $data->training_duration,
                    'ticket_return_date'             => $data->ticket_return_date,
                    'assessment_plan_date'           => $data->assessment_plan_date,
                    'assessment_realisation_date'    => $data->assessment_realisation_date,
                    'assessment_duration'            => $data->assessment_duration,
                    'status_fa_print'                => $data->status_fa_print,
                    'assessment_result'              => $data->assessment_result,
                    'status'                         => $data->status,
                    'description'                    => $data->description,
                    'status_active'                  => $data->status_active,
                ];
            });
        }

        return $training;
    }

    public function getDataTrainingByUUID($uuid, $request)
    {
        $start = (int) $request->start ? ((int) $request->start - 1) : 0;
        $size = (int) $request->size ? (int) $request->size :  5;
        $globalFilter = $request->globalFilter ?? '';
        $training = [];
        $countData = null;

        if (!empty($uuid)) {
            $training = $this->ikw
                ->where(function ($query) use ($globalFilter) {
                    if ($globalFilter) {
                        $query->where('code', 'LIKE',  "%{$globalFilter}%")
                            ->orWhere('name', 'LIKE',  "%{$globalFilter}%");
                    }
                })
                ->whereHas(
                    'ikwRevision.training.trainee',
                    fn($query) =>
                    $query->where('uuid', $uuid)
                )
                ->with([
                    'ikwRevision' => function ($query) use ($uuid) {
                        $query->with(['training' => function ($query) use ($uuid) {
                            $query->whereHas('trainee', fn($q) => $q->where('uuid', $uuid));
                        }]);
                    }
                ]);

            $countData = ceil($training->count() / $size);

            $training = $training
                ->skip($start)
                ->take($size)
                ->get();
        }

        return [
            'data'       => $training,
            'totalCount' => $countData,
        ];
    }

    public function getDataTrainingPagination(Request $request)
    {
        $start = (int) $request->start ? (int) $request->start : 0;
        $size = (int)$request->size ? (int)$request->size :  6;
        $filters = json_decode($request->filters, true) ?? [];
        $sorting = json_decode($request->sorting, true) ?? [];
        $globalFilter = $request->globalFilter ?? '';

        $training = $this->training->where(function ($query) use ($filters, $globalFilter) {

            if ($globalFilter) {
                $query->where(function ($query) use ($globalFilter) {
                    $query->where('no_training', 'LIKE', "%{$globalFilter}%");
                })->orWhereHas('trainer', function ($query) use ($globalFilter) {
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
                })->orWhereHas('trainee', function ($query) use ($globalFilter) {
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
                })->orWhereHas('assessor', function ($query) use ($globalFilter) {
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
                });
            }

            foreach ($filters as $filter) {
                $query->where($filter['id'], $filter['value']);
            }
        });

        foreach ($sorting as $sort) {
            if (isset($sort['id'])) {
                $training->orderBy($sort['id'], $sort['desc'] ? 'DESC' : 'ASC');
            }
        }

        $training = $training
            ->skip($start)
            ->take($size)
            ->get();


        $training = $training->map(function ($data) {
            $nip_trainee = $data->trainee ? $data->trainee->userEmployeeNumber()->where('status', 1)->first()->employee_number : '';
            $nip_trainer = $data->trainer ? $data->trainer->userEmployeeNumber()->where('status', 1)->first()->employee_number : '';
            $nip_assessor = $data->assessor ? $data->assessor->userEmployeeNumber()->where('status', 1)->first()->employee_number : '';
            $ikw_name =  $data->ikwRevision->ikw->code ?? '';
            $nip_ikw_trainee = $nip_trainee . '/' . $ikw_name;
            $role_position_code_trainee =  $data->assessor ? $data->assessor->userJobCode()->where('status', 1)->first()->jobCode->full_code . ' - ' . $data->assessor->userJobCode()->where('status', 1)->first()->position_code_structure : '';

            return [
                'id'                             => $data->id,
                'no_training'                    => $data->no_training,
                'nip_ikw_trainee'                => $nip_ikw_trainee,
                'trainee_id'                     => $data->trainee_id,
                'trainer_id'                     => $data->trainer_id,
                'assessor_id'                    => $data->assessor_id,
                'trainee_name'                   => $data->trainee->name ?? '',
                'trainer_name'                   => $data->trainer->name ?? '',
                'assessor_name'                  => $data->assessor->name ?? '',
                'trainee_identity_card'          => $data->trainee->identity_card ?? '',
                'trainer_identity_card'          => $data->trainer->identity_card ?? '',
                'assessor_identity_card'         => $data->assessor->identity_card ?? '',
                'nip_trainee'                    => $nip_trainee,
                'nip_trainer'                    => $nip_trainer,
                'nip_assessor'                   => $nip_assessor,
                'trainee_department'             => $data->trainee->department->name ?? '',
                'trainer_department'             => $data->trainer->department->name ?? '',
                'assessor_department'            => $data->assessor->department->name ?? '',
                'role_position_code_trainee'     => $role_position_code_trainee,
                'ikw_revision_id'                => $data->ikw_revision_id,
                'ikw_name'                       => $ikw_name,
                'ikw_revision'                   => $data->ikwRevision->revision_no ?? '',
                'ikw_module_no'                  => $data->ikwRevision->ikw->module_no ?? '',
                'training_plan_date'             => date('d/m/y', strtotime($data->training_plan_date)),
                'training_realisation_date'      => date('d/m/y', strtotime($data->training_realisation_date)),
                'training_duration'              => $data->training_duration,
                'ticket_return_date'             => $data->ticket_return_date,
                'assessment_plan_date'           => date('d/m/y', strtotime($data->assessment_plan_date)),
                'assessment_realisation_date'    => date('d/m/y', strtotime($data->assessment_realisastion_date)),
                'assessment_duration'            => $data->assessment_duration,
                'status_fa_print'                => $data->status_fa_print,
                'assessment_result'              => $data->assessment_result,
                'status'                         => $data->status == 1 ? 'DONE' : '',
                'description'                    => $data->description,
                'status_active'                  => $data->status_active == 1 ? 'ACTIVE' : 'NON ACTIVE',
            ];
        });

        return $training;
    }

    public function destroyTraining(Request $request, $id_training)
    {
        try {
            $this->setLog('info', 'Request delete data Training ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();
            $training = Training::find($id_training);

            if ($training) {
                $training->delete();
            } else {
                DB::rollBack();
                return false;
            }


            $this->setLog('info', 'deleted  Training data' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error delete Training = ' . $exception->getMessage());
            $this->setLog('error', 'Error delete Training = ' . $exception->getLine());
            $this->setLog('error', 'Error delete Training = ' . $exception->getFile());
            $this->setLog('error', 'Error delete Training = ' . $exception->getTraceAsString());
            return null;
        }
    }
}
