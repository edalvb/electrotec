import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  // Externalizar paquetes del lado del servidor
  serverExternalPackages: ["pdfkit"],
  // Evitar que errores de ESLint bloqueen el build (se recomienda corregirlos posteriormente)
  eslint: {
    ignoreDuringBuilds: true,
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
