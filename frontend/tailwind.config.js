/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.{js,jsx,ts,tsx}",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        // Custom color palette
        background: {
          light: '#FAFAFA',
          dark: '#101010',
        },
        surface: {
          light: '#FFFFFF',
          dark: '#191919',
        },
        text: {
          primary: {
            light: '#1A1A1A',
            dark: '#F1F1F1',
          },
          secondary: {
            light: '#6B6B6B',
            dark: '#7B7B7B',
          },
        },
        button: {
          primary: {
            bg: {
              light: '#1A1A1A',
              dark: '#FDFDFD',
            },
            text: {
              light: '#FFFFFF',
              dark: '#141414',
            },
          },
          secondary: {
            bg: {
              light: '#FFFFFF',
              dark: '#191919',
            },
            border: {
              light: '#E5E5E5',
              dark: '#282828',
            },
            text: {
              light: '#1A1A1A',
              dark: '#F1F1F1',
            },
          },
        },
      },
    },
  },
  plugins: [],
}