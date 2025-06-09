/* eslint-disable @typescript-eslint/no-explicit-any */
import ErrorNotification from "@/components/Notifications/ErrorNotification";
import { createContext, useContext, useEffect, useState } from "react";
import { ReactNode } from "react";
import { Employee } from "../../pages/api/admin/employee";
import { useForm } from "@mantine/form";
import { TreeNode } from "@unicef/react-org-chart";
import axios from "axios";
import { Node } from "@/employee/OrgChartTab/OrgChart";
import { useDisclosure } from "@mantine/hooks";
import SuccessNotification from "@/components/Notifications/SuccessNotification";

type EmployeeDataContextType = {
  globalTab: string;
  setGlobalTab: React.Dispatch<React.SetStateAction<string>>;
  UUID: string | null;
  setUUID: React.Dispatch<React.SetStateAction<string | null>>;
  handleGetStructureDetail: (id: string | null) => Promise<void>;
  handleSelectChange: (value: any) => void;
  tree: TreeNode | null;
  setTree: React.Dispatch<React.SetStateAction<TreeNode | null>>;
  foundEmployee: Employee | null;
  setFoundEmployee: React.Dispatch<React.SetStateAction<Employee | null>>;
  formEmployee: any;
  formUserStructureMapping: any;
  formUserJobCode: any;
  getChild: (id: number) => Promise<Node[]>;
  getParent: (d: Node) => Promise<Node | undefined>;
  isLoading: boolean;
  openedModal: boolean;
  openModal: (modalId: any) => void;
  closeModal: () => void;
  isOpen: (modalId: any) => boolean;
  handleSubmitStructureMappingRequest: (values: any) => Promise<void>;
  handleSubmitStructureMappingAssign: (values: any) => Promise<void>;
};

const EmployeeDataContext = createContext<EmployeeDataContextType | undefined>(
  undefined
);

