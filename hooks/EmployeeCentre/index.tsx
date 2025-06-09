/* eslint-disable @typescript-eslint/no-explicit-any */
import { useEffect, useState } from "react";
import { option } from "../../pages/types/option";
import { Employee } from "../../pages/types/employee";

import { useForm } from "@mantine/form";
import ErrorNotification from "@/components/Notifications/ErrorNotification";
import axios from "axios";

import SuccessNotification from "@/components/Notifications/SuccessNotification";
import { Text } from "@mantine/core";
import { modals } from "@mantine/modals";
import { RKI } from "../../pages/types/rki";
import { Structure } from "../../pages/types/structure";

const useEmployeeData = () => {
  const [dataEmployee, setDataEmployee] = useState<option[]>([]);
  const [dataNIKNIP, setDataNIKNIP] = useState<option[]>([]);
  const [dataStructure, setDataStructure] = useState<option[]>([]);
  const [dataCompany, setDataCompany] = useState<option[]>([]);
  const [dataDepartment, setDataDepartment] = useState<option[]>([]);
  const [dataPehCode, setDataPehCode] = useState<option[]>([]);
  const [idCompany, setIdCompany] = useState<string | null>(null);
  const [idDepartment, setIdDepartment] = useState<string | null>(null);
  const [idStructure, setIdStructure] = useState<string | null>(null);
  const [dataRki, setDataRki] = useState<RKI[]>([]);
  const [foundEmployee, setFoundEmployee] = useState<Employee | null>(null);
  const [foundStructure, setFoundStructure] = useState<Structure | null>(null);
  const [globalFilter, setGlobalFilter] = useState("");
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const formEmployee = useForm({
    initialValues: {
      id: 0,
      name: "",
      staff_id: "",
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
      userIKWS: [],
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

  const getDataCompany = async () => {
    try {
      const response = await axios.get("/api/admin/master_data/company");
      const data = response.data.data.data.map((item: any) => ({
        value: item.id.toString(),
        label: item.name,
      }));
      setDataCompany(data);
    } catch (err: any) {
      ErrorNotification({
        title: "Server Error",
        message: err.response.data.error,
      });
    }
  };

  const getDataDepartment = async () => {
    try {
      const id = idCompany ? idCompany : "1";
      const response = await axios.get(
        `/api/admin/master_data/department/${id}?type=showByCompany`
      );
      const data = response.data.data.map((item: any) => ({
        value: item.id.toString(),
        label: item.code,
      }));
      setDataDepartment(data);
    } catch (err: any) {
      ErrorNotification({
        title: "Server Error",
        message: err.response.data.error,
      });
    }
  };

  const getDataEmployee = async () => {
    setIsLoading(true);
    try {
      const response = await axios.get(
        "/api/admin/employee?type=showEmployeePagination",
        {
          params: {
            globalFilter: globalFilter ?? "",
            id_company: idCompany ?? null,
            id_department: idDepartment ?? null,
          },
        }
      );

      const data = response.data.data.map((item: any) => ({
        value: item.uuid.toString(),
        label: item.name,
        code: item.employeeStructure.name + " - " + item.group,
      }));
      const dataNIPNIK = response.data.data.map((item: any) => ({
        value: item.uuid.toString(),
        label: item.identity_card + " - " + item.employee_number,
      }));
      setDataEmployee(data);
      setDataNIKNIP(dataNIPNIK);
    } catch (err: any) {
      ErrorNotification({
        title: "Server Error",
        message: err.response.data.error,
      });
    } finally {
      setIsLoading(false);
    }
  };

  const getDataStructure = async () => {
    setIsLoading(true);
    try {
      const response = await axios.get(
        `/api/admin/structure?type=structureMapping`,
        {
          params: {
            globalFilter: globalFilter ?? "",
            id_department: idDepartment ?? null,
          },
        }
      );

      const data = response.data.data.map((item: any) => ({
        value: item.id.toString(),
        label: item.name,
      }));
      setDataStructure(data);
    } catch (err: any) {
      ErrorNotification({
        title: "Server Error",
        message: err.response.data.error,
      });
    } finally {
      setIsLoading(false);
    }
  };

  const handleGetEmployeeDetail = async (uuid: string | null) => {
    if (uuid) {
      try {
        const response = await axios.get(
          `/api/admin/employee/${uuid}?type=show`
        );

        setFoundEmployee(response.data.data.data);

        const dataCertificates = response.data.data.data.user_certificates.map(
          (item: any) => ({
            id: item.id,
            certificate_id: item.pivot.certificate_id.toString(),
            certificate_name: item.name,
            description: item.pivot.description,
            expiration_date: item.pivot.expiration_date,
          })
        );

        formEmployee.setValues({
          id: response.data.data.data.id,
          name: response.data.data.data.name,
          identity_card: response.data.data.data.identity_card,
          date_of_birth: response.data.data.data.date_of_birth
            ? new Date(response.data.data.data.date_of_birth)
            : null,
          gender: response.data.data.data.gender,
          religion: response.data.data.data.religion,
          education: response.data.data.data.education,
          marital_status: response.data.data.data.marital_status,
          phone: response.data.data.data.phone,
          address: response.data.data.data.address,
          company_id: response.data.data.data.company_id?.toString(),
          department_id: response.data.data.data.department_id?.toString(),
          staff_id: response.data.data.data.id_staff?.toString(),
          employee_type: response.data.data.data.employee_type,
          section: response.data.data.data.section,
          position_code: response.data.data.data.position_code,
          status: response.data.data.data.status == "Aktif" ? "1" : "2",
          schedule_type: response.data.data.data.schedule_type,
          status_twiji: response.data.data.data.status_twiji,
          join_date: response.data.data.data.join_date
            ? new Date(response.data.data.data.join_date)
            : null,
          leave_date: response.data.data.data.leave_date
            ? new Date(response.data.data.data.leave_date)
            : null,
          userEmployeeNumbers: response.data.data.data.employee_numbers,
          userCertificates: dataCertificates,
        });
      } catch (err: any) {
        ErrorNotification({
          title: "Server Error",
          message: err.response.data.error,
        });
      } finally {
        setIsLoading(false);
      }
    }
  };

  const handleGetStructureDetail = async (id: string | null) => {
    if (!id) return;
    try {
      const response = await axios.get(
        `/api/admin/structure/${id}?type=getMapping`
      );

      setFoundStructure(response.data.data.data);
      handleDataRKI(
        response.data.data.data?.job_code?.full_code +
          "-" +
          response.data.data.data?.user_job_code[0]?.position_code_structure
      );
    } catch (err: any) {
      ErrorNotification({
        title: "Server Error",
        message: err.response.data.error,
      });
    } finally {
      setIsLoading(false);
    }
  };

  const handleDataRKI = async (position_job_code: string) => {
    try {
      const response = await axios.get(
        `/api/admin/rki?type=showRKIByPositionJobCode`,
        {
          params: {
            position_job_code: position_job_code,
          },
        }
      );

      setDataRki(response.data.data);
    } catch (err: any) {
      ErrorNotification({
        title: "Server Error",
        message: err.response.data.error,
      });
    }
  };

  const handleSubmitEmployee = async (values: any) => {
    try {
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
    } catch (err: any) {
      if (err.response && err.response.status == 422) {
        ErrorNotification({
          title: "Server Error",
          message: err.response.data.error,
        });
      } else {
        ErrorNotification({
          title: "Server Error",
          message: "500 Internal Server Error",
        });
      }
    }
  };

  const handleEditEmployee = async (values: any, uuid: string | null) => {
    try {
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
    } catch (err: any) {
      if (err.response && err.response.status == 422) {
        ErrorNotification({
          title: "Server Error",
          message: err.response.data.error,
        });
      } else {
        ErrorNotification({
          title: "Server Error",
          message: "500 Internal Server Error",
        });
      }
    }
  };

  const handleSubmitStructure = async (mode: string, values: any) => {
    try {
      if (mode === "PUT") {
        const response = await axios.put(
          `/api/admin/structure/${idStructure}?type=updateMapping`,
          values
        );
        if (response.status === 200) {
          SuccessNotification({
            title: "Success",
            message: "structure data successfully updated",
          });
          // closeModal();
        }
      } else {
        const response = await axios.post(
          "/api/admin/structure?type=storeMapping",
          values
        );
        if (response.status === 201) {
          SuccessNotification({
            title: "Success",
            message: "structure data successfully created",
          });
          // closeModal();
        }
      }
    } catch (err: any) {
      if (err.response && err.response.status == 422) {
        ErrorNotification({
          title: "Server Error",
          message: err.response.data.error,
        });
      } else {
        ErrorNotification({
          title: "Server Error",
          message: "500 Internal Server Error",
        });
      }
    }
  };

  const handleRemoveAssignmentStructure = async (id: string) => {
    try {
      const response = await axios.put(
        `/api/admin/structure/${id}?type=updateStatus`
      );
      if (response.status === 200) {
        SuccessNotification({
          title: "Success",
          message: "Structure data successfully added",
        });
      }
    } catch (err: any) {
      ErrorNotification({
        title: "Server Error",
        message: err.response.data.error,
      });
    }
  };

  const openModalDeleteStructureAssignment = (id: string) => {
    modals.openConfirmModal({
      title: `Confirm deletion ?`,
      children: (
        <Text>
          Are you sure you want to remove assignment from this employee? This
          action cannot be undone.
        </Text>
      ),
      labels: { confirm: "Yes", cancel: "Cancel" },
      confirmProps: { color: "red" },
      onConfirm: () => {
        handleRemoveAssignmentStructure(id);
      },
    });
  };

  useEffect(() => {
    getDataCompany();
  }, []);

  useEffect(() => {
    getDataDepartment();
  }, [idCompany]);

  useEffect(() => {
    getDataEmployee();
  }, [idDepartment, idCompany]);

  useEffect(() => {
    getDataStructure();
  }, [globalFilter, idDepartment, idCompany]);

  useEffect(() => {
    const getDataPehCode = async () => {
      try {
        const response = await axios.get(
          "/api/admin/master_data/job_family/peh_code?type=show"
        );
        const data = response.data.data.data.map((item: any) => ({
          value: item.id.toString(),
          label: `${item.category_name} - ${item.position} - ${item.code}`,
        }));
        setDataPehCode(data);
      } catch (err: any) {
        ErrorNotification({
          title: "Server Error",
          message: err.response.data.error,
        });
      }
    };
    getDataPehCode();
  }, []);

  return {
    dataEmployee,
    setDataEmployee,
    dataNIKNIP,
    setDataNIKNIP,
    dataStructure,
    setDataStructure,
    dataCompany,
    setDataCompany,
    dataDepartment,
    setDataDepartment,
    dataPehCode,
    setDataPehCode,
    idCompany,
    setIdCompany,
    idDepartment,
    setIdDepartment,
    idStructure,
    setIdStructure,
    dataRki,
    setDataRki,
    foundEmployee,
    setFoundEmployee,
    foundStructure,
    setFoundStructure,
    globalFilter,
    setGlobalFilter,
    isLoading,
    setIsLoading,
    handleGetEmployeeDetail,
    handleSubmitEmployee,
    handleEditEmployee,
    handleGetStructureDetail,
    openModalDeleteStructureAssignment,
    handleSubmitStructure,
    formEmployee,
  };
};

export default useEmployeeData;
