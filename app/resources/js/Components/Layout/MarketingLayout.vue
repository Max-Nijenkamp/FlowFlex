<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import Logo from '@/Components/UI/Logo.vue'

const page = usePage<{ auth: { user: { name: string } | null } }>()
const user = computed(() => page.props.auth?.user ?? null)
const menuOpen = ref(false)

const nav = [
    { href: '/features', label: 'Product' },
    { href: '/pricing', label: 'Pricing' },
    { href: '/about', label: 'About' },
    { href: '/contact', label: 'Contact' },
]
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

                <nav class="hidden md:flex items-center gap-9 text-[15px] font-medium text-ink-soft">
                    <Link v-for="item in nav" :key="item.href" :href="item.href"
                        class="hover:text-ink transition ease-out duration-150">{{ item.label }}</Link>
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
                <Link v-for="item in nav" :key="item.href" :href="item.href" class="block py-1.5 font-medium text-ink-soft"
                    @click="menuOpen = false">{{ item.label }}</Link>
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
