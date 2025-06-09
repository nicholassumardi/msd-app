/* eslint-disable @typescript-eslint/no-explicit-any */
import { Select, Textarea, TextInput, Title } from "@mantine/core";
import { DatePickerInput } from "@mantine/dates";
import { IconSearch } from "@tabler/icons-react";
import { option } from "../../../../../pages/types/option";

const IkwForm = ({
  form,
  dataJobTask,
  dataDepartment,
}: {
  form: any;
  dataJobTask: option[];
  dataDepartment: option[];
}) => {
  const statusDocument = [
    { value: "DRAFT IKW CLOSE", label: "DRAFT IKW CLOSE" },
    { value: "IKW FINISH", label: "IKW FINISH" },
    { value: "IKW TERDAFTAR", label: "IKW TERDAFTAR" },
    { value: "ON - PROGRESS", label: "ON - PROGRESS" },
  ];
  return (
    <>
      <Title c="dimmed" fz="h4" mt="xl" mb="md">
        IKW Data
      </Title>
      <div className="md:grid grid-cols-2 gap-3 text-gray-500">
        <Select
          label="Job Task"
          placeholder="please select role code"
          mt="md"
          fw={100}
          size="md"
          color="gray"
          radius={12}
          searchable
          clearable
          className="shadow-default"
          key={form.key("job_task_id")}
          {...form.getInputProps("job_task_id")}
          data={dataJobTask}
        ></Select>
        <Select
          withAsterisk
          label="Choose Department"
          mt="md"
          radius={12}
          fw={100}
          size="md"
          color="gray"
          searchable
          className="shadow-default"
          clearable
          leftSection={<IconSearch />}
          key={form.key("department_id")}
          {...form.getInputProps("department_id")}
          data={dataDepartment}
        />
        <TextInput
          label="IKW Name"
          size="md"
          radius={12}
          mt="md"
          withAsterisk
          placeholder="please type role name"
          className="shadow-default"
          key={form.key("name")}
          {...form.getInputProps("name")}
        ></TextInput>
        <TextInput
          label="CODE IKW"
          size="md"
          radius={12}
          mt="md"
          withAsterisk
          placeholder="please type role code"
          className="shadow-default"
          key={form.key("code")}
          {...form.getInputProps("code")}
        ></TextInput>
        <TextInput
          label="Total Page"
          size="md"
          radius={12}
          mt="md"
          type="number"
          withAsterisk
          placeholder="please type total page"
          className="shadow-default"
          key={form.key("total_page")}
          {...form.getInputProps("total_page")}
        ></TextInput>
      </div>
      <Title c="dimmed" fz="h4" mt="xl" mb="md">
        IKW Date
      </Title>
      <div className="md:grid grid-cols-2 gap-3 text-gray-500">
        <DatePickerInput
          clearable
          label="Date register"
          name="registration_date"
          size="md"
          radius={12}
          mt="md"
          placeholder="please input date register"
          valueFormat="YYYY-MM-DD"
          key={form.key("registration_date")}
          {...form.getInputProps("registration_date")}
        ></DatePickerInput>
        <DatePickerInput
          clearable
          label="Back Office Print Date"
          name="print_by_back_office_date"
          size="md"
          radius={12}
          mt="md"
          placeholder="please input date"
          valueFormat="YYYY-MM-DD"
          key={form.key("print_by_back_office_date")}
          {...form.getInputProps("print_by_back_office_date")}
        ></DatePickerInput>
        <DatePickerInput
          clearable
          label="Submit to department date"
          name="submit_to_department_date"
          size="md"
          radius={12}
          mt="md"
          placeholder="please input date register"
          valueFormat="YYYY-MM-DD"
          key={form.key("submit_to_department_date")}
          {...form.getInputProps("submit_to_department_date")}
        ></DatePickerInput>
        <DatePickerInput
          clearable
          label="IKW Return Date"
          name="ikw_return_date"
          size="md"
          radius={12}
          mt="md"
          placeholder="please input date register"
          valueFormat="YYYY-MM-DD"
          key={form.key("ikw_return_date")}
          {...form.getInputProps("ikw_return_date")}
        ></DatePickerInput>
      </div>
      <Title c="dimmed" fz="h4" mt="xl" mb="md">
        IKW Duration, Status, & Description
      </Title>
      <div className="md:grid grid-cols-2 gap-3 text-gray-500">
        <TextInput
          label="IKW Create Duration"
          size="md"
          radius={12}
          mt="md"
          type="number"
          placeholder="please type total page"
          className="shadow-default"
          key={form.key("ikw_creation_duration")}
          {...form.getInputProps("ikw_creation_duration")}
        ></TextInput>
        <Select
          label="Document Status"
          mt="md"
          fw={100}
          size="md"
          color="gray"
          radius={12}
          searchable
          clearable
          className="shadow-default"
          key={form.key("status_document")}
          {...form.getInputProps("status_document")}
          data={statusDocument}
        ></Select>
        <DatePickerInput
          clearable
          label="Last Update Document"
          name="last_update_date"
          size="md"
          radius={12}
          mt="md"
          placeholder="please input date register"
          valueFormat="YYYY-MM-DD"
          key={form.key("last_update_date")}
          {...form.getInputProps("last_update_date")}
        ></DatePickerInput>
        <Textarea
          label="Description"
          placeholder="please input description here"
          autosize
          radius={12}
          minRows={2}
          mt="md"
          key={form.key("description")}
          {...form.getInputProps("description")}
        ></Textarea>
      </div>
    </>
  );
};

export default IkwForm;
