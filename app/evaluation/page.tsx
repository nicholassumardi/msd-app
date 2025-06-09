/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";

import DefaultLayout from "@/components/Layouts/DefaultLayout";
import { ModalsProvider } from "@mantine/modals";
import "@mantine/core/styles.css";
import { Button, Select, Text, Group, Container, Popover } from "@mantine/core";
import {
  IconDownload,
  IconEye,
  IconSearch,
  IconRefresh,
  IconFilter,
} from "@tabler/icons-react";
import Link from "next/link";
import DoughnutChart from "@/components/Section/Evaluation/SectionOne";
import BarChart from "@/components/Section/Evaluation/SectionTwo";
import PolarChart from "@/components/Section/Evaluation/SectionThree";
import StatsCards from "@/components/Section/Evaluation/StatsCard";
import { useEffect, useRef, useState } from "react";
import ErrorNotification from "@/components/Notifications/ErrorNotification";
import axios from "axios";
import FormIKWToTrain from "@/components/Section/Evaluation/SectionFour";
import { option } from "../../pages/types/option";
import { useForm } from "@mantine/form";
import { useDataTable } from "../../hooks/useDataTableState";

export type Evaluation = {
  total_competent: number;
  total_non_competent: number;
  total_remedial_competent: number;
  total_in_progress_assessment: number;
  total_assessment: number;
  cancel_assessment: number;
  monthly_trends: MonthlyTrend[];
  training_efficiency: [];
};

type MonthlyTrend = {
  year: number;
  month: number;
  competent: number;
  non_competent: number;
  remedial: number;
  avg_days_to_assessment: number;
};

export type IKWToTrain = {
  position_job_code: string;
  ikw_id: string;
  employee_name: string;
  employee_type: string;
  employee_department: string;
  ikw_name: string;
  ikw_code: string;
  training_time: number;
};

