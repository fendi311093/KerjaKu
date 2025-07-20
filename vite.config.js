import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/filament/dashboard/theme.css'
            ],
            refresh: true,
        }),
    ],
    server: {
        hmr: {
            host: "localhost",
        },
        watch: {
            usePolling: true, //untuk memastikan perubahan file terdeteksi
        },
    },
    css: {
        postcss: './postcss.config.js'
    },
});