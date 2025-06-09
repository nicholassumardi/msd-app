/* eslint-disable @typescript-eslint/no-explicit-any */
import React, { useEffect, useState } from "react";
import { useDisclosure } from "@mantine/hooks";
import { ActionIcon, Button, Modal, Text } from "@mantine/core";
import { IconEditCircle } from "@tabler/icons-react";
import axios from "axios";
import SuccessNotification from "@/components/Notifications/SuccessNotification";
import ErrorNotification from "@/components/Notifications/ErrorNotification";
import StepperUser from "./Stepper";
import { useForm } from "@mantine/form";
import { option } from "../../../../pages/types/option";

interface FormComponent {
  uuid?: string;
  getData: () => Promise<void>;
  setIsLoading: React.Dispatch<React.SetStateAction<boolean>>;
}

export type Employee = {
  id?: string;
  employee_number: string;
  registry_date: string | null;
};

export type Certificate = {
  id?: string;
  certificate_id: string;
  certificate_name: string;
  description: string;
  expiration_date: string | null;
};

const FormEmployee: React.FC<FormComponent> = ({
  uuid,
  getData,
  setIsLoading,
}) => {
  const [mode, setMode] = useState("POST");
  const [error, setError] = useState<{ [key: string]: string }>({});
  const [opened, { open, close }] = useDisclosure(false);
  const [employeeNumbers, setEmployeeNumbers] = useState<Employee[]>([]);
  const [dataCompanies, setDataCompanies] = useState<option[]>([]);
  const [dataDepartments, setDataDepartments] = useState<option[]>([]);
  const [certificates, setCertificates] = useState<Certificate[]>([]);
  const [dataCertificates, setDataCertificates] = useState<option[]>([]);

  const form = useForm({
    initialValues: {
      name: "",
      identity_card: "",
      date_of_birth: null as Date | null,
      gender: "",
      religion: "",
      education: "",
      marital_status: "",
      phone: "",
      address: "",
      company_id: "",
      department_id: "",
      employee_type: "",
      section: "",
      position_code: "",
      status: "",
      schedule_type: "",
      status_twiji: "",
      join_date: null as Date | null,
      leave_date: null as Date | null,
      userEmployeeNumbers: [],
      userCertificates: [],
    },
    validate: {
      name: (value) => (!value ? "The name field must be filled" : null),
      date_of_birth: (value) =>
        !value ? "The date of birth field is required" : null,
      identity_card: (value) =>
        value.length > 19
          ? "The number must be less than or equal 16 digits long."
          : null,
      company_id: (value) => (!value ? "Please select the company" : null),
      department_id: (value) =>
        !value ? "Please select the department" : null,
      join_date: (value) => (!value ? "The join date field is required" : null),
    },
  });

  const handleGetDataDetail = async () => {
    try {
      const response = await axios.get(`/api/admin/employee/${uuid}?type=show`);
      const dataCertificates = response.data.data.user_certificates.map(
        (item: any) => ({
          id: item.id,
          certificate_id: item.pivot.certificate_id.toString(),
          certificate_name: item.name,
          description: item.pivot.description,
          expiration_date: item.pivot.expiration_date,
        })
      );
      setEmployeeNumbers(response.data.data.employee_numbers);
      setCertificates(dataCertificates);

      form.setValues({
        name: response.data.data.name,
        identity_card: response.data.data.identity_card,
        date_of_birth: response.data.data.date_of_birth
          ? new Date(response.data.data.date_of_birth)
          : null,
        gender: response.data.data.gender,
        religion: response.data.data.religion,
        education: response.data.data.education,
        marital_status: response.data.data.marital_status,
        phone: response.data.data.phone,
        address: response.data.data.address,
        company_id: response.data.data.company_id?.toString(),
        department_id: response.data.data.department_id?.toString(),
        employee_type: response.data.data.employee_type,
        section: response.data.data.section,
        position_code: response.data.data.position_code,
        status: response.data.data.status == "Aktif" ? "1" : "2",
        schedule_type: response.data.data.schedule_type,
        status_twiji: response.data.data.status_twiji,
        join_date: response.data.data.join_date
          ? new Date(response.data.data.join_date)
          : null,
        leave_date: response.data.data.leave_date
          ? new Date(response.data.data.leave_date)
          : null,
        userEmployeeNumbers: response.data.data.employee_numbers,
        userCertificates: dataCertificates,
      });
    } catch (err: any) {
      if (err.response) {
        setError(err.response.data.message);
      }
    }
  };

  const handleCreateData = () => {
    form.reset();
    open();
  };

  const handleEditData = () => {
    handleGetDataDetail();
    open();
  };

  useEffect(() => {
    const handleMode = () => {
      if (uuid) {
        setMode("PUT");
      } else {
        setMode("POST");
      }
    };
    handleMode();
  }, [uuid]);

  const handleCloseModal = () => {
    setError({});
    close();
  };

  const handleSubmit = async (values: any) => {
    try {
      if (mode === "PUT") {
        const response = await axios.put(
          `/api/admin/employee/${uuid}?type=update`,
          values
        );
        if (response.status === 200) {
          SuccessNotification({
            title: "Success",
            message: "Company data successfully updated",
          });
          close();
        }
        setIsLoading(true);
        setInterval(() => {
          getData();
        }, 1500);
      } else {
        const response = await axios.post(
          "/api/admin/employee?type=storeEmployee",
          values
        );
        if (response.status === 201) {
          SuccessNotification({
            title: "Success",
            message: "Company data successfully created",
          });
          close();
        }
        setIsLoading(true);
        setInterval(() => {
          getData();
        }, 1500);
      }
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
        fullScreen
        radius={0}
        size="lg"
        transitionProps={{ transition: "scale", duration: 350 }}
        title={
          <Text fw={700} size="xl">
            {mode === "PUT" ? "Edit Employee" : "Create New Employee"}
          </Text>
        }
      >
        <form onSubmit={form.onSubmit((values) => handleSubmit(values))}>
          <div className="mt-7 md:w-full h-full md:flex md:items-center md:justify-center">
            <div className="w-full h-full">
              <StepperUser
                form={form}
                employeeNumbers={employeeNumbers}
                dataCompanies={dataCompanies}
                dataDepartments={dataDepartments}
                certificates={certificates}
                dataCertificates={dataCertificates}
                setEmployeeNumbers={setEmployeeNumbers}
                setDataCompanies={setDataCompanies}
                setDataDepartments={setDataDepartments}
                setCertificates={setCertificates}
                setDataCertificates={setDataCertificates}
              />
            </div>
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

export default FormEmployee;
