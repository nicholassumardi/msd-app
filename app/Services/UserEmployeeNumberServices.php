<?php

namespace App\Services;

use App\Models\HistoryLog;
use App\Models\User;
use App\Models\UserEmployeeNumber;
use App\Models\UserHistory;
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
                    'registry_date'    => $employeeNumber['registry_date'] ? $this->parseDateUTC($employeeNumber['registry_date']) : now()->format('Y-m-d'),
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
                $historyLog =  HistoryLog::create([
                    'modified_at' => date('Y-m-d'),
                    'table_name'  => 'user_histories',
                ]);

                if ($historyLog) {
                    UserHistory::create([
                        'history_log_id'  => $historyLog->id,
                        'name'            => $user->name,
                        'company_id'      => $user->company_id,
                        'department_id'   => $user->department_id,
                        'date_of_birth'   => $request->date_of_birth ? $this->parseDateUTC($request->date_of_birth) : null,
                        'identity_card'   => str_replace("-", "",  $user->identity_card),
                        'gender'          => $user->gender,
                        'religion'        => $user->religion,
                        'email'           => $user->email,
                        'photo'           => $user->photo ? $user->photo : '',
                        'education'       => $user->education,
                        'status'          => $user->status,
                        'marital_status'  => $user->marital_status,
                        'address'         => $user->address,
                        'phone'           => $user->phone,
                        'employee_type'   => $user->employee_type,
                        'section'         => $user->section,
                        'position_code'   => $user->position_code,
                        'status_twiji'    => $user->status_twiji,
                        'schedule_type'   => $user->schedule_type,
                        'employee_number' => $user->userEmployeeNumber()
                            ->where('status', 1)
                            ->latest('id')
                            ->first()
                            ->employee_number ?? "",
                        'join_date' => optional(
                            $user->userServiceYear()->latest('id')->first()
                        )->join_date,
                        'leave_date' => optional(
                            $user->userServiceYear()->latest('id')->first()
                        )->leave_date,
                    ]);

                    foreach ($request->userEmployeeNumbers as $key => $userEmployeeNumber) {
                        $data =   [
                            'user_id'          => $user->id,
                            'employee_number'  => $userEmployeeNumber["employee_number"],
                            'registry_date'    => $userEmployeeNumber['registry_date'] ? $this->parseDateUTC($userEmployeeNumber['registry_date']) : null,
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
