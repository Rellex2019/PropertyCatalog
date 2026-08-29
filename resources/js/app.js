import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        
        // Принудительно исправляем URL в Ziggy
        if (props.initialPage.props.ziggy) {
            props.initialPage.props.ziggy.url = window.location.origin;
            props.initialPage.props.ziggy.location = window.location.origin;
        }
        
        app.use(plugin);
        app.use(ZiggyVue, props.initialPage.props.ziggy);
        app.mount(el);
        
        return app;
    },
    progress: {
        color: '#4B5563',
    },
});