/* eslint-disable @typescript-eslint/no-explicit-any */
import { useEffect, useState } from "react";
import { IKWS } from "../../pages/types/ikws";
import ErrorNotification from "@/components/Notifications/ErrorNotification";
import axios from "axios";
import { RKI } from "../../pages/types/rki";
import { option } from "../../pages/types/option";

const useIKWData = () => {
  const [globalFilter, setGlobalFilter] = useState("");
  const [dataIKW, setDataIKW] = useState<option[]>([]);
  const [dataRKI, setDataRKI] = useState<RKI[]>([]);

  const getDataIKW = async () => {
    try {
      const response = await axios.get(
        "/api/admin/master_data/job_family/ikws?type=showPagination",
        {
          params: {
            globalFilter: globalFilter ?? "",
          },
        }
      );

      const data = response.data.data.map((item: any) => ({
        value: item.id.toString(),
        label: item.code,
        // code: item.employeeStructure.name + " - " + item.group,
      }));
      setDataIKW(data);
      console.log("data", data);
    } catch (err: any) {
      if (err.response) {
        ErrorNotification({
          title: "Server Error",
          message: err.response.data.error,
        });
      }
    }
  };

  const getDataRKI = async () => {
    try {
      const response = await axios.get(
        "/api/admin/rki?type=showRKIPagination",
        {
          params: {
            start: pagination.pageIndex * pagination.pageSize,
            size: pagination.pageSize,
            filters: JSON.stringify(columnFilters ?? []),
            globalFilter: globalFilter ?? "",
            sorting: JSON.stringify(sorting ?? []),
          },
        }
      );
      setDataRKI(response.data.data);
    } catch (err: any) {
      if (err.response) {
        ErrorNotification({
          title: "Server Error",
          message: err.response.data.error,
        });
      }
    }
  };

  useEffect(() => {
    getDataIKW();
    // getDataRKI();
  }, []);

  return {
    globalFilter,
    setGlobalFilter,
    dataIKW,
    setDataIKW,
  };
};

export default useIKWData;
