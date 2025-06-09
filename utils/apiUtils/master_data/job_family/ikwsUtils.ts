/* eslint-disable @typescript-eslint/no-explicit-any */
import { NextApiRequest } from "next";
import axiosInstance from "../../../axios";
import { IKWS } from "../../../../pages/api/admin/master_data/job_family/ikws";

const showPagination = async (req: NextApiRequest) => {
  const { start, size, filters, globalFilter, sorting } = req.query;
  const response = await axiosInstance.get<IKWS>(
    "admin/master_data/job_family/ikws/show_ikw_pagination",
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

const store = async (req: NextApiRequest) => {
  const response = await axiosInstance.post<IKWS>(
    "admin/master_data/job_family/ikws/store",
    req.body
  );

  return response;
};

const show = async (id: string | null) => {
  const response = await axiosInstance.get<IKWS>(
    `admin/master_data/job_family/ikws/show/${id}`
  );

  return response;
};

const showAll = async () => {
  const response = await axiosInstance.get<IKWS>(
    "admin/master_data/job_family/ikws/show"
  );

  return response;
};

const showAllRevision = async () => {
  const response = await axiosInstance.get<IKWS["ikw_revisions"]>(
    "admin/master_data/job_family/ikws/show_revision"
  );

  return response;
};

const update = async (req: NextApiRequest, id: string | null) => {
  const response = await axiosInstance.put<IKWS>(
    `admin/master_data/job_family/ikws/update/${id}`,
    req.body
  );

  return response;
};

const destroy = async (id: string | null) => {
  const response = await axiosInstance.delete<IKWS>(
    `admin/master_data/job_family/ikws/delete/${id}`
  );

  return response;
};

export const handlerMapIkws: Record<
  string,
  (req: NextApiRequest, id: string | null) => Promise<any>
> = {
  showPagination: (req) => showPagination(req),
  store: (req) => store(req),
  showAll: () => showAll(),
  showAllRevision: () => showAllRevision(),
  show: (_, id) => show(id),
  update: (req, id) => update(req, id),
  destroy: (_, id) => destroy(id),
};
