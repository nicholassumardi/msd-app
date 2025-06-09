/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";

import DefaultLayout from "@/components/Layouts/DefaultLayout";
import { ModalsProvider } from "@mantine/modals";
import "@mantine/core/styles.css";
import { AnimatedTestimonialsSlider } from "@/components/Aceternity/animated-slider";
import BreadcrumbDetail from "@/components/Breadcrumbs/custom/BreadcrumbDetail";
import { useEffect, useState } from "react";
import { Classification } from "../../../pages/api/admin/master_data/classification";
import axios from "axios";

export default function Classifications() {
  const [dataAge, setDataAge] = useState<Classification[]>([]);
  const [dataGeneration, setDataGeneration] = useState<Classification[]>([]);
  const [dataWorkingDuration, setDataWorkingDuration] = useState<
    Classification[]
  >([]);
  const [testimonials, setTestimonials] = useState<
    { quote: string; name: string; designation: string; src: string }[]
  >([]);

  useEffect(() => {
    const getDataAge = async () => {
      try {
        const response = await axios.get(
          "/api/admin/master_data/classification?type=ageClassification"
        );
        setDataAge(response.data.data);
      } catch (err: any) {}
    };

    const getDataGeneration = async () => {
      try {
        const response = await axios.get(
          "/api/admin/master_data/classification?type=generalClassification"
        );
        setDataGeneration(response.data.data);
      } catch (err: any) {}
    };

    const getDataWorkingDuration = async () => {
      try {
        const response = await axios.get(
          "/api/admin/master_data/classification?type=workingDurationClassification"
        );
        setDataWorkingDuration(response.data.data);
      } catch (err: any) {}
    };

    getDataAge();
    getDataGeneration();
    getDataWorkingDuration();
  }, []);

  useEffect(() => {
    const combinedAgeQuote = dataAge
      .map((row: Classification) => `${row.label}`)
      .join(", ");

    const combinedGenerationQuote = dataGeneration
      .map((row: Classification) => `${row.label}`)
      .join(", ");

    const combinedWorkingDurationQuote = dataWorkingDuration
      .map((row: Classification) => `${row.label}`)
      .join(", ");

    setTestimonials([
      {
        quote: combinedGenerationQuote,
        name: "Generation",
        designation: "Classified by Generation",
        src: "/images/images.webp",
      },
      {
        quote: combinedAgeQuote,
        name: "Age",
        designation: "Classified by age",
        src: "/images/age-gap.webp",
      },
      {
        quote: combinedWorkingDurationQuote,
        name: "Employement period",
        designation: "Classified by employement period",
        src: "/images/working-dur.png",
      },
    ]);
  }, [dataAge, dataGeneration, dataWorkingDuration]);

  return (
    <>
      <head>
        <title>Dashboard Admin | Classification </title>
        <link rel="icon" href="/images/images.jpeg" />
      </head>
      <DefaultLayout>
        <BreadcrumbDetail
          pageName="Classifications"
          url="/master_data/classifications/details"
        />
        {testimonials.length > 0 && (
          <div className="z-9999 h-dvh">
            <ModalsProvider>
              <AnimatedTestimonialsSlider testimonials={testimonials} />
            </ModalsProvider>
          </div>
        )}
      </DefaultLayout>
    </>
  );
}
