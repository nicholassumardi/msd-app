import Calendar from "@/components/Calender";
import DefaultLayout from "@/components/Layouts/DefaultLayout";
import { Metadata } from "next";

export const metadata: Metadata = {
  title: "Master Data Certificates | KAS",
  description: "This is Dashboard admin for PT. KAS",
  icons: {
    icon: "images/images.jpeg",
  },
};

export default function Calender() {
  return (
    <>
      <DefaultLayout>
        <Calendar />
      </DefaultLayout>
    </>
  );
}
