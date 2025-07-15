import { defineConfig } from 'vite'; 
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import svgLoader from 'vite-svg-loader';
import { ViteMinifyPlugin } from 'vite-plugin-minify'


export default defineConfig({
    // server: {
    //     // Use later npm run dev -- --host 172.20.10.3
    //     // host: true
    //     // cors: false,
    //     hmr: {
    //         // host: 'localhost',
    //     },
    // },
    build: {
        cssCodeSplit: true,
    },
    plugins: [
        ViteMinifyPlugin({
            collapseBooleanAttributes: true,
            collapseWhitespace: true,
            removeAttributeQuotes: true,
            removeComments: true,
            minifyJS: true,
        }),
        svgLoader(),
        laravel({
            input: [
                'backend/resources/css/app.css',
                'backend/resources/sass/app.scss',

                'backend/resources/sass/builder.scss',
                'backend/resources/sass/create.scss',
                'backend/resources/sass/site.scss',

                // Console
                'backend/resources/sass/console/console.scss',
                'backend/resources/sass/console/console.placeholder.scss',
                'backend/resources/sass/console/console.sidebar.scss',


                // Auth
                'backend/resources/sass/auth/auth.scss',

                'backend/resources/js/app.js',
                'backend/resources/js/moreUtils.js',
                'backend/resources/js/exportUtils.js',
                'backend/resources/js/yenaWire.js',
            ],
            refresh: [
                'database/**'
            ],
        }),
        {
            name: 'blade',
            handleHotUpdate({ file, server }) {
                if (file.endsWith('.blade.php')) {
                    server.ws.send({
                        type: 'full-reload',
                        path: '*',
                    });
                }
            },
        },
        vue({ 
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: { 
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
});
