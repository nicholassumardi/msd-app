<?php

namespace App\Services;

use App\Jobs\ImportRkiJob;
use App\Models\RKI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RkiServices extends BaseServices
{
    protected $rki;

    public function __construct()
    {
        $this->rki = RKI::with([
            'ikw',
            'userStructureMapping.department',
            'userStructureMapping.jobCode'
        ]);
    }

    public function importRKIExcel(Request $request)
    {
        $file = $request->file;
        $filepath = $file->storeAs('temp', $request->file->getClientOriginalName(), 'public');
        $query =  ImportRkiJob::dispatch($filepath);

        if ($query) {
            return true;
        }

        return false;
    }


    public function storeRKI(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data RKI ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();
            $this->setLog('info', 'New data RKI ' . json_encode($request->all()));

            foreach ($request->ikws as $ikw_id) {
                RKI::create([
                    'user_structure_mapping_id'   => $request->user_structure_mapping_id,
                    'ikw_id'                      => $ikw_id,
                    'training_time'               => (int) $request->training_time,
                ]);
            }

            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data RKI = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data RKI = ' . $exception->getLine());
            $this->setLog('error', 'Error store data RKI = ' . $exception->getFile());
            $this->setLog('error', 'Error store data RKI = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateRKI(Request $request, $id_rki)
    {
        try {
            $this->setLog('info', 'Request update data RKI ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();
            $this->setLog('info', 'New data RKI ' . json_encode($request->all()));

            $rki =  RKI::find($id_rki);

            if ($rki) {
                $rki->update([
                    'user_structure_mapping_id'   => $request->user_structure_mapping_id,
                    'ikw_id'                      => $request->ikw_id,
                    'training_time'               => $request->training_time,
                ]);
            } else {
                DB::rollback();
                return false;
            }

            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error update data RKI = ' . $exception->getMessage());
            $this->setLog('error', 'Error update data RKI = ' . $exception->getLine());
            $this->setLog('error', 'Error update data RKI = ' . $exception->getFile());
            $this->setLog('error', 'Error update data RKI = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function getDataRKIByUserStructureMapping(Request $request)
    {
        $rki = collect();
        if ($request->user_structure_mapping_id) {
            $rki = $this->rki->where('user_structure_mapping_id', $request->user_structure_mapping_id)->get()?->map(function ($data) {
                return [
                    'id'                      => $data->id ?? null,
                    'unique_code'             => $data->userStructureMapping
                        ?  $data->userStructureMapping->position_code_structure . "/" .
                        ($data->ikw ?  $data->ikw->code : "No Code") : null,
                    'user_structure_mapping'  => $data->userStructureMapping ?? "",
                    'ikw_id'                  => $data->ikw->id ?? "",
                    'ikw_code'                => $data->ikw->code ?? "",
                    'ikw_name'                => $data->ikw->name ?? "",
                    'ikw_page'                => $data->ikw->total_page ?? "",
                    'department'              => $data->ikw->department->code ?? "",
                    'training_time'           => $data->training_time ?? "",
                    'job_task'                => $data->ikw?->jobTaskDetail ? $data->ikw->jobTaskDetail()->with('jobTask')->get() : null,
                    'job_desc'                => $data->ikw?->jobDescDetail ? $data->ikw->jobDescDetail()->with('jobDescription')->get() : null,
                ];
            });
        }

        return $rki;
    }
    public function getDataRKIByIKW(Request $request)
    {
        if ($request->ikw_id) {
            $rki = $this->rki->where('ikw_id', $request->ikw_id)->get()->map(function ($data) {
                return [
                    'id'                      => $data->id,
                    'unique_code'             => $data->userStructureMapping
                        ?  $data->userStructureMapping->position_code_structure . "/" .
                        ($data->ikw->code ?  $data->ikw->code : "No Code") : null,
                    'user_structure_mapping'  => $data->userStructureMapping ?? "",
                    'ikw_id'                  => $data->ikw->id ?? "",
                    'ikw_code'                => $data->ikw->code ?? "",
                    'ikw_name'                => $data->ikw->name ?? "",
                    'ikw_page'                => $data->ikw->total_page ?? "",
                    'department'              => $data->ikw->department->code ?? "",
                    'training_time'           => $data->training_time ?? "",
                    'job_task'                => $data->ikw->jobTaskDetail ? $data->ikw->jobTaskDetail()->with('jobTask')->get() : null,
                    'job_desc'                => $data->ikw->jobDescDetail ? $data->ikw->jobDescDetail()->with('jobDescription')->get() : null,
                ];
            });
        }

        return $rki;
    }

    public function getDataRKI($id_rki = NULL)
    {
        if (!empty($id_rki)) {
            $rki = $this->rki->firstWhere('id', $id_rki);
        } else {
            $rki = $this->rki->get()->groupBy('position_job_code')->map(function ($group) {
                $data = $group->first();
                return [
                    'id'                 => $data->id,
                    'unique_code'        => $data->ikw->code . "/" . $data->position_job_code,
                    'position_job_code'  => $data->position_job_code ?? "",
                    'ikw_code'           => $data->ikw->code ?? "",
                    'ikw_name'           => $data->ikw->name ?? "",
                    'ikw_page'           => $data->ikw->total_page ?? "",
                    'department'         => $data->ikw->department->code ?? "",
                    'training_time'      => $data->training_time ?? "",
                ];
            });
        }

        return $rki;
    }

    public function getDataRKIPagination(Request $request)
    {
        $start = (int)$request->start ? (int)$request->start : 0;
        $size = (int)$request->size ? (int)$request->size : 6;
        $filters = json_decode($request->filters, true) ?? [];
        $sorting = json_decode($request->sorting, true) ?? [];
        $globalFilter = $request->globalFilter ?? '';

        $queryData = $this->rki
            ->with(['ikw', 'ikw.department', 'userStructureMapping'])
            ->where(function ($query) use ($filters, $globalFilter) {
                if ($globalFilter) {
                    $query->where(function ($query) use ($globalFilter) {
                        $query->where('position_job_code', 'LIKE', "%$globalFilter%")
                            ->orWhereHas('userStructureMapping', function ($q) use ($globalFilter) {
                                $q->where('name', 'LIKE', "%$globalFilter%");
                            });
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

        $allData = $queryData->get();

        // Group by user_structure_mapping_id
        $groupedData = $allData->groupBy('user_structure_mapping_id');

        $totalCount =  ceil($groupedData->count() / $size);


        // Paginate the grouped data
        $paginatedGroups = $groupedData->slice($start, $size);

        $formattedData = $paginatedGroups->map(function ($group, $userStructureId) {
            $firstRecord = $group->first();

            // Map each IKW in the group
            $ikwList = $group->map(function ($record) {
                return [
                    'id'                => $record->id,
                    'ikw_id'            => $record->ikw->id ?? null,
                    'ikw_code'          => $record->ikw->code ?? "",
                    'ikw_name'          => $record->ikw->name ?? "",
                    'position_job_code' => $record->position_job_code ?? "",
                    'training_time'     => $record->training_time ?? "",
                    'department'        => $record->ikw->department->code ?? "",
                    'ikw_page'          => $record->ikw->total_page ?? "",
                ];
            })->values();

            return [
                'user_structure_mapping_id' => $userStructureId,
                'structure_name'            => $firstRecord->userStructureMapping->name ?? "",
                'structure_description'     => $firstRecord->userStructureMapping->description ?? "",
                'structure_code'            => $firstRecord->userStructureMapping->code ?? "",
                'ikw_count'                 => $group->count(),
                'total_training_hours'      => $group->sum('training_time'),
                'ikwList'                   => $ikwList,
            ];
        })->values();

        return [
            'count' => $totalCount,
            'data'  => $formattedData,
        ];
    }


    public function destroyRKI(Request $request, $id_rki)
    {
        try {
            $this->setLog('info', 'Request store data RKI ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();
            $this->setLog('info', 'New data RKI ' . json_encode($request->all()));

            $rki =  RKI::find($id_rki);

            if ($rki) {
                $rki->delete();
            } else {
                DB::rollBack();
                return false;
            }


            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data RKI = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data RKI = ' . $exception->getLine());
            $this->setLog('error', 'Error store data RKI = ' . $exception->getFile());
            $this->setLog('error', 'Error store data RKI = ' . $exception->getTraceAsString());
            return null;
        }
    }
}
