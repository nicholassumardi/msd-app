/* eslint-disable @typescript-eslint/no-explicit-any */
import type { NextApiRequest, NextApiResponse } from "next";
import axiosInstance, { flattenData } from "../../../../../utils/axios";
import { handlerMapDepartment } from "../../../../../utils/apiUtils/master_data/departmentUtils";

export type Department = {
  id: string;
  company_id: string;
  company_name: string;
  parent_id: string;
  parent_name: string;
  name: string;
  code: string;
};

type Data = {
  data?: Department;
  error?: string;
  message?: string;
};

export default async function handler(
  req: NextApiRequest,
  res: NextApiResponse<Data>
) {
  try {
    const { type } = req.query;
    let response;
    let flattenedData;

    switch (req.method) {
      case "GET":
        if (!type || !handlerMapDepartment[type as string]) {
          return res
            .status(400)
            .json({ message: "Invalid type, this type does not exist" });
        }

        response = await handlerMapDepartment[type as string](req);

        flattenedData = flattenData(response);

        return res.status(200).json({ data: flattenedData });

      case "POST":
        response = await axiosInstance.post<Department>(
          "admin/master_data/department/store",
          req.body
        );

        flattenedData = flattenData(response);

        return res.status(201).json({ data: flattenedData });

      default:
        return res.status(405).json({ message: "Method not allowed" });
    }
  } catch (error: any) {
    if (error.response && error.response.status == 422) {
      const validationErrors = error.response.data.errors;

      res.status(422).json({ error: validationErrors });
    } else {
      res.status(500).json({ error: "Failed to fetch data from Laravel API" });
    }
  }
}
