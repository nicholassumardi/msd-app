/* eslint-disable @typescript-eslint/no-explicit-any */
import { NextApiRequest } from "next";
import axiosInstance from "../axios";
import { Hierarchy, Structure } from "../../pages/api/admin/structure";

const importStructureMapping = async (req: NextApiRequest) => {
  const response = await axiosInstance.post<Structure>(
    "admin/structure/import",
    req,
    {
      headers: {
        "Content-Type": req.headers["content-type"],
      },
      responseType: "arraybuffer",
    }
  );

  return response;
};

const importStructure = async (req: NextApiRequest) => {
  const response = await axiosInstance.post<Structure>(
    "admin/structure/import_user_job_code",
    req,
    {
      headers: {
        "Content-Type": req.headers["content-type"],
      },
    }
  );

  return response;
};

const storeMapping = async (req: NextApiRequest) => {
  const response = await axiosInstance.post<Structure>(
    "admin/structure/store_mapping",
    req.body
  );

  return response;
};

const storeStructure = async (req: NextApiRequest) => {
  const response = await axiosInstance.post<Structure>(
    "admin/structure/store",
    req.body
  );

  return response;
};

const storeMappingRequest = async (req: NextApiRequest) => {
  const response = await axiosInstance.post<Structure>(
    "admin/structure/store_mapping_request",
    req.body
  );

  return response;
};

const structureMappingByDepartment = async (req: NextApiRequest) => {
  const { current_page, per_page, globalFilter, id_department } = req.query;
  const response = await axiosInstance.get<Structure>(
    `admin/structure/structure_mapping`,
    {
      params: {
        current_page: current_page,
        per_page: per_page,
        globalFilter: globalFilter,
        id_department: id_department,
      },
    }
  );

  return response;
};

const showMapping = async () => {
  const response = await axiosInstance.get<Structure["StructureMapping"]>(
    `admin/structure/show_mapping`
  );

  return response;
};

const showStructure = async () => {
  const response = await axiosInstance.get<Structure>(`admin/structure/show`);

  return response;
};

// get position and also jobcode
const showUserJobCode = async () => {
  const response = await axiosInstance.get<Structure>(
    `admin/structure/show_user_job_code`
  );

  return response;
};

const showStructurePagination = async (req: NextApiRequest) => {
  const { start, size, filters, globalFilter, sorting } = req.query;
  const response = await axiosInstance.get<Structure>(
    `admin/structure/show_structure_pagination`,
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

const showMappingHierarchy = async (req: NextApiRequest, id: string | null) => {
  const { ...params } = req.query;
  const response = await axiosInstance.get<Hierarchy>(
    `admin/structure/show_user_mapping_hierarchy/${id}`,
    { params: params }
  );

  return response;
};

const showMappingHierarchyUser = async (
  req: NextApiRequest,
  id: string | null
) => {
  const { ...params } = req.query;
  const response = await axiosInstance.get<Hierarchy>(
    `admin/structure/show_mapping_hierarchy/${id}`,
    { params: params }
  );

  return response;
};

const showMappingHierarchyParent = async (
  req: NextApiRequest,
  id: string | null
) => {
  const { ...params } = req.query;
  const response = await axiosInstance.get<Hierarchy>(
    `admin/structure/show_mapping_hierarchy_parent/${id}`,
    { params: params }
  );

  return response;
};

const showMappingHierarchyChildren = async (
  req: NextApiRequest,
  id: string | null
) => {
  const { ...params } = req.query;
  const response = await axiosInstance.get<Hierarchy>(
    `admin/structure/show_mapping_hierarchy_children/${id}`,
    { params: params }
  );

  return response;
};

const getMapping = async (id: string | null) => {
  const response = await axiosInstance.get<Structure>(
    `admin/structure/show_mapping/${id}`
  );

  return response;
};

const getStructure = async (id: string | null) => {
  const response = await axiosInstance.get<Structure>(
    `admin/structure/show/${id}`
  );

  return response;
};

const updateMapping = async (req: NextApiRequest, id: string | null) => {
  const response = await axiosInstance.put<Structure>(
    `admin/structure/update_mapping/${id}`,
    req.body
  );

  return response;
};
const updateMappingRequest = async (req: NextApiRequest, id: string | null) => {
  const response = await axiosInstance.post<Structure>(
    `admin/structure/update_mapping_request/${id}`,
    req.body
  );

  return response;
};

const updateStatus = async (id: string | null) => {
  const response = await axiosInstance.put<Structure>(
    `admin/structure/update_status/${id}`
  );

  return response;
};

const update = async (req: NextApiRequest, uuid: string | null) => {
  const response = await axiosInstance.put<Structure>(
    `admin/structure/update/${uuid}`,
    req.body
  );

  return response;
};

const deleteMapping = async (id: string | null) => {
  const response = await axiosInstance.delete<Structure>(
    `admin/structure/delete_mapping/${id}`
  );

  return response;
};

const deleteStructure = async (id: string | null) => {
  const response = await axiosInstance.delete<Structure>(
    `admin/structure/delete/${id}`
  );

  return response;
};

const moveMappingRequest = async (req: NextApiRequest, id: string | null) => {
  const response = await axiosInstance.post<Structure>(
    `admin/structure/move_mapping_request/${id}`,
    req.body
  );

  return response;
};

export const handlerMapStructure: Record<
  string,
  (req: NextApiRequest, id: string | null) => Promise<any>
> = {
  importStructureMapping: (req) => importStructureMapping(req),
  importStructure: (req) => importStructure(req),
  storeMapping: (req) => storeMapping(req),
  storeMappingRequest: (req) => storeMappingRequest(req),
  storeStructure: (req) => storeStructure(req),
  showStructure: () => showStructure(),
  showStructurePagination: (req) => showStructurePagination(req),
  showMapping: () => showMapping(),
  showUserJobCode: () => showUserJobCode(),
  structureMapping: (req) => structureMappingByDepartment(req),
  showMappingHierarchy: (req, id) => showMappingHierarchy(req, id),
  showMappingHierarchyUser: (req, id) => showMappingHierarchyUser(req, id),
  showMappingHierarchyParent: (req, id) => showMappingHierarchyParent(req, id),
  showMappingHierarchyChildren: (req, id) =>
    showMappingHierarchyChildren(req, id),
  getMapping: (_, id) => getMapping(id),
  getStructure: (_, id) => getStructure(id),
  updateMapping: (req, id) => updateMapping(req, id),
  updateMappingRequest: (req, id) => updateMappingRequest(req, id),
  updateStatus: (_, id) => updateStatus(id),
  update: (req, id) => update(req, id),
  deleteMapping: (_, id) => deleteMapping(id),
  deleteStructure: (_, id) => deleteStructure(id),
  moveMappingRequest: (req, id) => moveMappingRequest(req, id),
};
