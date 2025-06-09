import DashboardComponent from "@/components/Dashboard/Dashboard";
import { Metadata } from "next";
import DefaultLayout from "@/components/Layouts/DefaultLayout";

export const metadata: Metadata = {
  title: "Dashboard Admin | KAS",
  description: "This is Dashboard admin for PT. KAS",
  icons: {
    icon: "images/images.jpeg",
  },
};

export default function Dashboard() {
  return (
    <>
      <DefaultLayout>
        <DashboardComponent />
      </DefaultLayout>
    </>
  );
}
