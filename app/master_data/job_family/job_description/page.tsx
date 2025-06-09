import Breadcrumb from "@/components/Breadcrumbs/Breadcrumb";
import DefaultLayout from "@/components/Layouts/DefaultLayout";
import { ModalsProvider } from "@mantine/modals";
import { Metadata } from "next";
import dynamic from "next/dynamic";

export const metadata: Metadata = {
  title: "Dashboard Admin | KAS",
  description: "This is Dashboard admin for PT. KAS",
  icons: {
    icon: "images/images.jpeg",
  },
};

const TableData = dynamic(
  () =>
    import(
      "../../../components/Tables/TableJobFamily/TableJobDescription/TableData"
    ),
  {
    ssr: false,
  }
);

export default function JobDescription() {
  return (
    <>
      <DefaultLayout>
        <Breadcrumb pageName="Job Description" />
        <div className="z-9999 h-dvh">
          <ModalsProvider>
            <TableData />
          </ModalsProvider>
        </div>
      </DefaultLayout>
    </>
  );
}
