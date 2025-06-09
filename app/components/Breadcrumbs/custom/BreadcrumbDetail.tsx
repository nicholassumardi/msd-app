import { Button, Select, Text, TextInput } from "@mantine/core";
import { IconEye } from "@tabler/icons-react";
import Link from "next/link";
interface BreadcrumbProps {
  pageName: string;
  url: string;
  globalFilter?: string;
  setGlobalFilter?: React.Dispatch<React.SetStateAction<string>>;
}
const BreadcrumbDetail = ({
  pageName,
  url,
  globalFilter,
  setGlobalFilter,
}: BreadcrumbProps) => {
  return (
    <div className="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <h2 className="text-title-md2 font-semibold text-black dark:text-white">
        {pageName}
      </h2>
      <nav>
        <ol className="flex items-center gap-2">
          {setGlobalFilter && (
            <li>
              <TextInput
                placeholder="Search employees..."
                c="dimmed"
                fw={500}
                size="sm"
                radius={9}
                onChange={(event) => setGlobalFilter(event.currentTarget.value)}
                value={globalFilter}
                opacity={0.6}
                className="shadow-md font-satoshi"
              />
            </li>
          )}
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
              href={url}
            >
              <Text className="font-satoshi" fw={700} size="sm">
                See Details
              </Text>
            </Button>
          </li>
        </ol>
      </nav>
    </div>
  );
};

export default BreadcrumbDetail;