export const EmployeeDataProvider = ({ children }: { children: ReactNode }) => {
  const [globalTab, setGlobalTab] = useState("overview");
  const [UUID, setUUID] = useState<string | null>(null);
  const [foundEmployee, setFoundEmployee] = useState<Employee | null>(null);
  const [tree, setTree] = useState<TreeNode | null>(null);
  const [treeChildren, setTreeChildren] = useState<TreeNode[]>([]);
  const [treeParent, setTreeParent] = useState<TreeNode | undefined>();
  const [cache, setCache] = useState<Map<string, any>>(new Map());
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const [openedModal, { open, close }] = useDisclosure(false);
  const [activeModalId, setActiveModalId] = useState(null);
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
  const formUserStructureMapping = useForm({
    initialValues: {
      department_id: "",
      name: "",
      quota: "",
      structure_type: "",
      job_code_id: null,
      revision_no: "",
      valid_date: null as Date | null,
      updated_date: null as Date | null,
      authorized_date: null as Date | null,
      approval_date: null as Date | null,
      acknowledged_date: null as Date | null,
      created_date: null as Date | null,
      distribution_date: null as Date | null,
      withdrawal_date: null as Date | null,
    },
    validate: {
      department_id: (value) => (!value ? "Please select a department" : null),
      name: (value) => (!value ? "Name cannot be empty" : null),
      quota: (value) => {
        if (!value) return "Quota cannot be empty";
        if (isNaN(Number(value))) return "Quota must be a valid number";
        return null;
      },
    },
  });
  const formUserJobCode = useForm({
    initialValues: {
      user_structure_mapping_id: "",
      uuid: "",
      id_structure: "",
      id_staff: "",
      group: "",
      position_code_structure: "",
      assign_date: null as Date | null,
      description: "",
    },
  });

  const handleGetDataHierarchy = async (
    params: string,
    id: number
  ): Promise<any> => {
    const cacheKey = `${params}-${id}`;
    if (cache.has(cacheKey)) {
      return cache.get(cacheKey);
    }

    if (!id) throw new Error("No ID provided");

    let url;
    switch (params) {
      case "parent":
        url = `/api/admin/structure/${id}?type=showMappingHierarchyParent`;
        break;
      case "children":
        url = `/api/admin/structure/${id}?type=showMappingHierarchyChildren`;
        break;
      case "user":
        url = `/api/admin/structure/${id}?type=showMappingHierarchyUser`;
        break;
      default:
        throw new Error("Invalid params");
    }

    try {
      const response = await axios.get(url);
      const data = response.data.data.data;
      setCache((prev) => new Map(prev).set(cacheKey, data));

      if (params === "user") {
        setTree(data);
      } else if (params === "children") {
        setTreeChildren(data);
      } else if (params === "parent") {
        setTreeParent(data);
      }

      return data;
    } catch (err: any) {
      ErrorNotification({
        title: "Server Error",
        message: err.response?.data?.error || "Failed to fetch data",
      });
      throw err;
    }
  };

  const getChild = async (id: number): Promise<Node[]> => {
    if (!id) return [];
    try {
      const data = await handleGetDataHierarchy("children", id);
      return data || [];
    } catch {
      return [];
    }
  };

  const getParent = async (d: Node): Promise<Node | undefined> => {
    if (!d.parentId) return undefined;
    try {
      const data = await handleGetDataHierarchy("parent", d.parentId);
      return data;
    } catch {
      return undefined;
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
    try {
      const response = await axios.get(
        `/api/admin/structure/${id}?type=getMapping`
      );

      formUserStructureMapping.setValues({
        department_id: response.data.data.data.department_id.toString(),
        name: response.data.data.data.name,
        quota: response.data.data.data.quota,
        job_code_id: response.data.data.data.job_code_id.toString(),
        structure_type: response.data.data.data.structure_type,
        // revision_no: "",
        // valid_date: response.data.data.data.user_mapping_histories.valid_date
        //   ? new Date(response.data.data.data.user_mapping_histories.valid_date)
        //   : null,
        // updated_date: response.data.data.data.user_mapping_histories
        //   .updated_date
        //   ? new Date(
        //       response.data.data.data.user_mapping_histories.updated_date
        //     )
        //   : null,
        // authorized_date: response.data.data.data.user_mapping_histories
        //   .authorized_date
        //   ? new Date(
        //       response.data.data.data.user_mapping_histories.authorized_date
        //     )
        //   : null,
        // approval_date: response.data.data.data.user_mapping_histories
        //   .approval_date
        //   ? new Date(
        //       response.data.data.data.user_mapping_histories.approval_date
        //     )
        //   : null,
        // acknowledged_date: response.data.data.data.user_mapping_histories
        //   .acknowledged_date
        //   ? new Date(
        //       response.data.data.data.user_mapping_histories.acknowledged_date
        //     )
        //   : null,
        // created_date: response.data.data.data.user_mapping_histories
        //   .created_date
        //   ? new Date(
        //       response.data.data.data.user_mapping_histories.created_date
        //     )
        //   : null,
        // distribution_date: response.data.data.data.user_mapping_histories
        //   .distribution_date
        //   ? new Date(
        //       response.data.data.data.user_mapping_histories.distribution_date
        //     )
        //   : null,
        // withdrawal_date: response.data.data.data.user_mapping_histories
        //   .withdrawal_date
        //   ? new Date(
        //       response.data.data.data.user_mapping_histories.withdrawal_date
        //     )
        //   : null,
      });
    } catch (err: any) {
      if (err.response) {
      }
    }
  };

  const handleSelectChange = (value: any) => {
    setFoundEmployee(null);
    setTree(null);
    setUUID(value || "");
    handleGetEmployeeDetail(value);
  };

  const handleSubmitStructureMappingRequest = async (values: any) => {
    try {
      const response = await axios.post(
        "/api/admin/structure?type=storeMappingRequest",
        values
      );
      if (response.status === 201) {
        SuccessNotification({
          title: "Success",
          message: "structure data successfully created",
        });
        closeModal();
      }
    } catch (err: any) {
      if (err.response && err.response.status == 422) {
        ErrorNotification({
          title: "Server Error",
          message: err.response.data.error,
        });
        closeModal();
      } else {
        ErrorNotification({
          title: "Server Error",
          message: "500 Internal Server Error",
        });
        closeModal();
      }
    }
  };

  const handleSubmitStructureMappingAssign = async (values: any) => {
    console.log(values);
    // try {
    //   const response = await axios.post(
    //     `/api/admin/structure?type=storeStructure`,
    //     values
    //   );
    //   if (response.status === 201) {
    //     SuccessNotification({
    //       title: "Success",
    //       message: "Structure data successfully added",
    //     });
    //     closeModal();
    //   }
    // } catch (err: any) {
    //   ErrorNotification({
    //     title: "Server Error",
    //     message: err.response.data.error,
    //   });
    //   closeModal();
    // }
  };

  const openModal = (modalId: any) => {
    setActiveModalId(modalId);
    open();
  };

  const closeModal = () => {
    setActiveModalId(null);
    close();
  };

  useEffect(() => {
    if (formEmployee.values.id) {
      handleGetDataHierarchy("user", Number(formEmployee.values.id));
    }
  }, [formEmployee.values.id]);

  const value = {
    globalTab,
    setGlobalTab,
    UUID,
    setUUID,
    handleGetStructureDetail,
    handleSelectChange,
    tree,
    setTree,
    treeChildren,
    treeParent,
    foundEmployee,
    formUserStructureMapping,
    formUserJobCode,
    setFoundEmployee,
    formEmployee,
    getChild,
    getParent,
    isLoading,
    openedModal,
    openModal,
    closeModal,
    isOpen: (modalId: any) => openedModal && activeModalId === modalId,
    handleSubmitStructureMappingRequest,
    handleSubmitStructureMappingAssign,
  };

  return (
    <EmployeeDataContext.Provider value={value}>
      {children}
    </EmployeeDataContext.Provider>
  );
};

export const useEmployeeDataContext = () => {
  const context = useContext(EmployeeDataContext);
  if (!context) {
    throw new Error(
      "useEmployeeData must be used within an EmployeeDataProvider"
    );
  }
  return context;
};
