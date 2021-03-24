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
  }
};
