<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserEmployeeNumber;
use App\Services\BaseServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserEmployeeNumberServices extends BaseServices
{
    public function storeEmployeeNumber(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data employee number ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            $formattedRequest = array_map(function ($employeeNumber, $index) use ($request) {
                return [
                    'user_id'          => $request->user_id,
                    'employee_number'  => $employeeNumber['employee_number'] ?? null,
                    'registry_date'    => $employeeNumber['registry_date'] ?? null,
                    'status'           => $index === 0 ? 1 : 0
                ];
            }, $request->userEmployeeNumbers, array_keys($request->userEmployeeNumbers));

            UserEmployeeNumber::insert($formattedRequest);

            $this->setLog('info', 'New data employee number' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data employee number = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data employee number = ' . $exception->getLine());
            $this->setLog('error', 'Error store data employee number = ' . $exception->getFile());
            $this->setLog('error', 'Error store data employee number = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateEmployeeNumber(Request $request, $uuid)
    {
        try {
            $this->setLog('info', 'Request update data employee number ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $user = User::firstWhere('uuid', $uuid);

            if ($user) {
                foreach ($request->userEmployeeNumbers as $key => $userEmployeeNumber) {
                    $data =   [
                        'user_id'          => $user->id,
                        'employee_number'  => $userEmployeeNumber["employee_number"],
                        'registry_date'    => $userEmployeeNumber["registry_date"],
                        'status'           => $key == 0 ? 1 : 0
                    ];

                    if (empty($userEmployeeNumber["id"])) {
                        UserEmployeeNumber::create($data);
                    } else {
                        UserEmployeeNumber::where('user_id', $user->id)
                            ->where('id', $userEmployeeNumber['id'])
                            ->update($data);
                    }
                }
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'updated data employee number ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error update data employee number = ' . $exception->getMessage());
            $this->setLog('error', 'Error update data employee number = ' . $exception->getLine());
            $this->setLog('error', 'Error update data employee number = ' . $exception->getFile());
            $this->setLog('error', 'Error update data employee number = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateEmployeeNumberStatus($id_employee_number)
    {
        try {
            $this->setLog('info', 'Request update data employee number ');
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $employeeNumber = UserEmployeeNumber::find($id_employee_number);

            if ($employeeNumber) {
                $employeeNumber->update([
                    'status' => 1
                ]);

                UserEmployeeNumber::where('user_id', $employeeNumber->user_id)
                    ->where('id', '!=', $id_employee_number)
                    ->update(['status' => 0]);
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'updated data employee number ');
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error update data employee number = ' . $exception->getMessage());
            $this->setLog('error', 'Error update data employee number = ' . $exception->getLine());
            $this->setLog('error', 'Error update data employee number = ' . $exception->getFile());
            $this->setLog('error', 'Error update data employee number = ' . $exception->getTraceAsString());
            return null;
        }
    }


    public function getDataEmployeeNumber($id_employee_number = NULL)
    {

        if (!empty($id_employee_number)) {
            $employeeNumber = UserEmployeeNumber::with('jobTask')->where('id', $id_employee_number)->first();
        } else {
            $employeeNumber = UserEmployeeNumber::with('jobTask')->get();
            $employeeNumber = $employeeNumber->map(function ($data) {
                return [
                    'id'                   => $data->id,
                    'code'                 => $data->code,
                    'job_task_id'          => $data->job_task_id,
                    'job_task_description' => $data->jobTask ? $data->jobTask->description : "",
                ];
            });
        }

        return $employeeNumber;
    }

    public function destroyEmployeeNumber(Request $request, $id_employee_number)
    {
        try {
            $this->setLog('info', 'Request delete data employee number ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();
            $employeeNumber = UserEmployeeNumber::find($id_employee_number);

            if ($employeeNumber) {
                $employeeNumber->delete();
            } else {
                DB::rollBack();
                return false;
            }


            $this->setLog('info', 'deleted  employee number data' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error delete employee number = ' . $exception->getMessage());
            $this->setLog('error', 'Error delete employee number = ' . $exception->getLine());
            $this->setLog('error', 'Error delete employee number = ' . $exception->getFile());
            $this->setLog('error', 'Error delete employee number = ' . $exception->getTraceAsString());
            return null;
        }
    }
}
