<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CalendarServices;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new CalendarServices();
    }


    public function index() {}

    public function store(Request $request)
    {
        $data = $this->service->storeCalendar($request);

        if ($data) {
            $response = [
                'status' => 201,
                'message' => 'Successfully updated data calendar'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to update data calendar'
            ];
        }

        return response()->json($response);
    }


    public function show($id)
    {
        $data = $this->service->getDataCalendar($id);

        if ($data) {
            $response = [
                'status'      => 200,
                'data'        => $data,
                'totalCount'  => $data->count(),
                'message'     => 'Success fetched data calendar'
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
        $data = $this->service->getDataCalendar();

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Success fetched data calendar'
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }


    public function showWeekly()
    {
        $data = $this->service->getDataCalendarWeekly();

        if ($data['data']) {
            $response = [
                'status'  => 200,
                'data'         => $data['data'],
                'weekRange'    => $data['weekRange'],
                'message' => 'Success fetched data calendar'
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }

    public function update(Request $request, $id_Calendar)
    {
        $data = $this->service->updateCalendar($request, $id_Calendar);

        if ($data) {
            $response = [
                'status' => 200,
                'message' => 'Successfully updated data calendar'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to update data calendar'
            ];
        }

        return response()->json($response);
    }


    public function destroy(Request $request, $id_Calendar)
    {
        $data = $this->service->destroyCalendar($request, $id_Calendar);

        if ($data) {
            $response = [
                'status' => 200,
                'message' => 'Successfully deleted data calendar'
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => 'Failed to delete data calendar'
            ];
        }

        return response()->json($response);
    }
}
