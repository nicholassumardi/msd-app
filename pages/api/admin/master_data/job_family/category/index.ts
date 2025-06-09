/* eslint-disable @typescript-eslint/no-explicit-any */
import type { NextApiRequest, NextApiResponse } from "next";
import { flattenData } from "../../../../../../utils/axios";
import { handlerMapCategory } from "../../../../../../utils/apiUtils/master_data/job_family/categoryUtils";
import { JobCode } from "../../../structure";

export type Category = {
  id: string;
  name: string;
  job_code: JobCode[];
};

type Data = {
  data?: Category;
  message?: string;
  error?: string;
};

export default async function handler(
  req: NextApiRequest,
  res: NextApiResponse<Data>
) {
  try {
    const { type } = req.query;
    let response;

    let flattenedData;
    if (!type || !handlerMapCategory[type as string]) {
      return res
        .status(400)
        .json({ message: "Invalid type, this type does not exist" });
    }
    switch (req.method) {
      case "GET":
        response = await handlerMapCategory[type as string](req);

        flattenedData = flattenData(response);

        return res.status(200).json({ data: flattenedData });

      case "POST":
        response = await handlerMapCategory[type as string](req);

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
