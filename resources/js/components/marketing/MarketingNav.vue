<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Menu, X, Sun, Moon } from 'lucide-vue-next'
import { useAppearance } from '@/composables/useAppearance'

const { appearance, updateAppearance } = useAppearance()

const scrolled = ref(false)
const mobileOpen = ref(false)

function onScroll() {
    scrolled.value = window.scrollY > 60
}

onMounted(() => window.addEventListener('scroll', onScroll))
onUnmounted(() => window.removeEventListener('scroll', onScroll))

function toggleDarkMode() {
    updateAppearance(appearance.value === 'dark' ? 'light' : 'dark')
}

const isDark = computed(() => appearance.value === 'dark')

const page = usePage()
const currentPath = computed(() => page.url)

const navLinks = [
    { label: 'Features', href: '/features' },
    { label: 'Pricing', href: '/pricing' },
    { label: 'Blog', href: '/blog' },
]

function isActive(href: string) {
    return currentPath.value === href || currentPath.value.startsWith(href + '/')
}

function closeMobile() {
    mobileOpen.value = false
}
</script>

<template>
    <nav
        :class="[
            'sticky top-0 z-50 w-full transition-all duration-300',
            scrolled
                ? 'backdrop-blur-md bg-slate-900/90 shadow-lg border-b border-white/5'
                : 'bg-slate-900',
        ]"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            <!-- Logo -->
            <Link href="/" class="flex items-center gap-2.5 select-none shrink-0 group">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-ocean-500 to-ocean-600 flex items-center justify-center shadow-sm shadow-ocean-500/30 group-hover:shadow-ocean-400/40 transition-shadow">
                    <svg viewBox="0 0 20 20" fill="none" class="w-4.5 h-4.5">
                        <path d="M3 5h8a4 4 0 0 1 0 8H3" stroke="white" stroke-width="2" stroke-linecap="round"/>
                        <path d="M3 10h5" stroke="white" stroke-width="2" stroke-linecap="round" opacity="0.6"/>
                    </svg>
                </div>
                <span class="text-lg font-bold text-white tracking-tight">Flow<span class="text-ocean-400">Flex</span></span>
            </Link>

            <!-- Center nav links (desktop) -->
            <div class="hidden md:flex items-center gap-6">
                <Link
                    v-for="link in navLinks"
                    :key="link.href"
                    :href="link.href"
                    :class="[
                        'text-sm font-medium transition-colors relative pb-0.5',
                        isActive(link.href)
                            ? 'text-white after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-ocean-400 after:rounded-full'
                            : 'text-white/70 hover:text-white',
                    ]"
                >
                    {{ link.label }}
                </Link>
            </div>

            <!-- Right actions (desktop) -->
            <div class="hidden md:flex items-center gap-2">
                <button
                    type="button"
                    class="p-2 rounded-lg text-white/60 hover:text-white hover:bg-white/10 transition-colors"
                    :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                    @click="toggleDarkMode"
                >
                    <Sun v-if="isDark" class="w-4 h-4" />
                    <Moon v-else class="w-4 h-4" />
                </button>

                <a
                    href="/workspace/login"
                    class="px-4 py-2 rounded-lg border border-white/20 text-white/80 text-sm font-medium hover:bg-white/10 hover:text-white transition-colors"
                >
                    Log in
                </a>
                <Link
                    href="/demo"
                    class="px-4 py-2 rounded-lg bg-ocean-500 hover:bg-ocean-400 text-white text-sm font-semibold transition-colors shadow-sm shadow-ocean-900/30"
                >
                    Request Demo
                </Link>
            </div>

            <!-- Mobile actions -->
            <div class="md:hidden flex items-center gap-2">
                <button
                    type="button"
                    class="p-2 rounded-lg text-white/60 hover:text-white hover:bg-white/10 transition-colors"
                    :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                    @click="toggleDarkMode"
                >
                    <Sun v-if="isDark" class="w-4 h-4" />
                    <Moon v-else class="w-4 h-4" />
                </button>
                <button
                    type="button"
                    class="p-2 rounded-lg text-white/60 hover:text-white hover:bg-white/10 transition-colors"
                    @click="mobileOpen = !mobileOpen"
                    aria-label="Toggle menu"
                >
                    <X v-if="mobileOpen" class="w-5 h-5" />
                    <Menu v-else class="w-5 h-5" />
                </button>
            </div>
        </div>

        <!-- Mobile menu overlay -->
        <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-2"
        >
            <div
                v-if="mobileOpen"
                class="md:hidden fixed inset-0 top-[57px] bg-slate-900 z-40 flex flex-col px-6 py-8 gap-4"
            >
                <Link
                    v-for="(link, i) in navLinks"
                    :key="link.href + '-mobile'"
                    :href="link.href"
                    :class="[
                        'text-lg font-medium transition-colors py-2 border-b border-white/5',
                        isActive(link.href) ? 'text-ocean-400' : 'text-white/80 hover:text-white',
                    ]"
                    :style="{ transitionDelay: `${i * 50}ms` }"
                    @click="closeMobile"
                >
                    {{ link.label }}
                </Link>

                <div class="flex flex-col gap-3 pt-2">
                    <a
                        href="/workspace/login"
                        class="px-4 py-3 rounded-xl border border-white/20 text-white text-sm font-medium hover:bg-white/10 transition-colors text-center"
                        @click="closeMobile"
                    >
                        Log in
                    </a>
                    <Link
                        href="/demo"
                        class="px-4 py-3 rounded-xl bg-ocean-500 hover:bg-ocean-400 text-white text-sm font-semibold transition-colors text-center shadow-sm"
                        @click="closeMobile"
                    >
                        Request Demo
                    </Link>
                </div>
            </div>
        </Transition>
    </nav>
</template>
