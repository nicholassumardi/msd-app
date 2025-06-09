/* eslint-disable @typescript-eslint/no-explicit-any */

import { useState } from "react";
import { Drawer } from "@mantine/core";
import { IconArrowRight, IconArrowLeft } from "@tabler/icons-react";
import EmployeeSelectionContent from "./SecondaryContent";
import { option } from "../../../../pages/types/option";
import { TableData } from "./TableData";
import { UseDataTableReturn } from "../../../../hooks/useDataTableState";
import { MRT_ColumnDef } from "mantine-react-table";
import { IKWToTrain } from "@/evaluation/page";

interface FormComponent {
  opened: boolean;
  setOpened: React.Dispatch<React.SetStateAction<boolean>>;
  dataIKWToTrain: UseDataTableReturn;
  handleGetDataTrainer: () => Promise<void>;
  dataTrainer: option[];
  form: any;
  dataTrainerIKW: option[];
  dataTraineeByTrainer: UseDataTableReturn;
}

const EmployeeIKWToTrainDrawer: React.FC<FormComponent> = ({
  opened,
  setOpened,
  dataIKWToTrain,
  handleGetDataTrainer,
  dataTrainer,
  form,
  dataTrainerIKW,
  dataTraineeByTrainer,
}) => {
  const [showSecondaryContent, setShowSecondaryContent] = useState(false);
  const ikwColumns: MRT_ColumnDef<IKWToTrain>[] = [
    { accessorKey: "employee_name", header: "Name" },
    { accessorKey: "employee_department", header: "Department Code" },
    { accessorKey: "ikw_name", header: "IKW Name" },
    { accessorKey: "ikw_code", header: "IKW Code" },
    { accessorKey: "assessment_result", header: "Result" },
  ];
  return (
    <div className="p-8 max-w-4xl mx-auto">
      <Drawer
        opened={opened}
        size="100%"
        onClose={() => {
          setOpened(false);
          setShowSecondaryContent(false);
        }}
        title={""}
        padding="0" // Remove padding for split view
        position="right"
        overlayProps={{ opacity: 0.5, blur: 4 }}
        styles={{
          header: {
            padding: "1.5rem",
            borderBottom: "1px solid #e5e7eb",
          },
          body: {
            overflow: "hidden", // Prevent double scrollbars
          },
        }}
      >
        <div className="flex h-full">
          {/* Primary content (table) - shrinks when secondary content is shown */}
          <div
            className={`transition-all duration-300 ease-in-out h-full overflow-auto ${
              showSecondaryContent ? "w-1/2" : "w-full"
            }`}
          >
            <div className="p-6">
              <TableData columns={ikwColumns} {...dataIKWToTrain} />
            </div>
          </div>

          {/* Secondary content - slides in from right */}
          <div
            className={`mt-6 bg-gradient-to-br from-gray-50 to-blue-50 h-full w-1/2 overflow-auto border-l border-gray-200 shadow-inner transform transition-all duration-300 ease-in-out ${
              showSecondaryContent ? "translate-x-0" : "translate-x-full w-0"
            }`}
          >
            <div className="p-6">
              <div className="flex justify-between items-center mb-6">
                <h2 className="text-2xl font-semibold text-gray-800">
                  Detail View
                </h2>
                <button
                  onClick={() => setShowSecondaryContent(false)}
                  className="text-gray-500 hover:text-gray-700 transition-colors"
                >
                  <IconArrowRight size={20} />
                </button>
              </div>
              <div className="bg-white rounded-lg shadow-sm p-6">
                <p className="text-gray-600 mb-4">
                  <EmployeeSelectionContent
                    dataTrainer={dataTrainer}
                    form={form}
                    dataTrainerIKW={dataTrainerIKW}
                    dataTraineeByTrainer={dataTraineeByTrainer}
                  />
                </p>
              </div>
            </div>
          </div>

          {/* Toggle button - only shown when secondary content is not visible */}
          {!showSecondaryContent && (
            <button
              onClick={() => {
                setShowSecondaryContent(true);
                handleGetDataTrainer();
              }}
              className="fixed right-8 top-1/2 transform -translate-y-1/2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white p-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110 z-20"
              aria-label="Show details"
            >
              <IconArrowLeft size={20} />
            </button>
          )}
        </div>
      </Drawer>
    </div>
  );
};

export default EmployeeIKWToTrainDrawer;
