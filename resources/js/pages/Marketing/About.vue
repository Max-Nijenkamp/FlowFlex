<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { ArrowRight, Cpu, Heart, DollarSign, Shield, MessageCircle, Users } from 'lucide-vue-next'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const values = [
    { icon: Cpu, titleKey: 'about.values.simplicity.title', descKey: 'about.values.simplicity.description', color: '#2199C8' },
    { icon: Heart, titleKey: 'about.values.builtForHumans.title', descKey: 'about.values.builtForHumans.description', color: '#DB2777' },
    { icon: DollarSign, titleKey: 'about.values.honestPricing.title', descKey: 'about.values.honestPricing.description', color: '#059669' },
    { icon: Shield, titleKey: 'about.values.dataSovereignty.title', descKey: 'about.values.dataSovereignty.description', color: '#7C3AED' },
    { icon: MessageCircle, titleKey: 'about.values.customerObsession.title', descKey: 'about.values.customerObsession.description', color: '#D97706' },
]
</script>

<template>
    <Head title="About — FlowFlex">
        <meta name="description" content="FlowFlex was built by people who got tired of managing 8 different tools. Learn about our mission, values, and team." />
    </Head>

    <!-- Hero -->
    <section class="relative bg-[#050E1A] py-32 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-ocean-950 via-[#071828] to-slate-950 pointer-events-none" />
        <div class="absolute -top-20 -right-20 w-[500px] h-[500px] rounded-full bg-ocean-700/15 blur-[100px] pointer-events-none" />
        <div class="absolute bottom-0 left-0 w-[300px] h-[300px] rounded-full bg-violet-800/10 blur-[80px] pointer-events-none" />
        <div class="absolute inset-0 bg-[linear-gradient(rgba(33,153,200,0.04)_1px,transparent_1px),linear-gradient(90deg,rgba(33,153,200,0.04)_1px,transparent_1px)] bg-[size:72px_72px] pointer-events-none" />
        <div class="relative max-w-4xl mx-auto text-center space-y-6">            <h1 class="text-6xl sm:text-7xl font-black text-white leading-tight tracking-tighter">
                {{ t('about.heading') }}
            </h1>
            <p class="text-xl text-slate-400 max-w-2xl mx-auto leading-relaxed">
                {{ t('about.subheading') }}
            </p>
        </div>
    </section>

    <!-- Origin story -->
    <section class="bg-white dark:bg-slate-900 py-24 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto space-y-6">
            <h2 class="text-3xl font-bold text-slate-900 dark:text-white">{{ t('about.whyBuilt') }}</h2>
            <div class="space-y-4 text-slate-600 dark:text-slate-400 text-lg leading-relaxed">
                <p>{{ t('about.story1') }}</p>
                <p>{{ t('about.story2') }}</p>
                <p>{{ t('about.story3') }}</p>
                <p>{{ t('about.story4') }}</p>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="bg-ocean-50 dark:bg-slate-800/50 py-24 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <h2 class="text-3xl font-bold text-slate-900 dark:text-white text-center mb-12">{{ t('about.missionHeading') }}</h2>
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-8 space-y-4">
                    <div class="text-ocean-500 font-bold uppercase tracking-widest text-xs">{{ t('about.missionLabel') }}</div>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ t('about.missionTitle') }}</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                        {{ t('about.missionDesc') }}
                    </p>
                </div>
                <div class="bg-slate-900 dark:bg-slate-950 rounded-2xl p-8 space-y-4">
                    <div class="text-ocean-400 font-bold uppercase tracking-widest text-xs">{{ t('about.visionLabel') }}</div>
                    <h3 class="text-2xl font-bold text-white">{{ t('about.visionTitle') }}</h3>
                    <p class="text-slate-400 leading-relaxed">
                        {{ t('about.visionDesc') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Values -->
    <section class="bg-white dark:bg-slate-900 py-24 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <h2 class="text-3xl font-bold text-slate-900 dark:text-white text-center mb-12">{{ t('about.valuesHeading') }}</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="value in values"
                    :key="value.titleKey"
                    class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 hover:shadow-md dark:hover:shadow-slate-900/50 hover:-translate-y-0.5 transition-all"
                >
                    <div
                        class="w-10 h-10 rounded-xl flex items-center justify-center mb-4"
                        :style="`background: ${value.color}18`"
                    >
                        <component :is="value.icon" class="w-5 h-5" :style="`color: ${value.color}`" />
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white mb-2">{{ t(value.titleKey) }}</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">{{ t(value.descKey) }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team -->
    <section class="bg-slate-50 dark:bg-slate-800/50 py-24 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-4">{{ t('about.teamHeading') }}</h2>
            <p class="text-slate-600 dark:text-slate-400 mb-12">{{ t('about.teamSubheading') }}</p>
            <div class="border border-slate-200 dark:border-slate-700 rounded-2xl p-16 bg-white dark:bg-slate-800 flex flex-col items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                    <Users class="w-8 h-8 text-slate-400" />
                </div>
                <p class="text-slate-500 dark:text-slate-400 text-lg">{{ t('about.teamComingSoon') }}</p>
                <p class="text-slate-400 dark:text-slate-500 text-sm">{{ t('about.teamComingSoonDesc') }}</p>
            </div>
        </div>
    </section>

    <!-- Numbers -->
    <section class="bg-slate-900 dark:bg-slate-950 py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-5xl font-bold text-ocean-400 mb-2">99+</div>
                    <div class="text-slate-400 text-sm">{{ t('stats.modules') }}</div>
                </div>
                <div>
                    <div class="text-5xl font-bold text-ocean-400 mb-2">13</div>
                    <div class="text-slate-400 text-sm">{{ t('stats.domains') }}</div>
                </div>
                <div>
                    <div class="text-5xl font-bold text-ocean-400 mb-2">300+</div>
                    <div class="text-slate-400 text-sm">{{ t('stats.features') }}</div>
                </div>
                <div>
                    <div class="text-5xl font-bold text-ocean-400 mb-2">1</div>
                    <div class="text-slate-400 text-sm">{{ t('stats.dataLayer') }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="bg-gradient-to-br from-ocean-600 via-ocean-500 to-ocean-700 py-24 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center space-y-6">
            <h2 class="text-4xl font-bold text-white">{{ t('about.ctaHeading') }}</h2>
            <p class="text-xl text-white/80">{{ t('about.ctaSubheading') }}</p>
            <Link
                href="/demo"
                class="inline-flex items-center gap-2 bg-white text-ocean-900 px-8 py-4 rounded-xl font-semibold hover:bg-ocean-50 transition-all shadow-xl hover:-translate-y-0.5"
            >
                {{ t('about.ctaButton') }} <ArrowRight class="w-5 h-5" />
            </Link>
        </div>
    </section>
</template>

