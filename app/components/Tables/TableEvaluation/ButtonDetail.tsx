/* eslint-disable @typescript-eslint/no-explicit-any */
import ErrorNotification from "@/components/Notifications/ErrorNotification";
import { Drawer, ActionIcon, Text, Tabs } from "@mantine/core";
import { IconInfoSquare } from "@tabler/icons-react";
import axios from "axios";
import { useState } from "react";
import DataGeneral from "./DataGeneral";
import DetailRKITable from "./DetailRki";

type FormComponent = {
  id: string;
};

export type TrainingGeneral = {
  planning: number;
  realisation: number;
  cancel: number;
  on_progress: number;
  planning_assesment: number;
  realisation_assesment: number;
  cancel_assesment: number;
  on_progress_assesment: number;
  competent_assessment: number;
  not_competent_assessment: number;
};

export type DetailRKI = {
  ikw_name: string;
  ikw_code: string;
  revisions: Revision[];
  assessment_result: string;
};

type Revision = {
  revision_no: number;
};

export type PaginationData = {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
};

const ButtonDetail: React.FC<FormComponent> = ({ id }) => {
  const [openedDrawer, setOpenedDrawer] = useState(false);
  const [dataEvaluationRKI, setDataEvaluationRKI] =
    useState<TrainingGeneral | null>(null);
  const [dataEvaluationGeneral, setDataEvaluationGeneral] =
    useState<TrainingGeneral | null>(null);
  const [dataDetailRKI, setDataDetailRKI] = useState<DetailRKI[]>([]);
  const [dataMaxRevision, setDataMaxRevision] = useState(0);
  const [pagination, setPagination] = useState<PaginationData>({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
  });

  const handleDataEvaluationRKI = async () => {
    try {
      const response = await axios.get(
        `/api/admin/evaluation/${id}?type=showTrainingRKI`
      );
      setDataEvaluationRKI(response.data.data.data);
    } catch (err: any) {
      ErrorNotification({
        title: "Server Error",
        message: err.response.data.error,
      });
    }
  };

  const handleDataEvaluationGeneral = async () => {
    try {
      const response = await axios.get(
        `/api/admin/evaluation/${id}?type=showTrainingGeneral`
      );

      setDataEvaluationGeneral(response.data.data.data);
    } catch (err: any) {
      ErrorNotification({
        title: "Server Error",
        message: err.response.data.error,
      });
    }
  };

  const handleDetailRKI = async () => {
    try {
      const response = await axios.get(
        `/api/admin/evaluation/${id}?type=showDetailRKI`,
        {
          params: {
            current_page: pagination.current_page,
          },
        }
      );

      setDataDetailRKI(response.data.data.data);
      setDataMaxRevision(response.data.data.max_revision);
      setPagination(
        response.data.data.pagination ?? {
          current_page: 1,
          last_page: 1,
          per_page: 10,
          total: 0,
        }
      );
    } catch (err: any) {
      ErrorNotification({
        title: "Server Error",
        message: err.response.data.error,
      });
    }
  };

  return (
    <>
      <ActionIcon
        variant="transparent"
        onClick={() => {
          setOpenedDrawer(true);
          handleDataEvaluationRKI();
          handleDataEvaluationGeneral();
          handleDetailRKI();
        }}
        color="blue"
        title="See Details"
      >
        <IconInfoSquare />
      </ActionIcon>

      <Drawer
        position="bottom"
        radius="md"
        size="100%"
        opened={openedDrawer}
        onClose={() => setOpenedDrawer(false)}
        transitionProps={{ transition: "slide-up" }}
        title={
          <div className="mb-4">
            <Text size="xl" fw={900} className="font-satoshi text-2xl">
              Training Evaluation Overview
            </Text>
            <Text c="dimmed" size="sm">
              Detailed breakdown of training program metrics
            </Text>
          </div>
        }
      >
        <Tabs
          variant="pills"
          radius="md"
          defaultValue="general"
          className="font-satoshi font-bold"
        >
          <Tabs.List grow>
            <Tabs.Tab value="general">General</Tabs.Tab>
            <Tabs.Tab value="detail">Detail</Tabs.Tab>
          </Tabs.List>

          <Tabs.Panel value="general">
            <DataGeneral {...{ dataEvaluationGeneral, dataEvaluationRKI }} />
          </Tabs.Panel>
          <Tabs.Panel value="detail">
            <DetailRKITable
              {...{
                dataDetailRKI,
                dataMaxRevision,
                pagination,
                setPagination,
                handleDetailRKI,
              }}
            />
          </Tabs.Panel>
        </Tabs>
      </Drawer>
    </>
  );
};

export default ButtonDetail;
