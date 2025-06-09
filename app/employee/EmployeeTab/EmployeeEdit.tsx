/* eslint-disable @typescript-eslint/no-explicit-any */
import {
  ScrollArea,
  Button,
  Tabs,
  Select,
  TextInput,
  Textarea,
  MultiSelect,
} from "@mantine/core";
import {
  IconUser,
  IconBriefcase,
  IconPhone,
  IconHome,
  IconFileText,
  IconX,
  IconDeviceFloppy,
  IconRings,
  IconBook,
  IconCalendar,
  IconBuildingEstate,
  IconStatusChange,
  IconCertificate,
  IconCertificate2,
} from "@tabler/icons-react";
import { DatePickerInput } from "@mantine/dates";
import { option } from "../../../pages/types/option";
import useEmployeeData from "../../../hooks/EmployeeCentre";
import { useEmployeeDataContext } from "../../../context/EmployeeCentre";

type editPageForm = {
  form: any;
  setIsTimelineOpen: React.Dispatch<React.SetStateAction<boolean>>;
  dataCompany?: option[];
};

export const PersonalInformation: React.FC<editPageForm> = ({
  form,
  setIsTimelineOpen,
}) => {
  const { handleEditEmployee } = useEmployeeData();
  const { UUID } = useEmployeeDataContext();

  return (
    <form
      onSubmit={form.onSubmit((values: any) =>
        handleEditEmployee(values, UUID)
      )}
    >
      <div className="max-w-6xl  bg-white dark:bg-neutral-900 font-sans rounded-lg shadow-md font-satoshi">
        <div className="p-3">
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
            </Tabs.List>

            <ScrollArea className="h-auto">
              <Tabs.Panel value="personal" pt="md">
                <div className="space-y-6">
                  <div className="bg-gray-50 dark:bg-neutral-800 p-4 rounded-lg">
                    <h3 className="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">
                      Basic Information
                    </h3>

                    <TextInput
                      label="Full Name"
                      placeholder="Enter full name"
                      required
                      leftSection={<IconUser size={16} />}
                      key={form.key("name")}
                      {...form.getInputProps("name")}
                      className="mb-4"
                    />

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                      <TextInput
                        label="ID Staff"
                        placeholder="Staff ID"
                        key={form.key("staff_id")}
                        {...form.getInputProps("staff_id")}
                        leftSection={<IconBriefcase size={16} />}
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
                  </div>

                  <div className="bg-gray-50 dark:bg-neutral-800 p-4 rounded-lg">
                    <h3 className="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">
                      Additional Information
                    </h3>

                    <TextInput
                      label="NIK (Identity Card)"
                      placeholder="Enter identity card number"
                      leftSection={<IconFileText size={16} />}
                      key={form.key("identity_card")}
                      {...form.getInputProps("identity_card")}
                      className="mb-4"
                    />

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <Select
                        label="Marital Status"
                        placeholder="Select status"
                        key={form.key("marital_status")}
                        {...form.getInputProps("marital_status")}
                        data={[
                          { value: "MENIKAH", label: "MENIKAH" },
                          { value: "BELUM MENIKAH", label: "BELUM MENIKAH" },
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
                          { value: "KATOLIK", label: "Katolik" },
                          { value: "KRISTEN", label: "Kristen" },
                          { value: "BUDHA", label: "Budha" },
                          { value: "HINDU", label: "Hindu" },
                        ]}
                        leftSection={<IconBook size={16} />}
                      />
                    </div>
                  </div>
                </div>
              </Tabs.Panel>

              <Tabs.Panel value="contact" pt="md">
                <div className="space-y-6">
                  <div className="bg-gray-50 dark:bg-neutral-800 p-4 rounded-lg">
                    <h3 className="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">
                      Contact Details
                    </h3>

                    <TextInput
                      label="Phone Number"
                      placeholder="Enter phone number"
                      type="tel"
                      key={form.key("phone")}
                      {...form.getInputProps("phone")}
                      leftSection={<IconPhone size={16} />}
                      className="mb-4"
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
              </Tabs.Panel>
            </ScrollArea>
          </Tabs>
          <div className="border-t border-gray-200 dark:border-gray-700 mt-6 pt-6 flex justify-end space-x-4">
            <Button
              variant="outline"
              color="gray"
              leftSection={<IconX size={16} />}
              className="px-4"
              onClick={() => {
                setIsTimelineOpen(false);
                form.reset();
              }}
            >
              Cancel
            </Button>
            <Button
              type="submit"
              leftSection={<IconDeviceFloppy size={16} />}
              className="px-6"
            >
              Save Changes
            </Button>
          </div>{" "}
        </div>
      </div>
    </form>
  );
};

