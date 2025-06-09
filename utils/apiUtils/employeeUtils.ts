/* eslint-disable @typescript-eslint/no-explicit-any */
import { NextApiRequest } from "next";
import { Employee } from "../../pages/api/admin/employee";
import axiosInstance from "../axios";

const storeEmployee = async (req: NextApiRequest) => {
  const response = await axiosInstance.post<Employee>(
    "admin/employee/store",
    req.body
  );

  return response;
};

const showEmployeePagination = async (req: NextApiRequest) => {
  const {
    start,
    size,
    filters,
    globalFilter,
    sorting,
    id_department,
    id_company,
  } = req.query;
  const response = await axiosInstance.get<Employee>(
    `admin/employee/show_user_pagination`,
    {
      params: {
        start: start,
        size: size,
        filters: filters,
        globalFilter: globalFilter,
        sorting: sorting,
        id_department: id_department,
        id_company: id_company,
      },
    }
  );

  return response;
};

const show = async (uuid: string | null) => {
  const response = await axiosInstance.get<Employee>(
    `admin/employee/show/${uuid}`
  );

  return response;
};

const showAll = async (req: NextApiRequest) => {
  const { id_company, id_department } = req.query;
  const response = await axiosInstance.get<Employee>(`admin/employee/show`, {
    params: {
      id_company: id_company,
      id_department: id_department,
    },
  });

  return response;
};

const showByDepartment = async (id: string | null) => {
  const response = await axiosInstance.get<Employee>(
    `admin/employee/show_by_department/${id}`
  );

  return response;
};

const importUpdateEmployee = async (req: NextApiRequest) => {
  const response = await axiosInstance.post<Employee>(
    `admin/employee/import_update`,
    req.body
  );

  return response;
};

const updateStatus = async (req: NextApiRequest, id: string | null) => {
  const response = await axiosInstance.put<Employee["employee_numbers"]>(
    `admin/employee/update_status/${id}`,
    req.body
  );

  return response;
};

const update = async (req: NextApiRequest, uuid: string | null) => {
  const response = await axiosInstance.put<Employee>(
    `admin/employee/update/${uuid}`,
    req.body
  );

  return response;
};

export const handlerMapEmployee: Record<
  string,
  (req: NextApiRequest, uuid: string | null) => Promise<any>
> = {
  storeEmployee: (req) => storeEmployee(req),
  show: (_, uuid) => show(uuid),
  showAll: (req) => showAll(req),
  showByDepartment: (_, uuid) => showByDepartment(uuid),
  showEmployeePagination: (req) => showEmployeePagination(req),
  importUpdateEmployee: (req) => importUpdateEmployee(req),
  updateStatus: (req, uuid) => updateStatus(req, uuid),
  update: (req, uuid) => update(req, uuid),
};
