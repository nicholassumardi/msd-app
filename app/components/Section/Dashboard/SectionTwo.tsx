/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";

import React from "react";
import { Avatar, Group, Select, SelectProps, Text } from "@mantine/core";
import "@mantine/core/styles.css";
import "@mantine/dates/styles.css"; //if using mantine date picker features
import "mantine-react-table/styles.css";
import { option } from "../../../../pages/types/option";

interface DashboardComponent {
  value: string | null;
  dataCompanies: option[];
  setValue: React.Dispatch<React.SetStateAction<string | null>>;
}

const SectionTwo: React.FC<DashboardComponent> = ({
  value,
  dataCompanies,
  setValue,
}) => {
  const renderAutocompleteOption: SelectProps["renderOption"] = ({
    option,
  }: any) => (
    <Group gap="sm">
      <Avatar src={"/images/wings.png"} size={36} radius="xl" />
      <div>
        <Text className="font-satoshi" size="xl">
          {" "}
          {option.label}
        </Text>
        <Text size="xs" opacity={0.5}>
          {option.code}
        </Text>
      </div>
    </Group>
  );
  return (
    <div className="col-span-12 rounded-sm border border-stroke bg-white p-7.5 shadow-default dark:border-strokedark dark:bg-boxdark xl:col-span-4 font-satoshi">
      <div className="mb-4 justify-between gap-4 sm:flex">
        <div>
          <h4 className="text-xl  font-semibold text-black dark:text-white">
            Companies
          </h4>
        </div>
        <div>
          <div className="relative z-20 inline-block"></div>
        </div>
      </div>

      <div>
        <div
          id="SectionTwo"
          className="grid grid-cols-12 justify-center items-center"
        >
          <div className="col-span-12">
            <Select
              mt="xl"
              c="dimmed"
              fw={500}
              className="font-serif"
              placeholder="Pick Branch"
              defaultValue="1"
              renderOption={renderAutocompleteOption}
              size="xl"
              radius={10}
              searchable
              value={value ? value : ""}
              onChange={(value) => {
                setValue(value);
              }}
              data={dataCompanies}
            />
          </div>
        </div>
      </div>
    </div>
  );
};

export default SectionTwo;
