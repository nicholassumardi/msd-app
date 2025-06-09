/* eslint-disable @typescript-eslint/no-explicit-any */
import ErrorNotification from "@/components/Notifications/ErrorNotification";
import SuccessNotification from "@/components/Notifications/SuccessNotification";
import { ActionIcon, Text } from "@mantine/core";
import { modals } from "@mantine/modals";
import { IconTrash } from "@tabler/icons-react";
import axios from "axios";

interface ButtonComponent {
  id: string;
  url: string;
  isJobFamily?: boolean;
  getData: () => Promise<void>;
  setIsLoading: React.Dispatch<React.SetStateAction<boolean>>;
  params?: string;
}

const ButtonDelete: React.FC<ButtonComponent> = ({
  id,
  url,
  isJobFamily,
  getData,
  setIsLoading,
  params,
}) => {
  const handleDestroy = async () => {
    try {
      const response = await axios.delete(
        `/api/admin/master_data/${
          isJobFamily ? `job_family` : ""
        }/${url.toLowerCase()}/${id}${params ? `?${params}` : ""}`
      );
      if (response.status === 200) {
        SuccessNotification({
          title: "Success",
          message: "Certificate successfully deleted",
        });
        close();
      }
      setIsLoading(true);
      setInterval(() => {
        getData();
      }, 1500);
    } catch (err: any) {
      if (err.response) {
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

  const openDeleteConfirmModal = () =>
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
      onConfirm: handleDestroy,
    });

  return (
    <ActionIcon
      variant="transparent"
      color="red"
      title="Delete"
      onClick={() => openDeleteConfirmModal()}
    >
      <IconTrash />
    </ActionIcon>
  );
};

export default ButtonDelete;
