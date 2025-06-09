/* eslint-disable @typescript-eslint/no-explicit-any */
import type { NextApiRequest, NextApiResponse } from "next";
import { flattenData } from "../../../../utils/axios";
import { handlerMapEvaluation } from "../../../../utils/evaluationUtils";

export type Evaluation = {
  id: string;
  name: string;
  nip: number;
  roleCode: string;
  group: string;
  department: string;
  identity_card: string;
  role_position_code: string;
};

export type EligibleIKWTrainer = {
  id: string;
  job_task_id: string;
  department_id: string;
  code: string;
  name: string;
  total_page: string;
  registration_date: string;
  print_by_back_office_date: string;
  submit_to_department_date: string;
  ikw_return_date: string;
  ikw_creation_duration: string;
  status_document: string;
  last_update_date: string;
  description: string;
};

type Data = {
  data?: Evaluation;
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

    if (!type || !handlerMapEvaluation[type as string]) {
      return res
        .status(400)
        .json({ message: "Invalid type, this type does not exist" });
    }

    switch (req.method) {
      case "GET":
        response = await handlerMapEvaluation[type as string](req, null);

        flattenedData = flattenData(response);

        return res.status(200).json(flattenedData);

      case "POST":
        response = await handlerMapEvaluation[type as string](req, null);

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
