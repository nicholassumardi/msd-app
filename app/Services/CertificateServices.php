<?php

namespace App\Services;

use App\Jobs\ImportCertificateJob;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CertificateServices extends BaseServices
{
    protected $certificate;

    public function __construct()
    {
        $this->certificate = Certificate::with('users');
    }

    public function importCertificateExcel(Request $request)
    {
        $file = $request->file;
        $filepath = $file->storeAs('temp', $request->file->getClientOriginalName(), 'public');
        $query =  ImportCertificateJob::dispatch($filepath);

        if ($query) {
            return true;
        }

        return false;
    }

    public function storeCertificate(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data certificate ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            Certificate::create([
                'name' => $request->certificate_name
            ]);

            $this->setLog('info', 'New data certificate' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data certificate = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data certificate = ' . $exception->getLine());
            $this->setLog('error', 'Error store data certificate = ' . $exception->getFile());
            $this->setLog('error', 'Error store data certificate = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateCertificate(Request $request, $id_certificate)
    {
        try {
            $this->setLog('info', 'Request update data certificate ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $certificate = Certificate::find($id_certificate);

            if ($certificate) {
                $certificate->update([
                    'name' => $request->certificate_name,
                ]);
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'updated data certificate ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error update data certificate = ' . $exception->getMessage());
            $this->setLog('error', 'Error update data certificate = ' . $exception->getLine());
            $this->setLog('error', 'Error update data certificate = ' . $exception->getFile());
            $this->setLog('error', 'Error update data certificate = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function getDataCertificate($id_certificate = NULL)
    {

        if (!empty($id_certificate)) {
            $certificate = $this->certificate->firstWhere('id', $id_certificate);
        } else {
            $certificate = $this->certificate->get();
        }

        return $certificate;
    }

    public function destroyCertificate(Request $request, $id_certificate)
    {
        try {
            $this->setLog('info', 'Request delete data certificate ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();
            $certificate = Certificate::find($id_certificate);

            if ($certificate) {
                $certificate->delete();
            } else {
                DB::rollBack();
                return false;
            }


            $this->setLog('info', 'deleted  certificate data' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error delete certificate = ' . $exception->getMessage());
            $this->setLog('error', 'Error delete certificate = ' . $exception->getLine());
            $this->setLog('error', 'Error delete certificate = ' . $exception->getFile());
            $this->setLog('error', 'Error delete certificate = ' . $exception->getTraceAsString());
            return null;
        }
    }
}
