import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  experimental: {
    serverComponentsExternalPackages: ["pdfkit"],
  },
  webpack: (config, { isServer }) => {
    if (isServer) {
      const externals = Array.isArray(config.externals) ? config.externals : []
      externals.push("pdfkit")
      config.externals = externals
    }
    return config
  },
};

export default nextConfig;
