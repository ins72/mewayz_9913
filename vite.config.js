import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/auth.css',
                'resources/sass/app.scss',
                'resources/sass/auth.scss',
                'resources/sass/dashboard.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});