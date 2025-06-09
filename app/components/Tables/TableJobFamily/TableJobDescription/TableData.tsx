/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";

import "@mantine/core/styles.css";
import "@mantine/dates/styles.css"; //if using mantine date picker features
import "mantine-react-table/styles.css"; //make sure MRT styles were imported in your app root (once)
import {
  MantineReactTable,
  useMantineReactTable,
  type MRT_ColumnDef,
  type MRT_Row,
} from "mantine-react-table";
import { Box } from "@mantine/core";
import { mkConfig, generateCsv, download } from "export-to-csv"; //or use your library of choice here
import axios from "axios";
import { useState, useEffect, useRef } from "react";
import ButtonDelete from "@/components/common/DeleteButton";
import { JobDescription } from "../../../../../pages/api/admin/master_data/job_family/job_description";
import FormJobDescription from "./Form";
import ButtonExport from "@/components/common/ButtonExport";

const columns: MRT_ColumnDef<JobDescription>[] = [
  {
    accessorKey: "job_code_code",
    header: "Role Code",
    size: 120,
  },
  {
    accessorKey: "code",
    header: "UT Code",
    size: 120,
  },
  {
    accessorKey: "description",
    header: "Job Description",

    size: 120,
  },
];

const csvConfig = mkConfig({
  fieldSeparator: ",",
  decimalSeparator: ".",
  useKeysAsHeaders: true,
});

const TableData = () => {
  const [data, setData] = useState<JobDescription[]>([]);
  const [rowCount, setRowCount] = useState(0);
  const [error, setError] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const hasFetchedData = useRef(false);

  const getData = async () => {
    try {
      const response = await axios.get(
        "/api/admin/master_data/job_family/job_description"
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
    if (!hasFetchedData.current) {
      getData();
      hasFetchedData.current = true;
    }
  }, []);

  const handleExportRows = (rows: MRT_Row<JobDescription>[]) => {
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
    state: {
      showSkeletons: isLoading,
    },
    autoResetPageIndex: false,
    manualPagination: true,
    manualSorting: true,
    rowCount,
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
    getRowId: (row) => row.id,
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
        <FormJobDescription getData={getData} setIsLoading={setIsLoading} />
        <ButtonExport {...{ table, handleExport }} />
      </Box>
    ),
    renderRowActions: ({ row }) => (
      <>
        <Box className="flex flex-nowrap gap-2">
          <FormJobDescription
            id={row.id}
            getData={getData}
            setIsLoading={setIsLoading}
          />
          <ButtonDelete
            id={row.id}
            url="job_description"
            isJobFamily={true}
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
