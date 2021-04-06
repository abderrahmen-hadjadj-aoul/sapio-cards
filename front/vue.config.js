/* eslint @typescript-eslint/no-var-requires: "off" */
const path = require("path");

module.exports = {
  publicPath: "/app/",
  outputDir: path.resolve(__dirname, "../public/app"),
  css: {
    loaderOptions: {
      scss: {
        additionalData: `@import "~@/assets/_variables.scss";`
      }
    }
  },
  configureWebpack: {
    module: {
      rules: [
        {
          enforce: "pre",
          test: /\.(js|vue)$/,
          loader: "eslint-loader",
          exclude: /node_modules/,
          options: {
            //fix: true
          }
        }
      ]
    }
  }
};
