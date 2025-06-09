<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CertificateRequest;
use App\Services\CertificateServices;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new CertificateServices();
    }

    public function importCertificateExcel(Request $request)
    {

        $query = $this->service->importCertificateExcel($request);

        if ($query) {
            $response = [
                'status' => 201,
                'message' => "Successfully import data certificate"
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => "Failed import data certificate"
            ];
        }


        return response()->json($response);
    }

    public function store(CertificateRequest $request)
    {
        $validatedRequest = $request->validated();
        $data = $this->service->storeCertificate(new Request($validatedRequest));

        if ($data) {
            $response = [
                'status' => 201,
                'message' => 'Successfully updated data company'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to update data company'
            ];
        }

        return response()->json($response);
    }



    public function show($id)
    {
        $data = $this->service->getDataCertificate($id);

        if ($data) {
            $response = [
                'status'      => 200,
                'data'        => $data,
                'totalCount'  => $data->count(),
                'message'     => 'Successfully updated data company'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to update data company'
            ];
        }

        return response()->json($response);
    }

    public function showAll()
    {
        $data = $this->service->getDataCertificate();

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully updated data company'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to update data company'
            ];
        }

        return response()->json($response);
    }

    public function update(CertificateRequest $request, $id_certificate)
    {
        $validatedRequest = $request->validated();
        $data = $this->service->updateCertificate(new Request($validatedRequest), $id_certificate);

        if ($data) {
            $response = [
                'status' => 200,
                'message' => 'Successfully updated data company'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to update data company'
            ];
        }

        return response()->json($response);
    }


    public function destroy(Request $request, $id_certificate)
    {
        $data = $this->service->destroyCertificate($request, $id_certificate);

        if ($data) {
            $response = [
                'status' => 200,
                'message' => 'Successfully deleted data company'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to delete data company'
            ];
        }

        return response()->json($response);
    }
}
