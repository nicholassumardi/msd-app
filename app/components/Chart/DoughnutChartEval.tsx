/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";

import dynamic from "next/dynamic";
import "chart.js/auto";
import { Evaluation } from "@/evaluation/page";

interface EvaluationComponent {
  dataEvaluation: Evaluation | null;
}

const Doughnut = dynamic(
  () => import("react-chartjs-2").then((mod) => mod.Doughnut),
  { ssr: false }
);

const DoughnutChartEval: React.FC<EvaluationComponent> = ({
  dataEvaluation,
}) => {
  const totalAssessments = dataEvaluation?.total_assessment || 0;

  const categories = [
    { key: "total_competent", label: "Competent", color: "#10B981" },
    { key: "total_non_competent", label: "Non-Competent", color: "#000000" },
    { key: "total_remedial_competent", label: "Remedial", color: "#3B82F6" },
    { key: "cancel_assessment", label: "Canceled", color: "#FC0324" },
    {
      key: "total_in_progress_assessment",
      label: "In Progress",
      color: "#FFF700",
    },
  ];

  const chartData = {
    labels: categories.map((c) => c.label),
    datasets: [
      {
        data: categories.map((c) => {
          const value = dataEvaluation?.[c.key as keyof Evaluation] || 0;
          return totalAssessments > 0 ? (value / totalAssessments) * 100 : 0;
        }),
        backgroundColor: categories.map((c) => `${c.color}CC`),
        borderColor: categories.map((c) => c.color),
        borderWidth: 2,
        hoverOffset: 8,
        hoverBorderColor: "#fff",
      },
    ],
  };

  const options = {
    cutout: "65%",
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: "bottom" as const,
        labels: {
          color: "#6B7280",
          font: {
            family: "Inter, sans-serif",
            size: 14,
            weight: 500, // Numeric weight
          },
          padding: 20,
          usePointStyle: true,
        },
      },
      tooltip: {
        callbacks: {
          label: (context: any) => {
            const label = context.label;
            const value =
              dataEvaluation?.[
                categories[context.dataIndex].key as keyof Evaluation
              ] || 0;
            const percentage = context.parsed.toFixed(1);
            return `${label}: ${percentage}% (${value} people)`;
          },
        },
      },
    },
  };

  return (
    <div className="relative h-full w-full p-4">
      <Doughnut data={chartData} options={options} />

      {/* Center Text - Add pointer-events-none */}
      {totalAssessments > 0 && (
        <div className="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
          <div className="text-center">
            <span className="text-2xl font-bold text-gray-700 block">
              {totalAssessments}
            </span>
            <span className="text-sm text-gray-500 block">
              Total Assessments
            </span>
          </div>
        </div>
      )}
    </div>
  );
};

export default DoughnutChartEval;
