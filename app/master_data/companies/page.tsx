/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";
import "@mantine/core/styles.css";
import { AppleCards } from "@/components/Aceternity/apple-carousel";
import Breadcrumb from "@/components/Breadcrumbs/Breadcrumb";
import DefaultLayout from "@/components/Layouts/DefaultLayout";
import { ModalsProvider } from "@mantine/modals";
import axios from "axios";
import { useEffect, useState } from "react";
import { Container } from "@mantine/core";
import { Company } from "../../../pages/api/admin/master_data/company";

export default function Companies() {
  const [data, setData] = useState<Company[]>([]);
  const getData = async () => {
    try {
      const response = await axios.get("/api/admin/master_data/company");
      setData(response.data.data.data);
    } catch (err: any) {}
  };

  useEffect(() => {
    getData();
  }, []);
  return (
    <>
      <DefaultLayout>
        <Breadcrumb pageName="Company" />
        <div className="z-9999 h-dvh">
          <ModalsProvider>
            <Container size="xl" px="md">
              <AppleCards dataCompany={data} />
            </Container>
          </ModalsProvider>
        </div>
      </DefaultLayout>
    </>
  );
}
