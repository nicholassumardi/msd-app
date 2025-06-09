/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";

import "@mantine/core/styles.css";
import "@mantine/dates/styles.css";
import {
  Accordion,
  AccordionControl,
  AccordionItem,
  AccordionPanel,
  ActionIcon,
  Button,
  CloseButton,
  Group,
  Menu,
  MenuDropdown,
  MenuItem,
  MenuTarget,
  rem,
  Text,
  ThemeIcon,
} from "@mantine/core";
import {
  IconChevronLeft,
  IconChevronRight,
  IconDots,
  IconLibraryPlus,
  IconPencil,
  IconTrash,
  IconUser,
  IconUsersGroup,
} from "@tabler/icons-react";
import { useDisclosure } from "@mantine/hooks";
import BreadCrumbStructure from "@/components/Accordion/AccordionStructure/BreadCrumb";
import { option } from "../../../../pages/types/option";
import { useEffect, useState } from "react";
import { Structure } from "../../../../pages/api/admin/structure";
import axios from "axios";
import Image from "next/image";
import dynamic from "next/dynamic";
import { useForm } from "@mantine/form";
import { modals } from "@mantine/modals";
import SuccessNotification from "@/components/Notifications/SuccessNotification";
import ErrorNotification from "@/components/Notifications/ErrorNotification";
import { PaginationData } from "@/components/Tables/TableEvaluation/ButtonDetail";

const LoadingState = dynamic(() => import("../../common/LoadingState/index"), {
  ssr: false,
});

const DrawerStructure = dynamic(() => import("../AccordionStructure/Drawer"), {
  ssr: false,
});

const ModalStructure = dynamic(() => import("../AccordionStructure/Modal"), {
  ssr: false,
});

