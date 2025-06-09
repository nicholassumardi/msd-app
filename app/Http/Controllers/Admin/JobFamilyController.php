<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Requests\IkwRequest;
use App\Http\Requests\JobCodeRequest;
use App\Http\Requests\JobDescriptionRequest;
use App\Http\Requests\JobTaskRequest;
use App\Models\Category;
use App\Models\JobCode;
use App\Services\CategoryServices;
use App\Services\IkwServices;
use App\Services\JobCodeServices;
use App\Services\JobDescriptionServices;
use App\Services\JobTaskServices;
use Illuminate\Http\Request;

class JobFamilyController extends Controller
{

    protected $jobCode;
    protected $category;
    protected $serviceCategory;
    protected $serviceJobCode;
    protected $serviceJobDescription;
    protected $serviceJobTask;
    protected $serviceIkws;

    public function __construct()

    {
        $this->jobCode = JobCode::with('category');
        $this->category = Category::with('jobCode');
        $this->serviceCategory = new CategoryServices();
        $this->serviceJobCode = new JobCodeServices();
        $this->serviceJobDescription = new JobDescriptionServices();
        $this->serviceJobTask = new JobTaskServices();
        $this->serviceIkws = new IkwServices();
    }


    public function index() {}


    public function importJobFamilyExcel(Request $request)
    {
        $query = $this->serviceJobCode->importJobFamilyExcel($request);

        if ($query) {
            $response = [
                'status' => 201,
                'message' => "Successfully import data job code"
            ];
        } else {
            $response = [
                'status' => 500,
                'message' => "Failed import data job code"
            ];
        }


        return response()->json($response);
    }

    public function storeCategory(CategoryRequest $request)
    {
        $validatedRequest = $request->validated();
        $data = $this->serviceCategory->storeCategory(new Request($validatedRequest));

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

    public function storeJobCode(JobCodeRequest $request)
    {
        $validatedRequest = $request->validated();
        $data = $this->serviceJobCode->storeJobCode(new Request($validatedRequest));

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

    public function storeJobDescription(JobDescriptionRequest $request)
    {
        $validatedRequest = $request->validated();
        $data = $this->serviceJobDescription->storeJobDescription(new Request($validatedRequest));

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

    public function storeJobTask(Request $request)
    {
        $validatedRequest = $request->validated();
        $data = $this->serviceJobTask->storeJobTask(new Request($validatedRequest));

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

    public function storeIkws(IkwRequest $request)
    {
        $validatedRequest = $request->validated();
        $data = $this->serviceIkws->storeIKW(new Request($validatedRequest));

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

    public function showCategory($id)
    {
        $data = $this->serviceCategory->getCategory($id);

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'totalCount' => $data->count(),
                'message'    => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to retrieve data"
            ];
        }

        return response()->json($response);
    }

    public function showCategoryPagination(Request $request)
    {
        $data = $this->serviceCategory->getDataCategoryPagination($request);

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'totalCount' => $this->category->count(),
                'message'    => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to retrieve data"
            ];
        }

        return response()->json($response);
    }

    public function showJobCode($id)
    {
        $data = $this->serviceJobCode->getDataJobCode($id);

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'totalCount' => $data->count(),
                'message'    => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to retrieve data"
            ];
        }

