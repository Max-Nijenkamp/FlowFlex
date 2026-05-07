<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { Construction } from 'lucide-vue-next'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps<{
    entries?: Array<{
        id: string
        title: string
        type: string
        body: string
        published_at: string
    }>
}>()

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    })
}

const typeConfig: Record<string, { label: string; bg: string; text: string }> = {
    feature: { label: t('changelog.types.feature'), bg: 'bg-success-500/10', text: 'text-success-500' },
    improvement: { label: t('changelog.types.improvement'), bg: 'bg-ocean-50 dark:bg-ocean-900/30', text: 'text-ocean-600 dark:text-ocean-400' },
    fix: { label: t('changelog.types.fix'), bg: 'bg-tide-500/10', text: 'text-tide-500' },
    infrastructure: { label: t('changelog.types.infrastructure'), bg: 'bg-slate-100 dark:bg-slate-800', text: 'text-slate-500 dark:text-slate-400' },
}

function typeStyle(type: string) {
    return typeConfig[type] ?? typeConfig.infrastructure
}
</script>

<template>
    <Head title="Changelog — FlowFlex">
        <meta name="description" content="What's new in FlowFlex. Track every feature release, improvement, and bug fix." />
    </Head>

    <!-- Hero -->
    <section class="relative bg-[#050E1A] py-24 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-ocean-950 via-[#071828] to-slate-950 pointer-events-none" />
        <div class="absolute top-0 right-1/4 w-[400px] h-[200px] bg-ocean-600/8 blur-[80px] rounded-full pointer-events-none" />
        <div class="relative max-w-3xl mx-auto text-center space-y-4">            <h1 class="text-4xl sm:text-6xl font-black text-white tracking-tighter">{{ t('changelog.heading') }}</h1>
            <p class="text-xl text-slate-400">{{ t('changelog.subheading') }}</p>
        </div>
    </section>

    <!-- Entries -->
    <section class="bg-white dark:bg-slate-900 py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">

            <!-- Empty state -->
            <div v-if="!entries || entries.length === 0" class="text-center py-24">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4">
                    <Construction class="w-8 h-8 text-slate-400" />
                </div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">{{ t('changelog.emptyTitle') }}</h2>
                <p class="text-slate-500 dark:text-slate-400">{{ t('changelog.emptyDesc') }}</p>
            </div>

            <!-- Timeline -->
            <div v-else class="relative">
                <!-- Timeline line -->
                <div class="absolute left-4 top-0 bottom-0 w-px bg-slate-100 dark:bg-slate-800" aria-hidden="true" />

                <div class="space-y-12">
                    <article
                        v-for="entry in entries"
                        :key="entry.id"
                        class="relative pl-14"
                    >
                        <!-- Dot -->
                        <div class="absolute left-2 top-1.5 w-4 h-4 rounded-full bg-ocean-500 border-2 border-white dark:border-slate-900 shadow-sm" aria-hidden="true" />

                        <!-- Date -->
                        <div class="flex items-center gap-3 mb-3">
                            <time class="text-sm text-slate-500 dark:text-slate-400 font-medium">
                                {{ formatDate(entry.published_at) }}
                            </time>
                            <span
                                class="text-xs font-semibold px-2.5 py-0.5 rounded-full"
                                :class="[typeStyle(entry.type).bg, typeStyle(entry.type).text]"
                            >
                                {{ typeStyle(entry.type).label }}
                            </span>
                        </div>

                        <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-3">{{ entry.title }}</h2>
                        <div
                            class="text-slate-600 dark:text-slate-400 leading-relaxed text-sm
                                   [&_p]:mb-3 [&_ul]:list-disc [&_ul]:pl-4 [&_ul]:space-y-1
                                   [&_a]:text-ocean-600 dark:[&_a]:text-ocean-400 [&_a]:underline"
                            v-html="entry.body"
                        />
                    </article>
                </div>
            </div>
        </div>
    </section>
</template>
