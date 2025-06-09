/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";

import ChartContainer from "@dormammuuuuu/nextjs-orgchart";
import "@dormammuuuuu/nextjs-orgchart/ChartContainer.css";
import "@dormammuuuuu/nextjs-orgchart/ChartNode.css";
// import "../../../styles/css/custom-hierarchy.css";
import { useEffect, useRef } from "react";

interface ChildComponentProps {
  data: any;
}

const LEVEL_CONFIG = {
  BASE_HEIGHT: 200, // Level 0 line height
  DECREMENT: 15, // Reduce 15px per level
  MIN_HEIGHT: 50, // Minimum line height
};

const MyNodeTemplate = ({ nodeData }: any) => {
  const nodeRef = useRef<HTMLDivElement>(null);

  // Calculate line height based on API level
  const lineHeight = Math.max(
    LEVEL_CONFIG.BASE_HEIGHT - nodeData.level * LEVEL_CONFIG.DECREMENT,
    LEVEL_CONFIG.MIN_HEIGHT
  );

  useEffect(() => {
    if (nodeRef.current) {
      const ocNode = nodeRef.current.closest(".oc-node");
      if (ocNode) {
        // Set CSS variable for connection lines
        (ocNode as HTMLElement).style.setProperty(
          "--line-height",
          `${lineHeight}px`
        );
      }
    }
  }, [lineHeight]);

  return (
    <div
      ref={nodeRef}
      className="relative inline-block border border-gray-300 text-gray-700 shadow-5 w-[600px] h-[300px] overflow-hidden"
      style={{ marginTop: `${lineHeight - 10}px` }}
    >
      {/* Your exact node structure preserved */}
      <p className="p-10 mx-30 font-satoshi text-lg font-black">
        {nodeData.name ?? ""} {nodeData.jobCode ?? ""}
      </p>
      {Object.keys(nodeData.desc).length > 0 &&
        Object.values(nodeData.desc).map((data: any, index: number) =>
          data.employee_type === "Staff" ? (
            <p key={index} className="my-3 text-md font-bold">
              {data.group} ({data.id_structure}) {data.id_staff} {data.pic}
            </p>
          ) : (
            <p key={index} className="my-3 text-md font-bold">
              {data.group} {data.employee_number} {data.pic}
            </p>
          )
        )}
    </div>
  );
};

const HierarchyChart: React.FC<ChildComponentProps> = ({ data }) => {
  const chartRef = useRef<any>(null);

  const exportChart = () => {
    chartRef?.current?.exportTo("Struktur", "pdf");
  };

  return (
    <div className="flex flex-col h-[80vh] min-h-[500px] max-h-[800px] w-full">
      <button
        onClick={exportChart}
        className="mb-4 px-4 py-2 w-fit bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
      >
        Export Chart
      </button>

      <div className="relative flex-1 border rounded-lg overflow-hidden shadow-sm">
        <ChartContainer
          ref={chartRef}
          datasource={data}
          pan={true}
          zoom={true}
          NodeTemplate={MyNodeTemplate}
          draggable={true}
          collapsible={true}
          className="w-full h-full bg-gray-50"
          zoomInitial={0.7}
          zoomMin={0.3}
          zoomMax={1.5}
        />
      </div>
    </div>
  );
};

export default HierarchyChart;
