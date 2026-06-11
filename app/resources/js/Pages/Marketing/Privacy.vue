<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'

defineOptions({ layout: MarketingLayout })

defineProps<{ content: string }>()
</script>

<template>
    <Head title="Privacy policy">
        <meta name="description" content="How FlowFlex handles your data — EU-hosted and GDPR-first." />
    </Head>
    <section class="mx-auto max-w-3xl px-6 py-20">
        <p class="section-index">LEGAL</p>
        <h1 class="mt-4 text-4xl font-bold tracking-display">Privacy policy</h1>
        <p class="mt-2 text-sm text-ink-faint">Last updated 11 June 2026</p>
        <div class="mt-10 space-y-5 text-ink-soft leading-relaxed border-t border-line pt-8">
            <p>{{ content }}</p>
        </div>
    </section>
</template>
