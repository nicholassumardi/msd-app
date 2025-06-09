/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";

import { Button, Modal, Text, Group, rem, Tooltip, Menu } from "@mantine/core";
import { useDisclosure } from "@mantine/hooks";
import {
  IconDatabaseImport,
  IconFileImport,
  IconFileTypeXls,
  IconUpload,
  IconX,
} from "@tabler/icons-react";
import { Dropzone, MIME_TYPES } from "@mantine/dropzone";
import Image from "next/image";
import { useState } from "react";
import axios from "axios";
import ErrorNotification from "@/components/Notifications/ErrorNotification";
import SuccessNotification from "@/components/Notifications/SuccessNotification";

const ImportDropzone = ({ url, mode }: { url: string; mode?: string }) => {
  const [opened, { open, close }] = useDisclosure(false);
  const [isLoading, setIsLoading] = useState(false);

  const handleImportExcel = async (files: any) => {
    const formData = new FormData();
    formData.append("file", files[0]);
    setIsLoading(true);
    try {
      const response = await axios.post(url, formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
        responseType: "blob",
      });
      const contentType = response.headers["content-type"] ?? "";
      if (!contentType.includes("application/json")) {
        const url = window.URL.createObjectURL(
          new Blob([response.data], {
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
          })
        );
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", "msd-data-karyawan.xlsx");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      }

      if (response.status === 201) {
        SuccessNotification({
          title: "Success",
          message: "File uploaded successfully",
        });
      }
    } catch (err: any) {
      ErrorNotification({
        title: "Server Error",
        message: err.response?.data?.error || "An error occurred",
      });
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <>
      <Modal
        opened={opened}
        onClose={close}
        radius="md"
        size="xl"
        transitionProps={{ transition: "scale", duration: 350 }}
        zIndex={1000}
        title={
          <>
            <Text size="xl" fw={700} className="font-satoshi">
              <div className="flex gap-2 items-center">
                <IconFileTypeXls size={30} />
                Import data
              </div>
            </Text>
          </>
        }
        centered
      >
        <form>
          <div className="grid gap-3 font-satoshi text-gray-500">
            <Dropzone
              onDrop={(files) => handleImportExcel(files)}
              onReject={() =>
                ErrorNotification({
                  title: "Error",
                  message: "This type of file is not supported",
                })
              }
              maxSize={150 * 1024 ** 2}
              maxFiles={1}
              accept={[MIME_TYPES.csv, MIME_TYPES.xls, MIME_TYPES.xlsx]}
              loading={isLoading}
            >
              <Group
                justify="center"
                gap="xl"
                mih={220}
                style={{ pointerEvents: "none" }}
              >
                <Dropzone.Accept>
                  <IconUpload
                    style={{
                      width: rem(52),
                      height: rem(52),
                      color: "var(--mantine-color-blue-6)",
                    }}
                    stroke={1.5}
                  />
                </Dropzone.Accept>
                <Dropzone.Reject>
                  <IconX
                    style={{
                      width: rem(52),
                      height: rem(52),
                      color: "var(--mantine-color-red-6)",
                    }}
                    stroke={1.5}
                  />
                </Dropzone.Reject>
                <Dropzone.Idle>
                  <Image
                    src="/images/excel.png"
                    width="60"
                    height="30"
                    alt=""
                  />
                </Dropzone.Idle>

                <div>
                  <Text size="xl" inline>
                    Drag your files here or click to select files
                  </Text>
                  <Text size="sm" c="dimmed" inline mt={7}>
                    Please ensure you have the right format (.xls, .xlsx) and
                    maximum 1 file per upload
                  </Text>
                </div>
              </Group>
            </Dropzone>
          </div>
        </form>
      </Modal>
      <Tooltip label="import data" position="bottom">
        {mode ? (
          <Menu.Item
            onClick={(e) => {
              e.preventDefault();
              e.stopPropagation();
              // If this is inside a Mantine Menu, you might need to keep it open
              open();
            }}
            leftSection={<IconFileImport size={16} />}
          >
            Import File
          </Menu.Item>
        ) : (
          <Button
            variant="subtle"
            color="blue"
            leftSection={<IconDatabaseImport />}
            onClick={open}
            className="hover:bg-blue-50"
          >
            Import
          </Button>
        )}
      </Tooltip>
    </>
  );
};

export default ImportDropzone;
