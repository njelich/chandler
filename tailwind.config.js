const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue',
    './lang/**/*.php',
    './lang/*.json',
  ],

  safelist: [
    'bg-neutral-200',
    'text-neutral-800',
    'bg-red-200',
    'text-red-600',
    'bg-amber-200',
    'text-amber-600',
    'bg-emerald-200',
    'text-emerald-600',
    'bg-slate-200',
    'text-slate-600',
    'bg-sky-200',
    'text-sky-600',
    {
      pattern: /bg-(green)-(100|200|300|400|500|600|700|800|900)/,
    },
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ['Nunito', ...defaultTheme.fontFamily.sans],
      },
    },
  },

  darkMode: 'class',

  plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
};
