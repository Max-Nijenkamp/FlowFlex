<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Linkedin, Twitter, Github } from 'lucide-vue-next'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const productLinks = [
    { key: 'footer.links.features', href: '/features' },
    { key: 'footer.links.pricing', href: '/pricing' },
    { key: 'footer.links.modules', href: '/features' },
    { key: 'footer.links.changelog', href: '/changelog' },
    { key: 'footer.links.status', href: 'https://status.flowflex.com', external: true },
    { key: 'footer.links.requestDemo', href: '/demo' },
]

const companyLinks = [
    { key: 'footer.links.about', href: '/about' },
    { key: 'footer.links.blog', href: '/blog' },
    { key: 'footer.links.careers', href: '/careers' },
    { key: 'footer.links.contact', href: '/contact' },
]

const legalLinks = [
    { key: 'footer.links.privacy', href: '/legal/privacy' },
    { key: 'footer.links.terms', href: '/legal/terms' },
    { key: 'footer.links.cookies', href: '/legal/cookies' },
    { key: 'footer.links.dpa', href: '/legal/dpa' },
    { key: 'footer.links.aup', href: '/legal/aup' },
    { key: 'footer.links.security', href: '/security' },
]
</script>

<template>
    <footer class="bg-ocean-950 text-slate-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <!-- Column 1: Brand -->
                <div class="space-y-4">
                    <div class="text-xl font-bold text-white tracking-tight">
                        Flow<span class="text-ocean-400">Flex</span>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        {{ t('footer.tagline') }}
                    </p>
                    <div class="flex items-center gap-4">
                        <a
                            href="https://linkedin.com/company/flowflex"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-slate-400 hover:text-white transition-colors"
                            aria-label="FlowFlex on LinkedIn"
                        >
                            <Linkedin class="w-5 h-5" />
                        </a>
                        <a
                            href="https://twitter.com/flowflex"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-slate-400 hover:text-white transition-colors"
                            aria-label="FlowFlex on Twitter/X"
                        >
                            <Twitter class="w-5 h-5" />
                        </a>
                        <a
                            href="https://github.com/flowflex"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-slate-400 hover:text-white transition-colors"
                            aria-label="FlowFlex on GitHub"
                        >
                            <Github class="w-5 h-5" />
                        </a>
                    </div>
                    <p class="text-xs text-slate-500 pt-2">
                        {{ t('footer.copyright') }}
                    </p>
                </div>

                <!-- Column 2: Product -->
                <div>
                    <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">{{ t('footer.product') }}</h3>
                    <ul class="space-y-3">
                        <li v-for="link in productLinks" :key="link.href">
                            <template v-if="link.external">
                                <a
                                    :href="link.href"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-sm text-slate-400 hover:text-white transition-colors"
                                >
                                    {{ t(link.key) }}
                                </a>
                            </template>
                            <template v-else>
                                <Link
                                    :href="link.href"
                                    class="text-sm text-slate-400 hover:text-white transition-colors"
                                >
                                    {{ t(link.key) }}
                                </Link>
                            </template>
                        </li>
                    </ul>
                </div>

                <!-- Column 3: Company -->
                <div>
                    <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">{{ t('footer.company') }}</h3>
                    <ul class="space-y-3">
                        <li v-for="link in companyLinks" :key="link.href">
                            <Link
                                :href="link.href"
                                class="text-sm text-slate-400 hover:text-white transition-colors"
                            >
                                {{ t(link.key) }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Column 4: Legal -->
                <div>
                    <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">{{ t('footer.legal') }}</h3>
                    <ul class="space-y-3">
                        <li v-for="link in legalLinks" :key="link.href">
                            <Link
                                :href="link.href"
                                class="text-sm text-slate-400 hover:text-white transition-colors"
                            >
                                {{ t(link.key) }}
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bottom bar -->
        <div class="border-t border-ocean-900/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-2">
                <p class="text-xs text-slate-500">
                    {{ t('footer.registration') }}
                </p>
                <button class="text-xs text-slate-500 hover:text-slate-300 transition-colors underline underline-offset-2">
                    {{ t('footer.cookiePreferences') }}
                </button>
            </div>
        </div>
    </footer>
</template>
