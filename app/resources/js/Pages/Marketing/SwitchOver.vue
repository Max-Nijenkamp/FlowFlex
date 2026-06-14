<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import BlueprintCell from '@/Components/Marketing/BlueprintCell.vue'
import CtaBand from '@/Components/Marketing/CtaBand.vue'
import Kicker from '@/Components/Marketing/Kicker.vue'
import ReplacesStrip from '@/Components/Marketing/ReplacesStrip.vue'
import SectionTag from '@/Components/Marketing/SectionTag.vue'
import Reveal from '@/Components/UI/Reveal.vue'
import Switch from '@/Components/UI/Switch.vue'
import { domainColors } from '@/data/marketing'
import { Head, Link } from '@inertiajs/vue3'

defineOptions({ layout: MarketingLayout })

const mapping = [
    { tool: 'BambooHR / Personio', domain: 'hr', modules: 'Employee profiles + Leave & absence' },
    { tool: 'Xero / QuickBooks', domain: 'finance', modules: 'Invoicing + Expenses + Reporting' },
    { tool: 'HubSpot / Pipedrive', domain: 'crm', modules: 'Contacts + Pipeline + Deals & quotes' },
    { tool: 'Asana / Monday.com', domain: 'projects', modules: 'Projects & boards + Time tracking' },
    { tool: 'Freshdesk / Zendesk', domain: 'support', modules: 'Tickets' },
    { tool: 'Zapier glue between all of it', domain: 'ai', modules: 'Nothing — one database needs no glue' },
]
</script>

