/* eslint-disable @typescript-eslint/no-explicit-any */
// hooks/useDataTable.ts
import { useState, useEffect } from "react";
import axios from "axios";
import {
  MRT_ColumnFiltersState,
  MRT_SortingState,
  MRT_PaginationState,
} from "mantine-react-table";

export const useDataTable = (
  apiEndpoint: string,
  intialPageSize = 5,
  customParams: Record<string, any> = {},
  enabled = true
) => {
  const [isLoading, setIsLoading] = useState(true);
  const [rowCount, setRowCount] = useState(0);
  const [globalFilter, setGlobalFilter] = useState("");
  const [columnFilters, setColumnFilters] = useState<MRT_ColumnFiltersState>(
    []
  );
  const [sorting, setSorting] = useState<MRT_SortingState>([]);
  const [pagination, setPagination] = useState<MRT_PaginationState>({
    pageIndex: 0,
    pageSize: intialPageSize,
  });
  const [data, setData] = useState<any[]>([]);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!enabled) return;
    const fetchData = async () => {
      try {
        setIsLoading(true);
        const response = await axios.get(apiEndpoint, {
          params: {
            start: pagination.pageIndex * pagination.pageSize,
            size: pagination.pageSize,
            filters: JSON.stringify(columnFilters),
            globalFilter: globalFilter,
            sorting: JSON.stringify(sorting),
            ...customParams,
          },
        });
        setData(response.data.data);
        setRowCount(response.data.totalCount);
        setError(null);
      } catch (err: any) {
        setError(err.response?.data?.message || "An error occurred");
      } finally {
        setIsLoading(false);
      }
    };

    fetchData();
  }, [
    columnFilters,
    globalFilter,
    pagination.pageIndex,
    pagination.pageSize,
    sorting,
    apiEndpoint,
    JSON.stringify(customParams),
    enabled,
  ]);

  return {
    isLoading,
    data,
    error,
    rowCount,
    globalFilter,
    columnFilters,
    sorting,
    pagination,
    setGlobalFilter,
    setColumnFilters,
    setSorting,
    setPagination,
  };
};

export type UseDataTableReturn = ReturnType<typeof useDataTable>;
