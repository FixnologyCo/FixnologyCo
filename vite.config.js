import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    optimizeDeps: {
        include: ['ziggy-js'],
      },    
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            '@images': path.resolve(__dirname, 'resources/images'), 
            'events': path.resolve(__dirname, 'node_modules/events'),
            'irc': path.resolve(__dirname, 'node_modules/ziggy'), 
            'colors': path.resolve(__dirname, 'node_modules/colors'),
        }
    }
});
