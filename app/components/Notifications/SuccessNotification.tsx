import { rem } from "@mantine/core";
import { notifications } from "@mantine/notifications";
import { IconCheck } from "@tabler/icons-react";
import "@mantine/core/styles.css";
import "@mantine/notifications/styles.css";

interface Notification {
  title?: string;
  message?: string;
}

const SuccessNotification: React.FC<Notification> = ({ title, message }) => {
  return notifications.show({
    title: title,
    message: message,
    color: "lime",
    position: "top-center",
    icon: <IconCheck style={{ width: rem(18), height: rem(18) }} />,
    autoClose: 2000,
  });
};

export default SuccessNotification;
