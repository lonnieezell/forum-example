/** @type {import('tailwindcss').Config} */
module.exports = {
  important: true,
  content: [
    "./themes/**/*.{html,php,js,ts,jsx,tsx}",
    "./app/Views/**/*.{html,php,js,ts,jsx,tsx}",
    "./app/Views/**/**/*.{html,php,js,ts,jsx,tsx}",
  ],
  safelist: [
    "alert-success",
    "alert-error",
    "alert-info",
    "progress-success",
    "progress-error",
    "progress-info",
    "badge-success",
    "badge-error",
  ],
  theme: {
    extend: {},
  },
  daisyui: {
    themes: [
      {
        light: {
          ...require("daisyui/src/theming/themes")["light"],
          primary: "#DD4815",
          "primary-content": "#fff",
          "base-content": "#000000de",
          "base-100": "rgb(249, 250, 251)",
        },
        dark: {
          ...require("daisyui/src/theming/themes")["dark"],
          primary: "#CD4417",
          "primary-content": "#fff",
          "base-content": "rgb(175, 176, 183)",
          "base-100": "#242424",
        },
      },
    ],
  },
  plugins: [require("@tailwindcss/typography"), require("daisyui")],
};
