/* eslint-disable @typescript-eslint/no-explicit-any */
import {
  Accordion,
  Button,
  Divider,
  Modal,
  NumberInput,
  Select,
  Text,
  TextInput,
} from "@mantine/core";
import {
  IconCalendar,
  IconFileText,
  IconSearch,
  IconUser,
  IconUsersGroup,
} from "@tabler/icons-react";
import React from "react";
import useEmployeeData from "../../../hooks/EmployeeCentre";
import { useEmployeeDataContext } from "../../../context/EmployeeCentre";
import { DatePickerInput } from "@mantine/dates";

const StructureEdit = () => {
  const { dataDepartment, dataPehCode, handleSubmitStructure } =
    useEmployeeData();
  const {
    isOpen,
    closeModal,
    formUserStructureMapping: form,
  } = useEmployeeDataContext();

  const handleCloseModal = () => {
    closeModal();
    form.reset();
  };

  return (
    <Modal
      opened={isOpen("Edit")}
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
          handleSubmitStructure("PUT", values)
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
                  limit={20}
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

          <Accordion.Item value="revision">
            <Accordion.Control
              icon={<IconFileText size={18} />}
              className="font-satoshi font-medium text-gray-700 dark:text-gray-300"
            >
              Revision & Dates
            </Accordion.Control>
            <Accordion.Panel>
              <div className="p-5 space-y-6">
                <div>
                  <Text
                    size="md"
                    fw={600}
                    className="font-satoshi text-gray-700 dark:text-gray-300 mb-3"
                  >
                    Revision Information
                  </Text>
                  <TextInput
                    label="Revision Number"
                    placeholder="Enter revision number"
                    size="md"
                    radius="md"
                    classNames={{
                      input: "border-gray-300 dark:border-gray-600",
                      wrapper: "shadow-sm",
                    }}
                    key={form.key("revision_no")}
                    {...form.getInputProps("revision_no")}
                  />
                  <DatePickerInput
                    clearable
                    label="Valid Date"
                    placeholder="Select valid date"
                    size="md"
                    radius="md"
                    valueFormat="YYYY-MM-DD"
                    leftSection={<IconCalendar size={16} />}
                    classNames={{
                      input: "border-gray-300 dark:border-gray-600",
                      wrapper: "shadow-sm",
                    }}
                    key={form.key("valid_date")}
                    {...form.getInputProps("valid_date")}
                    className="mt-4"
                  />
                </div>
                <Divider
                  my="md"
                  className="border-gray-200 dark:border-gray-700"
                />
                <div>
                  <Text
                    size="md"
                    fw={600}
                    className="font-satoshi text-gray-700 dark:text-gray-300 mb-3"
                  >
                    Approval Dates
                  </Text>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <DatePickerInput
                      clearable
                      label="Authorized Date"
                      placeholder="Select authorized date"
                      size="md"
                      radius="md"
                      valueFormat="YYYY-MM-DD"
                      leftSection={<IconCalendar size={16} />}
                      classNames={{
                        input: "border-gray-300 dark:border-gray-600",
                        wrapper: "shadow-sm",
                      }}
                      key={form.key("authorized_date")}
                      {...form.getInputProps("authorized_date")}
                    />
                    <DatePickerInput
                      clearable
                      label="Approval Date"
                      placeholder="Select approval date"
                      size="md"
                      radius="md"
                      valueFormat="YYYY-MM-DD"
                      leftSection={<IconCalendar size={16} />}
                      classNames={{
                        input: "border-gray-300 dark:border-gray-600",
                        wrapper: "shadow-sm",
                      }}
                      key={form.key("approval_date")}
                      {...form.getInputProps("approval_date")}
                    />
                    <DatePickerInput
                      clearable
                      label="Acknowledged Date"
                      placeholder="Select acknowledged date"
                      size="md"
                      radius="md"
                      valueFormat="YYYY-MM-DD"
                      leftSection={<IconCalendar size={16} />}
                      classNames={{
                        input: "border-gray-300 dark:border-gray-600",
                        wrapper: "shadow-sm",
                      }}
                      key={form.key("acknowledged_date")}
                      {...form.getInputProps("acknowledged_date")}
                    />
                  </div>
                </div>
                <Divider
                  my="md"
                  className="border-gray-200 dark:border-gray-700"
                />
                <div>
                  <Text
                    size="md"
                    fw={600}
                    className="font-satoshi text-gray-700 dark:text-gray-300 mb-3"
                  >
                    Lifecycle Dates
                  </Text>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <DatePickerInput
                      clearable
                      label="Created Date"
                      placeholder="Select created date"
                      size="md"
                      radius="md"
                      valueFormat="YYYY-MM-DD"
                      leftSection={<IconCalendar size={16} />}
                      classNames={{
                        input: "border-gray-300 dark:border-gray-600",
                        wrapper: "shadow-sm",
                      }}
                      key={form.key("created_date")}
                      {...form.getInputProps("created_date")}
                    />
                    <DatePickerInput
                      clearable
                      label="Updated Date"
                      placeholder="Select updated date"
                      size="md"
                      radius="md"
                      valueFormat="YYYY-MM-DD"
                      leftSection={<IconCalendar size={16} />}
                      classNames={{
                        input: "border-gray-300 dark:border-gray-600",
                        wrapper: "shadow-sm",
                      }}
                      key={form.key("updated_date")}
                      {...form.getInputProps("updated_date")}
                    />
                    <DatePickerInput
                      clearable
                      label="Distribution Date"
                      placeholder="Select distribution date"
                      size="md"
                      radius="md"
                      valueFormat="YYYY-MM-DD"
                      leftSection={<IconCalendar size={16} />}
                      classNames={{
                        input: "border-gray-300 dark:border-gray-600",
                        wrapper: "shadow-sm",
                      }}
                      key={form.key("distribution_date")}
                      {...form.getInputProps("distribution_date")}
                    />
                    <DatePickerInput
                      clearable
                      label="Withdrawal Date"
                      placeholder="Select withdrawal date"
                      size="md"
                      radius="md"
                      valueFormat="YYYY-MM-DD"
                      leftSection={<IconCalendar size={16} />}
                      classNames={{
                        input: "border-gray-300 dark:border-gray-600",
                        wrapper: "shadow-sm",
                      }}
                      key={form.key("withdrawal_date")}
                      {...form.getInputProps("withdrawal_date")}
                    />
                  </div>
                </div>
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

export default StructureEdit;
