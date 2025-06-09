<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IKW;
use App\Services\IkwServices;
use Illuminate\Http\Request;

class IkwController extends Controller
{
    protected $ikw;
    protected $service;

    public function __construct()
    {
        $this->ikw = IKW::with('jobTask', 'ikwRevision');
        $this->service = new IkwServices();
    }
    public function index() {}

    public function importIKWExcel(Request $request)
    {
        $query = $this->service->importIKWExcel($request);

        if ($query) {
            $response = [
                'status' => 201,
                'message' => "Successfully import data IKW"
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => "Failed import data IKW"
            ];
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
