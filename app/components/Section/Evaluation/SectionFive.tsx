"use client";

import { useState } from "react";
import { Paper, Title, Group, SegmentedControl, Text } from "@mantine/core";
import dynamic from "next/dynamic";
import HeadLeaderDrawer from "./SecondaryContent";

// Import Chart.js dynamically
const RadarChart = dynamic(() => import("../../Chart/DoughnutChartEval"), {
  ssr: false,
});

const CompetencyAssessment = () => {
  const [department, setDepartment] = useState("all");

  return (
    <Paper
      radius="md"
      p="md"
      className="col-span-12 border border-stroke dark:border-strokedark shadow-sm"
    >
      <Group justify="space-between" className="mb-4">
        <div>
          <Title order={4} className="text-black dark:text-white">
            Competency Assessment by Skill
          </Title>
          <Text size="sm" c="dimmed" className="mt-1">
            Performance across different skill categories
          </Text>
        </div>
        <SegmentedControl
          size="xs"
          data={[
            { value: "all", label: "All Departments" },
            { value: "it", label: "IT" },
            { value: "finance", label: "Finance" },
            { value: "marketing", label: "Marketing" },
          ]}
          value={department}
          onChange={setDepartment}
        />
      </Group>
      <div className="h-80">
        <HeadLeaderDrawer />
        <RadarChart />
      </div>
    </Paper>
  );
};

export default CompetencyAssessment;
