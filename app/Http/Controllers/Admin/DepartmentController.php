<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use App\Services\DepartmentServices;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{

    protected $department;
    protected $service;

    public function __construct()
    {
        $this->department = Department::with('company');
        $this->service = new DepartmentServices();
    }

    public function index() {}

    public function importDepartmentExcel(Request $request)
    {

        $query = $this->service->importDepartmentExcel($request);

        if ($query) {
            $response = [
                'status' => 201,
                'message' => "Successfully import data Department"
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => "Failed import data Department"
            ];
        }


        return response()->json($response);
    }

    public function store(DepartmentRequest $request)
    {
        $validatedRequest = $request->validated();
        $data =  $this->service->storeDepartment(new Request($validatedRequest));

        if ($data) {
            $response = [
                'status'  => 201,
                'message' => "Successfully created",
            ];
        } else {
            $response = [
                'status'    => 500,
                'message'   => "Failed to create data",
            ];
        }

        return response()->json($response);
    }

    public function show($id)
    {
        $data = $this->service->getDataDepartment($id);

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to retrieve data"
            ];
        }

        return response()->json($response);
    }

    public function showAll()
    {
        $data = $this->service->getDataDepartment();

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'message'    => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to retrieve data"
            ];
        }

        return response()->json($response);
    }

    public function showDepartmentByCompany($id_company)
    {
        $data = $this->service->getDataDepartmentByCompany($id_company);

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'totalCount' => $data->count(),
                'message'    => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to retrieve data"
            ];
        }

        return response()->json($response);
    }


    public function showDepartmentPagination(Request $request)
    {
        $data = $this->service->getDataDepartmentPagination($request);

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data['data'],
                'totalCount' => $data['totalCount'],
                'message'    => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to retrieve data"
            ];
        }

        return response()->json($response);
    }

    public function showParentDepartment()
    {
        $data = $this->service->getParentDepartment();

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'message'    => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to retrieve data"
            ];
        }

        return response()->json($response);
    }


    public function update(DepartmentRequest $request, $id_department)
    {
        $validatedRequest = $request->validated();
        $data = $this->service->updateDepartment(new Request($validatedRequest), $id_department);

        if ($data) {
            $response = [
                'status'  => 200,
                'message' => "data updated successfully"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "data failed to update"
            ];
        }

        return response()->json($response);
    }


    public function destroy(Request $request, $id_department)
    {
        $data = $this->service->destroyDepartment($request, $id_department);

        if ($data) {
            $response = [
                'status'    => 200,
                'message'   => "Data successfully deleted"
            ];
        } else {
            $response = [
                'status'    => 500,
                'message'   => "Data failed to delete"
            ];
        }

        return response()->json($response);
    }
}
