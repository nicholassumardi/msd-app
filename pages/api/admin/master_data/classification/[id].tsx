/* eslint-disable @typescript-eslint/no-explicit-any */
import type { NextApiRequest, NextApiResponse } from "next";
import { flattenData } from "../../../../../utils/axios";
import { Classification } from ".";
import {
  handlerMapDetails,
  handlerMapPut,
} from "../../../../../utils/apiUtils/master_data/classificationUtils";

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
    let response;
    let flattenedData;
    const { id, type } = req.query;

    if (!id || typeof id !== "string") {
      return res
        .status(400)
        .json({ error: "ID parameter is required and must be a string" });
    }

    if (
      !type ||
      (!handlerMapDetails[type as string] && !handlerMapPut[type as string])
    ) {
      return res
        .status(400)
        .json({ message: "Invalid type, this type does not exist" });
    }

    switch (req.method) {
      case "GET":
        response = await handlerMapDetails[type as string](id);

        flattenedData = flattenData(response);

        return res.status(200).json({ data: flattenedData });

      case "PUT":
        response = await handlerMapPut[type as string](req, id);

        flattenedData = flattenData(response);

        return res.status(200).json({ data: flattenedData });

      case "DELETE":
        response = await handlerMapDetails[type as string](id);

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
