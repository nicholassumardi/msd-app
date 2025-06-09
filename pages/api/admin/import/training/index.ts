/* eslint-disable @typescript-eslint/no-explicit-any */
import type { NextApiRequest, NextApiResponse } from "next";
import { handlerMapTraining } from "../../../../../utils/apiUtils/trainingUtils";

type Data =
  | {
      data?: any;
      message?: string;
      error?: string;
    }
  | Buffer;

export const config = {
  api: {
    bodyParser: false,
  },
};

export default async function handler(
  req: NextApiRequest,
  res: NextApiResponse<Data>
) {
  try {
    const { type } = req.query;
    let response;
    let contentType;

    if (!type || !handlerMapTraining[type as string]) {
      return res
        .status(400)
        .json({ message: "Invalid type, this type does not exist" });
    }

    switch (req.method) {
      case "POST":
        response = await handlerMapTraining[type as string](req, null);
        contentType = response.headers["content-type"] ?? "";
        if (!contentType.includes("application/json")) {
          res.setHeader(
            "Content-Type",
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
          );
          res.setHeader(
            "Content-Disposition",
            'attachment; filename="msd-data-karyawan.xlsx"'
          );

          res.status(201).send(Buffer.from(response.data));
        }

        res.status(201).json({ data: response.data });

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
