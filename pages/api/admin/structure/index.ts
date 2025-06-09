/* eslint-disable @typescript-eslint/no-explicit-any */
import type { NextApiRequest, NextApiResponse } from "next";
import { flattenData } from "../../../../utils/axios";
import { handlerMapStructure } from "../../../../utils/apiUtils/structureUtils";
import { Employee } from "../employee";

export type Structure = {
  uuid: string;
  name: string;
  company_name: string;
  company_id: string;
  department_name: string;
  employee_number: string;
  department_id: string;
  roleCode: string;
  job_code: JobCode;
  user_job_code: UserJobCode[];
  StructureMapping: StrucutureMapping[];
  group: string;
  status: string;
  description: string;
  id_staff: string;
  id_structure: string;
  position_code: string;
  sub_position: string;
};

export interface StrucutureMapping {
  id: number;
  department_id: number;
  job_code_id: number;
  name: string;
  quota: number;
  created_at: Date;
  updated_at: Date;
  deleted_at: null;
  department: Department;
  job_code: JobCode;
  user_job_code: UserJobCode[];
}

export interface Department {
  id: number;
  company_id: number;
  parent_id: number;
  name: string;
  code: string;
  created_at: null;
  updated_at: null;
  deleted_at: null;
}

export interface JobCode {
  id: number;
  category_id: number;
  org_level: string;
  job_family: string;
  code: string;
  full_code: string;
  level: number;
  position: string;
  created_at: Date;
  updated_at: Date;
  deleted_at: null;
}

export interface UserJobCode {
  id: number;
  user_id: number;
  job_code_id: number;
  user_structure_mapping_id: number;
  id_structure: string;
  id_staff: string;
  position_code_structure: string;
  group: string;
  status: number;
  user: Employee;
}

export interface Hierarchy {
  id: number;
  name: string;
  level: number;
  desc: Desc[];
  relationship?: string;
  children: Hierarchy[];
}

export interface Desc {
  id: number;
  pic: string;
  job_code_id: null;
  id_structure: number;
  id_staff: number;
  position_code_structure: string;
  employee_type: string;
  group: string;
  assign_date: Date;
}

type Data = {
  data?: Structure;
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

    if (!type || !handlerMapStructure[type as string]) {
      return res
        .status(400)
        .json({ message: "Invalid type, this type does not exist" });
    }

    switch (req.method) {
      case "GET":
        response = await handlerMapStructure[type as string](req, null);

        flattenedData = flattenData(response);

        return res.status(200).json(flattenedData);

      case "POST":
        response = await handlerMapStructure[type as string](req, null);

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
