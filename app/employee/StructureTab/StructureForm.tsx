/* eslint-disable @typescript-eslint/no-explicit-any */
import {
  Button,
  Modal,
  Text,
  TextInput,
  Group,
  Stack,
  Paper,
  Divider,
  Textarea,
  Select,
} from "@mantine/core";
import {
  IconUsersGroup,
  IconCalendar,
  IconBriefcase,
  IconHash,
  IconTextCaption,
  IconSearch,
} from "@tabler/icons-react";
import React, { useEffect } from "react";
import { useEmployeeDataContext } from "../../../context/EmployeeCentre";
import { DatePickerInput } from "@mantine/dates";
import useEmployeeData from "../../../hooks/EmployeeCentre";

export const StructureAddRequest = ({
  user_structure_mapping_id,
}: {
  user_structure_mapping_id: any;
}) => {
  const {
    isOpen,
    closeModal,
    formUserJobCode: form,
    handleSubmitStructureMappingRequest,
  } = useEmployeeDataContext();
  useEffect(() => {
    if (user_structure_mapping_id) {
      form.setValues({ user_structure_mapping_id: user_structure_mapping_id });
    }
  }, []);
  const handleCloseModal = () => {
    closeModal();
    form.reset();
  };

  return (
    <Modal
      opened={isOpen("Request")}
      onClose={handleCloseModal}
      radius="lg"
      size="xl"
      centered
      transitionProps={{ transition: "slide-up", duration: 300 }}
      title={
        <Group gap="sm" align="center">
          <div className="p-2 bg-violet-100 dark:bg-violet-900/30 rounded-lg">
            <IconUsersGroup
              size={20}
              className="text-violet-600 dark:text-violet-400"
            />
          </div>
          <div>
            <Text
              size="lg"
              fw={600}
              className="font-satoshi text-gray-900 dark:text-gray-100"
            >
              Request Employee for Structure
            </Text>
            <Text size="sm" c="dimmed" className="font-satoshi">
              Map employee to organizational structure
            </Text>
          </div>
        </Group>
      }
      classNames={{
        content: "bg-white dark:bg-neutral-900",
        header: "border-b border-gray-100 dark:border-gray-800 pb-4 mb-0",
        body: "p-0",
      }}
    >
      <form
        onSubmit={form.onSubmit((values: any) => {
          handleSubmitStructureMappingRequest(values);
        })}
      >
        <Stack gap="lg" className="p-6">
          {/* Structure Details Section */}
          <Paper
            p="md"
            radius="md"
            withBorder
            className="border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-neutral-800/50"
          >
            <Group gap="xs" mb="md">
              <IconBriefcase
                size={16}
                className="text-violet-600 dark:text-violet-400"
              />
              <Text
                size="sm"
                fw={500}
                className="text-gray-700 dark:text-gray-300"
              >
                Structure & Position Details
              </Text>
            </Group>

            <Stack gap="md">
              <Group grow>
                <TextInput
                  withAsterisk
                  label="Structure ID"
                  placeholder="Enter structure ID"
                  size="md"
                  radius="md"
                  leftSection={<IconHash size={16} />}
                  classNames={{
                    input:
                      "border-gray-200 dark:border-gray-700 bg-white dark:bg-neutral-800",
                  }}
                  key={form.key("id_structure")}
                  {...form.getInputProps("id_structure")}
                />
                <TextInput
                  label="Group"
                  placeholder="Enter group (optional)"
                  size="md"
                  radius="md"
                  leftSection={<IconUsersGroup size={16} />}
                  classNames={{
                    input:
                      "border-gray-200 dark:border-gray-700 bg-white dark:bg-neutral-800",
                  }}
                  key={form.key("group")}
                  {...form.getInputProps("group")}
                />
              </Group>

              <DatePickerInput
                label="Assignment Date"
                placeholder="Select assignment date"
                size="md"
                radius="md"
                leftSection={<IconCalendar size={16} />}
                classNames={{
                  input:
                    "border-gray-200 dark:border-gray-700 bg-white dark:bg-neutral-800",
                }}
                key={form.key("assign_date")}
                {...form.getInputProps("assign_date")}
              />
              <Group grow>
                <Textarea
                  label="Description"
                  placeholder="Enter description (optional)"
                  size="md"
                  radius="md"
                  resize
                  leftSection={<IconTextCaption size={16} />}
                  classNames={{
                    input:
                      "border-gray-200 dark:border-gray-700 bg-white dark:bg-neutral-800",
                  }}
                  key={form.key("description")}
                  {...form.getInputProps("description")}
                ></Textarea>
              </Group>
            </Stack>
          </Paper>
        </Stack>

        <Divider className="border-gray-100 dark:border-gray-800" />

        {/* Action Buttons */}
        <Group justify="space-between" className="p-6">
          <Text size="xs" c="dimmed" className="font-satoshi">
            All required fields must be filled before saving
          </Text>

          <Group gap="sm">
            <Button
              variant="subtle"
              color="gray"
              size="md"
              radius="md"
              onClick={handleCloseModal}
              classNames={{
                root: "font-satoshi hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors",
              }}
            >
              Cancel
            </Button>
            <Button
              variant="filled"
              color="violet"
              size="md"
              radius="md"
              type="submit"
              classNames={{
                root: "font-satoshi bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl",
              }}
            >
              Request Employee
            </Button>
          </Group>
        </Group>
      </form>
    </Modal>
  );
};

