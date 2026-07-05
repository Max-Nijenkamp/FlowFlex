<script setup lang="ts">
import { computed, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import MarketingLayout from '../../Components/Layout/MarketingLayout.vue'
import CtaBand from '../../Components/Marketing/CtaBand.vue'
import { domainColors } from '../../data/marketing'

defineOptions({ layout: MarketingLayout })

const cats: Array<[string, string | null, string, number]> = [
    ['Getting started', null, 'Workspace setup, inviting your team, first modules', 12],
    ['Billing & modules', null, 'Switching modules on and off, invoices, user counts', 9],
    ['HR & people', 'hr', 'Profiles, leave, payroll runs, onboarding flows', 16],
    ['Finance', 'finance', 'Invoicing, expenses, exports to your accountant', 14],
    ['CRM & sales', 'crm', 'Contacts, pipeline, quotes that become invoices', 11],
    ['Account & security', null, '2FA, roles & permissions, GDPR requests, exports', 10],
]

const articles = [
    'What happens to our data when we switch a module off?',
    'Setting up approval chains for leave requests',
    'Why is my user count different from my headcount?',
    'Exporting everything — the full workspace export',
    'Approving leave from the team calendar',
    'Changing modules mid-month — how billing follows',
    'Inviting your team in bulk with CSV',
    'Two-factor authentication for your whole workspace',
]

const query = ref('')
const results = computed(() => {
    const q = query.value.trim().toLowerCase()
    if (q.length < 2) return articles.slice(0, 4)
    return articles.filter((a) => a.toLowerCase().includes(q))
})
</script>

<template>
    <Head>
        <title>Help center</title>
        <meta name="description" content="FlowFlex help center — 72 articles, answered by humans within one business day if you're stuck." />
    </Head>

    <section class="ff-section ff-grid-bg" style="padding: 76px 0 64px">
        <div class="wrap text-center">
            <span class="ff-kicker"><span class="sq"></span>Help center</span>
            <h1 class="mt-5.5" style="font-size: clamp(34px, 5vw, 46px); letter-spacing: -0.03em">How can we help?</h1>
            <label
                class="mx-auto mt-7 flex max-w-[560px] items-center gap-3 rounded-[14px] border bg-(--card) px-5 py-4 text-left"
                style="border-color: var(--line-strong); box-shadow: 0 1px 2px rgba(17,24,39,0.04), 0 16px 32px -20px rgba(17,24,39,0.15)"
            >
                <svg width="17" height="17" viewBox="0 0 16 16" fill="none" stroke="#98A0AB" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><circle cx="7" cy="7" r="4.5" /><path d="M10.5 10.5L14 14" /></svg>
                <input
                    v-model="query"
                    type="search"
                    class="w-full bg-transparent text-[15.5px] outline-none placeholder:text-(--ink-faint)"
                    placeholder='Search articles — "approve leave", "change modules"…'
                    aria-label="Search help articles"
                />
            </label>
            <p class="mono mt-4 text-[11.5px]" style="color: var(--ink-faint)">
                72 articles · answered by humans within one business day if you're stuck
            </p>
        </div>
    </section>

    <section class="ff-section" style="background: var(--card)">
        <div class="wrap">
            <div class="grid gap-3.5 md:grid-cols-2 lg:grid-cols-3">
                <div v-for="[name, domain, desc, count] in cats" :key="name" class="ff-tile" style="padding: 22px">
                    <div class="top">
                        <span class="chip" :style="{ background: domain ? domainColors[domain] : 'var(--indigo)' }"><span></span></span>
                        <span class="mono text-[10.5px]" style="color: var(--ink-faint)">{{ count }} articles</span>
                    </div>
                    <div class="nm" style="font-size: 15.5px">{{ name }}</div>
                    <p class="mt-1.5 text-[13.5px] leading-relaxed" style="color: var(--ink-soft)">{{ desc }}</p>
                </div>
            </div>

            <div class="mt-11">
                <p class="ff-tag" style="letter-spacing: 0.18em">{{ query.trim().length >= 2 ? 'SEARCH RESULTS' : 'MOST READ THIS WEEK' }}</p>
                <div class="mt-3.5 overflow-hidden rounded-[14px] border" style="border-color: var(--line-strong)">
                    <div
                        v-for="(q, i) in results"
                        :key="q"
                        class="flex items-center justify-between gap-3.5 px-5.5 py-3.5"
                        :style="{ borderBottom: i < results.length - 1 ? '1px solid var(--line)' : 'none', background: i % 2 ? '#FAF9F5' : '#fff' }"
                    >
                        <span class="text-[14.5px] font-semibold">{{ q }}</span>
                        <span class="flex-none" style="color: var(--indigo)">→</span>
                    </div>
                    <div v-if="results.length === 0" class="px-5.5 py-6 text-[14.5px]" style="color: var(--ink-soft)">
                        Nothing matches "{{ query }}" yet — try a shorter word, or ask us directly below.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <CtaBand title="Still stuck?" sub="A human replies within one business day — usually much faster." cta="Contact support" href="/contact" />
</template>
