import colors from 'tailwindcss/colors.js';
import preset from './vendor/filament/filament/tailwind.config.preset' assert { type: "js" };

/** @type {import('tailwindcss').Config} */
module.exports = {
  presets: [preset],
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/Filament/**/*.php",
    "./resources/views/filament/**/*.blade.php",
    "./vendor/filament/**/*.blade.php",
    'node_modules/preline/dist/*.js',
  ],
  theme: {
    extend: {
      colors: {
        gray: colors.gray,
        blue: colors.blue,
        cyan: colors.cyan,
        slate: colors.slate,
        neutral: {
          50: '#fafafa',
          100: '#f5f5f5',
          200: '#e5e5e5',
          300: '#d4d4d4',
          400: '#a3a3a3',
          500: '#737373',
          600: '#525252',
          700: '#404040',
          800: '#262626',
          900: '#171717',
          950: '#0a0a0a',
        },
      },
      fontFamily: {
        sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/aspect-ratio'),
    // require('preline/plugin'),
    require('@tailwindcss/typography'),
  ],
  darkMode: 'class',
} 