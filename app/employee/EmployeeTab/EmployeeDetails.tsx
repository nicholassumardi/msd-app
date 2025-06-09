/* eslint-disable @typescript-eslint/no-explicit-any */
import "@mantine/core/styles.css";
import "@mantine/dates/styles.css";
import React, { useState } from "react";
import {
  Avatar,
  Group,
  Text,
  Title,
  ScrollArea,
  Badge,
  Accordion,
  Divider,
  ActionIcon,
  Tooltip,
} from "@mantine/core";
import {
  IconUser,
  IconBriefcase,
  IconBuildingBank,
  IconMail,
  IconId,
  IconSettings,
  IconBuildingSkyscraper,
  IconChevronRight,
  IconEdit,
  IconPrinter,
  IconDownload,
  IconInfoCircle,
  IconSquarePlus,
  IconIdBadge,
} from "@tabler/icons-react";
import { Timeline } from "@/components/ui/timelineEmployeeNumber";
import {
  Contract,
  EmployeementDetail,
  PersonalInformation,
  Skills,
} from "./EmployeeEdit";
import { useEmployeeDataContext } from "../../../context/EmployeeCentre";
import { cn } from "@lib/utils";

const EmployeeDetails = ({ employee, form, dataCompany, UUID }: any) => {
  const [isTimelineOpen, setIsTimelineOpen] = useState(false);
  const [modalTitle, setModalTitle] = useState("");
  const [modalContent, setModalContent] = useState<React.ReactNode>(null);
  const [activeTab, setActiveTab] = useState("personal");
  const { setGlobalTab, setUUID, handleSelectChange } =
    useEmployeeDataContext();

  if (!employee) return null;

  const timelineData =
    employee?.employee_numbers?.map((item: any, index: number) => ({
      title:
        index === 0
          ? "Initial Employee Number"
          : `Employee Number Update ${index}`,
      content: (
        <div className="bg-white dark:bg-neutral-900 p-4 rounded-lg shadow-md">
          <h4 className="text-lg font-semibold text-neutral-800 dark:text-neutral-200">
            Employee Number Details
          </h4>
          <p className="text-neutral-600 dark:text-neutral-400">
            Employee Number: {item.employee_number}
          </p>
          <p className="text-xs text-neutral-500 dark:text-neutral-400">
            Registry Date:{" "}
            {item.registry_date
              ? new Date(item.registry_date).toLocaleString()
              : "N/A"}
          </p>
          <p className="text-xs text-neutral-500 dark:text-neutral-400">
            Status: {parseInt(item.status) === 1 ? "Active" : "Inactive"}
          </p>
        </div>
      ),
    })) || [];

  const HorizontalDetailRow = ({
    label,
    value,
    onExtraClick,
    className,
    color,
  }: {
    label?: string;
    value: string;
    onExtraClick?: () => void;
    className?: string;
    color?: string;
  }) => (
    <div className="flex items-center py-2">
      {label && (
        <div className="w-1/3 text-gray-600 font-medium select-none">
          {label}:
        </div>
      )}
      <div className={cn("text-gray-800", className)}>
        {value}
        {onExtraClick && (
          <>
            <ActionIcon
              size="xs"
              variant="subtle"
              color={cn("violet", color)}
              onClick={onExtraClick}
            >
              <IconInfoCircle size={14} />
            </ActionIcon>
            <ActionIcon size="xs" variant="subtle" color={cn("violet", color)}>
              <IconEdit size={14} />
            </ActionIcon>
            <ActionIcon size="xs" variant="subtle" color={cn("violet", color)}>
              <IconSquarePlus size={14} />
            </ActionIcon>
          </>
        )}
      </div>
    </div>
  );

  const tabIcons = {
    personal: <IconUser size={20} />,
    professional: <IconBriefcase size={20} />,
    structure: <IconBuildingBank size={20} />,
    others: <IconSettings size={20} />,
  };

  // Calculate background pattern
  const getPatternBackground = () => {
    return {
      backgroundImage: `radial-gradient(circle at 25px 25px, rgba(255, 255, 255, 0.2) 2%, transparent 0%), 
                         radial-gradient(circle at 75px 75px, rgba(255, 255, 255, 0.2) 2%, transparent 0%)`,
      backgroundSize: "100px 100px",
    };
  };

  return (
    <div className="h-full flex flex-col rounded-xl shadow-lg overflow-hidden border border-gray-200 font-satoshi">
      {/* Top Header Actions */}
      <div className="bg-white px-6 py-3 border-b border-gray-200 flex justify-between items-center">
        <div className="flex items-center space-x-3">
          <Badge
            color={employee.status == "Aktif" ? "green" : "gray"}
            variant="filled"
            size="lg"
            radius="md"
            className="px-3 py-1"
          >
            {employee.status}
          </Badge>
          <Badge
            color="blue"
            variant="outline"
            size="lg"
            radius="md"
            className="px-3"
          >
            {employee.employee_type}
          </Badge>
        </div>
        <div className="flex items-center space-x-2">
          <Tooltip label="Print Employee Info">
            <ActionIcon variant="subtle" color="gray" size="lg">
              <IconPrinter size={20} />
            </ActionIcon>
          </Tooltip>
          <Tooltip label="Export Employee Data">
            <ActionIcon variant="subtle" color="gray" size="lg">
              <IconDownload size={20} />
            </ActionIcon>
          </Tooltip>
        </div>
      </div>

      {/* Profile Header with Glass Morphism */}
      <div
        className="p-8 text-white bg-gradient-to-r from-violet-700 via-blue-600 to-indigo-700"
        style={getPatternBackground()}
      >
        <div className="backdrop-blur-sm bg-violet-500 rounded-2xl p-6 border border-white/20">
          <div className="w-full flex justify-between items-start">
            {/* Left Section */}
            <div className="flex items-start gap-4">
              <div className="relative">
                <Avatar
                  size={100}
                  radius={100}
                  color="white"
                  className="shadow-xl border-4 border-white/30 text-3xl font-bold"
                >
                  {employee.name[0]}
                </Avatar>
              </div>
              <div className="space-y-2">
                <div>
                  <Title order={2} className="text-2xl font-bold">
                    {employee.name}
                  </Title>
                  <Text className="text-lg font-medium opacity-90">
                    {employee.position}
                  </Text>
                </div>
                <div>
                  <div className="flex items-center gap-3 mb-2">
                    <div className="flex items-center text-sm">
                      <IconBuildingSkyscraper
                        size={16}
                        className="mr-1 opacity-70"
                      />
                      <Text size="sm" className="opacity-90">
                        {employee.department_name}
                      </Text>
                    </div>
                    <div className="flex items-center text-sm">
                      <IconId size={16} className="mr-1 opacity-70" />
                      <Text size="sm" className="opacity-90">
                        {employee.employeeStructure.name}
                      </Text>
                    </div>
                  </div>
                  <div className="flex items-center gap-3">
                    <div className="flex items-center text-sm">
                      <IconId size={16} className="mr-1 opacity-70" />
                      <HorizontalDetailRow
                        value={employee.employee_number}
                        onExtraClick={() => {
                          setIsTimelineOpen(true);
                          setModalTitle("Employee Number History");
                          setModalContent(<Timeline data={timelineData} />);
                        }}
                        className="opacity-90 text-white"
                        color="white"
                      />
                    </div>
                    <div className="flex items-center text-sm">
                      <IconIdBadge size={16} className="mr-1 opacity-70" />
                      <Text size="sm" className="opacity-90">
                        {employee.id_staff} <b>(ID Staff)</b>
                      </Text>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {/* Right Section: Employment */}
            <div className="flex flex-col justify-center items-end mt-5">
              <div className="flex items-center bg-black/20 px-4 py-2 rounded-lg">
                <div className="text-right">
                  <Text size="xs" className="opacity-80">
                    Employment
                  </Text>
                  <Text size="lg" className="font-bold">
                    {employee.service_year}
                  </Text>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Custom Tab Navigation */}
      <div className="bg-gray-50">
        <div className="flex overflow-x-auto scrollbar-hide">
          {["personal", "professional", "structure", "others"].map((tab) => (
            <button
              key={tab}
              onClick={() => setActiveTab(tab)}
              className={`px-6 py-4 flex items-center space-x-2 font-medium text-sm transition-colors ${
                activeTab === tab
                  ? "border-b-2 border-blue-600 text-blue-600"
                  : "text-gray-600 hover:text-gray-900 hover:bg-gray-100"
              }`}
            >
              <span className="flex items-center justify-center w-8 h-8">
                {tabIcons[tab as keyof typeof tabIcons]}
              </span>
              <span className="capitalize">{tab}</span>
            </button>
          ))}
        </div>
      </div>

      {/* Tab Content with Scroll Area */}
      <div className="flex-1 overflow-hidden">
        {activeTab === "personal" && (
          <ScrollArea className="h-full">
            <div className="p-6">
              <div className="flex items-center justify-between mb-6">
                <Group>
                  <Title order={3} className="text-lg font-bold text-gray-700">
                    Personal Information
                  </Title>
                  <ActionIcon
                    variant="subtle"
                    color="gray"
                    size="lg"
                    onClick={() => {
                      setIsTimelineOpen(true);
                      setModalTitle("Edit Personal Information");
                      setModalContent(
                        <PersonalInformation
                          form={form}
                          setIsTimelineOpen={setIsTimelineOpen}
                        />
                      );
                    }}
                  >
                    <IconEdit size={20} />
                  </ActionIcon>
                </Group>
                <Badge color="blue" variant="outline">
                  Basic Details
                </Badge>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {/* Left Column */}
                <div className="bg-white p-3 rounded-xl shadow-sm border border-gray-100">
                  <div className="space-y-4">
                    <HorizontalDetailRow
                      label="Full Name"
                      value={employee.name}
                    />
                    <HorizontalDetailRow
                      label="Employee ID"
                      value={employee.employee_number}
                      onExtraClick={() => {
                        setIsTimelineOpen(true);
                        setModalTitle("Employee Number History");
                        setModalContent(<Timeline data={timelineData} />);
                      }}
                    />
                    <HorizontalDetailRow
                      label="Date of Birth"
                      value={employee.date_of_birth}
                    />
                    <HorizontalDetailRow
                      label="Gender"
                      value={employee.gender}
                    />
                    <HorizontalDetailRow
                      label="NIK"
                      value={employee.identity_card}
                    />
                  </div>
                </div>

                {/* Right Column */}
                <div className="bg-white p-3 rounded-xl shadow-sm border border-gray-100">
                  <div className="space-y-4">
                    <HorizontalDetailRow
                      label="Marital Status"
                      value={employee.marital_status}
                    />
                    <HorizontalDetailRow label="Phone" value={employee.phone} />
                    <HorizontalDetailRow
                      label="ID Staff"
                      value={employee.id_staff}
                    />
                    <HorizontalDetailRow
                      label="Religion"
                      value={employee.religion}
                    />
                    <HorizontalDetailRow
                      label="Address"
                      value={employee.address}
                    />
                  </div>
                </div>
              </div>

              {/* Address Section */}
              {/* <div className="mt-8 bg-white p-3 rounded-xl shadow-sm border border-gray-100">
                <Group gap="apart" className="mb-4">
                  <div className="flex items-center">
                    <div className="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600 mr-3">
                      <IconMapPin size={20} />
                    </div>
                    <Title order={4} className="font-semibold">
                      Address Information
                    </Title>
                  </div>
                  <Badge color="gray">Current</Badge>
                </Group>
                <Text className="text-gray-700 text-lg">
                  {employee.address}
                </Text>
              </div> */}
            </div>
          </ScrollArea>
        )}

        {activeTab === "professional" && (
          <ScrollArea className="h-full">
            <div className="p-6">
              <div className="flex items-center justify-between mb-10">
                <Title order={3} className="text-lg font-bold text-gray-700">
                  Professional Information
                </Title>
                <Badge color="indigo" variant="outline">
                  Work Details
                </Badge>
              </div>

              <div className="grid grid-cols-1 mb-5">
                {/* Employment Details */}
                <div className="bg-white p-3 rounded-xl shadow-sm border border-gray-100">
                  <div className="flex items-center justify-between mb-6">
                    <div className="flex items-center">
                      <Title
                        order={4}
                        className="text-lg font-bold text-gray-700 m-0"
                      >
                        Employment Details
                      </Title>
                      <ActionIcon
                        variant="subtle"
                        color="gray"
                        size="lg"
                        onClick={() => {
                          setIsTimelineOpen(true);
                          setModalTitle("Edit Employment Details");
                          setModalContent(
                            <EmployeementDetail
                              form={form}
                              setIsTimelineOpen={setIsTimelineOpen}
                              dataCompany={dataCompany}
                            />
                          );
                        }}
                      >
                        <IconEdit size={20} />
                      </ActionIcon>
                      <ActionIcon
                        variant="subtle"
                        color="gray"
                        size="lg"
                        className="-ml-2"
                      >
                        <IconSquarePlus size={20} />
                      </ActionIcon>
                    </div>
                  </div>
                  <div className="space-y-1">
                    <HorizontalDetailRow
                      label="Company"
                      value={employee.company_name}
                    />
                    <HorizontalDetailRow
                      label="Join Date"
                      value={employee.join_date}
                    />
                    <HorizontalDetailRow
                      label="Employee Type"
                      value={employee.employee_type}
                    />
                    <HorizontalDetailRow
                      label="Employee Status"
                      value={employee.status}
                    />
                    <HorizontalDetailRow
                      label="Work Schedule"
                      value={employee.schedule_type}
                    />
                    <HorizontalDetailRow
                      label="Location"
                      value="Jl. Raya Sukomulyo KM 24, Manyar - Gresik"
                    />
                  </div>
                </div>
              </div>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {/* Card: Position */}
                <div className="bg-white p-3 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                  <div className="flex items-center mb-4">
                    <div className="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 mr-3">
                      <IconBriefcase size={20} />
                    </div>
                    <div>
                      {/* Using roleCode as the displayed position title */}
                      <Text size="sm" c="dimmed">
                        Position
                        <Badge color="blue" className="mx-3">
                          Current
                        </Badge>
                      </Text>
                      <Text className="font-semibold">
                        {employee.employeeStructure.name}
                      </Text>
                    </div>
                  </div>
                  <Divider className="my-3" />
                  <div className="flex justify-between text-sm">
                    <Text c="dimmed">Position Code</Text>
                    <Text>{employee.roleCode}</Text>
                  </div>
                </div>

                {/* Card: Department */}
                <div className="bg-white p-3 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                  <div className="flex items-center mb-4">
                    <div className="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mr-3">
                      <IconBuildingBank size={20} />
                    </div>
                    <div>
                      <Text size="sm" c="dimmed">
                        Department
                      </Text>
                      <Text className="font-semibold">
                        {employee.department_name}
                      </Text>
                    </div>
                  </div>
                  <Divider className="my-3" />
                  <div className="flex justify-between text-sm">
                    <Text c="dimmed">Sub Section</Text>
                    <Text>{employee.section}</Text>
                  </div>
                </div>

                {/* Card: Employment Type & Status */}
                <div className="bg-white p-3 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                  <div className="flex items-center mb-4">
                    <div className="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 mr-3">
                      <IconSettings size={20} />
                    </div>
                    <div>
                      <Text size="sm" c="dimmed">
                        Employment Type
                      </Text>
                      <Text className="font-semibold">
                        {employee.employee_type}
                      </Text>
                    </div>
                  </div>
                  <Divider className="my-3" />
                  <div className="flex justify-between text-sm">
                    <Text c="dimmed">Status</Text>
                    <Badge
                      color={employee.status == "Aktif" ? "green" : "gray"}
                    >
                      {employee.status}
                    </Badge>
                  </div>
                </div>
              </div>

              {/* Employment History */}
              <div className="mt-8">
                <div className="flex items-center justify-between mb-6">
                  <div className="flex items-center">
                    <Title
                      order={4}
                      className="text-lg font-bold text-gray-700 m-0"
                    >
                      Employment History
                    </Title>
                    <ActionIcon variant="subtle" color="gray" size="lg">
                      <IconSquarePlus size={20} />
                    </ActionIcon>
                  </div>
                </div>

                <Accordion variant="separated" radius="md">
                  {employee.employeeStructures.map((item: any, i: number) => (
                    <Accordion.Item key={i} value={i.toString()}>
                      <Accordion.Control>
                        <Group>
                          <div className="flex-1 justify-between">
                            <div className="flex-row">
                              <Text> {employee.employeeStructure.name}</Text>
                              <Text size="sm" c="dimmed">
                                {item.status == 1
                                  ? "• Current"
                                  : item.assign_date + "-" + item.reassign_date}
                              </Text>
                            </div>
                          </div>
                          <ActionIcon variant="subtle" color="gray" size="lg">
                            <IconEdit size={20} />
                          </ActionIcon>
                          {item.status == 1 ? (
                            <Badge color="blue">Current</Badge>
                          ) : (
                            <Badge color="gray">Previous</Badge>
                          )}
                        </Group>
                      </Accordion.Control>
                      <Accordion.Panel>
                        <div className="space-y-4">
                          <Group gap="apart">
                            <Text size="sm" c="dimmed">
                              Start Date
                            </Text>
                            <Text size="sm">
                              {item.assign_date ?? "No Information"}
                            </Text>
                          </Group>
                          <Group gap="apart">
                            <Text size="sm" c="dimmed">
                              Reports To
                            </Text>
                            <Text size="sm">{employee.employee_superior}</Text>
                          </Group>
                          <Group gap="apart">
                            <Text size="sm" c="dimmed">
                              Team Members
                            </Text>
                            <Text size="sm">
                              {employee.totalMemberStructure} People
                            </Text>
                          </Group>
                          <Divider my="sm" />
                          <Text size="sm">
                            Responsible for developing and maintaining software
                            applications while leading a team.
                          </Text>
                        </div>
                      </Accordion.Panel>
                    </Accordion.Item>
                  ))}
                </Accordion>
              </div>
            </div>
          </ScrollArea>
        )}

        {activeTab === "structure" && (
          <ScrollArea className="h-full">
            <div className="p-6">
              <div className="flex items-center justify-between">
                <Title
                  order={3}
                  className="text-lg font-bold text-gray-700 select-none"
                >
                  Structure Information
                </Title>
                <Badge color="indigo" variant="outline">
                  Organization Details
                </Badge>
              </div>

              <div className="grid grid-cols-1 gap-6 mt-8">
                {/* Organizational Structure */}
                <div className="bg-white p-3 rounded-xl shadow-sm border border-gray-100">
                  <div className="flex items-center justify-between mb-6">
                    <div className="flex items-center">
                      <Title
                        order={4}
                        className="text-lg font-bold text-gray-700 m-0"
                      >
                        Organizational Structure
                      </Title>
                    </div>
                  </div>
                  <div className="space-y-1">
                    <HorizontalDetailRow
                      label="Department"
                      value={employee.department_name}
                    />
                    <HorizontalDetailRow
                      label="Section"
                      value={employee.section}
                    />
                    <HorizontalDetailRow
                      label="ID Structure"
                      value={employee.id_structure}
                    />
                    <HorizontalDetailRow
                      label="Sub Position"
                      value={employee.employeeStructure.name}
                    />
                    <HorizontalDetailRow label="Group" value={employee.group} />

                    <HorizontalDetailRow
                      label="Reports To"
                      value={employee.employee_superior}
                    />
                    <HorizontalDetailRow
                      label="Total Members"
                      value={employee.totalMemberStructure + " People"}
                    />
                    <div className="flex justify-end mt-4">
                      <button
                        className="flex items-center text-blue-600 text-sm font-medium hover:text-blue-800"
                        onClick={() => {
                          setGlobalTab("organization");
                          setUUID(UUID);
                          handleSelectChange(UUID);
                        }}
                      >
                        View Organization Chart
                        <IconChevronRight size={16} className="ml-1" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </ScrollArea>
        )}

        {activeTab === "others" && (
          <ScrollArea className="h-full">
            <div className="p-6">
              <div className="flex items-center justify-between mb-6">
                <Title
                  order={3}
                  className="text-lg font-bold text-gray-700 select-none"
                >
                  Others Information
                </Title>
                <Badge color="indigo" variant="outline">
                  Organization Details
                </Badge>
              </div>

              <div className="grid grid-cols-1 gap-6">
                {/* Contract */}
                <div className="bg-white p-3 rounded-xl shadow-sm border border-gray-100">
                  <div className="flex items-center justify-between mb-6">
                    <div className="flex items-center">
                      <Title
                        order={4}
                        className="text-lg font-bold text-gray-700 m-0"
                      >
                        Contract
                      </Title>
                      <ActionIcon
                        variant="subtle"
                        color="gray"
                        size="lg"
                        onClick={() => {
                          setIsTimelineOpen(true);
                          setModalTitle("Edit Contract");
                          setModalContent(
                            <Contract
                              form={form}
                              setIsTimelineOpen={setIsTimelineOpen}
                            />
                          );
                        }}
                      >
                        <IconEdit size={20} />
                      </ActionIcon>
                      <ActionIcon
                        variant="subtle"
                        color="gray"
                        size="lg"
                        className="-ml-2"
                      >
                        <IconSquarePlus size={20} />
                      </ActionIcon>
                    </div>
                  </div>
                  <div className="space-y-1">
                    <HorizontalDetailRow label="Status Contract" value="-" />
                    <HorizontalDetailRow label="Resign Date" value="-" />
                  </div>
                </div>
              </div>

              {/* Skills & Certifications */}
              <div className="mt-8 bg-gray-50 p-6 rounded-xl">
                <div className="flex items-center justify-between mb-4">
                  <div className="flex items-center justify-betwee">
                    <div className="flex items-center">
                      <Title
                        order={4}
                        className="text-lg font-bold text-gray-700 m-0"
                      >
                        Skills & Certifications
                      </Title>
                      <ActionIcon
                        variant="subtle"
                        color="gray"
                        size="lg"
                        onClick={() => {
                          setIsTimelineOpen(true);
                          setModalTitle("Edit Employment Details");
                          setModalContent(
                            <Skills
                              form={form}
                              setIsTimelineOpen={setIsTimelineOpen}
                            />
                          );
                        }}
                      >
                        <IconEdit size={20} />
                      </ActionIcon>
                    </div>
                  </div>
                </div>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="bg-white p-4 rounded-lg shadow-sm">
                    <Text w={600} className="mb-2">
                      IKW
                    </Text>
                    <div className="flex flex-wrap gap-2">
                      {employee.employeeIKWS.map((item: any, i: number) => (
                        <Badge key={i} color="blue" variant="outline">
                          {item.ikw_code}
                        </Badge>
                      ))}
                    </div>
                  </div>
                  <div className="bg-white p-4 rounded-lg shadow-sm">
                    <Text w={600} className="mb-2">
                      Certifications
                    </Text>
                    <div className="flex flex-wrap gap-2">
                      <Badge color="teal" variant="outline">
                        {/* AWS Certified */}
                      </Badge>
                    </div>
                  </div>
                </div>
              </div>

              {/* Benefits & Compensation */}
              <div className="mt-8 bg-gray-50 p-6 rounded-xl">
                <Title
                  order={4}
                  className="text-lg font-bold text-gray-700 mb-4"
                >
                  Benefits & Compensation
                </Title>
                <Text c="dimmed" size="sm" className="italic mb-4">
                  This information is restricted. Please contact HR for details.
                </Text>
                <Group>
                  <button className="flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm">
                    <IconMail size={16} className="mr-2" />
                    Contact HR
                  </button>
                </Group>
              </div>
            </div>
          </ScrollArea>
        )}
      </div>

      {/* Timeline Modal */}
      {isTimelineOpen && (
        <div className="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4">
          <div className="bg-white dark:bg-neutral-900 rounded-xl max-w-5xl w-full max-h-[90vh] overflow-y-auto">
            <div className="p-6 border-b border-neutral-200 dark:border-neutral-700 flex justify-between items-center">
              <h3 className="text-2xl font-bold text-neutral-800 dark:text-neutral-200">
                {modalTitle}
              </h3>
              <button
                onClick={() => {
                  setIsTimelineOpen(false);
                  form.reset();
                }}
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
    </div>
  );
};

export default EmployeeDetails;
