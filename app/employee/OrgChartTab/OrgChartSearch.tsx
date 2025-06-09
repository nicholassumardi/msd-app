/* eslint-disable @typescript-eslint/no-explicit-any */

import React, { useState, useEffect } from "react";
import {
  Group,
  Select,
  Button,
  Card,
  Title,
  Text,
  Divider,
  ActionIcon,
  Tooltip,
  Badge,
  Transition,
} from "@mantine/core";
import {
  IconSearch,
  IconFilter,
  IconUser,
  IconBuilding,
  IconBriefcase,
  IconX,
  IconRefresh,
  IconMinus,
  IconPlus,
  IconImageInPicture,
  IconFileTypePdf,
} from "@tabler/icons-react";
import NoDataFound from "@/components/NoDataFound/NoDataFound";
import EmployeeOrgChart from "./OrgChart";
import useEmployeeData from "../../../hooks/EmployeeCentre";
import { useEmployeeDataContext } from "../../../context/EmployeeCentre";

const OrgChartSearch = () => {
  const {
    setGlobalFilter,
    idCompany,
    setIdCompany,
    idDepartment,
    setIdDepartment,
    dataEmployee,
    dataCompany,
    dataDepartment,
    handleGetEmployeeDetail,
  } = useEmployeeData();
  const {
    UUID,
    setUUID,
    handleSelectChange,
    foundEmployee,
    setFoundEmployee,
    tree,
    getChild,
    getParent,
  } = useEmployeeDataContext();
  const [isFilterOpen, setIsFilterOpen] = useState(false);
  const [activeFilters, setActiveFilters] = useState(0);

  // Track active filters count
  useEffect(() => {
    let count = 0;
    if (idCompany) count++;
    if (idDepartment) count++;
    if (UUID) count++;
    setActiveFilters(count);
  }, [UUID, idCompany, idDepartment]);

  // Clear all filters
  const handleClearAll = () => {
    setUUID(null);
    setIdCompany(null);
    setIdDepartment(null);
    setFoundEmployee(null);
    if (setGlobalFilter) {
      setGlobalFilter("");
    }
  };

  return (
    <>
      <Card className="w-full mt-8 shadow-md border border-gray-100">
        <Group p="apart" className="mb-4">
          <Group>
            <Title order={4} className="text-gray-800 font-medium">
              Organization Chart
            </Title>
            {activeFilters > 0 && (
              <Badge
                size="md"
                radius="sm"
                className="bg-blue-100 text-blue-700"
              >
                {activeFilters} active filter{activeFilters !== 1 ? "s" : ""}
              </Badge>
            )}
          </Group>

          <Group>
            {activeFilters > 0 && (
              <Tooltip label="Clear all filters" position="bottom">
                <ActionIcon
                  color="gray"
                  variant="subtle"
                  onClick={handleClearAll}
                  className="hover:bg-gray-100"
                >
                  <IconX size={18} />
                </ActionIcon>
              </Tooltip>
            )}
            <Tooltip
              label={isFilterOpen ? "Hide filters" : "Show filters"}
              position="bottom"
            >
              <Button
                variant="subtle"
                color="blue"
                leftSection={<IconFilter size={16} />}
                onClick={() => setIsFilterOpen(!isFilterOpen)}
                className="hover:bg-blue-50"
              >
                {isFilterOpen ? "Hide Filters" : "Show Filters"}
              </Button>
            </Tooltip>
          </Group>
        </Group>

        <Transition
          mounted={activeFilters > 0 ? true : isFilterOpen}
          transition="fade"
          duration={200}
        >
          {(styles) => (
            <div style={styles}>
              <Divider className="mb-4" />

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <Text size="sm" w={500} className="mb-1 text-gray-700">
                    Employee Name
                  </Text>
                  <Select
                    placeholder="Search by employee name..."
                    size="md"
                    radius="md"
                    searchable
                    clearable
                    limit={5}
                    leftSection={
                      <IconUser size={16} className="text-gray-500" />
                    }
                    className="shadow-sm"
                    onChange={(value) => {
                      handleSelectChange(value);
                    }}
                    value={UUID}
                    styles={{
                      input: {
                        fontWeight: 500,
                      },
                    }}
                    data={dataEmployee}
                  />
                </div>

                <div>
                  <Text size="sm" w={500} className="mb-1 text-gray-700">
                    Company
                  </Text>
                  <Select
                    placeholder="Filter by company..."
                    size="md"
                    radius="md"
                    searchable
                    value={idCompany ?? null}
                    onChange={(idCompany) => {
                      setIdCompany(idCompany);
                      setIdDepartment(null);
                      setUUID("");
                    }}
                    clearable
                    leftSection={
                      <IconBuilding size={16} className="text-gray-500" />
                    }
                    className="shadow-sm"
                    classNames={{
                      input: "bg-white border-gray-200 focus:border-blue-500",
                      dropdown: "border border-gray-200 shadow-lg",
                      option: "hover:bg-blue-50",
                    }}
                    styles={{
                      input: {
                        fontWeight: 500,
                      },
                    }}
                    data={dataCompany}
                  />
                </div>

                <div>
                  <Text size="sm" w={500} className="mb-1 text-gray-700">
                    Department
                  </Text>
                  <Select
                    placeholder="Filter by department..."
                    size="md"
                    radius="md"
                    searchable
                    value={idDepartment ?? null}
                    onChange={(idDepartment) => setIdDepartment(idDepartment)}
                    clearable
                    leftSection={
                      <IconBriefcase size={16} className="text-gray-500" />
                    }
                    className="shadow-sm"
                    classNames={{
                      input: "bg-white border-gray-200 focus:border-blue-500",
                      dropdown: "border border-gray-200 shadow-lg",
                      option: "hover:bg-blue-50",
                    }}
                    styles={{
                      input: {
                        fontWeight: 500,
                      },
                    }}
                    data={dataDepartment}
                  />
                </div>
              </div>
            </div>
          )}
        </Transition>

        <Group p="apart" className="mt-4">
          <div>
            {activeFilters > 0 && (
              <Text size="sm" c="dimmed" className="italic">
                {activeFilters} filter{activeFilters !== 1 ? "s" : ""} applied
              </Text>
            )}
          </div>

          <Group p="sm">
            {activeFilters > 0 && (
              <Button
                variant="subtle"
                color="gray"
                leftSection={<IconRefresh size={16} />}
                onClick={handleClearAll}
                className="hover:bg-gray-100"
                size="md"
              >
                Reset
              </Button>
            )}

            <Button
              radius="md"
              size="md"
              leftSection={<IconSearch size={16} />}
              className="bg-blue-600 hover:bg-blue-700 transition-colors shadow-md"
              onClick={() => handleGetEmployeeDetail(UUID)}
              disabled={activeFilters === 0}
            >
              Search
            </Button>
          </Group>
        </Group>
        {foundEmployee && (
          <div className="p-4 ">
            <Group gap="sm" className="flex flex-wrap md:flex-nowrap gap-2">
              <div className="flex-1">
                <Group p="left" gap="xs">
                  <Tooltip
                    label="Download as Image"
                    position="bottom"
                    withArrow
                  >
                    <Button
                      leftSection={<IconImageInPicture size={16} />}
                      variant="outline"
                      color="blue"
                      id="download-image"
                      className="text-sm border border-blue-500 hover:bg-blue-50"
                    >
                      <span className="hidden sm:inline">Image</span>
                    </Button>
                  </Tooltip>

                  <Tooltip label="Download as PDF" position="bottom" withArrow>
                    <Button
                      leftSection={<IconFileTypePdf size={16} />}
                      variant="outline"
                      color="blue"
                      id="download-pdf"
                      className="text-sm border border-blue-500 hover:bg-blue-50"
                    >
                      <span className="hidden sm:inline">PDF</span>
                    </Button>
                  </Tooltip>
                </Group>
              </div>

              <div className="flex-none">
                <Group
                  p="md"
                  className="border rounded-md px-1 py-1 border-gray-300"
                >
                  <Tooltip label="Zoom Out" position="bottom" withArrow>
                    <ActionIcon
                      color="blue"
                      variant="subtle"
                      id="zoom-out"
                      className="hover:bg-gray-100"
                    >
                      <IconMinus size={16} />
                    </ActionIcon>
                  </Tooltip>

                  <Tooltip label="Zoom In" position="bottom" withArrow>
                    <ActionIcon
                      color="blue"
                      variant="subtle"
                      id="zoom-in"
                      className="hover:bg-gray-100"
                    >
                      <IconPlus size={16} />
                    </ActionIcon>
                  </Tooltip>
                </Group>
              </div>
            </Group>
          </div>
        )}
      </Card>
      {foundEmployee && tree ? (
        <EmployeeOrgChart
          tree={tree}
          getChild={getChild}
          getParent={getParent}
        />
      ) : (
        <NoDataFound />
      )}
    </>
  );
};

export default OrgChartSearch;
