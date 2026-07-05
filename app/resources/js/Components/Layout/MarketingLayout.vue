<script setup lang="ts">
import { ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import Logo from '../UI/Logo.vue'

const page = usePage()
const menuOpen = ref(false)

const links = [
    { label: 'Product', href: '/product' },
    { label: 'Pricing', href: '/pricing' },
    { label: 'About', href: '/about' },
    { label: 'Contact', href: '/contact' },
]

const isActive = (href: string): boolean => page.url === href || page.url.startsWith(href + '/')
</script>

<template>
    <div class="ff min-h-screen flex flex-col">
        <a
            href="#main"
            class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:bg-white focus:px-4 focus:py-2 focus:rounded-lg focus:shadow"
        >Skip to content</a>

        <header class="ff-nav-outer">
            <div class="wrap">
                <nav class="ff-nav" aria-label="Main">
                    <Link href="/" aria-label="FlowFlex home"><Logo :size="25" /></Link>
                    <div class="ff-nav-links">
                        <Link v-for="l in links" :key="l.href" :href="l.href" :class="{ on: isActive(l.href) }">{{ l.label }}</Link>
                    </div>
                    <div class="flex items-center" style="gap: 18px">
                        <a href="/app/login" class="hidden md:inline text-[14.5px] font-medium text-(--ink-soft) hover:text-(--ink) transition-colors">Sign in</a>
                        <Link href="/contact" class="ff-btn sm hidden md:inline-flex">Talk to us</Link>
                        <button
                            type="button"
                            class="md:hidden p-1"
                            :aria-expanded="menuOpen"
                            aria-label="Menu"
                            @click="menuOpen = !menuOpen"
                        >
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#111827" stroke-width="1.8" stroke-linecap="round">
                                <path v-if="!menuOpen" d="M4 7h16M4 12h16M4 17h16" />
                                <path v-else d="M6 6l12 12M18 6L6 18" />
                            </svg>
                        </button>
                    </div>
                </nav>
            </div>
            <div v-if="menuOpen" class="ff-nav-mobile md:hidden">
                <Link v-for="l in links" :key="l.href" :href="l.href" @click="menuOpen = false">{{ l.label }}</Link>
                <a href="/app/login" @click="menuOpen = false">Sign in</a>
            </div>
        </header>

        <main id="main" class="flex-1">
            <slot />
        </main>

        <footer class="ff-footer">
            <div class="wrap">
                <div class="ff-footer-grid">
                    <div>
                        <Link href="/" aria-label="FlowFlex home"><Logo light :size="22" /></Link>
                        <p class="mt-4 text-[13.5px] leading-relaxed text-white/50 max-w-[240px]">
                            One platform. Every tool. Always flexible. Built for teams of 50 to 500.
                        </p>
                    </div>
                    <div>
                        <h4>Product</h4>
                        <Link class="lnk" href="/product">All modules</Link>
                        <Link class="lnk" href="/pricing">Pricing</Link>
                        <a class="lnk" href="/app/login">Sign in</a>
                    </div>
                    <div>
                        <h4>Company</h4>
                        <Link class="lnk" href="/about">About</Link>
                        <Link class="lnk" href="/contact">Contact</Link>
                    </div>
                    <div>
                        <h4>Legal</h4>
                        <Link class="lnk" href="/terms">Terms</Link>
                        <Link class="lnk" href="/privacy">Privacy</Link>
                    </div>
                </div>
                <div class="ff-footer-base">
                    <span>© 2026 FlowFlex — everything flows</span>
                    <span>EU-hosted · GDPR-first · data portable</span>
                </div>
            </div>
        </footer>
    </div>
</template>
