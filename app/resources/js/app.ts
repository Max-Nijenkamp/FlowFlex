import { createApp, h, type DefineComponent } from 'vue'
import { createInertiaApp, router } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

// Perceived performance: page transitions start instantly; NProgress-style
// bar replaced by skeletons inside pages (perceived-performance pattern).
createInertiaApp({
    title: (title) => (title ? `${title} — FlowFlex` : 'FlowFlex'),
    resolve: (name) =>
        resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el)
    },
    progress: false, // no spinner bars — skeletons carry the loading state
})

export { router }
