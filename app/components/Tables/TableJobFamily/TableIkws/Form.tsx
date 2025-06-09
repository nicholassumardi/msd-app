/* eslint-disable @typescript-eslint/no-explicit-any */
import React, { useEffect, useState } from "react";
import { useDisclosure } from "@mantine/hooks";
import {
  ActionIcon,
  Button,
  Modal,
  rem,
  ScrollArea,
  Tabs,
  Text,
} from "@mantine/core";
import {
  IconEditCircle,
  IconFileInfo,
  IconFilePencil,
} from "@tabler/icons-react";
import axios from "axios";
import SuccessNotification from "@/components/Notifications/SuccessNotification";
import ErrorNotification from "@/components/Notifications/ErrorNotification";
import { useForm } from "@mantine/form";
import { option } from "../../../../../pages/types/option";
import RevisionForm from "./RevisionForm";
import IKWRevisionForm from "./RevisionForm";
import IkwForm from "./IkwForm";

interface FormComponent {
  id?: string;
  getData: () => Promise<void>;
  setIsLoading: React.Dispatch<React.SetStateAction<boolean>>;
  dataJobTask: option[];
  dataDepartment: option[];
}

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

const FormIkws: React.FC<FormComponent> = ({
  id,
  getData,
  setIsLoading,
  dataJobTask,
  dataDepartment,
}) => {
  const [mode, setMode] = useState("POST");
  const [error, setError] = useState<{ [key: string]: string }>({});
  const [opened, { open, close }] = useDisclosure(false);
  const [forms, setForms] = useState<RevisionForm[]>([]);
  const [nextRevisionNumber, setNextRevisionNumber] = useState(0);
  const iconStyle = { width: rem(35), height: rem(35) };

  const form = useForm({
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

  const handleGetDataDetail = async () => {
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

      console.log(dataRevisions);
      form.setValues({
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
        setError(err.response.data.message);
      }
    }
  };

  const handleCreateData = () => {
    open();
  };

  const handleEditData = () => {
    handleGetDataDetail();
    open();
  };

  useEffect(() => {
    const handleMode = () => {
      if (id) {
        setMode("PUT");
      } else {
        setMode("POST");
      }
    };
    handleMode();
  }, [id]);

  const handleCloseModal = () => {
    setError({});
    close();
  };

  const handleSubmit = async (values: any) => {
    try {
      if (mode === "PUT") {
        const response = await axios.put(
          `/api/admin/master_data/job_family/ikws/${id}?type=update`,
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
      setIsLoading(true);
      setInterval(() => {
        getData();
      }, 1500);
    } catch (err: any) {
      if (err.response && err.response.status == 422) {
        setError(err.response.data.error);
      } else {
        ErrorNotification({
          title: "Server Error",
          message: "500 Internal Server Error",
        });
      }
    }
  };

  return (
    <>
      <Modal
        opened={opened}
        onClose={handleCloseModal}
        centered
        fullScreen
        size="lg"
        transitionProps={{ transition: "scale", duration: 350 }}
        title={
          <Text fw={700} size="xl">
            {mode === "PUT" ? "Edit IKWS" : "Create New IKWS"}
          </Text>
        }
      >
        <form
          onSubmit={form.onSubmit((values) => {
            handleSubmit(values);
          })}
        >
          <Tabs variant="outline" radius="md" defaultValue="ikws">
            <Tabs.List grow>
              <Tabs.Tab
                value="ikws"
                leftSection={<IconFileInfo style={iconStyle} />}
              >
                IKW
              </Tabs.Tab>
              <Tabs.Tab
                value="revision"
                leftSection={<IconFilePencil style={iconStyle} />}
              >
                IKW Revision
              </Tabs.Tab>
            </Tabs.List>

            <Tabs.Panel value="ikws">
              <ScrollArea h={620}>
                <IkwForm
                  form={form}
                  dataJobTask={dataJobTask}
                  dataDepartment={dataDepartment}
                />
              </ScrollArea>
            </Tabs.Panel>
            <Tabs.Panel value="revision">
              <ScrollArea h={620} offsetScrollbars>
                <div>
                  <IKWRevisionForm
                    {...{
                      form,
                      forms,
                      setForms,
                      nextRevisionNumber,
                      setNextRevisionNumber,
                    }}
                  />
                </div>
              </ScrollArea>
            </Tabs.Panel>
          </Tabs>

          <Modal.Header
            pos={"sticky"}
            bottom={0}
            className="flex place-self-end gap-2"
          >
            <Button
              variant="default"
              color="white"
              size="lg"
              radius={12}
              onClick={handleCloseModal}
            >
              Close
            </Button>
            <Button
              variant="filled"
              color="violet"
              size="lg"
              type="submit"
              radius={12}
            >
              Save
            </Button>
          </Modal.Header>
        </form>
      </Modal>
      {mode === "PUT" ? (
        <ActionIcon
          variant="transparent"
          onClick={handleEditData}
          color="green"
          title="Edit"
        >
          <IconEditCircle />
        </ActionIcon>
      ) : (
        <Button
          className="shadow-md"
          size="sm"
          variant="filled"
          color="violet"
          radius={9}
          onClick={() => {
            handleCreateData();
          }}
        >
          <Text className="font-satoshi" size="sm">
            Add New
          </Text>
        </Button>
      )}
    </>
  );
};

export default FormIkws;
