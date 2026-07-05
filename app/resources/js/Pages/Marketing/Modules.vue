<script setup lang="ts">
import { computed, ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import MarketingLayout from '../../Components/Layout/MarketingLayout.vue'
import CtaBand from '../../Components/Marketing/CtaBand.vue'
import Reveal from '../../Components/UI/Reveal.vue'
import { domainColors } from '../../data/marketing'

defineOptions({ layout: MarketingLayout })

interface CatalogDomain {
    key: string
    name: string
    mods: Array<[string, string, string]> // [name, price, desc]
}

const catalog: CatalogDomain[] = [
    {
        key: 'hr', name: 'HR & people',
        mods: [
            ['Employee profiles', 'included', 'One record per person — contracts, documents, history.'],
            ['Leave & absence', '€1,50', 'Requests, balances, approvals, team calendar.'],
            ['Payroll', '€2,50', 'Salary runs that read contracts and approved leave.'],
            ['Recruiting', '€1,50', 'Vacancies, candidate pipeline, structured scoring.'],
            ['Onboarding', '€1,00', 'Checklists that provision IT, LMS and payroll.'],
            ['Time tracking', '€1,00', 'Hours that flow into payroll and billing.'],
        ],
    },
    {
        key: 'finance', name: 'Finance & accounting',
        mods: [
            ['Invoicing', '€2,00', 'Drafts itself from won deals and logged hours.'],
            ['Expenses', '€1,00', 'Receipts, approval chains, reimbursement runs.'],
            ['AP / AR', '€1,50', 'Bills in, payments out, ageing in one view.'],
            ['Reporting', '€1,00', 'P&L and cash flow from the live ledger.'],
        ],
    },
    {
        key: 'crm', name: 'CRM & sales',
        mods: [
            ['Contacts', 'included', 'Companies and people, shared with every module.'],
            ['Pipeline', '€1,50', 'Stages, forecasts, win analysis.'],
            ['Deals & quotes', '€1,50', 'Quotes that become invoices the moment they close.'],
        ],
    },
]

const filters: Array<[string, string | null]> = [
    ['All', null],
    ['HR & people', 'hr'],
    ['Finance', 'finance'],
    ['CRM & sales', 'crm'],
]

const activeFilter = ref<string | null>(null)
const visible = computed(() => (activeFilter.value === null ? catalog : catalog.filter((d) => d.key === activeFilter.value)))

const detailPages = ['hr', 'finance', 'crm', 'projects']
</script>

<template>
    <Head>
        <title>Module catalogue</title>
        <meta name="description" content="73 modules across 16 departments. Same database, same pricing model — flip any of them on from your billing page." />
    </Head>

    <section class="ff-hero ff-grid-bg" style="padding-bottom: 64px">
        <div class="wrap">
            <span class="ff-kicker"><span class="sq"></span>Module catalogue</span>
            <h1 style="max-width: 700px">Every <span class="u">switch</span><br />on the board.</h1>
            <p class="ff-lede">
                73 modules across 16 departments. Same database, same pricing model — flip any of them on from your
                billing page.
            </p>
            <div class="mt-9 flex flex-wrap gap-2.5">
                <button
                    v-for="[label, key] in filters"
                    :key="label"
                    type="button"
                    class="ff-dompill cursor-pointer"
                    :style="activeFilter === key ? { background: 'var(--ink)', color: '#fff', borderColor: 'var(--ink)' } : {}"
                    :aria-pressed="activeFilter === key"
                    @click="activeFilter = key"
                >
                    <span v-if="key" class="chip" :style="{ background: domainColors[key] }"></span>
                    {{ label }}
                </button>
                <span class="ff-dompill" style="border-style: dashed; background: transparent; color: var(--ink-faint)">+ 13 more soon</span>
            </div>
        </div>
    </section>

    <section
        v-for="(d, i) in visible"
        :key="d.key"
        class="ff-section"
        :style="{ background: i % 2 ? 'transparent' : 'var(--card)', padding: '72px 0' }"
    >
        <div class="wrap">
            <Reveal>
                <div class="flex flex-wrap items-baseline justify-between gap-4">
                    <h2 class="mt-0 flex items-center gap-3" style="font-size: 28px">
                        <span class="h-3 w-3 flex-none rounded" :style="{ background: domainColors[d.key] }"></span>
                        {{ d.name }}
                    </h2>
                    <Link v-if="detailPages.includes(d.key)" :href="`/product/${d.key}`" class="ff-arrlink" style="font-size: 14px">
                        Domain overview <span class="arr">→</span>
                    </Link>
                </div>
                <div class="mt-7 grid gap-3.5 md:grid-cols-2 lg:grid-cols-3">
                    <div v-for="[name, price, desc] in d.mods" :key="name" class="ff-tile" style="padding: 20px">
                        <div class="top">
                            <span class="chip" :style="{ background: domainColors[d.key] }"><span></span></span>
                            <span class="mono text-[11px]" style="color: var(--ink-faint)">{{ price === 'included' ? 'included' : price + '/user' }}</span>
                        </div>
                        <div class="nm" style="font-size: 15px">{{ name }}</div>
                        <p class="mt-1.5 text-[13.5px] leading-relaxed" style="color: var(--ink-soft)">{{ desc }}</p>
                    </div>
                </div>
            </Reveal>
        </div>
    </section>

    <section class="ff-section ff-grid-bg" style="padding: 72px 0">
        <div class="wrap text-center">
            <p class="mono text-[12px]" style="color: var(--ink-faint)">
                + 13 more departments — projects, support, marketing, analytics, IT, legal, e-commerce, learning, AI…
            </p>
        </div>
    </section>

    <CtaBand title="Found your first three?" sub="Price them against your team size — the receipt writes itself." />
</template>
