<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserCertificateServices extends BaseServices
{
    public function storeUserCertificate(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data User Certificate ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();

            $formattedRequest = array_map(function ($certificate) use ($request) {
                return [
                    'user_id'         => $request->user_id,
                    'certificate_id'  => $certificate['certificate_id'],
                    'description'     => $certificate['description'] ?? null,
                    'expiration_date' => $certificate['expiration_date'] ?? null,
                ];
            }, $request->userCertificates);

            UserCertificate::insert($formattedRequest);

            $this->setLog('info', 'New data User Certificate' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data User Certificate = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data User Certificate = ' . $exception->getLine());
            $this->setLog('error', 'Error store data User Certificate = ' . $exception->getFile());
            $this->setLog('error', 'Error store data User Certificate = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateUserCertificate(Request $request, $uuid)
    {
        try {
            $this->setLog('info', 'Request update data User Certificate ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $user = User::firstWhere('uuid', $uuid);

            if ($user) {
                foreach ($request->userCertificates as $userCertificate) {
                    $data =   [
                        'user_id'          => $user->id,
                        'certificate_id'   => $userCertificate["certificate_id"],
                        'description'      => $userCertificate["description"],
                        'expiration_date'  => $userCertificate["expiration_date"],
                    ];

                    if (empty($userCertificate["id"])) {
                        UserCertificate::create($data);
                    } else {
                        UserCertificate::where('user_id', $user->id)
                            ->where('id', $userCertificate['id'])
                            ->update($data);
                    }
                }
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'updated data User Certificate ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error update data User Certificate = ' . $exception->getMessage());
            $this->setLog('error', 'Error update data User Certificate = ' . $exception->getLine());
            $this->setLog('error', 'Error update data User Certificate = ' . $exception->getFile());
            $this->setLog('error', 'Error update data User Certificate = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function getDataUserCertificate($id_user_certificate = NULL)
    {

        if (!empty($id_user_certificate)) {
            $UserCertificate = UserCertificate::with('jobCode')->where('id', $id_user_certificate)->first();
        } else {
            $UserCertificate = UserCertificate::with('jobCode')->get();
            $UserCertificate = $UserCertificate->map(function ($data) {
                return [
                    'id'              => $data->id,
                    'job_code_id'     => $data->job_code_id,
                    'job_code_code'   => $data->jobCode ? $data->jobCode->full_code : "",
                    'description'     => $data->description,
                ];
            });
        }

        return $UserCertificate;
    }

    public function destroyUserCertificate(Request $request, $id_user_certificate)
    {
        try {
            $this->setLog('info', 'Request delete data User Certificate ' . json_encode($request->all()));
            $this->setLog('info', 'Start');
            DB::beginTransaction();
            $userCertificate = UserCertificate::find($id_user_certificate);

            if ($userCertificate) {
                $userCertificate->delete();
            } else {
                DB::rollBack();
                return false;
            }


            $this->setLog('info', 'deleted  UserCertificate data' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error delete UserCertificate = ' . $exception->getMessage());
            $this->setLog('error', 'Error delete UserCertificate = ' . $exception->getLine());
            $this->setLog('error', 'Error delete UserCertificate = ' . $exception->getFile());
            $this->setLog('error', 'Error delete UserCertificate = ' . $exception->getTraceAsString());
            return null;
        }
    }
}
