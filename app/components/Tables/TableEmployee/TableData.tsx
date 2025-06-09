/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";

import "@mantine/core/styles.css";
import "@mantine/dates/styles.css";
import "mantine-react-table/styles.css";
import {
  MantineReactTable,
  MRT_ColumnFiltersState,
  MRT_PaginationState,
  MRT_RowSelectionState,
  MRT_SortingState,
  useMantineReactTable,
  type MRT_ColumnDef,
} from "mantine-react-table";
import { Box, ActionIcon } from "@mantine/core";
import { IconTrash } from "@tabler/icons-react";
import { useEffect, useState } from "react";
import { Employee } from "../../../../pages/api/admin/employee";
import axios from "axios";
import ButtonDetail from "./ButtonDetail";
import FormEmployee from "./Form";
import ButtonExport from "@/components/common/ButtonExport";
import ImportDropzone from "@/components/common/Dropzone";

const columns: MRT_ColumnDef<Employee>[] = [
  {
    accessorKey: "uuid",
    enableHiding: false,
    header: "id",
    size: 40,
  },
  {
    accessorKey: "name",
    header: "Name",
    size: 40,
  },
  {
    accessorKey: "employee_number",
    header: "Nomor Pegawai",
    size: 40,
  },
  {
    accessorKey: "company_name",
    header: "PT",
    size: 40,
  },
  {
    accessorKey: "department_name",
    header: "Dept",
    size: 40,
  },
  {
    accessorKey: "identity_card",
    header: "NIK",
    size: 40,
  },
  {
    accessorKey: "unicode",
    header: "Nama Unicode",
  },
  {
    accessorKey: "status",
    header: "Status",
  },
  {
    accessorKey: "gender",
    header: "Gender",
  },
  {
    accessorKey: "religion",
    header: "Agama",
  },
  {
    accessorKey: "education",
    header: "Pendidikan",
  },
  {
    accessorKey: "marital_status",
    header: "Status Pernikahan",
  },
  {
    accessorKey: "address",
    header: "Address",
    size: 200,
  },
  {
    accessorKey: "phone",
    header: "Phone Number",
    size: 200,
  },
  {
    accessorKey: "employee_type",
    header: "Status TK",
  },
  {
    accessorKey: "section",
    header: "Bagian (Personalia)",
  },
  {
    accessorKey: "position_code",
    header: "Kode Posisi (Personalia)",
  },
  {
    accessorKey: "roleCode",
    header: "Kode Jabatan",
  },
  {
    accessorKey: "status_twiji",
    header: "Status TWIJI",
  },
  {
    accessorKey: "schedule_type",
    header: "Status Shift",
  },
];

const TableData = () => {
  const [data, setData] = useState<Employee[]>([]);
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
  const [rowSelection, setRowSelection] = useState<MRT_RowSelectionState>({});

  const getData = async () => {
    try {
      const response = await axios.get(
        "/api/admin/employee?type=showEmployeePagination",
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

  const handleExportRows = async () => {
    try {
      const response = await axios.get("/api/admin/import/employee", {
        params: {
          uuid: JSON.stringify(rowSelection ?? []),
        },
        headers: {
          "Content-Type": "multipart/form-data",
        },
        responseType: "blob",
      });
      const url = window.URL.createObjectURL(
        new Blob([response.data], {
          type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        })
      );
      const link = document.createElement("a");
      link.href = url;
      link.setAttribute("download", "msd-latest-data-karyawan.xlsx");
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
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

  const handleExportData = async () => {
    try {
      const response = await axios.get("/api/admin/import/employee", {
        params: {
          uuid: [],
        },
        headers: {
          "Content-Type": "multipart/form-data",
        },
        responseType: "blob",
      });
      const url = window.URL.createObjectURL(
        new Blob([response.data], {
          type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        })
      );
      const link = document.createElement("a");
      link.href = url;
      link.setAttribute("download", "msd-latest-data-karyawan.xlsx");
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
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

  const handleExport = ({ value }: { value: string | null }) => {
    switch (value) {
      case "all_data":
        return handleExportData();

      case "selected_rows":
        return handleExportRows();

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
    onRowSelectionChange: setRowSelection,
    columnFilterDisplayMode: "popover",
    paginationDisplayMode: "pages",
    positionToolbarAlertBanner: "bottom",
    enableRowActions: true,
    positionActionsColumn: "last",
    state: {
      showSkeletons: isLoading,
      columnFilters,
      globalFilter,
      pagination,
      sorting,
      rowSelection,
    },
    autoResetPageIndex: false,
    manualFiltering: true,
    manualPagination: true,
    manualSorting: true,
    rowCount,
    onColumnFiltersChange: setColumnFilters,
    onGlobalFilterChange: setGlobalFilter,
    onPaginationChange: setPagination,
    onSortingChange: setSorting,
    getRowId: (row) => row.uuid,
    enablePinning: true,
    enableColumnPinning: true,
    initialState: {
      showColumnFilters: true,
      columnVisibility: {
        uuid: false,
      },
      columnPinning: {
        left: [
          "id",
          "name",
          "employee_number",
          "company_name",
          "department",
          "identity_card",
        ],
      },
    },
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
        <FormEmployee getData={getData} setIsLoading={setIsLoading} />
        <ButtonExport {...{ table, handleExport }} />
        <ImportDropzone url="/api/admin/import/employee" />
      </Box>
    ),
    renderRowActions: ({ row }) => (
      <>
        <Box className="flex flex-nowrap gap-2">
          <ButtonDetail uuid={row.id} getData={getData} />
          <FormEmployee
            uuid={row.id}
            getData={getData}
            setIsLoading={setIsLoading}
          />
          <ActionIcon variant="transparent" color="red" title="Delete">
            <IconTrash />
          </ActionIcon>
        </Box>
      </>
    ),
  });

  return <MantineReactTable table={table} />;
};

export default TableData;
