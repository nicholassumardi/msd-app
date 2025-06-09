import { Alert } from "@mantine/core";
import { IconInfoCircle } from "@tabler/icons-react";
import React from "react";

interface Error {
  errTitle: string;
  errMessage: string;
}

const errorFetchingAlert: React.FC<Error> = ({ errTitle, errMessage }) => {
  const icon = <IconInfoCircle />;

  return (
    <Alert
      variant="filled"
      color="red"
      withCloseButton
      title={errTitle}
      icon={icon}
    >
      {errMessage}
    </Alert>
  );
};

export default errorFetchingAlert;
