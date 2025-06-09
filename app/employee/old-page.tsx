/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";

import "@mantine/core/styles.css";
import DefaultLayout from "@/components/Layouts/DefaultLayout";
import { ModalsProvider } from "@mantine/modals";
import { useEffect, useState } from "react";
import type { Employee } from "../../pages/types/employee";
import axios from "axios";
import LoadingState from "@/components/common/LoadingState";
import { AnimatedModal } from "@/components/Aceternity/animated-modal";
import { useInView } from "react-intersection-observer";
import BreadcrumbDetail from "@/components/Breadcrumbs/custom/BreadcrumbDetail";
import Image from "next/image";
import { Text } from "@mantine/core";

export default function Employee() {
  const [dataEmployee, setDataEmployee] = useState<Employee[]>([]);
  const [globalFilter, setGlobalFilter] = useState("");
  const [page, setPage] = useState(0);
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const [scrollTrigger, isInView] = useInView();
  const [debouncedIsInView, setDebouncedIsInView] = useState(isInView);
  const [debouncedGlobalFilter, setDebouncedGlobalFilter] = useState("");
  const [PAGE_SIZE, setPAGE_SIZE] = useState(6);

  useEffect(() => {
    const handler = setTimeout(() => {
      setDebouncedIsInView(isInView);
    }, 300);

    return () => clearTimeout(handler);
  }, [isInView]);

  useEffect(() => {
    const handler = setTimeout(() => {
      setDebouncedGlobalFilter(globalFilter);
      setPAGE_SIZE(globalFilter === "" ? 6 : 1);
    }, 500);

    return () => clearTimeout(handler);
  }, [globalFilter]);

  const getData = async () => {
    setIsLoading(true);
    try {
      const response = await axios.get(
        "/api/admin/employee?type=showEmployeePagination",
        {
          params: {
            start: page * PAGE_SIZE,
            size: PAGE_SIZE,
            globalFilter: debouncedGlobalFilter ?? "",
            filters: [],
            sorting: [],
          },
        }
      );
      if (debouncedGlobalFilter && debouncedGlobalFilter != "") {
        setDataEmployee(response.data.data);
      } else {
        setDataEmployee((prevData) => [...prevData, ...response.data.data]);
        setPage((prevPage) => prevPage + 1);
      }
      console.log(PAGE_SIZE);
    } catch (err: any) {
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    if (debouncedIsInView) {
      setPAGE_SIZE(6);
      getData();
    }
  }, [debouncedIsInView]);

  useEffect(() => {
    if (debouncedGlobalFilter !== "") {
      setPage(0);
      setPAGE_SIZE(1);
    }
    setDataEmployee([]);
    getData();
  }, [debouncedGlobalFilter]);

  return (
    <>
      <head>
        <title>Dashboard Admin | Employee</title>
        <link rel="icon" href="/images/images.jpeg" />
      </head>
      <DefaultLayout>
        <BreadcrumbDetail
          pageName="Employee"
          url="/employee/details"
          globalFilter={globalFilter}
          setGlobalFilter={setGlobalFilter}
        />
        <div className="z-9999 h-dvh">
          <ModalsProvider>
            {dataEmployee.length > 0 ? (
              <div className="grid grid-cols-1 lg:grid-cols-2 gap-2">
                {dataEmployee.map((item, index) => (
                  <AnimatedModal
                    key={index}
                    title={item.name}
                    detail={item?.employee_number?.toString()}
                    data={item}
                  />
                ))}
              </div>
            ) : (
              <div className="grid justify-center text-center place-items-center font-satoshi md:p-25">
                <Image
                  src="/images/no_data_logo.png"
                  width={300}
                  height={300}
                  sizes="100vw"
                  alt=""
                  className="rounded-full"
                />

                <div className="max-w-75">
                  <Text fw={750} fz={35} className="font-bold">
                    No Data Found
                  </Text>
                  <Text
                    fw={300}
                    c="dimmed"
                    fz={20}
                    className="font-bold break-normal"
                  >
                    Please ensure that you have the relevant data for this
                    department
                  </Text>
                </div>
              </div>
            )}
            <div ref={scrollTrigger}> {isLoading && <LoadingState />}</div>
          </ModalsProvider>
        </div>
      </DefaultLayout>
    </>
  );
}
