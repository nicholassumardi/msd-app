<?php

namespace App\Services;

use App\Jobs\ImportDepartmentJob;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DepartmentServices extends BaseServices
{
    protected $department;

    public function __construct()
    {
        $this->department = Department::with('company');
    }

    public function importDepartmentExcel(Request $request)
    {
        $file = $request->file;
        $filepath = $file->storeAs('temp', $request->file->getClientOriginalName(), 'public');
        $query =  ImportDepartmentJob::dispatch($filepath);

        if ($query) {
            return true;
        }

        return false;
    }

    public function storeDepartment(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data department ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            Department::create([
                'company_id'  => $request->company_id,
                'parent_id'   => $request->parent_id ?? 0,
                'name'        => $request->name,
                'code'        => $request->code,
            ]);

            $this->setLog('info', 'New data certificate' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data department = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data department = ' . $exception->getLine());
            $this->setLog('error', 'Error store data department = ' . $exception->getFile());
            $this->setLog('error', 'Error store data department = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateDepartment(Request $request, $id_department)
    {
        try {
            $this->setLog('info', 'Request update data department ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $department = Department::find($id_department);

            if ($department) {
                $department->update([
                    'company_id'  => $request->company_id,
                    'parent_id'   => $request->parent_id ?? 0,
                    'name'        => $request->name,
                    'code'        => $request->code,
                ]);
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'updated data department ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error update data department = ' . $exception->getMessage());
            $this->setLog('error', 'Error update data department = ' . $exception->getLine());
            $this->setLog('error', 'Error update data department = ' . $exception->getFile());
            $this->setLog('error', 'Error update data department = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function getDataDepartment($id_department = NULL)
    {
        if (!empty($id_department)) {
            $department = $this->department->firstWhere('id', $id_department);
        } else {
            $department = $this->department->get();
            $department = $department->map(function ($data) {
                return [
                    'id'            => $data->id,
                    'name'          => $data->name,
                    'code'          => $data->code,
                    'company_id'    => $data->company_id,
                    'company_name'  => $data->company ? $data->company->name : "",
                    'parent_name'   => $data->parent->name  ?? "",
                ];
            });
        }

        return $department;
    }

    public function getDataDepartmentByCompany($id_company)
    {
        $department = $this->department->where('company_id', $id_company)->get();
        $department = $department->map(function ($data) {
            return [
                'id'            => $data->id,
                'name'          => $data->name,
                'code'          => $data->code,
                'company_id'    => $data->company_id,
                'company_name'  => $data->company ? $data->company->name : "",
                'parent_name'   => $data->parent->name  ?? "",
            ];
        });
        
        return $department;
    }

    public function getDataDepartmentPagination(Request $request)
    {
        $start = (int) $request->start;
        $size = (int)$request->size;
        $filters = json_decode($request->filters, true) ?? [];
        $sorting = json_decode($request->sorting, true) ?? [];
        $globalFilter = $request->globalFilter ?? '';

        $queryData = $this->department->where(function ($query) use ($filters, $globalFilter) {
            if ($globalFilter) {
                $query->where(function ($query) use ($globalFilter) {
                    $query->where('name', 'LIKE',  "%$globalFilter%")
                        ->orWhere('code', 'LIKE',  "%$globalFilter%");
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
                'id'            => $data->id,
                'name'          => $data->name,
                'code'          => $data->code,
                'company_id'    => $data->company_id,
                'company_name'  => $data->company ? $data->company->name : "",
                'parent_name'   => $data->parent->name  ?? "",
            ];
        });

        return $queryData;
    }


    public function getParentDepartment()
    {
        $department = $this->department->where('parent_id', 0)->get();
        $department = $department->map(function ($data) {
            return [
                'id'            => $data->id,
                'name'          => $data->name,
                'code'          => $data->code,
                'company_id'    => $data->company_id,
                'company_name'  => $data->company ? $data->company->name : "",
                'parent_name'   => $data->parent->name  ?? "",
            ];
        });

        return $department;
    }


    public function destroyDepartment(Request $request, $id_department)
    {
        try {
            $this->setLog('info', 'Request delete data department ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $department = Department::find($id_department);

            if ($department) {
                $department->delete();
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'deleted data department ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error delete department = ' . $exception->getMessage());
            $this->setLog('error', 'Error delete department = ' . $exception->getLine());
            $this->setLog('error', 'Error delete department = ' . $exception->getFile());
            $this->setLog('error', 'Error delete department = ' . $exception->getTraceAsString());
            return null;
        }
    }
}
