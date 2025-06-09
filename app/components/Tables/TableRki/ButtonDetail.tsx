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
  Title,
} from "@mantine/core";
import { IconInfoSquare } from "@tabler/icons-react";
import axios from "axios";
import { useState } from "react";
import { RKI } from "../../../../pages/api/admin/rki";
import FormRKI from "./Form";
import { option } from "../../../../pages/types/option";

interface FormComponent {
  position_job_code?: string;
  getData: () => Promise<void>;
  setIsLoading: React.Dispatch<React.SetStateAction<boolean>>;
  dataIkw: option[];
  dataPositionCode: option[];
}

const ButtonDetail: React.FC<FormComponent> = ({
  position_job_code,
  getData,
  setIsLoading,
  dataIkw,
  dataPositionCode,
}) => {
  const [openedDrawer, setOpenedDrawer] = useState(false);
  const [dataRki, setDataRki] = useState<RKI[]>([]);

  const handleDataRKI = async () => {
    try {
      const response = await axios.get(
        `/api/admin/rki?type=showRKIByPositionJobCode`,
        {
          params: {
            position_job_code: position_job_code,
          },
        }
      );
      setDataRki(response.data.data);
    } catch (err: any) {
      ErrorNotification({
        title: "Server Error",
        message: err.response.data.error,
      });
    }
  };
  const openDrawer = () => setOpenedDrawer(true);
  const closeDrawer = () => setOpenedDrawer(false);

  function RkiCard({ rkiData }: { rkiData: any }) {
    return (
      <Paper
        p="md"
        shadow="sm"
        mb="xl"
        style={{ borderLeft: "4px solid #4c6ef5" }}
      >
        <Stack gap="sm">
          <Group justify="space-between">
            <Title order={3} c="dimmed">
              {rkiData.unique_code}
            </Title>
            <FormRKI
              id={rkiData.id}
              getData={getData}
              setIsLoading={setIsLoading}
              dataIkw={dataIkw}
              dataPositionCode={dataPositionCode}
            />
          </Group>
          {/* Main Information Section */}
          <Group>
            <Box>
              <Text size="lg" c="dimmed">
                IKW Name
              </Text>
              <Text w={500}>{rkiData.ikw_name || "-"}</Text>
            </Box>
            <Box>
              <Text size="lg" c="dimmed">
                Department
              </Text>
              <Text w={500}>{rkiData.department || "-"}</Text>
            </Box>
          </Group>

          {/* Detailed Information Section */}
          <Divider my="sm" label="Additional Details" labelPosition="center" />
          <Group>
            <Box mx="xl">
              <Text size="lg" c="dimmed">
                IKW Code
              </Text>
              <Text>{rkiData.no_ikw || "-"}</Text>
            </Box>
            <Box mx="xl">
              <Text size="lg" c="dimmed">
                Job Code
              </Text>
              <Text>{rkiData.position_job_code || "-"}</Text>
            </Box>
            <Box mx="xl">
              <Text size="lg" c="dimmed">
                Total Pages
              </Text>
              <Text>{rkiData.ikw_page || "-"}</Text>
            </Box>
            <Box mx="xl">
              <Text size="lg" c="dimmed">
                Training Time
              </Text>
              <Text>
                {rkiData.training_time
                  ? `${rkiData.training_time} minutes`
                  : "-"}
              </Text>
            </Box>
          </Group>
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
          handleDataRKI();
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
              {position_job_code ?? "-"}
            </div>
          </Text>
        }
      >
        {dataRki.length > 0 ? (
          <Card shadow="xs" padding="xl">
            {dataRki?.map((rkiData) => (
              <RkiCard key={rkiData.id} rkiData={rkiData} />
            ))}
          </Card>
        ) : (
          <Card shadow="xs" padding="xl">
            <Text size="xl">No data found</Text>
          </Card>
        )}
      </Drawer>
    </>
  );
};

export default ButtonDetail;
