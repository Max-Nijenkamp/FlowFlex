<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { computed, onMounted, onUnmounted, ref } from 'vue'
import Logo from '@/Components/UI/Logo.vue'

const page = usePage<{ auth: { user: { name: string } | null } }>()
const user = computed(() => page.props.auth?.user ?? null)
const menuOpen = ref(false)
const productOpen = ref(false)
const productMenu = ref<HTMLElement | null>(null)

const productItems = [
    { href: '/features', label: 'HR & people', detail: 'Recruiting to payroll' },
    { href: '/features', label: 'Finance & accounting', detail: 'Ledger-first books' },
    { href: '/features', label: 'CRM & sales', detail: 'Pipeline to contract' },
    { href: '/features', label: 'Core platform', detail: 'Roles, audit, API' },
]

function onClickOutside(e: MouseEvent) {
    if (productMenu.value && !productMenu.value.contains(e.target as Node)) productOpen.value = false
}

function onEscape(e: KeyboardEvent) {
    if (e.key === 'Escape') productOpen.value = false
}

onMounted(() => {
    document.addEventListener('click', onClickOutside)
    document.addEventListener('keydown', onEscape)
})

onUnmounted(() => {
    document.removeEventListener('click', onClickOutside)
    document.removeEventListener('keydown', onEscape)
})
</script>

