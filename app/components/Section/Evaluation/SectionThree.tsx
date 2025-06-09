"use client";

import { Paper, Title, Group } from "@mantine/core";
import dynamic from "next/dynamic";
import { Evaluation } from "@/evaluation/page";

interface FormComponent {
  dataEvaluation: Evaluation | null;
}

// Import Chart.js dynamically
const PolarAreaChart = dynamic(() => import("../../Chart/PolarChartEval"), {
  ssr: false,
});

const PolarChart: React.FC<FormComponent> = ({ dataEvaluation }) => {
  return (
    <Paper
      radius="md"
      p="md"
      className="col-span-12 xl:col-span-8 border border-stroke dark:border-strokedark shadow-sm"
    >
      <Group justify="space-between" className="mb-4 flex-wrap">
        <Title order={4} className="text-black dark:text-white">
          Assessment Progress
        </Title>
      </Group>
      <div className="h-80">
        <PolarAreaChart dataEvaluation={dataEvaluation} />
      </div>
    </Paper>
  );
};

export default PolarChart;
