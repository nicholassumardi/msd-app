import withBundleAnalyzer from '@next/bundle-analyzer';

const isAnalyze = process.env.ANALYZE === 'true';

const nextConfig = {
  reactStrictMode: false,
};

export default withBundleAnalyzer({
  enabled: isAnalyze,
})(nextConfig);