<template>
    <div class="min-h-screen flex flex-col bg-paper text-ink">
        <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:bg-ink focus:text-white focus:px-4 focus:py-2">
            Skip to content
        </a>

        <header class="sticky top-0 z-40 border-b border-line bg-paper/85 backdrop-blur-sm">
            <div class="mx-auto max-w-6xl px-6 h-[68px] flex items-center justify-between">
                <Link href="/" aria-label="FlowFlex home" class="shrink-0">
                    <Logo variant="dark" />
                </Link>

                <nav class="hidden md:flex items-center gap-8 text-[15px] font-medium text-ink-soft">
                    <div ref="productMenu" class="relative">
                        <button type="button"
                            class="flex items-center gap-1.5 hover:text-ink transition ease-out duration-150"
                            :class="productOpen ? 'text-ink' : ''"
                            :aria-expanded="productOpen"
                            @click="productOpen = !productOpen">
                            Product
                            <svg class="h-3.5 w-3.5 transition-transform ease-out duration-200" :class="productOpen ? 'rotate-180' : ''"
                                viewBox="0 0 16 16" fill="none">
                                <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div v-if="productOpen"
                            class="absolute left-1/2 top-full mt-3 w-[420px] -translate-x-1/2 rounded-2xl border border-line bg-white p-2 shadow-[0_16px_40px_-12px_rgba(17,24,39,0.18)]"
                            style="animation: ff-enter 0.18s cubic-bezier(0, 0, 0.2, 1)">
                            <div class="grid grid-cols-2 gap-1">
                                <Link v-for="item in productItems" :key="item.label" :href="item.href"
                                    class="rounded-xl px-4 py-3 hover:bg-paper transition ease-out duration-150"
                                    @click="productOpen = false">
                                    <span class="block text-sm font-semibold text-ink">{{ item.label }}</span>
                                    <span class="block text-[13px] text-ink-faint">{{ item.detail }}</span>
                                </Link>
                            </div>
                            <Link href="/features" class="mt-1 block rounded-xl bg-paper-deep px-4 py-3 text-sm font-medium text-ink hover:bg-line/60 transition ease-out duration-150"
                                @click="productOpen = false">
                                All modules →
                            </Link>
                        </div>
                    </div>
                    <Link href="/pricing" class="hover:text-ink transition ease-out duration-150">Pricing</Link>
                    <Link href="/about" class="hover:text-ink transition ease-out duration-150">About</Link>
                    <Link href="/contact" class="hover:text-ink transition ease-out duration-150">Contact</Link>
                </nav>

                <div class="hidden md:flex items-center gap-5">
                    <a v-if="user" href="/app"
                        class="rounded-full bg-ink px-5 py-2.5 text-sm font-semibold text-white hover:bg-accent transition ease-out duration-150">
                        Open workspace
                    </a>
                    <template v-else>
                        <Link href="/login" class="text-[15px] font-medium text-ink-soft hover:text-ink transition ease-out duration-150">
                            Sign in
                        </Link>
                        <Link href="/contact"
                            class="rounded-full bg-ink px-5 py-2.5 text-sm font-semibold text-white hover:bg-accent transition ease-out duration-150">
                            Talk to us
                        </Link>
                    </template>
                </div>

                <button type="button" class="md:hidden p-2 -mr-2" aria-label="Menu" @click="menuOpen = !menuOpen">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path v-if="!menuOpen" stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
                        <path v-else stroke-linecap="round" d="M6 6l12 12M18 6L6 18" />
                    </svg>
                </button>
            </div>

            <div v-if="menuOpen" class="md:hidden border-t border-line bg-paper px-6 py-4 space-y-3">
                <Link href="/features" class="block py-1.5 font-medium text-ink-soft" @click="menuOpen = false">Product</Link>
                <Link href="/pricing" class="block py-1.5 font-medium text-ink-soft" @click="menuOpen = false">Pricing</Link>
                <Link href="/about" class="block py-1.5 font-medium text-ink-soft" @click="menuOpen = false">About</Link>
                <Link href="/contact" class="block py-1.5 font-medium text-ink-soft" @click="menuOpen = false">Contact</Link>
                <Link href="/login" class="block py-1.5 font-medium text-ink-soft" @click="menuOpen = false">Sign in</Link>
            </div>
        </header>

        <main id="main" class="flex-1">
            <slot />
        </main>

        <footer class="border-t border-line bg-ink text-white">
            <div class="mx-auto max-w-6xl px-6 py-16">
                <div class="grid gap-12 md:grid-cols-[1.4fr_1fr_1fr_1fr]">
                    <div>
                        <Logo variant="light" />
                        <p class="mt-4 text-sm text-white/60 max-w-xs leading-relaxed">
                            One platform. Every tool. Always flexible.
                            Built for teams of 50 to 500.
                        </p>
                    </div>
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-[0.18em] text-white/40">Product</h3>
                        <div class="mt-4 space-y-2.5 text-sm">
                            <Link href="/features" class="block text-white/70 hover:text-white transition ease-out duration-150">All modules</Link>
                            <Link href="/pricing" class="block text-white/70 hover:text-white transition ease-out duration-150">Pricing</Link>
                            <Link href="/login" class="block text-white/70 hover:text-white transition ease-out duration-150">Sign in</Link>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-[0.18em] text-white/40">Company</h3>
                        <div class="mt-4 space-y-2.5 text-sm">
                            <Link href="/about" class="block text-white/70 hover:text-white transition ease-out duration-150">About</Link>
                            <Link href="/contact" class="block text-white/70 hover:text-white transition ease-out duration-150">Contact</Link>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-[0.18em] text-white/40">Legal</h3>
                        <div class="mt-4 space-y-2.5 text-sm">
                            <Link href="/terms" class="block text-white/70 hover:text-white transition ease-out duration-150">Terms</Link>
                            <Link href="/privacy" class="block text-white/70 hover:text-white transition ease-out duration-150">Privacy</Link>
                        </div>
                    </div>
                </div>
                <div class="mt-14 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-t border-white/10 pt-6 text-xs text-white/40">
                    <span>© {{ new Date().getFullYear() }} FlowFlex. Everything flows.</span>
                    <span>EU-hosted · GDPR-compliant · Data portable by default</span>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@keyframes ff-enter {
    from {
        opacity: 0;
        transform: translate(-50%, 4px) scale(0.99);
    }
    to {
        opacity: 1;
        transform: translate(-50%, 0) scale(1);
    }
}
</style>
