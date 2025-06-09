/* eslint-disable @typescript-eslint/no-explicit-any */

import { useForm } from "@mantine/form";
import { createContext, ReactNode, useContext } from "react";
import { RevisionForm } from "../../pages/types/ikws";
import axios from "axios";
import SuccessNotification from "@/components/Notifications/SuccessNotification";
import ErrorNotification from "@/components/Notifications/ErrorNotification";

type IKWDataContextType = {
  formIKW: any;
  formRKI: any;
};

const IKWDataContext = createContext<IKWDataContextType | undefined>(undefined);

export const IKWDataProvider = ({ children }: { children: ReactNode }) => {
  const formIKW = useForm({
    initialValues: {
      job_task_id: "",
      department_id: "",
      name: "",
      code: "",
      total_page: 0,
      registration_date: null as Date | null,
      print_by_back_office_date: null as Date | null,
      submit_to_department_date: null as Date | null,
      ikw_return_date: null as Date | null,
      ikw_creation_duration: 0,
      status_document: "",
      last_update_date: null as Date | null,
      description: "",
      revisions: [] as Array<RevisionForm["values"]>,
    },
    validate: {
      code: (value) => (!value ? "code cannot be empty" : null),
      name: (value) => (!value ? "name cannot be empty" : null),
    },
  });
  const formRKI = useForm({
    initialValues: {
      position_job_code: "",
      ikw_id: "",
      training_time: 0,
      ikws: [] as string[],
    },
    validate: {
      position_job_code: (value) => (!value ? "Please select position" : null),
      ikw_id: () => null,
      // mode === "PUT" ? (!value ? "Please select IKW" : null) : null,
      ikws: () => null,
      // mode === "POST"
      //   ? Array.isArray(value) && value.length == 0
      //     ? "Please select IKW"
      //     : null
      //   : null,
    },
  });

  const handleGetDataDetailIKW = async (id: string) => {
    try {
      const response = await axios.get(
        `/api/admin/master_data/job_family/ikws/${id}?type=show`
      );

      // console.log(response.data.data.data);
      const dataRevisions = response.data.data.data.ikw_revisions.map(
        (revision: any) => ({
          id: Math.random().toString(36).substr(2, 9),
          visible: false,
          revisionNumber: revision.revision_no,
          values: {
            revision_no: revision.revision_number,
            reason: revision.reason,
            process_status: revision.process_status.toString(),
            ikw_fix_status: revision.ikw_fix_status.toString(),
            confirmation: revision.confirmation,
            change_description: revision.change_description,
            submission_no: revision.submission_no,
            submission_received_date: revision.submission_received_date
              ? new Date(revision.submission_received_date)
              : null,
            submission_mr_date: revision.submission_mr_date
              ? new Date(revision.submission_mr_date)
              : null,
            backoffice_return_date: revision.backoffice_return_date
              ? new Date(revision.backoffice_return_date)
              : null,
            revision_status: revision.revision_status.toString(),
            print_date: revision.print_date
              ? new Date(revision.print_date)
              : null,
            handover_date: revision.handover_date
              ? new Date(revision.handover_date)
              : null,
            signature_mr_date: revision.signature_mr_date
              ? new Date(revision.signature_mr_date)
              : null,
            distribution_date: revision.distribution_date
              ? new Date(revision.distribution_date)
              : null,
            document_return_date: revision.document_return_date
              ? new Date(revision.document_return_date)
              : null,
            document_disposal_date: revision.document_disposal_date
              ? new Date(revision.document_disposal_date)
              : null,
            document_location_description:
              revision.document_location_description,
            revision_description: revision.revision_description,
            status_check: revision.status_check.toString(),
            position_calls: revision.ikw_position.map((pc: any) => ({
              position_call_number: pc.position_call_number ?? null,
              field_operator: pc.field_operator ?? null,
            })),
            meeting_contents: revision.ikw_meeting.map((mc: any) => ({
              meeting_date: mc.meeting_date ? new Date(mc.meeting_date) : null,
              meeting_duration: mc.meeting_duration ?? null,
              revision_status: mc.revision_status ?? null,
            })),
          },
        })
      );

      formIKW.setValues({
        job_task_id: response.data.data.data.job_task_id.toString(),
        department_id: response.data.data.data.department_id.toString(),
        name: response.data.data.data.name,
        code: response.data.data.data.code,
        total_page: response.data.data.data.total_page,
        registration_date: response.data.data.data.registration_date
          ? new Date(response.data.data.data.registration_date)
          : null,
        print_by_back_office_date: response.data.data.data
          .print_by_back_office_date
          ? new Date(response.data.data.data.print_by_back_office_date)
          : null,
        submit_to_department_date: response.data.data.data
          .submit_to_department_date
          ? new Date(response.data.data.data.submit_to_department_date)
          : null,
        ikw_return_date: response.data.data.data.ikw_return_date
          ? new Date(response.data.data.data.ikw_return_date)
          : null,
        ikw_creation_duration: response.data.data.data.ikw_creation_duration,
        status_document: response.data.data.data.status_document,
        last_update_date: response.data.data.data.last_update_date
          ? new Date(response.data.data.data.last_update_date)
          : null,
        description: response.data.data.data.description,
        revisions: dataRevisions.map((r: RevisionForm) => r.values),
      });

      const maxRevision = Math.max(
        ...dataRevisions.map((r: any) => r.revisionNumber),
        0
      );
      setNextRevisionNumber(maxRevision + 1);
      setForms(dataRevisions);
    } catch (err: any) {
      if (err.response) {
        ErrorNotification({
          title: "Server Error",
          message: err.response.data.error,
        });
      }
    }
  };

  const handleSubmitIKW = async (values: any) => {
    try {
      if (mode === "PUT") {
        const response = await axios.put(
          `/api/admin/master_data/job_family/ikws/${id_ikw}?type=update`,
          values
        );
        if (response.status === 200) {
          SuccessNotification({
            title: "Success",
            message: "IKWS data successfully updated",
          });
          close();
        }
      } else {
        const response = await axios.post(
          "/api/admin/master_data/job_family/ikws?type=store",
          values
        );
        if (response.status === 201) {
          SuccessNotification({
            title: "Success",
            message: "IKWS data successfully created",
          });
          close();
        }
      }
    } catch (err: any) {
      if (err.response && err.response.status == 422) {
        ErrorNotification({
          title: "Server Error",
          message: err.response.data.error,
        });
      } else {
        ErrorNotification({
          title: "Server Error",
          message: "500 Internal Server Error",
        });
      }
    }
  };

  const value = {
    formIKW,
    formRKI,
  };

  return (
    <IKWDataContext.Provider value={value}>{children}</IKWDataContext.Provider>
  );
};

export const useIKWDataContext = () => {
  const context = useContext(IKWDataContext);
  if (!context) {
    throw new Error("useIKWDataContext must be used within an IKWDataProvider");
  }
  return context;
};
