/* eslint-disable @typescript-eslint/no-explicit-any */
import { Button, Group, Modal, Radio, Stack, Text } from "@mantine/core";
import { useDisclosure } from "@mantine/hooks";
import { IconAdjustmentsAlt, IconFilter } from "@tabler/icons-react";
import { useState } from "react";
import classes from "../../../../styles/css/custom-radio.module.css";
import axios from "axios";
import ErrorNotification from "@/components/Notifications/ErrorNotification";

const handleFilter = async ({ id, value, setData }: any) => {
  try {
    const [key, val] = value.split("=");
    const params = { [key]: val };

    const response = await axios.get(
      `/api/admin/structure/${id}?type=showMappingHierarchy`,
      { params: params }
    );
    setData(response.data.data.data);
  } catch (err: any) {
    ErrorNotification({
      title: "Server Error",
      message: err.response.data.error,
    });
  }
};

const ButtonFilter = ({ id, setData }: { id: string | null; setData: any }) => {
  const [value, setValue] = useState<string | null>(null);
  const [opened, { open, close }] = useDisclosure(false);
  const [checked, setChecked] = useState<Record<string, boolean>>({});
  const data = [
    {
      name: "Staff",
      data: "employee_type=Staff",
      description: "Exporting all data that shows in the table",
    },
    {
      name: "Staff - Kepala Regu",
      data: "position=head",
      description: "Only exporting rows that have been selected by the user",
    },
  ];
  const handleClose = () => {
    setValue("");
    close();
  };

  const RadioGroupCards = data.map((item) => (
    <Radio.Card
      className={classes.root}
      radius="md"
      checked={checked[item.data] || false}
      onClick={() => {
        setChecked((prev) => ({
          ...Object.fromEntries(Object.keys(prev).map((k) => [k, false])),
          [item.data]: !prev[item.data],
        }));
      }}
      value={item.data}
      key={item.name}
    >
      <Group wrap="nowrap" align="flex-start">
        <Radio.Indicator />
        <div>
          <Text
            fw={700}
            className={(classes.label, "font-satoshi", "font-bold")}
          >
            {item.name}
          </Text>
          <Text className={classes.description}>{item.description}</Text>
        </div>
      </Group>
    </Radio.Card>
  ));

  return (
    <>
      <Modal
        opened={opened}
        onClose={handleClose}
        centered
        size="lg"
        transitionProps={{ transition: "scale", duration: 350 }}
        title={
          <>
            <Text size="xl" fw={700} className="font-satoshi font-bold">
              <div className="flex gap-2 items-center">
                <IconAdjustmentsAlt size={30} />
                Filter
              </div>
            </Text>
          </>
        }
      >
        <div>
          {
            <Radio.Group
              value={value}
              onChange={setValue}
              label="Pick one to export"
              description="Choose export method that you need"
            >
              <Stack pt="md" gap="xs">
                {RadioGroupCards}
              </Stack>
            </Radio.Group>
          }
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
            onClick={handleClose}
          >
            Close
          </Button>
          <Button
            variant="filled"
            color="violet"
            size="lg"
            type="submit"
            radius={12}
            onClick={() => handleFilter({ id, value, setData })}
          >
            Apply
          </Button>
        </Modal.Header>
      </Modal>

      <Button
        className="shadow-md mt-7"
        size="sm"
        variant="outline"
        color="gray"
        radius={9}
        leftSection={<IconFilter />}
        opacity={0.6}
        c="dimmed"
        onClick={open}
      >
        <Text className="font-satoshi" size="sm">
          Filter
        </Text>
      </Button>
    </>
  );
};

export default ButtonFilter;
