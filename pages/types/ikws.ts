type MeetingContent = {
  meeting_date: Date | null;
  meeting_duration: number;
  revision_status: string;
};

type PositionCall = {
  position_call_number: string;
  field_operator: string;
};

export type RevisionForm = {
  id: string;
  visible: boolean;
  revisionNumber: number;
  values: {
    revision_no: number;
    reason: string;
    process_status: string;
    ikw_fix_status: string;
    confirmation: string;
    change_description: string;
    submission_no: string;
    submission_received_date: Date | null;
    submission_mr_date: Date | null;
    backoffice_return_date: Date | null;
    revision_status: string;
    print_date: Date | null;
    handover_date: Date | null;
    signature_mr_date: Date | null;
    distribution_date: Date | null;
    document_return_date: Date | null;
    document_disposal_date: Date | null;
    document_location_description: string;
    revision_description: string;
    status_check: string;
    position_calls: PositionCall[];
    meeting_contents: MeetingContent[];
  };
};

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
