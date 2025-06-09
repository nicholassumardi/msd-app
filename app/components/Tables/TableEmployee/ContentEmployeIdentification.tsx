/* eslint-disable @typescript-eslint/no-explicit-any */
import {
  Autocomplete,
  Button,
  Group,
  Input,
  Select,
  Table,
  Text,
  TextInput,
  Title,
} from "@mantine/core";
import { IMaskInput } from "react-imask";
import { DatePickerInput } from "@mantine/dates";
import { IconSquareRoundedPlus, IconTrashFilled } from "@tabler/icons-react";
import React, { useEffect, useState } from "react";
import { useField } from "@mantine/form";
import { modals } from "@mantine/modals";
import axios from "axios";
import { option } from "../../../../pages/types/option";
import { Employee } from "./Form";

interface ContentComponent {
  form: any;
  employeeNumbers: Employee[];
  dataCompanies: option[];
  dataDepartments: option[];
  setEmployeeNumbers: React.Dispatch<React.SetStateAction<Employee[]>>;
  setDataCompanies: React.Dispatch<React.SetStateAction<option[]>>;
  setDataDepartments: React.Dispatch<React.SetStateAction<option[]>>;
}

const dataStatus = [
  { value: "1", label: "Aktif" },
  { value: "2", label: "Non Aktif" },
];

