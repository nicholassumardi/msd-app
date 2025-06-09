"use client";

import { rem, Tabs } from "@mantine/core";
import { IconCategory2, IconIdBadge2 } from "@tabler/icons-react";
import "@mantine/core/styles.css";
import "@mantine/dates/styles.css"; //if using mantine date picker features
import "mantine-react-table/styles.css";
import TableDataPehCode from "./TablePehCode/TableData";
import TableDataCategory from "./TableCategory/TableData";

const TabsDetail = () => {
  const iconStyle = { width: rem(35), height: rem(35) };

  return (
    <Tabs variant="outline" radius="md" defaultValue="category">
      <Tabs.List grow>
        <Tabs.Tab
          value="category"
          leftSection={<IconCategory2 style={iconStyle} />}
        >
          Categories
        </Tabs.Tab>
        <Tabs.Tab
          value="peh_code"
          leftSection={<IconIdBadge2 style={iconStyle} />}
        >
          Position & PEH Code
        </Tabs.Tab>
      </Tabs.List>

      <Tabs.Panel value="category">
        <TableDataCategory />
      </Tabs.Panel>
      <Tabs.Panel value="peh_code">
        <TableDataPehCode />
      </Tabs.Panel>
    </Tabs>
  );
};

export default TabsDetail;
