/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],
    
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', 'ui-sans-serif', 'system-ui'],
            },
            colors: {
                // App Colors
                'app-bg': '#101010',
                'card-bg': '#191919',
                'primary-text': '#F1F1F1',
                'secondary-text': '#7B7B7B',
                
                // Button Colors
                'btn-primary-bg': '#FDFDFD',
                'btn-primary-text': '#141414',
                'btn-secondary-bg': '#191919',
                'btn-secondary-border': '#282828',
                'btn-secondary-text': '#F1F1F1',
                
                // Additional UI Colors
                'border-color': '#282828',
                'hover-bg': '#222222',
                'success': '#10B981',
                'error': '#EF4444',
                'warning': '#F59E0B',
                'info': '#3B82F6',
            },
            backgroundColor: {
                'app': '#101010',
                'card': '#191919',
            },
            textColor: {
                'primary': '#F1F1F1',
                'secondary': '#7B7B7B',
            },
            borderColor: {
                'primary': '#282828',
            }
        },
    },
    
    plugins: [
        require('@tailwindcss/forms'),
    ],
};