<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\TemplateExcelServices;
use Illuminate\Support\Facades\Cache;

class TemplateExcelController extends Controller
{
    protected $service;
    public function __construct()
    {
        $this->service = new TemplateExcelServices();
    }

    public function index() {}

    public function exportTemplateStructure()
    {
        $cacheKey = uniqid();
        $this->service->exportTemplateStructure($cacheKey);
        $filepath = Cache::get($cacheKey);

        if (file_exists($filepath)) {
            return response()->download($filepath);
        } else {
            $response = [
                'status' => 500,
                'message' => "Failed import data user"
            ];
        }


        return response()->json($response);
    }

    public function exportTemplateCorporate()
    {
        $cacheKey = uniqid();
        $this->service->exportTemplateCorporate($cacheKey);
        $filepath = Cache::get($cacheKey);

        if (file_exists($filepath)) {
            return response()->download($filepath);
        } else {
            $response = [
                'status' => 500,
                'message' => "Failed import data user"
            ];
        }


        return response()->json($response);
    }

    public function exportTemplateEmployee()
    {
        $cacheKey = uniqid();
        $this->service->exportTemplateEmployee($cacheKey);
        $filepath = Cache::get($cacheKey);

        if (file_exists($filepath)) {
            return response()->download($filepath);
        } else {
            $response = [
                'status' => 500,
                'message' => "Failed import data user"
            ];
        }


        return response()->json($response);
    }

    public function exportTemplateRKI()
    {
        $cacheKey = uniqid();
        $this->service->exportTemplateRKI($cacheKey);
        $filepath = Cache::get($cacheKey);

        if (file_exists($filepath)) {
            return response()->download($filepath);
        } else {
            $response = [
                'status' => 500,
                'message' => "Failed import data user"
            ];
        }


        return response()->json($response);
    }

    public function exportTemplateTraining()
    {
        $cacheKey = uniqid();
        $this->service->exportTemplateTraining($cacheKey);
        $filepath = Cache::get($cacheKey);

        if (file_exists($filepath)) {
            return response()->download($filepath);
        } else {
            $response = [
                'status' => 500,
                'message' => "Failed import data user"
            ];
        }


        return response()->json($response);
    }

    public function exportTemplateIKW()
    {
        $cacheKey = uniqid();
        $this->service->exportTemplateIKW($cacheKey);
        $filepath = Cache::get($cacheKey);

        if (file_exists($filepath)) {
            return response()->download($filepath);
        } else {
            $response = [
                'status' => 500,
                'message' => "Failed import data user"
            ];
        }


        return response()->json($response);
    }

    public function exportTemplateIKWRevision()
    {
        $cacheKey = uniqid();
        $this->service->exportTemplateIKWRevision($cacheKey);
        $filepath = Cache::get($cacheKey);

        if (file_exists($filepath)) {
            return response()->download($filepath);
        } else {
            $response = [
                'status' => 500,
                'message' => "Failed import data user"
            ];
        }


        return response()->json($response);
    }

    public function exportTemplateJobCode()
    {
        $cacheKey = uniqid();
        $this->service->exportTemplateJobCode($cacheKey);
        $filepath = Cache::get($cacheKey);

        if (file_exists($filepath)) {
            return response()->download($filepath);
        } else {
            $response = [
                'status' => 500,
                'message' => "Failed import data user"
            ];
        }


        return response()->json($response);
    }
}
