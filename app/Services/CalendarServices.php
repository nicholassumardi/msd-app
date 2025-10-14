<?php

namespace App\Services;

use App\Models\Calendar;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CalendarServices extends BaseServices
{
    protected $calendar;

    public function __construct()
    {
        $this->calendar = Calendar::query();
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
                'start_date'    => $this->parseDateTime($request->start),
                'end_date'      => $this->parseDateTime($request->end),
                'all_day'       => (bool)$request->all_day ?? false,
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
                $data = [
                    'title'   => $request->title,
                    'link'    => $request->link,
                ];

                if (!is_null($request->start)) {
                    $data['start_date'] = $this->parseDateTime($request->start);
                }

                if (!is_null($request->end)) {
                    $data['end_date'] = $this->parseDateTime($request->end);
                }

                if (!is_null($request->all_day)) {
                    $data['start_date'] = $this->parseDateTime($request->start);
                }



                $calendar->update($data);
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


    public function getDataCalendarWeekly()
    {
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);

        $weekRange = $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M Y');

        $data = $this->calendar->whereBetween('start_date', [$startOfWeek, $endOfWeek])->get();

        return [
            'data'      => $data,
            'weekRange' => $weekRange,
        ];
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
