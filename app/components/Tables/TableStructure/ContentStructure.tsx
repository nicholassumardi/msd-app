/* eslint-disable @typescript-eslint/no-explicit-any */
import {
  Button,
  Group,
  Table,
  TextInput,
  Title,
  Text,
  Select,
} from "@mantine/core";
import { IconSquareRoundedPlus, IconTrashFilled } from "@tabler/icons-react";
import React, { useEffect, useState } from "react";
import { useField } from "@mantine/form";
import { modals } from "@mantine/modals";
import axios from "axios";
import { Structure } from "./Form";
import { option } from "../../../../pages/types/option";

interface ContentComponent {
  form: any;
  structures: Structure[];
  dataStructures: option[];
  setStructures: React.Dispatch<React.SetStateAction<Structure[]>>;
  setDataStructures: React.Dispatch<React.SetStateAction<option[]>>;
  mode: string;
}

const ContentStructure: React.FC<ContentComponent> = ({
  form,
  structures,
  dataStructures,
  setStructures,
  setDataStructures,
  mode,
}) => {
  const [error, setError] = useState<string | null>(null);

  const job_code_id = useField({
    initialValue: "",
    validate: (value) => (!value ? "role code cannot be empty" : null),
  });
  const group = useField({
    initialValue: "",
    validate: (value) => (!value ? "group cannot be empty" : null),
  });
  const description = useField({
    initialValue: "",
  });
  const id_structure = useField({
    initialValue: "",
  });
  const id_staff = useField({
    initialValue: "",
  });

  useEffect(() => {
    const getData = async () => {
      try {
        const response = await axios.get(
          "/api/admin/master_data/job_family/peh_code?type=show"
        );
        const structures = response.data.data.data.map((item: any) => ({
          value: item.id.toString(),
          label: item.code,
        }));
        setDataStructures(structures);
      } catch (err: any) {
        if (err.response) {
          setError(err.response.data.message);
        } else if (err.request) {
          setError("No response from server");
        } else {
          setError("Unexpected error occured");
        }
      }
    };

    getData();
  }, []);

  const handleAdd = () => {
    if (!job_code_id.getValue() && !group.getValue()) {
      job_code_id.validate();
      group.validate();
      return;
    }

    const dataStructureNow = {
      job_code_id: job_code_id.getValue(),
      group: group.getValue(),
      description: description.getValue(),
      id_structure: id_structure.getValue(),
      id_staff: id_staff.getValue(),
    };

    setStructures((prevStructure) => [...prevStructure, dataStructureNow]);

    form.setFieldValue("employeeStructures", [
      ...form.getValues().employeeStructures,
      dataStructureNow,
    ]);

    job_code_id.setValue("");
    group.setValue("");
    description.setValue("");
    id_structure.setValue("");
    id_staff.setValue("");
  };

  const handleEdit = (
    index: number,
    field: keyof Structure,
    value: string | null
  ) => {
    setStructures((prevStructures) => {
      const updatedStructures = [...prevStructures];
      updatedStructures[index][field] = value || "";
      return updatedStructures;
    });

    form.setFieldValue("employeeStructures", [
      ...structures.map((structure, i) =>
        i === index ? { ...structure, [field]: value || "" } : structure
      ),
    ]);
  };

  const handleDelete = async (indexToDelete: any, id?: string) => {
    setStructures((prev) => prev.filter((_, index) => index !== indexToDelete));
    if (mode == "PUT") {
      await axios.delete(`/api/admin/structure/${id}?type=deleteStructure`);
    }
  };

  const openDeleteConfirmModal = (indexToDelete: any, id?: string) =>
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
        handleDelete(indexToDelete, id);
      },
    });

  return (
    <>
      <Title c="dimmed" fz="h4" mb="md">
        Employee Structure
      </Title>
      <div className="grid grid-cols-2 gap-3">
        <Select
          mt="md"
          searchable
          clearable
          label="Role code"
          {...job_code_id.getInputProps()}
          data={dataStructures}
        ></Select>
        <TextInput
          mt="md"
          label="Group"
          placeholder="Please input description"
          {...group.getInputProps()}
        ></TextInput>
        <TextInput
          mt="md"
          label="ID Structure"
          placeholder="Please input description"
          {...id_structure.getInputProps()}
        ></TextInput>
        <TextInput
          mt="md"
          label="ID Staff"
          placeholder="Please input description"
          {...id_staff.getInputProps()}
        ></TextInput>
        <TextInput
          mt="md"
          label="Description"
          placeholder="Please input description"
          {...description.getInputProps()}
        ></TextInput>
        <div className="col-span-2">
          <Group justify="center" mt="md">
            <Button
              variant="light"
              leftSection={<IconSquareRoundedPlus />}
              size="md"
              fullWidth
              onClick={() => {
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
                <Table.Th>Role Code</Table.Th>
                <Table.Th>Group</Table.Th>
                <Table.Th>ID Structure</Table.Th>
                <Table.Th>ID Staff</Table.Th>
                <Table.Th>Description</Table.Th>
                <Table.Th>Action</Table.Th>
              </Table.Tr>
            </Table.Thead>
            <Table.Tbody>
              {structures.length > 0 ? (
                structures.map((structure, index) => (
                  <Table.Tr key={index}>
                    <Table.Td>
                      <Select
                        searchable
                        clearable
                        value={structure.job_code_id.toString()}
                        onChange={(value) => {
                          handleEdit(index, "job_code_id", value || "null");
                        }}
                        data={dataStructures}
                      ></Select>
                    </Table.Td>
                    <Table.Td>
                      {" "}
                      <TextInput
                        placeholder="Please input group"
                        value={structure.group}
                        onChange={(e) => {
                          handleEdit(index, "group", e.target.value);
                        }}
                      ></TextInput>
                    </Table.Td>
                    <Table.Td>
                      {" "}
                      <TextInput
                        placeholder="Please input ID Structure"
                        value={structure.id_structure}
                        onChange={(e) => {
                          handleEdit(index, "id_structure", e.target.value);
                        }}
                      ></TextInput>
                    </Table.Td>
                    <Table.Td>
                      {" "}
                      <TextInput
                        placeholder="Please input ID Staff"
                        value={structure.id_staff}
                        onChange={(e) => {
                          handleEdit(index, "id_staff", e.target.value);
                        }}
                      ></TextInput>
                    </Table.Td>
                    <Table.Td>
                      {" "}
                      <TextInput
                        placeholder="Please input description"
                        value={structure.description}
                        onChange={(e) => {
                          handleEdit(index, "description", e.target.value);
                        }}
                      ></TextInput>
                    </Table.Td>
                    <Table.Td>
                      <Button
                        variant="outline"
                        color="red"
                        size="xs"
                        onClick={() => {
                          openDeleteConfirmModal(index, structure.id);
                        }}
                      >
                        <IconTrashFilled />
                      </Button>
                    </Table.Td>
                  </Table.Tr>
                ))
              ) : (
                <Table.Tr>
                  <Table.Td align="center" colSpan={6}>
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

export default ContentStructure;
