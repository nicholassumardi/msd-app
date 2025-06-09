/* eslint-disable @typescript-eslint/no-explicit-any */
import { NextApiRequest } from "next";
import axiosInstance from "./axios";
import { EligibleIKWTrainer, Evaluation } from "../pages/api/admin/evaluation";

const showTrainingGeneral = async (id: string | null) => {
  const response = await axiosInstance.get<Evaluation>(
    `admin/evaluation/show_training_general/${id}`
  );

  return response;
};

const showTrainingRKI = async (id: string | null) => {
  const response = await axiosInstance.get<Evaluation>(
    `admin/evaluation/show_training_rki/${id}`
  );

  return response;
};

const showDetailRKI = async (req: NextApiRequest, id: string | null) => {
  const { current_page } = req.query;
  const response = await axiosInstance.get<Evaluation>(
    `admin/evaluation/show_detail_rki/${id}`,
    {
      params: {
        current_page: current_page,
      },
    }
  );

  return response;
};

const showEvaluationPagination = async (req: NextApiRequest) => {
  const { start, size, filters, globalFilter, sorting } = req.query;
  const response = await axiosInstance.get<Evaluation>(
    `admin/evaluation/show_evaluation_pagination`,
    {
      params: {
        start: start,
        size: size,
        filters: filters,
        globalFilter: globalFilter,
        sorting: sorting,
      },
    }
  );

  return response;
};

const showDataVisualization = async (req: NextApiRequest) => {
  // const { start, size, filters, globalFilter, sorting } = req.query;
  const response = await axiosInstance.get<Evaluation>(
    `admin/evaluation/show_data_visualization`,
    {
      // params: {
      //   start: start,
      //   size: size,
      //   filters: filters,
      //   globalFilter: globalFilter,
      //   sorting: sorting,
      // },
    }
  );

  return response;
};

const showIKWToTrain = async (req: NextApiRequest) => {
  const { start, size, filters, globalFilter, sorting, department_id } =
    req.query;
  const response = await axiosInstance.get<Evaluation>(
    `admin/evaluation/show_ikw_to_train`,
    {
      params: {
        start: start,
        size: size,
        filters: filters,
        globalFilter: globalFilter,
        sorting: sorting,
        department_id: department_id,
      },
    }
  );

  return response;
};

const showEligibleIKWTrainer = async (req: NextApiRequest) => {
  const { trainer_id } = req.query;
  const response = await axiosInstance.get<EligibleIKWTrainer>(
    `admin/evaluation/show_eligible_ikw`,
    {
      params: {
        trainer_id: trainer_id,
      },
    }
  );

  return response;
};

const showTraineeByTrainerIKW = async (req: NextApiRequest) => {
  const { ikw_id, start, size, filters, globalFilter, sorting } = req.query;
  const response = await axiosInstance.get<Evaluation>(
    `admin/evaluation/show_trainee_by_ikw`,
    {
      params: {
        start: start,
        size: size,
        filters: filters,
        globalFilter: globalFilter,
        sorting: sorting,
        ikw_id: ikw_id,
      },
    }
  );

  return response;
};

export const handlerMapEvaluation: Record<
  string,
  (req: NextApiRequest, id: string | null) => Promise<any>
> = {
  showTrainingGeneral: (_, id) => showTrainingGeneral(id),
  showTrainingRKI: (_, id) => showTrainingRKI(id),
  showDetailRKI: (req, id) => showDetailRKI(req, id),
  showEvaluationPagination: (req) => showEvaluationPagination(req),
  showDataVisualization: (req) => showDataVisualization(req),
  showIKWToTrain: (req) => showIKWToTrain(req),
  showEligibleIKWTrainer: (req) => showEligibleIKWTrainer(req),
  showTraineeByTrainerIKW: (req) => showTraineeByTrainerIKW(req),
};
