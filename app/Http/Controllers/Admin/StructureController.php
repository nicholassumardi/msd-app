<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StructureRequest;
use App\Models\User;
use App\Services\StructureServices;
use App\Services\UserPlotServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StructureController extends Controller
{

    protected $user;
    protected $service;
    protected $userPlotService;

    public function __construct()
    {
        $this->user = User::all();
        $this->service = new StructureServices();
        $this->userPlotService = new UserPlotServices();
    }

    public function index()
    {
        //
    }

    public function importStructureExcel(Request $request)
    {
        $cacheKey = uniqid();
        $query = $this->service->importStructureExcel($request, $cacheKey);
        $filepath = Cache::get($cacheKey);

        if (is_array($filepath) && isset($filepath['status']) && $filepath['status'] === 500) {
            return response()->json([
                'status'  => $filepath['status'],
                'message' => $filepath['message'],
            ]);
        }

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
        $query = $this->userPlotService->importUserJobCodeExcel($request);

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


    public function storeStructure(StructureRequest $request)
    {
        $validatedRequest = $request->validated();
        $data = $this->service->storeStructure(new Request($validatedRequest));

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

    public function storeUserPlot(Request $request)
    {
        $data = $this->userPlotService->storeUserPlot($request);

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

    public function storeUserPlotRequest(Request $request)
    {
        $data = $this->userPlotService->storeUserPlotRequest($request);

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

    public function showStructure($id)
    {
        $data = $this->service->getDataStructure($id);

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully fetched data mapping'
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }

    public function showUserPlot($id)
    {
        $data = $this->userPlotService->getDataUserPlot($id);

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully fetched data structure'
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }

    public function showAllUserPlot()
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
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }

    public function showUserPlotPagination(Request $request)
    {
        $data = $this->userPlotService->getDataUserPlotPagination($request);

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'totalCount' => $this->user->count(),
                'message'    => 'Successfully fetched data company'
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }

    public function showAllStructure()
    {
        $data = $this->service->getDataStructure();

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully fetched data mapping'
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }

    public function showAllStructureByDepartment(Request $request)
    {
        $data = $this->service->getDataStructureByDepartment($request);

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
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }

    public function showAllStructureHierarchy(Request $request, $id)
    {
        $data = $this->service->getDataAllStructureHierarchy($request, $id);

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully fetched data structure'
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }

    public function showStructureHierarchyUser($id)
    {
        $data = $this->service->getStructureHierarchyUser($id);

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully fetched data mapping'
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }

    public function showStructureHierarchyParent($parent_id)
    {
        $data = $this->service->getStructureHierarchyParent($parent_id);

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully fetched data structure'
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }

    public function showStructureHierarchyChildren($id)
    {
        $data = $this->service->getStructureHierarchyChildren($id);

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully fetched data Structure'
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }

    public function showUserPlotPosition()
    {
        $data = $this->userPlotService->getDataUserPlotPosition();

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Successfully fetched data position & job code'
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }


    public function updateUserPlot(Request $request, $uuid)
    {
        $data = $this->userPlotService->updateUserPlot($request, $uuid);

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

    public function updateStructure(Request $request, $id)
    {
        $data = $this->service->updateStructure($request, $id);

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

    public function updateBulkUserStructure(Request $request)
    {
        $data = $this->service->updateBulkStructure($request);

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

    public function updateUserPlotRequest(Request $request, $id)
    {
        $data = $this->userPlotService->updateUserPlotRequest($request, $id);

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

    public function updateStatusUserPlot($id_user_plot)
    {
        $data = $this->userPlotService->updateUserPlotStatus($id_user_plot);

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

    public function moveUserPlotRequest(Request $request, $id)
    {
        $data = $this->userPlotService->moveUserPlotRequest($request, $id);

        if ($data) {
            $response = [
                'status' => 200,
                'message' => 'Successfully updated data user plot'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to update data user plot'
            ];
        }

        return response()->json($response);
    }

    public function destroyStructure(Request $request, $id)
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

    public function destroyUserPlot(Request $request, $id)
    {
        $data = $this->userPlotService->destroyUserPlot($request, $id);

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
