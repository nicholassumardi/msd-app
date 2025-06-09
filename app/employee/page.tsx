/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";

import "@mantine/core/styles.css";
import DefaultLayout from "@/components/Layouts/DefaultLayout";
import { ModalsProvider } from "@mantine/modals";
import EmployeeSearch from "./EmployeeTab/EmployeeSearch";
import {
  IconChartBar,
  IconHierarchy3,
  IconUserCircle,
  IconUsers,
} from "@tabler/icons-react";
import { Tabs, Paper, Text, Skeleton } from "@mantine/core";
import StructureSearch from "./StructureTab/StructureSearch";
import OrgChartSearch from "./OrgChartTab/OrgChartSearch";
import {
  EmployeeDataProvider,
  useEmployeeDataContext,
} from "../../context/EmployeeCentre";
import { motion, AnimatePresence } from "framer-motion";
import { useState } from "react";

export default function EmployeeDefault() {
  return (
    <>
      <head>
        <title>Dashboard Admin | Employee</title>
        <link rel="icon" href="/images/images.jpeg" />
      </head>
      <DefaultLayout>
        <div className="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-700">
          <ModalsProvider>
            <EmployeeDataProvider>
              <motion.div
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5 }}
                className="p-6"
              >
                <EmployeeTabs />
              </motion.div>
            </EmployeeDataProvider>
          </ModalsProvider>
        </div>
      </DefaultLayout>
    </>
  );
}

function EmployeeTabs() {
  const { globalTab, setGlobalTab } = useEmployeeDataContext();
  const [isLoading, setIsLoading] = useState(false);

  const handleTabChange = (value: string | null) => {
    if (value === null) return;
    setIsLoading(true);
    setGlobalTab(value);
    // Simulate loading delay
    setTimeout(() => setIsLoading(false), 300);
  };

  const tabVariants = {
    hidden: { opacity: 0, y: 20 },
    visible: { opacity: 1, y: 0 },
    exit: { opacity: 0, y: -20 },
  };

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.6, delay: 0.2 }}
      className="flex flex-col gap-6"
    >
      <Paper
        shadow="sm"
        radius="lg"
        className="bg-white/80 backdrop-blur-sm border border-gray-200/50 overflow-hidden"
      >
        <Tabs
          value={globalTab}
          onChange={handleTabChange}
          variant="pills"
          radius="md"
          className="p-1"
        >
          <Tabs.List className="flex flex-wrap bg-gray-50/50 rounded-lg p-2 gap-1">
            <Tabs.Tab
              value="overview"
              leftSection={<IconUsers size={16} />}
              className="font-satoshi text-sm font-medium hover:scale-105 transition-all duration-200 data-[active]:shadow-md"
            >
              Employee Details
            </Tabs.Tab>
            <Tabs.Tab
              value="structure"
              leftSection={<IconUserCircle size={16} />}
              className="font-satoshi text-sm font-medium hover:scale-105 transition-all duration-200 data-[active]:shadow-md"
            >
              Structure
            </Tabs.Tab>
            <Tabs.Tab
              value="organization"
              leftSection={<IconHierarchy3 size={16} />}
              className="font-satoshi text-sm font-medium hover:scale-105 transition-all duration-200 data-[active]:shadow-md"
            >
              Organization Chart
            </Tabs.Tab>
            <Tabs.Tab
              value="performance"
              leftSection={<IconChartBar size={16} />}
              className="font-satoshi text-sm font-medium hover:scale-105 transition-all duration-200 data-[active]:shadow-md"
            >
              Performance
            </Tabs.Tab>
          </Tabs.List>

          {/* Tab content container with loading states */}

          <AnimatePresence mode="wait">
            {isLoading ? (
              <motion.div
                key="loading"
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                transition={{ duration: 0.2 }}
                className="space-y-4"
              >
                <Skeleton height={40} radius="md" />
                <Skeleton height={200} radius="md" />
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <Skeleton height={120} radius="md" />
                  <Skeleton height={120} radius="md" />
                  <Skeleton height={120} radius="md" />
                </div>
              </motion.div>
            ) : (
              <>
                <Tabs.Panel value="overview">
                  <motion.div
                    key="overview"
                    variants={tabVariants}
                    initial="hidden"
                    animate="visible"
                    exit="exit"
                    transition={{ duration: 0.4 }}
                    className="min-h-[70vh]"
                  >
                    <Paper
                      shadow="xs"
                      p="md"
                      radius="md"
                      className="h-full bg-gradient-to-br from-white to-gray-50/50"
                    >
                      <EmployeeSearch />
                    </Paper>
                  </motion.div>
                </Tabs.Panel>

                <Tabs.Panel value="structure">
                  <motion.div
                    key="structure"
                    variants={tabVariants}
                    initial="hidden"
                    animate="visible"
                    exit="exit"
                    transition={{ duration: 0.4 }}
                    className="min-h-[70vh]"
                  >
                    <Paper
                      shadow="xs"
                      p="md"
                      radius="md"
                      className="h-full bg-gradient-to-br from-white to-gray-50/50"
                    >
                      <StructureSearch />
                    </Paper>
                  </motion.div>
                </Tabs.Panel>

                <Tabs.Panel value="organization">
                  <motion.div
                    key="organization"
                    variants={tabVariants}
                    initial="hidden"
                    animate="visible"
                    exit="exit"
                    transition={{ duration: 0.4 }}
                    className="min-h-[70vh]"
                  >
                    <div className="h-[calc(180vh)] z-9999">
                      <OrgChartSearch />
                    </div>
                  </motion.div>
                </Tabs.Panel>

                <Tabs.Panel value="performance">
                  <motion.div
                    key="performance"
                    variants={tabVariants}
                    initial="hidden"
                    animate="visible"
                    exit="exit"
                    transition={{ duration: 0.4 }}
                    className="min-h-[70vh]"
                  >
                    <Paper
                      shadow="xs"
                      p="md"
                      radius="md"
                      className="h-full bg-gradient-to-br from-white to-blue-50/30"
                    >
                      <div className="flex items-center justify-center h-full">
                        <div className="text-center space-y-4">
                          <IconChartBar
                            size={64}
                            className="mx-auto text-gray-400"
                          />
                          <Text size="lg" className="text-gray-600">
                            Performance Dashboard
                          </Text>
                          <Text size="sm" className="text-gray-500">
                            Coming soon - Advanced performance analytics and
                            reporting
                          </Text>
                        </div>
                      </div>
                    </Paper>
                  </motion.div>
                </Tabs.Panel>
              </>
            )}
          </AnimatePresence>
        </Tabs>
      </Paper>
    </motion.div>
  );
}
