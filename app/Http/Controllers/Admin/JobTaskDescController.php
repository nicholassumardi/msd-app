<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\JobTaskDescriptionServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class JobTaskDescController extends Controller
{

    protected $user;
    protected $service;

    public function __construct()
    {
        $this->service = new JobTaskDescriptionServices();
    }
    public function importJobTaskDescExcel(Request $request)
    {

        $cacheKey = uniqid();
        $query = $this->service->importJobTaskDescExcel($request, $cacheKey);
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
}
