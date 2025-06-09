/* eslint-disable @typescript-eslint/no-explicit-any */
import type { NextApiRequest, NextApiResponse } from "next";

import { Employee } from ".";
import axiosInstance, { flattenData } from "../../../../utils/axios";
import { handlerMapEmployee } from "../../../../utils/apiUtils/employeeUtils";

type Data = {
  data?: Employee;
  message?: string;
  error?: string;
};

export default async function handler(
  req: NextApiRequest,
  res: NextApiResponse<Data>
) {
  try {
    const { uuid, type } = req.query;
    let response;
    let flattenedData;

    if (!uuid || typeof uuid !== "string") {
      return res
        .status(400)
        .json({ error: "uuid parameter is required and must be a string" });
    }

    switch (req.method) {
      case "GET":
        if (!type || !handlerMapEmployee[type as string]) {
          return res
            .status(400)
            .json({ message: "Invalid type, this type does not exist" });
        }

        response = await handlerMapEmployee[type as string](req, uuid);

        flattenedData = flattenData(response);

        return res.status(200).json({ data: flattenedData });

      case "PUT":
        if (!type || !handlerMapEmployee[type as string]) {
          return res
            .status(400)
            .json({ message: "Invalid type, this type does not exist" });
        }

        response = await handlerMapEmployee[type as string](req, uuid);

        return res.status(200).json({ data: flattenedData });

      case "DELETE":
        response = await axiosInstance.delete<Employee>(
          `admin/employee/delete/${uuid}`
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
