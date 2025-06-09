"use client";

import "@mantine/core/styles.css";
import React, { useState } from "react";
import {
  Text,
  Container,
  TextInput,
  Paper,
  Title,
  ActionIcon,
  Skeleton,
  Button,
  Group,
  Tooltip,
  Menu,
  Select,
} from "@mantine/core";
import { motion, AnimatePresence } from "framer-motion";
import {
  IconFileText,
  IconSettings,
  IconSearch,
  IconFilter,
  IconDatabase,
  IconPlus,
  IconUpload,
  IconDownload,
  IconRefresh,
  IconChevronDown,
  IconFileImport,
  IconHistory,
  IconShare,
  IconPrinter,
  IconMail,
} from "@tabler/icons-react";
import DefaultLayout from "@/components/Layouts/DefaultLayout";
import { ModalsProvider } from "@mantine/modals";
import { IKWDataProvider } from "../../context/IKWCentre";
import { IKWDetails } from "./IKWDetails";
import ImportDropzone from "@/components/common/Dropzone";
import useIKWData from "../../hooks/IKWCentre";

const IKWDefault = () => {
  return (
    <>
      <head>
        <title>Dashboard Admin | Employee</title>
        <link rel="icon" href="/images/images.jpeg" />
      </head>
      <DefaultLayout>
        <ModalsProvider>
          <IKWDataProvider>
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.5 }}
              className="p-6"
            >
              <IKWTabs />
            </motion.div>
          </IKWDataProvider>
        </ModalsProvider>
      </DefaultLayout>
    </>
  );
};

