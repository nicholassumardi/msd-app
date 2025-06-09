import { Paper, Text } from "@mantine/core";

type CardComponent = {
  completed: number;
  onProgress: number;
  cancelled: number;
};

const TrainingOverview: React.FC<CardComponent> = ({
  completed,
  onProgress,
  cancelled,
}) => {
  return (
    <Paper
      shadow="sm"
      radius="md"
      p="lg"
      className="mb-8 border border-gray-100"
    >
      <Text fw={600} size="md" className="mb-3">
        Training Progress Overview
      </Text>
      <div className="h-6 w-full bg-gray-100 rounded-full overflow-hidden mb-3">
        <div className="flex h-full">
          <div
            className="bg-green-500 h-full"
            style={{
              width: `${completed}%`,
            }}
          ></div>
          <div
            className="bg-yellow-400 h-full"
            style={{
              width: `${onProgress}%`,
            }}
          ></div>
          <div
            className="bg-red-400 h-full"
            style={{
              width: `${cancelled}%`,
            }}
          ></div>
        </div>
      </div>
      <div className="flex justify-between">
        <div className="flex items-center">
          <div className="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
          <Text size="xs">
            Completed ({completed.toFixed(2)}
            %)
          </Text>
        </div>
        <div className="flex items-center">
          <div className="w-3 h-3 rounded-full bg-yellow-400 mr-2"></div>
          <Text size="xs">
            In Progress ({onProgress.toFixed(2)}
            %)
          </Text>
        </div>
        <div className="flex items-center">
          <div className="w-3 h-3 rounded-full bg-red-400 mr-2"></div>
          <Text size="xs">
            Cancelled ({cancelled.toFixed(2)}
            %)
          </Text>
        </div>
      </div>
    </Paper>
  );
};

export default TrainingOverview;
