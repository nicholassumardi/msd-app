"use client";

import { Paper, Title, Group } from "@mantine/core";
import { Evaluation } from "@/evaluation/page";
import dynamic from "next/dynamic";

const BarChartEval = dynamic(() => import("../../Chart/BarChartEval"), {
  ssr: false,
});

interface FormComponent {
  dataEvaluation: Evaluation | null;
}

const BarChart: React.FC<FormComponent> = ({ dataEvaluation }) => {
  // const [timeframe, setTimeframe] = useState("monthly");

  return (
    <Paper
      radius="md"
      p="md"
      className="col-span-12 xl:col-span-6 border border-stroke dark:border-strokedark shadow-sm"
    >
      <Group justify="space-between" className="mb-6">
        <Title order={4} className="text-black dark:text-white">
          Training Results
        </Title>
      </Group>
      <div className="h-64">
        <BarChartEval dataEvaluation={dataEvaluation} />
      </div>
    </Paper>
  );
};

export default BarChart;
