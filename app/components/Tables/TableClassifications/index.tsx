"use client";

import { rem, Tabs } from "@mantine/core";
import { IconBriefcase, IconMoodKid, IconOld } from "@tabler/icons-react";
import TableDataGeneration from "./General/TableData";
import "@mantine/core/styles.css";
import "@mantine/dates/styles.css"; //if using mantine date picker features
import "mantine-react-table/styles.css";
import TableDataAge from "./Age/TableData";
import TableDataWorkDuration from "./WorkDuration/TableData";

const TabsDetail = () => {
  const iconStyle = { width: rem(35), height: rem(35) };

  return (
    <Tabs variant="outline" radius="md" defaultValue="generation">
      <Tabs.List grow>
        <Tabs.Tab
          value="generation"
          leftSection={<IconMoodKid style={iconStyle} />}
        >
          Generation
        </Tabs.Tab>
        <Tabs.Tab value="age" leftSection={<IconOld style={iconStyle} />}>
          Age
        </Tabs.Tab>
        <Tabs.Tab
          value="duration"
          leftSection={<IconBriefcase style={iconStyle} />}
        >
          Working Duration
        </Tabs.Tab>
      </Tabs.List>

      <Tabs.Panel value="generation">
        <TableDataGeneration />
      </Tabs.Panel>
      <Tabs.Panel value="age">
        <TableDataAge />
      </Tabs.Panel>
      <Tabs.Panel value="duration">
        <TableDataWorkDuration />
      </Tabs.Panel>
    </Tabs>
  );
};

export default TabsDetail;
