/* eslint-disable @typescript-eslint/no-explicit-any */
import type { NextApiRequest, NextApiResponse } from "next";
import { flattenData } from "../../../../../../utils/axios";
import { IKWS } from ".";
import { handlerMapIkws } from "../../../../../../utils/apiUtils/master_data/job_family/ikwsutils";

type Data = {
  data?: IKWS;
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

    if (!type || !handlerMapIkws[type as string]) {
      return res
        .status(400)
        .json({ message: "Invalid type, this type does not exist" });
    }

    if (!id || typeof id !== "string") {
      return res
        .status(400)
        .json({ error: "ID parameter is required and must be a string" });
    }

    switch (req.method) {
      case "GET":
        response = await handlerMapIkws[type as string](req, id);

        flattenedData = flattenData(response);

        return res.status(200).json({ data: flattenedData });

      case "PUT":
        response = await handlerMapIkws[type as string](req, id);

        flattenedData = flattenData(response);

        return res.status(200).json({ data: flattenedData });

      case "DELETE":
        response = await handlerMapIkws[type as string](req, id);

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
