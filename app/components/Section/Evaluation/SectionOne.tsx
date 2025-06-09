"use client";

import { Paper, Title, Group } from "@mantine/core";
import dynamic from "next/dynamic";
import { Evaluation } from "@/evaluation/page";
interface EvaluationComponent {
  dataEvaluation: Evaluation | null;
}
// Import Chart.js dynamically to avoid SSR issues
const DoughnutChartEval = dynamic(
  () => import("../../Chart/DoughnutChartEval"),
  {
    ssr: false,
  }
);

const DoughnutChart: React.FC<EvaluationComponent> = ({ dataEvaluation }) => {
  return (
    <Paper
      className="col-span-12 xl:col-span-6 border border-stroke dark:border-strokedark shadow-sm"
      radius="md"
      p="md"
    >
      <Group justify="space-between" className="mb-6">
        <Title order={4} className="text-black dark:text-white">
          Employee Competency Overview
        </Title>
      </Group>
      <div className="flex justify-center items-center h-64">
        <DoughnutChartEval dataEvaluation={dataEvaluation} />
      </div>
    </Paper>
  );
};

export default DoughnutChart;
