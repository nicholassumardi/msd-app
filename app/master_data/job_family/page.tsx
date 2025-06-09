/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";

import { ExpandableJobCategoryCard } from "@/components/Aceternity/expandable-card";
// import Breadcrumb from "@/components/Breadcrumbs/Breadcrumb";
import DefaultLayout from "@/components/Layouts/DefaultLayout";
import { ModalsProvider } from "@mantine/modals";

import "@mantine/core/styles.css";
import BreadcrumbDetail from "@/components/Breadcrumbs/custom/BreadcrumbDetail";
import { useEffect, useState } from "react";
import axios from "axios";
import ErrorNotification from "@/components/Notifications/ErrorNotification";
import { Category } from "../../../pages/api/admin/master_data/job_family/category";
import CategorySearch from "./JobFamilyDetail/CategorySearch";
import { option } from "../../../pages/types/option";

export default function CategoryPage() {
  const [dataCategory, setDataCategory] = useState<Category[]>([]);
  const [foundCategory, setFoundCategory] = useState<Category | null>(null);
  const [categoryOption, setCategoryOption] = useState<option[]>([]);
  const [id, setId] = useState("");

  const handleGetCategoryDetail = async (id: string | null) => {
    try {
      const response = await axios.get(
        `/api/admin/master_data/job_family/category/${id}?type=structureMapping`
      );

      setFoundCategory(response.data.data.data);
    } catch (err: any) {
    } finally {
    }
  };

  useEffect(() => {
    const getDataCategory = async () => {
      try {
        const response = await axios.get(
          "/api/admin/master_data/job_family/category?type=showAll"
        );
        const data = response.data.data.data.map((item: any) => ({
          value: item.id.toString(),
          label: item.name,
        }));

        setDataCategory(response.data.data.data);
        setCategoryOption(data);
      } catch (err: any) {
        ErrorNotification({
          title: "Server Error",
          message: err.response.data.error,
        });
      }
    };

    getDataCategory();
  }, []);

  return (
    <>
      <DefaultLayout>
        <BreadcrumbDetail
          pageName="Job Family"
          url="/master_data/job_family/category/details"
        />
        <div className="z-9999 h-dvh">
          <ModalsProvider>
            <CategorySearch
              dataCategory={categoryOption}
              foundCategory={foundCategory}
              setId={setId}
              id={id}
              handleGetCategoryDetail={handleGetCategoryDetail}
              setFoundCategory={setFoundCategory}
            />
            {/* <ExpandableJobCategoryCard data={dataCategory} /> */}
          </ModalsProvider>
        </div>
      </DefaultLayout>
    </>
  );
}
