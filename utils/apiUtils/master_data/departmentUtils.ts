/* eslint-disable @typescript-eslint/no-explicit-any */
import axiosInstance from "../../axios";
import { Department } from "../../../pages/api/admin/master_data/department";
import { NextApiRequest } from "next";

const show = async (id: string | null) => {
  const response = await axiosInstance.get<Department>(
    `admin/master_data/department/show/${id}`
  );

  return response.data;
};

const showAll = async () => {
  const response = await axiosInstance.get<Department>(
    "admin/master_data/department/show"
  );

  return response.data;
};

const showByCompany = async (id: string | null) => {
  const response = await axiosInstance.get<Department>(
    `admin/master_data/department/show_by_company/${id}`
  );

  return response.data;
};

const showPagination = async (req: NextApiRequest) => {
  const { start, size, filters, globalFilter, sorting } = req.query;
  const response = await axiosInstance.get<Department>(
    "admin/master_data/department/show_department_pagination",
    {
      params: {
        start: start,
        size: size,
        filters: filters,
        globalFilter: globalFilter,
        sorting: sorting,
      },
    }
  );

  return response;
};

const showParent = async () => {
  const response = await axiosInstance.get<Department>(
    "admin/master_data/department/show_parent"
  );

  return response.data;
};

export const handlerMapDepartment: Record<
  string,
  (req: NextApiRequest, id: string | null) => Promise<any>
> = {
  show: (_, id) => show(id),
  showAll: () => showAll(),
  showPagination: (req) => showPagination(req),
  showByCompany: (_, id) => showByCompany(id),
  showParent: () => showParent(),
};
