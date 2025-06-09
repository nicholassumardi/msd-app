/* eslint-disable @typescript-eslint/no-explicit-any */
import type { NextApiRequest, NextApiResponse } from "next";
import axiosInstance, { flattenData } from "../../../../../utils/axios";
import { Department } from ".";
import { handlerMapDepartment } from "../../../../../utils/apiUtils/master_data/departmentUtils";

type Data = {
  data?: Department;
  message?: string;
  error?: string;
};

export default async function handler(
  req: NextApiRequest,
  res: NextApiResponse<Data>
) {
  try {
    const { id, type } = req.query;
    let response;
    let flattenedData;

    if (!id || typeof id !== "string") {
      return res
        .status(400)
        .json({ error: "id parameter is required and must be a string" });
    }

    switch (req.method) {
      case "GET":
        if (!type || !handlerMapDepartment[type as string]) {
          return res
            .status(400)
            .json({ message: "Invalid type, this type does not exist" });
        }

        response = await handlerMapDepartment[type as string](req, id);

        flattenedData = flattenData(response);

        return res.status(200).json({ data: flattenedData });

      case "PUT":
        response = await axiosInstance.put<Department>(
          `admin/master_data/department/update/${id}`,
          req.body
        );

        flattenedData = flattenData(response);

        return res.status(200).json({ data: flattenedData });

      case "DELETE":
        response = await axiosInstance.delete<Department>(
          `admin/master_data/department/delete/${id}`
        );

        flattenedData = flattenData(response);

        return res.status(200).json({ data: flattenedData });

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