export const EmployeementDetail: React.FC<editPageForm> = ({
  form,
  setIsTimelineOpen,
  dataCompany,
}) => {
  const { handleEditEmployee } = useEmployeeData();
  const { UUID } = useEmployeeDataContext();
  return (
    <>
      <form
        onSubmit={form.onSubmit((values: any) =>
          handleEditEmployee(values, UUID)
        )}
      >
        <div className="max-w-6xl  bg-white dark:bg-neutral-900 font-sans rounded-lg shadow-md font-satoshi">
          <div className="p-3">
            <ScrollArea className="h-auto">
              <div className="space-y-6">
                <div className="bg-gray-50 dark:bg-neutral-800 p-4 rounded-lg">
                  <h3 className="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">
                    Employment Details
                  </h3>
                  <Select
                    label="Company"
                    placeholder="Select company"
                    key={form.key("company_id")}
                    {...form.getInputProps("company_id")}
                    data={dataCompany}
                    leftSection={<IconBuildingEstate size={16} />}
                    className="mb-4"
                  />
                  <DatePickerInput
                    clearable
                    label="Join Date"
                    placeholder="Select join date"
                    valueFormat="YYYY-MM-DD"
                    key={form.key("join_date")}
                    {...form.getInputProps("join_date")}
                    className="mb-4"
                    leftSection={<IconCalendar size={16} />}
                  />
                  <TextInput
                    label="Employee Type"
                    placeholder="Enter Employee Type"
                    key={form.key("employee_type")}
                    {...form.getInputProps("employee_type")}
                    leftSection={<IconUser size={16} />}
                    className="mb-4"
                  />
                  <Select
                    label="Employee Status"
                    placeholder="Select Employee Status"
                    key={form.key("status")}
                    {...form.getInputProps("status")}
                    data={[
                      { value: "1", label: "Aktif" },
                      { value: "0", label: "Non Aktif" },
                    ]}
                    leftSection={<IconStatusChange size={16} />}
                  />
                </div>
              </div>
            </ScrollArea>
            <div className="border-t border-gray-200 dark:border-gray-700 mt-6 pt-6 flex justify-end space-x-4">
              <Button
                variant="outline"
                color="gray"
                leftSection={<IconX size={16} />}
                className="px-4"
                onClick={() => {
                  setIsTimelineOpen(false);
                  form.reset();
                }}
              >
                Cancel
              </Button>
              <Button
                type="submit"
                leftSection={<IconDeviceFloppy size={16} />}
                className="px-6"
              >
                Save Changes
              </Button>
            </div>{" "}
          </div>
        </div>
      </form>
    </>
  );
};

// export const EmployeementHistory: React.FC<editPageForm> = async ({
//   form,
//   setIsTimelineOpen,
// }) => {
//   return <>
// <form action="" onSubmit={form.onSubmit((values:any) => handleEditEmployee(values, UUID))}></form></>;
// };

// export const OrganizationalStructure: React.FC<editPageForm> = async ({
//   form,
//   setIsTimelineOpen,
// }) => {
//   return <>
// <form action="" onSubmit={form.onSubmit((values:any) => handleEditEmployee(values, UUID))}></form></>;
// };

