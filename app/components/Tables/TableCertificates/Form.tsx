/* eslint-disable @typescript-eslint/no-explicit-any */
import React, {
  // useCallback,
  useEffect,
  useState,
} from "react";
import { useDisclosure } from "@mantine/hooks";
import { ActionIcon, Button, Input, Modal, Text } from "@mantine/core";
import { IconEditCircle } from "@tabler/icons-react";
import axios from "axios";
import SuccessNotification from "@/components/Notifications/SuccessNotification";
import ErrorNotification from "@/components/Notifications/ErrorNotification";

interface FormComponent {
  id?: string;
  getData: () => Promise<void>;
  setIsLoading: React.Dispatch<React.SetStateAction<boolean>>;
}

const FormCertificate: React.FC<FormComponent> = ({
  id,
  getData,
  setIsLoading,
}) => {
  const [formData, setFormData] = useState({
    certificate_name: "",
  });
  const [mode, setMode] = useState("POST");
  const [error, setError] = useState<{ [key: string]: string }>({});
  const [opened, { open, close }] = useDisclosure(false);

  const handleGetDataDetail = async () => {
    try {
      const response = await axios.get(
        `/api/admin/master_data/certificate/${id}`
      );
      setFormData({ certificate_name: response.data.data.name || "" });
    } catch (err: any) {
      if (err.response) {
        setError(err.response.data.message);
      }
    }
  };

  const handleCreateData = () => {
    setFormData({
      certificate_name: "",
    });
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

  const handleInputChange = (e: any) => {
    const { name, value } = e.target;

    setFormData({ ...formData, [name]: value });
  };

  const handleSubmit = async () => {
    try {
      if (mode === "PUT") {
        const response = await axios.put(
          `/api/admin/master_data/certificate/${id}`,
          formData
        );
        if (response.status === 200) {
          SuccessNotification({
            title: "Success",
            message: "Certificate data successfully updated",
          });
          close();
        }
        setIsLoading(true);
        setInterval(() => {
          getData();
        }, 1500);
      } else {
        const response = await axios.post(
          "/api/admin/master_data/certificate",
          formData
        );
        if (response.status === 201) {
          SuccessNotification({
            title: "Success",
            message: "Certificate data successfully created",
          });
          close();
        }
        setIsLoading(true);
        setInterval(() => {
          getData();
        }, 1500);
      }
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
            {mode === "PUT" ? "Edit Certificate" : "Create New Certificate"}
          </Text>
        }
      >
        <div>
          <Input.Wrapper
            withAsterisk
            label="Name"
            error={error?.certificate_name ?? ""}
          >
            <Input
              placeholder="please input name"
              name="certificate_name"
              onChange={handleInputChange}
              value={formData.certificate_name}
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

export default FormCertificate;
