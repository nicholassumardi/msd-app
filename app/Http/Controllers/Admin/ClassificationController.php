<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassificationRequest;
use App\Services\ClassificationServices;
use Illuminate\Http\Request;

class ClassificationController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new ClassificationServices();
    }

    public function storeAge(ClassificationRequest $request)
    {
        $validatedRequest = $request->validated();
        $data = $this->service->storeAgeClassfication(new Request($validatedRequest));

        if ($data) {
            $response = [
                'status'  => 201,
                'message' => "Successfully stored data",
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to store data",
            ];
        }

        return response()->json($response);
    }

    public function storeWorkingDuration(ClassificationRequest $request)
    {
        $validatedRequest = $request->validated();
        $data = $this->service->storeWorkingDurationClassfication(new Request($validatedRequest));

        if ($data) {
            $response = [
                'status'  => 201,
                'message' => "Successfully stored data",
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to store data",
            ];
        }

        return response()->json($response);
    }

    public function storeGeneral(ClassificationRequest $request)
    {
        $validatedRequest = $request->validated();
        $data = $this->service->storeGeneralClassfication(new Request($validatedRequest));

        if ($data) {
            $response = [
                'status'  => 201,
                'message' => "Successfully stored data",
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to store data",
            ];
        }

        return response()->json($response);
    }


    public function showAge($id)
    {
        $data = $this->service->getDataAgeClassification($id);

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => "Success",
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed",
            ];
        }

        return response()->json($response);
    }

    public function showWorkingDuration($id)
    {
        $data = $this->service->getDataWorkingDurationClassification($id);

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => "Succes",
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed",
            ];
        }

        return response()->json($response);
    }

    public function showGeneral($id)
    {
        $data = $this->service->getDataGeneralClassification($id);

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => "Success",
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed",
            ];
        }

        return response()->json($response);
    }

    public function showAgeAll()
    {

        $data = $this->service->getDataAgeClassification();

        if ($data) {
            $response = [
                'status'        => 200,
                'data'          => $data,
                'totalCount'    => $data->count(),
                'message'       => "Success",
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed",
            ];
        }

        return response()->json($response);
    }

    public function showWorkingDurationAll()
    {
        $data = $this->service->getDataWorkingDurationClassification();

        if ($data) {
            $response = [
                'status'        => 200,
                'data'          => $data,
                'totalCount'    => $data->count(),
                'message'       => "Success",
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed",
            ];
        }

        return response()->json($response);
    }

    public function showGeneralAll()
    {
        $data = $this->service->getDataGeneralClassification();

        if ($data) {
            $response = [
                'status'        => 200,
                'data'          => $data,
                'totalCount'    => $data->count(),
                'message'       => "Success",
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed",
            ];
        }

        return response()->json($response);
    }


    public function updateAge(ClassificationRequest $request, $id_age)
    {
        $validatedRequest = $request->validated();
        $data = $this->service->updateAgeClassfication(new Request($validatedRequest), $id_age);

        if ($data) {
            $response = [
                'status'  => 200,
                'message' => "Successfully updated data",
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to update data",
            ];
        }

        return response()->json($response);
    }

    public function updateWorkingDuration(ClassificationRequest $request, $id_working_duration)
    {
        $validatedRequest = $request->validated();
        $data = $this->service->updateWorkingDurationClassfication(new Request($validatedRequest), $id_working_duration);

        if ($data) {
            $response = [
                'status'  => 200,
                'message' => "Successfully updated data",
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to update data",
            ];
        }

        return response()->json($response);
    }

    public function updateGeneral(ClassificationRequest $request, $id_general)
    {
        $validatedRequest = $request->validated();
        $data = $this->service->updateGeneralClassfication(new Request($validatedRequest), $id_general);

        if ($data) {
            $response = [
                'status'  => 200,
                'message' => "Successfully updated data",
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to update data",
            ];
        }

        return response()->json($response);
    }


    public function destroyAge(Request $request, $id_age)
    {
        $data = $this->service->destroyAgeClassfication($request, $id_age);

        if ($data) {
            $response = [
                'status'  => 200,
                'message' => "Successfully deleted data",
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to delete data",
            ];
        }

        return response()->json($response);
    }

    public function destroyWorkingDuration(Request $request, $id_working_duration)
    {
        $data = $this->service->destroyAgeClassfication($request, $id_working_duration);

        if ($data) {
            $response = [
                'status'  => 200,
                'message' => "Successfully deleted data",
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to delete data",
            ];
        }

        return response()->json($response);
    }

    public function destroyGeneral(Request $request, $id_general)
    {
        $data = $this->service->destroyAgeClassfication($request, $id_general);

        if ($data) {
            $response = [
                'status'  => 200,
                'message' => "Successfully deleted data",
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to delete data",
            ];
        }

        return response()->json($response);
    }
}
