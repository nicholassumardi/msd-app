/* eslint-disable @typescript-eslint/no-explicit-any */
import type { NextApiRequest, NextApiResponse } from "next";
import { flattenData } from "../../../../utils/axios";
import { handlerMapRki } from "../../../../utils/apiUtils/rkiUtils";

export type RKI = {
  id: string;
  unique_code: string;
  position_job_code: string;
  no_ikw: string;
  ikw_name: string;
  ikw_page: string;
  department: string;
  training_time: string;
};

type Data = {
  data?: RKI;
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

    if (!type || !handlerMapRki[type as string]) {
      return res
        .status(400)
        .json({ message: "Invalid type, this type does not exist" });
    }

    switch (req.method) {
      case "GET":
        response = await handlerMapRki[type as string](req, null);

        flattenedData = flattenData(response);

        return res.status(200).json(flattenedData);

      case "POST":
        response = await handlerMapRki[type as string](req, null);

        flattenedData = flattenData(response);

        return res.status(201).json(flattenedData);

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
