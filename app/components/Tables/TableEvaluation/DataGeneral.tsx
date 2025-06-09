import { Badge, Group, Paper, Text, Title } from "@mantine/core";
import {
  IconCalendarStats,
  IconChecklist,
  IconCircleX,
  IconClipboardCheck,
  IconFileX,
  IconLoader2,
  IconMoodCheck,
  IconMoodX,
  IconProgress,
} from "@tabler/icons-react";
import TrainingOverview from "./TrainingOverview";
import AssessmentOverview from "./AssesmentOverview";
import { TrainingGeneral } from "./ButtonDetail";

type DataComponent = {
  dataEvaluationGeneral: TrainingGeneral | null;
  dataEvaluationRKI: TrainingGeneral | null;
};

const DataGeneral: React.FC<DataComponent> = ({
  dataEvaluationGeneral,
  dataEvaluationRKI,
}) => {
  const percentageTrainingGeneral =
    ((dataEvaluationGeneral?.realisation ?? 0) /
      (dataEvaluationGeneral?.planning ?? 0)) *
    100;

  const percentageTrainingOnProgressGeneral =
    ((dataEvaluationGeneral?.on_progress ?? 0) /
      (dataEvaluationGeneral?.planning ?? 0)) *
    100;

  const percentageTrainingCancelGeneral =
    ((dataEvaluationGeneral?.cancel ?? 0) /
      (dataEvaluationGeneral?.planning ?? 0)) *
    100;

  const percentageAssesmentGeneral =
    ((dataEvaluationGeneral?.realisation_assesment ?? 0) /
      (dataEvaluationGeneral?.planning_assesment ?? 0)) *
    100;

  const percentageAssesmentOnProgressGeneral =
    ((dataEvaluationGeneral?.on_progress_assesment ?? 0) /
      (dataEvaluationGeneral?.planning_assesment ?? 0)) *
    100;

  const percentageAssesmentCancelGeneral =
    ((dataEvaluationGeneral?.cancel_assesment ?? 0) /
      (dataEvaluationGeneral?.planning_assesment ?? 0)) *
    100;

  const percentageTrainingRKI =
    ((dataEvaluationRKI?.realisation ?? 0) /
      (dataEvaluationRKI?.planning ?? 0)) *
    100;

  const percentageTrainingOnProgressRKI =
    ((dataEvaluationRKI?.on_progress ?? 0) /
      (dataEvaluationRKI?.planning ?? 0)) *
    100;

  const percentageTrainingCancelRKI =
    ((dataEvaluationRKI?.cancel ?? 0) / (dataEvaluationRKI?.planning ?? 0)) *
    100;

  const percentageAssesmentRKI =
    ((dataEvaluationRKI?.realisation_assesment ?? 0) /
      (dataEvaluationRKI?.planning_assesment ?? 0)) *
    100;

  const percentageAssesmentOnProgressRKI =
    ((dataEvaluationRKI?.on_progress_assesment ?? 0) /
      (dataEvaluationRKI?.planning_assesment ?? 0)) *
    100;

  const percentageAssesmentCancelRKI =
    ((dataEvaluationRKI?.cancel_assesment ?? 0) /
      (dataEvaluationRKI?.planning_assesment ?? 0)) *
    100;

  return (
    <>
      <div className="max-w-6xl mx-auto font-satoshi mb-20 mt-10">
        {/* === REALIZATION BASED SECTION === */}
        {dataEvaluationGeneral ? (
          <>
            <div className="mb-8">
              <div className="relative flex flex-col items-center justify-center space-y-2">
                <div className="absolute inset-x-0 top-1/2 h-[2px] bg-gradient-to-r from-blue-100 via-blue-400 to-blue-100" />

                <div className="relative z-10 bg-white px-6 py-2 rounded-full shadow-sm">
                  <Group justify="center" gap="xs">
                    <Title
                      order={2}
                      className="text-4xl font-bold text-gray-800"
                    >
                      Training Based on Realization
                    </Title>
                    <Badge
                      variant="light"
                      color="teal"
                      size="xl"
                      radius="sm"
                      className="ml-2"
                      mt="sm"
                    >
                      Live Data
                    </Badge>
                  </Group>
                </div>
              </div>
              <Text
                c="dimmed"
                size="sm"
                ta="center"
                className="mt-2 max-w-6xl mx-auto"
              >
                Comprehensive overview of training initiatives and completion
                metrics | Last updated: {new Date().toLocaleDateString()}
              </Text>
            </div>

            {/* Main Stats Cards */}
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
              <Paper
                shadow="sm"
                radius="lg"
                p="md"
                className="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200"
              >
                <div className="flex flex-col items-center text-center p-2">
                  <IconCalendarStats
                    size={40}
                    className="text-purple-600 mb-2"
                  />
                  <Text fw={700} className="mb-1">
                    Planned Training
                  </Text>
                  <Badge
                    variant="filled"
                    color="violet"
                    size="xl"
                    radius="md"
                    className="text-lg py-3 px-6 mt-2 shadow-sm"
                  >
                    {dataEvaluationGeneral.planning ?? 0}
                  </Badge>
                </div>
              </Paper>

              <Paper
                shadow="sm"
                radius="lg"
                p="md"
                className="bg-gradient-to-br from-green-50 to-green-100 border border-green-200"
              >
                <div className="flex flex-col items-center text-center p-2">
                  <IconChecklist size={40} className="text-green-600 mb-2" />
                  <Text fw={700} className="mb-1">
                    Completed Training
                  </Text>
                  <Badge
                    variant="filled"
                    color="green"
                    size="xl"
                    radius="md"
                    className="text-lg py-3 px-6 mt-2 shadow-sm"
                  >
                    {dataEvaluationGeneral.realisation ?? 0}
                  </Badge>
                </div>
              </Paper>

              <Paper
                shadow="sm"
                radius="lg"
                p="md"
                className="bg-gradient-to-br from-red-50 to-red-100 border border-red-200"
              >
                <div className="flex flex-col items-center text-center p-2">
                  <IconCircleX size={40} className="text-red-400 mb-2" />
                  <Text fw={700} className="mb-1">
                    Cancelled Training
                  </Text>
                  <Badge
                    variant="filled"
                    color="red"
                    size="xl"
                    radius="md"
                    className="text-lg py-3 px-6 mt-2 shadow-sm"
                  >
                    {dataEvaluationGeneral.cancel ?? 0}
                  </Badge>
                </div>
              </Paper>

              <Paper
                shadow="sm"
                radius="lg"
                p="md"
                className="bg-gradient-to-br from-yellow-50 to-yellow-100 border border-yellow-200"
              >
                <div className="flex flex-col items-center text-center p-2">
                  <IconProgress size={40} className="text-yellow-500 mb-2" />
                  <Text fw={700} className="mb-1">
                    On Progress Training
                  </Text>
                  <Badge
                    variant="filled"
                    color="yellow"
                    size="xl"
                    radius="md"
                    className="text-lg py-3 px-6 mt-2 shadow-sm"
                  >
                    {dataEvaluationGeneral.on_progress ?? 0}
                  </Badge>
                </div>
              </Paper>
            </div>

            {/* Training Progress Visualization */}
            <TrainingOverview
              completed={percentageTrainingGeneral}
              onProgress={percentageTrainingOnProgressGeneral}
              cancelled={percentageTrainingCancelGeneral}
            />

            {/* Assessment Cards */}
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8 max-w-6xl mx-auto mb-10">
              <Paper
                p="md"
                shadow="sm"
                className="border-l-4 border-purple-600 hover:shadow-md transition-shadow"
              >
                <div className="flex items-center">
                  <div className="mr-3">
                    <IconCalendarStats className="text-purple-600" size={32} />
                  </div>
                  <div>
                    <Text fw={600} size="sm" c="dimmed">
                      Planned Assessment
                    </Text>
                    <Text fw={700} size="xl">
                      {dataEvaluationGeneral.planning_assesment ?? 0}
                    </Text>
                  </div>
                </div>
              </Paper>

              <Paper
                p="md"
                shadow="sm"
                className="border-l-4 border-green-500 hover:shadow-md transition-shadow"
              >
                <div className="flex items-center">
                  <div className="mr-3">
                    <IconClipboardCheck className="text-green-500" size={32} />
                  </div>
                  <div>
                    <Text fw={600} size="sm" c="dimmed">
                      Completed Assessment
                    </Text>
                    <Text fw={700} size="xl">
                      {dataEvaluationGeneral.realisation_assesment ?? 0}
                    </Text>
                  </div>
                </div>
              </Paper>

              <Paper
                p="md"
                shadow="sm"
                className="border-l-4 border-red-400 hover:shadow-md transition-shadow"
              >
                <div className="flex items-center">
                  <div className="mr-3">
                    <IconCircleX className="text-red-400" size={32} />
                  </div>
                  <div>
                    <Text fw={600} size="sm" c="dimmed">
                      Cancelled Assessment
                    </Text>
                    <Text fw={700} size="xl">
                      {dataEvaluationGeneral.cancel_assesment ?? 0}
                    </Text>
                  </div>
                </div>
              </Paper>

              <Paper
                p="md"
                shadow="sm"
                className="border-l-4 border-yellow-200 hover:shadow-md transition-shadow"
              >
                <div className="flex items-center">
                  <div className="mr-3">
                    <IconLoader2 className="text-yellow-500" size={32} />
                  </div>
                  <div>
                    <Text fw={600} size="sm" c="dimmed">
                      On Progress Assessment
                    </Text>
                    <Text fw={700} size="xl">
                      {dataEvaluationGeneral.on_progress_assesment ?? 0}
                    </Text>
                  </div>
                </div>
              </Paper>
            </div>

            {/* Stats Segments */}
            <div className="mt-4 max-w-6xl mx-auto">
              <Paper
                shadow="sm"
                radius="md"
                p="lg"
                className="border border-gray-100"
              >
                <Text fw={600} size="md" className="mb-4">
                  Performance Breakdown
                </Text>
                <AssessmentOverview
                  completed={percentageAssesmentGeneral}
                  onProgress={percentageAssesmentOnProgressGeneral}
                  cancelled={percentageAssesmentCancelGeneral}
                />
              </Paper>
            </div>
          </>
        ) : (
          <div className="flex flex-col items-center justify-center py-16">
            <IconFileX size={60} className="text-gray-300 mb-3" />
            <Text c="dimmed" size="lg">
              No data available for Realization
            </Text>
          </div>
        )}

        {/* === RKI BASED SECTION === */}
        {dataEvaluationRKI ? (
          <div className="mt-16 mb-20">
            <div className="mb-8">
              <div className="relative flex flex-col items-center justify-center space-y-2">
                <div className="absolute inset-x-0 top-1/2 h-[2px] bg-gradient-to-r from-teal-100 via-teal-400 to-teal-100" />

                <div className="relative z-10 bg-white px-6 py-2 rounded-full shadow-sm">
                  <Group justify="center" gap="xs">
                    <Title
                      order={2}
                      className="text-4xl font-bold text-gray-800"
                    >
                      Training Based on RKI
                    </Title>
                    <Badge
                      variant="light"
                      color="cyan"
                      size="xl"
                      radius="sm"
                      className="ml-2"
                      mt="sm"
                    >
                      Live Data
                    </Badge>
                  </Group>
                </div>
              </div>
              <Text
                c="dimmed"
                size="sm"
                ta="center"
                className="mt-2 max-w-6xl mx-auto"
              >
                Key performance indicators for training program tracking | Last
                updated: {new Date().toLocaleDateString()}
              </Text>
            </div>

            {/* Main Stats Cards */}
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
              <Paper
                shadow="sm"
                radius="lg"
                p="md"
                className="bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200"
              >
                <div className="flex flex-col items-center text-center p-2">
                  <IconCalendarStats
                    size={40}
                    className="text-indigo-600 mb-2"
                  />
                  <Text fw={700} className="mb-1">
                    Planned Training
                  </Text>
                  <Badge
                    variant="filled"
                    color="indigo"
                    size="xl"
                    radius="md"
                    className="text-lg py-3 px-6 mt-2 shadow-sm"
                  >
                    {dataEvaluationRKI.planning ?? 0}
                  </Badge>
                </div>
              </Paper>

              <Paper
                shadow="sm"
                radius="lg"
                p="md"
                className="bg-gradient-to-br from-green-50 to-green-100 border border-green-200"
              >
                <div className="flex flex-col items-center text-center p-2">
                  <IconChecklist size={40} className="text-green-600 mb-2" />
                  <Text fw={700} className="mb-1">
                    Completed Training
                  </Text>
                  <Badge
                    variant="filled"
                    color="green"
                    size="xl"
                    radius="md"
                    className="text-lg py-3 px-6 mt-2 shadow-sm"
                  >
                    {dataEvaluationRKI.realisation ?? 0}
                  </Badge>
                </div>
              </Paper>

              <Paper
                shadow="sm"
                radius="lg"
                p="md"
                className="bg-gradient-to-br from-red-50 to-red-100 border border-red-200"
              >
                <div className="flex flex-col items-center text-center p-2">
                  <IconCircleX size={40} className="text-red-400 mb-2" />
                  <Text fw={700} className="mb-1">
                    Cancelled Training
                  </Text>
                  <Badge
                    variant="filled"
                    color="red"
                    size="xl"
                    radius="md"
                    className="text-lg py-3 px-6 mt-2 shadow-sm"
                  >
                    {dataEvaluationRKI.cancel ?? 0}
                  </Badge>
                </div>
              </Paper>

              <Paper
                shadow="sm"
                radius="lg"
                p="md"
                className="bg-gradient-to-br from-yellow-50 to-yellow-100 border border-yellow-200"
              >
                <div className="flex flex-col items-center text-center p-2">
                  <IconProgress size={40} className="text-yellow-500 mb-2" />
                  <Text fw={700} className="mb-1">
                    On Progress Training
                  </Text>
                  <Badge
                    variant="filled"
                    color="yellow"
                    size="xl"
                    radius="md"
                    className="text-lg py-3 px-6 mt-2 shadow-sm"
                  >
                    {dataEvaluationRKI.on_progress ?? 0}
                  </Badge>
                </div>
              </Paper>
            </div>

            {/* Training Progress Visualization */}
            <TrainingOverview
              completed={percentageTrainingRKI}
              onProgress={percentageTrainingOnProgressRKI}
              cancelled={percentageTrainingCancelRKI}
            />

            {/* Assessment Cards */}
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8 max-w-6xl mx-auto">
              <Paper
                p="md"
                shadow="sm"
                className="border-l-4 border-indigo-600 hover:shadow-md transition-shadow"
              >
                <div className="flex items-center">
                  <div className="mr-3">
                    <IconCalendarStats className="text-indigo-600" size={32} />
                  </div>
                  <div>
                    <Text fw={600} size="sm" c="dimmed">
                      Planned Assessment
                    </Text>
                    <Text fw={700} size="xl">
                      {dataEvaluationRKI.planning_assesment ?? 0}
                    </Text>
                  </div>
                </div>
              </Paper>

              <Paper
                p="md"
                shadow="sm"
                className="border-l-4 border-green-500 hover:shadow-md transition-shadow"
              >
                <div className="flex items-center">
                  <div className="mr-3">
                    <IconClipboardCheck className="text-green-500" size={32} />
                  </div>
                  <div>
                    <Text fw={600} size="sm" c="dimmed">
                      Completed Assessment
                    </Text>
                    <Text fw={700} size="xl">
                      {dataEvaluationRKI.realisation_assesment ?? 0}
                    </Text>
                  </div>
                </div>
              </Paper>

              <Paper
                p="md"
                shadow="sm"
                className="border-l-4 border-red-400 hover:shadow-md transition-shadow"
              >
                <div className="flex items-center">
                  <div className="mr-3">
                    <IconCircleX className="text-red-400" size={32} />
                  </div>
                  <div>
                    <Text fw={600} size="sm" c="dimmed">
                      Cancelled Assessment
                    </Text>
                    <Text fw={700} size="xl">
                      {dataEvaluationRKI.cancel_assesment ?? 0}
                    </Text>
                  </div>
                </div>
              </Paper>

              <Paper
                p="md"
                shadow="sm"
                className="border-l-4 border-yellow-200 hover:shadow-md transition-shadow"
              >
                <div className="flex items-center">
                  <div className="mr-3">
                    <IconLoader2 className="text-yellow-500" size={32} />
                  </div>
                  <div>
                    <Text fw={600} size="sm" c="dimmed">
                      On Progress Assessment
                    </Text>
                    <Text fw={700} size="xl">
                      {dataEvaluationRKI.on_progress_assesment ?? 0}
                    </Text>
                  </div>
                </div>
              </Paper>
            </div>

            {/* Stats Segments */}
            <div className="mt-4 max-w-6xl mx-auto">
              <Paper
                shadow="sm"
                radius="md"
                p="lg"
                className="border border-gray-100"
              >
                <Text fw={600} size="md" className="mb-4">
                  Performance Breakdown
                </Text>
                <AssessmentOverview
                  completed={percentageAssesmentRKI}
                  onProgress={percentageAssesmentOnProgressRKI}
                  cancelled={percentageAssesmentCancelRKI}
                />
              </Paper>
            </div>
          </div>
        ) : (
          <div className="flex flex-col items-center justify-center py-16 mt-10">
            <IconFileX size={60} className="text-gray-300 mb-3" />
            <Text c="dimmed" size="lg">
              No data available for RKI
            </Text>
          </div>
        )}

        {/* Comparison Section */}
        {dataEvaluationGeneral && dataEvaluationRKI && (
          <div className="mb-20">
            <div className="relative flex flex-col items-center justify-center space-y-2 mb-8">
              <div className="absolute inset-x-0 top-1/2 h-[2px] bg-gradient-to-r from-transparent via-gray-300 to-transparent" />

              <div className="relative z-10 bg-white px-6 py-2 rounded-full shadow-sm">
                <Title order={3} className="text-2xl font-bold text-gray-800">
                  Training Comparison Analysis
                </Title>
              </div>
            </div>

            <Paper
              shadow="lg"
              radius="md"
              p="xl"
              className="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100"
            >
              <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                  <Text fw={600} className="mb-2">
                    Completion Rate Comparison
                  </Text>
                  <div className="h-64 bg-white p-4 rounded-lg shadow-sm">
                    {/* Here you would ideally have a chart component */}
                    <div className="flex flex-col h-full justify-center">
                      <Text ta="center" c="dimmed" size="sm" className="mb-4">
                        Assesment Completion Rate (%)
                      </Text>
                      <div className="space-y-4">
                        <div>
                          <div className="flex justify-between mb-1">
                            <Text size="sm">Realization</Text>
                            <Text size="sm" fw={500}>
                              {percentageAssesmentGeneral.toFixed(2)}%
                            </Text>
                          </div>
                          <div className="h-4 bg-gray-100 rounded-full overflow-hidden">
                            <div
                              className="bg-blue-500 h-full"
                              style={{
                                width: `${percentageAssesmentGeneral}%`,
                              }}
                            ></div>
                          </div>
                        </div>
                        <div>
                          <div className="flex justify-between mb-1">
                            <Text size="sm">RKI</Text>
                            <Text size="sm" fw={500}>
                              {percentageAssesmentRKI.toFixed(2)}%
                            </Text>
                          </div>
                          <div className="h-4 bg-gray-100 rounded-full overflow-hidden">
                            <div
                              className="bg-indigo-500 h-full"
                              style={{
                                width: `${percentageAssesmentRKI}%`,
                              }}
                            ></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div>
                  <Text fw={600} className="mb-2">
                    Assessment Completion Status
                  </Text>
                  <div className="h-64 bg-white p-4 rounded-lg shadow-sm flex flex-col justify-between">
                    <div>
                      <Text fw={500} size="sm" className="mb-2">
                        Realization Assessments
                      </Text>
                      <div className="grid grid-cols-3 gap-2 text-center">
                        <div className="p-2 bg-blue-50 rounded-md">
                          <Text c="dimmed" size="xs">
                            Planned
                          </Text>
                          <Text fw={600}>
                            {dataEvaluationGeneral.planning_assesment ?? 0}
                          </Text>
                        </div>
                        <div className="p-2 bg-green-50 rounded-md">
                          <Text c="dimmed" size="xs">
                            Completed
                          </Text>
                          <Text fw={600} className="text-green-600">
                            {dataEvaluationGeneral.realisation_assesment ?? 0}
                          </Text>
                        </div>
                        <div className="p-2 bg-red-50 rounded-md">
                          <Text c="dimmed" size="xs">
                            Cancelled
                          </Text>
                          <Text fw={600} className="text-red-500">
                            {dataEvaluationGeneral.cancel_assesment ?? 0}
                          </Text>
                        </div>
                      </div>
                    </div>

                    <div>
                      <Text fw={500} size="sm" className="mb-2">
                        RKI Assessments
                      </Text>
                      <div className="grid grid-cols-3 gap-2 text-center">
                        <div className="p-2 bg-blue-50 rounded-md">
                          <Text c="dimmed" size="xs">
                            Planned
                          </Text>
                          <Text fw={600}>
                            {dataEvaluationRKI.planning_assesment ?? 0}
                          </Text>
                        </div>
                        <div className="p-2 bg-green-50 rounded-md">
                          <Text c="dimmed" size="xs">
                            Completed
                          </Text>
                          <Text fw={600} className="text-green-600">
                            {dataEvaluationRKI.realisation_assesment ?? 0}
                          </Text>
                        </div>
                        <div className="p-2 bg-red-50 rounded-md">
                          <Text c="dimmed" size="xs">
                            Cancelled
                          </Text>
                          <Text fw={600} className="text-red-500">
                            {dataEvaluationRKI.cancel_assesment ?? 0}
                          </Text>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </Paper>
          </div>
        )}

        {(dataEvaluationGeneral?.competent_assessment !== undefined ||
          dataEvaluationRKI?.competent_assessment !== undefined) && (
          <div className="mt-16 mb-20">
            <div className="mb-8">
              <div className="relative flex flex-col items-center justify-center space-y-2">
                <div className="absolute inset-x-0 top-1/2 h-[2px] bg-gradient-to-r from-emerald-100 via-emerald-400 to-emerald-100" />

                <div className="relative z-10 bg-white px-6 py-2 rounded-full shadow-sm">
                  <Group justify="center" gap="xs">
                    <Title
                      order={2}
                      className="text-4xl font-bold text-gray-800"
                    >
                      Competency Assessment Results
                    </Title>
                    <Badge
                      variant="light"
                      color="teal"
                      size="xl"
                      radius="sm"
                      className="ml-2"
                      mt="sm"
                    >
                      Performance Metrics
                    </Badge>
                  </Group>
                </div>
              </div>
              <Text
                c="dimmed"
                size="sm"
                ta="center"
                className="mt-2 max-w-6xl mx-auto"
              >
                Detailed breakdown of competency assessment outcomes | Last
                updated: {new Date().toLocaleDateString()}
              </Text>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
              {/* General Competency */}
              {dataEvaluationGeneral?.competent_assessment !== undefined && (
                <Paper
                  shadow="sm"
                  p="lg"
                  className="border-l-4 border-blue-500 bg-blue-50"
                >
                  <Title order={3} className="mb-4 text-blue-800">
                    General Competency
                  </Title>
                  <div className="grid grid-cols-2 gap-4">
                    <div className="flex items-center p-4 bg-white rounded-lg">
                      <IconMoodCheck
                        className="text-green-600 mr-3"
                        size={32}
                      />
                      <div>
                        <Text fw={600} size="sm" c="dimmed">
                          Competent
                        </Text>
                        <Text fw={700} size="xl">
                          {dataEvaluationGeneral.competent_assessment ?? 0}
                        </Text>
                      </div>
                    </div>
                    <div className="flex items-center p-4 bg-white rounded-lg">
                      <IconMoodX className="text-red-600 mr-3" size={32} />
                      <div>
                        <Text fw={600} size="sm" c="dimmed">
                          Not Competent
                        </Text>
                        <Text fw={700} size="xl">
                          {dataEvaluationGeneral.not_competent_assessment ?? 0}
                        </Text>
                      </div>
                    </div>
                  </div>
                </Paper>
              )}

              {/* RKI Competency */}
              {dataEvaluationRKI?.competent_assessment !== undefined && (
                <Paper
                  shadow="sm"
                  p="lg"
                  className="border-l-4 border-teal-500 bg-teal-50"
                >
                  <Title order={3} className="mb-4 text-teal-800">
                    RKI Competency
                  </Title>
                  <div className="grid grid-cols-2 gap-4">
                    <div className="flex items-center p-4 bg-white rounded-lg">
                      <IconMoodCheck
                        className="text-green-600 mr-3"
                        size={32}
                      />
                      <div>
                        <Text fw={600} size="sm" c="dimmed">
                          Competent
                        </Text>
                        <Text fw={700} size="xl">
                          {dataEvaluationRKI.competent_assessment ?? 0}
                        </Text>
                      </div>
                    </div>
                    <div className="flex items-center p-4 bg-white rounded-lg">
                      <IconMoodX className="text-red-600 mr-3" size={32} />
                      <div>
                        <Text fw={600} size="sm" c="dimmed">
                          Not Competent
                        </Text>
                        <Text fw={700} size="xl">
                          {dataEvaluationRKI.not_competent_assessment ?? 0}
                        </Text>
                      </div>
                    </div>
                  </div>
                </Paper>
              )}
            </div>

            {!(
              dataEvaluationGeneral?.competent_assessment !== undefined ||
              dataEvaluationRKI?.competent_assessment !== undefined
            ) && (
              <div className="flex flex-col items-center justify-center py-16">
                <IconFileX size={60} className="text-gray-300 mb-3" />
                <Text c="dimmed" size="lg">
                  No competency assessment data available
                </Text>
              </div>
            )}
          </div>
        )}
      </div>
    </>
  );
};

export default DataGeneral;
