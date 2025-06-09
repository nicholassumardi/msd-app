/* eslint-disable @typescript-eslint/no-explicit-any */
import type { NextApiRequest, NextApiResponse } from "next";
import axiosInstance, { flattenData } from "../../../../utils/axios";

export type Dashboard = {
  totalUser: number;
  activeUser: number;
  inActiveUser: number;
  totalUserMale: number;
  totalUserFemale: number;
  totalUserPerCompany: number;
};

type Data = {
  data?: Dashboard;
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
        response = await axiosInstance.get<Dashboard>(
          "admin/dashboard",
          req.body
        );

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
