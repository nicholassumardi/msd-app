<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExcelRequest;
use App\Http\Requests\UserRequest;
use App\Jobs\ExportUserJob;
use App\Jobs\ImportUpdatedUserJob;
use App\Jobs\ImportUserJob;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use App\Services\UserCertificateServices;
use App\Services\UserEmployeeNumberServices;
use App\Services\UserServices;
use App\Services\UserServiceYearServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{

    protected $user;
    protected $service;
    protected $serviceUserServiceYear;
    protected $serviceUserCertificate;
    protected $serviceUserEmployeeNumber;

    public function __construct()
    {
        $this->user = User::all();
        $this->service = new UserServices();
        $this->serviceUserServiceYear = new UserServiceYearServices();
        $this->serviceUserCertificate = new UserCertificateServices();
        $this->serviceUserEmployeeNumber = new UserEmployeeNumberServices();
    }

    public function index()
    {
        return view('testupload');
    }

    public function storeTest(Request $request)
    {
        $file = $request->file;
        $filepath = $file->storeAs('temp', $request->file->getClientOriginalName(), 'public');

        $cacheKey = uniqid();
        ImportUserJob::dispatch($filepath, $cacheKey);

        $filepath = Cache::get($cacheKey);

        if (file_exists($filepath)) {
            return response()->download($filepath);
        }
    }

    public function updateTest(Request $request)
    {
        $file = $request->file;
        $filepath = $file->storeAs('temp', $request->file->getClientOriginalName(), 'public');

        ImportUpdatedUserJob::dispatch($filepath);
    }

    public function exportTest(Request $request)
    {
        $cacheKey = uniqid();
        $this->service->exportDataUserExcel($request, $cacheKey);

        $filepath = Cache::get($cacheKey);

        if (file_exists($filepath)) {
            return response()->download($filepath);
        }
    }

    public function importUserExcel(Request $request)
    {

        $cacheKey = uniqid();
        $query = $this->service->importUserExcel($request, $cacheKey);
        $filepath = Cache::get($cacheKey);

        if (file_exists($filepath)) {
            return response()->download($filepath);
        }

        if ($query) {
            $response = [
                'status' => 201,
                'message' => "Successfully import data user"
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => "Failed import data user"
            ];
        }


        return response()->json($response);
    }

    public function importUpdatedUserExcel(Request $request)
    {

        $query = $this->service->importUpdatedUserExcel($request);

        if ($query) {
            $response = [
                'status' => 201,
                'message' => "Successfully import data user"
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => "Failed import data user"
            ];
        }


        return response()->json($response);
    }

    public function exportDataUserExcel(Request $request)
    {
        $cacheKey = uniqid();
        $this->service->exportDataUserExcel($request, $cacheKey);
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

    public function store(UserRequest $request)
    {
        $validatedRequest = $request->validated();
        $newRequest = new Request($validatedRequest);
        $data = $this->service->storeUser($newRequest);

        if (!$data) {
            return response()->json([
                'status'  => 500,
                'message' => "Failed to create data",
            ]);
        }

        $newRequest->merge(['user_id' => $data->id]);
        $this->serviceUserServiceYear->storeUserService($newRequest);

        $employeeNumbers = !empty($newRequest->userEmployeeNumbers)
            ? $this->serviceUserEmployeeNumber->storeEmployeeNumber($newRequest)
            : true;

        $userCertificates = !empty($newRequest->userCertificates)
            ? $this->serviceUserCertificate->storeUserCertificate($newRequest)
            : true;

        if (!$employeeNumbers || !$userCertificates) {
            return response()->json([
                'status'  => 500,
                'message' => "Failed to create data",
            ]);
        }

        return response()->json([
            'status'  => 201,
            'message' => "Successfully created",
        ]);
    }

    public function show($uuid, Request $request)
    {
        $data = $this->service->getDataUser($uuid, $request);

        if ($data) {
            $response = [
                'status' => 200,
                'data'   => $data,
                'message' => "Successfully fetched data company"
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => "failed to fetch data company"
            ];
        }

        return response()->json($response);
    }

    public function showAll(Request $request)
    {
        $data = $this->service->getDataUser(NULL, $request);

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully fetched data company'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to fetch data company'
            ];
        }

        return response()->json($response);
    }

    public function showUserPagination(Request $request)
    {
        $data = $this->service->getDataUserPagination($request);

        if ($data) {
            $response = [
                'status'       => 200,
                'data'         => $data,
                'totalCount'   => $this->user->count(),
                'message'      => 'Successfully fetched data company'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to fetch data company'
            ];
        }

        return response()->json($response);
    }


    public function showByDepartment($id_department)
    {
        $data = $this->service->getDataUserByDepartment($id_department);

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully fetched data company'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to fetch data company'
            ];
        }

        return response()->json($response);
    }


    public function update(UserRequest $request, $uuid)
    {
        $validatedRequest = $request->validated();
        $newRequest = new Request($validatedRequest);
        $data = $this->service->updateUser($newRequest, $uuid);

        if (!$data) {
            return response()->json([
                'status'  => 500,
                'message' => "Failed to create data",
            ]);
        }

        $this->serviceUserServiceYear->updateUserService($newRequest, $uuid);

        $employeeNumbers = !empty($newRequest->userEmployeeNumbers)
            ? $this->serviceUserEmployeeNumber->updateEmployeeNumber($newRequest, $uuid)
            : true;

        $userCertificates = !empty($newRequest->userCertificates)
            ? $this->serviceUserCertificate->updateUserCertificate($newRequest, $uuid)
            : true;

        if (!$employeeNumbers || !$userCertificates) {
            return response()->json([
                'status'  => 500,
                'message' => "Failed to create data",
            ]);
        }

        return response()->json([
            'status'  => 200,
            'message' => "Successfully updated",
        ]);
    }

    public function updateStatus($id_employee_number)
    {
        $data = $this->serviceUserEmployeeNumber->updateEmployeeNumberStatus($id_employee_number);

        if ($data) {
            $response = [
                'status'  => 200,
                'message' => "Successfully updated",
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to create data",
            ];
        }

        return response()->json($response);
    }


    public function destroy(Request $request, $id_user)
    {
        $data = $this->service->destroyUser($request, $id_user);

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
