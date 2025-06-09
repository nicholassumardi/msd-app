/* eslint-disable @typescript-eslint/no-explicit-any */
import React, { useEffect, useState } from "react";
import { useDisclosure } from "@mantine/hooks";
import {
  ActionIcon,
  Button,
  Modal,
  ScrollArea,
  Select,
  Text,
  Textarea,
  TextInput,
  Title,
} from "@mantine/core";
import { IconEditCircle, IconSearch } from "@tabler/icons-react";
import axios from "axios";
import SuccessNotification from "@/components/Notifications/SuccessNotification";
import ErrorNotification from "@/components/Notifications/ErrorNotification";
import { useForm } from "@mantine/form";
import { option } from "../../../../pages/types/option";
import { DatePickerInput } from "@mantine/dates";

interface FormComponent {
  id?: string;
  getData: () => Promise<void>;
  setIsLoading: React.Dispatch<React.SetStateAction<boolean>>;
  dataIkwRevision: option[];
  dataEmployee: option[];
}

const FormTraining: React.FC<FormComponent> = ({
  id,
  getData,
  setIsLoading,
  dataIkwRevision,
  dataEmployee,
}) => {
  const [mode, setMode] = useState("POST");
  const [error, setError] = useState<{ [key: string]: string }>({});
  const [opened, { open, close }] = useDisclosure(false);

  const statusActiveOptions = [
    { value: "1", label: "Active" },
    { value: "0", label: "Not Active" },
  ];

  const statusFAPrint = [
    { value: "1", label: "1" },
    { value: "0", label: "0" },
  ];

  const statusAssessment = [
    { value: "K", label: "K" },
    { value: "BK", label: "BK" },
    { value: "RK", label: "RK" },
  ];

  const statusOptions = [
    { value: "1", label: "Done" },
    { value: "0", label: "Not Done" },
  ];

  const form = useForm({
    initialValues: {
      no_training: "",
      trainee_id: "",
      trainer_id: "",
      assessor_id: "",
      ikw_revision_id: "",
      training_plan_date: null as Date | null,
      training_realisation_date: null as Date | null,
      training_duration: 0,
      ticket_return_date: null as Date | null,
      assessment_plan_date: null as Date | null,
      assessment_realisation_date: null as Date | null,
      assessment_duration: 0,
      status_fa_print: "",
      assessment_result: "",
      status: "",
      description: "",
      status_active: "",
    },
    validate: {
      // no_training: (value) =>
      //   !value ? "Training number cannot be empty" : null,
      // trainee_id: (value) => (value === null ? "Trainee is required" : null),
      // trainer_id: (value) => (value === null ? "Trainer is required" : null),
      // assessor_id: (value) => (value === null ? "Assessor is required" : null),
      // training_plan_date: (value) =>
      //   !value ? "Training plan date is required" : null,
      // training_realisation_date: (value) =>
      //   !value ? "Training realisation date is required" : null,
      // assessment_plan_date: (value) =>
      //   !value ? "Assessment plan date is required" : null,
      // assessment_realisation_date: (value) =>
      //   !value ? "Assessment realisation date is required" : null,
      // assessment_result: (value) =>
      //   !value ? "Assessment result cannot be empty" : null,
    },
  });

  const handleGetDataDetail = async () => {
    try {
      const response = await axios.get(`/api/admin/training/${id}?type=show`);
      form.setValues({
        no_training: response.data.data.data.no_training,
        trainee_id: response.data.data.data.trainee_id.toString(),
        trainer_id: response.data.data.data.trainer_id.toString(),
        assessor_id: response.data.data.data.assessor_id.toString(),
        ikw_revision_id: response.data.data.data.ikw_revision_id.toString(),
        training_plan_date: response.data.data.data.training_plan_date
          ? new Date(response.data.data.data.training_plan_date)
          : null,
        training_realisation_date: response.data.data.data
          .training_realisation_date
          ? new Date(response.data.data.data.training_realisation_date)
          : null,
        training_duration: response.data.data.data.training_duration,
        ticket_return_date: response.data.data.data.ticket_return_date
          ? new Date(response.data.data.data.ticket_return_date)
          : null,
        assessment_plan_date: response.data.data.data.assessment_plan_date
          ? new Date(response.data.data.data.assessment_plan_date)
          : null,
        assessment_realisation_date: response.data.data.data
          .assessment_realisation_date
          ? new Date(response.data.data.data.assessment_realisation_date)
          : null,
        assessment_duration: response.data.data.data.assessment_duration,
        status_fa_print: response.data.data.data.status_fa_print.toString(),
        assessment_result: response.data.data.data.assessment_result.toString(),
        status: response.data.data.data.status.toString(),
        description: response.data.data.data.description,
        status_active: response.data.data.data.status_active.toString(),
      });
    } catch (err: any) {
      if (err.response) {
        setError(err.response.data.message);
      }
    }
  };

  const handleCreateData = () => {
    open();
  };

  const handleEditData = () => {
    handleGetDataDetail();
    open();
  };

  useEffect(() => {
    const handleMode = () => {
      if (id) {
        setMode("PUT");
      } else {
        setMode("POST");
      }
    };
    handleMode();
  }, [id]);

  const handleCloseModal = () => {
    setError({});
    close();
  };

  const handleSubmit = async (values: any) => {
    console.log(values);
    try {
      if (mode === "PUT") {
        const response = await axios.put(
          `/api/admin/training/${id}?type=update`,
          values
        );
        if (response.status === 200) {
          SuccessNotification({
            title: "Success",
            message: "Training data successfully updated",
          });
          close();
        }
      } else {
        const response = await axios.post(
          "/api/admin/training?type=store",
          values
        );
        if (response.status === 201) {
          SuccessNotification({
            title: "Success",
            message: "Training data successfully created",
          });
          close();
        }
      }
      setIsLoading(true);
      setInterval(() => {
        getData();
      }, 1500);
    } catch (err: any) {
      if (err.response && err.response.status == 422) {
        setError(err.response.data.error);
      } else {
        ErrorNotification({
          title: "Server Error",
          message: "500 Internal Server Error",
        });
      }
    }
  };

  return (
    <>
      <Modal
        opened={opened}
        onClose={handleCloseModal}
        centered
        fullScreen
        size="lg"
        transitionProps={{ transition: "scale", duration: 350 }}
        title={
          <Text fw={700} size="xl">
            {mode === "PUT" ? "Edit Training" : "Create New Training"}
          </Text>
        }
      >
        <form
          onSubmit={form.onSubmit((values) => {
            handleSubmit(values);
          })}
        >
          <div>
            <ScrollArea h={620} offsetScrollbars>
              <Title c="dimmed" fz="h2" mt="xl" mb="md">
                Training Data (Trainee, Trainer, Assessor)
              </Title>
              <div className="md:grid grid-cols-2 gap-3 text-gray-500">
                <Select
                  label="Trainee"
                  placeholder="please select trainee"
                  mt="md"
                  fw={100}
                  size="md"
                  color="gray"
                  radius={12}
                  searchable
                  clearable
                  className="shadow-default"
                  leftSection={<IconSearch />}
                  key={form.key("trainee_id")}
                  {...form.getInputProps("trainee_id")}
                  data={dataEmployee}
                ></Select>
                <Select
                  withAsterisk
                  label="Choose Trainer"
                  placeholder="please select trainer"
                  mt="md"
                  radius={12}
                  fw={100}
                  size="md"
                  color="gray"
                  searchable
                  className="shadow-default"
                  clearable
                  leftSection={<IconSearch />}
                  key={form.key("trainer_id")}
                  {...form.getInputProps("trainer_id")}
                  data={dataEmployee}
                />
                <Select
                  withAsterisk
                  label="Choose Asessor"
                  placeholder="please select assessor"
                  mt="md"
                  radius={12}
                  fw={100}
                  size="md"
                  color="gray"
                  searchable
                  className="shadow-default"
                  clearable
                  leftSection={<IconSearch />}
                  key={form.key("assessor_id")}
                  {...form.getInputProps("assessor_id")}
                  data={dataEmployee}
                />
              </div>
              <Title c="dimmed" fz="h2" mt="xl" mb="md">
                IKW to train
              </Title>
              <div className="md:grid grid-cols-2 gap-3 text-gray-500">
                <Select
                  label="IKW"
                  placeholder="please select role code"
                  mt="md"
                  fw={100}
                  size="md"
                  color="gray"
                  radius={12}
                  searchable
                  clearable
                  className="shadow-default"
                  key={form.key("ikw_revision_id")}
                  {...form.getInputProps("ikw_revision_id")}
                  data={dataIkwRevision}
                ></Select>
              </div>
              <Title c="dimmed" fz="h2" mt="xl" mb="md">
                Training & Assessment Details
              </Title>
              <div className="md:grid grid-cols-2 gap-3 text-gray-500">
                <DatePickerInput
                  clearable
                  label="Training Plan Date"
                  size="md"
                  radius={12}
                  mt="md"
                  placeholder="Select training plan date"
                  valueFormat="YYYY-MM-DD"
                  key={form.key("training_plan_date")}
                  {...form.getInputProps("training_plan_date")}
                />

                <DatePickerInput
                  clearable
                  label="Training Realisation Date"
                  size="md"
                  radius={12}
                  mt="md"
                  placeholder="Select training realisation date"
                  valueFormat="YYYY-MM-DD"
                  key={form.key("training_realisation_date")}
                  {...form.getInputProps("training_realisation_date")}
                />

                <TextInput
                  label="Training Duration (minutes)"
                  size="md"
                  radius={12}
                  mt="md"
                  type="number"
                  withAsterisk
                  placeholder="Enter training duration"
                  className="shadow-default"
                  key={form.key("training_duration")}
                  {...form.getInputProps("training_duration")}
                />

                <DatePickerInput
                  clearable
                  label="Ticket Return Date"
                  size="md"
                  radius={12}
                  mt="md"
                  placeholder="Select ticket return date"
                  valueFormat="YYYY-MM-DD"
                  key={form.key("ticket_return_date")}
                  {...form.getInputProps("ticket_return_date")}
                />

                <DatePickerInput
                  clearable
                  label="Assessment Plan Date"
                  size="md"
                  radius={12}
                  mt="md"
                  placeholder="Select assessment plan date"
                  valueFormat="YYYY-MM-DD"
                  key={form.key("assessment_plan_date")}
                  {...form.getInputProps("assessment_plan_date")}
                />

                <DatePickerInput
                  clearable
                  label="Assessment Realisation Date"
                  size="md"
                  radius={12}
                  mt="md"
                  placeholder="Select assessment realisation date"
                  valueFormat="YYYY-MM-DD"
                  key={form.key("assessment_realisation_date")}
                  {...form.getInputProps("assessment_realisation_date")}
                />

                <TextInput
                  label="Assessment Duration (minutes)"
                  size="md"
                  radius={12}
                  mt="md"
                  type="number"
                  withAsterisk
                  placeholder="Enter assessment duration"
                  className="shadow-default"
                  key={form.key("assessment_duration")}
                  {...form.getInputProps("assessment_duration")}
                />
              </div>
              <Title c="dimmed" fz="h2" mt="xl" mb="md">
                Result & Status Print
              </Title>
              <div className="md:grid grid-cols-2 gap-3 text-gray-500">
                <Select
                  label="Status FA Print"
                  mt="md"
                  radius={12}
                  size="md"
                  color="gray"
                  className="shadow-default"
                  clearable
                  key={form.key("status_fa_print")}
                  {...form.getInputProps("status_fa_print")}
                  data={statusFAPrint}
                />
                <Select
                  label="Result"
                  mt="md"
                  radius={12}
                  size="md"
                  color="gray"
                  className="shadow-default"
                  clearable
                  key={form.key("assessment_result")}
                  {...form.getInputProps("assessment_result")}
                  data={statusAssessment}
                />
                <Select
                  label="Status"
                  mt="md"
                  radius={12}
                  size="md"
                  color="gray"
                  className="shadow-default"
                  clearable
                  key={form.key("status")}
                  {...form.getInputProps("status")}
                  data={statusOptions}
                />
                <Select
                  label="Status Active"
                  mt="md"
                  radius={12}
                  size="md"
                  color="gray"
                  className="shadow-default"
                  clearable
                  key={form.key("status_active")}
                  {...form.getInputProps("status_active")}
                  data={statusActiveOptions}
                />

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
            </ScrollArea>
          </div>
          <Modal.Header
            pos={"sticky"}
            bottom={0}
            className="flex place-self-end gap-2"
          >
            <Button
              variant="default"
              color="white"
              size="lg"
              radius={12}
              onClick={handleCloseModal}
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
              Save
            </Button>
          </Modal.Header>
        </form>
      </Modal>
      {mode === "PUT" ? (
        <ActionIcon
          variant="transparent"
          onClick={handleEditData}
          color="green"
          title="Edit"
        >
          <IconEditCircle />
        </ActionIcon>
      ) : (
        <Button
          className="shadow-md"
          size="sm"
          variant="filled"
          color="violet"
          radius={9}
          onClick={handleCreateData}
        >
          <Text className="font-satoshi" size="sm">
            Add New
          </Text>
        </Button>
      )}
    </>
  );
};

export default FormTraining;
