/* eslint-disable @typescript-eslint/no-explicit-any */
import { useField } from "@mantine/form";
import { RevisionForm } from "./Form";
import { Button, Table, TextInput, Title } from "@mantine/core";
import { IconSquareRoundedPlus } from "@tabler/icons-react";

const PositionCallForm = ({
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
  const positionCallNumber = useField({
    initialValue: "",
  });

  const fieldOperator = useField({
    initialValue: "",
  });

  const updatePositionCall = (
    revisionId: string,
    index: number,
    field: "position_call_number" | "field_operator",
    value: string
  ) => {
    const revisionIndex = forms.findIndex((f) => f.id === revisionId);

    // Update main form
    form.setFieldValue(
      `revisions.${revisionIndex}.position_calls.${index}.${field}`,
      value
    );

    // Update local state
    setForms((prev) =>
      prev.map((form) => {
        if (form.id === revisionId) {
          const updatedCalls = [...form.values.position_calls];
          updatedCalls[index][field] = value;
          return {
            ...form,
            values: {
              ...form.values,
              position_calls: updatedCalls,
            },
          };
        }
        return form;
      })
    );
  };

  const addPositionCall = (revisionId: string) => {
    const newCall = {
      position_call_number: positionCallNumber.getValue(),
      field_operator: fieldOperator.getValue(),
    };

    const revisionIndex = forms.findIndex((f) => f.id === revisionId);

    form.setFieldValue(`revisions.${revisionIndex}.position_calls`, [
      ...form.values.revisions[revisionIndex].position_calls,
      newCall,
    ]);

    // Update local state
    setForms((prev) =>
      prev.map((form) => {
        if (form.id === revisionId) {
          return {
            ...form,
            values: {
              ...form.values,
              position_calls: [...form.values.position_calls, newCall],
            },
          };
        }
        return form;
      })
    );

    positionCallNumber.setValue("");
    fieldOperator.setValue("");
  };

  const deletePositionCall = (revisionId: string, index: number) => {
    const revisionIndex = forms.findIndex((f) => f.id === revisionId);

    const updatedCalls = form.values.revisions[
      revisionIndex
    ].position_calls.filter((_: any, i: number) => i !== index);
    form.setFieldValue(
      `revisions.${revisionIndex}.position_calls`,
      updatedCalls
    );

    // Update local state
    setForms((prev) =>
      prev.map((form) => {
        if (form.id === revisionId) {
          return {
            ...form,
            values: {
              ...form.values,
              position_calls: form.values.position_calls.filter(
                (_, i) => i !== index
              ),
            },
          };
        }
        return form;
      })
    );
  };

  const revision = forms.find((f) => f.id === revisionId);
  if (!revision) return null;

  return (
    <>
      <Title c="dimmed" fz="h1" mt="xl" mb="md">
        Position
      </Title>
      <div className="md:grid grid-cols-3 gap-3 text-gray-500">
        {/* Add new position call inputs */}
        <TextInput
          {...positionCallNumber.getInputProps()}
          label="Position Call Number"
          placeholder="Enter call number"
        />
        <TextInput
          {...fieldOperator.getInputProps()}
          label="Field Operator"
          placeholder="Enter operator name"
        />

        <div className="col-span-3">
          <Button
            onClick={() => addPositionCall(revisionId)}
            leftSection={<IconSquareRoundedPlus />}
            mb="md"
            fullWidth
          >
            Add Position Call
          </Button>
        </div>

        {/* Existing position calls table */}
        <div className="col-span-3">
          <Table striped highlightOnHover withTableBorder>
            <Table.Thead>
              <Table.Tr>
                <Table.Th>Position Call Number</Table.Th>
                <Table.Th>Field Operator</Table.Th>
                <Table.Th>Action</Table.Th>
              </Table.Tr>
            </Table.Thead>
            <Table.Tbody>
              {revision.values.position_calls.map((call, index) => (
                <Table.Tr key={index}>
                  <Table.Td>
                    <TextInput
                      size="sm"
                      value={call.position_call_number}
                      onChange={(e) =>
                        updatePositionCall(
                          revisionId,
                          index,
                          "position_call_number",
                          e.target.value
                        )
                      }
                    />
                  </Table.Td>
                  <Table.Td>
                    <TextInput
                      size="sm"
                      value={call.field_operator}
                      onChange={(e) =>
                        updatePositionCall(
                          revisionId,
                          index,
                          "field_operator",
                          e.target.value
                        )
                      }
                    />
                  </Table.Td>
                  <Table.Td>
                    <Button
                      variant="outline"
                      color="red"
                      size="sm"
                      onClick={() => deletePositionCall(revisionId, index)}
                    >
                      Delete
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

export default PositionCallForm;
