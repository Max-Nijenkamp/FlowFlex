<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import MarketingLayout from '../../Components/Layout/MarketingLayout.vue'
import CtaBand from '../../Components/Marketing/CtaBand.vue'
import Reveal from '../../Components/UI/Reveal.vue'
import { domainColors } from '../../data/marketing'

defineOptions({ layout: MarketingLayout })

const timeline: Array<[string, string, string, string]> = [
    ['Month 1', 'hr', 'HR & people live', 'Employee profiles + leave imported from BambooHR. First tool cancelled.'],
    ['Month 2', 'finance', 'Finance live', 'Open invoices and balances from Xero. Deal-won → draft-invoice flow starts immediately.'],
    ['Month 3', 'crm', 'CRM live', 'HubSpot pipeline mapped across. Support tickets begin feeding account health.'],
    ['Month 4', 'projects', 'Projects + the rest', 'Asana boards and Harvest hours land. Zapier account deleted — nothing left to glue.'],
]

const running: Array<[string, string]> = [
    ['hr', 'HR & people'], ['finance', 'Finance'], ['crm', 'CRM'], ['projects', 'Projects'], ['support', 'Support'], ['lms', 'Learning'],
]
</script>

<template>
    <Head>
        <title>Veldkamp Logistics — customer story</title>
        <meta name="description" content="Nine tools became one platform. How Veldkamp Logistics (127 people) switched to FlowFlex domain by domain in four months." />
    </Head>

    <section class="ff-hero ff-grid-bg" style="padding-bottom: 72px">
        <div class="wrap">
            <div class="ff-crumb"><span>Customers</span><span>/</span><span class="here">Veldkamp Logistics</span></div>
            <h1 class="mt-6" style="max-width: 880px">
                Nine tools became one platform.<br /><span class="acc">The Tuesday copy-paste shift didn't survive.</span>
            </h1>
            <div class="mono mt-7 flex flex-wrap gap-x-7 gap-y-2 text-[12px]" style="color: var(--ink-faint)">
                <span class="whitespace-nowrap">logistics · 127 people</span><span>·</span>
                <span class="whitespace-nowrap">switched over 4 months</span><span>·</span>
                <span class="whitespace-nowrap">9 tools replaced</span>
            </div>
        </div>
    </section>

    <section class="ff-section" style="background: var(--card)">
        <div class="wrap">
            <Reveal>
                <div class="ff-cells" style="margin-top: 0">
                    <div class="ff-cell">
                        <span class="corner"></span>
                        <div class="big">9 → 1</div>
                        <h3>Tools consolidated</h3>
                        <p>BambooHR, Xero, HubSpot, Asana, Harvest, Freshdesk, Zapier, TalentLMS and a shared drive.</p>
                    </div>
                    <div class="ff-cell">
                        <span class="corner"></span>
                        <div class="big">−46%</div>
                        <h3>Monthly software spend</h3>
                        <p>From €1.840 across nine invoices to €990 on one — at the same headcount.</p>
                    </div>
                    <div class="ff-cell">
                        <span class="corner"></span>
                        <div class="big">6 h<em>/week</em></div>
                        <h3>Re-typing eliminated</h3>
                        <p>One employee record, one customer record. The duplicate-entry shift simply stopped existing.</p>
                    </div>
                </div>
            </Reveal>
        </div>
    </section>

    <section class="ff-section">
        <div class="wrap">
            <div class="grid items-start gap-11 lg:gap-[72px] lg:grid-cols-2">
                <Reveal>
                    <p class="ff-tag"><b>01</b> / THE SWITCH</p>
                    <h2 style="font-size: 34px">Domain by domain, four months.</h2>
                    <div class="mt-9" style="border-left: 1px solid var(--line-strong)">
                        <div v-for="[m, d, t, p] in timeline" :key="m" class="relative py-4 pl-8">
                            <span
                                class="absolute top-[24px] -left-[6px] box-border h-[11px] w-[11px] rounded-full bg-white"
                                :style="{ border: '2px solid ' + domainColors[d] }"
                            ></span>
                            <span class="mono text-[11px] uppercase" style="color: var(--ink-faint)">{{ m }}</span>
                            <h3 class="mt-1" style="font-size: 18px">{{ t }}</h3>
                            <p class="mt-1.5 text-[14.5px] leading-relaxed" style="color: var(--ink-soft)">{{ p }}</p>
                        </div>
                    </div>
                </Reveal>
                <div class="flex flex-col gap-4.5 lg:sticky lg:top-28">
                    <div class="rounded-[18px] border bg-(--card) px-8 py-7.5" style="border-color: var(--line-strong); box-shadow: 0 1px 2px rgba(17,24,39,0.04)">
                        <p style="font-family: var(--font-display); font-size: 22px; line-height: 1.45; font-weight: 600; letter-spacing: -0.01em">
                            "I found the patchwork during an annual cost review. Thirteen logins, nine invoices, and
                            nobody could tell me why. Now I have one number, and I can defend it."
                        </p>
                        <div class="mt-5 flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full font-bold text-white" style="background: #6366F1; font-family: var(--font-display)">T</span>
                            <span>
                                <span class="block text-[14.5px] font-bold">Tom de Vries</span>
                                <span class="text-[13px]" style="color: var(--ink-faint)">Operations director, Veldkamp Logistics</span>
                            </span>
                        </div>
                    </div>
                    <div class="rounded-[18px] border bg-(--card) px-7 py-6" style="border-color: var(--line-strong)">
                        <p class="ff-tag" style="letter-spacing: 0.18em">RUNNING TODAY</p>
                        <div class="mt-3.5 flex flex-wrap gap-2">
                            <span v-for="[k, n] in running" :key="k" class="ff-dompill" style="padding: 6px 14px; font-size: 12.5px">
                                <span class="chip" :style="{ background: domainColors[k] }"></span>{{ n }}
                            </span>
                        </div>
                        <p class="mono mt-3.5 text-[11.5px]" style="color: var(--ink-faint)">14 modules · €9,90/user · 127 users</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <CtaBand
        title="Your stack could be a story like this."
        sub="Map it on a 30-minute call — the plan and the price come out the other end."
        cta="Plan my switch"
        href="/contact"
    />
</template>
