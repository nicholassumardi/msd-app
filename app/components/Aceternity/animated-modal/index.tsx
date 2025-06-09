"use client";
import React, { useState } from "react";
import Image from "next/image";
import { motion } from "framer-motion";
import {
  Modal,
  ModalBody,
  ModalContent,
  ModalFooter,
  ModalTrigger,
  useModal,
} from "@/components/ui/animated-modal";
import { ThreeDCardDemo } from "../3d-card";
import { Employee } from "../../../../pages/types/employee";
import { ScrollArea } from "@mantine/core";
import { Timeline } from "@/components/ui/timelineEmployeeNumber";

export function AnimatedModal({
  title,
  detail,
  data,
}: {
  title: string;
  detail: string;
  data: Employee | null;
}) {
  return (
    <div className="py-5 flex items-center justify-center">
      <Modal>
        <AnimatedModalBody {...{ title, detail, data }} />
      </Modal>
    </div>
  );
}

export function AnimatedModalBody({
  title,
  detail,
  data,
}: {
  title: string;
  detail: string;
  data: Employee | null;
}) {
  const [isTimelineOpen, setIsTimelineOpen] = useState(false);
  const images = [
    "/images/images.jpeg",
    "/images/images.jpeg",
    "/images/images.jpeg",
  ];
  const { setOpen } = useModal();

  const timelineData =
    data?.employee_numbers?.map((item, index) => ({
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

  const DetailRow = ({
    label,
    value,
    onExtraClick,
  }: {
    label: string;
    value: string | undefined;
    onExtraClick?: () => void;
  }) => (
    <div className="flex items-center justify-between space-x-3 p-2 bg-gray-50 dark:bg-neutral-800 rounded-lg">
      <div>
        <span className="text-sm font-medium text-neutral-600 dark:text-neutral-300 block">
          {label}
        </span>
        <p className="text-neutral-800 dark:text-neutral-100 font-semibold">
          {value || "N/A"}
        </p>
      </div>
      {onExtraClick && (
        <button
          onClick={onExtraClick}
          className="text-blue-500 hover:bg-blue-100 dark:hover:bg-blue-900 p-2 rounded-full transition-colors"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            className="h-5 w-5"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2}
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
        </button>
      )}
    </div>
  );

  return (
    <>
      <ModalTrigger>
        <ThreeDCardDemo {...{ title, detail }} />
      </ModalTrigger>
      <ModalBody>
        <ModalContent className="max-w-5xl w-full mx-auto">
          <ScrollArea h={650}>
            <div className="p-8">
              {/* Header Section */}
              <div className="text-center mb-10">
                <h4 className="text-3xl font-bold text-neutral-800 dark:text-neutral-100 mb-3">
                  Employee Details
                </h4>
                <span className="px-3 py-1.5 rounded-md bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-base">
                  Karyawan
                </span>
              </div>

              {/* Images Section */}
              <div className="flex justify-center items-center space-x-6 mb-10">
                {images.map((image, idx) => (
                  <motion.div
                    key={"images" + idx}
                    style={{
                      rotate: Math.random() * 20 - 10,
                    }}
                    whileHover={{
                      scale: 1.1,
                      rotate: 0,
                      zIndex: 100,
                    }}
                    whileTap={{
                      scale: 1.1,
                      rotate: 0,
                      zIndex: 100,
                    }}
                    className="rounded-xl p-1.5 bg-white dark:bg-neutral-800 border border-neutral-100 dark:border-neutral-700"
                  >
                    <Image
                      src={image}
                      alt="Employee image"
                      width="250"
                      height="250"
                      className="rounded-lg h-36 w-36 md:h-44 md:w-44 object-cover"
                    />
                  </motion.div>
                ))}
              </div>

              {/* Personal Details Section */}
              <div className="grid md:grid-cols-2 gap-8">
                <div className="space-y-5">
                  <DetailRow label="Name" value={data?.name} />
                  <DetailRow
                    label="Employee Number"
                    value={data?.employee_number.toString()}
                    onExtraClick={() => setIsTimelineOpen(true)}
                  />
                  <DetailRow label="Company" value={data?.company_name} />
                  <DetailRow
                    label="Date of Birth"
                    value={data?.date_of_birth}
                  />
                </div>
                <div className="space-y-5">
                  <DetailRow label="Address" value={data?.address} />
                  <DetailRow label="Phone" value={data?.phone.toString()} />
                  <DetailRow label="Status" value={data?.status} />
                  <DetailRow
                    label="Employee Type"
                    value={data?.employee_type}
                  />
                </div>
              </div>

              {/* Additional Details */}
              <div className="mt-10 grid md:grid-cols-3 gap-5">
                <DetailRow label="Department" value={data?.department_name} />
                <DetailRow label="Section" value={data?.section} />
                <DetailRow label="Position Code" value={data?.position_code} />
                <DetailRow label="Role Code" value={data?.roleCode} />
                <DetailRow label="Gender" value={data?.gender} />
                <DetailRow label="Religion" value={data?.religion} />
                <DetailRow label="Education" value={data?.education} />
                <DetailRow
                  label="Marital Status"
                  value={data?.marital_status}
                />
                <DetailRow label="Identity Card" value={data?.identity_card} />
              </div>

              {/* Timeline Modal */}
              {isTimelineOpen && (
                <div className="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4">
                  <div className="bg-white dark:bg-neutral-900 rounded-xl max-w-5xl w-full max-h-[90vh] overflow-y-auto">
                    <div className="p-6 border-b border-neutral-200 dark:border-neutral-700 flex justify-between items-center">
                      <h3 className="text-2xl font-bold text-neutral-800 dark:text-neutral-200">
                        Employee Number History
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
                    <Timeline data={timelineData} />
                  </div>
                </div>
              )}
            </div>
          </ScrollArea>
        </ModalContent>
        <ModalFooter className="flex justify-end space-x-4 p-4">
          <button
            className="px-5 py-2.5 bg-gray-200 text-black dark:bg-neutral-800 dark:text-white border border-gray-300 dark:border-neutral-700 rounded-md hover:bg-gray-300 dark:hover:bg-neutral-700 transition-colors"
            onClick={() => setOpen(false)}
          >
            Close
          </button>
        </ModalFooter>
      </ModalBody>
    </>
  );
}
