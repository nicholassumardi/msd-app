import { Metadata } from "next";
import DefaultLayout from "@/components/Layouts/DefaultLayout";
import Breadcrumb from "@/components/Breadcrumbs/Breadcrumb";
import { ModalsProvider } from "@mantine/modals";
import dynamic from "next/dynamic";

export const metadata: Metadata = {
  title: "Dashboard Admin | KAS",
  description: "This is Dashboard admin for PT. KAS",
  icons: {
    icon: "images/images.jpeg",
  },
};

const TableData = dynamic(
  () => import("../../components/Tables/TableEvaluation/TableData"),
  {
    ssr: false,
  }
);

export default function Evaluation() {
  return (
    <>
      <DefaultLayout>
        <Breadcrumb pageName="Evaluation" />
        <div className="z-9999 h-dvh">
          <ModalsProvider>
            <TableData />
          </ModalsProvider>
        </div>
      </DefaultLayout>
    </>
  );
}
