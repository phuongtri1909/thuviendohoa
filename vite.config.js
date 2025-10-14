import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/assets/frontend/css/styles.css',
                'resources/assets/frontend/css/styles-header.css',
                'resources/assets/admin/css/styles_admin.css',
                'resources/assets/frontend/css/home.css',
                'resources/assets/frontend/css/desktop.css',
                'resources/assets/frontend/css/footer.css',
                'resources/assets/frontend/css/search.css',
                'resources/assets/frontend/css/search-result.css',
                'resources/assets/frontend/css/styles-blog.css',
                'resources/assets/frontend/css/blog-sidebar.css'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: 'thuviendohoa.local',
        port: 5173,
        strictPort: true,
        cors: true,
        hmr: {
            host: 'thuviendohoa.local',
            protocol: 'http',
            port: 5173,
        },
    },
    
    
});
