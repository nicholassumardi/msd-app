/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";

import "@mantine/core/styles.css";
import "@mantine/dates/styles.css"; //if using mantine date picker features
import "mantine-react-table/styles.css"; //make sure MRT styles were imported in your app root (once)
import {
  MantineReactTable,
  MRT_ColumnFiltersState,
  MRT_PaginationState,
  MRT_SortingState,
  useMantineReactTable,
  type MRT_ColumnDef,
  type MRT_Row,
} from "mantine-react-table";
import { Box } from "@mantine/core";
import { mkConfig, generateCsv, download } from "export-to-csv"; //or use your library of choice here
import axios from "axios";
import { useState, useEffect } from "react";
import ButtonDelete from "@/components/common/DeleteButton";
import { Department } from "../../../../pages/api/admin/master_data/department";
import FormDepartment from "./Form";
import ButtonExport from "@/components/common/ButtonExport";
import ImportDropzone from "@/components/common/Dropzone";

const columns: MRT_ColumnDef<Department>[] = [
  {
    accessorKey: "company_name",
    header: "Company Name",
    size: 120,
  },
  {
    accessorKey: "parent_name",
    header: "Department Name",
    size: 120,
  },
  {
    accessorKey: "name",
    header: "Name",
    size: 120,
  },
  {
    accessorKey: "code",
    header: "Code",
    size: 120,
  },
];

const csvConfig = mkConfig({
  fieldSeparator: ",",
  decimalSeparator: ".",
  useKeysAsHeaders: true,
});

const TableData = () => {
  const [data, setData] = useState<Department[]>([]);
  const [rowCount, setRowCount] = useState(0);
  const [error, setError] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [globalFilter, setGlobalFilter] = useState("");
  const [columnFilters, setColumnFilters] = useState<MRT_ColumnFiltersState>(
    []
  );
  const [sorting, setSorting] = useState<MRT_SortingState>([]);
  const [pagination, setPagination] = useState<MRT_PaginationState>({
    pageIndex: 0,
    pageSize: 10,
  });

  const getData = async () => {
    try {
      const response = await axios.get(
        "/api/admin/master_data/department?type=showPagination",
        {
          params: {
            start: pagination.pageIndex * pagination.pageSize,
            size: pagination.pageSize,
            filters: JSON.stringify(columnFilters ?? []),
            globalFilter: globalFilter ?? "",
            sorting: JSON.stringify(sorting ?? []),
          },
        }
      );

      setData(response.data.data.data);
      setRowCount(response.data.data.totalCount);
      setIsLoading(false);
    } catch (err: any) {
      if (err.response) {
        setError(err.response.data.message);
      } else if (err.request) {
        setError("No response from server");
      } else {
        setError("Unexpected error occured");
      }
    }
  };

  useEffect(() => {
    getData();
  }, [
    columnFilters,
    globalFilter,
    pagination.pageIndex,
    pagination.pageSize,
    sorting,
  ]);

  const handleExportRows = (rows: MRT_Row<Department>[]) => {
    const rowData = rows.map((row) => row.original);
    const csv = generateCsv(csvConfig)(rowData);
    download(csvConfig)(csv);
  };

  const handleExportData = () => {
    const csv = generateCsv(csvConfig)(data);
    download(csvConfig)(csv);
  };

  const handleExport = ({
    table,
    value,
  }: {
    table: any;
    value: string | null;
  }) => {
    switch (value) {
      case "all_data":
        return handleExportData();

      case "all_rows":
        return handleExportRows(table.getPrePaginationRowModel().rows);

      case "page_rows":
        return handleExportRows(table.getRowModel().rows);

      case "selected_rows":
        return handleExportRows(table.getSelectedRowModel().rows);

      default:
        return null;
    }
  };

  const table = useMantineReactTable({
    enableRowNumbers: true,
    rowNumberDisplayMode: "static",
    columns,
    data,
    enableFullScreenToggle: true,
    enableRowSelection: true,
    columnFilterDisplayMode: "popover",
    paginationDisplayMode: "pages",
    positionToolbarAlertBanner: "bottom",
    enableRowActions: true,
    positionActionsColumn: "last",
    getRowId: (row) => row.id,
    state: {
      showSkeletons: isLoading,
      columnFilters,
      globalFilter,
      pagination,
      sorting,
    },
    autoResetPageIndex: false,
    manualPagination: true,
    manualSorting: true,
    rowCount,
    onColumnFiltersChange: setColumnFilters,
    onGlobalFilterChange: setGlobalFilter,
    onPaginationChange: setPagination,
    onSortingChange: setSorting,
    mantineTableHeadProps: {
      style: {
        fontFamily: "satoshi",
        fontSize: "17px",
        fontWeight: "bold",
      },
    },
    mantineTableBodyProps: {
      style: {
        fontFamily: "satoshi",
        fontSize: "16px",
        fontWeight: 410,
      },
    },
    renderTopToolbarCustomActions: ({ table }) => (
      <Box
        style={{
          display: "flex",
          gap: "16px",
          padding: "12px",
          flexWrap: "wrap",
          zIndex: 2000,
        }}
      >
        <FormDepartment getData={getData} setIsLoading={setIsLoading} />
        <ButtonExport {...{ table, handleExport }} />
        <ImportDropzone url="/api/admin/import/department" />
      </Box>
    ),
    renderRowActions: ({ row }) => (
      <>
        <Box className="flex flex-nowrap gap-2">
          <FormDepartment
            id={row.id}
            getData={getData}
            setIsLoading={setIsLoading}
          />
          <ButtonDelete
            id={row.id}
            url="Department"
            getData={getData}
            setIsLoading={setIsLoading}
          />
        </Box>
      </>
    ),
  });

  return <MantineReactTable table={table} />;
};

export default TableData;
