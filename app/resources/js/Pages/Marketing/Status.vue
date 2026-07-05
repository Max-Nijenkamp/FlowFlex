<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import MarketingLayout from '../../Components/Layout/MarketingLayout.vue'

defineOptions({ layout: MarketingLayout })

const props = defineProps<{ checkedAt: string }>()

// Static uptime mock until spatie/laravel-health results are wired in
// (frontend/_index.md §23 — 60s-cached health endpoint).
const rows: Array<[string, number | null, string]> = [
    ['Core platform — auth, files, notifications', null, '100%'],
    ['HR & people', null, '100%'],
    ['Finance & accounting', 41, '99.98%'],
    ['CRM & sales', null, '100%'],
    ['Projects & work', null, '100%'],
    ['Support & help desk', null, '100%'],
]

const bars = (bad: number | null): boolean[] => Array.from({ length: 60 }, (_, i) => bad !== null && i === bad)
</script>

<template>
    <Head>
        <title>Status</title>
        <meta name="description" content="FlowFlex system status — uptime by domain, past incidents, post-mortems." />
    </Head>

    <section class="ff-section ff-grid-bg" style="padding: 64px 0">
        <div class="wrap" style="max-width: 980px">
            <div
                class="flex items-center gap-4 rounded-2xl border bg-(--card) px-7 py-5.5"
                style="border-color: rgba(16,185,129,0.4); box-shadow: 0 1px 2px rgba(17,24,39,0.04)"
            >
                <span class="h-3.5 w-3.5 flex-none rounded-full" style="background: #10B981; box-shadow: 0 0 0 5px rgba(16,185,129,0.15)"></span>
                <div>
                    <h1 class="m-0" style="font-size: 24px; letter-spacing: -0.02em">All systems flowing</h1>
                    <p class="mono mt-1 text-[11.5px]" style="color: var(--ink-faint)">last checked {{ checkedAt }} · EU-west</p>
                </div>
            </div>

            <div class="mt-7 overflow-hidden rounded-2xl border bg-(--card)" style="border-color: var(--line-strong)">
                <div class="flex justify-between px-6 py-3.5" style="border-bottom: 1px solid var(--line)">
                    <span class="whitespace-nowrap text-[14px] font-bold" style="font-family: var(--font-display)">Uptime by domain</span>
                    <span class="mono whitespace-nowrap text-[11px]" style="color: var(--ink-faint)">last 60 days</span>
                </div>
                <div
                    v-for="[name, bad, pct] in rows"
                    :key="name"
                    class="grid items-center gap-5 px-6 py-3 [grid-template-columns:1fr_auto_70px] max-md:[grid-template-columns:1fr_70px]"
                    style="border-bottom: 1px solid var(--line)"
                >
                    <span class="truncate text-[14px] font-semibold">{{ name }}</span>
                    <span class="flex items-center gap-0.5 max-md:hidden" aria-hidden="true">
                        <span
                            v-for="(isBad, i) in bars(bad)"
                            :key="i"
                            class="h-4.5 w-1 rounded-[1.5px]"
                            :style="{ background: isBad ? '#F59E0B' : '#10B981', opacity: isBad ? 1 : 0.8 }"
                        ></span>
                    </span>
                    <span class="mono text-right text-[12px]" :style="{ color: bad !== null ? '#B45309' : '#0E8C61' }">{{ pct }}</span>
                </div>
            </div>

            <div class="mt-7">
                <p class="ff-tag" style="letter-spacing: 0.18em">PAST INCIDENTS</p>
                <div class="mt-3.5 rounded-[14px] border bg-(--card) px-6 py-4.5" style="border-color: var(--line-strong)">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="mono text-[11.5px]" style="color: var(--ink-faint)">02 MAY 2026 · 14:10–14:52</span>
                        <span class="rounded-full px-2.5 py-0.5 text-[11.5px] font-bold" style="background: #FDF1DC; color: #B45309">DEGRADED · RESOLVED</span>
                    </div>
                    <h3 class="mt-2.5" style="font-size: 16px">Slow invoice PDF generation in Finance</h3>
                    <p class="mt-1.5 text-[14px] leading-relaxed" style="color: var(--ink-soft)">
                        PDF rendering queued behind a long export job. No data was lost; all queued PDFs were delivered
                        by 15:05. Post-mortem and the queue isolation fix are linked below.
                    </p>
                    <span class="mt-2.5 inline-block text-[13px] font-semibold" style="color: var(--indigo)">Read the post-mortem →</span>
                </div>
                <p class="mono mt-4.5 text-center text-[12px]" style="color: var(--ink-faint)">that's the whole list for 2026</p>
            </div>
        </div>
    </section>
</template>
