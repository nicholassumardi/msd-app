/* eslint-disable @typescript-eslint/no-explicit-any */
import React, { useEffect, useState } from "react";
import { useDisclosure } from "@mantine/hooks";
import { ActionIcon, Button, Input, Modal, Select } from "@mantine/core";
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

const FormJobDescription: React.FC<FormComponent> = ({
  id,
  getData,
  setIsLoading,
}) => {
  const [mode, setMode] = useState("POST");
  const [dataJobCode, setDataJobCode] = useState<option[]>([]);
  const [error, setError] = useState<{ [key: string]: string }>({});
  const [opened, { open, close }] = useDisclosure(false);
  const form = useForm({
    initialValues: {
      job_code_id: "",
      code: "",
      description: "",
    },
    validate: {
      code: (value) => (!value ? "code cannot be empty" : null),
    },
  });

  const handleGetDataDetail = async () => {
    try {
      const response = await axios.get(
        `/api/admin/master_data/job_family/job_description/${id}`
      );
      form.setValues({
        job_code_id: response.data.data.job_code_id?.toString(),
        code: response.data.data.code,
        description: response.data.data.description,
      });
    } catch (err: any) {
      if (err.response) {
        setError(err.response.data.message);
      }
    }
  };

  useEffect(() => {
    const getDataJobCode = async () => {
      try {
        const response = await axios.get(
          " /api/admin/master_data/job_family/peh_code?type=show"
        );
        const data = response.data.data.map((item: any) => ({
          value: item.id.toString(),
          label: item.code,
        }));
        setDataJobCode(data);
      } catch (err: any) {
        if (err.response) {
          setError(err.response.data.message);
        }
      }
    };
    getDataJobCode();
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
          `/api/admin/master_data/job_family/job_description/${id}`,
          values
        );
        if (response.status === 200) {
          SuccessNotification({
            title: "Success",
            message: "Job Description data successfully updated",
          });
          close();
        }
      } else {
        const response = await axios.post(
          "/api/admin/master_data/job_family/job_description",
          values
        );
        if (response.status === 201) {
          SuccessNotification({
            title: "Success",
            message: "Job Description data successfully created",
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
          mode === "PUT" ? "Edit Job Description" : "Create New Job Description"
        }
      >
        <form
          onSubmit={form.onSubmit((values) => {
            handleSubmit(values);
          })}
        >
          <div>
            <Select
              label="Role Code"
              placeholder="please select role code"
              mt="md"
              withAsterisk
              clearable
              searchable
              key={form.key("job_code_id")}
              {...form.getInputProps("job_code_id")}
              data={dataJobCode}
            ></Select>
            <Input.Wrapper
              withAsterisk
              label="Code"
              error={error?.code ?? ""}
              mt="md"
            >
              <Input
                placeholder="code ex. UT.12.01, UT.13.05, etc"
                name="code"
                key={form.key("code")}
                required
              />
            </Input.Wrapper>
            <Input.Wrapper
              withAsterisk
              label="Description"
              error={error?.description ?? ""}
              mt="md"
            >
              <Input
                placeholder="please input description"
                name="description"
                key={form.key("description")}
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
              onClick={handleSubmit}
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
        <Button onClick={handleCreateData} variant="filled">
          Create New Job Description
        </Button>
      )}
    </>
  );
};

export default FormJobDescription;
