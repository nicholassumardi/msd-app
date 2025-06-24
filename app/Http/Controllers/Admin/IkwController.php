<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IKW;
use App\Services\IkwServices;
use App\Services\JobTaskDescriptionServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IkwController extends Controller
{
    protected $ikw;
    protected $service;
    protected $serviceJobTaskDescription;

    public function __construct()
    {
        $this->ikw = IKW::with('jobTask', 'ikwRevision');
        $this->service = new IkwServices();
        $this->serviceJobTaskDescription = new JobTaskDescriptionServices();
    }
    public function index() {}

    public function importJobTaskDescExcel(Request $request)
    {

        $cacheKey = uniqid();
        $query = $this->serviceJobTaskDescription->importJobTaskDescExcel($request, $cacheKey);
        $filepath = Cache::get($cacheKey);

        if (file_exists($filepath)) {
            return response()->download($filepath);
        }

        if ($query) {
            $response = [
                'status' => 201,
                'message' => "Successfully import data"
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => "Failed import data"
            ];
        }


        return response()->json($response);
    }

    public function importIKWExcel(Request $request)
    {
        $cacheKey = uniqid();
        $query = $this->service->importIKWExcel($request, $cacheKey);
        $response = [];

        if ($query) {
            $response = Cache::get($cacheKey);
        }

        return response()->json($response);
    }

    public function store(Request $request)
    {
        $data = $this->service->storeIKW($request);

        if ($data) {
            $response = [
                'status'     => 201,
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

    public function update(Request $request, $id_training)
    {
        $data = $this->service->updateIKW($request, $id_training);

        if ($data) {
            $response = [
                'status'     => 200,
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

    public function show($id_ikw)
    {
        $data = $this->service->getDataIKW($id_ikw);

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
        $data = $this->service->getDataIKW();

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'totalCount' => $this->ikw->count(),
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

    public function showAllRevision()
    {
        $data = $this->service->getDataIKWRevision();

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

    public function showIkwPagination(Request $request)
    {
        $data = $this->service->getDataIKWPagination($request);

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'totalCount' => $this->ikw->count(),
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
