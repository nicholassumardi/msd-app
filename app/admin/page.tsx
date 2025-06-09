import "@mantine/core/styles.css";

// import { Metadata } from "next";
import DefaultLayout from "@/components/Layouts/DefaultLayout";
import Breadcrumb from "@/components/Breadcrumbs/Breadcrumb";
import { ModalsProvider } from "@mantine/modals";
// import InteractiveTree from "./detail5";
// import OrgChartComponent from "./detail3";
import UNICEFOrgChart from "./detail5";
import App from "./detail";
// import OrgChartComponent from "./detail4";

// import App from "./detail2";

// import App from "./detail";

// data/treeData.ts
// export const treeData = {
//   id: 1,
//   person: {
//     // id: 1,
//     // avatar: "https://s3.amazonaws.com/uifaces/faces/twitter/spbroma/128.jpg",
//     name: "Jane Doe",
//     title: "CEO",
//     totalReports: 5,
//   },
//   hasChild: true,
//   hasParent: false,
//   isHighlight: true,
//   children: [
//     {
//       id: 2,
//       person: {
//         id: 2,
//         avatar:
//           "https://s3.amazonaws.com/uifaces/faces/twitter/spbroma/128.jpg",
//         name: "John Smith",
//         title: "CTO",
//       },
//       hasChild: true,
//       hasParent: true,
//       children: [
//         {
//           id: 3,
//           person: {
//             id: 3,
//             // avatar:
//             //   "https://s3.amazonaws.com/uifaces/faces/twitter/spbroma/128.jpg",
//             name: "Alice Johnson",
//             title: "Senior Developer",
//           },
//           hasChild: false,
//           hasParent: true,
//           children: [],
//         },
//         {
//           id: 4,
//           person: {
//             id: 4,
//             // avatar:
//             //   "https://s3.amazonaws.com/uifaces/faces/twitter/spbroma/128.jpg",
//             name: "Bob Lee",
//             title: "DevOps Engineer",
//           },
//           hasChild: false,
//           hasParent: true,
//           children: [],
//         },
//       ],
//     },
//     {
//       id: 5,
//       person: {
//         id: 5,
//         avatar:
//           "https://s3.amazonaws.com/uifaces/faces/twitter/spbroma/128.jpg",
//         name: "Emily Davis",
//         title: "CFO",
//       },
//       hasChild: false,
//       hasParent: true,
//       children: [],
//     },
//   ],
// };

export default function Certificates() {
  return (
    <>
      <DefaultLayout>
        <Breadcrumb pageName="RKI" />
        <div className="z-9999 h-dvh">
          <ModalsProvider>
            {/* <App /> */}

            {/* <OrgChartComponent /> */}
            {/* <OrgChartComponent /> */}
            {/* <App /> */}
            <UNICEFOrgChart />
            {/* <InteractiveTree /> */}
          </ModalsProvider>
        </div>
      </DefaultLayout>
    </>
  );
}
