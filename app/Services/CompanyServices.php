<?php

namespace App\Services;

use App\Jobs\ExportCompanyJob;
use App\Jobs\ImportCompanyJob;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyServices extends BaseServices
{
    protected $company;

    public function __construct()
    {
        $this->company = Company::select('id', 'name', 'code');
    }

    public function importCompanyExcel(Request $request)
    {
        $file = $request->file;
        $filepath = $file->storeAs('temp', $request->file->getClientOriginalName(), 'public');
        $query =  ImportCompanyJob::dispatch($filepath);

        if ($query) {
            return true;
        }

        return false;
    }

    public function exportDataCompanyExcel(Request $request, $cachekey)
    {
        $companies = json_decode($request->id_companies, true) ?? [];
        $company =  $this->company;

        if ($companies) {
            $company->whereIn('id', array_keys($companies));
        }

        $company = $company
            ->orderBy('id', 'ASC')
            ->get()
            ->map(function ($company, $key) {
                return [
                    'no'   => $key + 1,
                    'name' => $company->name ?? "",
                    'code' => $company->code ?? "",
                ];
            });

        $query =  ExportCompanyJob::dispatch($cachekey, $company);

        if ($query) {
            return true;
        }

        return false;
    }

    public function storeCompany(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data company ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            Company::create([
                'name'        => $request->company_name,
                'unique_code' => $request->unique_code,
                'code'        => $request->company_code,
            ]);

            $this->setLog('info', 'New data company' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data company = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data company = ' . $exception->getLine());
            $this->setLog('error', 'Error store data company = ' . $exception->getFile());
            $this->setLog('error', 'Error store data company = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateCompany(Request $request,  $id_company)
    {
        try {
            $this->setLog('info', 'Request update data company ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $company = Company::find($id_company);

            if ($company) {
                $company->update([
                    'name'        => $request->company_name,
                    'unique_code' => $request->unique_code,
                    'code'        => $request->company_code,
                ]);
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'End');
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error update company = ' . $exception->getMessage());
            $this->setLog('error', 'Error update company = ' . $exception->getLine());
            $this->setLog('error', 'Error update company = ' . $exception->getFile());
            $this->setLog('error', 'Error update company = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function getDataCompany($id_company = NULL)
    {
        if (!empty($id_company)) {
            $company = $this->company->where('id', $id_company)->first();
        } else {
            $company = $this->company->get();
        }

        return $company;
    }

    public function destroyCompany(Request $request,  $id_company)
    {
        try {
            $this->setLog('info', 'Request delete data company ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            $company = Company::find($id_company);

            if ($company) {
                $company->delete();
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'deleted data company' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error delete company = ' . $exception->getMessage());
            $this->setLog('error', 'Error delete company = ' . $exception->getLine());
            $this->setLog('error', 'Error delete company = ' . $exception->getFile());
            $this->setLog('error', 'Error delete company = ' . $exception->getTraceAsString());
            return null;
        }
    }
}
