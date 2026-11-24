import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { createSSRApp, h } from 'vue';
import { renderToString } from 'vue/server-renderer';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { route as ziggyRoute, ZiggyVue } from '../../vendor/tightenco/ziggy';

createServer((page) => {
    const ziggyConfig = {
        ...page.props.ziggy,
        location: new URL(page.props.ziggy.location),
    };

    globalThis.route = (name, params, absolute) =>
        ziggyRoute(name, params, absolute, ziggyConfig);

    return createInertiaApp({
        page,
        render: renderToString,
        title: (title) => title,
        resolve: (name) =>
            resolvePageComponent(
                `./Pages/${name}.vue`,
                import.meta.glob('./Pages/**/*.vue'),
            ),
        setup({ App, props, plugin }) {
            return createSSRApp({ render: () => h(App, props) })
                .use(plugin)
                .use(ZiggyVue, ziggyConfig);
        },
    });
}, { cluster: true });
