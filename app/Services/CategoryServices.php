<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryServices extends BaseServices
{
    protected $category;

    public function __construct()
    {
        $this->category = Category::with('jobCode');
    }

    public function storeCategory(Request $request)
    {
        try {
            $this->setLog('info', 'Request store data category ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();
            $this->setLog('info', 'New data category ' . json_encode($request->all()));

            Category::create([
                'name'      => $request->name,
            ]);


            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data category = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data category = ' . $exception->getLine());
            $this->setLog('error', 'Error store data category = ' . $exception->getFile());
            $this->setLog('error', 'Error store data category = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function updateCategory(Request $request, $id_category)
    {
        try {
            $this->setLog('info', 'Request update data category ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();
            $this->setLog('info', 'New data category ' . json_encode($request->all()));

            $category =  Category::find($id_category);

            if ($category) {
                $category->update([
                    'name'      => $request->name,
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
            $this->setLog('error', 'Error update data category = ' . $exception->getMessage());
            $this->setLog('error', 'Error update data category = ' . $exception->getLine());
            $this->setLog('error', 'Error update data category = ' . $exception->getFile());
            $this->setLog('error', 'Error update data category = ' . $exception->getTraceAsString());
            return null;
        }
    }

    public function getCategory($id_category = NULL)
    {
        if (!empty($id_category)) {
            $category = $this->category->firstWhere('id', $id_category);
        } else {
            $category = $this->category->get();
        }

        return $category;
    }

    public function getDataCategoryPagination(Request $request)
    {
        $start = (int) $request->start;
        $size = (int)$request->size;
        $filters = json_decode($request->filters, true) ?? [];
        $sorting = json_decode($request->sorting, true) ?? [];
        $globalFilter = $request->globalFilter ?? '';

        $queryData = $this->category->where(function ($query) use ($filters, $globalFilter) {
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

    public function destroyCategory(Request $request, $id_category)
    {
        try {
            $this->setLog('info', 'Request store data category ' . json_encode($request->all()));
            $this->setLog('info', 'Start');

            DB::beginTransaction();
            $this->setLog('info', 'New data category ' . json_encode($request->all()));

            $category =  Category::find($id_category);

            if ($category) {
                $category->delete();
            } else {
                DB::rollBack();
                return false;
            }


            DB::commit();
            $this->setLog('info', 'End');

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->setLog('error', 'Error store data category = ' . $exception->getMessage());
            $this->setLog('error', 'Error store data category = ' . $exception->getLine());
            $this->setLog('error', 'Error store data category = ' . $exception->getFile());
            $this->setLog('error', 'Error store data category = ' . $exception->getTraceAsString());
            return null;
        }
    }
}
