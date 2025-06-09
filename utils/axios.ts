/* eslint-disable @typescript-eslint/no-explicit-any */
import axios from "axios";

export function flattenData(response: any) {
  return response?.data ?? [];
}

const axiosInstance = axios.create({
  baseURL: " http://127.0.0.1:8000/api/",
});

axiosInstance.interceptors.request.use((config) => {
  // Adjust 'Content-Type' for specific requests
  if (config.data instanceof FormData) {
    config.headers["Content-Type"] = "multipart/form-data";
  } else if (!config.headers["Content-Type"]) {
    config.headers["Content-Type"] = "application/json";
  }

  return config;
});

export default axiosInstance;
