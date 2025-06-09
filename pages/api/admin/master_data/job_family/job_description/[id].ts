/* eslint-disable @typescript-eslint/no-explicit-any */
import type { NextApiRequest, NextApiResponse } from "next";
import axiosInstance, { flattenData } from "../../../../../../utils/axios";
import { JobDescription } from ".";

type Data = {
  data?: JobDescription;
  message?: string;
  error?: string;
};

export default async function handler(
  req: NextApiRequest,
  res: NextApiResponse<Data>
) {
  try {
    const { id } = req.query;
    let response;
    let flattenedData;

    if (!id || typeof id !== "string") {
      return res
        .status(400)
        .json({ error: "ID parameter is required and must be a string" });
    }

    switch (req.method) {
      case "GET":
        response = await axiosInstance.get<JobDescription>(
          `admin/master_data/job_family/job_description/show/${id}`
        );

        flattenedData = flattenData(response);

        return res.status(200).json({ data: flattenedData });

      case "PUT":
        response = await axiosInstance.put<JobDescription>(
          `admin/master_data/job_family/job_description/update/${id}`,
          req.body
        );

        flattenedData = flattenData(response);

        return res.status(200).json({ data: flattenedData });

      case "DELETE":
        response = await axiosInstance.delete<JobDescription>(
          `admin/master_data/job_family/job_description/delete/${id}`
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
