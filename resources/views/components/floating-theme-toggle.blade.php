<!-- Floating Theme Toggle -->
<div class="fixed bottom-6 right-6 z-50">
    <livewire:components.theme-toggle />
</div>

<style>
/* Enhanced theme toggle animations */
.theme-toggle-floating {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Theme transition effects */
body[data-theme="dark"] {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

body[data-theme="light"] {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Custom theme switch animation */
[data-theme="dark"] .theme-icon-sun {
    opacity: 0;
    transform: rotate(180deg) scale(0.5);
}

[data-theme="light"] .theme-icon-moon {
    opacity: 0;
    transform: rotate(-180deg) scale(0.5);
}

[data-theme="dark"] .theme-icon-moon {
    opacity: 1;
    transform: rotate(0deg) scale(1);
}

[data-theme="light"] .theme-icon-sun {
    opacity: 1;
    transform: rotate(0deg) scale(1);
}
</style>