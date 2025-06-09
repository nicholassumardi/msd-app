/* eslint-disable @typescript-eslint/no-explicit-any */
import { useState } from "react";
import {
  Stepper,
  Group,
  rem,
  UnstyledButton,
  ThemeIcon,
  ScrollArea,
  Blockquote,
} from "@mantine/core";
import {
  IconCaretLeft,
  IconCaretRight,
  IconCertificate,
  IconIdBadge2,
  IconInfoCircle,
  IconUser,
} from "@tabler/icons-react";
import ContentUserData from "./ContentUserData";
import ContentEmployeeIdentification from "./ContentEmployeIdentification";
import ContentCertificate from "./ContentCertificate";
import { option } from "../../../../pages/types/option";
import { Certificate, Employee } from "./Form";

interface ContentComponent {
  form: any;
  employeeNumbers: Employee[];
  dataCompanies: option[];
  dataDepartments: option[];
  certificates: Certificate[];
  dataCertificates: option[];
  setEmployeeNumbers: React.Dispatch<React.SetStateAction<Employee[]>>;
  setDataCompanies: React.Dispatch<React.SetStateAction<option[]>>;
  setDataDepartments: React.Dispatch<React.SetStateAction<option[]>>;
  setCertificates: React.Dispatch<React.SetStateAction<Certificate[]>>;
  setDataCertificates: React.Dispatch<React.SetStateAction<option[]>>;
}

const StepperUser: React.FC<ContentComponent> = ({
  form,
  employeeNumbers,
  dataCompanies,
  dataDepartments,
  certificates,
  dataCertificates,
  setEmployeeNumbers,
  setDataCompanies,
  setDataDepartments,
  setCertificates,
  setDataCertificates,
}) => {
  const [active, setActive] = useState(0);

  const nextStep = () =>
    setActive((current) => (current < 3 ? current + 1 : current));
  const prevStep = () =>
    setActive((current) => (current > 0 ? current - 1 : current));

  return (
    <>
      <Stepper size="xl" active={active} onStepClick={setActive}>
        <Stepper.Step
          icon={<IconUser />}
          label="First step"
          description="Employee personal data"
        >
          <div className="container-fluid">
            <ScrollArea h={450} offsetScrollbars>
              <ContentUserData form={form} />
            </ScrollArea>
          </div>
        </Stepper.Step>
        <Stepper.Step
          icon={<IconIdBadge2 />}
          label="Second step"
          description="Employee identification"
        >
          <div className="container-fluid">
            <ScrollArea h={450} offsetScrollbars>
              <ContentEmployeeIdentification
                form={form}
                employeeNumbers={employeeNumbers}
                dataCompanies={dataCompanies}
                dataDepartments={dataDepartments}
                setEmployeeNumbers={setEmployeeNumbers}
                setDataCompanies={setDataCompanies}
                setDataDepartments={setDataDepartments}
              />
            </ScrollArea>
          </div>
        </Stepper.Step>
        <Stepper.Step
          icon={<IconCertificate />}
          label="Final step"
          description="Add Certification"
        >
          <div className="container-fluid">
            <ScrollArea h={450} offsetScrollbars>
              <ContentCertificate
                form={form}
                certificates={certificates}
                dataCertificates={dataCertificates}
                setCertificates={setCertificates}
                setDataCertificates={setDataCertificates}
              />
            </ScrollArea>
          </div>
        </Stepper.Step>
        <Stepper.Completed>
          <div className="container-fluid">
            {" "}
            <Blockquote
              color="blue"
              cite="– Developer Team"
              icon={<IconInfoCircle />}
              mt="xl"
            >
              Make sure to double check all the fields before saving
            </Blockquote>
          </div>
        </Stepper.Completed>
      </Stepper>

      <Group justify="center" mt="xl" gap={0} className=" bottom-20 inset-x-0 ">
        <UnstyledButton onClick={prevStep}>
          <ThemeIcon variant="transparent" size="xl">
            <IconCaretLeft style={{ width: rem(35), height: rem(35) }} />
          </ThemeIcon>
        </UnstyledButton>
        <UnstyledButton onClick={nextStep}>
          <ThemeIcon variant="transparent" size="xl">
            <IconCaretRight style={{ width: rem(35), height: rem(35) }} />
          </ThemeIcon>
        </UnstyledButton>
      </Group>
    </>
  );
};

export default StepperUser;
