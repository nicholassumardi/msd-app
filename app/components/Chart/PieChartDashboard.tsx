"use client";

import dynamic from "next/dynamic";
import "chart.js/auto";
import { Dashboard } from "@/components/Dashboard/Dashboard";

interface DashboardComponent {
  dataDashboard: Dashboard | null;
}
const Pie = dynamic(() => import("react-chartjs-2").then((mod) => mod.Pie), {
  ssr: false,
});

const PieChart: React.FC<DashboardComponent> = ({ dataDashboard }) => {
  const labels = dataDashboard?.dataUserCompany?.map((company) => company.name);
  const dataValues = dataDashboard?.dataUserCompany?.map(
    (company) => company.user_count
  );

  const data = {
    labels: labels,
    datasets: [
      {
        label: "Test Dummy",
        data: dataValues,
        backgroundColor: [
          "rgba(255, 99, 132, 0.9)",
          "rgba(54, 162, 235, 0.9)",
          "rgba(255, 206, 86, 0.9)",
          "rgba(153, 102, 255, 0.9)",
          "rgba(255, 159, 64, 0.9)",
        ],
        borderColor: [
          "rgba(255, 99, 132, 1)",
          "rgba(54, 162, 235, 1)",
          "rgba(255, 206, 86, 1)",
          "rgba(153, 102, 255, 1)",
          "rgba(255, 159, 64, 1)",
        ],
        borderWidth: 1,
      },
    ],
  };

  const options = {
    plugins: {
      legend: {
        labels: {
          font: {
            family: "Satoshi, sans-serif",
            size: 13,
            weight: 550,
          },
        },
      },
    },
  };
  return <Pie data={data} options={options} />;
};

export default PieChart;
