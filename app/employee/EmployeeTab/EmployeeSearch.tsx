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
  SelectProps,
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
  IconIdBadge,
} from "@tabler/icons-react";
import EmployeeDetails from "./EmployeeDetails";
import NoDataFound from "@/components/NoDataFound/NoDataFound";
import useEmployeeData from "../../../hooks/EmployeeCentre";
import { useEmployeeDataContext } from "../../../context/EmployeeCentre";
import { EmployeeAddForm } from "./EmployeeAdd";
import ImportDropzone from "@/components/common/Dropzone";

const EmployeeSearch = () => {
  const [isFilterOpen, setIsFilterOpen] = useState(false);
  const [activeFilters, setActiveFilters] = useState(0);

  const {
    setGlobalFilter,
    idCompany,
    setIdCompany,
    idDepartment,
    setIdDepartment,
    dataNIKNIP,
    dataEmployee,
    dataCompany,
    dataDepartment,
    handleGetEmployeeDetail,
    foundEmployee,
    setFoundEmployee,
    formEmployee: form,
  } = useEmployeeData();

  const { UUID, setUUID } = useEmployeeDataContext();
  const [isTimelineOpen, setIsTimelineOpen] = useState(false);
  const [modalTitle, setModalTitle] = useState("");
  const [modalContent, setModalContent] = useState<React.ReactNode>(null);
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

  const renderAutocompleteOption: SelectProps["renderOption"] = ({
    option,
  }: any) => (
    <Group gap="sm">
      <div>
        <Text className="font-satoshi" size="xl">
          {option.label}
        </Text>
        <Text size="md" opacity={0.5}>
          {option.code}
        </Text>
      </div>
    </Group>
  );

  return (
    <Card className="w-full mt-8 mb-6 shadow-md border border-gray-100">
      <Group p="apart" className="mb-4">
        <Group>
          <Title order={4} className="text-gray-800 font-medium">
            Employee Search
          </Title>
          {activeFilters > 0 && (
            <Badge size="md" radius="sm" className="bg-blue-100 text-blue-700">
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
              className="hover:bg-blue-50"
              onClick={() => {
                setIsTimelineOpen(true);
                setModalTitle("Add New Employee");
                setModalContent(
                  <EmployeeAddForm
                    form={form}
                    setIsTimelineOpen={setIsTimelineOpen}
                    dataCompany={dataCompany}
                  />
                );
              }}
            >
              New Employee
            </Button>
          </Tooltip>
          <ImportDropzone url="/api/admin/import/employee" />
        </Group>
      </Group>

      <Transition
        mounted={activeFilters > 1 ? true : isFilterOpen}
        transition="fade"
        duration={200}
      >
        {(styles) => (
          <div style={styles}>
            <Divider className="mb-4" />
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <Text size="sm" w={500} className="mb-1 text-gray-700">
                  Employee ID
                </Text>
                <Select
                  placeholder="Search by employee ID..."
                  size="md"
                  radius="md"
                  searchable
                  clearable
                  limit={5}
                  renderOption={renderAutocompleteOption}
                  leftSection={<IconUser size={16} className="text-gray-500" />}
                  className="shadow-sm"
                  onChange={(value) => {
                    setUUID(value || "");
                    const selectedOption = dataEmployee.find(
                      (opt) => opt.value == value
                    );
                    setGlobalFilter(selectedOption ? selectedOption.label : "");
                    handleGetEmployeeDetail(value);
                  }}
                  onSelect={() => setGlobalFilter("")}
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
                  NIK/ NIP
                </Text>
                <Select
                  placeholder="Search by NIK & NIP"
                  size="md"
                  radius="md"
                  searchable
                  clearable
                  limit={10}
                  leftSection={
                    <IconIdBadge size={16} className="text-gray-500" />
                  }
                  className="shadow-sm"
                  onChange={(value) => {
                    setUUID(value || "");
                    const selectedOption = dataEmployee.find(
                      (opt) => opt.value == value
                    );
                    setGlobalFilter(selectedOption ? selectedOption.label : "");
                    handleGetEmployeeDetail(value);
                  }}
                  value={UUID}
                  styles={{
                    input: {
                      fontWeight: 500,
                    },
                  }}
                  data={dataNIKNIP}
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
                    setUUID(null);
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
      <Group>
        <div className="flex-1 overflow-auto mb-4">
          {foundEmployee ? (
            <EmployeeDetails
              employee={foundEmployee}
              form={form}
              dataCompany={dataCompany}
              UUID={UUID}
            />
          ) : (
            <NoDataFound />
          )}
        </div>
      </Group>
      {isTimelineOpen && (
        <div className="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4">
          <div className="bg-white dark:bg-neutral-900 rounded-xl max-w-5xl w-full max-h-[90vh] overflow-y-auto">
            <div className="p-6 border-b border-neutral-200 dark:border-neutral-700 flex justify-between items-center">
              <h3 className="text-2xl font-bold text-neutral-800 dark:text-neutral-200">
                {modalTitle}
              </h3>
              <button
                onClick={() => setIsTimelineOpen(false)}
                className="text-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-800 p-2 rounded-full"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  className="h-6 w-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>
            {modalContent}
          </div>
        </div>
      )}
    </Card>
  );
};

export default EmployeeSearch;
