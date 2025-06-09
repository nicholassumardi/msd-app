"use client";

import { ModalsProvider } from "@mantine/modals";
import { useEffect, useState } from "react";
import { Hierarchy } from "../../../pages/api/admin/structure";
import axios from "axios";
import ErrorNotification from "@/components/Notifications/ErrorNotification";
import dynamic from "next/dynamic";
import { Button, Select, Text } from "@mantine/core";
import { IconDownload, IconSearch, IconHierarchy } from "@tabler/icons-react";
import { option } from "../../../pages/types/option";
import ButtonFilter from "@/components/common/ButtonFilter";

const CustomHierarchyChart = dynamic(
  () => import("../../components/Chart/HierarchyNew"),
  { ssr: false }
);

export default function Structure() {
  const [data, setData] = useState<Hierarchy[]>([]);
  const [dataCompany, setDataCompany] = useState<option[]>([]);
  const [dataDepartment, setDataDepartment] = useState<option[]>([]);
  const [idCompany, setIdCompany] = useState<string | null>("2");
  const [idDepartment, setIdDepartment] = useState<string | null>("1");

  const getDataCompany = async () => {
    try {
      const response = await axios.get("/api/admin/master_data/company");
      const data = response.data.data.data.map((item: any) => ({
        value: item.id.toString(),
        label: item.name,
      }));
      setDataCompany(data);
    } catch (err: any) {
      ErrorNotification({
        title: "Server Error",
        message: err.response.data.error,
      });
    }
  };

  const getDataDepartment = async () => {
    try {
      const id = idCompany ? idCompany : "1";
      const response = await axios.get(
        `/api/admin/master_data/department/${id}?type=showByCompany`
      );
      const data = response.data.data.map((item: any) => ({
        value: item.id.toString(),
        label: item.code,
      }));
      setDataDepartment(data);
    } catch (err: any) {
      ErrorNotification({
        title: "Server Error",
        message: err.response.data.error,
      });
    }
  };

  const getData = async (id: string | null) => {
    try {
      const response = await axios.get(
        `/api/admin/structure/${id}?type=showMappingHierarchy`
      );

      setData(response.data.data.data);
    } catch (err: any) {
      ErrorNotification({
        title: "Server Error",
        message: err.response.data.error,
      });
    }
  };

  useEffect(() => {
    getDataCompany();
  }, []);

  useEffect(() => {
    getDataDepartment();
  }, [idCompany]);

  useEffect(() => {
    if (idDepartment) {
      getData(idDepartment);
    }
  }, [idDepartment]);

  return (
    <div className="min-h-screen bg-gray-50 p-6 flex flex-col">
      <div className="container mx-auto flex-1 flex flex-col">
        <header className="mb-6">
          <h1 className="text-3xl font-bold text-center flex items-center justify-center gap-3">
            <IconHierarchy size={36} stroke={1.5} className="text-blue-600" />
            Organization Structure
          </h1>
        </header>

        <ModalsProvider>
          <div className="flex flex-col flex-1">
            {/* Control Bar */}
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
              <Select
                withAsterisk
                fw={100}
                label="Company"
                size="md"
                color="gray"
                radius={12}
                searchable
                value={idCompany ? idCompany : "2"}
                onChange={(idCompany) => setIdCompany(idCompany)}
                clearable
                leftSection={<IconSearch />}
                className="shadow-default"
                data={dataCompany}
              />
              <Select
                withAsterisk
                fw={100}
                label="Department"
                size="md"
                color="gray"
                radius={12}
                searchable
                value={idDepartment ? idDepartment : "1"}
                onChange={(idDepartment) => setIdDepartment(idDepartment)}
                clearable
                leftSection={<IconSearch />}
                className="shadow-default"
                data={dataDepartment}
              />
              <ButtonFilter id={idDepartment} setData={setData} />
              <Button
                className="shadow-md mt-auto"
                size="sm"
                variant="outline"
                color="gray"
                radius={9}
                leftSection={<IconDownload />}
                opacity={0.6}
                c="dimmed"
              >
                Export
              </Button>
            </div>

            {/* Chart Container */}
            <div className="bg-white rounded-lg shadow-lg flex-1 flex flex-col">
              {data.length > 0 ? (
                <div className="flex-1 relative min-h-[500px] p-4">
                  {data.map((hierarchyData, index) => (
                    <CustomHierarchyChart key={index} data={hierarchyData} />
                  ))}
                </div>
              ) : (
                <div className="flex-1 flex flex-col items-center justify-center p-8 text-center">
                  <div className="mb-6 text-gray-400">
                    <IconHierarchy size={96} stroke={1} />
                  </div>
                  <Text size="xl" fw={600} className="mb-2 text-gray-800">
                    No Organizational Data
                  </Text>
                  <Text c="dimmed" className="max-w-md mx-auto">
                    Please select a company and department to view the
                    organizational structure
                  </Text>
                </div>
              )}
            </div>
          </div>
        </ModalsProvider>
      </div>
    </div>
  );
}
