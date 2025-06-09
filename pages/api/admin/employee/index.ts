/* eslint-disable @typescript-eslint/no-explicit-any */
import type { NextApiRequest, NextApiResponse } from "next";
import { flattenData } from "../../../../utils/axios";
import { handlerMapEmployee } from "../../../../utils/apiUtils/employeeUtils";

export type Employee = {
  id: string;
  uuid: string;
  name: string;
  company_id: string;
  companies: Company[];
  department_id: string;
  company_name: string;
  department_name: string;
  employee_number: number;
  employee_numbers?: UserEmployeeNumbers[];
  date_of_birth: string;
  identity_card: string;
  unicode: string;
  gender: string;
  religion: string;
  email: string;
  photo: string;
  education: string;
  status: string;
  marital_status: string;
  address: string;
  phone: number;
  employee_type: string;
  section: string;
  position_code: string;
  roleCode: string;
  roleCodes: RoleCode[];
  status_twiji: string;
  schedule_type: string;
  user_certificates?: UserCertificate[];
  join_date: string;
  age: string;
  year: string;
  service_year: string;
  age_classification: string;
  general_classification: string;
  working_duration_classification: string;
};

type UserEmployeeNumbers = {
  id: string;
  user_id: string;
  employee_number: string;
  registry_date: string;
  status: string;
};

type RoleCode = {
  id: string;
  user_id: string;
  job_code_id: string;
  group: string;
  description: string;
  status: string;
  job_code: {
    department_id: string;
    full_code: string;
  };
};

type UserCertificate = {
  id: string;
  name: string;
  pivot: {
    user_id: string;
    certificate_id: string;
    description: string;
    expiration_date: string;
  };
};

type Company = {
  id: string;
  name: string;
  code: string;
  unique_code: string;
};

type Data = {
  data?: Employee;
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

    if (!type || !handlerMapEmployee[type as string]) {
      return res
        .status(400)
        .json({ message: "Invalid type, this type does not exist" });
    }

    switch (req.method) {
      case "GET":
        response = await handlerMapEmployee[type as string](req, null);

        flattenedData = flattenData(response);

        return res.status(200).json(flattenedData);

      case "POST":
        response = await handlerMapEmployee[type as string](req, null);

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
