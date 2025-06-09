import { rem } from "@mantine/core";
import { notifications } from "@mantine/notifications";
import { IconX } from "@tabler/icons-react";
import "@mantine/core/styles.css";
import "@mantine/notifications/styles.css";

interface Notification {
  title?: string;
  message?: string;
}

const ErrorNotification: React.FC<Notification> = ({ title, message }) => {
  return notifications.show({
    title: title,
    message: message,
    color: "red",
    position: "top-center",
    icon: <IconX style={{ width: rem(18), height: rem(18) }} />,
    autoClose: 2000,
  });

//   <div className="bg-red-500"></div>
};

export default ErrorNotification;
