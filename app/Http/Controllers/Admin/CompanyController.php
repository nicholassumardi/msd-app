<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Services\CompanyServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CompanyController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new CompanyServices();
    }

    public function importCompanyExcel(Request $request)
    {

        $query = $this->service->importCompanyExcel($request);

        if ($query) {
            $response = [
                'status' => 201,
                'message' => "Successfully import data company"
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => "Failed import data company"
            ];
        }


        return response()->json($response);
    }

    public function exportDataCompanyExcel(Request $request)
    {
        $cacheKey = uniqid();
        $this->service->exportDataCompanyExcel($request, $cacheKey);
        $filepath = Cache::get($cacheKey);

        if (file_exists($filepath)) {
            return response()->download($filepath);
        } else {
            $response = [
                'status' => 500,
                'message' => "Failed import data user"
            ];
        }


        return response()->json($response);
    }


    public function store(CompanyRequest $request)
    {
        $validatedRequest = $request->validated();
        $data = $this->service->storeCompany(new Request($validatedRequest));

        if ($data) {
            $response = [
                'status' => 201,
                'message' => 'Successfully created data company'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to create data company'
            ];
        }

        return response()->json($response);
    }

    public function show($id)
    {
        $data = $this->service->getDataCompany($id);

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully fetched data company'
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }

    public function showAll()
    {
        $data = $this->service->getDataCompany();

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully fetched data company'
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }


    public function update(CompanyRequest $request, $id_company)
    {
        $validatedRequest = $request->validated();
        $data = $this->service->updateCompany(new Request($validatedRequest), $id_company);

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

    public function destroy(Request $request, $id_company)
    {
        $data = $this->service->destroyCompany($request, $id_company);

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
