/* eslint-disable @typescript-eslint/no-explicit-any */
import { NextApiRequest } from "next";
import axiosInstance from "../../../axios";
import { Category } from "../../../../pages/api/admin/master_data/job_family/category";

const showPagination = async (req: NextApiRequest) => {
  const { start, size, filters, globalFilter, sorting } = req.query;
  const response = await axiosInstance.get<Category>(
    "admin/master_data/job_family/category/show_category_pagination",
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

const showAll = async () => {
  const response = await axiosInstance.get<Category>(
    "admin/master_data/job_family/category/show"
  );

  return response;
};

const show = async (id: string | null) => {
  const response = await axiosInstance.get<Category>(
    `admin/master_data/job_family/category/show/${id}`
  );

  return response;
};

const store = async (req: NextApiRequest) => {
  const response = await axiosInstance.post<Category>(
    "admin/master_data/job_family/category/store",
    req.body
  );
  return response;
};

export const handlerMapCategory: Record<
  string,
  (req: NextApiRequest, id: string | null) => Promise<any>
> = {
  showPagination: (req) => showPagination(req),
  showAll: () => showAll(),
  show: (_, id) => show(id),
  store: (req) => store(req),
};
