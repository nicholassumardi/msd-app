/* eslint-disable @typescript-eslint/no-explicit-any */
import type { NextApiRequest, NextApiResponse } from "next";
import { flattenData } from "../../../../../utils/axios";
import { handlerMap } from "../../../../../utils/apiUtils/master_data/classificationUtils";

export type Classification = {
  id: string;
  rule: string;
  label: string;
};

type Data = {
  data?: Classification;
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

    if (!type || !handlerMap[type as string]) {
      return res
        .status(400)
        .json({ message: "Invalid type, this type does not exist" });
    }

    switch (req.method) {
      case "GET":
        response = await handlerMap[type as string](req);

        flattenedData = flattenData(response);

        return res.status(200).json({ data: flattenedData });

      case "POST":
        response = await handlerMap[type as string](req);

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
