import { Company } from "./company";

type UserEmployeeNumbers = {
  id: string;
  user_id: string;
  employee_number: string;
  registry_date: string;
  status: string;
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
