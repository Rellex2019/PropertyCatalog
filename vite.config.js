import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

// Получаем внешний URL для Vite
const devServerUrl = process.env.VITE_DEV_SERVER_URL;

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        watch: {
            usePolling: true,
        },
        hmr: {
            // Если URL есть, используем его хост, иначе localhost
            host: new URL(devServerUrl).hostname,
            protocol: 'wss', // Явно указываем wss для HTTPS
            clientPort: 443,
        },
        // Добавляем CORS настройки
        cors: {
            origin: '*',
            methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
            credentials: true,
        },
    },
});