import Alpine from 'alpinejs';
import './bootstrap';

window.Alpine = Alpine;

Alpine.start();

// Global app functions
window.app = {
    // Toggle sidebar on mobile
    toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            sidebar.classList.toggle('open');
        }
    },
    
    // Close sidebar on mobile
    closeSidebar() {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            sidebar.classList.remove('open');
        }
    },
    
    // Show notification
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    },
    
    // Format currency
    formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    },
    
    // Format date
    formatDate(date) {
        return new Intl.DateTimeFormat('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        }).format(new Date(date));
    }
};

// Alpine.js components
Alpine.data('dashboard', () => ({
    sidebarOpen: false,
    
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
    },
    
    closeSidebar() {
        this.sidebarOpen = false;
    }
}));

Alpine.data('modal', () => ({
    open: false,
    
    show() {
        this.open = true;
        document.body.style.overflow = 'hidden';
    },
    
    hide() {
        this.open = false;
        document.body.style.overflow = 'auto';
    }
}));

Alpine.data('dropdown', () => ({
    open: false,
    
    toggle() {
        this.open = !this.open;
    },
    
    close() {
        this.open = false;
    }
}));

// Close dropdowns when clicking outside
document.addEventListener('click', (e) => {
    if (!e.target.closest('[x-data*="dropdown"]')) {
        Alpine.store('dropdown', { open: false });
    }
});

// Close sidebar when clicking outside on mobile
document.addEventListener('click', (e) => {
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
    
    if (sidebar && !sidebar.contains(e.target) && !sidebarToggle?.contains(e.target)) {
        sidebar.classList.remove('open');
    }
});

// Handle responsive sidebar
function handleResize() {
    const sidebar = document.querySelector('.sidebar');
    if (window.innerWidth >= 768 && sidebar) {
        sidebar.classList.remove('open');
    }
}

window.addEventListener('resize', handleResize);
document.addEventListener('DOMContentLoaded', handleResize);