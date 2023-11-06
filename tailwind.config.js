/** @type {import('tailwindcss').Config} */
module.exports = {
  important: true,
  content: [
    "./themes/**/*.{html,php,js,ts,jsx,tsx}",
    "./app/Views/**/*.{html,php,js,ts,jsx,tsx}",
    "./app/Views/**/**/*.{html,php,js,ts,jsx,tsx}",
  ],
  safelist: [
    'alert-success',
    'alert-error',
    'alert-info',
    'progress-success',
    'progress-error',
    'progress-info',
  ],
  theme: {
    extend: {},
  },
  plugins: [require("@tailwindcss/typography"), require("daisyui")],
};
