// src/components/NoDataFound.js
import React from "react";
import { Text } from "@mantine/core";
import Image from "next/image";

const NoDataFound = () => {
  return (
    <div className="grid justify-center text-center place-items-center font-satoshi md:p-25">
      <Image
        src="/images/no_data_logo.png"
        width={300}
        height={300}
        sizes="100vw"
        alt=""
        className="rounded-full"
      />

      <div className="max-w-75">
        <Text fw={750} fz={35} className="font-bold">
          No Data Found
        </Text>
        <Text fw={300} c="dimmed" fz={20} className="font-bold break-normal">
          Please ensure that you have the relevant data for this page
        </Text>
      </div>
    </div>
  );
};

export default NoDataFound;
