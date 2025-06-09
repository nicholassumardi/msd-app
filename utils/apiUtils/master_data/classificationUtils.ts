/* eslint-disable @typescript-eslint/no-explicit-any */
import { NextApiRequest } from "next";
import { Classification } from "../../../pages/api/admin/master_data/classification";
import axiosInstance from "../../axios";

const getAgeClassification = async () => {
  const response = await axiosInstance.get<Classification>(
    "admin/master_data/classification/show_age"
  );

  return response.data;
};

const getWorkingDurationClassification = async () => {
  const response = await axiosInstance.get<Classification>(
    "admin/master_data/classification/show_working_duration"
  );

  return response.data;
};

const getGeneralClassification = async () => {
  const response = await axiosInstance.get<Classification>(
    "admin/master_data/classification/show_general"
  );

  return response.data;
};

const postAgeClassification = async (req: NextApiRequest) => {
  const response = await axiosInstance.post<Classification>(
    "admin/master_data/classification/store_age",
    req.body
  );

  return response.data;
};

const postWorkingDurationClassification = async (req: NextApiRequest) => {
  const response = await axiosInstance.post<Classification>(
    "admin/master_data/classification/store_working_duration",
    req.body
  );

  return response.data;
};

const postGeneralClassification = async (req: NextApiRequest) => {
  const response = await axiosInstance.post<Classification>(
    "admin/master_data/classification/store_general",
    req.body
  );

  return response.data;
};

const showAgeClassification = async (id: string) => {
  const response = await axiosInstance.get<Classification>(
    `admin/master_data/classification/show_age/${id}`
  );

  return response.data;
};

const showWorkingDurationClassification = async (id: string) => {
  const response = await axiosInstance.get<Classification>(
    `admin/master_data/classification/show_working_duration/${id}`
  );

  return response.data;
};

const showGeneralClassification = async (id: string) => {
  const response = await axiosInstance.get<Classification>(
    `admin/master_data/classification/show_general/${id}`
  );

  return response.data;
};

const updateAgeClassification = async (req: NextApiRequest, id: string) => {
  const response = await axiosInstance.put<Classification>(
    `admin/master_data/classification/update_age/${id}`,
    req.body
  );

  return response.data;
};

const updateWorkingDurationClassification = async (
  req: NextApiRequest,
  id: string
) => {
  const response = await axiosInstance.put<Classification>(
    `admin/master_data/classification/update_working_duration/${id}`,
    req.body
  );

  return response.data;
};

const updateGeneralClassification = async (req: NextApiRequest, id: string) => {
  const response = await axiosInstance.put<Classification>(
    `admin/master_data/classification/update_general/${id}`,
    req.body
  );

  return response.data;
};

const deleteAgeClassification = async (id: string) => {
  const response = await axiosInstance.delete<Classification>(
    `admin/master_data/classification/delete_age/${id}`
  );

  return response.data;
};

const deleteWorkingDurationClassification = async (id: string) => {
  const response = await axiosInstance.delete<Classification>(
    `admin/master_data/classification/delete_working_duration/${id}`
  );

  return response.data;
};

const deleteGeneralClassification = async (id: string) => {
  const response = await axiosInstance.delete<Classification>(
    `admin/master_data/classification/delete_general/${id}`
  );

  return response.data;
};

export const handlerMap: Record<string, (req: NextApiRequest) => Promise<any>> =
  {
    ageClassification: getAgeClassification,
    workingDurationClassification: getWorkingDurationClassification,
    generalClassification: getGeneralClassification,
    postAgeClassification: (req) => postAgeClassification(req),
    postWorkingDurationClassification: (req) =>
      postWorkingDurationClassification(req),
    postGeneralClassification: (req) => postGeneralClassification(req),
  };

export const handlerMapDetails: Record<string, (id: string) => Promise<any>> = {
  showAgeClassification: (id) => showAgeClassification(id),
  showWorkingDurationClassification: (id) =>
    showWorkingDurationClassification(id),
  showGeneralClassification: (id) => showGeneralClassification(id),
  deleteAgeClassification: (id) => deleteAgeClassification(id),
  deleteWorkingDurationClassification: (id) =>
    deleteWorkingDurationClassification(id),
  deleteGeneralClassification: (id) => deleteGeneralClassification(id),
};

export const handlerMapPut: Record<
  string,
  (req: NextApiRequest, id: string) => Promise<any>
> = {
  updateAgeClassification: (req, id) => updateAgeClassification(req, id),
  updateWorkingDurationClassification: (req, id) =>
    updateWorkingDurationClassification(req, id),
  updateGeneralClassification: (req, id) =>
    updateGeneralClassification(req, id),
};
