import Breadcrumb from "@/components/Breadcrumbs/Breadcrumb";
import DefaultLayout from "@/components/Layouts/DefaultLayout";
import { ModalsProvider } from "@mantine/modals";

import { Metadata } from "next";
import dynamic from "next/dynamic";

export const metadata: Metadata = {
  title: "Master Data Classifications | KAS",
  description: "This is Dashboard admin for PT. KAS",
  icons: {
    icon: "images/images.jpeg",
  },
};

const TabsDetail = dynamic(
  () => import("../../../components/Tables/TableClassifications/index"),
  {
    ssr: false,
  }
);

export default function ClassificationsDetails() {
  return (
    <>
      <DefaultLayout>
        <Breadcrumb pageName="Classifications" />
        <div className="z-9999 h-dvh">
          <ModalsProvider>
            <TabsDetail />
          </ModalsProvider>
        </div>
      </DefaultLayout>
    </>
  );
}
