<?php

namespace App\Services;

use App\Models\AgeClassification;
use App\Models\EmploymentDurationClassification;
use App\Models\GeneralClassification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassificationServices extends BaseServices
{
    public function storeAgeClassfication(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data age classification ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            AgeClassification::create([
                'rule'  => $request->rule,
                'label' => $request->label,
            ]);

            $this->setLog('info', 'New data age classification ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data age classification = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data age classification = ' . $exception->getLine());
            $this->setLog('error', 'Error store data age classification = ' . $exception->getFile());
            $this->setLog('error', 'Error store data age classification = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function storeWorkingDurationClassfication(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data working duration classification ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            EmploymentDurationClassification::create([
                'rule'  => $request->rule,
                'label' => $request->label,
            ]);

            $this->setLog('info', 'New data working duration classification ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data working duration classification = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data working duration classification = ' . $exception->getLine());
            $this->setLog('error', 'Error store data working duration classification = ' . $exception->getFile());
            $this->setLog('error', 'Error store data working duration classification = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function storeGeneralClassfication(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            GeneralClassification::create([
                'rule'  => $request->rule,
                'label' => $request->label,
            ]);

            $this->setLog('info', 'New data general classification ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data general classification = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data general classification = ' . $exception->getLine());
            $this->setLog('error', 'Error store data general classification = ' . $exception->getFile());
            $this->setLog('error', 'Error store data general classification = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateAgeClassfication(Request $request, $id_age)
    {
        try {
            $this->setLog('info', 'Request update data age classification ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $ageClassification = AgeClassification::find($id_age);

            if ($ageClassification) {
                $ageClassification->update([
                    'rule'  => $request->rule,
                    'label' => $request->label,
                ]);
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'updated age classification ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error update data age classification = ' . $exception->getMessage());
            $this->setLog('error', 'Error update data age classification = ' . $exception->getLine());
            $this->setLog('error', 'Error update data age classification = ' . $exception->getFile());
            $this->setLog('error', 'Error update data age classification = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateWorkingDurationClassfication(Request $request, $id_working_duration)
    {
        try {
            $this->setLog('info', 'Request update data working duration classification ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $workingDurationClassification = EmploymentDurationClassification::find($id_working_duration);

            if ($workingDurationClassification) {
                $workingDurationClassification->update([
                    'rule'  => $request->rule,
                    'label' => $request->label,
                ]);
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'updated working duration classification ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error update data working duration classification = ' . $exception->getMessage());
            $this->setLog('error', 'Error update data working duration classification = ' . $exception->getLine());
            $this->setLog('error', 'Error update data working duration classification = ' . $exception->getFile());
            $this->setLog('error', 'Error update data working duration classification = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateGeneralClassfication(Request $request, $id_general)
    {
        try {
            $this->setLog('info', 'Request update data ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $generalClassification = GeneralClassification::find($id_general);

            if ($generalClassification) {
                $generalClassification->update([
                    'rule'  => $request->rule,
                    'label' => $request->label,
                ]);
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'updated general classification ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error update data general classification = ' . $exception->getMessage());
            $this->setLog('error', 'Error update data general classification = ' . $exception->getLine());
            $this->setLog('error', 'Error update data general classification = ' . $exception->getFile());
            $this->setLog('error', 'Error update data general classification = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function getDataAgeClassification($id_age = NULL)
    {
        if (!empty($id_age)) {
            $ageClassification = AgeClassification::where('id', $id_age)->first();
        } else {
            $ageClassification = AgeClassification::all();
        }

        return $ageClassification;
    }

    public function getDataWorkingDurationClassification($id_workingduration = NULL)
    {
        if (!empty($id_workingduration)) {
            $workingDurationClassification = EmploymentDurationClassification::where('id', $id_workingduration)->first();
        } else {
            $workingDurationClassification = EmploymentDurationClassification::all();
        }

        return $workingDurationClassification;
    }

    public function getDataGeneralClassification($id_general = NULL)
    {
        if (!empty($id_general)) {
            $generalClassification = GeneralClassification::where('id', $id_general)->first();
        } else {
            $generalClassification = GeneralClassification::all();
        }

        return $generalClassification;
    }

    public function destroyAgeClassfication(Request $request, $id_age)
    {
        try {
            $this->setLog('info', 'Request delete data age classification ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $ageClassification = AgeClassification::find($id_age);

            if ($ageClassification) {
                $ageClassification->delete();
            } else {
                DB::rollBack();
                return false;
            }


            $this->setLog('info', 'deleted age classification data ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'Start');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error delete data age classification = ' . $exception->getMessage());
            $this->setLog('error', 'Error delete data age classification = ' . $exception->getLine());
            $this->setLog('error', 'Error delete data age classification = ' . $exception->getFile());
            $this->setLog('error', 'Error delete data age classification = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function destroyWorkingDurationClassfication(Request $request, $id_working_duration)
    {
        try {
            $this->setLog('info', 'Request delete data working duration classification ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $workingDurationClassification = EmploymentDurationClassification::find($id_working_duration);

            if ($workingDurationClassification) {
                $workingDurationClassification->delete();
            } else {
                DB::rollBack();
                return false;
            }


            $this->setLog('info', 'deleted working duration classification data ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error delete data working duration classification = ' . $exception->getMessage());
            $this->setLog('error', 'Error delete data working duration classification = ' . $exception->getLine());
            $this->setLog('error', 'Error delete data working duration classification = ' . $exception->getFile());
            $this->setLog('error', 'Error delete data working duration classification = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function destroyGeneralClassfication(Request $request, $id_general)
    {
        try {
            $this->setLog('info', 'Request delete data ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();

            $generalClassification = GeneralClassification::find($id_general);

            if ($generalClassification) {
                $generalClassification->delete();
            } else {
                DB::rollBack();
                return false;
            }

            $this->setLog('info', 'deleted general classification data ' . json_encode($request->all()));
            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error delete data general classification = ' . $exception->getMessage());
            $this->setLog('error', 'Error delete data general classification = ' . $exception->getLine());
            $this->setLog('error', 'Error delete data general classification = ' . $exception->getFile());
            $this->setLog('error', 'Error delete data general classification = ' . $exception->getTraceAsString());
            return null;
        }
    }
}
