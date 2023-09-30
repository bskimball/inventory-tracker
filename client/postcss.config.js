const autoprefixer = require("autoprefixer");
const purgeCSSPlugin = require("@fullhuman/postcss-purgecss");

module.exports = {
  syntax: "postcss-scss",
  plugins: [
    autoprefixer(),
    process.env.NODE_ENV === "production" &&
      purgeCSSPlugin({
        content: ["./src/**/*.vue", "./src/**/*.js"],
      }),
  ],
};
