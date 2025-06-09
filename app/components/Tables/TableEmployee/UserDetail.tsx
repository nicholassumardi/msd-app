/* eslint-disable @typescript-eslint/no-explicit-any */
import "@mantine/core/styles.css";
import "@mantine/dates/styles.css"; //if using mantine date picker features
import "mantine-react-table/styles.css"; //make sure MRT styles were imported in your app root (once)
import {
  Text,
  Title,
  Mark,
  ScrollArea,
  Select,
  rem,
  Tabs,
} from "@mantine/core";
import {
  IconBuildingSkyscraper,
  IconCertificate2,
  IconUserCircle,
} from "@tabler/icons-react";
import { Employee } from "../../../../pages/api/admin/employee";
import axios from "axios";
import SuccessNotification from "@/components/Notifications/SuccessNotification";
import { useEffect, useState } from "react";

interface TabsDetail {
  dataUser: Employee | null;
  getData: () => Promise<void>;
}

const TabsDetail: React.FC<TabsDetail> = ({ dataUser, getData }) => {
  const iconStyle = { width: rem(50), height: rem(50) };
  const [selectedValueStructure, setSelectedValueStructure] = useState("");
  const employeeNumbers = dataUser?.employee_numbers
    ? dataUser?.employee_numbers.map((employee) => ({
        value: employee.id.toString(),
        label: employee.employee_number,
        status: employee.status,
      }))
    : [];
  const employeeRoleCode = dataUser?.roleCodes.map((roleCode) => ({
    value: roleCode.id.toString(),
    label: "roleCode.job_code.full_code ?? ",
    status: roleCode.status,
  }));

  const handleUpdateStatusEmployeeNumber = async (id: any) => {
    try {
      const response = await axios.put(
        `/api/admin/employee/${id}?type=updateStatus`,
        {}
      );

      if (response.status === 200) {
        getData();
        SuccessNotification({
          title: "Success",
          message: "Employee number successfully updated",
        });
      }
    } catch (error) {
      console.error("Error updating status:", error);
    }
  };

  const handleUpdateStatusStructure = async (id: any) => {
    try {
      const response = await axios.put(
        `/api/admin/structure/${id}?type=updateStatus`,
        {}
      );

      if (response.status === 200) {
        setSelectedValueStructure(id);
        getData();
        SuccessNotification({
          title: "Success",
          message: "Structure number successfully updated",
        });
      }
    } catch (error) {
      console.error("Error updating status:", error);
    }
  };

  useEffect(() => {
    const selectedRoleCode = employeeRoleCode?.find(
      (structure) => structure.status == "1"
    )?.value;

    if (selectedRoleCode) {
      setSelectedValueStructure(selectedRoleCode);
    }
  }, [employeeRoleCode]);

  const UserDetail = () => {
    return (
      <>
        <div className="mr-5">
          <Title fz="h2">
            <Mark color="gray">Data Diri </Mark>
          </Title>
          <ScrollArea h={350}>
            <div className="grid grid-cols-3 gap-3">
              <div className="col-span-3">
                <Select
                  mt="sm"
                  label={
                    <span className="font-thin text-base space-y-2">
                      No Pegawai
                    </span>
                  }
                  c="dimmed"
                  fw={250}
                  placeholder="Pick value"
                  defaultValue={
                    employeeNumbers.find((employee) => employee.status == "1")
                      ?.value
                  }
                  onChange={(value) => {
                    handleUpdateStatusEmployeeNumber(value);
                  }}
                  data={employeeNumbers}
                />
              </div>
              <div>
                <Title order={5} mt="sm" fw={250} c="dimmed">
                  Unicode Data Diri
                </Title>
                <Text fz="lg" c="dimmed" fw={620}>
                  {dataUser?.unicode}
                </Text>
              </div>
              <div>
                <Title order={5} mt="sm" fw={250} c="dimmed">
                  Kelamin
                </Title>
                <Text fz="lg" c="dimmed" fw={620}>
                  {dataUser?.gender}
                </Text>
              </div>
              <div>
                <Title order={5} mt="sm" fw={250} c="dimmed">
                  Agama
                </Title>
                <Text fz="lg" c="dimmed" fw={620}>
                  {dataUser?.religion}
                </Text>
              </div>
              <div>
                <Title order={5} mt="sm" fw={250} c="dimmed">
                  Pendidikan
                </Title>
                <Text fz="lg" c="dimmed" fw={620}>
                  {dataUser?.education}
                </Text>
              </div>
              <div>
                <Title order={5} mt="sm" fw={250} c="dimmed">
                  Status Pernikan
                </Title>
                <Text fz="lg" c="dimmed" fw={620}>
                  {dataUser?.marital_status}
                </Text>
              </div>
              <div>
                <Title order={5} mt="sm" fw={250} c="dimmed">
                  Umur
                </Title>
                <Text fz="lg" c="dimmed" fw={620}>
                  {dataUser?.age}
                </Text>
              </div>
              <div>
                <Title order={5} mt="sm" fw={250} c="dimmed">
                  Tahun
                </Title>
                <Text fz="lg" c="dimmed" fw={620}>
                  {dataUser?.year}
                </Text>
              </div>
              <div>
                <Title order={5} mt="sm" fw={250} c="dimmed">
                  Generasi
                </Title>
                <Text fz="lg" c="dimmed" fw={620}>
                  {dataUser?.general_classification}
                </Text>
              </div>
              <div>
                <Title order={5} mt="sm" fw={250} c="dimmed">
                  Alamat
                </Title>
                <Text fz="lg" c="dimmed" fw={620}>
                  {dataUser?.address}
                </Text>
              </div>
            </div>
          </ScrollArea>
        </div>
      </>
    );
  };

  const UserDepartment = () => {
    return (
      <>
        <div className="mr-5">
          <Title fz="h2">
            <Mark color="gray">Department </Mark>
          </Title>
          <ScrollArea h={350}>
            <div className="grid grid-cols-3 gap-3">
              <div>
                <Title order={5} mt="sm" fw={250} c="dimmed">
                  PT
                </Title>
                <Text fz="lg" c="dimmed" fw={620}>
                  {dataUser?.company_name}
                </Text>
              </div>
              <div>
                <Title order={5} mt="sm" fw={250} c="dimmed">
                  Department
                </Title>
                <Text fz="lg" c="dimmed" fw={620}>
                  {dataUser?.department_name}
                </Text>
              </div>
              <div>
                <Select
                  mt="sm"
                  label={
                    <span className="font-thin text-base space-y-2">
                      Kode Jabatan
                    </span>
                  }
                  c="dimmed"
                  fw={250}
                  placeholder="Pick value"
                  defaultValue={selectedValueStructure}
                  onChange={(value) => {
                    handleUpdateStatusStructure(value);
                  }}
                  data={employeeRoleCode}
                />
              </div>
              <div>
                <Title order={5} mt="sm" fw={250} c="dimmed">
                  Kode Posisi (Personalia)
                </Title>
                <Text fz="lg" c="dimmed" fw={620}>
                  {dataUser?.position_code}
                </Text>
              </div>
              <div>
                <Title order={5} mt="sm" fw={250} c="dimmed">
                  Status
                </Title>
                <Text fz="lg" c="dimmed" fw={620}>
                  {dataUser?.status}
                </Text>
              </div>
              <div>
                <Title order={5} mt="sm" fw={250} c="dimmed">
                  Bagian (Personalia)
                </Title>
                <Text fz="lg" c="dimmed" fw={620}>
                  {dataUser?.section}
                </Text>
              </div>
              <div>
                <Title order={5} mt="sm" fw={250} c="dimmed">
                  Status Shift
                </Title>
                <Text fz="lg" c="dimmed" fw={620}>
                  {dataUser?.schedule_type}
                </Text>
              </div>
              <div>
                <Title order={5} mt="sm" fw={250} c="dimmed">
                  Status TWIJI
                </Title>
                <Text fz="lg" c="dimmed" fw={620}>
                  {dataUser?.status_twiji}
                </Text>
              </div>
              <div>
                <Title order={5} mt="sm" fw={250} c="dimmed">
                  Status TK
                </Title>
                <Text fz="lg" c="dimmed" fw={620}>
                  {dataUser?.employee_type}
                </Text>
              </div>
              <div>
                <Title order={5} mt="sm" fw={250} c="dimmed">
                  Tenaga Kerja
                </Title>
                <Text fz="lg" c="dimmed" fw={620}>
                  Staff
                </Text>
              </div>
              <div>
                <Title order={5} mt="sm" fw={250} c="dimmed">
                  Staff/ Non Staff
                </Title>
                <Text fz="lg" c="dimmed" fw={620}>
                  {dataUser?.employee_type.toLowerCase() == "staff"
                    ? "Staff"
                    : "Non Staff"}
                </Text>
              </div>
              <div>
                <Title order={5} mt="sm" fw={250} c="dimmed">
                  Tanggal Masuk Awal
                </Title>
                <Text fz="lg" c="dimmed" fw={620}>
                  {dataUser?.join_date}
                </Text>
              </div>
              <div>
                <Title order={5} mt="sm" fw={250} c="dimmed">
                  Service Years (All)
                </Title>
                <Text fz="lg" c="dimmed" fw={620}>
                  {dataUser?.service_year}
                </Text>
              </div>
              <div>
                <Title order={5} mt="sm" fw={250} c="dimmed">
                  Masa Kerja
                </Title>
                <Text fz="lg" c="dimmed" fw={620}>
                  {dataUser?.working_duration_classification}
                </Text>
              </div>
            </div>
          </ScrollArea>
        </div>
      </>
    );
  };

  const UserCertification = () => {
    return (
      <>
        <div className="mr-5">
          <Title fz="h2">
            <Mark color="gray">Sertifikasi </Mark>
          </Title>
          <ScrollArea h={350}>
            <div className="grid grid-cols-3 gap-3">
              {dataUser?.user_certificates &&
                dataUser.user_certificates.map((certificate, index) => (
                  <div key={index}>
                    <Title order={5} mt="sm" fw={250} c="dimmed">
                      {certificate.name}
                    </Title>
                    <Text fz="lg" c="dimmed" fw={620}>
                      {certificate.pivot.description}
                    </Text>
                  </div>
                ))}
            </div>
          </ScrollArea>
        </div>
      </>
    );
  };

  return (
    <Tabs
      color="teal"
      variant="default"
      radius="md"
      orientation="vertical"
      defaultValue="detail"
      placement="right"
      className="flex items-center"
    >
      <Tabs.List>
        <Tabs.Tab
          value="detail"
          leftSection={<IconUserCircle strokeWidth={1} style={iconStyle} />}
        >
          Employee Data
        </Tabs.Tab>
        <Tabs.Tab
          value="department"
          leftSection={
            <IconBuildingSkyscraper strokeWidth={1} style={iconStyle} />
          }
        >
          Office
        </Tabs.Tab>
        <Tabs.Tab
          value="sertifikasi"
          leftSection={<IconCertificate2 strokeWidth={1} style={iconStyle} />}
        >
          Certification
        </Tabs.Tab>
      </Tabs.List>

      <Tabs.Panel value="detail">
        <UserDetail />
      </Tabs.Panel>

      <Tabs.Panel value="department">
        <UserDepartment />
      </Tabs.Panel>

      <Tabs.Panel value="sertifikasi">
        <UserCertification />
      </Tabs.Panel>
    </Tabs>
  );
};

export default TabsDetail;
