/* eslint-disable @typescript-eslint/no-explicit-any */
import React, { useState } from "react";
import { useDisclosure } from "@mantine/hooks";
import { ActionIcon, Button, Modal } from "@mantine/core";
import { IconEditCircle } from "@tabler/icons-react";
import axios from "axios";
import SuccessNotification from "@/components/Notifications/SuccessNotification";
import ErrorNotification from "@/components/Notifications/ErrorNotification";
import { useForm } from "@mantine/form";
import ContentStructure from "./ContentStructure";
import { option } from "../../../../pages/types/option";

interface FormComponent {
  id?: string;
  getData: () => Promise<void>;
  setIsLoading: React.Dispatch<React.SetStateAction<boolean>>;
}

export type Structure = {
  id?: string;
  job_code_id: string;
  group: string;
  id_structure: string;
  id_staff: string;
  description: string;
};

const FormStructure: React.FC<FormComponent> = ({
  id,
  getData,
  setIsLoading,
}) => {
  const [mode, setMode] = useState("POST");
  const [error, setError] = useState<{ [key: string]: string }>({});
  const [opened, { open, close }] = useDisclosure(false);
  const [structures, setStructures] = useState<Structure[]>([]);
  const [dataStructures, setDataStructures] = useState<option[]>([]);
  const form = useForm({
    initialValues: {
      uuid: id,
      employeeStructures: [],
    },
  });

  const handleGetDataDetail = async () => {
    try {
      const response = await axios.get(
        `/api/admin/structure/${id}?type=getStructure`
      );

      if (Object.keys(response.data.data.data).length > 0) {
        setMode("PUT");
        setStructures(response.data.data.data);
        form.setValues({
          employeeStructures: response.data.data.data,
        });
      }
    } catch (err: any) {
      if (err.response) {
        setError(err.response.data.message);
      }
    }
  };

  const handleEditData = () => {
    handleGetDataDetail();
    open();
  };

  const handleCloseModal = () => {
    setMode("POST");
    setError({});
    close();
  };

  const handleSubmit = async (values: any) => {
    try {
      if (mode === "PUT") {
        const response = await axios.put(
          `/api/admin/structure/${id}?type=update`,
          values
        );
        if (response.status === 200) {
          SuccessNotification({
            title: "Success",
            message: "Structure data successfully updated",
          });
          close();
        }
      } else {
        const response = await axios.post(
          `/api/admin/structure?type=storeStructure`,
          values
        );
        if (response.status === 201) {
          SuccessNotification({
            title: "Success",
            message: "Structure data successfully added",
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
        fullScreen
        radius={0}
        centered
        size="lg"
        transitionProps={{ transition: "scale", duration: 350 }}
        title={"Create Role Code"}
      >
        <form onSubmit={form.onSubmit((values) => handleSubmit(values))}>
          <div className="p-6 mt-2 container-fluid">
            <ContentStructure
              form={form}
              structures={structures}
              setStructures={setStructures}
              dataStructures={dataStructures}
              setDataStructures={setDataStructures}
              mode={mode}
            />
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
      <ActionIcon
        variant="transparent"
        onClick={handleEditData}
        color="green"
        title="Edit"
      >
        <IconEditCircle />
      </ActionIcon>
    </>
  );
};

export default FormStructure;
