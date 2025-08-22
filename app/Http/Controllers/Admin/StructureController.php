<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStructureMappingRequest;
use App\Models\User;
use App\Services\StructureServices;
use App\Services\UserStructureMappingServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StructureController extends Controller
{

    protected $user;
    protected $service;
    protected $userMappingService;

    public function __construct()
    {
        $this->user = User::all();
        $this->service = new StructureServices();
        $this->userMappingService = new UserStructureMappingServices();
    }

    public function index()
    {
        //
    }

    public function importStructureExcel(Request $request)
    {

        $cacheKey = uniqid();
        $query = $this->userMappingService->importStructureExcel($request, $cacheKey);
        $filepath = Cache::get($cacheKey);

        if (file_exists($filepath)) {
            return response()->download($filepath);
        }

        if ($query) {
            $response = [
                'status' => 201,
                'message' => "Successfully import data structure"
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => "Failed import data structure"
            ];
        }


        return response()->json($response);
    }

    public function importUserJobCodeExcel(Request $request)
    {
        $query = $this->service->importUserJobCodeExcel($request);

        if ($query) {
            $response = [
                'status' => 201,
                'message' => "Successfully import data user job code"
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => "Failed import data user job code"
            ];
        }


        return response()->json($response);
    }

    public function store(Request $request)
    {
        $data = $this->service->storeStructure($request);

        if ($data) {
            $response = [
                'status'  => 200,
                'message' => 'Data successfully created'
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Data failed to create'
            ];
        }

        return response()->json($response);
    }

    public function storeMapping(UserStructureMappingRequest $request)
    {
        $validatedRequest = $request->validated();
        $data = $this->userMappingService->storeUserMapping(new Request($validatedRequest));

        if ($data) {
            $response = [
                'status'  => 200,
                'message' => 'Data successfully created'
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Data failed to create'
            ];
        }

        return response()->json($response);
    }

    public function storeRequestEmployee(Request $request)
    {
        $data = $this->service->requestNewEmployee($request);

        if ($data) {
            $response = [
                'status'  => 200,
                'message' => 'Data successfully created'
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Data failed to create'
            ];
        }

        return response()->json($response);
    }

    public function storeMappingRequest(Request $request)
    {
        $data = $this->userMappingService->storeUserMappingRequest($request);

        if ($data) {
            $response = [
                'status'  => 200,
                'message' => 'Data successfully created'
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Data failed to create'
            ];
        }

        return response()->json($response);
    }

    public function show($id)
    {

        $data = $this->service->getDataStructure($id);

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully fetched data structure'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to fetch data structure'
            ];
        }

        return response()->json($response);
    }

    public function showUserMapping($id)
    {

        $data = $this->userMappingService->getDataUserMapping($id);

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully fetched data mapping'
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Failed to fetch data mapping'
            ];
        }

        return response()->json($response);
    }

    public function showAll()
    {
        $data = $this->service->getDataStructure();

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully fetched data'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to fetch data'
            ];
        }

        return response()->json($response);
    }

    public function showStructurePagination(Request $request)
    {
        $data = $this->service->getDataStructurePagination($request);

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'totalCount' => $this->user->count(),
                'message'    => 'Successfully fetched data company'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to fetch data company'
            ];
        }

        return response()->json($response);
    }

    public function showAllUserMapping()
    {
        $data = $this->userMappingService->getDataUserMapping();

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully fetched data mapping'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to fetch data mapping'
            ];
        }

        return response()->json($response);
    }

    public function showAllMappingByDepartment(Request $request)
    {
        $data = $this->userMappingService->getDataUserMappingByDepartment($request);

        if ($data['data']) {
            $pagination = [
                'current_page'  => (int) $request->current_page ?? 1,
                'last_page'     => ceil(($data['totalCount'] / 10)),
                'per_page'      => 10,
                'total'         => $data['totalCount'],
            ];

            $response = [
                'status'      => 200,
                'data'        => $data['data'],
                'pagination'  => $pagination,
                'message'     => 'Successfully fetched data mapping'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to fetch data mapping'
            ];
        }

        return response()->json($response);
    }

    public function showUserMappingHierarchy(Request $request, $id)
    {
        $data = $this->userMappingService->getDataUserMappingHierarchy($request, $id);

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully fetched data mapping'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to fetch data mapping'
            ];
        }

        return response()->json($response);
    }

    public function showMappingHierarchyUser($id)
    {
        $data = $this->userMappingService->getMappingHierarchyUser($id);

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully fetched data mapping'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to fetch data mapping'
            ];
        }

        return response()->json($response);
    }

    public function showMappingHierarchyParent($parent_id)
    {
        $data = $this->userMappingService->getMappingHierarchyParent($parent_id);

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully fetched data mapping'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to fetch data mapping'
            ];
        }

        return response()->json($response);
    }

    public function showMappingHierarchyChildren($id)
    {
        $data = $this->userMappingService->getMappingHierarchyChildren($id);

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully fetched data mapping'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to fetch data mapping'
            ];
        }

        return response()->json($response);
    }

    public function showUserJobCode()
    {
        $data = $this->service->getDataUserJobCode();

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully fetched data position & job code'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to fetch data position & job code'
            ];
        }

        return response()->json($response);
    }


    public function update(Request $request, $uuid)
    {
        $data = $this->service->updateStructure($request, $uuid);

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

    public function updateUserMapping(Request $request, $id)
    {
        $data = $this->userMappingService->updateUserMapping($request, $id);

        if ($data) {
            $response = [
                'status' => 200,
                'message' => 'Successfully updated data user mapping'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to update data user mapping'
            ];
        }

        return response()->json($response);
    }

    public function updateUserMappingRequest(Request $request, $id)
    {
        $data = $this->userMappingService->updateUserMappingRequest($request, $id);

        if ($data) {
            $response = [
                'status' => 200,
                'message' => 'Successfully updated data user mapping'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to update data user mapping'
            ];
        }

        return response()->json($response);
    }

    public function moveUserMappingRequest(Request $request, $id)
    {
        $data = $this->userMappingService->moveUserMappingRequest($request, $id);

        if ($data) {
            $response = [
                'status' => 200,
                'message' => 'Successfully updated data user mapping'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to update data user mapping'
            ];
        }

        return response()->json($response);
    }

    public function updateStatus($id_user_job_code)
    {
        $data = $this->service->updateStructureStatus($id_user_job_code);

        if ($data) {
            $response = [
                'status' => 200,
                'message' => 'Successfully updated data status'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to update data status'
            ];
        }

        return response()->json($response);
    }

    public function destroy(Request $request, $id)
    {
        $data = $this->service->destroyStructure($request, $id);

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
