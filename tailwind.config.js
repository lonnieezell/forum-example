/** @type {import('tailwindcss').Config} */
module.exports = {
  important: true,
  content: [
    "./themes/**/*.{html,php,js,ts,jsx,tsx}",
    "./app/Views/**/*.{html,php,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {},
  },
  plugins: [require("@tailwindcss/typography"), require("daisyui")],
};
