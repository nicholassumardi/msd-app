<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IKWRevision;
use App\Models\Training;
use App\Models\User;
use App\Services\EvaluationServices;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    protected $training;
    protected $service;
    protected $user;
    protected $ikwRevision;

    public function __construct()
    {
        $this->training = Training::with('trainee', 'trainer', 'assessor', 'ikwRevision');
        $this->user = User::all();
        $this->ikwRevision = IKWRevision::with('ikw', 'ikwMeeting');
        $this->service = new EvaluationServices();
    }


    public function index() {}

    public function showEvaluationPagination(Request $request)
    {
        $data = $this->service->getDataEvaluationPagination($request);

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'totalCount' => $this->user->count(),
                'message'    => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }

    public function showTrainingPlanning($id)
    {
        $data = $this->service->getTrainingPlanning($id);

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'message'    => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }

    public function showTrainingPlanningRKI($id)
    {
        $data = $this->service->getTrainingPlanningRKI($id);

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'message'    => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }

    public function showDetailRKI(Request $request, $id)
    {
        $data = $this->service->getDetailRKI($id);

        if ($data) {
            $pagination = [
                'current_page'  => (int) $request->current_page ?? 1,
                'last_page'     => ceil((count($data) / 10)),
                'per_page'      => 10,
                'total'         => count($data),
            ];

            $response = [
                'status'        => 200,
                'data'          => array_slice($data, (((int) $request->current_page - 1) * 10), 10),
                'max_revision'  => $this->ikwRevision->max('revision_no') + 1,
                'pagination'    => $pagination,
                'message'       => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }


    public function showDataVisualization(Request $request)
    {
        $data = $this->service->getDataVisualization($request);

        if ($data) {
            $response = [
                'status'        => 200,
                'data'          => $data,
                'message'       => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to retrieve data"
            ];
        }

        return response()->json($response);
    }

    public function showEligibleIKWByTrainer(Request $request)
    {
        $data = $this->service->getEligibleIKWByTrainer($request);

        if ($data) {
            $response = [
                'status'        => 200,
                'dataIKW'       => $data['dataIKW'],
                'dataTraining'  => $data['dataTraining'],
                'message'       => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }

    public function showEligibleEmployeeByIKW(Request $request)
    {
        $data = $this->service->getEligibleEmployeeByIKW($request);

        if ($data) {
            $response = [
                'status'        => 200,
                'totalCount'    => $data['totalCount'],
                'totalPage'     => ceil($data['totalCount'] / 5),
                'data'          => $data['data'],
                'message'       => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }

    public function showTraineeByTrainerIKW(Request $request)
    {
        $data = $this->service->getTraineeByTrainerIKW($request);

        if ($data['data']) {
            $response = [
                'status'        => 200,
                'data'          => $data['data'],
                'totalCount'    => $data['totalCount'],
                'message'       => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 404,
                'message' => 'No Data found'
            ];
        }

        return response()->json($response);
    }

    public function showIKWToTrainForTrainee(Request $request)
    {
        $data = $this->service->getIKWToTrainForTrainee($request);

        if ($data['data']) {
            $response = [
                'status'        => 200,
                'data'          => $data['data'],
                'totalCount'    => $data['totalCount'],
                'message'       => "Successfully fetched"
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
