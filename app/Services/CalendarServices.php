<?php

use App\Models\Calendar;
use App\Services\BaseServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarServices extends BaseServices
{
    protected $calendar;

    public function __construct()
    {
        $this->calendar = Calendar::with('jobCode');
    }

    public function storeCalendar(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data calendar ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();
            $this->setLog('info', 'New data calendar ' . json_encode($request->all()));

            Calendar::create([
                'title'         => $request->title,
                'link'          => $request->link,
                'start_date'    => $request->start_date,
                'end_date'      => $request->end_date,
            ]);


            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data calendar = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data calendar = ' . $exception->getLine());
            $this->setLog('error', 'Error store data calendar = ' . $exception->getFile());
            $this->setLog('error', 'Error store data calendar = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateCalendar(Request $request, $id_calendar)
    {
        try {
            $this->setLog('info', 'Request update data calendar ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();
            $this->setLog('info', 'New data calendar ' . json_encode($request->all()));

            $calendar =  Calendar::find($id_calendar);

            if ($calendar) {
                $calendar->update([
                    'title'         => $request->title,
                    'link'          => $request->link,
                    'start_date'    => $request->start_date,
                    'end_date'      => $request->end_date,
                ]);
            } else {
                DB::rollback();
                return false;
            }

            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error update data calendar = ' . $exception->getMessage());
            $this->setLog('error', 'Error update data calendar = ' . $exception->getLine());
            $this->setLog('error', 'Error update data calendar = ' . $exception->getFile());
            $this->setLog('error', 'Error update data calendar = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function getDataCalendar($id_calendar = NULL)
    {
        if (!empty($id_calendar)) {
            $calendar = $this->calendar->firstWhere('id', $id_calendar);
        } else {
            $calendar = $this->calendar->get();
        }

        return $calendar;
    }

    public function getDataCalendarPagination(Request $request)
    {
        $start = (int) $request->start;
        $size = (int)$request->size;
        $filters = json_decode($request->filters, true) ?? [];
        $sorting = json_decode($request->sorting, true) ?? [];
        $globalFilter = $request->globalFilter ?? '';

        $queryData = $this->calendar->where(function ($query) use ($filters, $globalFilter) {
            if ($globalFilter) {
                $query->where(function ($query) use ($globalFilter) {
                    $query->where('name', 'LIKE',  "%$globalFilter%");
                });
            }

            foreach ($filters as $filter) {
                $query->where($filter['id'], $filter['value']);
            }
        });

        foreach ($sorting as $sort) {
            if (isset($sort['id'])) {
                $queryData->orderBy($sort['id'], $sort['desc'] ? 'DESC' : 'ASC');
            }
        }

        $queryData = $queryData->skip($start)
            ->take($size)
            ->get();

        $queryData = $queryData->map(function ($data) {
            return [
                'id'              => $data->id,
                'name'            => $data->name,
            ];
        });

        return $queryData;
    }

    public function destroyCalendar(Request $request, $id_calendar)
    {
        try {
            $this->setLog('info', 'Request store data calendar ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();
            $this->setLog('info', 'New data calendar ' . json_encode($request->all()));

            $calendar =  Calendar::find($id_calendar);

            if ($calendar) {
                $calendar->delete();
            } else {
                DB::rollBack();
                return false;
            }


            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data calendar = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data calendar = ' . $exception->getLine());
            $this->setLog('error', 'Error store data calendar = ' . $exception->getFile());
            $this->setLog('error', 'Error store data calendar = ' . $exception->getTraceAsString());
            return null;
        }
    }
}
