/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.{js,jsx,ts,tsx}",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        // Professional color palette
        background: {
          light: '#FAFBFC',
          dark: '#101010',
        },
        surface: {
          light: '#FFFFFF',
          dark: '#191919',
        },
        text: {
          primary: {
            light: '#1A202C',
            dark: '#F1F1F1',
          },
          secondary: {
            light: '#718096',
            dark: '#7B7B7B',
          },
          muted: {
            light: '#A0AEC0',
            dark: '#6C757D',
          },
        },
        button: {
          primary: {
            bg: {
              light: '#2D3748',
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
              light: '#E2E8F0',
              dark: '#282828',
            },
            text: {
              light: '#2D3748',
              dark: '#F1F1F1',
            },
          },
        },
        accent: {
          primary: {
            light: '#667EEA',
            dark: '#818CF8',
          },
          success: {
            light: '#48BB78',
            dark: '#68D391',
          },
          warning: {
            light: '#ED8936',
            dark: '#F6AD55',
          },
          danger: {
            light: '#F56565',
            dark: '#FC8181',
          },
        },
        border: {
          light: '#E2E8F0',
          dark: '#282828',
        },
      },
      animation: {
        'fade-in': 'fadeIn 0.3s ease-out',
        'slide-in': 'slideIn 0.3s ease-out',
      },
      backdropBlur: {
        xs: '2px',
      },
    },
  },
  plugins: [],
}