import { Evaluation } from "@/evaluation/page";
import { BarChart } from "@mantine/charts";

interface Props {
  dataEvaluation: Evaluation | null;
}

const BarChartEval = ({ dataEvaluation }: Props) => {
  // Transform evaluation data into chart-friendly format
  const chartData = dataEvaluation
    ? [
        {
          category: "Competencies",
          Competent: dataEvaluation.total_competent,
          "Non-Competent": dataEvaluation.total_non_competent,
          "Remedial Competent": dataEvaluation.total_remedial_competent,
          "In Progress": dataEvaluation.total_in_progress_assessment,
          Canceled: dataEvaluation.cancel_assessment,
          Assessments: dataEvaluation.total_assessment,
        },
      ]
    : [];

  return (
    <BarChart
      h={300}
      data={chartData}
      dataKey="category"
      valueFormatter={(value) => new Intl.NumberFormat("en-US").format(value)}
      withBarValueLabel
      series={[
        { name: "Competent", color: "green.6" },
        { name: "Non-Competent", color: "black.6" },
        { name: "Remedial Competent", color: "yellow.6" },
        { name: "In Progress", color: "blue.6" },
        { name: "Canceled", color: "red.6" },
        { name: "Assessments", color: "violet.6" },
      ]}
      tickLine="xy"
      gridAxis="xy"
      yAxisProps={{ width: 80 }}
      legendProps={{ verticalAlign: "bottom" }}
    />
  );
};
export default BarChartEval;
