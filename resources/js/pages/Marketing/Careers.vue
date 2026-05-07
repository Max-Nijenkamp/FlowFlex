<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { ArrowRight, Mail, Rocket, Globe, Lightbulb, Search } from 'lucide-vue-next'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const perks = [
    { icon: Rocket, titleKey: 'careers.perks.groundUp.title', descKey: 'careers.perks.groundUp.description', color: '#2199C8' },
    { icon: Globe, titleKey: 'careers.perks.remote.title', descKey: 'careers.perks.remote.description', color: '#059669' },
    { icon: Lightbulb, titleKey: 'careers.perks.realProblems.title', descKey: 'careers.perks.realProblems.description', color: '#D97706' },
]
</script>

<template>
    <Head title="Careers — FlowFlex">
        <meta name="description" content="Join the FlowFlex team. We're building the future of business operations software. See our open roles." />
    </Head>

    <!-- Hero -->
    <section class="relative bg-[#050E1A] py-28 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-[#0a1020] to-ocean-950 pointer-events-none" />
        <div class="absolute bottom-0 left-1/3 w-[500px] h-[300px] bg-ocean-700/10 blur-[100px] rounded-full pointer-events-none" />
        <div class="relative max-w-4xl mx-auto text-center space-y-6">            <h1 class="text-4xl sm:text-6xl font-black text-white tracking-tighter">{{ t('careers.heading') }}</h1>
            <p class="text-xl text-slate-400 max-w-2xl mx-auto leading-relaxed">
                {{ t('careers.subheading') }}
            </p>
        </div>
    </section>

    <!-- Values as employer -->
    <section class="bg-white dark:bg-slate-900 py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white text-center mb-10">{{ t('careers.whyUs') }}</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <div
                    v-for="perk in perks"
                    :key="perk.titleKey"
                    class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 space-y-3 hover:shadow-md dark:hover:shadow-slate-900/50 hover:-translate-y-0.5 transition-all"
                >
                    <div
                        class="w-10 h-10 rounded-xl flex items-center justify-center"
                        :style="`background: ${perk.color}18`"
                    >
                        <component :is="perk.icon" class="w-5 h-5" :style="`color: ${perk.color}`" />
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white">{{ t(perk.titleKey) }}</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ t(perk.descKey) }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Open roles (empty state) -->
    <section class="bg-slate-50 dark:bg-slate-800/50 py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white text-center mb-4">{{ t('careers.openRoles') }}</h2>
            <p class="text-slate-500 dark:text-slate-400 text-center mb-10">{{ t('careers.openRolesSubheading') }}</p>

            <div class="border border-slate-200 dark:border-slate-700 rounded-2xl p-12 bg-white dark:bg-slate-800 text-center space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mx-auto">
                    <Search class="w-7 h-7 text-slate-400" />
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ t('careers.noRolesTitle') }}</h3>
                <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto leading-relaxed">
                    {{ t('careers.noRolesDesc') }}
                </p>
                <a
                    href="mailto:jobs@flowflex.com"
                    class="inline-flex items-center gap-2 mt-4 bg-ocean-500 hover:bg-ocean-400 text-white px-6 py-3 rounded-xl font-semibold transition-all shadow-sm hover:-translate-y-0.5"
                >
                    <Mail class="w-4 h-4" />
                    jobs@flowflex.com
                </a>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="bg-gradient-to-br from-ocean-600 via-ocean-500 to-ocean-700 py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center space-y-4">
            <h2 class="text-3xl font-bold text-white">{{ t('careers.ctaTitle') }}</h2>
            <p class="text-white/80">
                {{ t('careers.ctaDesc') }}
            </p>
        </div>
    </section>
</template>
