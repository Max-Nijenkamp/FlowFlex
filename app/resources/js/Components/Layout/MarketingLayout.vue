<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage<{ auth: { user: { name: string } | null } }>()
const user = computed(() => page.props.auth?.user ?? null)

const nav = [
    { href: '/features', label: 'Features' },
    { href: '/pricing', label: 'Pricing' },
    { href: '/about', label: 'About' },
    { href: '/contact', label: 'Contact' },
]
</script>

<template>
    <div class="min-h-screen flex flex-col">
        <header class="border-b border-slate-100 sticky top-0 bg-white/90 backdrop-blur z-40">
            <div class="mx-auto max-w-6xl px-6 h-16 flex items-center justify-between">
                <Link href="/" class="text-xl font-bold tracking-tight text-sky-500">FlowFlex</Link>
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                    <Link v-for="item in nav" :key="item.href" :href="item.href"
                        class="hover:text-slate-900 transition ease-out duration-150">{{ item.label }}</Link>
                </nav>
                <div class="flex items-center gap-3">
                    <a v-if="user" href="/app"
                        class="rounded-lg bg-sky-500 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-600 transition ease-out duration-150">
                        Open workspace
                    </a>
                    <Link v-else href="/login"
                        class="rounded-lg bg-sky-500 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-600 transition ease-out duration-150">
                        Sign in
                    </Link>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="border-t border-slate-100 py-12 text-sm text-slate-500">
            <div class="mx-auto max-w-6xl px-6 flex flex-col md:flex-row justify-between gap-6">
                <div>
                    <div class="text-lg font-bold text-sky-500">FlowFlex</div>
                    <p class="mt-1">The all-in-one workspace for growing teams.</p>
                </div>
                <div class="flex gap-8">
                    <Link href="/terms" class="hover:text-slate-900">Terms</Link>
                    <Link href="/privacy" class="hover:text-slate-900">Privacy</Link>
                    <Link href="/contact" class="hover:text-slate-900">Contact</Link>
                </div>
            </div>
        </footer>
    </div>
</template>
