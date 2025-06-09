/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";

import "@mantine/core/styles.css";
import "@mantine/dates/styles.css"; //if using mantine date picker features
import "mantine-react-table/styles.css";
import {
  MantineReactTable,
  MRT_ColumnFiltersState,
  MRT_PaginationState,
  MRT_SortingState,
  useMantineReactTable,
  type MRT_ColumnDef,
  type MRT_Row,
} from "mantine-react-table";
import { mkConfig, generateCsv, download } from "export-to-csv";
import { Box } from "@mantine/core";
import ButtonExport from "@/components/common/ButtonExport";

const csvConfig = mkConfig({
  fieldSeparator: ",",
  decimalSeparator: ".",
  useKeysAsHeaders: true,
});

interface DataTableProps<T extends Record<string, any>> {
  columns: MRT_ColumnDef<T>[];
  data: any[];
  error?: string | null;
  isLoading: boolean;
  rowCount: number;
  globalFilter: string;
  columnFilters: MRT_ColumnFiltersState;
  sorting: MRT_SortingState;
  pagination: MRT_PaginationState;
  setGlobalFilter: React.Dispatch<React.SetStateAction<string>>;
  setColumnFilters: React.Dispatch<
    React.SetStateAction<MRT_ColumnFiltersState>
  >;
  setSorting: React.Dispatch<React.SetStateAction<MRT_SortingState>>;
  setPagination: React.Dispatch<React.SetStateAction<MRT_PaginationState>>;
  exportFileName?: string;
  getRowId?: (row: T) => string;
}

export const TableData = <T extends Record<string, any>>({
  columns,
  data,
  error,
  isLoading,
  rowCount,
  globalFilter,
  columnFilters,
  sorting,
  pagination,
  setGlobalFilter,
  setColumnFilters,
  setSorting,
  setPagination,
  exportFileName = "export",
  getRowId = (row) => row.id,
}: DataTableProps<T>) => {
  const handleExportRows = (rows: MRT_Row<T>[]) => {
    const rowData = rows.map((row) => row.original);
    const csv = generateCsv(csvConfig)(rowData);
    download({ ...csvConfig, filename: exportFileName })(csv);
  };

  const handleExportData = () => {
    const csv = generateCsv(csvConfig)(data);
    download({ ...csvConfig, filename: exportFileName })(csv);
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
    columns,
    data,
    enableRowNumbers: true,
    rowNumberDisplayMode: "static",
    enableFullScreenToggle: true,
    enableRowSelection: true,
    columnFilterDisplayMode: "popover",
    paginationDisplayMode: "pages",
    positionToolbarAlertBanner: "bottom",
    getRowId,
    state: {
      showAlertBanner: !!error,
      showSkeletons: isLoading,
      columnFilters,
      globalFilter,
      pagination,
      sorting,
    },
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
        <ButtonExport {...{ table, handleExport }} />
      </Box>
    ),
  });

  return <MantineReactTable table={table} />;
};
