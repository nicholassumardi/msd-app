/* eslint-disable @typescript-eslint/no-explicit-any */
import type { NextApiRequest, NextApiResponse } from "next";
import { flattenData } from "../../../../utils/axios";
import { handlerMapTraining } from "../../../../utils/apiUtils/trainingUtils";
import { Employee } from "../employee";

export type Training = {
  id: string;
  no_training: number;
  nip_ikw_trainee: string;
  trainee_id: number;
  trainer_id: number;
  trainee: Employee[];
  trainer: Employee[];
  assessor: Employee[];
  assessor_id: number;
  ikw_revision_id: number;
  trainee_name: string;
  trainer_name: string;
  assessor_name: string;
  trainee_identity_card: number;
  trainer_identity_card: number;
  assessor_identity_card: number;
  nip_trainee: string;
  nip_trainer: string;
  nip_assessor: string;
  trainee_department: string;
  trainer_department: string;
  assessor_department: string;
  role_position_code_trainee: string;
  ikw_name: string;
  ikw_revision: string;
  ikw_module_no: number;
  training_plan_date: Date;
  training_realisation_date: Date;
  training_duration: number;
  ticket_return_date: Date;
  assessment_plan_date: Date;
  assessment_realisation_date: Date;
  assessment_duration: number;
  status_fa_print: number;
  assessment_result: string;
  status: string;
  description: string;
  status_active: string;
};

type Data = {
  data?: Training;
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

    if (!type || !handlerMapTraining[type as string]) {
      return res
        .status(400)
        .json({ message: "Invalid type, this type does not exist" });
    }

    switch (req.method) {
      case "GET":
        response = await handlerMapTraining[type as string](req, null);

        flattenedData = flattenData(response);

        return res.status(200).json(flattenedData);

      case "POST":
        response = await handlerMapTraining[type as string](req, null);

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
