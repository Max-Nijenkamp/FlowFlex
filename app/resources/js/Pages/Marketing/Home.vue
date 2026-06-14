<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import BlueprintCell from '@/Components/Marketing/BlueprintCell.vue'
import CtaBand from '@/Components/Marketing/CtaBand.vue'
import FlowBand from '@/Components/Marketing/FlowBand.vue'
import Kicker from '@/Components/Marketing/Kicker.vue'
import ModuleTile from '@/Components/Marketing/ModuleTile.vue'
import Receipt from '@/Components/Marketing/Receipt.vue'
import ReplacesStrip from '@/Components/Marketing/ReplacesStrip.vue'
import SectionTag from '@/Components/Marketing/SectionTag.vue'
import Switchboard from '@/Components/Marketing/Switchboard.vue'
import Reveal from '@/Components/UI/Reveal.vue'
import { domainColors, domains, euro } from '@/data/marketing'
import { Head, Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

defineOptions({ layout: MarketingLayout })

defineProps<{
    domains?: { name: string; modules: number }[]
    module_count?: number
    sample_modules?: { key: string; name: string; domain: string }[]
}>()

// Hero switchboard — optimistic local state, toggles update the total reactively.
const HERO_USERS = 80
const boardModules = ref([
    { key: 'hr.profiles', name: 'Employee profiles', domain: 'hr', cents: 0, on: true },
    { key: 'hr.leave', name: 'Leave & absence', domain: 'hr', cents: 150, on: true },
    { key: 'hr.payroll', name: 'Payroll', domain: 'hr', cents: 250, on: false },
    { key: 'finance.invoicing', name: 'Invoicing', domain: 'finance', cents: 200, on: true },
    { key: 'finance.expenses', name: 'Expenses', domain: 'finance', cents: 100, on: false },
    { key: 'crm.pipeline', name: 'Pipeline', domain: 'crm', cents: 150, on: true },
    { key: 'projects.boards', name: 'Projects & boards', domain: 'projects', cents: 150, on: false },
])

const boardRows = computed(() => boardModules.value.map((m) => ({
    key: m.key,
    name: m.name,
    color: domainColors[m.domain],
    price: m.cents === 0 ? 'included' : `${euro(m.cents)}/user`,
    on: m.on,
})))

const perUserCents = computed(() => boardModules.value.filter((m) => m.on).reduce((sum, m) => sum + m.cents, 0))
const monthlyCents = computed(() => perUserCents.value * HERO_USERS)

function toggleBoard(key: string) {
    const row = boardModules.value.find((m) => m.key === key)
    if (row) row.on = !row.on
}

const tiles = [
    { name: 'Employee profiles', domain: 'hr', price: 'included', on: true },
    { name: 'Leave & absence', domain: 'hr', price: '€1,50/user', on: true },
    { name: 'Invoicing', domain: 'finance', price: '€2,00/user', on: true },
    { name: 'Pipeline', domain: 'crm', price: '€1,50/user', on: true },
    { name: 'Payroll', domain: 'hr', price: '€2,50/user', on: false },
    { name: 'Expenses', domain: 'finance', price: '€1,00/user', on: false },
    { name: 'Tickets', domain: 'support', price: '€1,50/user', on: false },
]

// Only hr/finance/crm have live /product/{domain} pages today.
const linkable = ['hr', 'finance', 'crm']
const coverage = domains.slice(0, 12)
</script>

<template>
    <Head title="Run everything. Pay for what's switched on.">
        <meta name="description"
            content="HR, finance, CRM and 70 more modules on one database. Each one is a switch on your billing page — flip it on when you need it, off when you don't." />
    </Head>

    <!-- Hero -->
    <section class="bg-bloom relative border-b border-line">
        <div class="mx-auto max-w-6xl px-6 pt-14 pb-16 md:pt-[84px] md:pb-[92px]">
            <div class="grid items-center gap-11 lg:grid-cols-[1.05fr_1fr] lg:gap-16">
                <div>
                    <Kicker>Per user · per module</Kicker>
                    <h1 class="mt-[26px] font-display text-[40px] font-bold leading-[1.02] tracking-[-0.03em] md:text-[62px]">
                        Run everything.<br>
                        Pay for what's <span class="[box-shadow:inset_0_-0.16em_0_#C7D2FE]">switched on</span>.
                    </h1>
                    <p class="mt-[22px] max-w-[500px] text-base leading-[1.65] text-ink-soft md:text-lg">
                        HR, finance, CRM and 70 more modules on one database. Each one is a switch on your billing
                        page — flip it on when you need it, off when you don't.
                    </p>
                    <div class="mt-[34px] flex flex-col items-stretch gap-3.5 sm:flex-row sm:flex-wrap sm:items-center">
                        <Link href="/pricing"
                            class="inline-flex items-center justify-center rounded-xl bg-accent px-8 py-4 text-base font-semibold text-white shadow-[0_1px_2px_rgba(79,70,229,0.4),0_8px_20px_-10px_rgba(79,70,229,0.5)] transition ease-out duration-150 hover:bg-accent-deep active:scale-[0.98]">
                            Build your price
                        </Link>
                        <Link href="/features"
                            class="inline-flex items-center justify-center rounded-xl border border-line-strong bg-card px-8 py-4 text-base font-semibold text-ink shadow-[0_1px_2px_rgba(17,24,39,0.04)] transition ease-out duration-150 hover:border-ink-faint active:scale-[0.98]">
                            See the modules
                        </Link>
                    </div>
                    <p class="mt-5 font-mono text-xs text-ink-faint">teams of 50–500 · no tiers · no lock-in · data portable</p>
                </div>
                <Switchboard :rows="boardRows" title="Your modules" :meta="`${HERO_USERS} users`"
                    :formula="`${euro(perUserCents)}/user × ${HERO_USERS} users`"
                    :total="`€${Math.round(monthlyCents / 100)}`" total-suffix="/month" @toggle="toggleBoard" />
            </div>
        </div>
    </section>

    <ReplacesStrip />

    <!-- 01 / The patchwork tax -->
    <section class="border-b border-line bg-card">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[104px]">
            <Reveal>
                <SectionTag num="01" label="THE PATCHWORK TAX" />
                <h2 class="mt-4 max-w-[640px] font-display text-3xl font-bold leading-[1.06] tracking-display md:text-[42px]">
                    Twelve tools, one company, and nothing talks to anything.
                </h2>
                <p class="mt-[22px] max-w-[500px] text-[16.5px] leading-[1.65] text-ink-soft">
                    Somewhere between 40 and 80 people, the cost of switching, syncing and re-typing quietly outgrows
                    the cost of the tools themselves.
                </p>
            </Reveal>
            <Reveal :delay="100">
                <div class="mt-[52px] grid gap-px border border-line-strong bg-line-strong md:grid-cols-3">
                    <BlueprintCell big="5–15" title="Separate tools at 100 people"
                        body="Each with its own login, its own invoice, its own idea of who your employees are." />
                    <BlueprintCell big="×5" title="Forms per new hire"
                        body="HR, payroll, IT, the LMS, the project tool. One person, five data entries, five chances to typo." />
                    <BlueprintCell big="0" title="Integrations to maintain"
                        body="One database. There is nothing to glue together, so nothing breaks at 2am." />
                </div>
            </Reveal>
        </div>
    </section>

    <!-- 02 / Flex -->
    <section class="bg-bloom border-b border-line">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[104px]">
            <Reveal>
                <SectionTag num="02" label="FLEX" />
                <h2 class="mt-4 max-w-[640px] font-display text-3xl font-bold leading-[1.06] tracking-display md:text-[42px]">
                    Modules are switches, not sales calls.
                </h2>
                <p class="mt-[22px] max-w-[500px] text-[16.5px] leading-[1.65] text-ink-soft">
                    Flip one on and it's live immediately. Flip it off and billing stops at month-end — your data
                    stays exactly where it was.
                </p>
            </Reveal>
            <Reveal :delay="100">
                <div class="mt-[52px] grid grid-cols-2 gap-2.5 md:grid-cols-4 md:gap-3.5">
                    <ModuleTile v-for="t in tiles" :key="t.name" :name="t.name" :color="domainColors[t.domain]"
                        :price="t.price" :on="t.on" />
                    <ModuleTile ghost ghost-label="+ 65 more modules" />
                </div>
            </Reveal>
        </div>
    </section>

    <!-- 03 / Flow -->
    <FlowBand tag="03" />

    <!-- 04 / Coverage -->
    <section class="border-b border-line bg-card">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[104px]">
            <Reveal>
                <SectionTag num="04" label="COVERAGE" />
                <h2 class="mt-4 max-w-[640px] font-display text-3xl font-bold leading-[1.06] tracking-display md:text-[42px]">
                    Every department, already inside.
                </h2>
            </Reveal>
            <Reveal :delay="100">
                <div class="mt-[52px] overflow-hidden rounded-[14px] border border-line-strong bg-card shadow-[0_1px_2px_rgba(17,24,39,0.03)]">
                    <component :is="linkable.includes(d.key) ? Link : 'div'" v-for="(d, i) in coverage" :key="d.key"
                        :href="linkable.includes(d.key) ? `/product/${d.key}` : undefined"
                        class="grid grid-cols-[18px_1fr_84px] items-center gap-2.5 border-b border-line px-4 py-3 text-sm transition ease-out duration-150 last:border-b-0 md:grid-cols-[24px_1fr_110px_90px] md:gap-4 md:px-[22px] md:py-[13px]"
                        :class="[i % 2 === 0 ? 'bg-[#FAF9F5]' : '', linkable.includes(d.key) && 'hover:bg-accent-soft/40']">
                        <span class="h-[11px] w-[11px] rounded-[3px]" :style="{ background: domainColors[d.key] }" />
                        <span class="font-semibold">{{ d.name }}</span>
                        <span class="whitespace-nowrap font-mono text-[11.5px] text-ink-faint">{{ d.modules }} modules</span>
                        <span class="hidden whitespace-nowrap text-right font-mono text-[11.5px] md:block"
                            :class="linkable.includes(d.key) ? 'text-accent' : 'text-ink-faint'">
                            {{ linkable.includes(d.key) ? 'explore →' : 'soon' }}
                        </span>
                    </component>
                </div>
                <p class="mt-4 font-mono text-xs text-ink-faint">+ 4 more departments · all on the same database</p>
            </Reveal>
        </div>
    </section>

    <!-- 05 / Pricing teaser -->
    <section class="bg-bloom border-b border-line">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[104px]">
            <div class="grid items-center gap-11 lg:grid-cols-[1fr_400px] lg:gap-20">
                <Reveal>
                    <SectionTag num="05" label="PRICING" />
                    <h2 class="mt-4 max-w-[640px] font-display text-3xl font-bold leading-[1.06] tracking-display md:text-[42px]">
                        Your invoice is a list, not a tier.
                    </h2>
                    <p class="mt-[22px] max-w-[500px] text-[16.5px] leading-[1.65] text-ink-soft">
                        The sum of the modules you switched on, times the people on your team. The per-module price is
                        identical at 50 users or 500 — you pay for more seats, never a higher tier.
                    </p>
                    <div class="mt-[34px]">
                        <Link href="/pricing"
                            class="inline-flex items-center justify-center rounded-xl bg-accent px-8 py-4 text-base font-semibold text-white shadow-[0_1px_2px_rgba(79,70,229,0.4),0_8px_20px_-10px_rgba(79,70,229,0.5)] transition ease-out duration-150 hover:bg-accent-deep active:scale-[0.98]">
                            Build your price
                        </Link>
                    </div>
                </Reveal>
                <Reveal :delay="100">
                    <Receipt title="FLOWFLEX · MONTHLY" class="rotate-[0.6deg]">
                        <div class="h-3.5" />
                        <div class="mb-1.5 flex justify-between gap-4 whitespace-nowrap border-b border-dashed border-line-strong py-[7px] font-bold text-ink">
                            <span>module</span><span>/user</span>
                        </div>
                        <div class="flex justify-between gap-4 whitespace-nowrap py-[7px] text-ink-soft"><span>Employee profiles</span><span>€0,00</span></div>
                        <div class="flex justify-between gap-4 whitespace-nowrap py-[7px] text-ink-soft"><span>Leave &amp; absence</span><span>€1,50</span></div>
                        <div class="flex justify-between gap-4 whitespace-nowrap py-[7px] text-ink-soft"><span>Invoicing</span><span>€2,00</span></div>
                        <div class="flex justify-between gap-4 whitespace-nowrap py-[7px] text-ink-soft"><span>Pipeline</span><span>€1,50</span></div>
                        <div class="mt-2 flex justify-between gap-4 whitespace-nowrap border-t border-dashed border-line-strong pt-3.5 pb-[7px] text-base font-bold text-ink">
                            <span>€5,00 × 80 users</span><span>€400</span>
                        </div>
                        <div class="h-2" />
                        <p class="text-center text-[11px] text-ink-faint">change modules any month · no contracts</p>
                    </Receipt>
                </Reveal>
            </div>
        </div>
    </section>

    <CtaBand title="Switch on what you need. Nothing else."
        sub="See what your stack would cost on one platform — it takes about a minute." />
</template>
