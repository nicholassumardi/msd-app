<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BaseServices;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new BaseServices();
    }
    public function index($company_id)
    {
        $data = $this->service->getDataDashboard($company_id);

        if ($data) {
            $response = [
                'status'  => 200,
                'data'    => $data,
                'message' => 'Data successfully fetched'
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Data successfully fetched'
            ];
        }

        return response()->json($response);
    }
}
