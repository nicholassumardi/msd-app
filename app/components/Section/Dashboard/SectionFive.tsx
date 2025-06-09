"use client";

import React from "react";
import { Dashboard } from "../../Dashboard/Dashboard";
import LineChart from "../../Chart/LineChartDashboard";

interface DashboardComponent {
  dataDashboard: Dashboard | null;
}
const SectionFive: React.FC<DashboardComponent> = ({ dataDashboard }) => {
  return (
    <div className="col-span-12 rounded-sm border border-stroke bg-white px-5 pb-5 pt-7.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:col-span-12">
      <div>
        <h5 className="text-xl font-semibold text-black dark:text-white">
          Total User (Gender)
        </h5>
      </div>
      <div className="flex flex-wrap items-start justify-between gap-3 sm:flex-nowrap mt-5">
        <div className="flex w-full flex-wrap justify-evenly sm:gap-5">
          <LineChart dataDashboard={dataDashboard} />
        </div>
      </div>
    </div>
  );
};

export default SectionFive;
