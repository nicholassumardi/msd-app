import "@mantine/core/styles.css";
import "@mantine/dates/styles.css";
import "mantine-react-table/styles.css";
import { Avatar, Group, Text, Stack } from "@mantine/core";
import { IconPhoneCall, IconAt } from "@tabler/icons-react";
import { Employee } from "../../../../pages/api/admin/employee";

const UserInfoIcons = ({ dataUser }: { dataUser: Employee | null }) => {
  return (
    <Stack align="center" justify="center">
      <Text fz="xs" tt="uppercase" fw={700} c="dimmed">
        Liaison Officer
      </Text>
      <Avatar src="images/images.jpeg" size={150} radius="md" />
      <div>
        <Text fz="lg" fw={820}>
          {dataUser?.name}
        </Text>
        <Text fz="xs" tt="uppercase" fw={700} c="dimmed">
          {dataUser?.identity_card}
        </Text>
        <Group wrap="nowrap" gap={10} mt={3}>
          <IconAt stroke={1.5} size="1rem" />
          <Text fz="xs" c="dimmed">
            {dataUser?.email ?? "none@gmail.com"}
          </Text>
        </Group>

        <Group wrap="nowrap" gap={10} mt={5}>
          <IconPhoneCall stroke={1.5} size="1rem" />
          <Text fz="xs" c="dimmed">
            {dataUser?.phone}
          </Text>
        </Group>
      </div>
    </Stack>
  );
};

export default UserInfoIcons;
