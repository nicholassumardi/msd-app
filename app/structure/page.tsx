import AccordionStructure from "@/components/Accordion/AccordionStructure";
import DefaultLayout from "@/components/Layouts/DefaultLayout";
import { ModalsProvider } from "@mantine/modals";
import { Metadata } from "next";

export const metadata: Metadata = {
  title: "Dashboard Admin | KAS",
  description: "This is Dashboard admin for PT. KAS",
  icons: {
    icon: "images/images.jpeg",
  },
};

export default function Structure() {
  return (
    <>
      <DefaultLayout>
        <div className="z-9999 h-dvh">
          <ModalsProvider>
            <AccordionStructure />
          </ModalsProvider>
        </div>
      </DefaultLayout>
    </>
  );
}
