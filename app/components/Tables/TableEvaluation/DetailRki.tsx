"use client";

import {
  Table,
  Group,
  Title,
  Paper,
  Avatar,
  Badge,
  Button,
  // Menu,
  Text,
} from "@mantine/core";
// import { IconChevronDown } from "@tabler/icons-react";
import { motion } from "framer-motion";
import { DetailRKI, PaginationData } from "./ButtonDetail";
import { useEffect } from "react";

type DetailRKIComponent = {
  dataDetailRKI: DetailRKI[];
  dataMaxRevision: number;
  pagination: PaginationData;
  setPagination: React.Dispatch<React.SetStateAction<PaginationData>>;
  handleDetailRKI: () => Promise<void>;
};

const DetailRKITable: React.FC<DetailRKIComponent> = ({
  dataDetailRKI,
  dataMaxRevision,
  pagination,
  setPagination,
  handleDetailRKI,
}) => {
  const columnColors = {
    no: "bg-gradient-to-b from-purple-50 to-purple-100 border-t-2 border-purple-200",
    code: "bg-gradient-to-b from-blue-50 to-blue-100 border-t-2 border-blue-200",
    name: "bg-gradient-to-b from-cyan-50 to-cyan-100 border-t-2 border-cyan-200",
    desc: "bg-gradient-to-b from-green-50 to-green-100 border-t-2 border-green-200",
  };
  const {
    current_page = 1,
    last_page = 1,
    per_page = 10,
    total = 0,
  } = pagination || {};
  const showingStart = (current_page - 1) * per_page + 1;

  const handlePageChange = (page: number) => {
    if (page < 1 || page > last_page) return;
    setPagination((prev) => ({
      ...prev,
      current_page: page,
    }));
  };

  useEffect(() => {
    handleDetailRKI();
  }, [pagination?.current_page]);

  const pageButtons = Array.from({ length: last_page }, (_, i) => (
    <Button
      key={i + 1}
      variant={i + 1 == current_page ? "filled" : "subtle"}
      size="sm"
      color="violet"
      className="px-3"
      onClick={() => handlePageChange(i + 1)}
    >
      {i + 1}
    </Button>
  ));
  const revisionNo = Array.from({ length: dataMaxRevision }, (_, i) => i);
  const rows = Object.values(dataDetailRKI || {}).map((item, i) => (
    <motion.tr
      key={i}
      whileHover={{ backgroundColor: "#f8f9fa" }}
      className="cursor-pointer"
    >
      <td className="px-6 py-4 whitespace-nowrap text-sm">
        <span className="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
          {i + 1}
        </span>
      </td>
      <td className="px-6 py-4 whitespace-nowrap">
        <Badge color="blue" variant="light" className="bg-blue-100">
          {item.ikw_code}
        </Badge>
      </td>
      <td className="px-6 py-4 whitespace-nowrap">
        <Group>
          <Avatar size="sm" radius="xl" color="cyan">
            {item.ikw_name.charAt(0)}
          </Avatar>
          <div>
            <Text size="sm" className="font-medium">
              {item.ikw_name}
            </Text>
          </div>
        </Group>
      </td>
      {revisionNo.map((_, i) => {
        // Convert header number to string to match data format
        const targetRev = i;
        // Count revisions matching the current header
        const count = item.revisions.filter(
          (rev) => rev.revision_no == targetRev
        ).length;

        return (
          <td
            key={i}
            className="px-6 py-4 text-sm text-gray-600 max-w-xs truncate"
          >
            {/* Show count if > 0, otherwise show a dash */}
            {count > 0 ? count : 0}
          </td>
        );
      })}
    </motion.tr>
  ));

  return (
    <Paper shadow="md" radius="lg" className="overflow-hidden ">
      <div className="bg-white p-4 border-b border-violet-400 mt-5">
        <Group p="apart" className="border-violet-400">
          <div>
            <Title order={3} className="text-gray-800">
              RKI Table
            </Title>
            <Text size="sm" c="dimmed" className="mt-1">
              Manage your key indicators
            </Text>
          </div>
        </Group>
      </div>

      <div className="max-w-full overflow-x-auto">
        <Table className="min-w-[800px]">
          <thead>
            <tr>
              <th
                className={`px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider ${columnColors.no}`}
              >
                No
              </th>
              <th
                className={`px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider ${columnColors.code}`}
              >
                IKW Code
              </th>
              <th
                className={`px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider ${columnColors.name}`}
              >
                IKW Name
              </th>
              {revisionNo.map((i) => (
                <th
                  key={i}
                  className={`px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider ${columnColors.desc}`}
                >
                  {i}
                </th>
              ))}
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {/* Example rows */}
            {rows}
          </tbody>
        </Table>
      </div>

      <div className="bg-gray-50 px-6 py-3 border-t border-gray-200">
        <Group p="apart">
          <Text size="sm" c="dimmed">
            Showing {showingStart} of {total} entries
          </Text>
          <Group>
            <Button
              variant="subtle"
              size="sm"
              disabled={current_page === 1}
              onClick={() => handlePageChange(current_page - 1)}
            >
              Previous
            </Button>
            {pageButtons}
            <Button
              variant="subtle"
              size="sm"
              disabled={current_page === last_page}
              onClick={() => handlePageChange(current_page + 1)}
            >
              Next
            </Button>
          </Group>
        </Group>
      </div>
    </Paper>
  );
};

export default DetailRKITable;
