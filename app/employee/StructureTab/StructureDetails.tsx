/* eslint-disable @typescript-eslint/no-explicit-any */
import React, { useState } from "react";
import { Avatar, Badge, Tabs, Paper, Button } from "@mantine/core";
import {
  IconId,
  IconPencil,
  IconPlus,
  IconRefreshDot,
} from "@tabler/icons-react";
import useEmployeeData from "../../../hooks/EmployeeCentre";
import { useEmployeeDataContext } from "../../../context/EmployeeCentre";
import dynamic from "next/dynamic";
import { StructureAddAssignment, StructureAddRequest } from "./StructureForm";

const StructureEdit = dynamic(() => import("../StructureTab/StructureEdit"), {
  ssr: false,
});

interface StructureDetailsProps {
  structure: any;
  dataRki: any;
}

const StructureDetails: React.FC<StructureDetailsProps> = ({
  structure,
  dataRki,
}) => {
  const [activeTab, setActiveTab] = useState<string>("organization");
  const { openModal, handleGetStructureDetail } = useEmployeeDataContext();

  return (
    <>
      <StructureEdit />
      <div className="h-full flex flex-col font-satoshi">
        {/* Header Card */}
        <div className="bg-gradient-to-br from-indigo-500 to-violet-400 rounded-xl overflow-hidden shadow-lg mb-6">
          <div className="flex flex-col md:flex-row">
            {/* Profile Column */}
            <div className="p-6 md:w-2/3">
              <div className="flex items-start justify-between">
                <div className="flex items-start">
                  <Avatar
                    size={80}
                    radius="xl"
                    color="indigo"
                    className="mr-4 bg-white/20 backdrop-blur-sm"
                  >
                    {structure.name ? structure.name.charAt(0) : "?"}
                  </Avatar>
                  <div className="text-white">
                    <h1 className="text-2xl font-bold">{structure.name}</h1>
                    <p className="text-indigo-100">{structure.position}</p>
                    <div className="mt-2 flex flex-wrap gap-2">
                      <Badge
                        size="lg"
                        className="bg-white/20 backdrop-blur-sm text-white border-none"
                      >
                        {structure.department.name} ({structure.department.code}
                        )
                      </Badge>
                      <Badge
                        size="lg"
                        className="bg-white/20 backdrop-blur-sm text-white border-none"
                      >
                        Slot: {structure.totalAssignedEmployee}/
                        {structure.quota} people
                      </Badge>
                    </div>
                  </div>
                </div>
                {/* Add New Structure Button */}
                <button
                  className="mt-4 md:mt-0 bg-white/20 backdrop-blur-sm text-white font-medium py-2 px-4 rounded-lg hover:bg-white/30 transition-colors duration-200 flex items-center gap-2"
                  onClick={() => {
                    openModal("Edit");
                    handleGetStructureDetail(structure.id);
                  }}
                >
                  <IconPencil />
                  Edit Structure
                </button>
              </div>
            </div>

            {/* Stats Column */}
            <div className="bg-black/10 backdrop-blur-sm p-6 md:w-1/3">
              <div className="grid grid-cols-2 gap-4 h-full">
                <div className="text-white">
                  <p className="text-xs uppercase tracking-wider text-indigo-100">
                    Position Code
                  </p>
                  <p className="text-xl font-mono mt-1">
                    {structure.job_code.full_code}
                  </p>
                </div>
                <div className="text-white">
                  <p className="text-xs uppercase tracking-wider text-indigo-100">
                    Last Update
                  </p>
                  <p className="text-xl mt-1">
                    {new Date(structure.updated_at).toLocaleString("default", {
                      month: "long",
                      year: "numeric",
                    })}
                  </p>
                </div>
                <div className="text-white">
                  <p className="text-xs uppercase tracking-wider text-indigo-100">
                    Superior
                  </p>
                  <p className="text-xl mt-1">{structure.superior}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Tabs */}
        <Tabs
          value={activeTab}
          onChange={(value) => setActiveTab(value || "organization")}
          className="mb-6"
        >
          <Tabs.List grow>
            <Tabs.Tab value="organization" className="text-lg font-medium py-3">
              Organization Structure
            </Tabs.Tab>
            <Tabs.Tab value="rki" className="text-lg font-medium py-3">
              Required IKW
            </Tabs.Tab>
          </Tabs.List>

          <Tabs.Panel value="organization" className="p-4">
            <OrganizationTab structure={structure} />
          </Tabs.Panel>

          <Tabs.Panel value="rki" className="p-4">
            <IKWTab dataRki={dataRki} />
          </Tabs.Panel>
        </Tabs>
      </div>
    </>
  );
};

