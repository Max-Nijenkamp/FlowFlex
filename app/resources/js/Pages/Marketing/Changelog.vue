<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import MarketingLayout from '../../Components/Layout/MarketingLayout.vue'
import Reveal from '../../Components/UI/Reveal.vue'
import { domainColors } from '../../data/marketing'

defineOptions({ layout: MarketingLayout })

const domainLabel: Record<string, string> = { hr: 'HR & people', finance: 'Finance', crm: 'CRM & sales', projects: 'Projects' }

const log = [
    {
        month: 'JUNE 2026',
        entries: [
            { d: '11 Jun', domain: 'hr', tag: 'New module', on: true, t: 'Recruiting', p: 'Vacancies, candidate pipeline and structured scoring — hired candidates flow straight into onboarding.' },
            { d: '05 Jun', domain: 'finance', tag: 'Improved', on: false, t: 'Invoicing — payment links', p: 'Invoices now carry a pay-now link; payments reconcile themselves against the ledger.' },
        ],
    },
    {
        month: 'MAY 2026',
        entries: [
            { d: '27 May', domain: 'projects', tag: 'New module', on: true, t: 'Time tracking', p: 'Hours land on projects and flow into payroll and billable invoice lines.' },
            { d: '15 May', domain: 'crm', tag: 'Improved', on: false, t: 'Pipeline — health signals', p: 'Accounts now surface support-ticket spikes before renewal conversations.' },
            { d: '02 May', domain: 'hr', tag: 'Improved', on: false, t: 'Leave — coverage warnings', p: 'Approving leave now flags scheduling gaps in the same dialog.' },
        ],
    },
]
</script>

<template>
    <Head>
        <title>Changelog</title>
        <meta name="description" content="Every module and improvement as it ships. New modules appear on your billing page the day they land here — switched off, until you say otherwise." />
    </Head>

    <section class="ff-hero ff-grid-bg" style="padding: 72px 0 56px">
        <div class="wrap">
            <span class="ff-kicker"><span class="sq"></span>Changelog</span>
            <h1 style="font-size: clamp(36px, 5vw, 52px)">New on the switchboard.</h1>
            <p class="ff-lede">
                Every module and improvement as it ships. New modules appear on your billing page the day they land
                here — switched off, until you say otherwise.
            </p>
        </div>
    </section>

    <section class="ff-section" style="background: var(--card)">
        <div class="wrap" style="max-width: 880px">
            <Reveal v-for="g in log" :key="g.month">
                <div class="mb-12">
                    <p class="ff-tag" style="letter-spacing: 0.22em">{{ g.month }}</p>
                    <div class="mt-4.5 flex flex-col" style="border-left: 1px solid var(--line-strong)">
                        <div v-for="e in g.entries" :key="e.t" class="relative py-4.5 pl-9">
                            <span
                                class="absolute top-[26px] -left-[6px] box-border h-[11px] w-[11px] rounded-full bg-white"
                                :style="{ border: '2px solid ' + domainColors[e.domain] }"
                            ></span>
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="mono text-[11.5px]" style="color: var(--ink-faint)">{{ e.d }}</span>
                                <span class="ff-state" :class="e.on ? 'on' : 'off'">{{ e.tag.toUpperCase() }}</span>
                                <span class="ff-dompill" style="padding: 4px 12px; font-size: 12px">
                                    <span class="chip" :style="{ background: domainColors[e.domain] }"></span>
                                    {{ domainLabel[e.domain] }}
                                </span>
                            </div>
                            <h3 class="mt-2.5" style="font-size: 19px">{{ e.t }}</h3>
                            <p class="mt-1.5 max-w-[620px] text-[15px] leading-[1.65]" style="color: var(--ink-soft)">{{ e.p }}</p>
                        </div>
                    </div>
                </div>
            </Reveal>
            <button type="button" class="ff-btn outline" disabled title="You've reached the beginning">Older entries</button>
        </div>
    </section>
</template>
