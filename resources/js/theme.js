// Theme initialization script
(function() {
    'use strict';
    
    // Get saved theme from localStorage or use system preference
    function getInitialTheme() {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            return savedTheme;
        }
        
        // Check if user prefers dark mode
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return 'dark';
        }
        
        return 'light';
    }
    
    // Apply theme to document
    function applyTheme(theme) {
        document.body.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
    }
    
    // Initialize theme on page load
    const initialTheme = getInitialTheme();
    applyTheme(initialTheme);
    
    // Listen for system theme changes
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
            const newTheme = e.matches ? 'dark' : 'light';
            applyTheme(newTheme);
        });
    }
    
    // Listen for theme toggle events
    document.addEventListener('theme-changed', function(e) {
        applyTheme(e.detail);
    });
})();