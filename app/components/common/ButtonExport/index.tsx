/* eslint-disable @typescript-eslint/no-explicit-any */
import { Button, Group, Modal, Radio, Stack, Text } from "@mantine/core";
import { useDisclosure } from "@mantine/hooks";
import { IconDownload, IconFileTypeXls } from "@tabler/icons-react";
import { useState } from "react";
import classes from "../../../../styles/css/custom-radio.module.css";

const ButtonExport = ({ table, handleExport }: any) => {
  const [value, setValue] = useState<string | null>(null);
  const [opened, { open, close }] = useDisclosure(false);
  const dataRadio = [
    {
      name: "Export All Data",
      data: "all_data",
      description: "Exporting all data that shows in the table",
    },
    {
      name: "Export Selected Rows",
      data: "selected_rows",
      description: "Only exporting rows that have been selected by the user",
    },
  ];
  const handleClose = () => {
    setValue("");
    close();
  };
  const RadioGroupCards = dataRadio.map((item) => (
    <Radio.Card
      className={classes.root}
      radius="md"
      value={item.data}
      key={item.name}
      disabled={
        item.data == "selected_rows"
          ? !table.getIsSomeRowsSelected() && !table.getIsAllRowsSelected()
          : false
      }
    >
      <Group wrap="nowrap" align="flex-start">
        <Radio.Indicator
          disabled={
            item.data == "selected_rows"
              ? !table.getIsSomeRowsSelected() && !table.getIsAllRowsSelected()
              : false
          }
        />
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
                <IconFileTypeXls size={30} />
                Choose Export
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
            onClick={() => handleExport({ value })}
          >
            Export
          </Button>
        </Modal.Header>
      </Modal>

      <Button
        className="shadow-md"
        size="sm"
        variant="outline"
        color="gray"
        radius={9}
        leftSection={<IconDownload />}
        opacity={0.6}
        c="dimmed"
        onClick={open}
      >
        <Text className="font-satoshi" size="sm">
          Export
        </Text>
      </Button>
    </>
  );
};

export default ButtonExport;