// Organization Structure Tab Component
const OrganizationTab: React.FC<{ structure: any }> = ({ structure }) => {
  const { openModalDeleteStructureAssignment } = useEmployeeData();
  const { openModal } = useEmployeeDataContext();
  // Create an array of all positions (filled and vacant)
  const allPositions = Array.from({ length: structure.quota }, (_, i) => {
    if (
      structure?.user_job_code?.length > 0 &&
      i < structure.user_job_code.length &&
      structure.user_job_code[i].status == 1
    ) {
      return {
        ...structure.user_job_code[i],
        index: i,
        isVacant: false,
      };
    }
    return {
      index: i,
      isVacant: true,
      group: ``, // Default grouping for vacant positions
    };
  });

  // Group positions by their group property
  const groupedPositions = allPositions.reduce((groups, position) => {
    const group = position.group.charAt(0) || "VACANT";
    if (!groups[group]) {
      groups[group] = [];
    }
    groups[group].push(position);
    return groups;
  }, {});

  return (
    <>
      <StructureAddRequest
        user_structure_mapping_id={structure.id.toString()}
      />
      <StructureAddAssignment
        user_structure_mapping_id={structure.id.toString()}
      />
      <Paper shadow="sm" p="md" radius="md" className="bg-white">
        <h2 className="text-2xl font-bold text-gray-800 mb-6">
          Organizational Structure
        </h2>

        {/* Detail Grid */}
        <div className="grid grid-cols-1 md:grid-cols-1 gap-6">
          <Paper shadow="xs" p="md" radius="md" className="bg-gray-50">
            <h3 className="text-lg font-semibold text-gray-700 mb-4 flex items-center">
              <span className="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-violet-400 mr-2">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  className="h-5 w-5"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                  />
                </svg>
              </span>
              Department Information
            </h3>
            <div className="space-y-4">
              <DetailRow label="Company" value={structure.company_name ?? ""} />
              <DetailRow
                label="Department"
                value={structure.department.name ?? ""}
              />
              <DetailRow
                label="Department Code"
                value={structure.department.code}
              />
            </div>
          </Paper>
        </div>
      </Paper>
      <Paper shadow="sm" p="md" radius="md" className="bg-white">
        <div className="flex justify-between items-center mb-6">
          <h2 className="text-2xl font-bold text-gray-800">People in Charge</h2>
          <Badge size="xl" color="indigo" variant="light">
            {structure.totalAssignedEmployee} Personnel
          </Badge>
        </div>

        <div className="space-y-8">
          {Object.entries(groupedPositions).map(([groupName, positions]) => (
            <div key={groupName}>
              {/* Group Header */}
              <div className="mb-4">
                <h2 className="text-xl font-bold text-gray-800 border-b-2 border-indigo-200 pb-2">
                  Group : {groupName}
                </h2>
              </div>

              {/* Original grid layout for each group */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {(positions as any[]).map((position) =>
                  !position.isVacant ? (
                    <Paper
                      key={position.index}
                      shadow="sm"
                      p="0"
                      radius="lg"
                      className="overflow-hidden border border-gray-200 relative"
                    >
                      {/* Close button added here */}
                      <button
                        className="absolute top-2 right-2 z-10 rounded-full h-8 w-8 flex items-center justify-center bg-white hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors shadow-sm"
                        onClick={() => {
                          openModalDeleteStructureAssignment(
                            position.id.toString()
                          );
                        }}
                      >
                        ×
                      </button>

                      <div className="bg-gradient-to-r from-indigo-100 to-blue-100 px-6 py-4">
                        <div className="flex items-center">
                          <Avatar
                            size={64}
                            radius="xl"
                            color="indigo"
                            className="mr-4"
                          >
                            {position.user.name.charAt(0)}
                          </Avatar>
                          <div>
                            <h3 className="text-lg font-semibold text-gray-800">
                              {position.user?.name ?? "-"}
                            </h3>
                            <p className="text-sm text-gray-600">
                              {position.position_code_structure} (
                              {position.group})
                            </p>
                            <p className="text-sm text-gray-600">
                              ID Structure:
                              {position.id_structure
                                ? position.id_structure
                                : "-"}
                            </p>
                            <p className="text-sm text-gray-600">
                              ID Staff:
                              {position.id_staff ? position.id_staff : "-"}
                            </p>
                          </div>
                        </div>
                      </div>
                      {/* Rest of the card content remains the same */}
                      <div className="px-6 py-4">
                        <div className="space-y-3">
                          <div className="flex items-center">
                            <b>(NIP) &nbsp;</b>
                            <span className="text-gray-700">
                              {position.user.user_employee_number[0]
                                ?.employee_number ?? ""}
                            </span>
                          </div>
                          <div className="flex items-center">
                            <svg
                              className="h-5 w-5 text-gray-500 mr-2"
                              fill="none"
                              strokeLinecap="round"
                              strokeLinejoin="round"
                              strokeWidth="2"
                              viewBox="0 0 24 24"
                              stroke="currentColor"
                            >
                              <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span className="text-gray-700">
                              {position.user.phone ?? "-"}
                            </span>
                          </div>
                        </div>
                      </div>
                    </Paper>
                  ) : (
                    <Paper
                      key={position.index}
                      shadow="sm"
                      p="0"
                      radius="lg"
                      className="overflow-hidden border border-gray-200 relative"
                    >
                      <div className="bg-gradient-to-r from-indigo-100 to-blue-100 px-6 py-4">
                        <div className="flex items-center">
                          <Avatar
                            size={64}
                            radius="xl"
                            color="indigo"
                            className="mr-4"
                          >
                            E
                          </Avatar>
                          <div>
                            <h3 className="text-lg font-semibold text-gray-800">
                              VACANT
                            </h3>
                            <p className="text-sm text-gray-600">Empty</p>
                            <p className="text-sm text-gray-600">
                              ID Structure: Empty
                            </p>
                            <p className="text-sm text-gray-600">
                              ID Staff: Empty
                            </p>
                            <p className="text-sm text-gray-600">
                              Status:
                              <Badge size="sm" color="indigo" variant="light">
                                Empty
                              </Badge>
                            </p>
                          </div>
                        </div>
                      </div>
                      {/* Rest of the card content remains the same */}
                      <div className="px-6 py-4">
                        <div className="space-y-3">
                          <div className="flex items-center">
                            <IconId className="h-5 w-5 text-gray-500 mr-2" />
                            <span className="text-gray-700">Empty</span>
                          </div>
                          <div className="flex items-center">
                            <svg
                              className="h-5 w-5 text-gray-500 mr-2"
                              fill="none"
                              strokeLinecap="round"
                              strokeLinejoin="round"
                              strokeWidth="2"
                              viewBox="0 0 24 24"
                              stroke="currentColor"
                            >
                              <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span className="text-gray-700">Empty</span>
                          </div>
                        </div>
                      </div>
                      <div className="px-4 py-3 bg-gray-50 border-t border-gray-100 flex justify-end">
                        {/* Conditional button rendering based on structure_type */}
                        {structure.structure_type === "Staff" ? (
                          <Button
                            size="sm"
                            variant="filled"
                            color="teal"
                            className="flex items-center hover:bg-teal-500 transition-colors"
                            leftSection={<IconRefreshDot className="h-4 w-4" />}
                            onClick={() => {
                              openModal("Request");
                            }}
                          >
                            Request
                          </Button>
                        ) : (
                          <Button
                            size="sm"
                            variant="filled"
                            color="violet"
                            className="flex items-center hover:bg-violet-500 transition-colors"
                            leftSection={<IconPlus className="h-4 w-4" />}
                            onClick={() => {
                              openModal("Assign");
                            }}
                          >
                            Add
                          </Button>
                        )}
                      </div>
                    </Paper>
                  )
                )}
              </div>
            </div>
          ))}
        </div>
      </Paper>
    </>
  );
};

// People in Charge Tab Component

// Certifications Tab Component
const IKWTab: React.FC<{ dataRki: any }> = ({ dataRki }) => {
  // const getStatusColor = (status: string) => {
  //   switch (status) {
  //     case "Required":
  //       return "red";
  //     case "In Progress":
  //       return "orange";
  //     case "Completed":
  //       return "green";
  //     case "Optional":
  //       return "blue";
  //     default:
  //       return "gray";
  //   }
  // };

  // const getPriorityColor = (priority: string) => {
  //   switch (priority) {
  //     case "High":
  //       return "red";
  //     case "Medium":
  //       return "yellow";
  //     case "Low":
  //       return "green";
  //     default:
  //       return "gray";
  //   }
  // };

  return (
    <Paper shadow="sm" p="md" radius="md" className="bg-white">
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-2xl font-bold text-gray-800">Required IKW</h2>
        <Badge size="xl" color="indigo" variant="light">
          {dataRki.length} IKW
        </Badge>
      </div>

      {/* Certification Overview */}
      {/* <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <StatCard
          title="Required"
          count={
            allCertifications.filter((c) => c.status === "Required").length
          }
          color="red"
        />
        <StatCard
          title="In Progress"
          count={
            allCertifications.filter((c) => c.status === "In Progress").length
          }
          color="orange"
        />
        <StatCard
          title="Completed"
          count={
            allCertifications.filter((c) => c.status === "Completed").length
          }
          color="green"
        />
        <StatCard
          title="Optional"
          count={
            allCertifications.filter((c) => c.status === "Optional").length
          }
          color="blue"
        />
      </div> */}

      {/* Certification List */}
      <div className="overflow-x-auto mb-6">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th
                scope="col"
                className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                NO IKW
              </th>
              <th
                scope="col"
                className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Name
              </th>
              {/* <th
                scope="col"
                className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Progress
              </th> */}
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {dataRki.map((item: any, i: number) => (
              <tr key={i} className="hover:bg-gray-50">
                <td className="px-6 py-4">
                  <div className="text-sm font-medium text-gray-900">
                    {item.no_ikw}
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <Badge color="indigo" variant="outline" size="sm">
                    {item.ikw_name}
                  </Badge>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Learning Paths */}
      {/* <Paper shadow="xs" p="md" radius="md" className="bg-gray-50">
        <h3 className="text-lg font-semibold text-gray-700 mb-4">
          Required Learning Paths
        </h3>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div className="bg-white p-4 rounded-lg shadow-sm">
            <h4 className="font-medium text-gray-800 mb-2">Technical Path</h4>
            <ul className="space-y-2 text-sm">
              <li className="flex items-center">
                <svg
                  className="w-4 h-4 text-green-500 mr-2"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth="2"
                    d="M5 13l4 4L19 7"
                  ></path>
                </svg>
                <span>Cloud Architecture Fundamentals</span>
              </li>
              <li className="flex items-center">
                <svg
                  className="w-4 h-4 text-gray-400 mr-2"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth="2"
                    d="M6 18L18 6M6 6l12 12"
                  ></path>
                </svg>
                <span>Microservices Design Patterns</span>
              </li>
              <li className="flex items-center">
                <svg
                  className="w-4 h-4 text-gray-400 mr-2"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth="2"
                    d="M6 18L18 6M6 6l12 12"
                  ></path>
                </svg>
                <span>CI/CD Implementation</span>
              </li>
            </ul>
          </div>
          <div className="bg-white p-4 rounded-lg shadow-sm">
            <h4 className="font-medium text-gray-800 mb-2">Management Path</h4>
            <ul className="space-y-2 text-sm">
              <li className="flex items-center">
                <svg
                  className="w-4 h-4 text-green-500 mr-2"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth="2"
                    d="M5 13l4 4L19 7"
                  ></path>
                </svg>
                <span>Agile Team Management</span>
              </li>
              <li className="flex items-center">
                <svg
                  className="w-4 h-4 text-green-500 mr-2"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth="2"
                    d="M5 13l4 4L19 7"
                  ></path>
                </svg>
                <span>Effective Communication</span>
              </li>
              <li className="flex items-center">
                <svg
                  className="w-4 h-4 text-gray-400 mr-2"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth="2"
                    d="M6 18L18 6M6 6l12 12"
                  ></path>
                </svg>
                <span>Project Planning & Estimation</span>
              </li>
            </ul>
          </div>
        </div>
      </Paper> */}
    </Paper>
  );
};

// Helper Components
const DetailRow: React.FC<{ label: string; value: string }> = ({
  label,
  value,
}) => (
  <div className="flex justify-between items-center">
    <span className="text-sm font-medium text-gray-500 select-none">
      {label}
    </span>
    <span className="text-sm text-gray-800">{value || "N/A"}</span>
  </div>
);

export default StructureDetails;
