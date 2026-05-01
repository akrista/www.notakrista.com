import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import vue from '@vitejs/plugin-vue';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
    optimizeDeps: {
        include: [
            'react',
            'react-dom',
            'vue',
            'svelte',
            '@inertiajs/react',
            '@inertiajs/vue3',
            '@inertiajs/svelte',
            'axios',
        ],
        exclude: ['@inertiajs/core/server'],
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app-islands.js',
                'resources/js/app-react.tsx',
                'resources/js/app-vue.ts',
                'resources/js/app-svelte.ts',
                'resources/css/filament/admin/theme.css',
            ],
            ssr: [
                'resources/js/ssr.tsx',
                'resources/js/ssr-vue.ts',
                'resources/js/ssr-svelte.ts',
            ],
            refresh: true,
        }),
        react({
            babel: {
                plugins: ['babel-plugin-react-compiler'],
            },
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        svelte(),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
    ],
    server: {
        cors: true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
    build: {
        cssCodeSplit: true,
        rolldownOptions: {
            output: {
                assetFileNames: (assetInfo) => {
                    if (!assetInfo.name) {
                        return 'assets/[name]-[hash][extname]';
                    }

                    const info = assetInfo.name.split('.');
                    const ext = info[info.length - 1];

                    if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(ext)) {
                        return 'images/[name]-[hash][extname]';
                    }

                    if (/woff2?|eot|ttf|otf/i.test(ext)) {
                        return 'fonts/[name]-[hash][extname]';
                    }

                    return 'assets/[name]-[hash][extname]';
                },
                chunkFileNames: 'js/[name]-[hash].js',
                entryFileNames: 'js/[name]-[hash].js',
                manualChunks: (id) => {
                    if (id.includes('node_modules')) {
                        if (id.includes('react') || id.includes('react-dom')) {
                            return 'react-vendor';
                        }
                        if (id.includes('vue')) {
                            return 'vue-vendor';
                        }
                        if (id.includes('svelte')) {
                            return 'svelte-vendor';
                        }
                        if (id.includes('@inertiajs')) {
                            return 'inertia';
                        }
                        if (id.includes('axios')) {
                            return 'axios';
                        }
                        return 'vendor';
                    }
                },
            },
        },
        sourcemap: false,
        reportCompressedSize: true,
        chunkSizeWarningLimit: 600,
    },
});
