/* eslint-disable @typescript-eslint/no-explicit-any */
import { Modal, Divider, Title, ActionIcon } from "@mantine/core";
import { IconUserScan } from "@tabler/icons-react";
import { useDisclosure } from "@mantine/hooks";
import UserInfoIcons from "./UserIcon";
import TabsDetail from "./UserDetail";
import { useState } from "react";
import axios from "axios";
import { Employee } from "../../../../pages/api/admin/employee";

interface ButtonDetailEmployee {
  uuid: string;
  getData: () => Promise<void>;
}

const ButtonDetail: React.FC<ButtonDetailEmployee> = ({ uuid, getData }) => {
  const [opened, { open, close }] = useDisclosure(false);
  const [dataUser, setDataUser] = useState<Employee | null>(null);

  const handleDetail = async () => {
    try {
      const response = await axios.get(`/api/admin/employee/${uuid}?type=show`);
      setDataUser(response.data.data.data);
    } catch (error: any) {}
    open();
  };

  return (
    <>
      <Modal
        opened={opened}
        onClose={close}
        size="100%"
        title={<Title fz="lg">Profile Details</Title>}
        centered
        transitionProps={{ transition: "scale", duration: 200 }}
      >
        <div className="grid grid-cols-12 gap-1">
          <div className="col-span-2 flex justify-center">
            <UserInfoIcons dataUser={dataUser} />
          </div>
          <div className="col-span-1 flex justify-center">
            <Divider size="md" orientation="vertical" />
          </div>
          <div className="col-span-9 gap-4 items-center">
            <TabsDetail dataUser={dataUser} getData={getData} />
          </div>
        </div>
      </Modal>

      <ActionIcon
        variant="transparent"
        onClick={handleDetail}
        color="blue"
        title="See Details"
      >
        <IconUserScan />
      </ActionIcon>
    </>
  );
};
export default ButtonDetail;
