/* eslint-disable @typescript-eslint/no-explicit-any */
import {
  ActionIcon,
  Button,
  Select,
  Textarea,
  TextInput,
  Title,
} from "@mantine/core";
import { DatePickerInput } from "@mantine/dates";
import { IconArrowBack, IconPlus, IconTrash } from "@tabler/icons-react";
import { RevisionForm } from "./Form";
import React from "react";
import MeetingForm from "./MeetingForm";
import PositionCallForm from "./PositionCall";

type IKWRevisionFormProps = {
  form: any;
  forms: RevisionForm[];
  setForms: React.Dispatch<React.SetStateAction<RevisionForm[]>>;
  nextRevisionNumber: number;
  setNextRevisionNumber: React.Dispatch<React.SetStateAction<number>>;
};

const IKWRevisionForm: React.FC<IKWRevisionFormProps> = ({
  form,
  forms,
  nextRevisionNumber,
  setNextRevisionNumber,
  setForms,
}) => {
  const createRevisionForm = (revisionNumber: number): RevisionForm => ({
    id: Math.random().toString(36).substr(2, 9),
    visible: true,
    revisionNumber,
    values: {
      revision_no: revisionNumber,
      reason: "",
      process_status: "",
      ikw_fix_status: "",
      confirmation: "",
      change_description: "",
      submission_no: "",
      submission_received_date: null as Date | null,
      submission_mr_date: null as Date | null,
      backoffice_return_date: null as Date | null,
      revision_status: "",
      print_date: null as Date | null,
      handover_date: null as Date | null,
      signature_mr_date: null as Date | null,
      distribution_date: null as Date | null,
      document_return_date: null as Date | null,
      document_disposal_date: null as Date | null,
      document_location_description: "",
      revision_description: "",
      status_check: "",
      position_calls: [],
      meeting_contents: [],
    },
  });

  const addForm = () => {
    const newRevisionNumber = nextRevisionNumber;
    const newRevision = createRevisionForm(newRevisionNumber);

    setForms((prev) => [...prev, newRevision]);
    setNextRevisionNumber((prev) => prev + 1);

    form.setFieldValue("revisions", [
      ...form.values.revisions,
      newRevision.values,
    ]);
  };

  const updateForm = (id: string, values: RevisionForm["values"]) => {
    setForms((prev) =>
      prev.map((rev) => (rev.id === id ? { ...rev, values } : rev))
    );

    const index = forms.findIndex((rev) => rev.id === id);
    if (index !== -1) {
      form.setFieldValue(`revisions.${index}`, values);
    }
  };

  const handleReturn = (id: string) => {
    setForms((prev) =>
      prev.map((rev) => (rev.id === id ? { ...rev, visible: false } : rev))
    );
  };

  const deleteForm = (id: string) => {
    setForms((prevForms) => prevForms.filter((form) => form.id !== id));
    setNextRevisionNumber((prev) => prev - 1);
  };
  const processStatus = [
    { value: "1", label: "DONE" },
    { value: "2", label: "FOD - PENGAJUAN" },
    { value: "3", label: "FU-LO" },
    { value: "4", label: "ON - PROGRESS" },
  ];

  const ikwFixStatus = [
    { value: "1", label: "MAJOR" },
    { value: "2", label: "MINOR" },
    { value: "3", label: "HAPUS" },
    { value: "4", label: "ON - PROGRESS" },
  ];

  const confirmation = [
    { value: "1", label: "Hapus" },
    { value: "2", label: "Rev" },
  ];

  const revisionStatus = [
    { value: "1", label: "MAJOR" },
    { value: "2", label: "MINOR" },
    { value: "3", label: "HAPUS" },
    { value: "4", label: "ON - PROGRESS" },
  ];

  const statusCheck = [
    { value: "1", label: "TRUE" },
    { value: "0", label: "FALSE" },
  ];

  return (
    <>
      {forms.every((form) => !form.visible) && (
        <div className="mb-4">
          <Button
            variant="outline"
            mt="lg"
            size="xl"
            radius={12}
            onClick={addForm}
          >
            <IconPlus /> Add New
          </Button>

          {forms.length > 0 && (
            <div className="mt-4">
              <div className="flex gap-2 flex-wrap">
                {forms.map((form, index) => (
                  <Button
                    key={form.id}
                    variant="outline"
                    color="gray"
                    size="xl"
                    onClick={() =>
                      setForms((prev) =>
                        prev.map((f) =>
                          f.id === form.id ? { ...f, visible: true } : f
                        )
                      )
                    }
                  >
                    Revision {form.revisionNumber.toString().padStart(2, "0")}
                    {index === forms.length - 1 && (
                      <ActionIcon
                        variant="transparent"
                        color="red"
                        onClick={() => deleteForm(form.id)}
                        title="Delete Revision"
                      >
                        <IconTrash size={20} />
                      </ActionIcon>
                    )}
                  </Button>
                ))}
              </div>
            </div>
          )}
        </div>
      )}
      {forms.map(
        (formData) =>
          formData.visible && (
            <div key={formData.id}>
              <div className="md:grid grid-cols-[auto_1fr] gap-3 text-gray-500 items-center">
                <Title c="dimmed" fz="h1" mt="xl" mb="md">
                  Revision No{" "}
                  {formData.revisionNumber.toString().padStart(2, "0")}
                </Title>
                <div className="justify-self-end">
                  <Button
                    variant="outline"
                    mt="xl"
                    radius={12}
                    onClick={() => handleReturn(formData.id)}
                  >
                    <IconArrowBack />
                    Return
                  </Button>
                </div>
              </div>

              <div className="md:grid grid-cols-2 gap-3 text-gray-500">
                {/* Reason */}
                <Textarea
                  label="Reason"
                  placeholder="please input reason here"
                  autosize
                  radius={12}
                  minRows={2}
                  mt="md"
                  value={formData.values.reason}
                  onChange={(e) =>
                    updateForm(formData.id, {
                      ...formData.values,
                      reason: e.target.value,
                    })
                  }
                />

                {/* Status IKW (Pending/Cancel) */}
                <Select
                  withAsterisk
                  label="Status IKW (Pending/ Cancel)"
                  mt="md"
                  radius={12}
                  fw={100}
                  size="md"
                  color="gray"
                  searchable
                  className="shadow-default"
                  clearable
                  value={formData.values.process_status}
                  onChange={(value) =>
                    updateForm(formData.id, {
                      ...formData.values,
                      process_status: value || "",
                    })
                  }
                  data={processStatus}
                />

                {/* Status IKW FIX */}
                <Select
                  withAsterisk
                  label="Status IKW FIX"
                  mt="md"
                  radius={12}
                  fw={100}
                  size="md"
                  color="gray"
                  searchable
                  className="shadow-default"
                  clearable
                  value={formData.values.ikw_fix_status}
                  onChange={(value) =>
                    updateForm(formData.id, {
                      ...formData.values,
                      ikw_fix_status: value || "",
                    })
                  }
                  data={ikwFixStatus}
                />

                {/* Confirmation */}
                <Select
                  withAsterisk
                  label="Confirmation"
                  mt="md"
                  radius={12}
                  fw={100}
                  size="md"
                  color="gray"
                  searchable
                  className="shadow-default"
                  clearable
                  value={formData.values.confirmation}
                  onChange={(value) =>
                    updateForm(formData.id, {
                      ...formData.values,
                      confirmation: value || "",
                    })
                  }
                  data={confirmation}
                />

                {/* Description of Cancellation, Revision/Deletion/Special */}
                <Textarea
                  label="Description of Cancellation, Revision/ Deletion / Special"
                  placeholder="please input description here"
                  autosize
                  radius={12}
                  minRows={2}
                  mt="md"
                  value={formData.values.change_description}
                  onChange={(e) =>
                    updateForm(formData.id, {
                      ...formData.values,
                      change_description: e.target.value,
                    })
                  }
                />

                {/* Submission No */}
                <TextInput
                  label="Submission No"
                  size="md"
                  radius={12}
                  mt="md"
                  type="number"
                  withAsterisk
                  placeholder="please type total page"
                  className="shadow-default"
                  value={formData.values.submission_no}
                  onChange={(e) =>
                    updateForm(formData.id, {
                      ...formData.values,
                      submission_no: e.target.value,
                    })
                  }
                />

                {/* Major/Minor */}
                <Select
                  withAsterisk
                  label="Major/ Minor"
                  mt="md"
                  radius={12}
                  fw={100}
                  size="md"
                  color="gray"
                  searchable
                  className="shadow-default"
                  clearable
                  value={formData.values.revision_status}
                  onChange={(value) =>
                    updateForm(formData.id, {
                      ...formData.values,
                      revision_status: value || "",
                    })
                  }
                  data={revisionStatus}
                />
              </div>

              {/* Revision Submission and Processing Timeline */}
              <Title c="dimmed" fz="h2" mt="xl" mb="md">
                Revision Submission and Processing Timeline
              </Title>
              <div className="md:grid grid-cols-2 gap-3 text-gray-500">
                {/* Submission Received Date */}
                <DatePickerInput
                  clearable
                  label="Date Received Submission"
                  name="submission_received_date"
                  size="md"
                  radius={12}
                  mt="md"
                  placeholder="please input date"
                  valueFormat="YYYY-MM-DD"
                  value={formData.values.submission_received_date}
                  onChange={(value) =>
                    updateForm(formData.id, {
                      ...formData.values,
                      submission_received_date: value,
                    })
                  }
                />

                {/* Submission MR Date */}
                <DatePickerInput
                  clearable
                  label=" Date Submitted to MR"
                  name="submission_mr_date"
                  size="md"
                  radius={12}
                  mt="md"
                  placeholder="please input date"
                  valueFormat="YYYY-MM-DD"
                  value={formData.values.submission_mr_date}
                  onChange={(value) =>
                    updateForm(formData.id, {
                      ...formData.values,
                      submission_mr_date: value,
                    })
                  }
                />

                {/* Backoffice Return Date */}
                <DatePickerInput
                  clearable
                  label="Date Returned to Back Office (Update Database Training)"
                  name="backoffice_return_date"
                  size="md"
                  radius={12}
                  mt="md"
                  placeholder="please input date"
                  valueFormat="YYYY-MM-DD"
                  value={formData.values.backoffice_return_date}
                  onChange={(value) =>
                    updateForm(formData.id, {
                      ...formData.values,
                      backoffice_return_date: value,
                    })
                  }
                />
              </div>

              {/* Revision Document Processing and Lifecycle */}
              <Title c="dimmed" fz="h2" mt="xl" mb="md">
                Revision Document Processing and Lifecycle
              </Title>
              <div className="md:grid grid-cols-2 gap-3 text-gray-500">
                {/* Print Date */}
                <DatePickerInput
                  clearable
                  label="Print Date"
                  name="print_date"
                  size="md"
                  radius={12}
                  mt="md"
                  placeholder="please input date register"
                  valueFormat="YYYY-MM-DD"
                  value={formData.values.print_date}
                  onChange={(value) =>
                    updateForm(formData.id, {
                      ...formData.values,
                      print_date: value,
                    })
                  }
                />

                {/* Handover Date */}
                <DatePickerInput
                  clearable
                  label="Handover Date"
                  name="handover_date"
                  size="md"
                  radius={12}
                  mt="md"
                  placeholder="please input date register"
                  valueFormat="YYYY-MM-DD"
                  value={formData.values.handover_date}
                  onChange={(value) =>
                    updateForm(formData.id, {
                      ...formData.values,
                      handover_date: value,
                    })
                  }
                />

                {/* MR Signature Date */}
                <DatePickerInput
                  clearable
                  label="MR Signature Date"
                  name="signature_mr_date"
                  size="md"
                  radius={12}
                  mt="md"
                  placeholder="please input date register"
                  valueFormat="YYYY-MM-DD"
                  value={formData.values.signature_mr_date}
                  onChange={(value) =>
                    updateForm(formData.id, {
                      ...formData.values,
                      signature_mr_date: value,
                    })
                  }
                />

                {/* Distribution Date */}
                <DatePickerInput
                  clearable
                  label="Distribution Date"
                  name="distribution_date"
                  size="md"
                  radius={12}
                  mt="md"
                  placeholder="please input date register"
                  valueFormat="YYYY-MM-DD"
                  value={formData.values.distribution_date}
                  onChange={(value) =>
                    updateForm(formData.id, {
                      ...formData.values,
                      distribution_date: value,
                    })
                  }
                />

                {/* Document Return Date */}
                <DatePickerInput
                  clearable
                  label="Document Return Date"
                  name="document_return_date"
                  size="md"
                  radius={12}
                  mt="md"
                  placeholder="please input date register"
                  valueFormat="YYYY-MM-DD"
                  value={formData.values.document_return_date}
                  onChange={(value) =>
                    updateForm(formData.id, {
                      ...formData.values,
                      document_return_date: value,
                    })
                  }
                />

                {/* Document Destruction Date */}
                <DatePickerInput
                  clearable
                  label="Document Destruction Date"
                  name="document_disposal_date"
                  size="md"
                  radius={12}
                  mt="md"
                  placeholder="please input date register"
                  valueFormat="YYYY-MM-DD"
                  value={formData.values.document_disposal_date}
                  onChange={(value) =>
                    updateForm(formData.id, {
                      ...formData.values,
                      document_disposal_date: value,
                    })
                  }
                />
              </div>

              {/* Revision Document Status and Notes */}
              <Title c="dimmed" fz="h2" mt="xl" mb="md">
                Revision Document Status and Notes
              </Title>
              <div className="md:grid grid-cols-2 gap-3 text-gray-500">
                {/* Document Position */}
                <TextInput
                  label="Document Position"
                  size="md"
                  radius={12}
                  mt="md"
                  placeholder="please type Document Position"
                  className="shadow-default"
                  value={formData.values.document_location_description}
                  onChange={(e) =>
                    updateForm(formData.id, {
                      ...formData.values,
                      document_location_description: e.target.value,
                    })
                  }
                />

                {/* Description */}
                <TextInput
                  label="Description"
                  size="md"
                  radius={12}
                  mt="md"
                  placeholder="please type Description"
                  className="shadow-default"
                  value={formData.values.revision_description}
                  onChange={(e) =>
                    updateForm(formData.id, {
                      ...formData.values,
                      revision_description: e.target.value,
                    })
                  }
                />
                {/* Status Check */}
                <Select
                  withAsterisk
                  label="Status Check"
                  mt="md"
                  radius={12}
                  fw={100}
                  size="md"
                  color="gray"
                  searchable
                  className="shadow-default"
                  clearable
                  value={formData.values.status_check}
                  onChange={(value) =>
                    updateForm(formData.id, {
                      ...formData.values,
                      status_check: value || "",
                    })
                  }
                  data={statusCheck}
                />
              </div>
              <MeetingForm
                revisionId={formData.id}
                forms={forms}
                form={form}
                setForms={setForms}
              />
              <PositionCallForm
                revisionId={formData.id}
                forms={forms}
                form={form}
                setForms={setForms}
              />
            </div>
          )
      )}
    </>
  );
};

export default IKWRevisionForm;
