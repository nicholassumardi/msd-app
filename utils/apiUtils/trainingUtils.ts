/* eslint-disable @typescript-eslint/no-explicit-any */
import { NextApiRequest } from "next";
import axiosInstance from "../axios";
import { Training } from "../../pages/api/admin/training";

const importTraining = async (req: NextApiRequest) => {
  const response = await axiosInstance.post<Training>(
    "admin/training/import",
    req,
    {
      headers: {
        "Content-Type": req.headers["content-type"],
      },
      responseType: "arraybuffer",
    }
  );

  return response;
};

const store = async (req: NextApiRequest) => {
  const response = await axiosInstance.post<Training>(
    "admin/training/store",
    req.body
  );

  return response;
};

const show = async (id: string | null) => {
  const response = await axiosInstance.get<Training>(
    `admin/training/show/${id}`
  );

  return response;
};

const showAll = async () => {
  const response = await axiosInstance.get<Training>(`admin/training/show`);

  return response;
};

const showTrainingPagination = async (req: NextApiRequest) => {
  const { start, size, filters, globalFilter, sorting } = req.query;
  const response = await axiosInstance.get<Training>(
    `admin/training/show_training_pagination`,
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

const getStructure = async (id: string | null) => {
  const response = await axiosInstance.get<Training>(
    `admin/training/show/${id}`
  );

  return response;
};

const updateStatus = async (id: string | null) => {
  const response = await axiosInstance.put<Training>(
    `admin/training/update_status/${id}`
  );

  return response;
};

const updateStatusActive = async (id: string | null) => {
  const response = await axiosInstance.put<Training>(
    `admin/training/update_status_active/${id}`
  );

  return response;
};

const update = async (req: NextApiRequest, uuid: string | null) => {
  const response = await axiosInstance.put<Training>(
    `admin/training/update/${uuid}`,
    req.body
  );

  return response;
};

const deleteTraining = async (id: string | null) => {
  const response = await axiosInstance.delete<Training>(
    `admin/training/delete/${id}`
  );

  return response;
};

export const handlerMapTraining: Record<
  string,
  (req: NextApiRequest, id: string | null) => Promise<any>
> = {
  importTraining: (req) => importTraining(req),
  store: (req) => store(req),
  showTrainingPagination: (req) => showTrainingPagination(req),
  show: (_, id) => show(id),
  showAll: () => showAll(),
  getStructure: (_, id) => getStructure(id),
  update: (req, id) => update(req, id),
  updateStatus: (_, id) => updateStatus(id),
  updateStatusActive: (_, id) => updateStatusActive(id),
  deleteTraining: (_, id) => deleteTraining(id),
};