export default function Evaluation() {
  const form = useForm({
    initialValues: {
      trainer_id: "",
      ikw_id: "",
      company_id: "",
      department_id: "",
      trainer_name: "",
      trainer_nip: "",
    },
    validate: {},
  });
  const [dataEvaluation, setDataEvaluation] = useState<Evaluation | null>(null);
  const dataIKWToTrain = useDataTable(
    "/api/admin/evaluation?type=showIKWToTrain",
    5,
    {
      department_id: form.values.department_id,
    }
  );
  const [dataTrainer, setDataTrainer] = useState<option[]>([]);
  const [dataTrainerIKW, setDataTrainerIKW] = useState<option[]>([]);
  const dataTraineeByTrainer = useDataTable(
    "/api/admin/evaluation?type=showTraineeByTrainerIKW",
    5,
    { ikw_id: form.values.ikw_id },
    !!form.values.ikw_id
  );
  const [dataCompany, setDataCompany] = useState<option[]>([]);
  const [dataDepartment, setDataDepartment] = useState<option[]>([]);
  const [idDepartment, setIdDepartment] = useState<string | null>("");
  const [error, setError] = useState<string | null>(null);
  const [isPopoverOpen, setIsPopoverOpen] = useState(false);
  const dataTrainerCalled = useRef(false);

  const handleGetDataTrainer = async () => {
    if (dataTrainerCalled.current) return;
    dataTrainerCalled.current = true;
    try {
      const response = await axios.get("/api/admin/employee?type=showAll");
      const data = response.data.data.map((item: any) => ({
        value: item.uuid.toString(),
        label: `${item.name} (${item.employee_number ?? ""})`,
      }));
      setDataTrainer(data);
    } catch (err: any) {
      if (err.response) {
        setError(err.response.data.message);
      }
    }
  };

  const handleGetDataIKWByTrainer = async () => {
    try {
      const response = await axios.get(
        `/api/admin/evaluation?type=showEligibleIKWTrainer`,
        {
          params: {
            trainer_id: form.values.trainer_id,
          },
        }
      );
      const data = response.data.data.map((item: any) => ({
        value: item.id.toString(),
        label: `${item.name} (${item.code ?? ""})`,
      }));
      setDataTrainerIKW(data);
    } catch (err: any) {
      if (err.response) {
        setError(err.response.data.message);
      }
    }
  };

  const getDataCompany = async () => {
    try {
      const response = await axios.get("/api/admin/master_data/company");
      const data = response.data.data.data.map((item: any) => ({
        value: item.id.toString(),
        label: item.name,
      }));
      setDataCompany(data);
    } catch (err: any) {
      ErrorNotification({
        title: "Server Error",
        message: err.response.data.error,
      });
    }
  };

  const getDataDepartment = async () => {
    try {
      const id = form.values.company_id;
      const response = await axios.get(
        `/api/admin/master_data/department/${id}?type=showByCompany`
      );
      const data = response.data.data.map((item: any) => ({
        value: item.id.toString(),
        label: item.code,
      }));
      setDataDepartment(data);
    } catch (err: any) {
      ErrorNotification({
        title: "Server Error",
        message: err.response.data.error,
      });
    }
  };

  const handleApplyFilter = () => {
    form.setFieldValue("department_id", idDepartment ?? "");
  };

  useEffect(() => {
    getDataCompany();
  }, []);

  useEffect(() => {
    if (form.values.company_id) {
      getDataDepartment();
    }
  }, [form.values.company_id, form.values.department_id]);

  useEffect(() => {
    if (form.values.trainer_id) {
      form.setFieldValue("ikw_id", "");
      handleGetDataIKWByTrainer();
    }
  }, [form.values.trainer_id]);

  useEffect(() => {
    const handleDataEvaluation = async () => {
      try {
        const response = await axios.get(
          `/api/admin/evaluation?type=showDataVisualization`
        );
        setDataEvaluation(response.data.data);
      } catch (err: any) {
        ErrorNotification({
          title: "Server Error",
          message: err.response.data.error,
        });
      }
    };

    handleDataEvaluation();
  }, []);

  return (
    <>
      <DefaultLayout>
        <ModalsProvider>
          <Container fluid className="px-0">
            {/* Header Section */}
            <div className="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
              <h2 className="text-2xl font-semibold text-black dark:text-white">
                Evaluation Dashboard
              </h2>
              <Group gap="sm">
                <Popover
                  width={300}
                  position="bottom"
                  withArrow
                  shadow="md"
                  closeOnClickOutside={false}
                  opened={isPopoverOpen}
                  onClose={() => setIsPopoverOpen(false)}
                >
                  <Popover.Target>
                    <Button
                      size="sm"
                      variant="light"
                      color="violet"
                      radius="md"
                      leftSection={<IconFilter size={16} />}
                      className="shadow-sm"
                      onClick={() => setIsPopoverOpen((o) => !o)}
                    >
                      <Text size="sm">Filter</Text>
                    </Button>
                  </Popover.Target>
                  <Popover.Dropdown>
                    <Select
                      placeholder="Select Company"
                      size="sm"
                      radius="md"
                      searchable
                      clearable
                      rightSection={<IconSearch size={16} />}
                      className="shadow-sm"
                      onChange={(idCompany) => {
                        form.setFieldValue("company_id", idCompany ?? "");
                      }}
                      data={dataCompany}
                    />
                    <Select
                      mt="sm"
                      placeholder="Select Department"
                      size="sm"
                      radius="md"
                      searchable
                      clearable
                      rightSection={<IconSearch size={16} />}
                      className="shadow-sm"
                      onChange={(idDepartment) => setIdDepartment(idDepartment)}
                      data={dataDepartment}
                    />
                    <Button
                      mt="md"
                      size="sm"
                      variant="filled"
                      color="violet"
                      radius="md"
                      fullWidth
                      onClick={() => {
                        handleApplyFilter();
                        setIsPopoverOpen(false);
                      }}
                    >
                      Apply
                    </Button>
                  </Popover.Dropdown>
                </Popover>
                <Button
                  size="sm"
                  variant="outline"
                  color="gray"
                  radius="md"
                  leftSection={<IconRefresh size={16} />}
                  className="shadow-sm"
                >
                  <Text size="sm">Refresh</Text>
                </Button>
                <Button
                  size="sm"
                  variant="outline"
                  color="gray"
                  radius="md"
                  leftSection={<IconDownload size={16} />}
                  className="shadow-sm"
                >
                  <Text size="sm">Export</Text>
                </Button>
                <Button
                  size="sm"
                  variant="filled"
                  color="violet"
                  radius="md"
                  leftSection={<IconEye size={16} />}
                  className="shadow-sm"
                  component={Link}
                  href="/evaluation/details"
                >
                  <Text size="sm">Details</Text>
                </Button>
              </Group>
            </div>

            {/* Stats Cards */}
            <StatsCards dataEvaluation={dataEvaluation} />

            {/* Charts Section */}
            <div className="mt-6 grid grid-cols-12 gap-5">
              <BarChart dataEvaluation={dataEvaluation} />
              <DoughnutChart dataEvaluation={dataEvaluation} />
              <PolarChart dataEvaluation={dataEvaluation} />
              <FormIKWToTrain
                {...{
                  dataIKWToTrain,
                  handleGetDataTrainer,
                  dataTrainer,
                  form,
                  dataTrainerIKW,
                  dataTraineeByTrainer,
                }}
              />
            </div>
          </Container>
        </ModalsProvider>
      </DefaultLayout>
    </>
  );
}
