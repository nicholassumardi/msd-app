/* eslint-disable @typescript-eslint/no-explicit-any */
import React, { useEffect, useState } from "react";
import { useDisclosure } from "@mantine/hooks";
import { ActionIcon, Button, Input, Modal, Select, Text } from "@mantine/core";
import { IconEditCircle } from "@tabler/icons-react";
import axios from "axios";
import SuccessNotification from "@/components/Notifications/SuccessNotification";
import ErrorNotification from "@/components/Notifications/ErrorNotification";
import { useForm } from "@mantine/form";
import { option } from "../../../../pages/types/option";

interface FormComponent {
  id?: string;
  getData: () => Promise<void>;
  setIsLoading: React.Dispatch<React.SetStateAction<boolean>>;
}

const FormDepartment: React.FC<FormComponent> = ({
  id,
  getData,
  setIsLoading,
}) => {
  const [mode, setMode] = useState("POST");
  const [hasClass, setHasClass] = useState(true);
  const [dataCompanies, setDataCompanies] = useState<option[]>([]);
  const [dataParent, setDataParent] = useState<option[]>([]);
  const [error, setError] = useState<{ [key: string]: string }>({});
  const [opened, { open, close }] = useDisclosure(false);

  const form = useForm({
    initialValues: {
      company_id: "",
      parent_id: "",
      name: "",
      code: "",
    },
    validate: {
      name: (value) => (!value ? "Name cannot be empty" : null),
      code: (value) => (!value ? "code cannot be empty" : null),
    },
  });

  const handleGetDataDetail = async () => {
    try {
      const response = await axios.get(
        `/api/admin/master_data/department/${id}`
      );
      form.setValues({
        company_id: response.data.data.company_id?.toString(),
        parent_id: response.data.data.parent_id?.toString(),
        name: response.data.data.name,
        code: response.data.data.code,
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
    const getDataCompanies = async () => {
      try {
        const response = await axios.get("/api/admin/master_data/company");
        const data = response.data.data.data.map((item: any) => ({
          value: item.id.toString(),
          label: item.name,
        }));
        setDataCompanies(data);
      } catch (err: any) {
        if (err.response) {
          setError(err.response.data.message);
        }
      }
    };
    getDataCompanies();
  }, []);

  useEffect(() => {
    const getDataParent = async () => {
      try {
        const response = await axios.get(
          "/api/admin/master_data/department?type=showAll"
        );
        const data = response.data.data.map((item: any) => ({
          value: item.id.toString(),
          label: item.name,
        }));
        setDataParent(data);
      } catch (err: any) {
        if (err.response) {
          setError(err.response.data.message);
        }
      }
    };
    getDataParent();
  }, []);

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

  const handleTypeChange = (value: any) => {
    if (value == "Plant") {
      setHasClass(false);
    } else {
      setHasClass(true);
    }
  };

  const handleSubmit = async (values: any) => {
    console.log(values);
    try {
      if (mode === "PUT") {
        const response = await axios.put(
          `/api/admin/master_data/department/${id}?type=show`,
          values
        );
        if (response.status === 200) {
          SuccessNotification({
            title: "Success",
            message: "Department data successfully updated",
          });
          close();
        }
      } else {
        const response = await axios.post(
          "/api/admin/master_data/department",
          values
        );
        if (response.status === 201) {
          SuccessNotification({
            title: "Success",
            message: "Department data successfully created",
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
            {mode === "PUT" ? "Edit Department" : "Create New Certificate"}
          </Text>
        }
      >
        <form onSubmit={form.onSubmit((values) => handleSubmit(values))}>
          <div>
            <Select
              label="Company"
              placeholder="please select company"
              withAsterisk
              clearable
              searchable
              key={form.key("company_id")}
              {...form.getInputProps("company_id")}
              data={dataCompanies}
            ></Select>
            <Select
              label="Choose Type"
              placeholder="please select parent"
              mt="md"
              withAsterisk
              clearable
              searchable
              onChange={(value) => handleTypeChange(value)}
              data={["Department", "Plant"]}
            ></Select>
            <Select
              label="Department"
              placeholder="please select parent"
              mt="md"
              withAsterisk
              clearable
              searchable
              className={hasClass ? "hidden" : ""}
              key={form.key("parent_id")}
              {...form.getInputProps("parent_id")}
              data={dataParent}
            ></Select>
            <Input.Wrapper
              mt="md"
              withAsterisk
              label="Name"
              error={error?.name ?? ""}
            >
              <Input
                placeholder="please input name"
                key={form.key("name")}
                {...form.getInputProps("name")}
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
                placeholder="code ex. HAS, KAS, etc"
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

export default FormDepartment;
