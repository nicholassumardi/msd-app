/* eslint-disable @typescript-eslint/no-explicit-any */
import { NextApiRequest, NextApiResponse } from "next";
import axiosInstance, { flattenData } from "../../../../../../utils/axios";
import { handlerMapPehCode } from "../../../../../../utils/apiUtils/master_data/job_family/pehCodeUtils";

export type PehCode = {
  id: number;
  category_id: number;
  org_level: string;
  job_family: string;
  code: string;
  full_code: string;
  level: number;
  position: string;
};

type Data = {
  data?: PehCode;
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

    if (!type || !handlerMapPehCode[type as string]) {
      return res
        .status(400)
        .json({ message: "Invalid type, this type does not exist" });
    }

    switch (req.method) {
      case "GET":
        response = await handlerMapPehCode[type as string](req);

        flattenedData = flattenData(response);

        return res.status(200).json({ data: flattenedData });
      case "POST":
        response = axiosInstance.post<PehCode>(
          "admin/master_data/job_family/peh_code/store",
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
