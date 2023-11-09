module.exports = (cfg) => {
  const dev = cfg.env === "development",
    scss = cfg.file.extname === ".scss";

  return {
    map: dev ? { inline: false } : false,
    parser: scss ? "postcss-scss" : false,
    plugins: [
      require("postcss-import"),
      require("postcss-advanced-variables")(),
      require("postcss-nested")(),
      require("autoprefixer")(),
    ],
  };
};
