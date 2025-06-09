"use client";
import "../../../../styles/js/us-aea-en";
import PieChart from "@/components/Chart/PieChartDashboard";
import { Dashboard } from "@/components/Dashboard/Dashboard";

interface DashboardComponent {
  dataDashboard: Dashboard | null;
}
const SectionFour: React.FC<DashboardComponent> = ({ dataDashboard }) => {
  return (
    <div className="col-span-12 rounded-sm border border-stroke bg-white px-7.5 py-6 shadow-default dark:border-strokedark dark:bg-boxdark xl:col-span-7">
      <h4 className="mb-2 text-xl font-semibold text-black dark:text-white">
        TBD Chart
      </h4>
      <div className="container items-center flex justify-center h-90">
        <div id="chartOne" className="-ml-5">
          <PieChart dataDashboard={dataDashboard} />
        </div>
      </div>
    </div>
  );
};

export default SectionFour;