export const StructureAddAssignment = ({
  user_structure_mapping_id,
}: {
  user_structure_mapping_id: any;
}) => {
  const {
    isOpen,
    closeModal,
    formUserJobCode: form,
    handleSubmitStructureMappingAssign,
  } = useEmployeeDataContext();

  const { dataEmployee } = useEmployeeData();
  useEffect(() => {
    if (user_structure_mapping_id) {
      form.setValues({ user_structure_mapping_id: user_structure_mapping_id });
    }
  }, []);
  const handleCloseModal = () => {
    closeModal();
    form.reset();
  };

  return (
    <Modal
      opened={isOpen("Assign")}
      onClose={handleCloseModal}
      radius="lg"
      size="xl"
      centered
      transitionProps={{ transition: "slide-up", duration: 300 }}
      title={
        <Group gap="sm" align="center">
          <div className="p-2 bg-violet-100 dark:bg-violet-900/30 rounded-lg">
            <IconUsersGroup
              size={20}
              className="text-violet-600 dark:text-violet-400"
            />
          </div>
          <div>
            <Text
              size="lg"
              fw={600}
              className="font-satoshi text-gray-900 dark:text-gray-100"
            >
              Request Employee for Structure
            </Text>
            <Text size="sm" c="dimmed" className="font-satoshi">
              Map employee to organizational structure
            </Text>
          </div>
        </Group>
      }
      classNames={{
        content: "bg-white dark:bg-neutral-900",
        header: "border-b border-gray-100 dark:border-gray-800 pb-4 mb-0",
        body: "p-0",
      }}
    >
      <form
        onSubmit={form.onSubmit((values: any) =>
          handleSubmitStructureMappingAssign(values)
        )}
      >
        <Stack gap="lg" className="p-6">
          {/* Structure Details Section */}
          <Paper
            p="md"
            radius="md"
            withBorder
            className="border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-neutral-800/50"
          >
            <Group gap="xs" mb="md">
              <IconBriefcase
                size={16}
                className="text-violet-600 dark:text-violet-400"
              />
              <Text
                size="sm"
                fw={500}
                className="text-gray-700 dark:text-gray-300"
              >
                Structure & Position Details
              </Text>
            </Group>

            <Stack gap="md">
              <Group grow>
                <Select
                  withAsterisk
                  label="Employee"
                  placeholder="Search and select employee"
                  size="md"
                  radius="md"
                  searchable
                  clearable
                  limit={20}
                  leftSection={<IconSearch size={16} />}
                  classNames={{
                    input:
                      "border-gray-200 dark:border-gray-700 bg-white dark:bg-neutral-800",
                    dropdown: "border-gray-200 dark:border-gray-700",
                    option: "hover:bg-violet-50 dark:hover:bg-violet-900/20",
                  }}
                  key={form.key("uuid")}
                  {...form.getInputProps("uuid")}
                  data={dataEmployee}
                />
              </Group>

              <Group grow>
                <TextInput
                  label="Group"
                  placeholder="Enter group (optional)"
                  size="md"
                  radius="md"
                  leftSection={<IconUsersGroup size={16} />}
                  classNames={{
                    input:
                      "border-gray-200 dark:border-gray-700 bg-white dark:bg-neutral-800",
                  }}
                  key={form.key("group")}
                  {...form.getInputProps("group")}
                />
              </Group>
              <DatePickerInput
                label="Assignment Date"
                placeholder="Select assignment date"
                size="md"
                radius="md"
                leftSection={<IconCalendar size={16} />}
                classNames={{
                  input:
                    "border-gray-200 dark:border-gray-700 bg-white dark:bg-neutral-800",
                }}
                key={form.key("assign_date")}
                {...form.getInputProps("assign_date")}
              />
            </Stack>
          </Paper>
        </Stack>

        <Divider className="border-gray-100 dark:border-gray-800" />

        {/* Action Buttons */}
        <Group justify="space-between" className="p-6">
          <Text size="xs" c="dimmed" className="font-satoshi">
            All required fields must be filled before saving
          </Text>

          <Group gap="sm">
            <Button
              variant="subtle"
              color="gray"
              size="md"
              radius="md"
              onClick={handleCloseModal}
              classNames={{
                root: "font-satoshi hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors",
              }}
            >
              Cancel
            </Button>
            <Button
              variant="filled"
              color="violet"
              size="md"
              radius="md"
              type="submit"
              classNames={{
                root: "font-satoshi bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl",
              }}
            >
              Assign Employee
            </Button>
          </Group>
        </Group>
      </form>
    </Modal>
  );
};