<template>
    <Head title="Switch over">
        <meta name="description"
            content="Leave the patchwork, keep the data. How companies move from 5–15 tools to one switchboard in about three weeks." />
    </Head>

    <!-- Hero -->
    <section class="bg-bloom border-b border-line">
        <div class="mx-auto max-w-6xl px-6 pt-14 pb-16 md:pt-[84px] md:pb-[76px]">
            <Kicker>Switch over</Kicker>
            <h1 class="mt-[26px] max-w-[760px] font-display text-[40px] font-bold leading-[1.02] tracking-[-0.03em] md:text-[62px]">
                Leave the patchwork.<br><span class="[box-shadow:inset_0_-0.16em_0_#C7D2FE]">Keep the data</span>.
            </h1>
            <p class="mt-[22px] max-w-[500px] text-base leading-[1.65] text-ink-soft md:text-lg">
                Switching tools is scary because of the data, not the software. So the switch-over starts and ends
                with your data — imported, checked, and portable forever after.
            </p>
            <div class="mt-[34px] flex flex-col items-stretch gap-3.5 sm:flex-row sm:items-center">
                <Link href="/contact"
                    class="inline-flex items-center justify-center rounded-xl bg-accent px-8 py-4 text-base font-semibold text-white shadow-[0_1px_2px_rgba(79,70,229,0.4),0_8px_20px_-10px_rgba(79,70,229,0.5)] transition ease-out duration-150 hover:bg-accent-deep active:scale-[0.98]">
                    Map my stack
                </Link>
                <Link href="/patchwork" class="group inline-flex items-center justify-center gap-2 text-[15px] font-semibold text-ink">
                    What does my patchwork cost?
                    <span class="text-accent transition-transform ease-out duration-150 group-hover:translate-x-1">→</span>
                </Link>
            </div>
            <p class="mt-5 font-mono text-xs text-ink-faint">most teams: fully switched in ~3 weeks · no parallel-running fees</p>
        </div>
    </section>

    <ReplacesStrip />

    <!-- 01 / The plan -->
    <section class="border-b border-line bg-card">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[104px]">
            <Reveal>
                <SectionTag num="01" label="THE PLAN" />
                <h2 class="mt-4 max-w-[640px] font-display text-3xl font-bold leading-[1.06] tracking-display md:text-[42px]">
                    Three weeks, three steps, no big bang.
                </h2>
            </Reveal>
            <Reveal :delay="100">
                <div class="mt-[52px] grid gap-px border border-line-strong bg-line-strong md:grid-cols-3">
                    <BlueprintCell big="01" title="Map your stack"
                        body="Tell us the tools you run. We map every one to its modules and give you the before/after invoice — usually the same day." />
                    <BlueprintCell big="02" title="Import and verify"
                        body="Employees first, then finance, then everything else. CSV and native importers; you check the numbers before anything goes live." />
                    <BlueprintCell big="03" title="Switch the old stack off"
                        body="Department by department, the moment that team is comfortable. The old tools become read-only archives — then cancelled subscriptions." />
                </div>
            </Reveal>
        </div>
    </section>

    <!-- 02 / The mapping -->
    <section class="border-b border-line">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[104px]">
            <Reveal>
                <SectionTag num="02" label="THE MAPPING" />
                <h2 class="mt-4 max-w-[640px] font-display text-3xl font-bold leading-[1.06] tracking-display md:text-[42px]">
                    Where each tool lands.
                </h2>
            </Reveal>
            <Reveal :delay="100">
                <div class="mt-[52px] overflow-hidden rounded-[14px] border border-line-strong bg-card shadow-[0_1px_2px_rgba(17,24,39,0.03)]">
                    <div v-for="(row, i) in mapping" :key="row.tool"
                        class="grid items-center gap-3 border-b border-line px-4 py-3.5 last:border-b-0 md:grid-cols-[1fr_24px_1.2fr] md:gap-6 md:px-[22px]"
                        :class="i % 2 === 0 ? 'bg-[#FAF9F5]' : ''">
                        <span class="text-sm font-medium text-ink-faint line-through decoration-accent/45">{{ row.tool }}</span>
                        <span class="hidden text-center font-mono text-xs text-accent md:block">→</span>
                        <span class="flex items-center gap-2.5 text-sm font-semibold">
                            <span class="h-[11px] w-[11px] shrink-0 rounded-[3px]" :style="{ background: domainColors[row.domain] }" />
                            {{ row.modules }}
                        </span>
                    </div>
                </div>
            </Reveal>
        </div>
    </section>

    <!-- 03 / Why it sticks -->
    <section class="bg-bloom border-b border-line">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[104px]">
            <Reveal>
                <SectionTag num="03" label="WHY IT STICKS" />
                <h2 class="mt-4 max-w-[640px] font-display text-3xl font-bold leading-[1.06] tracking-display md:text-[42px]">
                    The switch only has to happen once.
                </h2>
                <div class="mt-11 border-t border-line">
                    <div v-for="row in [
                        { is: 'Start with one department — HR profiles are free', not: 'Months of all-at-once migration' },
                        { is: 'Your data imported, checked, and exportable any day', not: 'Lock-in dressed up as onboarding' },
                        { is: 'Old tools stay until each team says so', not: 'A hard cut-over date nobody chose' },
                    ]" :key="row.is" class="grid gap-3 border-b border-line py-[22px] md:grid-cols-2 md:gap-6">
                        <p class="flex items-baseline gap-3 text-base font-semibold">
                            <Switch on sm class="relative top-1" />
                            {{ row.is }}
                        </p>
                        <p class="text-[15.5px] text-ink-faint line-through decoration-[rgba(152,160,171,0.5)]">{{ row.not }}</p>
                    </div>
                </div>
                <p class="mt-8 text-[15px] text-ink-soft">
                    How it went for a 142-person logistics company:
                    <Link href="/customers/veldkamp" class="font-semibold text-accent hover:underline">read the Veldkamp story →</Link>
                </p>
            </Reveal>
        </div>
    </section>

    <CtaBand title="Tell us what you run today."
        sub="We map your tools to modules and send back a real monthly number — usually the same day." cta="Map my stack"
        href="/contact" />
</template>
