/* eslint-disable @typescript-eslint/no-explicit-any */
import { NextApiRequest, NextApiResponse } from "next";
import { flattenData } from "../../../../../../utils/axios";
import { handlerMapIkws } from "../../../../../../utils/apiUtils/master_data/job_family/ikwsutils";

export type IKWS = {
  id: string;
  job_task_id: string;
  department_id: string;
  department_name: string;
  code: string;
  name: string;
  total_page: number;
  registration_date: string;
  print_by_back_office_date: string;
  submit_to_department_date: string;
  ikw_return_date: string;
  ikw_creation_duration: number;
  status_document: string;
  last_update_date: string;
  description: string;
  ikw_revisions: IKWRevision[];
};

type IKWRevision = {
  ikw_id: string;
  revision_no: string;
  reason: string;
  process_status: string;
  ikw_fix_status: string;
  confirmation: string;
  change_description: string;
  submission_no: string;
  submission_received_date: string;
  submission_mr_date: string;
  backoffice_return_date: string;
  revision_status: string;
  print_date: string;
  handover_date: string;
  signature_mr_date: string;
  distribution_date: string;
  document_return_date: string;
  document_disposal_date: string;
  document_location_description: string;
  revision_description: string;
  status_check: string;
  ikw_meetings: IKWMeeting[];
  ikw_positions: IKWPosition[];
};

type IKWMeeting = {
  ikw_revision_id: string;
  department_id: string;
  no_revision: string;
  ikw_code: string;
  meeting_date: string;
  meeting_duration: string;
  revision_status: string;
};

type IKWPosition = {
  ikw_revision_id: string;
  department_id: string;
  no_revision: string;
  ikw_code: string;
  position_call_number: string;
  field_operator: string;
};

type Data = {
  data?: IKWS;
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

    if (!type || !handlerMapIkws[type as string]) {
      return res
        .status(400)
        .json({ message: "Invalid type, this type does not exist" });
    }

    switch (req.method) {
      case "GET":
        response = await handlerMapIkws[type as string](req, null);

        flattenedData = flattenData(response);

        return res.status(200).json({ data: flattenedData });
      case "POST":
        response = await handlerMapIkws[type as string](req, null);

        flattenedData = await flattenData(response);

        return res.status(201).json({ data: flattenedData });

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
