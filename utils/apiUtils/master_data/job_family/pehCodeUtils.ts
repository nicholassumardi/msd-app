/* eslint-disable @typescript-eslint/no-explicit-any */
import { NextApiRequest } from "next";
import axiosInstance from "../../../axios";
import { PehCode } from "../../../../pages/api/admin/master_data/job_family/peh_code";

const showPagination = async (req: NextApiRequest) => {
  const { start, size, filters, globalFilter, sorting } = req.query;
  const response = await axiosInstance.get<PehCode>(
    "admin/master_data/job_family/peh_code/show_job_code_pagination",
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

const show = async () => {
  const response = await axiosInstance.get<PehCode>(
    "admin/master_data/job_family/peh_code/show"
  );

  return response;
};

export const handlerMapPehCode: Record<
  string,
  (req: NextApiRequest) => Promise<any>
> = {
  showPagination: (req) => showPagination(req),
  show: () => show(),
};
