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
  IconPlus,
} from "@tabler/icons-react";
import NoDataFound from "@/components/NoDataFound/NoDataFound";
import StructureDetails from "./StructureDetails";
import useEmployeeData from "../../../hooks/EmployeeCentre";
import dynamic from "next/dynamic";
import { useEmployeeDataContext } from "../../../context/EmployeeCentre";
import ImportDropzone from "@/components/common/Dropzone";

const StructureAdd = dynamic(() => import("../StructureTab/StructureAdd"), {
  ssr: false,
});

const StructureSearch = () => {
  const {
    setGlobalFilter,
    setIdStructure,
    idStructure,
    idCompany,
    setIdCompany,
    idDepartment,
    setIdDepartment,
    dataStructure,
    dataCompany,
    dataDepartment,
    handleGetStructureDetail,
    foundStructure,
    setFoundStructure,
    dataRki,
  } = useEmployeeData();
  const { openModal } = useEmployeeDataContext();
  const [isFilterOpen, setIsFilterOpen] = useState(false);
  const [activeFilters, setActiveFilters] = useState(0);

  // Track active filters count
  useEffect(() => {
    let count = 0;
    if (idCompany) count++;
    if (idDepartment) count++;
    if (idStructure) count++;
    setActiveFilters(count);
  }, [idStructure, idCompany, idDepartment]);

  // Clear all filters
  const handleClearAll = () => {
    setIdStructure("");
    setIdCompany(null);
    setIdDepartment(null);
    setFoundStructure(null);
    if (setGlobalFilter) {
      setGlobalFilter("");
    }
  };

  return (
    <>
      <StructureAdd />
      <Card className="w-full mt-8 mb-6 shadow-md border border-gray-100">
        <Group p="apart" className="mb-4">
          <Group>
            <Title order={4} className="text-gray-800 font-medium">
              Structure Search
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
            <Tooltip label="Create new structure" position="bottom">
              <Button
                variant="subtle"
                color="blue"
                leftSection={<IconPlus size={16} />}
                onClick={() => {
                  openModal("Add");
                }}
                className="hover:bg-blue-50"
              >
                New Structure
              </Button>
            </Tooltip>
            <ImportDropzone url="/api/admin/import/structure?type=importStructureMapping" />
          </Group>
        </Group>

        <Transition mounted={isFilterOpen} transition="fade" duration={200}>
          {(styles) => (
            <div style={styles}>
              <Divider className="mb-4" />

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                {setGlobalFilter && (
                  <div>
                    <Text size="sm" w={500} className="mb-1 text-gray-700">
                      Structure Name
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
                      // onSearchChange={(value) => setGlobalFilter(value || "")}
                      // searchValue={globalFilter}
                      onChange={(value) => {
                        setIdStructure(value || "");
                        const selectedOption = dataStructure.find(
                          (opt) => opt.value == value
                        );
                        setGlobalFilter(
                          selectedOption ? selectedOption.label : ""
                        );
                        handleGetStructureDetail(value);
                      }}
                      onSelect={() => setGlobalFilter("")}
                      value={idStructure}
                      styles={{
                        input: {
                          fontWeight: 500,
                        },
                      }}
                      data={dataStructure}
                    />
                  </div>
                )}

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
                      setIdStructure("");
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
              onClick={() => handleGetStructureDetail(idStructure)}
              disabled={activeFilters === 0}
            >
              Search
            </Button>
          </Group>
        </Group>
        <Group>
          <div className="flex-1 overflow-auto mb-4">
            {foundStructure ? (
              <StructureDetails structure={foundStructure} dataRki={dataRki} />
            ) : (
              <NoDataFound />
            )}
          </div>
        </Group>
      </Card>
    </>
  );
};

export default StructureSearch;
