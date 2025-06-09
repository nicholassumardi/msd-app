/* eslint-disable @typescript-eslint/no-explicit-any */
import { Autocomplete, Input, Textarea, TextInput, Title } from "@mantine/core";
import { IMaskInput } from "react-imask";
import { DatePickerInput } from "@mantine/dates";

const ContentUserData = ({ form }: any) => {
  return (
    <>
      <Title c="dimmed" fz="h4">
        Personal Data
      </Title>
      <div className="md:grid grid-cols-2 gap-3">
        <TextInput
          mt="md"
          label="Name"
          placeholder="please input name"
          key={form.key("name")}
          {...form.getInputProps("name")}
          withAsterisk
        ></TextInput>
        <Input.Wrapper label="Identinty Number (No. KTP)" mt="md" withAsterisk>
          <Input
            component={IMaskInput}
            mask="000000-000000-0000"
            placeholder="please input identity card number"
            key={form.key("identity_card")}
            {...form.getInputProps("identity_card")}
          ></Input>
        </Input.Wrapper>
        <DatePickerInput
          mt="md"
          label="Date of birth"
          placeholder="please input date of birth"
          key={form.key("date_of_birth")}
          {...form.getInputProps("date_of_birth")}
          valueFormat="YYYY-MM-DD"
          withAsterisk
        ></DatePickerInput>
      </div>
      <div className="md:grid grid-cols-2 gap-3">
        <Autocomplete
          mt="md"
          label="Gender"
          data={["Male", "Female"]}
          key={form.key("gender")}
          {...form.getInputProps("gender")}
        ></Autocomplete>
        <Autocomplete
          mt="md"
          label="Religion"
          key={form.key("religion")}
          {...form.getInputProps("religion")}
          data={["Islam", "Katolik", "Kristen", "Budha", "Hindu"]}
        ></Autocomplete>
        <Autocomplete
          mt="md"
          label="Education"
          key={form.key("education")}
          {...form.getInputProps("education")}
          data={[
            "TK",
            "SD",
            "SMP",
            "SMA",
            "Diploma 1 (D1)",
            "Diploma 2 (D2)",
            "Diploma 3 (D3)",
            "Diploma 4 (D4)",
            "Bachelor's Degree (S1)",
            "Master's degree (S2)",
            "Doctorate (S3)",
          ]}
          name="education"
        ></Autocomplete>
        <Autocomplete
          mt="md"
          label="Marital Status"
          key={form.key("marital_status")}
          {...form.getInputProps("marital_status")}
          data={["Menikah", "Belum Menikah", "Cerai"]}
          name="education"
        ></Autocomplete>
        <Input.Wrapper label="Phone number" mt="md">
          <Input
            component={IMaskInput}
            mask="+62 (000) 000-00-00"
            placeholder="please input identity card number"
            key={form.key("phone")}
            {...form.getInputProps("phone")}
          ></Input>
        </Input.Wrapper>
        <div className="col-span-2">
          <Textarea
            label="Address"
            placeholder="please input address here"
            autosize
            minRows={2}
            mt="md"
            key={form.key("address")}
            {...form.getInputProps("address")}
          ></Textarea>{" "}
        </div>
      </div>
    </>
  );
};

export default ContentUserData;
