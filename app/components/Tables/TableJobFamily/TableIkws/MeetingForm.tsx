/* eslint-disable @typescript-eslint/no-explicit-any */
import { Button, Group, Select, Table, TextInput, Title } from "@mantine/core";
import { DatePickerInput } from "@mantine/dates";
import { RevisionForm } from "./Form";
import { useField } from "@mantine/form";
import { IconSquareRoundedPlus, IconTrashFilled } from "@tabler/icons-react";

const MeetingForm = ({
  revisionId,
  forms,
  form,
  setForms,
}: {
  revisionId: string;
  forms: RevisionForm[];
  form: any;
  setForms: React.Dispatch<React.SetStateAction<RevisionForm[]>>;
}) => {
  const meetingDate = useField({
    initialValue: null as Date | null,
  });
  const meetingDuration = useField({
    initialValue: 0,
  });
  const meetingStatus = useField({
    initialValue: "",
  });

  const addMeetingContent = (revisionId: string) => {
    const rawDate = meetingDate.getValue();

    const safeDate =
      rawDate instanceof Date ? rawDate : rawDate ? new Date(rawDate) : null;

    const isoDateString =
      safeDate && !isNaN(safeDate.getTime())
        ? safeDate.toISOString().split("T")[0]
        : null;

    const newMeeting = {
      meeting_date: meetingDate.getValue(), // Store as Date object
      meeting_duration: meetingDuration.getValue(),
      revision_status: meetingStatus.getValue(),
      meeting_date_string: isoDateString,
    };

    const revisionIndex = forms.findIndex((f) => f.id === revisionId);
    form.setFieldValue(`revisions.${revisionIndex}.meeting_contents`, [
      ...(form.values.revisions[revisionIndex]?.meeting_contents || []),
      newMeeting,
    ]);

    // Update local state with both Date and string representation
    setForms((prev) =>
      prev.map((form) => {
        if (form.id === revisionId) {
          return {
            ...form,
            values: {
              ...form.values,
              meeting_contents: [
                ...form.values.meeting_contents,
                {
                  ...newMeeting,
                  meeting_date: rawDate,
                  meeting_date_string: isoDateString,
                },
              ],
            },
          };
        }
        return form;
      })
    );

    // Reset fields
    meetingDate.setValue(null);
    meetingDuration.setValue(0);
    meetingStatus.setValue("");
  };

  //   const updateMeetingContent = (
  //     revisionId: string,
  //     field: keyof MeetingContent,
  //     value: string
  //   ) => {
  //     setForms((prev) =>
  //       prev.map((form) =>
  //         form.id === revisionId
  //           ? {
  //               ...form,
  //               values: {
  //                 ...form.values,
  //                 meeting_contents: form.values.meeting_contents.map((call, i) =>
  //                   i === index ? { ...call, [field]: value } : call
  //                 ),
  //               },
  //             }
  //           : form
  //       )
  //     );
  //   };

  const deleteMeetingContent = (revisionId: string, index: number) => {
    setForms((prev) =>
      prev.map((form) =>
        form.id === revisionId
          ? {
              ...form,
              values: {
                ...form.values,
                meeting_contents: form.values.meeting_contents.filter(
                  (_, i) => i !== index
                ),
              },
            }
          : form
      )
    );
  };
  const revisionStatusMeeting = [
    { value: "OK", label: "OK" },
    { value: "NOK", label: "NOK" },
    { value: "REV", label: "REV" },
    { value: "DELETE", label: "DELETE" },
  ];
  const revision = forms.find((f) => f.id === revisionId);
  if (!revision) return null;

  return (
    <>
      <Title c="dimmed" fz="h1" mt="xl" mb="md">
        Meeting
      </Title>
      <div className="md:grid grid-cols-3 gap-3 text-gray-500">
        <DatePickerInput
          {...meetingDate.getInputProps()}
          label="Meeting Date"
          placeholder="Select date"
          valueFormat="YYYY-MM-DD"
          size="md"
          color="gray"
          radius={12}
          withAsterisk
        />

        <TextInput
          {...meetingDuration.getInputProps()}
          type="number"
          label="Meeting Duration (minutes)"
          size="md"
          color="gray"
          radius={12}
          placeholder="Enter duration"
        />

        <Select
          {...meetingStatus.getInputProps()}
          label="Meeting Status"
          placeholder="Select status"
          size="md"
          color="gray"
          radius={12}
          data={revisionStatusMeeting}
        />

        <div className="col-span-3">
          <Group justify="center" mt="md">
            <Button
              variant="light"
              leftSection={<IconSquareRoundedPlus />}
              size="md"
              fullWidth
              onClick={() => addMeetingContent(revisionId)}
            >
              Add Meeting
            </Button>
          </Group>

          {/* Table with editable rows */}
          <Table striped highlightOnHover withTableBorder mt="md">
            <Table.Thead>
              <Table.Tr>
                <Table.Th>Meeting Date</Table.Th>
                <Table.Th>Duration</Table.Th>
                <Table.Th>Status</Table.Th>
                <Table.Th>Action</Table.Th>
              </Table.Tr>
            </Table.Thead>
            <Table.Tbody>
              {revision.values.meeting_contents.map((meeting, index) => (
                <Table.Tr key={index}>
                  <Table.Td>
                    <DatePickerInput
                      value={meeting.meeting_date}
                      onChange={(value) => {
                        form.setFieldValue(
                          `revisions.${forms.findIndex(
                            (f) => f.id === revisionId
                          )}.meeting_contents.${index}.meeting_date`,
                          value
                        );
                      }}
                    />
                  </Table.Td>
                  <Table.Td>
                    <TextInput
                      value={meeting.meeting_duration}
                      onChange={(e) => {
                        form.setFieldValue(
                          `revisions.${forms.findIndex(
                            (f) => f.id === revisionId
                          )}.meeting_contents.${index}.meeting_duration`,
                          Number(e.target.value)
                        );
                      }}
                    />
                  </Table.Td>
                  <Table.Td>
                    <Select
                      value={meeting.revision_status}
                      data={revisionStatusMeeting}
                      onChange={(value) => {
                        form.setFieldValue(
                          `revisions.${forms.findIndex(
                            (f) => f.id === revisionId
                          )}.meeting_contents.${index}.revision_status`,
                          value
                        );
                      }}
                    />
                  </Table.Td>
                  <Table.Td>
                    <Button
                      variant="outline"
                      color="red"
                      onClick={() => deleteMeetingContent(revisionId, index)}
                    >
                      <IconTrashFilled />
                    </Button>
                  </Table.Td>
                </Table.Tr>
              ))}
            </Table.Tbody>
          </Table>
        </div>
      </div>
    </>
  );
};

export default MeetingForm;