        return response()->json($response);
    }

    public function showJobDescription($id)
    {
        $data = $this->serviceJobDescription->getDataJobDescription($id);

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'totalCount' => $data->count(),
                'message'    => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to retrieve data"
            ];
        }

        return response()->json($response);
    }

    public function showJobTask($id)
    {
        $data = $this->serviceJobTask->getDataJobTask($id);

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'totalCount' => $data->count(),
                'message'    => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to retrieve data"
            ];
        }

        return response()->json($response);
    }

    public function showIkws($id)
    {
        $data = $this->serviceIkws->getDataIKW($id);

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'totalCount' => $data->count(),
                'message'    => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to retrieve data"
            ];
        }

        return response()->json($response);
    }

    public function showCategoryAll()
    {
        $data = $this->serviceCategory->getCategory();

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'totalCount' => $data->count(),
                'message'    => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to retrieve data"
            ];
        }

        return response()->json($response);
    }

    public function showJobCodeAll()
    {
        $data = $this->serviceJobCode->getDataJobCode();

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'totalCount' => $data->count(),
                'message'    => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to retrieve data"
            ];
        }

        return response()->json($response);
    }

    public function showJobCodePagination(Request $request)
    {
        $data = $this->serviceJobCode->getDataJobCodePagination($request);

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'totalCount' => $this->jobCode->count(),
                'message'    => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to retrieve data"
            ];
        }

        return response()->json($response);
    }

    public function showJobDescriptionAll()
    {
        $data = $this->serviceJobDescription->getDataJobDescription();

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'totalCount' => $data->count(),
                'message'    => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to retrieve data"
            ];
        }

        return response()->json($response);
    }

    public function showJobTaskAll()
    {
        $data = $this->serviceJobTask->getDataJobTask();

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'totalCount' => $data->count(),
                'message'    => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to retrieve data"
            ];
        }

        return response()->json($response);
    }

    public function showIkwsAll()
    {
        $data = $this->serviceIkws->getDataIKW();

        if ($data) {
            $response = [
                'status'     => 200,
                'data'       => $data,
                'totalCount' => $data->count(),
                'message'    => "Successfully fetched"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "Failed to retrieve data"
            ];
        }

        return response()->json($response);
    }


    public function updateCategory(CategoryRequest $request, $id_category)
    {
        $validatedRequest = $request->validated();
        $data = $this->serviceCategory->updateCategory(new Request($validatedRequest), $id_category);

        if ($data) {
            $response = [
                'status'  => 200,
                'message' => "data updated successfully"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "data failed to update"
            ];
        }

        return response()->json($response);
    }

    public function updateJobCode(JobCodeRequest $request, $id_job_code)
    {
        $validatedRequest = $request->validated();
        $data = $this->serviceJobCode->updateJobCode(new Request($validatedRequest), $id_job_code);

        if ($data) {
            $response = [
                'status'  => 200,
                'message' => "data updated successfully"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "data failed to update"
            ];
        }

        return response()->json($response);
    }

    public function updateJobDescription(JobDescriptionRequest $request, $id_job_description)
    {
        $validatedRequest = $request->validated();
        $data = $this->serviceJobDescription->updateJobDescription(new Request($validatedRequest), $id_job_description);

        if ($data) {
            $response = [
                'status'  => 200,
                'message' => "data updated successfully"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "data failed to update"
            ];
        }

        return response()->json($response);
    }

    public function updateJobTask(JobTaskRequest $request, $id_job_task)
    {
        $validatedRequest = $request->validated();
        $data = $this->serviceJobTask->updateJobTask(new Request($validatedRequest), $id_job_task);

        if ($data) {
            $response = [
                'status'  => 200,
                'message' => "data updated successfully"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "data failed to update"
            ];
        }

        return response()->json($response);
    }

    public function updateIkws(IkwRequest $request, $id_ikw)
    {

        $validatedRequest = $request->validated();
        $data = $this->serviceIkws->updateIKW(new Request($validatedRequest), $id_ikw);

        if ($data) {
            $response = [
                'status'  => 200,
                'message' => "data updated successfully"
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => "data failed to update"
            ];
        }

        return response()->json($response);
    }


    public function destroyCategory(Request $request, $id_job_code)
    {
        $data = $this->serviceCategory->destroyCategory($request, $id_job_code);

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

    public function destroyJobCode(Request $request, $id_job_code)
    {
        $data = $this->serviceJobCode->destroyJobCode($request, $id_job_code);

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

    public function destroyJobDescription(Request $request, $id_job_description)
    {
        $data = $this->serviceJobDescription->destroyJobDescription($request, $id_job_description);

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

    public function destroyJobTask(Request $request, $id_job_task)
    {
        $data = $this->serviceJobTask->destroyJobTask($request, $id_job_task);

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


    public function destroyIkws(Request $request, $id_ikw)
    {
        $data = $this->serviceIkws->destroyIKW($request, $id_ikw);

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
