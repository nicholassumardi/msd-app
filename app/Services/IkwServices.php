<?php

namespace App\Services;

use App\Jobs\ImportIkwJob;
use App\Models\IKW;
use App\Models\IkwMeeting;
use App\Models\IkwPosition;
use App\Models\IKWRevision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IkwServices extends BaseServices
{
    protected $ikw;
    protected $ikwRevision;
    protected $ikwMeeting;
    protected $ikwPosition;

    public function __construct()
    {
        $this->ikw = IKW::with('jobTask', 'ikwRevision');
        $this->ikwRevision = IKWRevision::with('ikw', 'ikwMeeting', 'ikwPosition');
        $this->ikwMeeting = IkwMeeting::with('ikwRevision');
        $this->ikwPosition = IkwPosition::with('ikwRevision');
    }

    public function importIKWExcel(Request $request)
    {
        $file = $request->file;
        $filepath = $file->storeAs('temp', $request->file->getClientOriginalName(), 'public');
        $query =  ImportIkwJob::dispatch($filepath);

        if ($query) {
            return true;
        }

        return false;
    }

    public function storeIKW(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data IKW ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            $ikw = IKW::create([
                'job_task_id'                 => $request->job_task_id,
                'department_id'               => $request->department_id,
                'code'                        => $request->code,
                'name'                        => $request->name,
                'total_page'                  => $request->total_page,
                'registration_date'           => date('Y-m-d', strtotime($request->registration_date)),
                'print_by_back_office_date'   => date('Y-m-d', strtotime($request->print_by_back_office_date)),
                'submit_to_department_date'   => date('Y-m-d', strtotime($request->submit_to_department_date)),
                'ikw_return_date'             => date('Y-m-d', strtotime($request->ikw_return_date)),
                'ikw_creation_duration'       => $request->ikw_creation_duration,
                'status_document'             => $request->status_document,
                'last_update_date'            => date('Y-m-d', strtotime($request->last_update_date)),
                'description'                 => $request->description,
            ]);


            $this->updateOrStoreIkwRevision($request, $ikw);

            $this->setLog('info', 'New data IKW' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data IKW = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data IKW = ' . $exception->getLine());
            $this->setLog('error', 'Error store data IKW = ' . $exception->getFile());
            $this->setLog('error', 'Error store data IKW = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateIKW(Request $request, $id)
    {
        try {
            $this->setLog('info', 'Request update data IKW ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            $ikw = IKW::findOrFail($id);

            $ikw->update([
                'job_task_id'                 => $request->job_task_id,
                'department_id'               => $request->department_id,
                'code'                        => $request->code,
                'name'                        => $request->name,
                'total_page'                  => $request->total_page,
                'registration_date'           => date('Y-m-d', strtotime($request->registration_date)),
                'print_by_back_office_date'   => date('Y-m-d', strtotime($request->print_by_back_office_date)),
                'submit_to_department_date'   => date('Y-m-d', strtotime($request->submit_to_department_date)),
                'ikw_return_date'             => date('Y-m-d', strtotime($request->ikw_return_date)),
                'ikw_creation_duration'       => $request->ikw_creation_duration,
                'status_document'             => $request->status_document,
                'last_update_date'            => date('Y-m-d', strtotime($request->last_update_date)),
                'description'                 => $request->description,
            ]);

            // Delete existing revisions and their related data
            if ($ikw->ikwRevision) {
                foreach ($ikw->revisions as $revision) {
                    $revision->ikwMeeting()->delete();
                    $revision->ikwPosition()->delete();
                    $revision->delete();
                }
            }

            // Create new revisions from the request data
            $this->updateOrStoreIkwRevision($request, $ikw);

            $this->setLog('info', 'Updated data IKW ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error updating data IKW = ' . $exception->getMessage());
            $this->setLog('error', 'Error updating data IKW = ' . $exception->getLine());
            $this->setLog('error', 'Error updating data IKW = ' . $exception->getFile());
            $this->setLog('error', 'Error updating data IKW = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateOrStoreIkwRevision(Request $request, $ikw = NULL)
    {
        foreach ($request->revisions as $ikwRevision) {
            $dataIkwRevision = [
                'ikw_id'                          => $ikw ? $ikw->id : $request->ikw_id,
                'revision_no'                     => $ikwRevision['revision_no'],
                'reason'                          => $ikwRevision['reason'],
                'process_status'                  => $ikwRevision['process_status'],
                'ikw_fix_status'                  => $ikwRevision['ikw_fix_status'],
                'confirmation'                    => $ikwRevision['confirmation'],
                'change_description'              => $ikwRevision['change_description'],
                'submission_no'                   => $ikwRevision['submission_no'],
                'submission_received_date'        => $ikwRevision['submission_received_date'] ?  date('Y-m-d', strtotime($ikwRevision['submission_received_date'])) : NULL,
                'submission_mr_date'              => $ikwRevision['submission_mr_date'] ? date('Y-m-d', strtotime($ikwRevision['submission_mr_date'])) : NULL,
                'backoffice_return_date'          => $ikwRevision['backoffice_return_date'] ? date('Y-m-d', strtotime($ikwRevision['backoffice_return_date'])) : NULL,
                'revision_status'                 => $ikwRevision['revision_status'],
                'print_date'                      => $ikwRevision['print_date'] ? date('Y-m-d', strtotime($ikwRevision['print_date'])) : NULL,
                'handover_date'                   => $ikwRevision['handover_date'] ? date('Y-m-d', strtotime($ikwRevision['handover_date'])) : NULL,
                'signature_mr_date'               => $ikwRevision['signature_mr_date'] ? date('Y-m-d', strtotime($ikwRevision['signature_mr_date'])) : NULL,
                'distribution_date'               => $ikwRevision['distribution_date'] ? date('Y-m-d', strtotime($ikwRevision['distribution_date'])) : NULL,
                'document_return_date'            => $ikwRevision['document_return_date'] ? date('Y-m-d', strtotime($ikwRevision['document_return_date'])) : NULL,
                'document_disposal_date'          => $ikwRevision['document_disposal_date'] ? date('Y-m-d', strtotime($ikwRevision['document_disposal_date'])) : NULL,
                'document_location_description'   => $ikwRevision['document_location_description'],
                'revision_description'            => $ikwRevision['revision_description'],
                'status_check'                    => $ikwRevision['status_check'],

            ];

            $ikw_revision =  IKWRevision::create($dataIkwRevision);

            if ($ikwRevision['meeting_contents']) {
                foreach ($ikwRevision['meeting_contents'] as $key => $ikwMeeting) {
                    $data =   [
                        'ikw_revision_id'    => $ikw_revision->id,
                        'department_id'      => $ikw->department_id,
                        'revision_no'        => $ikw_revision->revision_no,
                        'ikw_code'           => $ikw->code,
                        'ikw_meeting_no'     => $key,
                        'meeting_date'       => $ikwMeeting['meeting_date'] ? date('Y-m-d', strtotime($ikwMeeting['meeting_date'])) : NULL,
                        'meeting_duration'   => $ikwMeeting['meeting_duration'],
                        'revision_status'    => $ikwMeeting['revision_status'],

                    ];

                    IkwMeeting::create($data);
                }
            }

            if ($ikwRevision['position_calls']) {
                foreach ($ikwRevision['position_calls'] as $key => $ikwPosition) {
                    $data =   [
                        'ikw_revision_id'      => $ikw_revision->id,
                        'department_id'        => $ikw->department_id,
                        'revision_no'          => $ikw_revision->revision_no,
                        'ikw_code'             => $ikw->code,
                        'ikw_position_no'      => $key,
                        'position_call_number' => $ikwPosition['position_call_number'],
                        'field_operator'       => $ikwPosition['field_operator'],

                    ];

                    IkwPosition::create($data);
                }
            }
        }
    }

    public function getDataIKW($id_IKW = NULL)
    {

        if (!empty($id_IKW)) {
            $ikw = $this->ikw->firstWhere('id', $id_IKW);
            $ikw = [
                'id'                             => $ikw->id,
                'revision_no'                    => $ikw->ikwRevision()->orderBy('revision_no', 'DESC')->first()->revision_no ?? 0,
                'job_task_id'                    => $ikw->job_task_id ?? "",
                'department_id'                  => $ikw->department_id ?? "",
                'department_name'                => $ikw->department->name ?? "",
                'code'                           => $ikw->code ?? "",
                'name'                           => $ikw->name ?? "",
                'total_page'                     => $ikw->total_page ?? "",
                'registration_date'              => $ikw->registration_date ? $ikw->registration_date : NULL,
                'print_by_back_office_date'      => $ikw->print_by_back_office_date ? $ikw->print_by_back_office_date : NULL,
                'submit_to_department_date'      => $ikw->submit_to_department_date ? $ikw->submit_to_department_date : NULL,
                'ikw_return_date'                => $ikw->ikw_return_date ? $ikw->ikw_return_date : NULL,
                'ikw_creation_duration'          => $ikw->ikw_creation_duration ?? "",
                'status_document'                => $ikw->status_document ?? "",
                'last_update_date'               => $ikw->last_update_date ? $ikw->last_update_date  : NULL,
                'description'                    => $ikw->description ?? "",
                'ikw_revisions'                  => $ikw->ikwRevision ? $ikw->ikwRevision()->with('ikwMeeting', 'ikwPosition')->get() : NULL,
                'job_task'                       => $ikw->ikwJobTask ? $ikw->ikwJobTask()->with('jobTask')->get() : NULL,
                'job_desc'                       => $ikw->ikwJobDescription ? $ikw->ikwJobDescription()->with('jobDescription')->get() : NULL,
            ];
        } else {
            $ikw = $this->ikw->get()->map(function ($data) {
                return [
                    'id'                             => $data->id,
                    'revision_no'                    => $data->ikwRevision()->orderBy('revision_no', 'DESC')->first()->revision_no ?? "",
                    'job_task_id'                    => $data->job_task_id ?? "",
                    'department_id'                  => $data->department_id ?? "",
                    'department_name'                => $data->department->name ?? "",
                    'code'                           => $data->code ?? "",
                    'name'                           => $data->name ?? "",
                    'total_page'                     => $data->total_page ?? "",
                    'registration_date'              => $data->registration_date ? date('d/m/y', strtotime($data->registration_date)) : NULL,
                    'print_by_back_office_date'      => $data->print_by_back_office_date ? date('d/m/y', strtotime($data->print_by_back_office_date)) : NULL,
                    'submit_to_department_date'      => $data->submit_to_department_date ? date('d/m/y', strtotime($data->submit_to_department_date)) : NULL,
                    'ikw_return_date'                =>  $data->ikw_return_date ? date('d/m/y', strtotime($data->ikw_return_date)) : NULL,
                    'ikw_creation_duration'          => $data->ikw_creation_duration ?? "",
                    'status_document'                => $data->status_document ?? "",
                    'last_update_date'               => date('d/m/y', strtotime($data->last_update_date))  ?? "",
                    'description'                    => $data->description ?? "",
                    'ikw_revisions'                  => $data->dataRevision ? $data->ikwRevision()->with('ikwMeeting', 'ikwPosition')->get() : null,
                ];
            });
        }

        return $ikw;
    }

    public function getDataIKWRevision($id_ikw_revision = NULL)
    {

        if (!empty($id_ikw_revision)) {
            $revision_ikw =  $this->ikwRevision->firstWhere('id', $id_ikw_revision);
            $revision_ikw = [
                'id'                            => $revision_ikw->id,
                'ikw_id'                        => $revision_ikw->ikw_id,
                'ikw_name'                      => $revision_ikw->ikw->name . "/" . $revision_ikw->ikw->code,
                'revision_no'                   => $revision_ikw->revision_no ?? "",
                'reason'                        => $revision_ikw->reason ?? "",
                'process_status'                => $revision_ikw->process_status ?? "",
                'ikw_fix_status'                => $revision_ikw->ikw_fix_status ?? "",
                'confirmation'                  => $revision_ikw->confirmation ?? "",
                'change_description'            => $revision_ikw->change_description ?? "",
                'submission_no'                 => $revision_ikw->submission_no ?? "",
                'submission_received_date'      => date('d/m/y', strtotime($revision_ikw->submission_received_date)) ?? "",
                'submission_mr_date'            => date('d/m/y', strtotime($revision_ikw->submission_mr_date)) ?? "",
                'backoffice_return_date'        => date('d/m/y', strtotime($revision_ikw->backoffice_return_date)) ?? "",
                'revision_status'               => $revision_ikw->revision_status ?? "",
                'print_date'                    => date('d/m/y', strtotime($revision_ikw->print_date)) ?? "",
                'handover_date'                 => date('d/m/y', strtotime($revision_ikw->handover_date)) ?? "",
                'signature_mr_date'             => date('d/m/y', strtotime($revision_ikw->signature_mr_date)) ?? "",
                'distribution_date'             => date('d/m/y', strtotime($revision_ikw->distribution_date)) ?? "",
                'document_return_date'          => date('d/m/y', strtotime($revision_ikw->document_return_date)) ?? "",
                'document_disposal_date'        => date('d/m/y', strtotime($revision_ikw->document_disposal_date)) ?? "",
                'document_location_description' => $revision_ikw->document_location_description ?? "",
                'revision_description'          => $revision_ikw->revision_description ?? "",
                'status_check'                  => $revision_ikw->status_check  == '1' ? "TRUE" : "FALSE",
            ];
        } else {
            $revision_ikw =  $this->ikwRevision
                ->where('revision_no', function ($query) {
                    $query->selectRaw('MAX(revision_no)')
                        ->from('ikw_revisions as sub')
                        ->whereColumn('sub.ikw_id', 'ikw_revisions.ikw_id');
                })
                ->orderByDesc('revision_no')
                ->get()->map(function ($data) {
                    return [
                        'id'                            => $data->id,
                        'ikw_id'                        => $data->ikw_id,
                        'ikw_name'                      => $data->ikw->name . "/" . $data->ikw->code,
                        'revision_no'                   => $data->revision_no ?? "",
                        'reason'                        => $data->reason ?? "",
                        'process_status'                => $data->process_status ?? "",
                        'ikw_fix_status'                => $data->ikw_fix_status ?? "",
                        'confirmation'                  => $data->confirmation ?? "",
                        'change_description'            => $data->change_description ?? "",
                        'submission_no'                 => $data->submission_no ?? "",
                        'submission_received_date'      => date('d/m/y', strtotime($data->submission_received_date)) ?? "",
                        'submission_mr_date'            => date('d/m/y', strtotime($data->submission_mr_date)) ?? "",
                        'backoffice_return_date'        => date('d/m/y', strtotime($data->backoffice_return_date)) ?? "",
                        'revision_status'               => $data->revision_status ?? "",
                        'print_date'                    => date('d/m/y', strtotime($data->print_date)) ?? "",
                        'handover_date'                 => date('d/m/y', strtotime($data->handover_date)) ?? "",
                        'signature_mr_date'             => date('d/m/y', strtotime($data->signature_mr_date)) ?? "",
                        'distribution_date'             => date('d/m/y', strtotime($data->distribution_date)) ?? "",
                        'document_return_date'          => date('d/m/y', strtotime($data->document_return_date)) ?? "",
                        'document_disposal_date'        => date('d/m/y', strtotime($data->document_disposal_date)) ?? "",
                        'document_location_description' => $data->document_location_description ?? "",
                        'revision_description'          => $data->revision_description ?? "",
                        'status_check'                  => $data->status_check  == '1' ? "TRUE" : "FALSE",
                    ];
                });
        }

        return $revision_ikw;
    }


    public function getDataIKWPagination(Request $request)
    {
        $start = (int) $request->start ? (int) $request->start : 0;
        $size = (int)$request->size ? (int)$request->size : 6;
        $filters = json_decode($request->filters, true) ?? [];
        $sorting = json_decode($request->sorting, true) ?? [];
        $globalFilter = $request->globalFilter ?? '';

        $ikw = $this->ikw->where(function ($query) use ($filters, $globalFilter) {
            if ($globalFilter) {
                $query->where(function ($query) use ($globalFilter) {
                    $query->where('code', 'LIKE',  "%{$globalFilter}%")
                        ->orWhere('name', 'LIKE',  "%{$globalFilter}%")
                        ->orWhere('status_document', 'LIKE',  "%{$globalFilter}%")
                        ->orWhere('description', 'LIKE',  "%{$globalFilter}%");
                })->orWhereHas('ikwRevision', function ($query) use ($globalFilter) {
                    $query->where(function ($q) use ($globalFilter) {
                        $columns = [
                            'revision_no',
                            'reason',
                            'process_status',
                            'ikw_fix_status',
                            'confirmation',
                            'change_description',
                            'submission_no',
                            'submission_received_date',
                            'submission_mr_date',
                            'backoffice_return_date',
                            'revision_status',
                            'print_date',
                            'handover_date',
                            'signature_mr_date',
                            'distribution_date',
                            'document_return_date',
                            'document_disposal_date',
                            'document_location_description',
                            'description',
                            'status_check',
                        ];

                        foreach ($columns as $index => $column) {
                            if ($index === 0) {
                                $q->where($column, 'LIKE', "%{$globalFilter}%");
                            } else {
                                $q->orWhere($column, 'LIKE', "%{$globalFilter}%");
                            }
                        }
                    });
                })->orWhereHas('jobTask', function ($query) use ($globalFilter) {
                    $query->Where('description', 'LIKE',  "%{$globalFilter}%");
                })->orWhereHas('department', function ($query) use ($globalFilter) {
                    $query->where('name', 'LIKE',  "%{$globalFilter}%");
                    $query->orWhere('code', 'LIKE',  "%{$globalFilter}%");
                });
            }

            foreach ($filters as $filter) {
                $query->where($filter['id'], $filter['value']);
            }
        });

        foreach ($sorting as $sort) {
            if (isset($sort['id'])) {
                $ikw->orderBy($sort['id'], $sort['desc'] ? 'DESC' : 'ASC');
            }
        }

        $ikw = $ikw
            ->skip($start)
            ->take($size)
            ->get();


        $ikw = $ikw->map(function ($data) {

            return [
                'id'                             => $data->id,
                'revision_no'                    => $data->ikwRevision()->orderBy('revision_no', 'DESC')->first()->revision_no ?? "",
                'job_task_id'                    => $data->job_task_id ?? "",
                'department_id'                  => $data->department_id ?? "",
                'department_name'                => $data->department->name ?? "",
                'code'                           => $data->code ?? "",
                'name'                           => $data->name ?? "",
                'total_page'                     => $data->total_page ?? "",
                'registration_date'              => $data->registration_date ? date('d/m/y', strtotime($data->registration_date)) : "",
                'print_by_back_office_date'      => $data->print_by_back_office_date ? date('d/m/y', strtotime($data->print_by_back_office_date)) : "",
                'submit_to_department_date'      => $data->submit_to_department_date ? date('d/m/y', strtotime($data->submit_to_department_date)) : "",
                'ikw_return_date'                => $data->ikw_return_date ? date('d/m/y', strtotime($data->ikw_return_date)) : "",
                'ikw_creation_duration'          => $data->ikw_creation_duration ?? "",
                'status_document'                => $data->status_document ?? "",
                'last_update_date'               => $data->last_update_date ? date('d/m/y', strtotime($data->last_update_date))  : "",
                'description'                    => $data->description ?? "",
                'ikw_revisions'                  => $data->dataRevision ? $data->ikwRevision()->with('ikwMeeting', 'ikwPosition')->get() : null,
            ];
        });

        return $ikw;
    }

    public function destroyIKW(Request $request, $id_IKW)
    {
        try {
            $this->setLog('info', 'Request delete data IKW ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();
            $ikw = IKW::find($id_IKW);

            if ($ikw) {
                $ikw->delete();
            } else {
                DB::rollBack();
                return false;
            }


            $this->setLog('info', 'deleted  IKW data' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error delete IKW = ' . $exception->getMessage());
            $this->setLog('error', 'Error delete IKW = ' . $exception->getLine());
            $this->setLog('error', 'Error delete IKW = ' . $exception->getFile());
            $this->setLog('error', 'Error delete IKW = ' . $exception->getTraceAsString());
            return null;
        }
    }
}
