import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { MotionPlugin } from '@vueuse/motion'
import { initializeTheme } from '@/composables/useAppearance'
import AppLayout from '@/layouts/AppLayout.vue'
import MarketingLayout from '@/layouts/MarketingLayout.vue'
import { initializeFlashToast } from '@/lib/flashToast'
import { i18n } from '@/i18n'

createInertiaApp({
    title: (title) => (title ? `${title} - FlowFlex` : 'FlowFlex — Your business, your tools in flow.'),
    layout: (name) => {
        if (name === 'Welcome' || name.startsWith('Marketing/')) return MarketingLayout
        return AppLayout
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
        app.use(plugin)
        app.use(i18n)
        app.use(MotionPlugin)
        app.mount(el)
    },
    progress: {
        color: '#4B5563',
    },
})

// This will set light / dark mode on page load...
initializeTheme()

// This will listen for flash toast data from the server...
initializeFlashToast()
