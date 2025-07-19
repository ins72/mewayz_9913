const { override } = require('customize-cra');

module.exports = override(
  (config, env) => {
    // Disable host check for emergent preview
    if (env === 'development') {
      config.devServer = {
        ...config.devServer,
        allowedHosts: 'all',
        host: '0.0.0.0',
        port: 3000,
        proxy: {
          '/api': {
            target: 'http://localhost:8001',
            changeOrigin: true,
            secure: false,
            logLevel: 'debug'
          }
        }
      };
    }
    return config;
  }
);