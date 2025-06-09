/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";

// import { ExpandableJobCategoryCard } from "@/components/Aceternity/expandable-card";
// import Breadcrumb from "@/components/Breadcrumbs/Breadcrumb";
import DefaultLayout from "@/components/Layouts/DefaultLayout";
import { ModalsProvider } from "@mantine/modals";

import "@mantine/core/styles.css";
import BreadcrumbDetail from "@/components/Breadcrumbs/custom/BreadcrumbDetail";
import { useEffect, useState } from "react";
import axios from "axios";
import ErrorNotification from "@/components/Notifications/ErrorNotification";
import { Category } from "../../../../pages/api/admin/master_data/job_family/category";
import { motion, AnimatePresence } from "framer-motion";
import { Button, Text, Title } from "@mantine/core";
import { IconChevronDown, IconChevronUp } from "@tabler/icons-react";

const slides = [
  {
    id: 1,
    image: "/api/placeholder/600/800",
    title: "Natural Landscapes",
    description:
      "Explore breathtaking views of mountains, forests, and lakes. Our collection showcases the beauty of untouched nature from around the world.",
  },
  {
    id: 2,
    image: "/api/placeholder/600/800",
    title: "Urban Architecture",
    description:
      "Discover stunning cityscapes and iconic buildings designed by world-renowned architects. Each structure tells a story of innovation and creativity.",
  },
  {
    id: 3,
    image: "/api/placeholder/600/800",
    title: "Abstract Expressions",
    description:
      "Immerse yourself in the world of abstract art where emotions and concepts are portrayed through color, form, and texture rather than realistic imagery.",
  },
];

export default function CategoryPage() {
  const [currentIndex, setCurrentIndex] = useState(0);
  const [direction, setDirection] = useState(0);

  const goToPrevious = () => {
    setDirection(-1);
    setCurrentIndex((prevIndex) =>
      prevIndex === 0 ? slides.length - 1 : prevIndex - 1
    );
  };

  const goToNext = () => {
    setDirection(1);
    setCurrentIndex((prevIndex) =>
      prevIndex === slides.length - 1 ? 0 : prevIndex + 1
    );
  };

  const slideVariants = {
    enter: (direction: any) => ({
      y: direction > 0 ? 1000 : -1000,
      opacity: 0,
    }),
    center: {
      y: 0,
      opacity: 1,
    },
    exit: (direction: any) => ({
      y: direction < 0 ? 1000 : -1000,
      opacity: 0,
    }),
  };

  const [dataCategory, setDataCategory] = useState<Category[]>([]);

  useEffect(() => {
    const getDataCategory = async () => {
      try {
        const response = await axios.get(
          "/api/admin/master_data/job_family/category?type=showAll"
        );
        setDataCategory(response.data.data.data);
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
          pageName="Category & Position"
          url="/master_data/job_family/category/details"
        />
        <div className="z-9999 h-dvh">
          <ModalsProvider>
            <div className="w-full lg:w-3/4 h-3/4 relative overflow-hidden rounded-lg shadow-xl mx-auto">
              <AnimatePresence initial={false} custom={direction} mode="wait">
                <motion.div
                  key={currentIndex}
                  custom={direction}
                  variants={slideVariants}
                  initial="enter"
                  animate="center"
                  exit="exit"
                  transition={{
                    y: { type: "spring", stiffness: 300, damping: 30 },
                    opacity: { duration: 0.4 },
                  }}
                  className="absolute w-full h-full flex"
                >
                  {/* Left side - Image */}
                  <div className="w-1/2 h-full relative overflow-hidden">
                    <img
                      src={slides[currentIndex].image}
                      alt={slides[currentIndex].title}
                      className="w-full h-full object-cover"
                    />
                    <div className="absolute inset-0 bg-gradient-to-r from-transparent to-black/20" />
                  </div>

                  {/* Right side - Description */}
                  <div className="w-1/2 h-full flex flex-col justify-center p-6 lg:p-8 bg-white">
                    <motion.div
                      initial={{ y: 20, opacity: 0 }}
                      animate={{ y: 0, opacity: 1 }}
                      transition={{ delay: 0.2, duration: 0.5 }}
                    >
                      <Title
                        order={2}
                        className="text-2xl lg:text-3xl font-bold mb-3 text-gray-800"
                      >
                        {slides[currentIndex].title}
                      </Title>
                      <Text className="text-base lg:text-lg text-gray-600 mb-6">
                        {slides[currentIndex].description}
                      </Text>
                      <div className="flex space-x-4">
                        <Button
                          variant="outline"
                          className="border border-gray-300 hover:bg-gray-100"
                        >
                          Learn More
                        </Button>
                        <Button
                          variant="filled"
                          className="bg-blue-600 hover:bg-blue-700"
                        >
                          View Gallery
                        </Button>
                      </div>
                    </motion.div>
                  </div>
                </motion.div>
              </AnimatePresence>

              {/* Vertical Navigation buttons */}
              <div className="absolute right-4 top-1/2 transform -translate-y-1/2 flex flex-col space-y-2 z-10">
                <button
                  onClick={goToPrevious}
                  className="p-2 rounded-full bg-white/80 hover:bg-white shadow-md text-gray-800 flex items-center justify-center"
                  aria-label="Previous slide"
                >
                  <IconChevronUp size={24} />
                </button>
                <button
                  onClick={goToNext}
                  className="p-2 rounded-full bg-white/80 hover:bg-white shadow-md text-gray-800 flex items-center justify-center"
                  aria-label="Next slide"
                >
                  <IconChevronDown size={24} />
                </button>
              </div>

              {/* Slide indicators */}
              <div className="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-10">
                {slides.map((_, index) => (
                  <button
                    key={index}
                    onClick={() => {
                      setDirection(index > currentIndex ? 1 : -1);
                      setCurrentIndex(index);
                    }}
                    className={`w-2 h-2 rounded-full transition-all duration-300 ${
                      index === currentIndex ? "bg-blue-600 w-6" : "bg-gray-400"
                    }`}
                    aria-label={`Go to slide ${index + 1}`}
                  />
                ))}
              </div>
            </div>
          </ModalsProvider>
        </div>
      </DefaultLayout>
    </>
  );
}
