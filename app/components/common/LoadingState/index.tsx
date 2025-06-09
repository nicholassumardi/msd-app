import { LoadingOverlay } from "@mantine/core";

const LoadingState = () => {
  return (
    <LoadingOverlay
      visible={true}
      zIndex={1000}
      overlayProps={{ blur: 2 }}
      loaderProps={{ color: "violet", type: "oval", size: "xl" }}
    />
  );
};

export default LoadingState;
