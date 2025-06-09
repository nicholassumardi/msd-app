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
import ButtonExport from "@/components/common/ButtonExport";
import ImportDropzone from "@/components/common/Dropzone";
import FormRki from "./Form";
import { RKI } from "../../../../pages/api/admin/rki";
import ButtonDetail from "./ButtonDetail";
import ErrorNotification from "@/components/Notifications/ErrorNotification";
import { option } from "../../../../pages/types/option";

const columns: MRT_ColumnDef<RKI>[] = [
  {
    accessorKey: "position_job_code",
    header: "Position Job Code",
  },
  {
    accessorKey: "department",
    header: "Department Code",
  },
];

const csvConfig = mkConfig({
  fieldSeparator: ",",
  decimalSeparator: ".",
  useKeysAsHeaders: true,
});

const TableData = () => {
  const [data, setData] = useState<RKI[]>([]);
  const [error, setError] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [rowCount, setRowCount] = useState(0);
  const [globalFilter, setGlobalFilter] = useState("");
  const [columnFilters, setColumnFilters] = useState<MRT_ColumnFiltersState>(
    []
  );
  const [sorting, setSorting] = useState<MRT_SortingState>([]);
  const [pagination, setPagination] = useState<MRT_PaginationState>({
    pageIndex: 0,
    pageSize: 10,
  });
  const [dataIkw, setDataIkw] = useState<option[]>([]);
  const [dataPositionCode, setDataPositionCode] = useState<option[]>([]);

  const getData = async () => {
    try {
      const response = await axios.get(
        "/api/admin/rki?type=showRKIPagination",
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
      setData(response.data.data);
      setRowCount(response.data.totalCount);
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

  useEffect(() => {
    const getDataIkw = async () => {
      try {
        const response = await axios.get(
          `/api/admin/master_data/job_family/ikws?type=showAll`
        );
        const data = response.data.data.data.map((item: any) => ({
          value: item.id.toString(),
          label: item.code,
        }));
        setDataIkw(data);
      } catch (err: any) {
        ErrorNotification({
          title: "Server Error",
          message: err.response.data.error,
        });
      }
    };
    getDataIkw();
  }, []);

  useEffect(() => {
    const getDataUserJobCode = async () => {
      try {
        const response = await axios.get(
          `/api/admin/structure?type=showUserJobCode`
        );

        const data = response.data.data.map((item: any) => ({
          value: `${item.full_code ?? ""}-${
            item.position_code_structure ?? ""
          }`,
          label: `${item.full_code ?? ""}-${
            item.position_code_structure ?? ""
          }`,
        }));

        setDataPositionCode(data);
      } catch (err: any) {
        if (err.response) {
          setError(err.response.data.message);
        }
      }
    };

    getDataUserJobCode();
  }, []);

  const handleExportRows = (rows: MRT_Row<RKI>[]) => {
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
    getRowId: (row) => row.position_job_code,
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
        <FormRki
          getData={getData}
          setIsLoading={setIsLoading}
          dataIkw={dataIkw}
          dataPositionCode={dataPositionCode}
        />
        <ButtonExport {...{ table, handleExport }} />
        <ImportDropzone url="/api/admin/import/rki" />
      </Box>
    ),
    renderRowActions: ({ row }) => (
      <>
        <ButtonDetail
          position_job_code={row.id}
          getData={getData}
          setIsLoading={setIsLoading}
          dataIkw={dataIkw}
          dataPositionCode={dataPositionCode}
        />
        <Box className="flex flex-nowrap gap-2"></Box>
      </>
    ),
  });

  return <MantineReactTable table={table} />;
};

export default TableData;
