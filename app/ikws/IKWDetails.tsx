import "@mantine/core/styles.css";
import React from "react";
import {
  Text,
  Stack,
  Grid,
  Card,
  Badge,
  Divider,
  Group,
  Paper,
  Title,
  Flex,
  Button,
} from "@mantine/core";
import {
  IconFileText,
  IconBriefcase,
  IconUsers,
  IconClipboardList,
  IconFlag,
  IconDownload,
  IconEye,
  IconShield,
  IconTrendingUp,
  IconClock,
  IconAlertTriangle,
  IconCheck,
  IconBook,
  IconSettings,
} from "@tabler/icons-react";
// Enhanced sample data structure for document details
const documentData = {
  documentInfo: {
    id: "DOC-IKW-2024-001",
    title: "TEST IKW",
    version: "REV-02",
    type: "Analysis Report",
    category: "IKW Documentation",
    status: "Active",
    createdDate: "2024-05-15",
    lastModified: "2024-05-28",
    owner: "Document Management Team",
    department: "QC",
    confidentiality: "Internal Use",
    pages: 45,
    wordCount: 12750,
    description:
      "This comprehensive document provides detailed analysis of organizational processes, risk assessments, and task documentation within the IKW framework. It includes multi-layered assessments and strategic recommendations.",
  },
  documentStructure: {
    sections: [
      { name: "Executive Summary", pages: "1-3", status: "Complete" },
      { name: "Process Documentation", pages: "4-15", status: "Complete" },
      {
        name: "Risk Assessment Matrix",
        pages: "16-28",
        status: "Under Review",
      },
      { name: "Task Analysis", pages: "29-38", status: "Complete" },
      { name: "Recommendations", pages: "39-43", status: "Draft" },
      { name: "Appendices", pages: "44-45", status: "Complete" },
    ],
    completionRate: 85,
  },
  riskAnalysis: {
    overallRisk: "Medium",
    assessmentDate: "2024-05-25",
    assessor: "Risk Management Division",
    categories: [
      {
        category: "Data Security Risk",
        level: "Low",
        description:
          "Document contains minimal sensitive information with proper access controls",
        impact: "Low",
      },
      {
        category: "Compliance Risk",
        level: "Medium",
        description: "Some sections require regulatory compliance verification",
        impact: "Medium",
      },
      {
        category: "Operational Risk",
        level: "Low",
        description:
          "Well-documented processes with clear implementation guidelines",
        impact: "Low",
      },
      {
        category: "Information Risk",
        level: "Medium",
        description:
          "Contains strategic information requiring controlled access",
        impact: "Medium",
      },
    ],
    mitigationActions: [
      "Implement periodic compliance reviews",
      "Establish controlled access protocols",
      "Regular document version control audits",
      "Stakeholder approval workflows",
    ],
  },
  analytics: {
    viewCount: 247,
    downloadCount: 89,
    collaborators: 12,
    comments: 34,
    lastAccessed: "2024-06-02",
    popularSections: [
      "Process Documentation",
      "Risk Assessment Matrix",
      "Executive Summary",
    ],
  },
};
export const IKWDetails = () => (
  <div className="space-y-6">
    {/* Main Document Container */}
    <Paper className="p-8 bg-white/95 backdrop-blur-sm border border-white/20 shadow-xl rounded-2xl">
      {/* Document Header */}
      <div className="mb-8">
        <Flex justify="space-between" align="flex-start" className="mb-4">
          <div>
            <Title order={1} className="text-3xl font-bold text-gray-900 mb-2">
              {documentData.documentInfo.title}
            </Title>
            <Group gap="md" className="mb-4">
              <Badge
                size="lg"
                variant="gradient"
                gradient={{ from: "blue", to: "purple" }}
                className="px-4 py-2"
              >
                {documentData.documentInfo.status}
              </Badge>
              <Badge size="lg" color="gray" variant="light">
                {documentData.documentInfo.id}
              </Badge>
              <Badge size="lg" color="indigo" variant="light">
                {documentData.documentInfo.version}
              </Badge>
            </Group>
          </div>
          <div className="text-right">
            <Text size="sm" c="dimmed" className="mb-1">
              Overall Risk
            </Text>
            <Badge
              size="lg"
              color={
                documentData.riskAnalysis.overallRisk === "Low"
                  ? "green"
                  : documentData.riskAnalysis.overallRisk === "Medium"
                  ? "yellow"
                  : "red"
              }
              variant="filled"
            >
              {documentData.riskAnalysis.overallRisk}
            </Badge>
          </div>
        </Flex>

        {/* Document Info Cards */}
        <Grid>
          <Grid.Col span={{ base: 12, md: 3 }}>
            <Card className="h-full bg-gradient-to-br from-blue-50 to-blue-100 border-blue-200">
              <Group gap="sm">
                <IconFileText size={20} className="text-blue-600" />
                <div>
                  <Text size="xs" c="dimmed">
                    Document Type
                  </Text>
                  <Text fw={500}>{documentData.documentInfo.type}</Text>
                </div>
              </Group>
            </Card>
          </Grid.Col>
          <Grid.Col span={{ base: 12, md: 3 }}>
            <Card className="h-full bg-gradient-to-br from-green-50 to-green-100 border-green-200">
              <Group gap="sm">
                <IconBriefcase size={20} className="text-green-600" />
                <div>
                  <Text size="xs" c="dimmed">
                    Department
                  </Text>
                  <Text fw={500}>{documentData.documentInfo.department}</Text>
                </div>
              </Group>
            </Card>
          </Grid.Col>
          <Grid.Col span={{ base: 12, md: 3 }}>
            <Card className="h-full bg-gradient-to-br from-purple-50 to-purple-100 border-purple-200">
              <Group gap="sm">
                <IconBook size={20} className="text-purple-600" />
                <div>
                  <Text size="xs" c="dimmed">
                    Pages
                  </Text>
                  <Text fw={500}>{documentData.documentInfo.pages} pages</Text>
                </div>
              </Group>
            </Card>
          </Grid.Col>
          <Grid.Col span={{ base: 12, md: 3 }}>
            <Card className="h-full bg-gradient-to-br from-orange-50 to-orange-100 border-orange-200">
              <Group gap="sm">
                <IconClock size={20} className="text-orange-600" />
                <div>
                  <Text size="xs" c="dimmed">
                    Last Modified
                  </Text>
                  <Text fw={500}>{documentData.documentInfo.lastModified}</Text>
                </div>
              </Group>
            </Card>
          </Grid.Col>
        </Grid>
      </div>

      <Divider className="my-8" />

      {/* Main Content Grid */}
      <Grid>
        {/* Left Column */}
        <Grid.Col span={{ base: 12, lg: 8 }}>
          <Stack gap="xl">
            {/* Document Overview */}
            <Card className="p-6 bg-gradient-to-br from-slate-50 to-slate-100 border border-slate-200">
              <Group gap="sm" className="mb-4">
                <IconFileText size={24} className="text-slate-700" />
                <Title order={3} className="text-slate-800">
                  Document Overview
                </Title>
              </Group>
              <Text className="text-gray-700 leading-relaxed mb-4">
                {documentData.documentInfo.description}
              </Text>
              <Grid className="mt-4">
                <Grid.Col span={6}>
                  <Text size="sm" c="dimmed">
                    Owner
                  </Text>
                  <Text fw={500}>{documentData.documentInfo.owner}</Text>
                </Grid.Col>
                <Grid.Col span={6}>
                  <Text size="sm" c="dimmed">
                    Word Count
                  </Text>
                  <Text fw={500}>
                    {documentData.documentInfo.wordCount.toLocaleString()} words
                  </Text>
                </Grid.Col>
              </Grid>
            </Card>

            {/* Document Structure */}
            <Card className="p-6 bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200">
              <Group gap="sm" className="mb-4">
                <IconClipboardList size={24} className="text-blue-700" />
                <Title order={3} className="text-blue-800">
                  Job Description
                </Title>
                <Badge color="blue" variant="light">
                  {documentData.documentStructure.completionRate}% Complete
                </Badge>
              </Group>
              <Stack gap="sm">
                {documentData.documentStructure.sections.map(
                  (section, index) => (
                    <div
                      key={index}
                      className="p-4 bg-white/70 rounded-lg border border-blue-200"
                    >
                      <Flex justify="space-between" align="center">
                        <div>
                          <Text fw={500}>{section.name}</Text>
                          <Text size="sm" c="dimmed">
                            Pages {section.pages}
                          </Text>
                        </div>
                        <Badge
                          size="sm"
                          color={
                            section.status === "Complete"
                              ? "green"
                              : section.status === "Under Review"
                              ? "yellow"
                              : "gray"
                          }
                          variant="light"
                        >
                          {section.status}
                        </Badge>
                      </Flex>
                    </div>
                  )
                )}
              </Stack>
            </Card>

            {/* Document Analytics */}
            <Card className="p-6 bg-gradient-to-br from-green-50 to-green-100 border border-green-200">
              <Group gap="sm" className="mb-4">
                <IconTrendingUp size={24} className="text-green-700" />
                <Title order={3} className="text-green-800">
                  Document Analytics
                </Title>
              </Group>
              <Grid>
                <Grid.Col span={3}>
                  <div className="text-center p-3 bg-white/70 rounded-lg">
                    <Text size="xl" fw={700} className="text-green-600">
                      {documentData.analytics.viewCount}
                    </Text>
                    <Text size="sm" c="dimmed">
                      Views
                    </Text>
                  </div>
                </Grid.Col>
                <Grid.Col span={3}>
                  <div className="text-center p-3 bg-white/70 rounded-lg">
                    <Text size="xl" fw={700} className="text-blue-600">
                      {documentData.analytics.downloadCount}
                    </Text>
                    <Text size="sm" c="dimmed">
                      Downloads
                    </Text>
                  </div>
                </Grid.Col>
                <Grid.Col span={3}>
                  <div className="text-center p-3 bg-white/70 rounded-lg">
                    <Text size="xl" fw={700} className="text-purple-600">
                      {documentData.analytics.collaborators}
                    </Text>
                    <Text size="sm" c="dimmed">
                      Collaborators
                    </Text>
                  </div>
                </Grid.Col>
                <Grid.Col span={3}>
                  <div className="text-center p-3 bg-white/70 rounded-lg">
                    <Text size="xl" fw={700} className="text-orange-600">
                      {documentData.analytics.comments}
                    </Text>
                    <Text size="sm" c="dimmed">
                      Comments
                    </Text>
                  </div>
                </Grid.Col>
              </Grid>
            </Card>
          </Stack>
        </Grid.Col>

        {/* Right Column */}
        <Grid.Col span={{ base: 12, lg: 4 }}>
          <Stack gap="xl">
            {/* Risk Analysis */}
            <Card className="p-6 bg-gradient-to-br from-orange-50 to-orange-100 border border-orange-200">
              <Group gap="sm" className="mb-4">
                <IconShield size={24} className="text-orange-700" />
                <Title order={3} className="text-orange-800">
                  Duties and responsibilities (UT)
                </Title>
              </Group>
              <div className="space-y-4">
                <div>
                  <Text size="sm" c="dimmed">
                    Assessment Date
                  </Text>
                  <Text fw={500}>
                    {documentData.riskAnalysis.assessmentDate}
                  </Text>
                </div>
                <div>
                  <Text size="sm" c="dimmed">
                    Assessor
                  </Text>
                  <Text fw={500}>{documentData.riskAnalysis.assessor}</Text>
                </div>
                <Divider />
                <div>
                  <Text fw={600} className="mb-3">
                    Risk Categories
                  </Text>
                  <Stack gap="sm">
                    {documentData.riskAnalysis.categories.map((cat, index) => (
                      <div key={index} className="p-3 bg-white/70 rounded-lg">
                        <Group justify="space-between" className="mb-2">
                          <Text fw={500} size="sm">
                            {cat.category}
                          </Text>
                          <Badge
                            size="sm"
                            color={
                              cat.level === "Low"
                                ? "green"
                                : cat.level === "Medium"
                                ? "yellow"
                                : "red"
                            }
                          >
                            {cat.level}
                          </Badge>
                        </Group>
                        <Text size="xs" c="dimmed" className="mb-1">
                          {cat.description}
                        </Text>
                        <Text size="xs" fw={500}>
                          Impact: {cat.impact}
                        </Text>
                      </div>
                    ))}
                  </Stack>
                </div>
              </div>
            </Card>

            {/* Quick Actions */}
            <Card className="p-6 bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200">
              <Group gap="sm" className="mb-4">
                <IconSettings size={24} className="text-purple-700" />
                <Title order={3} className="text-purple-800">
                  Quick Actions
                </Title>
              </Group>
              <Stack gap="sm">
                <Button
                  leftSection={<IconEye size={16} />}
                  variant="light"
                  color="blue"
                  fullWidth
                >
                  View Document
                </Button>
                <Button
                  leftSection={<IconDownload size={16} />}
                  variant="light"
                  color="green"
                  fullWidth
                >
                  Download PDF
                </Button>
                <Button
                  leftSection={<IconUsers size={16} />}
                  variant="light"
                  color="purple"
                  fullWidth
                >
                  Share Document
                </Button>
                <Button
                  leftSection={<IconFlag size={16} />}
                  variant="light"
                  color="orange"
                  fullWidth
                >
                  Generate Report
                </Button>
              </Stack>
            </Card>

            {/* Mitigation Actions */}
            <Card className="p-6 bg-gradient-to-br from-red-50 to-red-100 border border-red-200">
              <Group gap="sm" className="mb-4">
                <IconAlertTriangle size={24} className="text-red-700" />
                <Title order={3} className="text-red-800">
                  Mitigation Actions
                </Title>
              </Group>
              <Stack gap="sm">
                {documentData.riskAnalysis.mitigationActions.map(
                  (action, index) => (
                    <Group key={index} gap="sm" align="flex-start">
                      <IconCheck
                        size={16}
                        className="text-red-600 mt-1 flex-shrink-0"
                      />
                      <Text size="sm" className="text-gray-700">
                        {action}
                      </Text>
                    </Group>
                  )
                )}
              </Stack>
            </Card>
          </Stack>
        </Grid.Col>
      </Grid>
    </Paper>
  </div>
);
