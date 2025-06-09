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
import { Training } from "../../../../pages/api/admin/training";
import FormTraining from "./Form";
import { option } from "../../../../pages/types/option";

const columns: MRT_ColumnDef<Training>[] = [
  {
    accessorKey: "no_training",
    header: "No Training",
  },
  {
    accessorKey: "nip_ikw_trainee",
    header: "NIP Trainee + No IKW",
  },
  {
    accessorKey: "nip_trainee",
    header: "NIP Trainee",
  },
  {
    accessorKey: "trainee_identity_card",
    header: "No KTP",
  },
  {
    accessorKey: "trainee_name",
    header: "Nama Trainee",
  },
  {
    accessorKey: "trainee_department",
    header: "Department",
  },
  {
    accessorKey: "role_position_code_trainee",
    header: "Kode Posisi - Kode Jabatan",
  },
  {
    accessorKey: "ikw_name",
    header: "Materi Pembalejaran IKW",
  },
  {
    accessorKey: "ikw_revision",
    header: "No Revisi",
  },
  {
    accessorKey: "ikw_module_no",
    header: "No Modul Pembelajaran IKW",
  },
  {
    accessorKey: "nip_trainer",
    header: "NIP Trainer(INTIJI)",
  },
  {
    accessorKey: "trainer_identity_card",
    header: "No KTP",
  },
  {
    accessorKey: "trainer_name",
    header: "Nama Trainer (INTIJI)",
  },
  {
    accessorKey: "training_plan_date",
    header: "Tgl Perencanaan Pengajaran (M/D/Y)",
  },
  {
    accessorKey: "training_realisation_date",
    header: "Tgl Realisasi Pengajaran (M/D/Y)",
  },
  {
    accessorKey: "training_duration",
    header: "Waktu Lama Training (Menit)",
  },
  {
    accessorKey: "ticket_return_date",
    header: "Tanggal Pengembalian Tiket",
  },
  {
    accessorKey: "nip_assessor",
    header: "NIP Assessor (GUTEJI)",
  },
  {
    accessorKey: "assessor_identity_card",
    header: "NO KTP",
  },
  {
    accessorKey: "assessor_name",
    header: "Nama Assessor (GUTEJI)",
  },
  {
    accessorKey: "assessment_plan_date",
    header: "Tgl Rencana Assessment (M/D/Y)",
  },
  {
    accessorKey: "status_fa_print",
    header: "Status FA Print",
  },
  {
    accessorKey: "assessment_realisation_date",
    header: "Tgl Realisasi Assessment (M/D/Y)",
  },
  {
    accessorKey: "assessment_duration",
    header: "Waktu Lama Assessment (Menit)",
  },
  {
    accessorKey: "assessment_result",
    header: "Hasil Assessment (K, BK, RK)",
  },
  {
    accessorKey: "status",
    header: "Status",
  },
  {
    accessorKey: "description",
    header: "Keterangan",
  },
  {
    accessorKey: "status_active",
    header: "Status",
  },
];

const csvConfig = mkConfig({
  fieldSeparator: ",",
  decimalSeparator: ".",
  useKeysAsHeaders: true,
});

const TableData = () => {
  const [data, setData] = useState<Training[]>([]);
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
  const [dataIkwRevision, setDataIkwRevision] = useState<option[]>([]);
  const [dataEmployee, setDataEmployee] = useState<option[]>([]);

  const getData = async () => {
    try {
      const response = await axios.get(
        "/api/admin/training?type=showTrainingPagination",
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
      console.log(response.data.data);
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
    const getDataEmployee = async () => {
      try {
        const response = await axios.get("/api/admin/employee?type=showAll");
        const data = response.data.data.map((item: any) => ({
          value: item.uuid.toString(),
          label: `${item.name} (${item.employee_number ?? ""})`,
        }));
        setDataEmployee(data);
      } catch (err: any) {
        if (err.response) {
          setError(err.response.data.message);
        }
      }
    };
    getDataEmployee();
  }, []);

  useEffect(() => {
    const getDataIkwRevision = async () => {
      try {
        const response = await axios.get(
          `/api/admin/master_data/job_family/ikws?type=showAllRevision`
        );

        const data = response.data.data.data.map((item: any) => ({
          value: item.id.toString(),
          label:
            item.ikw_name +
            " " +
            "(" +
            item.revision_no.toString().padStart(2, "0") +
            ")",
        }));

        setDataIkwRevision(data);
      } catch (err: any) {
        if (err.response) {
          setError(err.response.data.message);
        }
      }
    };

    getDataIkwRevision();
  }, []);

  const handleExportRows = (rows: MRT_Row<Training>[]) => {
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
    initialState: {
      columnVisibility: {
        uuid: false,
      },
    },
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
        <FormTraining
          getData={getData}
          setIsLoading={setIsLoading}
          dataIkwRevision={dataIkwRevision}
          dataEmployee={dataEmployee}
        />
        <ButtonExport {...{ table, handleExport }} />
        <ImportDropzone url="/api/admin/import/training?type=importTraining" />
      </Box>
    ),
    renderRowActions: ({ row }) => (
      <>
        <FormTraining
          id={row.id}
          getData={getData}
          setIsLoading={setIsLoading}
          dataIkwRevision={dataIkwRevision}
          dataEmployee={dataEmployee}
        />
        <Box className="flex flex-nowrap gap-2"></Box>
      </>
    ),
  });

  return <MantineReactTable table={table} />;
};

export default TableData;