const AccordionStructure = () => {
  const [openedDrawer, { open: openDrawer, close: closeDrawer }] =
    useDisclosure(false);
  const [openedModal, { open: openModal, close: closeModal }] =
    useDisclosure(false);
  const [dataDepartment, setDataDepartment] = useState<option[]>([]);
  const [dataPehCode, setDataPehCode] = useState<option[]>([]);
  const [dataEmployee, setDataEmployee] = useState<option[]>([]);
  const [selectedId, setSelectedId] = useState<string | null>(null);
  const [dataStructure, setDataStructure] = useState<
    Structure["StructureMapping"]
  >([]);
  const [error, setError] = useState([]);
  const [isLoading, setIsLoading] = useState(false);
  const [activeItem, setActiveItem] = useState<string[]>([]);
  const [value, setValue] = useState<string | null>("1");
  const [pagination, setPagination] = useState<PaginationData>({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
  });
  const {
    current_page = 1,
    last_page = 1,
    per_page = 10,
    total = 0,
  } = pagination || {};

  const handlePageChange = (page: number) => {
    if (page < 1 || page > last_page) return;
    setPagination((prev) => ({
      ...prev,
      current_page: page,
    }));
  };
  const form = useForm({
    initialValues: {
      user_structure_mapping_id: "",
      uuid: "",
      id_structure: "",
      id_staff: "",
      group: "",
      position_code_structure: "",
      assign_date: null as Date | null,
    },
    validate: {
      uuid: (value) => (!value ? "Please select employee" : null),
      id_structure: (value) => (!value ? "ID Structure cannot be empty" : null),
      id_staff: (value) => (!value ? "ID Staff cannot be empty" : null),
      position_code_structure: (value) =>
        !value ? "Position code cannot be empty" : null,
    },
  });

  const getDataStructureChange = async (id: any) => {
    setIsLoading(true);
    try {
      const response = await axios.get(
        `/api/admin/structure?type=structureMapping`,
        {
          params: {
            current_page: pagination.current_page,
            per_page: per_page,
            id_department: id,
          },
        }
      );
      setDataStructure(response.data.data.data);
      setPagination(
        response.data.data.pagination ?? {
          current_page: 1,
          last_page: 1,
          per_page: 10,
          total: 0,
        }
      );
    } catch (err: any) {
      if (err.response) {
        setError(err.response.data.message);
      }
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    if (value) {
      getDataStructureChange(value);
    }
  }, [value, pagination?.current_page]);

  useEffect(() => {
    const getDataDepartment = async () => {
      try {
        const response = await axios.get(
          "/api/admin/master_data/department?type=showParent"
        );
        const data = response.data.data.map((item: any) => ({
          value: item.id.toString(),
          label: item.code,
        }));

        setDataDepartment(data);
      } catch (err: any) {
        if (err.response) {
          setError(err.response.data.message);
        }
      }
    };
    getDataDepartment();
  }, []);

  useEffect(() => {
    const getDataEmployee = async () => {
      try {
        const response = await axios.get("/api/admin/employee?type=showAll");
        const data = response.data.data.map((item: any) => ({
          value: item.uuid.toString(),
          label: `${item.name} (${item.employee_number ?? ""})`,
        }));
        setDataEmployee(data);
      } catch (err: any) {
        if (err.response) {
          setError(err.response.data.message);
        }
      }
    };
    getDataEmployee();
  }, []);

  useEffect(() => {
    const getDataPehCode = async () => {
      try {
        const response = await axios.get(
          "/api/admin/master_data/job_family/peh_code?type=show"
        );
        const data = response.data.data.data.map((item: any) => ({
          value: item.id.toString(),
          label: `${item.category_name} - ${item.position} - ${item.code}`,
        }));
        setDataPehCode(data);
      } catch (err: any) {
        if (err.response) {
          setError(err.response.data.message);
        }
      }
    };
    getDataPehCode();
  }, []);

  useEffect(() => {
    if (activeItem) {
    }
  }, [activeItem]);

  const handleReAssign = async (id: string) => {
    try {
      const response = await axios.put(
        `/api/admin/structure/${id}?type=updateStatus`
      );
      if (response.status === 200) {
        SuccessNotification({
          title: "Success",
          message: "Structure data successfully added",
        });
        getDataStructureChange(value);
      }
    } catch (err: any) {
      ErrorNotification({
        title: "Server Error",
        message: err.response.data.error,
      });
    }
  };

  const openDeleteConfirmModal = (id: string) =>
    modals.openConfirmModal({
      title: `Confirm deletion ?`,
      children: (
        <Text>
          Are you sure you want to remove assignment from this employee ? This
          action cannot be undone.
        </Text>
      ),
      labels: { confirm: "Yes", cancel: "Cancel" },
      confirmProps: { color: "red" },
      onConfirm: () => {
        handleReAssign(id);
      },
    });

  const Accordionlabel = ({ structure }: any) => {
    return (
      <Group wrap="nowrap">
        <ThemeIcon
          variant="gradient"
          size="xl"
          radius="xl"
          gradient={{ from: "violet", to: "rgba(257,211,211,1)", deg: 160 }}
        >
          <IconUsersGroup />
        </ThemeIcon>
        <div>
          <Text size="lg" fw={700}>
            {structure.name}
          </Text>
          <Text size="sm" c="dimmed" fw={400}>
            {structure.job_code?.position ?? "Empty"} (
            {structure.job_code?.code ?? "Empty"}){" "}
            <b>
              ({structure?.totalAssignedEmployee ?? 0}/{structure.quota})
            </b>
          </Text>
        </div>
      </Group>
    );
  };

  return (
    <>
      <BreadCrumbStructure
        {...{ openModal, dataDepartment, value, setValue }}
      />
      <ModalStructure
        id={selectedId}
        openedModal={openedModal}
        closeModal={() => {
          closeModal(); // Function to close modal
          setSelectedId(null); // Reset ID on close
        }}
        dataDepartment={dataDepartment}
        dataPehCode={dataPehCode}
        value={value}
        getDataStructureChange={getDataStructureChange}
      />
      <DrawerStructure
        {...{
          openedDrawer,
          closeDrawer,
          dataEmployee,
          form,
          value,
          getDataStructureChange,
        }}
      />
      {isLoading && <LoadingState />}
      {dataStructure.length > 0 ? (
        <div className="grid gap-6">
          <Accordion
            variant="separated"
            radius="md"
            className="shadow-sm font-satoshi"
            chevronPosition="left"
            multiple
            value={activeItem}
            onChange={setActiveItem}
          >
            {dataStructure.map((structure, index) => (
              <AccordionItem
                key={index}
                value={structure.id.toString()}
                onClick={() => {
                  form.setValues({
                    user_structure_mapping_id: structure.id.toString(),
                  });
                }}
              >
                <div className="flex items-center p-2">
                  <AccordionControl>
                    <Accordionlabel {...{ structure }} />
                  </AccordionControl>
                  <Menu width={150} shadow="md">
                    <MenuTarget>
                      <ActionIcon
                        size="lg"
                        variant="subtle"
                        color="black"
                        opacity={0.5}
                      >
                        <IconDots size="1.5rem" />
                      </ActionIcon>
                    </MenuTarget>

                    <MenuDropdown>
                      <MenuItem
                        color="#51cf66"
                        leftSection={<IconPencil size={14} />}
                        onClick={() => {
                          setSelectedId(structure.id.toString());
                          openModal(); // Function to set openedModal to true
                        }}
                      >
                        <Text size="md">Edit</Text>
                      </MenuItem>
                      <MenuItem
                        color="#c92a2a"
                        leftSection={
                          <IconTrash
                            size={20}
                            style={{ width: rem(20), height: rem(20) }}
                          />
                        }
                      >
                        <Text size="md" className="font-satoshi">
                          Delete
                        </Text>
                      </MenuItem>
                    </MenuDropdown>
                  </Menu>
                </div>
                <AccordionPanel>
                  <div className="container w-11/12 justify-self-center ">
                    <ol className="list-decimal font-satoshi gap-3 grid text-gray-500">
                      {Array.from({ length: structure.quota }, (_, i) =>
                        structure?.user_job_code?.length > 0 &&
                        i < structure.user_job_code.length &&
                        structure.user_job_code[i].status == 1 ? (
                          <li className="pl-5" key={i}>
                            <Accordion
                              variant="contained"
                              radius={12}
                              opacity={1}
                              color="gray"
                              className="shadow-default shadow-gray-200 font-satoshi"
                              chevron
                            >
                              <AccordionItem value="test">
                                <div className="flex items-center p-2">
                                  <AccordionControl>
                                    <Group wrap="inherit">
                                      <ThemeIcon
                                        variant="gradient"
                                        size="xl"
                                        radius="xl"
                                        gradient={{
                                          from: "blue",
                                          to: "rgba(255,255,255,1)",
                                          deg: 150,
                                        }}
                                      >
                                        <IconUser />
                                      </ThemeIcon>
                                      <div>
                                        <Text size="lg" fw={700}>
                                          {structure.user_job_code[i].user.name}
                                          (
                                          {
                                            structure.user_job_code[i]
                                              .position_code_structure
                                          }
                                          )
                                        </Text>
                                        <Text size="sm" c="dimmed" fw={400}>
                                          {
                                            structure.user_job_code[i].user
                                              .employee_number
                                          }
                                        </Text>
                                      </div>
                                    </Group>
                                  </AccordionControl>
                                  <CloseButton
                                    onClick={() => {
                                      openDeleteConfirmModal(
                                        structure.user_job_code[i].id.toString()
                                      );
                                    }}
                                  />
                                </div>
                              </AccordionItem>
                            </Accordion>
                          </li>
                        ) : (
                          <li className="pl-4" key={i}>
                            <Button
                              className="shadow-default"
                              size="sm"
                              variant="subtle"
                              color="gray"
                              radius={12}
                              leftSection={<IconLibraryPlus />}
                              opacity={0.9}
                              c="dimmed"
                              justify="start"
                              fullWidth
                              onClick={() => {
                                openDrawer();
                              }}
                            >
                              <Text className="font-satoshi" fw={400} size="md">
                                Assign Role
                              </Text>
                            </Button>
                          </li>
                        )
                      )}
                    </ol>
                  </div>
                </AccordionPanel>
              </AccordionItem>
            ))}
          </Accordion>
          {/* Enhanced Pagination */}
          <div className="bg-gray-50 px-6 py-6 border-t border-gray-100 rounded-b-lg">
            <Group justify="center" gap="lg">
              <Button
                variant="subtle"
                color="violet"
                size="md"
                leftSection={<IconChevronLeft size={24} />}
                disabled={current_page === 1}
                onClick={() => handlePageChange(current_page - 1)}
                className="px-4"
              >
                Previous
              </Button>

              <Group gap="xs">
                {Array.from({ length: last_page }, (_, i) => (
                  <Button
                    key={i + 1}
                    variant={i + 1 === current_page ? "filled" : "light"}
                    color="violet"
                    size="md"
                    className="w-12 h-12" // Fixed size for buttons
                    onClick={() => handlePageChange(i + 1)}
                  >
                    {i + 1}
                  </Button>
                ))}
              </Group>

              <Button
                variant="subtle"
                color="violet"
                size="md"
                rightSection={<IconChevronRight size={24} />}
                disabled={current_page === last_page}
                onClick={() => handlePageChange(current_page + 1)}
                className="px-4"
              >
                Next
              </Button>
            </Group>

            <Text size="sm" c="dimmed" ta="center" mt="sm">
              Page {current_page} of {last_page} • {total} total items
            </Text>
          </div>
        </div>
      ) : (
        <div className="grid justify-center text-center place-items-center font-satoshi md:p-25">
          <Image
            src="/images/no_data_logo.png"
            width={300}
            height={300}
            sizes="100vw"
            alt=""
            className="rounded-full"
          />

          <div className="max-w-75">
            <Text fw={750} fz={35} className="font-bold">
              No Data Found
            </Text>
            <Text
              fw={300}
              c="dimmed"
              fz={20}
              className="font-bold break-normal"
            >
              Please ensure that you have the relevant data for this department
            </Text>
          </div>
        </div>
      )}
    </>
  );
};

export default AccordionStructure;
