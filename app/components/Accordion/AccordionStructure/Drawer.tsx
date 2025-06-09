/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";
import "@mantine/core/styles.css";
import "@mantine/dates/styles.css";

import { Button, Drawer, Select, Text, TextInput } from "@mantine/core";
import {
  IconCalendarFilled,
  IconLayersIntersect,
  IconSearch,
} from "@tabler/icons-react";
import { DatePickerInput } from "@mantine/dates";
import { option } from "../../../../pages/types/option";
import { useForm } from "@mantine/form";
import axios from "axios";
import ErrorNotification from "@/components/Notifications/ErrorNotification";
import SuccessNotification from "@/components/Notifications/SuccessNotification";
import Structure from "@/structure/page";

interface ChildComponenetProps {
  openedDrawer: boolean;
  closeDrawer: any;
  dataEmployee: option[];
  form: any;
  value: string | null;
  getDataStructureChange: (value: any) => Promise<void>;
}

const DrawerStructure: React.FC<ChildComponenetProps> = ({
  openedDrawer,
  closeDrawer,
  dataEmployee,
  form,
  value,
  getDataStructureChange,
}) => {
  // form.setValues({ user_structure_mapping_id: structureMappingId });
  // const handleGetDataDetail = async (uuid: string | null) => {
  //   try {
  //     const response = await axios.get(`/api/admin/employee/${uuid}?type=show`);
  //     setEmployee(response.data.data.data.companies);
  //   } catch (err: any) {
  //     if (err.response) {
  //     }
  //   }
  // };

  // const generateCode = (
  //   companyUniqueCode: string,
  //   idStructure: string,
  //   idStaff: string,
  //   group: string
  // ): string => {
  //   const combinedValues = [
  //     ...companyUniqueCode,
  //     idStructure,
  //     idStaff,
  //     group,
  //   ].join("");
  //   return combinedValues;
  // };

  // const setValuePositionCode = () => {
  //   const companyUniqueCode = employee?.unique_code ?? "";
  //   const idStructure = form.getValues().id_structure;
  //   const idStaff = form.getValues().id_staff;
  //   const group = form.getValues().group;

  //   const positionCode = generateCode(
  //     companyUniqueCode,
  //     idStructure,
  //     idStaff,
  //     group
  //   );

  //   form.setValues({
  //     position_code_structure: positionCode,
  //   });
  // };

  const handleSubmit = async (values: any) => {
    try {
      const response = await axios.post(
        `/api/admin/structure?type=storeStructure`,
        values
      );
      if (response.status === 201) {
        SuccessNotification({
          title: "Success",
          message: "Structure data successfully added",
        });
        getDataStructureChange(value);
        closeDrawer();
      }
    } catch (err: any) {
      ErrorNotification({
        title: "Server Error",
        message: err.response.data.error,
      });
      closeDrawer();
    }
  };
  return (
    <Drawer
      offset={10}
      position="right"
      radius="md"
      size="lg"
      opened={openedDrawer}
      onClose={closeDrawer}
      transitionProps={{ transition: "skew-up" }}
      title={
        <>
          <Text size="xl" fw={700} className="font-satoshi">
            <div className="flex gap-2 items-center">
              <IconLayersIntersect size={35} />
              Assign new role
            </div>
          </Text>
          <Text size="md" fw={200} c="dimmed" className="font-satoshi">
            <div className="ml-11 items-center">Assigning role to employee</div>
          </Text>
        </>
      }
    >
      <form onSubmit={form.onSubmit((values: any) => handleSubmit(values))}>
        <div className="grid grid-cols-2 gap-3 font-satoshi text-gray-500 ">
          <div className="col-span-2">
            <Select
              withAsterisk
              fw={100}
              label="Choose employee"
              size="md"
              color="gray"
              radius={12}
              searchable
              clearable
              limit={10}
              leftSection={<IconSearch />}
              className="shadow-default"
              key={form.key("uuid")}
              {...form.getInputProps("uuid")}
              data={dataEmployee}
            />
          </div>
          <TextInput
            label="ID Structure"
            size="md"
            radius={12}
            placeholder="ID Structure"
            withAsterisk
            key={form.key("id_structure")}
            {...form.getInputProps("id_structure")}
            className="shadow-default"
          ></TextInput>
          <TextInput
            label="ID Staff"
            size="md"
            radius={12}
            placeholder="ID Staff"
            withAsterisk
            key={form.key("id_staff")}
            {...form.getInputProps("id_staff")}
            className="shadow-default"
          ></TextInput>
          <TextInput
            label="Group"
            size="md"
            radius={12}
            placeholder="Group"
            withAsterisk
            key={form.key("group")}
            {...form.getInputProps("group")}
            className="shadow-default"
          ></TextInput>
          <TextInput
            label="Position Code"
            size="md"
            radius={12}
            placeholder="Position code"
            withAsterisk
            key={form.key("position_code_structure")}
            {...form.getInputProps("position_code_structure")}
            className="shadow-default"
          ></TextInput>
          <DatePickerInput
            label="Assign Date"
            placeholder="Select date"
            size="md"
            radius={12}
            leftSection={<IconCalendarFilled />}
            withAsterisk
            key={form.key("assign_date")}
            {...form.getInputProps("assign_date")}
            valueFormat="YYYY-MM-DD"
            className="shadow-default"
          ></DatePickerInput>
        </div>
        <div className="md:flex md:flex-row md:justify-end  md:absolute md:bottom-5 md:right-5 gap-2">
          <Button
            variant="default"
            color="white"
            size="lg"
            radius={12}
            onClick={closeDrawer}
          >
            Close
          </Button>
          <Button
            variant="filled"
            color="violet"
            size="lg"
            type="submit"
            radius={12}
          >
            Save changes
          </Button>
        </div>
      </form>
    </Drawer>
  );
};

export default DrawerStructure;
