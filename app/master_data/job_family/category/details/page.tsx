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

const TabsDetail = dynamic(
  () => import("../../../../components/Tables/TableJobFamily/index"),
  {
    ssr: false,
  }
);

export default function Category() {
  return (
    <>
      <DefaultLayout>
        <Breadcrumb pageName="Category & Position" />
        <div className="z-9999 h-dvh">
          <ModalsProvider>
            <TabsDetail />
          </ModalsProvider>
        </div>
      </DefaultLayout>
    </>
  );
}
