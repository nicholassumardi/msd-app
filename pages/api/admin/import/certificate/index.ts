/* eslint-disable @typescript-eslint/no-explicit-any */
import type { NextApiRequest, NextApiResponse } from "next";
import axiosInstance from "../../../../../utils/axios";

type Data = {
  data?: any;
  message?: string;
  error?: string;
};

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
    let response;

    switch (req.method) {
      case "POST":
        response = await axiosInstance.post(
          `admin/master_data/certificate/import`,
          req,
          {
            headers: {
              "Content-Type": req.headers["content-type"],
            },
          }
        );

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
