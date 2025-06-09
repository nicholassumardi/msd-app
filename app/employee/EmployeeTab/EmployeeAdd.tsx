/* eslint-disable @typescript-eslint/no-explicit-any */
import React from "react";
import {
  Tabs,
  TextInput,
  Select,
  Textarea,
  Button,
  ScrollArea,
} from "@mantine/core";
import {
  IconUser,
  IconPhone,
  IconCalendar,
  IconFileText,
  IconRings,
  IconBook,
  IconHome,
  IconBriefcase,
  IconBuildingEstate,
  IconStatusChange,
  IconX,
  IconDeviceFloppy,
} from "@tabler/icons-react";
import useEmployeeData from "../../../hooks/EmployeeCentre";
import { DatePickerInput } from "@mantine/dates";
import { option } from "../../../pages/types/option";

interface EmployeeAddFormProps {
  form: any;
  setIsTimelineOpen: (value: boolean) => void;
  dataCompany?: option[];
}

export const EmployeeAddForm: React.FC<EmployeeAddFormProps> = ({
  form,
  setIsTimelineOpen,
  dataCompany,
}) => {
  const { handleSubmitEmployee } = useEmployeeData();

  const handleCancel = () => {
    setIsTimelineOpen(false);
    form.reset();
  };

  const handleSubmit = (values: any) => {
    handleSubmitEmployee(values);
  };

  return (
    <form onSubmit={form.onSubmit((values: any) => handleSubmit(values))}>
      <div className="max-w-6xl bg-white dark:bg-neutral-900 font-sans rounded-lg shadow-md font-satoshi">
        <div className="p-6">
          <Tabs defaultValue="personal" className="mb-4">
            <Tabs.List className="mb-6">
              <Tabs.Tab
                value="personal"
                leftSection={<IconUser size={18} />}
                className="px-4 py-2 font-medium"
              >
                Personal Info
              </Tabs.Tab>
              <Tabs.Tab
                value="contact"
                leftSection={<IconPhone size={18} />}
                className="px-4 py-2 font-medium"
              >
                Contact Info
              </Tabs.Tab>
              <Tabs.Tab
                value="employment"
                leftSection={<IconBriefcase size={18} />}
                className="px-4 py-2 font-medium"
              >
                Employment
              </Tabs.Tab>
              <Tabs.Tab
                value="contract"
                leftSection={<IconFileText size={18} />}
                className="px-4 py-2 font-medium"
              >
                Contract
              </Tabs.Tab>
            </Tabs.List>

            <ScrollArea className="h-auto">
              {/* Personal Information Tab */}
              <Tabs.Panel value="personal" pt="md">
                <div className="space-y-6">
                  <div className="bg-gray-50 dark:bg-neutral-800 p-6 rounded-lg">
                    <h3 className="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-6">
                      Basic Information
                    </h3>

                    <div className="space-y-4">
                      <TextInput
                        label="Full Name"
                        placeholder="Enter full name"
                        required
                        leftSection={<IconUser size={16} />}
                        key={form.key("name")}
                        {...form.getInputProps("name")}
                      />

                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <TextInput
                          label="Staff ID"
                          placeholder="Enter staff ID"
                          key={form.key("staff_id")}
                          {...form.getInputProps("staff_id")}
                          leftSection={<IconBriefcase size={16} />}
                        />

                        <TextInput
                          label="NIK (Identity Card)"
                          placeholder="Enter identity card number"
                          leftSection={<IconFileText size={16} />}
                          key={form.key("identity_card")}
                          {...form.getInputProps("identity_card")}
                        />
                      </div>

                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <DatePickerInput
                          clearable
                          label="Date of Birth"
                          placeholder="Select date of birth"
                          key={form.key("date_of_birth")}
                          {...form.getInputProps("date_of_birth")}
                          valueFormat="YYYY-MM-DD"
                          leftSection={<IconCalendar size={16} />}
                        />

                        <Select
                          label="Gender"
                          placeholder="Select gender"
                          key={form.key("gender")}
                          {...form.getInputProps("gender")}
                          data={[
                            { value: "MALE", label: "Male" },
                            { value: "FEMALE", label: "Female" },
                          ]}
                          leftSection={<IconUser size={16} />}
                        />
                      </div>

                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Select
                          label="Marital Status"
                          placeholder="Select marital status"
                          key={form.key("marital_status")}
                          {...form.getInputProps("marital_status")}
                          data={[
                            { value: "MENIKAH", label: "Married" },
                            { value: "BELUM MENIKAH", label: "Single" },
                          ]}
                          leftSection={<IconRings size={16} />}
                        />

                        <Select
                          label="Religion"
                          placeholder="Select religion"
                          key={form.key("religion")}
                          {...form.getInputProps("religion")}
                          data={[
                            { value: "ISLAM", label: "Islam" },
                            { value: "KATOLIK", label: "Catholic" },
                            { value: "KRISTEN", label: "Christian" },
                            { value: "BUDHA", label: "Buddhism" },
                            { value: "HINDU", label: "Hindu" },
                          ]}
                          leftSection={<IconBook size={16} />}
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </Tabs.Panel>

              {/* Contact Information Tab */}
              <Tabs.Panel value="contact" pt="md">
                <div className="space-y-6">
                  <div className="bg-gray-50 dark:bg-neutral-800 p-6 rounded-lg">
                    <h3 className="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-6">
                      Contact Details
                    </h3>

                    <div className="space-y-4">
                      <TextInput
                        label="Phone Number"
                        placeholder="Enter phone number"
                        type="tel"
                        key={form.key("phone")}
                        {...form.getInputProps("phone")}
                        leftSection={<IconPhone size={16} />}
                      />

                      <Textarea
                        label="Address"
                        placeholder="Enter full address"
                        key={form.key("address")}
                        {...form.getInputProps("address")}
                        minRows={3}
                        leftSection={<IconHome size={16} />}
                      />
                    </div>
                  </div>
                </div>
              </Tabs.Panel>

              {/* Employment Details Tab */}
              <Tabs.Panel value="employment" pt="md">
                <div className="space-y-6">
                  <div className="bg-gray-50 dark:bg-neutral-800 p-6 rounded-lg">
                    <h3 className="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-6">
                      Employment Details
                    </h3>

                    <div className="space-y-4">
                      <Select
                        label="Company"
                        placeholder="Select company"
                        key={form.key("company_id")}
                        {...form.getInputProps("company_id")}
                        data={dataCompany}
                        leftSection={<IconBuildingEstate size={16} />}
                      />

                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <DatePickerInput
                          clearable
                          label="Join Date"
                          placeholder="Select join date"
                          valueFormat="YYYY-MM-DD"
                          key={form.key("join_date")}
                          {...form.getInputProps("join_date")}
                          leftSection={<IconCalendar size={16} />}
                        />

                        <TextInput
                          label="Employee Type"
                          placeholder="Enter employee type"
                          key={form.key("employee_type")}
                          {...form.getInputProps("employee_type")}
                          leftSection={<IconUser size={16} />}
                        />
                      </div>

                      <Select
                        label="Employee Status"
                        placeholder="Select employee status"
                        key={form.key("status")}
                        {...form.getInputProps("status")}
                        data={[
                          { value: "1", label: "Active" },
                          { value: "0", label: "Inactive" },
                        ]}
                        leftSection={<IconStatusChange size={16} />}
                      />
                    </div>
                  </div>
                </div>
              </Tabs.Panel>

              {/* Contract Tab */}
              <Tabs.Panel value="contract" pt="md">
                <div className="space-y-6">
                  <div className="bg-gray-50 dark:bg-neutral-800 p-6 rounded-lg">
                    <h3 className="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-6">
                      Contract Information
                    </h3>

                    <div className="space-y-4">
                      <Select
                        label="Contract Status"
                        placeholder="Select contract status"
                        key={form.key("contract_status")}
                        {...form.getInputProps("contract_status")}
                        data={[
                          { value: "ACTIVE", label: "Active" },
                          { value: "EXPIRED", label: "Expired" },
                          { value: "TERMINATED", label: "Terminated" },
                        ]}
                        leftSection={<IconFileText size={16} />}
                      />

                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <DatePickerInput
                          clearable
                          label="Contract Start Date"
                          placeholder="Select contract start date"
                          valueFormat="YYYY-MM-DD"
                          key={form.key("contract_start_date")}
                          {...form.getInputProps("contract_start_date")}
                          leftSection={<IconCalendar size={16} />}
                        />

                        <DatePickerInput
                          clearable
                          label="Contract End Date"
                          placeholder="Select contract end date"
                          valueFormat="YYYY-MM-DD"
                          key={form.key("contract_end_date")}
                          {...form.getInputProps("contract_end_date")}
                          leftSection={<IconCalendar size={16} />}
                        />
                      </div>

                      <DatePickerInput
                        clearable
                        label="Resign Date"
                        placeholder="Select resign date (if applicable)"
                        valueFormat="YYYY-MM-DD"
                        key={form.key("resign_date")}
                        {...form.getInputProps("resign_date")}
                        leftSection={<IconCalendar size={16} />}
                      />
                    </div>
                  </div>
                </div>
              </Tabs.Panel>
            </ScrollArea>
          </Tabs>

          {/* Action Buttons */}
          <div className="border-t border-gray-200 dark:border-gray-700 mt-6 pt-6 flex justify-end space-x-4">
            <Button
              variant="outline"
              color="gray"
              leftSection={<IconX size={16} />}
              className="px-6 py-2"
              onClick={handleCancel}
            >
              Cancel
            </Button>
            <Button
              type="submit"
              leftSection={<IconDeviceFloppy size={16} />}
              className="px-6 py-2"
            >
              Save Changes
            </Button>
          </div>
        </div>
      </div>
    </form>
  );
};