const ContentEmployeeIdentification: React.FC<ContentComponent> = ({
  form,
  employeeNumbers,
  dataCompanies,
  dataDepartments,
  setEmployeeNumbers,
  setDataCompanies,
  setDataDepartments,
}) => {
  const [error, setError] = useState<string | null>(null);

  const fetchData = async (
    endpoint: string,
    setData: React.Dispatch<React.SetStateAction<option[]>>
  ) => {
    try {
      const response = await axios.get(endpoint);
      const data = response.data.data.map((item: any) => ({
        value: item.id.toString(),
        label: item.name,
      }));
      setData(data);
    } catch (err: any) {
      if (err.response) {
        setError(err.response.data.message);
      } else if (err.request) {
        setError("No response from server");
      } else {
        setError("Unexpected error occurred");
      }
    }
  };

  useEffect(() => {
    fetchData("/api/admin/master_data/company", setDataCompanies);
  }, []);

  useEffect(() => {
    fetchData(
      "/api/admin/master_data/department?type=showParent",
      setDataDepartments
    );
  }, []);

  const employeeNumber = useField({
    initialValue: "",
    validate: (value) =>
      !value ? "employee number should not be empty" : null,
  });

  const registryDate = useField({
    initialValue: null as Date | null,
    validate: (value) => (!value ? "please select registry date" : null),
  });

  const handleAdd = () => {
    const employeeNum = employeeNumber.getValue();
    const regDate =
      registryDate.getValue()?.toISOString().split("T")[0] || null;

    if (employeeNum && regDate) {
      const employeeNumberDataNow = {
        employee_number: employeeNum,
        registry_date: regDate,
      };

      setEmployeeNumbers((prev) => [...prev, employeeNumberDataNow]);

      form.setFieldValue("userEmployeeNumbers", [
        ...form.getValues().userEmployeeNumbers,
        employeeNumberDataNow,
      ]);
    }

    employeeNumber.setValue("");
    registryDate.setValue(null);
  };

  const handleEdit = (
    index: number,
    field: keyof Employee,
    value: string | null
  ) => {
    setEmployeeNumbers((prev) => {
      const updatedEmployeeNumbers = [...prev];
      updatedEmployeeNumbers[index][field] = value || "";
      return updatedEmployeeNumbers;
    });

    form.setFieldValue("userEmployeeNumbers", [
      ...employeeNumbers.map((employeeNumber, i) =>
        i === index
          ? { ...employeeNumber, [field]: value || "" }
          : employeeNumber
      ),
    ]);
  };

  const handleDelete = (indexToDelete: any) => {
    setEmployeeNumbers((prev) =>
      prev.filter((_, index) => index !== indexToDelete)
    );
  };

  const openDeleteConfirmModal = (indexToDelete: any) =>
    modals.openConfirmModal({
      title: `Confirm deletion ?`,
      children: (
        <Text>
          Are you sure you want to delete this row ? This action cannot be
          undone.
        </Text>
      ),
      labels: { confirm: "Delete", cancel: "Cancel" },
      confirmProps: { color: "red" },
      onConfirm: () => {
        handleDelete(indexToDelete);
      },
    });

  return (
    <>
      <Title c="dimmed" fz="h4">
        Identification Data
      </Title>
      <div className="md:grid grid-cols-2 gap-3">
        <Select
          mt="md"
          label="Company"
          placeholder="please select company"
          clearable
          searchable
          key={form.key("company_id")}
          {...form.getInputProps("company_id")}
          withAsterisk
          data={dataCompanies}
        ></Select>
        <Select
          mt="md"
          label="Department"
          placeholder="please select department"
          withAsterisk
          clearable
          searchable
          key={form.key("department_id")}
          {...form.getInputProps("department_id")}
          data={dataDepartments}
        ></Select>
        <Select
          mt="md"
          label="Status"
          placeholder="please select status employee"
          clearable
          searchable
          key={form.key("status")}
          {...form.getInputProps("status")}
          data={dataStatus}
        ></Select>
        <TextInput
          mt="md"
          label="Classification"
          placeholder="please input classification (ex. Staff, OS, PHL, etc)"
          key={form.key("employee_type")}
          {...form.getInputProps("employee_type")}
        ></TextInput>
        <TextInput
          mt="md"
          label="Section"
          placeholder="please input section (ex. QC, PRD, TEK M, etc)"
          key={form.key("section")}
          {...form.getInputProps("section")}
        ></TextInput>
        <TextInput
          mt="md"
          label="Position code"
          placeholder="please input position code (ex. SPV, KRB, KARU 1, etc)"
          key={form.key("position_code")}
          {...form.getInputProps("position_code")}
        ></TextInput>
        <Autocomplete
          mt="md"
          label="Status Shift"
          placeholder="please select status shift"
          data={["Shift", "Non Shift"]}
          key={form.key("schedule_type")}
          {...form.getInputProps("schedule_type")}
        ></Autocomplete>
        <TextInput
          mt="md"
          label="Status TWIJI"
          placeholder="please input Status TWIJI"
          key={form.key("status_twiji")}
          {...form.getInputProps("status_twiji")}
        ></TextInput>
      </div>
      <Title c="dimmed" fz="h4" mt="xl" mb="md">
        Employee tenure
      </Title>
      <div className="md:grid grid-cols-2 gap-3">
        <DatePickerInput
          label="Join date"
          placeholder="please input join date"
          key={form.key("join_date")}
          {...form.getInputProps("join_date")}
          valueFormat="YYYY-MM-DD"
        ></DatePickerInput>
        <DatePickerInput
          label="Leave date"
          placeholder="please input leave date"
          key={form.key("leave_date")}
          {...form.getInputProps("leave_date")}
          valueFormat="YYYY-MM-DD"
        ></DatePickerInput>
      </div>
      <Title c="dimmed" fz="h4" mt="xl" mb="md">
        Employee number
      </Title>
      <div className="md:grid grid-cols-2 gap-3">
        <Input.Wrapper label="Employee number (NOPEG)" withAsterisk>
          <Input
            {...employeeNumber.getInputProps()}
            component={IMaskInput}
            mask={Number}
            placeholder="please input employee number"
            name="employee_number"
          ></Input>
          <Input.Error mt={3}>
            {employeeNumber.error ? employeeNumber.error : ""}
          </Input.Error>
        </Input.Wrapper>
        <DatePickerInput
          {...registryDate.getInputProps()}
          clearable
          label="Date register"
          name="registry_date"
          placeholder="please input date register for employee number"
          valueFormat="YYYY-MM-DD"
          withAsterisk
        ></DatePickerInput>
        <div className="col-span-2">
          <Group justify="center" mt="md">
            {" "}
            <Button
              variant="light"
              leftSection={<IconSquareRoundedPlus />}
              size="md"
              fullWidth
              onClick={() => {
                employeeNumber.validate();
                registryDate.validate();
                handleAdd();
              }}
            >
              Add
            </Button>
          </Group>
        </div>
        <div className="col-span-2">
          <Table striped highlightOnHover withTableBorder mt="md">
            <Table.Thead>
              <Table.Tr>
                <Table.Th>Employee number</Table.Th>
                <Table.Th>Registry date</Table.Th>
                <Table.Th>Action</Table.Th>
              </Table.Tr>
            </Table.Thead>
            <Table.Tbody>
              {employeeNumbers.length > 0 ? (
                employeeNumbers.map((employee, index) => (
                  <Table.Tr key={index}>
                    <Table.Td>
                      <TextInput
                        placeholder="Please input employee number"
                        value={employee.employee_number}
                        onChange={(e) => {
                          handleEdit(index, "employee_number", e.target.value);
                        }}
                      ></TextInput>
                    </Table.Td>
                    <Table.Td>
                      <DatePickerInput
                        clearable
                        value={
                          employee.registry_date
                            ? new Date(employee.registry_date)
                            : null
                        }
                        placeholder="please input date register for employee number"
                        valueFormat="YYYY-MM-DD"
                        onChange={(value) => {
                          handleEdit(
                            index,
                            "registry_date",
                            value?.toString() || null
                          );
                        }}
                        withAsterisk
                      ></DatePickerInput>
                    </Table.Td>
                    <Table.Td>
                      <Button
                        variant="outline"
                        color="red"
                        size="xs"
                        onClick={() => {
                          openDeleteConfirmModal(index);
                        }}
                      >
                        <IconTrashFilled />
                      </Button>
                    </Table.Td>
                  </Table.Tr>
                ))
              ) : (
                <Table.Tr>
                  <Table.Td align="center" colSpan={3}>
                    No data found
                  </Table.Td>
                </Table.Tr>
              )}
            </Table.Tbody>
          </Table>
        </div>
      </div>
    </>
  );
};

export default ContentEmployeeIdentification;
