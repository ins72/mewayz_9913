<div class="relative">
    <button wire:click="toggleTheme" 
            class="group flex items-center justify-center w-10 h-10 rounded-lg transition-all duration-300 ease-in-out
                   bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700
                   focus:ring-2 focus:ring-blue-500 focus:outline-none focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900
                   border border-gray-200 dark:border-gray-700"
            title="{{ $theme === 'light' ? __('Switch to Dark Mode') : __('Switch to Light Mode') }}">
        
        <div class="relative w-5 h-5 transition-transform duration-300 ease-in-out">
            @if($theme === 'light')
                <!-- Sun icon for light mode -->
                <svg class="w-5 h-5 text-yellow-500 transition-all duration-300 ease-in-out transform group-hover:rotate-12" 
                     fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z"/>
                </svg>
            @else
                <!-- Moon icon for dark mode -->
                <svg class="w-5 h-5 text-blue-400 transition-all duration-300 ease-in-out transform group-hover:scale-110" 
                     fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9.528 1.718a.75.75 0 01.162.819A8.97 8.97 0 009 6a9 9 0 009 9 8.97 8.97 0 003.463-.69.75.75 0 01.981.98 10.503 10.503 0 01-9.694 6.46c-5.799 0-10.5-4.701-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 01.818.162z"/>
                </svg>
            @endif
        </div>
    </button>
    
    <!-- Theme indicator tooltip -->
    <div class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
        <div class="bg-gray-900 dark:bg-gray-700 text-white text-xs px-2 py-1 rounded whitespace-nowrap">
            {{ $theme === 'light' ? __('Dark Mode') : __('Light Mode') }}
        </div>
    </div>
    
    <script>
        document.addEventListener('livewire:initialized', function() {
            // Set initial theme
            const initialTheme = '{{ $theme }}';
            document.body.setAttribute('data-theme', initialTheme);
            
            // Listen for theme changes
            Livewire.on('theme-changed', function(data) {
                const theme = data.theme || data;
                document.body.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
                
                // Smooth transition for theme switch
                document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
            });
        });
        
        // Apply saved theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                document.body.setAttribute('data-theme', savedTheme);
            }
        });
    </script>
</div>