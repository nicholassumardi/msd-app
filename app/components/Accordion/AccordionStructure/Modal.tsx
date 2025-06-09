/* eslint-disable @typescript-eslint/no-explicit-any */
import {
  Button,
  Modal,
  NumberInput,
  Select,
  Text,
  TextInput,
} from "@mantine/core";
import { IconSearch, IconUsersGroup } from "@tabler/icons-react";
import { option } from "../../../../pages/types/option";
import { useForm } from "@mantine/form";
import React, { useEffect, useState } from "react";
import axios from "axios";
import SuccessNotification from "@/components/Notifications/SuccessNotification";
import ErrorNotification from "@/components/Notifications/ErrorNotification";

interface ChildComponenetProps {
  id?: string | null;
  openedModal: boolean;
  closeModal: any;
  dataDepartment: option[];
  dataPehCode: option[];
  value: string | null;
  getDataStructureChange: (value: any) => Promise<void>;
}

const ModalStructure: React.FC<ChildComponenetProps> = ({
  id,
  openedModal,
  closeModal,
  dataDepartment,
  dataPehCode,
  value,
  getDataStructureChange,
}) => {
  const [mode, setMode] = useState("POST");
  const form = useForm({
    initialValues: {
      department_id: "",
      name: "",
      quota: "",
      job_code_id: null,
    },
    validate: {
      department_id: (value) => (!value ? "Please select department" : null),
      name: (value) => (!value ? "Name cannot be empty" : null),
      quota: (value) => (!value ? "quota cannot be empty" : null),
    },
  });

  const handleGetDataDetail = async () => {
    try {
      const response = await axios.get(
        `/api/admin/structure/${id}?type=getMapping`
      );

      form.setValues({
        department_id: response.data.data.data.department_id.toString(),
        name: response.data.data.data.name,
        quota: response.data.data.data.quota,
        job_code_id: response.data.data.data.job_code_id.toString(),
      });
    } catch (err: any) {
      if (err.response) {
      }
    }
  };

  const handleCloseModal = () => {
    closeModal();
    form.reset();
  };

  useEffect(() => {
    const handleMode = () => {
      if (id) {
        setMode("PUT");
        handleGetDataDetail();
      } else {
        setMode("POST");
      }
    };
    handleMode();
  }, [id]);

  const handleSubmit = async (values: any) => {
    try {
      if (mode === "PUT") {
        const response = await axios.put(
          `/api/admin/structure/${id}?type=updateMapping`,
          values
        );
        if (response.status === 200) {
          SuccessNotification({
            title: "Success",
            message: "Department data successfully updated",
          });
          closeModal();
          getDataStructureChange(value);
        }
      } else {
        const response = await axios.post(
          "/api/admin/structure?type=storeMapping",
          values
        );
        if (response.status === 201) {
          SuccessNotification({
            title: "Success",
            message: "Department data successfully created",
          });
          closeModal();
          getDataStructureChange(value);
        }
      }
    } catch (err: any) {
      if (err.response && err.response.status == 422) {
        ErrorNotification({
          title: "Server Error",
          message: err.response.data.error,
        });
      } else {
        ErrorNotification({
          title: "Server Error",
          message: "500 Internal Server Error",
        });
      }
    }
  };

  return (
    <Modal
      opened={openedModal}
      onClose={handleCloseModal}
      radius="md"
      size="md"
      transitionProps={{ transition: "scale", duration: 350 }}
      title={
        <>
          <Text size="xl" fw={700} className="font-satoshi">
            <div className="flex gap-2 items-center">
              <IconUsersGroup size={30} />
              Add new structure mapping
            </div>
          </Text>
          <Text size="md" fw={200} c="dimmed" className="font-satoshi">
            <div className="ml-11 items-center">Defining structure mapping</div>
          </Text>
        </>
      }
    >
      <form onSubmit={form.onSubmit((values) => handleSubmit(values))}>
        <div className="">
          <div className="grid gap-3 font-satoshi text-gray-500">
            <Select
              withAsterisk
              fw={100}
              label="Choose Department"
              size="md"
              color="gray"
              radius={12}
              searchable
              clearable
              leftSection={<IconSearch />}
              className="shadow-default"
              key={form.key("department_id")}
              {...form.getInputProps("department_id")}
              data={dataDepartment}
            />
            <TextInput
              label="Name"
              size="md"
              radius={12}
              withAsterisk
              className="shadow-default"
              key={form.key("name")}
              {...form.getInputProps("name")}
            ></TextInput>
            <NumberInput
              label="Quota"
              size="md"
              radius={12}
              withAsterisk
              className="shadow-default"
              hideControls
              key={form.key("quota")}
              {...form.getInputProps("quota")}
            ></NumberInput>
            <Select
              fw={100}
              label="PEH Code"
              size="md"
              color="gray"
              radius={12}
              searchable
              clearable
              leftSection={<IconSearch />}
              className="shadow-default"
              key={form.key("job_code_id")}
              {...form.getInputProps("job_code_id")}
              data={dataPehCode}
            />
          </div>
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
            Save changes
          </Button>
        </Modal.Header>
      </form>
    </Modal>
  );
};

export default ModalStructure;
