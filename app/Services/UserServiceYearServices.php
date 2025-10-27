<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserServiceYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserServiceYearServices extends BaseServices
{
    public function storeUserService(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data user services year ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            UserServiceYear::create([
                'user_id'    => $request->user_id,
                'join_date'  =>  $this->parseDateUTC($request->join_date),
                'leave_date' =>  $this->parseDateUTC($request->leave_date),
            ]);


            $this->setLog('info', 'New data user services year' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data user services year = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data user services year = ' . $exception->getLine());
            $this->setLog('error', 'Error store data user services year = ' . $exception->getFile());
            $this->setLog('error', 'Error store data user services year = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateUserService(Request $request, $uuid)
    {
        try {
            $this->setLog('info', 'Request update data services year ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $userServiceYear = User::firstWhere('uuid', $uuid)->userServiceYear;

            if ($userServiceYear) {
                $userServiceYear->update([
                    'join_date'  =>  $this->parseDateUTC($request->join_date),
                    'leave_date' =>  $this->parseDateUTC($request->leave_date),
                ]);
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'updated data services year ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error update user services year = ' . $exception->getMessage());
            $this->setLog('error', 'Error update user services year = ' . $exception->getLine());
            $this->setLog('error', 'Error update user services year = ' . $exception->getFile());
            $this->setLog('error', 'Error update user services year = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function destroyUserService(Request $request)
    {
        try {
            $this->setLog('info', 'Request delete data services year ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $userServiceYear = User::find($request->user_id)->userServiceYear;

            if ($userServiceYear) {
                $userServiceYear->delete();
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'deleted delete data services year ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error delete user services year = ' . $exception->getMessage());
            $this->setLog('error', 'Error delete user services year = ' . $exception->getLine());
            $this->setLog('error', 'Error delete user services year = ' . $exception->getFile());
            $this->setLog('error', 'Error delete user services year = ' . $exception->getTraceAsString());
            return null;
        }
    }
}
