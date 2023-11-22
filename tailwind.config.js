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
      "dark",
      "light",
      {
        my_dark: {
          primary: "#6a290d",
          "primary-content": "#fff",
          "base-content": "rgb(155, 156, 163)",
          "base-100": "rgb(30, 33, 41)",
        },
        my_light: {
          primary: "#ff6e42",
          "primary-content": "#fff",
          "base-content": "#000000de",
          "base-100": "rgb(249, 250, 251)",
        },
      },
    ],
  },
  plugins: [require("@tailwindcss/typography"), require("daisyui")],
};
