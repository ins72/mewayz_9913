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
                'resources/css/app.css',
                'resources/sass/app.scss',

                'resources/sass/builder.scss',
                'resources/sass/create.scss',
                'resources/sass/site.scss',

                // Console
                'resources/sass/console/console.scss',
                'resources/sass/console/console.placeholder.scss',
                'resources/sass/console/console.sidebar.scss',
                'resources/sass/console/community.scss',


                // Auth
                'resources/sass/auth/auth.scss',

                'resources/js/app.js',
                'resources/js/moreUtils.js',
                'resources/js/exportUtils.js',
                'resources/js/yenaWire.js',
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
