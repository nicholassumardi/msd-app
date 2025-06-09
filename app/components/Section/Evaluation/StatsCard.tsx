/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";

import {
  Group,
  Paper,
  Text,
  ThemeIcon,
  SimpleGrid,
  Progress,
} from "@mantine/core";
import {
  IconUserCheck,
  IconUserX,
  IconLoader2,
  IconClockPause,
} from "@tabler/icons-react";

const StatsCards = ({ dataEvaluation }: any) => {
  const stats = [
    {
      title: "Competent Employees",
      value: dataEvaluation?.total_competent ?? 0,
      percent:
        ((dataEvaluation?.total_competent ?? 0) /
          (dataEvaluation?.total_assessment ?? 0)) *
        100,
      icon: <IconUserCheck size={24} />,
      color: "teal",
    },
    {
      title: "Non-Competent Employees",
      value: dataEvaluation?.total_non_competent ?? 0,
      percent:
        ((dataEvaluation?.total_non_competent ?? 0) /
          (dataEvaluation?.total_assessment ?? 0)) *
        100,
      icon: <IconUserX size={24} />,
      color: "red",
    },
    {
      title: "In Progress Assessments",
      value: dataEvaluation?.total_in_progress_assessment ?? 0,
      percent:
        ((dataEvaluation?.total_in_progress_assessment ?? 0) /
          (dataEvaluation?.total_assessment ?? 0)) *
        100,
      icon: <IconLoader2 size={24} className="animate-spin" />,
      color: "blue",
    },
    {
      title: "Canceled Assessments",
      value: dataEvaluation?.total_in_progress_assessment ?? 0,
      percent:
        ((dataEvaluation?.total_in_progress_assessment ?? 0) /
          (dataEvaluation?.total_assessment ?? 0)) *
        100,
      icon: <IconClockPause size={24} className="animate-pulse" />,
      color: "gray",
    },
  ];

  return (
    <SimpleGrid cols={{ base: 1, sm: 2, lg: 4 }} spacing="lg">
      {stats.map((stat, index) => (
        <Paper
          key={index}
          radius="md"
          p="md"
          className="border border-stroke dark:border-strokedark shadow-sm hover:shadow-md transition-shadow duration-300"
        >
          <Group justify="space-between" wrap="nowrap">
            <div>
              <Text size="xs" c="dimmed" className="font-medium mb-1">
                {stat.title}
              </Text>
              <Text size="xl" fw={700} className="mb-2">
                {stat.value}
              </Text>
              <Progress
                value={stat.percent}
                color={stat.color}
                size="sm"
                radius="xs"
                className="mb-2"
              />
            </div>
            <ThemeIcon size="lg" variant="light" color={stat.color} radius="md">
              {stat.icon}
            </ThemeIcon>
          </Group>
        </Paper>
      ))}
    </SimpleGrid>
  );
};

export default StatsCards;
