<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Training;
use App\Services\TrainingServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TrainingController extends Controller
{
    protected $training;
    protected $service;

    public function __construct()
    {
        $this->training = Training::with('traineer');
        $this->service = new TrainingServices();
    }
    public function index() {}

    public function importTrainingExcel(Request $request)
    {
        $cacheKey = uniqid();
        $query = $this->service->importTrainingExcel($request, $cacheKey);
        $filepath = Cache::get($cacheKey);

        if (file_exists($filepath)) {
            return response()->download($filepath);
        }

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
        $data = $this->service->storeTraining($request);

        if ($data) {
            return response()->json([
                'message'    => 'Training created successfully',
                'status'     => 201,
            ]);
        } else {
            return response()->json([
                'message' => 'Failed to create training',
                'status'     => 500,
            ]);
        }
    }

    public function update(Request $request, $id_training)
    {
        $data = $this->service->updateTraining($request, $id_training);

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

    public function show($id_training)
    {
        $data = $this->service->getDataTraining($id_training);

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'message'    => 'Successfully fetched data training'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to fetch data training'
            ];
        }

        return response()->json($response);
    }

    public function showAll()
    {
        $data = $this->service->getDataTraining();

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'message'    => 'Successfully fetched data training'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to fetch data training'
            ];
        }

        return response()->json($response);
    }

    public function showByUUID($uuid, Request $request)
    {
        $data = $this->service->getDataTrainingByUUID($uuid, $request);

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data['data'],
                'totalCount' => $data['totalCount'],
                'message'    => 'Successfully fetched data training'
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Failed to fetch data training'
            ];
        }

        return response()->json($response);
    }

    public function showTrainingPagination(Request $request)
    {
        $data = $this->service->getDataTrainingPagination($request);

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'totalCount' => $this->training->count(),
                'message'    => 'Successfully fetched data training'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to fetch data training'
            ];
        }

        return response()->json($response);
    }
}
