/* eslint-disable @typescript-eslint/no-explicit-any */
import ErrorNotification from "@/components/Notifications/ErrorNotification";
import {
  Drawer,
  ActionIcon,
  Text,
  Card,
  Stack,
  Divider,
  Paper,
  Group,
  Box,
} from "@mantine/core";
import { IconInfoSquare } from "@tabler/icons-react";
import axios from "axios";
import { useState } from "react";
import { IKWS } from "../../../../../pages/api/admin/master_data/job_family/ikws";
import { Timeline } from "@/components/ui/timeline";

interface Revision {
  title: any;
  content: JSX.Element;
}

export default function ButtonDetail({ id }: { id: string }) {
  const [openedDrawer, setOpenedDrawer] = useState(false);
  const [dataIKW, setDataIKW] = useState<IKWS | null>(null);
  const [dataRevision, setDataRevision] = useState<Revision[] | []>([]);
  const processStatusMapping: Record<string, { label: string; color: string }> =
    {
      "1": { label: "DONE", color: "green" },
      "2": { label: "FOD - PENGAJUAN", color: "blue" },
      "3": { label: "FU-LO", color: "red" },
      "4": { label: "ON - PROGRESS", color: "yellow" },
    };

  const handleDataIKW = async () => {
    try {
      const response = await axios.get(
        `/api/admin/master_data/job_family/ikws/${id}?type=show`
      );
      const formatedData: Revision[] | [] =
        response.data?.data?.data?.ikw_revisions?.map((revision: any) => ({
          title: "Revision " + revision.revision_no.toString().padStart(2, "0"),
          content: <RevisionCard revision={revision} />,
        })) || [];

      setDataIKW(response.data.data.data);
      setDataRevision(formatedData);
    } catch (err: any) {
      ErrorNotification({
        title: "Server Error",
        message: err.response.data.error,
      });
    }
  };
  const openDrawer = () => setOpenedDrawer(true);
  const closeDrawer = () => setOpenedDrawer(false);

  function RevisionCard({ revision }: { revision: any }) {
    const statusDetails =
      processStatusMapping[revision.process_status.toString()];
    return (
      <Paper
        p="md"
        shadow="sm"
        mb="xl"
        style={{ borderLeft: "4px solid #4c6ef5" }}
      >
        <Stack gap="sm">
          {/* Main Revision Info */}
          <Group>
            <Box>
              <Text size="lg" c="dimmed">
                Reason
              </Text>
              <Text w={500}>{revision.reason ?? "-"}</Text>
            </Box>
            <Box>
              <Text size="lg" c="dimmed">
                Process Status
              </Text>
              <Text w={500} fw={900} c={statusDetails.color}>
                {statusDetails.label ?? "-"}
              </Text>
            </Box>
            <Box>
              <Text size="lg" c="dimmed">
                Submission Date
              </Text>
              <Text w={500}>
                {new Date(
                  revision.submission_received_date
                ).toLocaleDateString() ?? "-"}
              </Text>
            </Box>
          </Group>

          {/* Dates Section */}
          <Divider my="sm" label="Important Dates" labelPosition="center" />
          <Group>
            <Box mx="xl">
              <Text size="lg" c="dimmed">
                Print Date
              </Text>
              <Text>
                {new Date(revision.print_date).toLocaleDateString() ?? "-"}
              </Text>
            </Box>
            <Box mx="xl">
              <Text size="lg" c="dimmed">
                Distribution Date
              </Text>
              <Text>
                {new Date(revision.distribution_date).toLocaleDateString() ??
                  "-"}
              </Text>
            </Box>
            <Box mx="xl">
              <Text size="lg" c="dimmed">
                Handover Date
              </Text>
              <Text>
                {new Date(revision.handover_date).toLocaleDateString() ?? "-"}
              </Text>
            </Box>
          </Group>
          {/* Meetings Section */}
          <Divider my="sm" label="Meetings" labelPosition="center" />
          {revision.ikw_meeting.map((meeting: any) => (
            <Paper key={meeting.id} p="sm" mt="xs" withBorder>
              <Group>
                <Box mx="xl">
                  <Text size="lg" c="dimmed">
                    Meeting Date
                  </Text>
                  <Text>
                    {new Date(meeting.meeting_date).toLocaleDateString()}
                  </Text>
                </Box>
                <Box mx="xl">
                  <Text size="lg" c="dimmed">
                    Duration
                  </Text>
                  <Text>{meeting.meeting_duration ?? "-"} minutes</Text>
                </Box>
                <Box mx="xl">
                  <Text size="lg" c="dimmed">
                    Revision Status
                  </Text>
                  <Text>{meeting.revision_status ?? "-"}</Text>
                </Box>
              </Group>
            </Paper>
          ))}

          {/* Positions Section */}
          <Divider my="sm" label="Positions" labelPosition="center" />
          {revision.ikw_position.map((position: any) => (
            <Paper key={position.id} p="sm" mt="xs" withBorder>
              <Group>
                <Box mx="xl">
                  <Text size="lg" c="dimmed">
                    Call Number
                  </Text>
                  <Text>{position.position_call_number ?? "-"}</Text>
                </Box>
                <Box mx="xl">
                  <Text size="lg" c="dimmed">
                    Field Operator
                  </Text>
                  <Text>{position.field_operator ?? "-"}</Text>
                </Box>
              </Group>
            </Paper>
          ))}
        </Stack>
      </Paper>
    );
  }

  return (
    <>
      <ActionIcon
        variant="transparent"
        onClick={() => {
          openDrawer();
          handleDataIKW();
        }}
        color="blue"
        title="See Details"
      >
        <IconInfoSquare />
      </ActionIcon>

      <Drawer
        position="bottom"
        radius="md"
        size="100%"
        opened={openedDrawer}
        onClose={closeDrawer}
        transitionProps={{ transition: "slide-up" }}
        title={
          <Text size="xl" fw={700} className="font-satoshi">
            <div className="flex gap-2 items-center">
              IKW {dataIKW?.code ?? "-"}
            </div>
          </Text>
        }
      >
        <div className="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 font-satoshi font-semibold">
          {[
            "department_name",
            "code",
            "name",
            "total_page",
            "registration_date",
            "print_by_back_office_date",
            "submit_to_department_date",
            "ikw_return_date",
            "ikw_creation_duration",
            "status_document",
            "last_update_date",
            "description",
          ].map((key) => {
            let value = dataIKW?.[key as keyof IKWS] || "-";

            if (
              [
                "registration_date",
                "print_by_back_office_date",
                "submit_to_department_date",
                "ikw_return_date",
                "last_update_date",
              ].includes(key) &&
              value !== "-"
            ) {
              const date = new Date(value as string | number);
              value = isNaN(date.getTime())
                ? "-"
                : `${date.getDate()}/${
                    date.getMonth() + 1
                  }/${date.getFullYear()}`;
            }

            return (
              <Card
                key={key}
                shadow="sm"
                radius="md"
                className="p-4 border border-gray-200"
              >
                <Stack>
                  <Text size="lg" className="uppercase font-semibold">
                    {key.replace(/_/g, " ")}
                  </Text>
                  <Divider />
                  <Text size="md" fw={500} className="text-gray-900">
                    {String(value)}
                  </Text>
                </Stack>
              </Card>
            );
          })}
        </div>

        <Timeline data={dataRevision} />
      </Drawer>
    </>
  );
}
