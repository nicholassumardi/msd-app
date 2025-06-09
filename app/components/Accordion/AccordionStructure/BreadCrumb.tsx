/* eslint-disable @typescript-eslint/no-explicit-any */
import { Button, Select, Text } from "@mantine/core";
import { IconEye, IconHierarchy3, IconPlus } from "@tabler/icons-react";
import Link from "next/link";
import { option } from "../../../../pages/types/option";

interface ChildComponenetProps {
  openModal: any;
  dataDepartment: option[];
  value: string | null;
  setValue: React.Dispatch<React.SetStateAction<string | null>>;
}

const BreadCrumbStructure: React.FC<ChildComponenetProps> = ({
  openModal,
  dataDepartment,
  value,
  setValue,
}) => {
  return (
    <div className="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <h2 className="text-title-md2 font-semibold text-black dark:text-white">
        Database Structure
      </h2>

      <nav>
        <ol className="flex items-center gap-2">
          <li>
            <Select
              c="dimmed"
              fw={500}
              size="sm"
              color="gray"
              radius={9}
              searchable
              opacity={0.6}
              className="shadow-md font-satoshi"
              value={value ? value : "1"}
              onChange={(value) => setValue(value)}
              limit={10}
              data={dataDepartment}
              clearable
            />
          </li>
          <li>
            <Button
              className="shadow-md"
              size="sm"
              variant="outline"
              color="gray"
              radius={9}
              leftSection={<IconPlus />}
              opacity={0.6}
              c="dimmed"
              onClick={openModal}
            >
              <Text className="font-satoshi" fw={700} size="sm">
                Add Plot
              </Text>
            </Button>
          </li>
          <li>
            <Button
              className="shadow-md"
              size="sm"
              variant="outline"
              color="gray"
              radius={9}
              leftSection={<IconEye />}
              opacity={0.6}
              c="dimmed"
              component={Link}
              href="/structure/details"
            >
              <Text className="font-satoshi" fw={700} size="sm">
                See Details
              </Text>
            </Button>
          </li>
          <li>
            <Button
              className="shadow-md"
              size="sm"
              variant="outline"
              color="gray"
              radius={9}
              leftSection={<IconHierarchy3 />}
              opacity={0.6}
              c="dimmed"
              component={Link}
              href="/structure/chart"
            >
              <Text className="font-satoshi" fw={700} size="sm">
                See Hierarchy Chart
              </Text>
            </Button>
          </li>
        </ol>
      </nav>
    </div>
  );
};

export default BreadCrumbStructure;
