<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RkiRequest;
use App\Models\RKI;
use App\Services\RkiServices;
use Illuminate\Http\Request;

class RkiController extends Controller
{
    protected $training;
    protected $service;

    public function __construct()
    {
        $this->training = RKI::with('ikw');
        $this->service = new RkiServices();
    }
    public function index() {}

    public function importRKIExcel(Request $request)
    {
        $query = $this->service->importRKIExcel($request);

        if ($query) {
            $response = [
                'status' => 201,
                'message' => "Successfully import data training"
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => "Failed import data training"
            ];
        }


        return response()->json($response);
    }

    public function store(Request $request)
    {
        // $validatedRequest = $request->validated();
        // $newRequest = new Request($validatedRequest);
        $data = $this->service->storeRKI($request);

        if ($data) {
            return response()->json([
                'message'    => 'RKI created successfully',
                'status'     => 201,
            ]);
        } else {
            return response()->json([
                'message' => 'Failed to create RKI',
                'status'     => 500,
            ]);
        }
    }

    public function update(Request $request, $id_training)
    {
        $data = $this->service->updateRKI($request, $id_training);

        if ($data) {
            return response()->json([
                'status'     => 200,
                'message'    => 'Training created successfully',
            ]);
        } else {
            return response()->json([
                'status'     => 500,
                'message' => 'Failed to create training',
            ]);
        }
    }

    public function show($id_rki)
    {
        $data = $this->service->getDataRKI($id_rki);

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'message'    => 'Successfully fetched data training'
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
        $data = $this->service->getDataRKI();

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'message'    => 'Successfully fetched data training'
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }

    public function showByUserStructureMapping($user_structure_mapping_id)
    {
        $data = $this->service->getDataRKIByUserStructureMapping($user_structure_mapping_id);

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'message'    => 'Successfully fetched data training'
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }

    public function showByIKW(Request $request)
    {
        $data = $this->service->getDataRKIByIKW($request);

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'message'    => 'Successfully fetched data training'
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }

    public function showRkiPagination(Request $request)
    {
        $data = $this->service->getDataRKIPagination($request);

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data['data'],
                'totalCount' => $data['count'],
                'message'    => 'Successfully fetched data training'
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }
}
