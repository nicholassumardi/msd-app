/* eslint-disable @typescript-eslint/no-explicit-any */
import {
  Button,
  Group,
  Table,
  TextInput,
  Title,
  Text,
  Select,
} from "@mantine/core";
import { IconSquareRoundedPlus, IconTrashFilled } from "@tabler/icons-react";
import { useEffect, useState } from "react";
import { useField } from "@mantine/form";
import { modals } from "@mantine/modals";
import axios from "axios";
import { Certificate } from "./Form";
import { DatePickerInput } from "@mantine/dates";
import { option } from "../../../../pages/types/option";

interface ContentComponent {
  form: any;
  certificates: Certificate[];
  dataCertificates: option[];
  setCertificates: React.Dispatch<React.SetStateAction<Certificate[]>>;
  setDataCertificates: React.Dispatch<React.SetStateAction<option[]>>;
}

const ContentCertificate: React.FC<ContentComponent> = ({
  form,
  certificates,
  dataCertificates,
  setCertificates,
  setDataCertificates,
}) => {
  const [error, setError] = useState<string | null>(null);
  const certificate_id = useField({
    initialValue: "",
    validate: (value) => (!value ? "Certificate cannot be empty" : null),
  });
  const description = useField({
    initialValue: "",
  });
  const expiryDate = useField({
    initialValue: null as Date | null,
  });

  useEffect(() => {
    const getData = async () => {
      try {
        const response = await axios.get("/api/admin/master_data/certificate");
        const certificatesData = response.data.data.map((item: any) => ({
          value: item.id.toString(),
          label: item.name,
        }));
        setDataCertificates(certificatesData);
      } catch (err: any) {
        if (err.response) {
          setError(err.response.data.message);
        } else if (err.request) {
          setError("No response from server");
        } else {
          setError("Unexpected error occured");
        }
      }
    };

    getData();
  }, []);

  const handleAdd = () => {
    if (!certificate_id.getValue()) {
      certificate_id.validate();
      return;
    }
    const selectedCertificate = dataCertificates.find(
      (cert) => cert.value === certificate_id.getValue()
    );

    const certificateDataNow = {
      certificate_id: certificate_id.getValue(),
      certificate_name: selectedCertificate ? selectedCertificate.label : "",
      description: description.getValue(),
      expiration_date:
        expiryDate.getValue()?.toISOString().split("T")[0] || null,
    };

    setCertificates((prevCertificates) => [
      ...prevCertificates,
      certificateDataNow,
    ]);

    form.setFieldValue("userCertificates", [
      ...form.getValues().userCertificates,
      certificateDataNow,
    ]);

    certificate_id.setValue("");
    description.setValue("");
  };

  const handleEdit = (
    index: number,
    field: keyof Certificate,
    value: string | null
  ) => {
    setCertificates((prev) => {
      const updatedCertificate = [...prev];
      updatedCertificate[index][field] = value || "";
      return updatedCertificate;
    });

    form.setFieldValue("userCertificates", [
      ...certificates.map((certificate, i) =>
        i === index ? { ...certificate, [field]: value || "" } : certificate
      ),
    ]);
  };

  const handleDelete = (indexToDelete: any) => {
    setCertificates((prev) =>
      prev.filter((_, index) => index !== indexToDelete)
    );
  };

  const openDeleteConfirmModal = (indexToDelete: any) =>
    modals.openConfirmModal({
      title: `Confirm deletion ?`,
      children: (
        <Text>
          Are you sure you want to delete this row ? This action cannot be
          undone.
        </Text>
      ),
      labels: { confirm: "Delete", cancel: "Cancel" },
      confirmProps: { color: "red" },
      onConfirm: () => {
        handleDelete(indexToDelete);
      },
    });

  return (
    <>
      <Title c="dimmed" fz="h4" mt="xl" mb="md">
        Employee Certification
      </Title>
      <div className="grid grid-cols-2 gap-3">
        <Select
          mt="md"
          searchable
          clearable
          label="Certificate type"
          {...certificate_id.getInputProps()}
          data={dataCertificates}
        ></Select>
        <TextInput
          mt="md"
          label="Certificate description"
          placeholder="Please input description"
          {...description.getInputProps()}
        ></TextInput>
        <DatePickerInput
          {...expiryDate.getInputProps()}
          clearable
          label="Expiry date"
          name="expiration_date"
          placeholder="please input date register for employee number"
          valueFormat="YYYY-MM-DD"
          withAsterisk
        ></DatePickerInput>
        <div className="col-span-2">
          <Group justify="center" mt="md">
            <Button
              variant="light"
              leftSection={<IconSquareRoundedPlus />}
              size="md"
              fullWidth
              onClick={() => {
                handleAdd();
              }}
            >
              Add
            </Button>
          </Group>
        </div>
        <div className="col-span-2">
          <Table striped highlightOnHover withTableBorder mt="md">
            <Table.Thead>
              <Table.Tr>
                <Table.Th>Certificate</Table.Th>
                <Table.Th>Description</Table.Th>
                <Table.Th>Expiration Date</Table.Th>
                <Table.Th>Action</Table.Th>
              </Table.Tr>
            </Table.Thead>
            <Table.Tbody>
              {certificates.length > 0 ? (
                certificates.map((certificate, index) => (
                  <Table.Tr key={index}>
                    <Table.Td>
                      <Select
                        searchable
                        clearable
                        value={certificate.certificate_id.toString()}
                        onChange={(value) => {
                          handleEdit(index, "certificate_id", value || "null");
                        }}
                        data={dataCertificates}
                      ></Select>
                    </Table.Td>
                    <Table.Td>
                      {" "}
                      <TextInput
                        placeholder="Please input description"
                        value={certificate.description}
                        onChange={(e) => {
                          handleEdit(index, "description", e.target.value);
                        }}
                      ></TextInput>
                    </Table.Td>
                    <Table.Td>
                      <DatePickerInput
                        clearable
                        value={
                          certificate.expiration_date
                            ? new Date(certificate.expiration_date)
                            : null
                        }
                        placeholder="please input date register for employee number"
                        valueFormat="YYYY-MM-DD"
                        onChange={(value) => {
                          handleEdit(
                            index,
                            "expiration_date",
                            value?.toString() || null
                          );
                        }}
                        withAsterisk
                      ></DatePickerInput>
                    </Table.Td>
                    <Table.Td>
                      <Button
                        variant="outline"
                        color="red"
                        size="xs"
                        onClick={() => {
                          openDeleteConfirmModal(index);
                        }}
                      >
                        <IconTrashFilled />
                      </Button>
                    </Table.Td>
                  </Table.Tr>
                ))
              ) : (
                <Table.Tr>
                  <Table.Td align="center" colSpan={4}>
                    No data found
                  </Table.Td>
                </Table.Tr>
              )}
            </Table.Tbody>
          </Table>
        </div>
      </div>
    </>
  );
};

export default ContentCertificate;
