/* eslint-disable @typescript-eslint/no-explicit-any */
import { Select, Avatar } from "@mantine/core";
import { IconUserCheck } from "@tabler/icons-react";
import { option } from "../../../../pages/types/option";
import { UseDataTableReturn } from "../../../../hooks/useDataTableState";
import { TableData } from "./TableData";
import { MRT_ColumnDef } from "mantine-react-table";
import { IKWToTrain } from "@/evaluation/page";

interface FormComponent {
  dataTrainer: option[];
  form: any;
  dataTrainerIKW: option[];
  dataTraineeByTrainer: UseDataTableReturn;
}

const EmployeeSelectionContent: React.FC<FormComponent> = ({
  dataTrainer,
  form,
  dataTrainerIKW,
  dataTraineeByTrainer,
}) => {
  const ikwColumns: MRT_ColumnDef<IKWToTrain>[] = [
    { accessorKey: "employee_name", header: "Name" },
    { accessorKey: "employee_department", header: "Department Code" },
    { accessorKey: "ikw_name", header: "IKW Name" },
    { accessorKey: "ikw_code", header: "IKW Code" },
  ];
  return (
    <div className="bg-white rounded-lg shadow-sm">
      {/* Employee selection */}
      <div className="flex gap-3 p-6 border-b border-gray-200">
        <Select
          id="employee-select"
          label="Select Instructor/ NIP"
          placeholder="Choose an employee to see their trainees"
          onChange={(value) => {
            const selectedItem = dataTrainer.find(
              (item) => item.value === value
            );
            const selectedName = selectedItem?.label.split(" (")[0]; // Extract name from label
            const selectedNIP = selectedItem?.label.split(" ")[1]; // Extract name from label
            form.setFieldValue("trainer_name", selectedName);
            form.setFieldValue("trainer_nip", selectedNIP);
            form.setFieldValue("trainer_id", value);
            form.setFieldValue("ikw_id", null);
          }}
          data={dataTrainer}
          searchable
          clearable
          max={10}
          nothingFoundMessage="No employees found"
          rightSection={<IconUserCheck size={16} />}
          className="w-full max-w-md"
          // key={form.key("trainer_id")}
          // {...form.getInputProps("trainer_id")}
        />
        <Select
          id="employee-select"
          label="IKW Available"
          placeholder="Choose an IKW to see trainees"
          data={dataTrainerIKW}
          clearable
          searchable
          nothingFoundMessage="No IKW found"
          rightSection={<IconUserCheck size={16} />}
          className="w-full max-w-md"
          key={form.key("ikw_id")}
          {...form.getInputProps("ikw_id")}
        />
      </div>

      {/* Instructor details */}
      {form.values.trainer_name && (
        <>
          <div className="p-6 bg-blue-50 border-b border-gray-200">
            <div className="flex items-center">
              <Avatar
                src="/api/placeholder/40/40"
                radius="xl"
                size="lg"
                className="mr-4"
              />
              <div>
                <h3 className="text-lg font-medium text-gray-900">
                  {form.values.trainer_name}
                </h3>
                <div className="text-sm text-gray-600">
                  NIP : {form.values.trainer_nip}
                </div>
              </div>
            </div>
          </div>

          <div className="p-6  border-b border-gray-200">
            <TableData columns={ikwColumns} {...dataTraineeByTrainer} />
          </div>
        </>
      )}

      {/* Empty state - when no instructor is selected */}
      {!form.values.trainer_name && (
        <div className="p-12 text-center">
          <div className="mx-auto h-20 w-20 bg-blue-100 rounded-full flex items-center justify-center mb-4">
            <IconUserCheck size={40} className="text-blue-600" />
          </div>
          <h3 className="text-lg font-medium text-gray-900 mb-2">
            Select Trainer
          </h3>
          <p className="text-gray-500 max-w-md mx-auto">
            Choose a training instructor from the dropdown above to view the
            list of people who need to be trained.
          </p>
        </div>
      )}
    </div>
  );
};

export default EmployeeSelectionContent;
