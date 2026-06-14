<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import CtaBand from '@/Components/Marketing/CtaBand.vue'
import Kicker from '@/Components/Marketing/Kicker.vue'
import Reveal from '@/Components/UI/Reveal.vue'
import { domainColors } from '@/data/marketing'
import { Head } from '@inertiajs/vue3'

defineOptions({ layout: MarketingLayout })

defineProps<{
    entries: { date: string; domain: string; title: string; body: string }[]
}>()

const chipColor = (domain: string) => domainColors[domain] ?? '#4F46E5'
</script>

<template>
    <Head title="Changelog">
        <meta name="description" content="What shipped on FlowFlex, in order. New modules, new flows, and the small things that make the days smoother." />
    </Head>

    <!-- Hero -->
    <section class="bg-bloom border-b border-line">
        <div class="mx-auto max-w-6xl px-6 pt-14 pb-12 md:pt-[84px] md:pb-[64px]">
            <Kicker>Changelog</Kicker>
            <h1 class="mt-[26px] max-w-[720px] font-display text-[40px] font-bold leading-[1.02] tracking-[-0.03em] md:text-[56px]">
                What shipped, in order.
            </h1>
            <p class="mt-[22px] max-w-[500px] text-base leading-[1.65] text-ink-soft md:text-lg">
                New modules, new flows, and the small things that make the days smoother. No version numbers,
                no marketing — just what changed.
            </p>
        </div>
    </section>

    <!-- Entries -->
    <section class="border-b border-line bg-card">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[88px]">
            <div class="border-t border-line">
                <Reveal v-for="(e, i) in entries" :key="e.date + e.title" :delay="Math.min(i * 60, 300)">
                    <article class="grid gap-2 border-b border-line py-7 md:grid-cols-[140px_1fr] md:gap-10">
                        <p class="font-mono text-xs text-ink-faint">{{ e.date }}</p>
                        <div>
                            <h2 class="flex items-center gap-2.5 font-display text-[19px] font-bold tracking-display">
                                <span class="h-2.5 w-2.5 shrink-0 rounded-[3px]" :style="{ background: chipColor(e.domain) }" />
                                {{ e.title }}
                            </h2>
                            <p class="mt-2 max-w-[640px] text-[15px] leading-[1.65] text-ink-soft">{{ e.body }}</p>
                        </div>
                    </article>
                </Reveal>
            </div>
            <p class="mt-6 font-mono text-xs text-ink-faint">older entries archive as domains ship · everything above is live for every workspace</p>
        </div>
    </section>

    <CtaBand title="The next entry could be your request."
        sub="Customers steer the roadmap more than they expect — tell us what's missing." cta="Contact us" href="/contact" />
</template>
