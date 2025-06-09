/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";

import {
  Paper,
  Title,
  Table,
  Badge,
  Avatar,
  Text,
  Group,
  ActionIcon,
  Tooltip,
} from "@mantine/core";
import { IconInfoCircle } from "@tabler/icons-react";
import { useState } from "react";
import EmployeeIKWToTrainDrawer from "./Drawer";
import { option } from "../../../../pages/types/option";
import { UseDataTableReturn } from "../../../../hooks/useDataTableState";

interface FormProps {
  dataIKWToTrain: UseDataTableReturn;
  handleGetDataTrainer: () => Promise<void>;
  dataTrainer: option[];
  form: any;
  dataTrainerIKW: option[];
  dataTraineeByTrainer: UseDataTableReturn;
}

const FormIKWToTrain: React.FC<FormProps> = ({
  dataIKWToTrain,
  handleGetDataTrainer,
  dataTrainer,
  form,
  dataTrainerIKW,
  dataTraineeByTrainer,
}) => {
  const [opened, setOpened] = useState<boolean>(false);
  const colors = [
    "blue",
    "teal",
    "violet",
    "orange",
    "red",
    "green",
    "purple",
    "pink",
  ];

  return (
    <>
      <Paper
        radius="md"
        p="md"
        className="col-span-12 xl:col-span-4 border border-stroke dark:border-strokedark shadow-sm"
      >
        <Group justify="space-between" className="mb-4">
          <Title order={4} className="text-black dark:text-white">
            Employee Need to Train
          </Title>
          <Tooltip label="List of IKW to train" withArrow position="top">
            <ActionIcon variant="subtle" color="gray">
              <IconInfoCircle size={18} />
            </ActionIcon>
          </Tooltip>
        </Group>
        <div className="overflow-x-auto">
          <Table striped highlightOnHover>
            <Table.Thead>
              <Table.Tr>
                <Table.Th>Employee</Table.Th>
                <Table.Th>IKW Name</Table.Th>
                <Table.Th>IKW Code</Table.Th>
              </Table.Tr>
            </Table.Thead>
            <Table.Tbody>
              {dataIKWToTrain?.data.map((employee, i) => (
                <Table.Tr key={i}>
                  <Table.Td>
                    <Group gap="sm">
                      <Avatar
                        size="sm"
                        color={
                          colors[Math.floor(Math.random() * colors.length)]
                        }
                      >
                        {employee.employee_name
                          .split(" ")
                          .map((n: any) => n[0])
                          .join("")}
                      </Avatar>
                      <div>
                        <Text size="sm" fw={500}>
                          {employee.employee_name}
                        </Text>
                        <Text size="xs" c="dimmed">
                          {employee.employee_type}
                        </Text>
                      </div>
                    </Group>
                  </Table.Td>
                  <Table.Td>
                    <Badge
                      color={colors[Math.floor(Math.random() * colors.length)]}
                      variant="light"
                    >
                      {employee.ikw_name}
                    </Badge>
                  </Table.Td>
                  <Table.Td>
                    <Badge
                      color={colors[Math.floor(Math.random() * colors.length)]}
                      variant="light"
                    >
                      {employee.ikw_code}
                    </Badge>
                  </Table.Td>
                </Table.Tr>
              ))}
            </Table.Tbody>
          </Table>
          <div className="mt-4 text-center">
            <Text
              size="sm"
              c="blue"
              className="hover:underline cursor-pointer"
              onClick={() => setOpened(true)}
            >
              View all data
            </Text>
          </div>
        </div>
      </Paper>
      <EmployeeIKWToTrainDrawer
        {...{
          opened,
          setOpened,
          dataIKWToTrain,
          handleGetDataTrainer,
          dataTrainer,
          form,
          dataTrainerIKW,
          dataTraineeByTrainer,
        }}
      />
    </>
  );
};

export default FormIKWToTrain;
