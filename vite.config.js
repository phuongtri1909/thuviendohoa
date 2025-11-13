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
                'resources/assets/frontend/css/albums.css',
                'resources/assets/frontend/css/components/collection-card.css',
                'resources/assets/frontend/css/components/featured-collections.css',
                'resources/assets/frontend/css/desktop.css',
                'resources/assets/frontend/css/footer.css',
                'resources/assets/frontend/css/search.css',
                'resources/assets/frontend/css/search-result.css',
                'resources/assets/frontend/css/styles-blog.css',
                'resources/assets/frontend/css/blog-sidebar.css',
                'resources/assets/frontend/css/get-link.css',
                'resources/assets/frontend/css/page-detail.css',
                'resources/assets/frontend/css/styles-auth.css',
                'resources/assets/frontend/css/information.css',
                'resources/assets/frontend/js/script.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    publicDir: 'public',
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
        middlewareMode: false,
        fs: {
            allow: ['..']
        }
    },
    
    
});
