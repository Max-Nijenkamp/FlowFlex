<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Menu, X, Sun, Moon, Globe, ChevronDown } from 'lucide-vue-next'
import { useI18n } from 'vue-i18n'
import { useAppearance } from '@/composables/useAppearance'

const { t, locale } = useI18n()
const { appearance, updateAppearance } = useAppearance()

const scrolled = ref(false)
const mobileOpen = ref(false)
const langOpen = ref(false)

function onScroll() {
    scrolled.value = window.scrollY > 60
}

onMounted(() => {
    window.addEventListener('scroll', onScroll)
    // Restore saved language preference
    const savedLang = localStorage.getItem('locale')
    if (savedLang === 'nl' || savedLang === 'en') {
        locale.value = savedLang
    }
})

onUnmounted(() => window.removeEventListener('scroll', onScroll))

function setLocale(lang: string) {
    locale.value = lang
    localStorage.setItem('locale', lang)
    langOpen.value = false
}

function toggleDarkMode() {
    if (appearance.value === 'dark') {
        updateAppearance('light')
    } else {
        updateAppearance('dark')
    }
}

const isDark = computed(() => appearance.value === 'dark')

const page = usePage()
const currentPath = computed(() => page.url)

const navLinks = computed(() => [
    { label: t('nav.features'), href: '/features' },
    { label: t('nav.pricing'), href: '/pricing' },
    { label: t('nav.blog'), href: '/blog' },
])

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
                <!-- Logo mark: stacked flow lines -->
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
                    :key="link.href + link.label"
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
                <!-- Dark mode toggle -->
                <button
                    type="button"
                    class="p-2 rounded-lg text-white/60 hover:text-white hover:bg-white/10 transition-colors"
                    :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                    @click="toggleDarkMode"
                >
                    <Sun v-if="isDark" class="w-4 h-4" />
                    <Moon v-else class="w-4 h-4" />
                </button>

                <!-- Language switcher -->
                <div class="relative">
                    <button
                        type="button"
                        class="flex items-center gap-1.5 p-2 rounded-lg text-white/60 hover:text-white hover:bg-white/10 transition-colors text-sm font-medium"
                        @click="langOpen = !langOpen"
                        aria-label="Switch language"
                    >
                        <Globe class="w-4 h-4" />
                        <span class="uppercase text-xs font-semibold">{{ locale }}</span>
                        <ChevronDown class="w-3 h-3 transition-transform" :class="langOpen ? 'rotate-180' : ''" />
                    </button>
                    <div
                        v-if="langOpen"
                        class="absolute right-0 top-full mt-1 bg-slate-800 border border-slate-700 rounded-xl shadow-xl py-1 min-w-[100px] z-50"
                    >
                        <button
                            type="button"
                            :class="['w-full text-left px-4 py-2 text-sm transition-colors', locale === 'en' ? 'text-ocean-400 font-semibold' : 'text-white/70 hover:text-white hover:bg-white/5']"
                            @click="setLocale('en')"
                        >
                            EN — English
                        </button>
                        <button
                            type="button"
                            :class="['w-full text-left px-4 py-2 text-sm transition-colors', locale === 'nl' ? 'text-ocean-400 font-semibold' : 'text-white/70 hover:text-white hover:bg-white/5']"
                            @click="setLocale('nl')"
                        >
                            NL — Nederlands
                        </button>
                    </div>
                </div>

                <a
                    href="/workspace/login"
                    class="px-4 py-2 rounded-lg border border-white/20 text-white/80 text-sm font-medium hover:bg-white/10 hover:text-white transition-colors"
                >
                    {{ t('nav.login') }}
                </a>
                <Link
                    href="/demo"
                    class="px-4 py-2 rounded-lg bg-ocean-500 hover:bg-ocean-400 text-white text-sm font-semibold transition-colors shadow-sm shadow-ocean-900/30"
                >
                    {{ t('nav.requestDemo') }}
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
                    :key="link.href + link.label + '-mobile'"
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

                <!-- Language switcher mobile -->
                <div class="flex items-center gap-3 py-2 border-b border-white/5">
                    <Globe class="w-4 h-4 text-white/40" />
                    <button
                        type="button"
                        :class="['text-sm font-medium px-2 py-1 rounded transition-colors', locale === 'en' ? 'text-ocean-400' : 'text-white/60 hover:text-white']"
                        @click="setLocale('en')"
                    >
                        EN
                    </button>
                    <span class="text-white/20">/</span>
                    <button
                        type="button"
                        :class="['text-sm font-medium px-2 py-1 rounded transition-colors', locale === 'nl' ? 'text-ocean-400' : 'text-white/60 hover:text-white']"
                        @click="setLocale('nl')"
                    >
                        NL
                    </button>
                </div>

                <div class="flex flex-col gap-3 pt-2">
                    <a
                        href="/workspace/login"
                        class="px-4 py-3 rounded-xl border border-white/20 text-white text-sm font-medium hover:bg-white/10 transition-colors text-center"
                        @click="closeMobile"
                    >
                        {{ t('nav.login') }}
                    </a>
                    <Link
                        href="/demo"
                        class="px-4 py-3 rounded-xl bg-ocean-500 hover:bg-ocean-400 text-white text-sm font-semibold transition-colors text-center shadow-sm"
                        @click="closeMobile"
                    >
                        {{ t('nav.requestDemo') }}
                    </Link>
                </div>
            </div>
        </Transition>
    </nav>

    <!-- Click outside to close lang dropdown -->
    <div
        v-if="langOpen"
        class="fixed inset-0 z-40"
        @click="langOpen = false"
    />
</template>
