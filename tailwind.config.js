/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./views/**/*.php",
    "./public/index.php",
  ],
  theme: {
    extend: {
      colors: {
        primary: '#2563eb',
        secondary: '#64748b',
      },
    },
  },
  plugins: [],
}
