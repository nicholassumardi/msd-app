/* eslint-disable @typescript-eslint/no-explicit-any */
import { NextApiRequest, NextApiResponse } from "next";
import axiosInstance, { flattenData } from "../../../../../../utils/axios";

export type JobDescription = {
  id: string;
  job_code_id: string;
  job_code_code: string;
  code: string;
  description: string;
};

type Data = {
  data?: JobDescription;
  error?: string;
  message?: string;
};

export default async function handler(
  req: NextApiRequest,
  res: NextApiResponse<Data>
) {
  try {
    let response;
    let flattenedData;

    switch (req.method) {
      case "GET":
        response = await axiosInstance.get<JobDescription>(
          "admin/master_data/job_family/job_description/show"
        );

        flattenedData = flattenData(response);

        return res.status(200).json({ data: flattenedData });
      case "POST":
        response = axiosInstance.post<JobDescription>(
          "admin/master_data/job_family/job_description/store",
          req.body
        );

        flattenedData = await flattenData(response);

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
