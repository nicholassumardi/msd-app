/* eslint-disable @typescript-eslint/no-explicit-any */
import React, { useEffect, useState } from "react";
import { useDisclosure } from "@mantine/hooks";
import { ActionIcon, Button, Input, Modal, Select, Text } from "@mantine/core";
import { IconEditCircle } from "@tabler/icons-react";
import axios from "axios";
import SuccessNotification from "@/components/Notifications/SuccessNotification";
import ErrorNotification from "@/components/Notifications/ErrorNotification";
import { useForm } from "@mantine/form";
import { option } from "../../../../../pages/types/option";

interface FormComponent {
  id?: string;
  getData: () => Promise<void>;
  setIsLoading: React.Dispatch<React.SetStateAction<boolean>>;
}

const FormPehCode: React.FC<FormComponent> = ({
  id,
  getData,
  setIsLoading,
}) => {
  const [mode, setMode] = useState("POST");
  const [dataCategory, setDataCategory] = useState<option[]>([]);
  const [error, setError] = useState<{ [key: string]: string }>({});
  const [opened, { open, close }] = useDisclosure(false);
  const form = useForm({
    initialValues: {
      category_id: "",
      org_level: "",
      job_family: "",
      code: "",
      position: "",
    },
    validate: {
      position: (value) => (!value ? "position cannot be empty" : null),
      code: (value) => (!value ? "code cannot be empty" : null),
    },
  });

  const handleGetDataDetail = async () => {
    try {
      const response = await axios.get(
        `/api/admin/master_data/job_family/peh_code/${id}`
      );
      form.setValues({
        category_id: response.data.data.category_id?.toString(),
        code: response.data.data.code,
      });
    } catch (err: any) {
      if (err.response) {
        setError(err.response.data.message);
      }
    }
  };

  useEffect(() => {
    const getDataCategory = async () => {
      try {
        const response = await axios.get(
          "/api/admin/master_data/job_family/category"
        );
        const data = response.data.data.data.map((item: any) => ({
          value: item.id.toString(),
          label: item.name,
        }));
        setDataCategory(data);
      } catch (err: any) {
        if (err.response) {
          setError(err.response.data.message);
        }
      }
    };
    getDataCategory();
  }, []);

  const handleCreateData = () => {
    open();
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
          `/api/admin/master_data/job_family/peh_code/${id}`,
          values
        );
        if (response.status === 200) {
          SuccessNotification({
            title: "Success",
            message: "PEH Code data successfully updated",
          });
          close();
        }
      } else {
        const response = await axios.post(
          "/api/admin/master_data/job_family/peh_code",
          values
        );
        if (response.status === 201) {
          SuccessNotification({
            title: "Success",
            message: "PEH Code data successfully created",
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
        size="lg"
        transitionProps={{ transition: "scale", duration: 350 }}
        title={
          <Text fw={700} size="xl">
            {mode === "PUT" ? "Edit PEH Code" : "Create New PEH Code"}
          </Text>
        }
      >
        <form
          onSubmit={form.onSubmit((values) => {
            handleSubmit(values);
          })}
        >
          <div>
            <Select
              label="Category"
              placeholder="please select Category"
              mt="md"
              withAsterisk
              clearable
              searchable
              key={form.key("category_id")}
              {...form.getInputProps("category_id")}
              data={dataCategory}
            ></Select>
            <Input.Wrapper
              withAsterisk
              label="Position"
              error={error?.position ?? ""}
              mt="md"
            >
              <Input
                placeholder="Kepala Pabrik, Ketua divisi, etc"
                key={form.key("position")}
                {...form.getInputProps("position")}
                required
              />
            </Input.Wrapper>
            <Input.Wrapper
              withAsterisk
              label="Org Level"
              error={error?.org_level ?? ""}
              mt="md"
            >
              <Input
                placeholder="ST, NS, etc"
                key={form.key("org_level")}
                {...form.getInputProps("org_level")}
                required
              />
            </Input.Wrapper>
            <Input.Wrapper
              withAsterisk
              label="JF"
              error={error?.job_family ?? ""}
              mt="md"
            >
              <Input
                key={form.key("job_family")}
                {...form.getInputProps("job_family")}
                required
              />
            </Input.Wrapper>
            <Input.Wrapper
              withAsterisk
              label="Code"
              error={error?.code ?? ""}
              mt="md"
            >
              <Input
                key={form.key("code")}
                {...form.getInputProps("code")}
                required
              />
            </Input.Wrapper>
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

export default FormPehCode;
