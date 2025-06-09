/* eslint-disable @typescript-eslint/no-explicit-any */
import {
  Accordion,
  Button,
  Modal,
  NumberInput,
  Select,
  Text,
  TextInput,
} from "@mantine/core";
import { IconSearch, IconUser, IconUsersGroup } from "@tabler/icons-react";
import React from "react";
import useEmployeeData from "../../../hooks/EmployeeCentre";
import { useEmployeeDataContext } from "../../../context/EmployeeCentre";

const StructureAdd = () => {
  const { dataDepartment, dataPehCode, handleSubmitStructure } =
    useEmployeeData();
  const {
    isOpen,
    closeModal,
    formUserJobCode: form,
  } = useEmployeeDataContext();

  const handleCloseModal = () => {
    closeModal();
    form.reset();
  };

  return (
    <Modal
      opened={isOpen("Add")}
      onClose={handleCloseModal}
      radius="lg"
      size="xl"
      transitionProps={{ transition: "scale", duration: 350 }}
      title={
        <div className="space-y-1">
          <Text
            size="xl"
            fw={700}
            className="font-satoshi flex items-center gap-2"
          >
            <IconUsersGroup size={24} />
            Add New Structure Mapping
          </Text>
          <Text size="sm" c="dimmed" className="font-satoshi">
            Define the structure mapping for the employee
          </Text>
        </div>
      }
      classNames={{
        content: "bg-white dark:bg-neutral-900 font-sans",
        header: "border-b border-gray-200 dark:border-gray-700 pb-4",
      }}
    >
      <form
        onSubmit={form.onSubmit((values: any) =>
          handleSubmitStructure("POST", values)
        )}
        className="p-4"
      >
        <Accordion
          variant="separated"
          radius="md"
          defaultValue="structure"
          classNames={{
            control: "bg-gray-50 dark:bg-neutral-800",
            content: "bg-gray-50 dark:bg-neutral-800",
            chevron: "text-gray-600 dark:text-gray-400",
          }}
        >
          <Accordion.Item value="structure">
            <Accordion.Control
              icon={<IconUsersGroup size={18} />}
              className="font-satoshi font-medium text-gray-700 dark:text-gray-300"
            >
              Structure Details
            </Accordion.Control>
            <Accordion.Panel>
              <div className="p-5 space-y-4">
                <Select
                  withAsterisk
                  label="Department"
                  placeholder="Select department"
                  size="md"
                  radius="md"
                  searchable
                  clearable
                  leftSection={<IconSearch size={16} />}
                  classNames={{
                    input: "border-gray-300 dark:border-gray-600",
                    wrapper: "shadow-sm",
                  }}
                  key={form.key("department_id")}
                  {...form.getInputProps("department_id")}
                  data={dataDepartment}
                />
                <TextInput
                  label="Name"
                  placeholder="Enter structure name"
                  size="md"
                  radius="md"
                  withAsterisk
                  classNames={{
                    input: "border-gray-300 dark:border-gray-600",
                    wrapper: "shadow-sm",
                  }}
                  key={form.key("name")}
                  {...form.getInputProps("name")}
                />
                <NumberInput
                  label="Quota"
                  placeholder="Enter quota"
                  size="md"
                  radius="md"
                  withAsterisk
                  hideControls
                  classNames={{
                    input: "border-gray-300 dark:border-gray-600",
                    wrapper: "shadow-sm",
                  }}
                  key={form.key("quota")}
                  {...form.getInputProps("quota")}
                />
                <Select
                  label="PEH Code"
                  placeholder="Select PEH code"
                  size="md"
                  radius="md"
                  searchable
                  clearable
                  limit={20}
                  leftSection={<IconSearch size={16} />}
                  classNames={{
                    input: "border-gray-300 dark:border-gray-600",
                    wrapper: "shadow-sm",
                  }}
                  key={form.key("job_code_id")}
                  {...form.getInputProps("job_code_id")}
                  data={dataPehCode}
                />
                <Select
                  label="Level"
                  placeholder="Select level"
                  size="md"
                  radius="md"
                  searchable
                  clearable
                  leftSection={<IconUser size={16} />}
                  classNames={{
                    input: "border-gray-300 dark:border-gray-600",
                    wrapper: "shadow-sm",
                  }}
                  key={form.key("structure_type")}
                  {...form.getInputProps("structure_type")}
                  data={[
                    { value: "Staff", label: "Staff" },
                    { value: "Non Staff", label: "Non Staff" },
                    { value: "Non Staff Leader", label: "Non Staff Leader" },
                  ]}
                />
              </div>
            </Accordion.Panel>
          </Accordion.Item>
        </Accordion>

        <div className="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
          <Button
            variant="outline"
            color="gray"
            size="md"
            radius="md"
            onClick={handleCloseModal}
            classNames={{ root: "px-4 font-satoshi" }}
          >
            Cancel
          </Button>
          <Button
            variant="filled"
            color="violet"
            size="md"
            radius="md"
            type="submit"
            classNames={{ root: "px-6 font-satoshi" }}
          >
            Save Changes
          </Button>
        </div>
      </form>
    </Modal>
  );
};

export default StructureAdd;