const IKWTabs = () => {
  const [activeTab, setActiveTab] = useState("documents");
  const [searchQuery, setSearchQuery] = useState("");
  const [isLoading, setIsLoading] = useState(false);
  const { dataIKW } = useIKWData();

  const handleTabChange = (value: string | null) => {
    if (value === null) return;
    setIsLoading(true);
    setActiveTab(value);
    // Simulate loading delay
    setTimeout(() => setIsLoading(false), 300);
  };

  const handleRefresh = () => {
    setIsLoading(true);
    setTimeout(() => setIsLoading(false), 1000);
  };

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.6, delay: 0.2 }}
      className="flex flex-col gap-6"
    >
      <div className="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
        <Container size="xl" className="py-8">
          {/* Top Action Bar */}
          <motion.div
            initial={{ opacity: 0, y: -20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5 }}
            className="mb-8"
          >
            <Paper className="p-4 bg-white/90 backdrop-blur-md border border-white/20 shadow-lg rounded-2xl">
              <div className="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                {/* Primary Actions */}
                <Group gap="sm">
                  <Button
                    leftSection={<IconPlus size={18} />}
                    variant="gradient"
                    gradient={{ from: "blue", to: "cyan" }}
                    size="md"
                    radius="xl"
                    className="shadow-md hover:shadow-lg transition-all duration-300"
                  >
                    New Document
                  </Button>

                  <Menu
                    shadow="lg"
                    width={200}
                    radius="md"
                    closeOnItemClick={false}
                  >
                    <Menu.Target>
                      <Button
                        leftSection={<IconUpload size={18} />}
                        variant="light"
                        color="blue"
                        size="md"
                        radius="xl"
                        rightSection={<IconChevronDown size={16} />}
                        className="shadow-sm hover:shadow-md transition-all duration-300"
                      >
                        Import
                      </Button>
                    </Menu.Target>
                    <Menu.Dropdown>
                      <ImportDropzone
                        url="/api/admin/import/ikw"
                        mode="ikwImport"
                      />
                      <Menu.Divider />
                      <Menu.Item leftSection={<IconHistory size={16} />}>
                        Import History
                      </Menu.Item>
                    </Menu.Dropdown>
                  </Menu>

                  <Tooltip label="Refresh data" position="bottom">
                    <ActionIcon
                      size="lg"
                      variant="subtle"
                      color="gray"
                      radius="xl"
                      onClick={handleRefresh}
                      className="hover:bg-gray-100 transition-colors duration-200"
                    >
                      <IconRefresh size={18} />
                    </ActionIcon>
                  </Tooltip>
                </Group>

                {/* Secondary Actions */}
                <Group gap="sm">
                  <Menu shadow="lg" width={180} radius="md">
                    <Menu.Target>
                      <Button
                        variant="subtle"
                        color="gray"
                        size="md"
                        radius="xl"
                        leftSection={<IconDownload size={18} />}
                        rightSection={<IconChevronDown size={16} />}
                        className="hover:bg-gray-100 transition-colors duration-200"
                      >
                        Export
                      </Button>
                    </Menu.Target>
                    <Menu.Dropdown>
                      <Menu.Item leftSection={<IconDownload size={16} />}>
                        Download PDF
                      </Menu.Item>
                      <Menu.Item leftSection={<IconMail size={16} />}>
                        Email Report
                      </Menu.Item>
                      <Menu.Item leftSection={<IconPrinter size={16} />}>
                        Print
                      </Menu.Item>
                      <Menu.Divider />
                      <Menu.Item leftSection={<IconShare size={16} />}>
                        Share Link
                      </Menu.Item>
                    </Menu.Dropdown>
                  </Menu>
                </Group>
              </div>
            </Paper>
          </motion.div>

          {/* Header Section */}
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.3 }}
            className="text-center mb-8"
          >
            <Text
              size="2.5rem"
              className="font-light text-gray-900 mb-3 tracking-tight"
            >
              Document Overview IKW <br />
              <span className="text-xl text-gray-600 font-normal">
                Document Details, Structure Analysis, Risk Assessment
              </span>
            </Text>
            <div className="w-20 h-0.5 bg-gradient-to-r from-blue-500 to-purple-500 mx-auto mb-6"></div>

            {/* Enhanced Search Input */}
            <div className="max-w-2xl mx-auto mb-8">
              <div className="flex gap-3">
                <Select
                  placeholder="Search documents, analyze content, or find specific information..."
                  leftSection={
                    <IconSearch size={16} className="text-gray-400" />
                  }
                  value={searchQuery}
                  clearable
                  data={dataIKW}
                  size="md"
                  radius="xl"
                  className="flex-1 shadow-lg"
                  styles={{
                    input: {
                      backgroundColor: "rgba(255, 255, 255, 0.9)",
                      backdropFilter: "blur(12px)",
                      border: "1px solid rgba(255, 255, 255, 0.2)",
                      "&:focus": {
                        borderColor: "#3b82f6",
                        backgroundColor: "rgba(255, 255, 255, 0.95)",
                        boxShadow: "0 0 0 3px rgba(59, 130, 246, 0.1)",
                      },
                    },
                  }}
                />
                <Tooltip label="Advanced filters" position="bottom">
                  <ActionIcon
                    size="lg"
                    variant="gradient"
                    gradient={{ from: "blue", to: "purple" }}
                    className="shadow-lg hover:shadow-xl transition-all duration-300"
                    radius="xl"
                  >
                    <IconFilter size={18} />
                  </ActionIcon>
                </Tooltip>
              </div>
            </div>
          </motion.div>

          {/* Floating Tab Navigation */}
          <motion.div
            initial={{ opacity: 0, scale: 0.9 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.5, delay: 0.4 }}
            className="flex justify-center mb-8"
          >
            <div className="flex bg-white/80 backdrop-blur-md rounded-2xl p-2 shadow-lg border border-white/20">
              {[
                {
                  id: "documents",
                  label: "Document Analysis",
                  icon: IconFileText,
                },
                {
                  id: "structures",
                  label: "Structure Mapping",
                  icon: IconDatabase,
                },
                {
                  id: "others",
                  label: "Additional Info",
                  icon: IconSettings,
                },
              ].map(({ id, label, icon: Icon }) => (
                <button
                  key={id}
                  onClick={() => handleTabChange(id)}
                  className={`flex items-center gap-3 px-6 py-3 rounded-xl font-medium transition-all duration-300 relative ${
                    activeTab === id
                      ? "bg-gradient-to-r from-blue-500 to-purple-500 text-white shadow-lg transform scale-105"
                      : "text-gray-600 hover:text-gray-900 hover:bg-gray-50"
                  }`}
                >
                  <Icon size={18} />
                  <span>{label}</span>
                </button>
              ))}
            </div>
          </motion.div>

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
              <motion.div
                key="content"
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -20 }}
                transition={{ duration: 0.3 }}
                className="max-w-7xl mx-auto"
              >
                {activeTab === "documents" && <IKWDetails />}

                {activeTab === "structures" && (
                  <Paper className="p-8 bg-white/95 backdrop-blur-sm border border-white/20 shadow-xl rounded-2xl">
                    <div className="text-center">
                      <IconDatabase
                        size={64}
                        className="text-gray-400 mx-auto mb-4"
                      />
                      <Title order={2} className="text-gray-700 mb-4">
                        Structure Mapping & Analysis
                      </Title>
                      <Text className="text-gray-500 max-w-2xl mx-auto">
                        This section will display document structure analysis,
                        content mapping, hierarchical organization, and
                        cross-reference relationships between different document
                        sections.
                      </Text>
                    </div>
                  </Paper>
                )}

                {activeTab === "others" && (
                  <Paper className="p-8 bg-white/95 backdrop-blur-sm border border-white/20 shadow-xl rounded-2xl">
                    <div className="text-center">
                      <IconSettings
                        size={64}
                        className="text-gray-400 mx-auto mb-4"
                      />
                      <Title order={2} className="text-gray-700 mb-4">
                        Additional Information & Tools
                      </Title>
                      <Text className="text-gray-500 max-w-2xl mx-auto">
                        Extended document metadata, version history,
                        collaboration tools, compliance tracking, and advanced
                        analytics will be available in this section.
                      </Text>
                    </div>
                  </Paper>
                )}
              </motion.div>
            )}
          </AnimatePresence>
        </Container>
      </div>
    </motion.div>
  );
};

export default IKWDefault;
