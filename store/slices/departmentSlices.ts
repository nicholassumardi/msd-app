import { createAsyncThunk, createSlice } from "@reduxjs/toolkit";
import axios from "axios";

const initialState = {};

export const fetchDepartment = createAsyncThunk(
  "department/fetchDepartments",
  async () => {
    const response = await axios.get("/api/admin/master_data/department");

    return response.data.data;
  }
);

const departmentSlices = createSlice({
  name: "department",
  initialState,
  reducers: {},
  extraReducers(builder) {
    builder.addCase(fetchDepartment.fulfilled, () => {});
    builder.addCase(fetchDepartment.fulfilled, () => {});
    builder.addCase(fetchDepartment.fulfilled, () => {});
  },
});

export const {} = departmentSlices.actions;

export default departmentSlices.reducer;
