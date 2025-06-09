"use client";

import React from "react";
import Image from "next/image";
import { Dashboard } from "../../Dashboard/Dashboard";

interface DashboardComponent {
  dataDashboard: Dashboard | null;
}
const SectionOne: React.FC<DashboardComponent> = ({ dataDashboard }) => {
  return (
    <div className="col-span-12 rounded-sm border border-stroke bg-white px-5 pb-5 pt-7.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:col-span-12">
      <div>
        <h5 className="text-xl font-semibold text-black dark:text-white">
          Total User (Gender)
        </h5>
      </div>
      <div className="flex flex-wrap items-start justify-between gap-3 sm:flex-nowrap mt-5">
        <div className="flex w-full flex-wrap justify-evenly sm:gap-5">
          <div className="flex-col justify-items-center min-w-80">
            <Image
              className="col-span-2"
              src="/images/man.png"
              width={150}
              height={150}
              alt=""
            />
            <div className="flex min-w-32">
              <span className="mr-2 mt-1 flex h-4 w-full max-w-4 items-center justify-center rounded-full border border-primary">
                <span className="block h-2.5 w-full max-w-2.5 rounded-full bg-primary"></span>
              </span>
              <div className="w-full">
                <p className="font-semibold text-primary">Laki - Laki</p>
                <p className="text-sm font-medium">
                  {dataDashboard?.totalUserMale} (
                  {dataDashboard?.totalUserPerCompany ?? 0 > 0
                    ? (
                        ((dataDashboard?.totalUserMale ?? 0) /
                          (dataDashboard?.totalUserPerCompany ?? 0)) *
                        100
                      ).toFixed(1)
                    : "0.0"}
                  %)
                </p>
              </div>
            </div>
          </div>
          <div className="flex-col justify-items-center min-w-80">
            <Image
              className="col-span-2"
              src="/images/woman.png"
              width={150}
              height={150}
              alt=""
            />
            <div className="flex min-w-32">
              <span className="mr-2 mt-1 flex h-4 w-full max-w-4 items-center justify-center rounded-full border border-pink-400">
                <span className="block h-2.5 w-full max-w-2.5 rounded-full bg-pink-400"></span>
              </span>
              <div className="w-full">
                <p className="font-semibold text-pink-400">Perempuan</p>
                <p className="text-sm font-medium">
                  {dataDashboard?.totalUserFemale} ({" "}
                  {dataDashboard?.totalUserPerCompany ?? 0 > 0
                    ? (
                        ((dataDashboard?.totalUserFemale ?? 0) /
                          (dataDashboard?.totalUserPerCompany ?? 0)) *
                        100
                      ).toFixed(1)
                    : "0.0"}
                  %)
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default SectionOne;