export const Contract: React.FC<editPageForm> = ({
  form,
  setIsTimelineOpen,
}) => {
  const { handleEditEmployee } = useEmployeeData();
  const { UUID } = useEmployeeDataContext();
  return (
    <>
      <form
        onSubmit={form.onSubmit((values: any) =>
          handleEditEmployee(values, UUID)
        )}
      ></form>
      <div className="max-w-6xl  bg-white dark:bg-neutral-900 font-sans rounded-lg shadow-md font-satoshi">
        <div className="p-3">
          <ScrollArea className="h-auto">
            <div className="space-y-6">
              <div className="bg-gray-50 dark:bg-neutral-800 p-4 rounded-lg">
                <h3 className="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">
                  Contract
                </h3>
                <Select
                  label="Status Contract"
                  placeholder="Select status contract"
                  data={[
                    { value: "Male", label: "Male" },
                    { value: "Female", label: "Female" },
                  ]}
                  leftSection={<IconUser size={16} />}
                  className="mb-4"
                />

                <DatePickerInput
                  clearable
                  label="Resign Date"
                  placeholder="Select resign date"
                  valueFormat="YYYY-MM-DD"
                  leftSection={<IconCalendar size={16} />}
                />
              </div>
            </div>
          </ScrollArea>
          <div className="border-t border-gray-200 dark:border-gray-700 mt-6 pt-6 flex justify-end space-x-4">
            <Button
              variant="outline"
              color="gray"
              leftSection={<IconX size={16} />}
              className="px-4"
              onClick={() => {
                setIsTimelineOpen(false);
                form.reset();
              }}
            >
              Cancel
            </Button>
            <Button
              type="submit"
              leftSection={<IconDeviceFloppy size={16} />}
              className="px-6"
            >
              Save Changes
            </Button>
          </div>{" "}
        </div>
      </div>
    </>
  );
};

export const Skills: React.FC<editPageForm> = ({ form, setIsTimelineOpen }) => {
  const { handleEditEmployee } = useEmployeeData();
  const { UUID } = useEmployeeDataContext();
  return (
    <>
      <form
        action=""
        onSubmit={form.onSubmit((values: any) =>
          handleEditEmployee(values, UUID)
        )}
      >
        <div className="max-w-6xl  bg-white dark:bg-neutral-900 font-sans rounded-lg shadow-md font-satoshi">
          <div className="p-3">
            <ScrollArea className="h-auto">
              <div className="space-y-6">
                <div className="bg-gray-50 dark:bg-neutral-800 p-4 rounded-lg">
                  <h3 className="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">
                    Skills & Certifications
                  </h3>
                  <MultiSelect
                    label="IKW"
                    placeholder="Select IKW"
                    data={[
                      { value: "Male", label: "Male" },
                      { value: "Female", label: "Female" },
                      { value: "Other", label: "Other" },
                    ]}
                    leftSection={<IconCertificate size={16} />}
                    className="mb-4"
                    key={form.key("ikws")}
                    {...form.getInputProps("ikws")}
                  />
                  <MultiSelect
                    label="Certification"
                    placeholder="Select Certification"
                    data={[
                      { value: "Male", label: "Male" },
                      { value: "Female", label: "Female" },
                      { value: "Other", label: "Other" },
                    ]}
                    leftSection={<IconCertificate2 size={16} />}
                    className="mb-4"
                    key={form.key("certificates")}
                    {...form.getInputProps("certificates")}
                  />
                </div>
              </div>
            </ScrollArea>
            <div className="border-t border-gray-200 dark:border-gray-700 mt-6 pt-6 flex justify-end space-x-4">
              <Button
                variant="outline"
                color="gray"
                leftSection={<IconX size={16} />}
                className="px-4"
                onClick={() => {
                  setIsTimelineOpen(false);
                  form.reset();
                }}
              >
                Cancel
              </Button>
              <Button
                type="submit"
                leftSection={<IconDeviceFloppy size={16} />}
                className="px-6"
              >
                Save Changes
              </Button>
            </div>{" "}
          </div>
        </div>
      </form>
    </>
  );
};
