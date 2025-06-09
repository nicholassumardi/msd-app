/* eslint-disable @typescript-eslint/no-explicit-any */
import type { NextApiRequest, NextApiResponse } from "next";
import axiosInstance from "../../../../../utils/axios";
import { Buffer } from "node:buffer";

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
    const { uuid } = req.query;
    let response;

    switch (req.method) {
      case "POST":
        response = await axiosInstance.post(`admin/rki/import`, req, {
          headers: {
            "Content-Type": req.headers["content-type"],
          },
        });

        res.status(201).json({ data: response.data });

      case "GET":
        response = await axiosInstance.get(`admin/rki/export`, {
          params: {
            uuid: uuid,
          },
          headers: {
            "Content-Type": req.headers["content-type"],
          },
          responseType: "arraybuffer",
        });

        res.setHeader(
          "Content-Type",
          "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
        );
        res.setHeader(
          "Content-Disposition",
          'attachment; filename="msd-data-karyawan.xlsx"'
        );

        res.status(200).send(Buffer.from(response.data));
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
