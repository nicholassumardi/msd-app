/* eslint-disable @typescript-eslint/no-explicit-any */
import React, { useEffect, useState } from "react";
import { useDisclosure } from "@mantine/hooks";
import {
  ActionIcon,
  Badge,
  Button,
  Group,
  Modal,
  ScrollArea,
  Select,
  Text,
  TextInput,
  Title,
} from "@mantine/core";
import { IconEditCircle, IconSearch, IconX } from "@tabler/icons-react";
import axios from "axios";
import SuccessNotification from "@/components/Notifications/SuccessNotification";
import ErrorNotification from "@/components/Notifications/ErrorNotification";
import { useForm } from "@mantine/form";
import { option } from "../../../../pages/types/option";

interface FormComponent {
  id?: string;
  getData: () => Promise<void>;
  setIsLoading: React.Dispatch<React.SetStateAction<boolean>>;
  dataIkw: option[];
  dataPositionCode: option[];
}

const FormRKI: React.FC<FormComponent> = ({
  id,
  getData,
  setIsLoading,
  dataIkw,
  dataPositionCode,
}) => {
  const [mode, setMode] = useState("POST");
  const [error, setError] = useState<{ [key: string]: string }>({});
  const [opened, { open, close }] = useDisclosure(false);
  const [selectedIkws, setSelectedIkws] = useState<string[]>([]);

  const form = useForm({
    initialValues: {
      position_job_code: "",
      ikw_id: "",
      training_time: 0,
      ikws: [] as string[],
    },
    validate: {
      position_job_code: (value) => (!value ? "Please select position" : null),
      ikw_id: (value) =>
        mode === "PUT" ? (!value ? "Please select IKW" : null) : null,
      ikws: (value) =>
        mode === "POST"
          ? Array.isArray(value) && value.length == 0
            ? "Please select IKW"
            : null
          : null,
    },
  });

  const handleGetDataDetail = async () => {
    try {
      const response = await axios.get(`/api/admin/rki/${id}?type=show`);
      form.setValues({
        position_job_code: response.data.data.data.position_job_code,
        ikw_id: response.data.data.data.ikw_id.toString(),
        training_time: response.data.data.data.training_time,
      });
    } catch (err: any) {
      if (err.response) {
        setError(err.response.data.message);
      }
    }
  };

  const handleCreateData = () => {
    form.reset();
    open();
  };

  const handleAddIkw = (ikwId: string | null) => {
    if (ikwId && !selectedIkws.includes(ikwId)) {
      const newIkws = [...selectedIkws, ikwId];
      setSelectedIkws(newIkws);
      form.setFieldValue("ikws", newIkws);
    }
  };

  const handleRemoveIkw = (ikwId: string) => {
    const newIkws = selectedIkws.filter((id) => id !== ikwId);
    setSelectedIkws(newIkws);
    form.setFieldValue("ikws", newIkws);
  };
  const handleEditData = () => {
    handleGetDataDetail();
    open();
  };

  useEffect(() => {
    const handleMode = () => {
      if (id) {
        setMode("PUT");
      } else {
        setMode("POST");
      }
    };
    handleMode();
  }, [id]);

  const handleCloseModal = () => {
    setError({});
    close();
  };

  const handleSubmit = async (values: any) => {
    try {
      if (mode === "PUT") {
        const response = await axios.put(
          `/api/admin/rki/${id}?type=update`,
          values
        );
        if (response.status === 200) {
          SuccessNotification({
            title: "Success",
            message: "RKI data successfully updated",
          });
          close();
        }
      } else {
        const response = await axios.post("/api/admin/rki?type=store", values);
        if (response.status === 201) {
          SuccessNotification({
            title: "Success",
            message: "RKI data successfully created",
          });
          close();
        }
      }
      setIsLoading(true);
      setInterval(() => {
        getData();
      }, 1500);
    } catch (err: any) {
      if (err.response && err.response.status == 422) {
        setError(err.response.data.error);
      } else {
        ErrorNotification({
          title: "Server Error",
          message: "500 Internal Server Error",
        });
      }
    }
  };

  return (
    <>
      <Modal
        opened={opened}
        onClose={handleCloseModal}
        centered
        fullScreen
        size="lg"
        transitionProps={{ transition: "scale", duration: 350 }}
        title={
          <Text fw={700} size="xl">
            {mode === "PUT" ? "Edit RKI" : "Create New RKI"}
          </Text>
        }
      >
        <form onSubmit={form.onSubmit((values: any) => handleSubmit(values))}>
          <div>
            <ScrollArea h={620} offsetScrollbars>
              <Title c="dimmed" fz="h2" mt="xl" mb="md">
                RKI Data
              </Title>
              <div className="md:grid grid-cols-2 gap-3 text-gray-500">
                <Select
                  label="Position"
                  placeholder="Select Position Code"
                  mt="md"
                  fw={500}
                  size="md"
                  radius="md"
                  searchable
                  clearable
                  limit={10}
                  leftSection={<IconSearch className="text-gray-500" />}
                  key={form.key("position_job_code")}
                  {...form.getInputProps("position_job_code")}
                  data={dataPositionCode}
                  className="w-full"
                  classNames={{
                    input:
                      "border-gray-300 focus:border-violet-500 transition-all",
                    label: "text-gray-700 font-semibold",
                  }}
                />
                <div>
                  {mode === "PUT" ? (
                    <Group gap="sm" className="mb-5">
                      <Select
                        label="Select IKW"
                        placeholder="Add IKW"
                        data={dataIkw}
                        searchable
                        clearable
                        mt="md"
                        fw={500}
                        size="md"
                        radius="md"
                        limit={10}
                        leftSection={<IconSearch className="text-gray-500" />}
                        className="w-full"
                        classNames={{
                          input:
                            "border-gray-300 focus:border-violet-500 transition-all",
                          label: "text-gray-700 font-semibold",
                        }}
                        key={form.key("ikw_id")}
                        {...form.getInputProps("ikw_id")}
                      />
                    </Group>
                  ) : (
                    <>
                      <Group>
                        <Select
                          label="Select IKW"
                          placeholder="Add IKW"
                          data={dataIkw}
                          searchable
                          clearable
                          mt="md"
                          fw={500}
                          size="md"
                          radius="md"
                          limit={10}
                          key={form.key("ikws")}
                          {...form.getInputProps("ikws")}
                          onChange={(value) => {
                            handleAddIkw(value);
                          }}
                          leftSection={<IconSearch className="text-gray-500" />}
                          className="w-full"
                          classNames={{
                            input:
                              "border-gray-300 focus:border-violet-500 transition-all",
                            label: "text-gray-700 font-semibold",
                          }}
                        />
                      </Group>
                      {selectedIkws.length > 0 && (
                        <Group gap="sm" className="flex flex-wrap">
                          {selectedIkws.map((ikwId) => {
                            const ikw = dataIkw.find((i) => i.value === ikwId);
                            return (
                              <Badge
                                key={ikwId}
                                rightSection={
                                  <IconX
                                    size={16}
                                    onClick={() => handleRemoveIkw(ikwId)}
                                    className="cursor-pointer hover:text-red-500 transition-colors"
                                  />
                                }
                                variant="light"
                                color="violet"
                                className="pr-2"
                              >
                                {ikw?.label}
                              </Badge>
                            );
                          })}
                        </Group>
                      )}
                    </>
                  )}
                </div>

                <TextInput
                  type="number"
                  label="Meeting Duration (minutes)"
                  size="md"
                  color="gray"
                  radius={12}
                  placeholder="Enter duration"
                  key={form.key("training_time")}
                  {...form.getInputProps("training_time")}
                />
              </div>
            </ScrollArea>
          </div>
          <Modal.Header
            pos={"sticky"}
            bottom={0}
            className="flex place-self-end gap-2"
          >
            <Button
              variant="default"
              color="white"
              size="lg"
              radius={12}
              onClick={handleCloseModal}
            >
              Close
            </Button>
            <Button
              variant="filled"
              color="violet"
              size="lg"
              type="submit"
              radius={12}
            >
              Save
            </Button>
          </Modal.Header>
        </form>
      </Modal>
      {mode === "PUT" ? (
        <ActionIcon
          variant="transparent"
          onClick={handleEditData}
          color="green"
          title="Edit"
        >
          <IconEditCircle />
        </ActionIcon>
      ) : (
        <Button
          className="shadow-md"
          size="sm"
          variant="filled"
          color="violet"
          radius={9}
          onClick={handleCreateData}
        >
          <Text className="font-satoshi" size="sm">
            Add New
          </Text>
        </Button>
      )}
    </>
  );
};

export default FormRKI;
