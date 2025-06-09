/* eslint-disable @typescript-eslint/no-explicit-any */
import { NextApiRequest } from "next";
import axiosInstance from "../axios";
import { RKI } from "../../pages/api/admin/rki";

const store = async (req: NextApiRequest) => {
  const response = await axiosInstance.post<RKI>("admin/rki/store", req.body);

  return response;
};

const show = async (id: string | null) => {
  const response = await axiosInstance.get<RKI>(`admin/rki/show/${id}`);

  return response;
};

const showAll = async () => {
  const response = await axiosInstance.get<RKI>(`admin/rki/show`);

  return response;
};

const showRKIByPositionJobCode = async (req: NextApiRequest) => {
  const { position_job_code } = req.query;
  const response = await axiosInstance.get<RKI>(
    `admin/rki/show_by_position_job_code`,
    {
      params: {
        position_job_code: position_job_code,
      },
    }
  );

  return response;
};

const showRKIPagination = async (req: NextApiRequest) => {
  const { start, size, filters, globalFilter, sorting } = req.query;
  const response = await axiosInstance.get<RKI>(
    `admin/rki/show_rki_pagination`,
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

const update = async (req: NextApiRequest, id: string | null) => {
  const response = await axiosInstance.put<RKI>(
    `admin/rki/update/${id}`,
    req.body
  );

  return response;
};

const destroy = async (id: string | null) => {
  const response = await axiosInstance.delete<RKI>(`admin/rki/delete/${id}`);

  return response;
};

export const handlerMapRki: Record<
  string,
  (req: NextApiRequest, id: string | null) => Promise<any>
> = {
  store: (req) => store(req),
  show: (_, id) => show(id),
  showAll: () => showAll(),
  showRKIPagination: (req) => showRKIPagination(req),
  showRKIByPositionJobCode: (req) => showRKIByPositionJobCode(req),
  update: (req, id) => update(req, id),
  destroy: (_, id) => destroy(id),
};
