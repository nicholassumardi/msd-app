/* eslint-disable @typescript-eslint/no-explicit-any */
import React, { useEffect, useState } from "react";
import { useDisclosure } from "@mantine/hooks";
import {
  ActionIcon,
  Button,
  Input,
  Modal,
  NumberInput,
  Text,
} from "@mantine/core";
import { IconEditCircle } from "@tabler/icons-react";
import axios from "axios";
import SuccessNotification from "@/components/Notifications/SuccessNotification";
import ErrorNotification from "@/components/Notifications/ErrorNotification";
import { useForm } from "@mantine/form";

interface FormComponent {
  id?: string;
  getData: () => Promise<void>;
  setIsLoading: React.Dispatch<React.SetStateAction<boolean>>;
}

const FormCompany: React.FC<FormComponent> = ({
  id,
  getData,
  setIsLoading,
}) => {
  const [mode, setMode] = useState("POST");
  const [error, setError] = useState<{ [key: string]: string }>({});
  const [opened, { open, close }] = useDisclosure(false);
  const form = useForm({
    initialValues: {
      company_name: "",
      unique_code: "",
      company_code: "",
    },
    validate: {
      company_name: (value) => (!value ? "Name cannot be empty" : null),
      unique_code: (value) => (!value ? "quota cannot be empty" : null),
      company_code: (value) => (!value ? "Please choose PEH Code" : null),
    },
  });
  const handleGetDataDetail = async () => {
    try {
      const response = await axios.get(`/api/admin/master_data/company/${id}`);
      form.setValues({
        company_name: response.data.data.name || "",
        unique_code: response.data.data.name || "",
        company_code: response.data.data.code || "",
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
          `/api/admin/master_data/company/${id}`,
          values
        );
        if (response.status === 200) {
          SuccessNotification({
            title: "Success",
            message: "Company data successfully updated",
          });
          close();
        }
      } else {
        const response = await axios.post(
          "/api/admin/master_data/company",
          values
        );
        if (response.status === 201) {
          SuccessNotification({
            title: "Success",
            message: "Company data successfully created",
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
            {mode === "PUT" ? "Edit Company" : "Create New Company"}
          </Text>
        }
      >
        <form onSubmit={form.onSubmit((values) => handleSubmit(values))}>
          <div>
            <Input.Wrapper
              withAsterisk
              label="Name"
              error={error?.company_name ?? ""}
            >
              <Input
                placeholder="please input name"
                name="company_name"
                key={form.key("company_name")}
                {...form.getInputProps("company_name")}
                required
              />
            </Input.Wrapper>

            <Input.Wrapper
              mt="md"
              withAsterisk
              label="Unique Code"
              error={error?.company_name ?? ""}
            >
              <NumberInput
                placeholder="please input unique code (01, 02, 03, etc)"
                name="unique_code"
                hideControls
                key={form.key("unique_code")}
                {...form.getInputProps("unique_code")}
                required
              />
            </Input.Wrapper>
            <Input.Wrapper
              withAsterisk
              label="Code"
              error={error?.company_code ?? ""}
              mt="md"
            >
              <Input
                placeholder="code ex. HAS, KAS, etc"
                name="company_code"
                key={form.key("company_code")}
                {...form.getInputProps("company_code")}
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

export default FormCompany;
