<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import MarketingLayout from '../../Components/Layout/MarketingLayout.vue'
import CtaBand from '../../Components/Marketing/CtaBand.vue'
import FlowBand from '../../Components/Marketing/FlowBand.vue'
import Reveal from '../../Components/UI/Reveal.vue'
import { domainColors, type Flow } from '../../data/marketing'

defineOptions({ layout: MarketingLayout })

const map: Array<{ old: string; mods: Array<[string, string]>; note: string }> = [
    { old: 'BambooHR', mods: [['HR & people', 'hr']], note: '4 modules' },
    { old: 'Xero', mods: [['Finance', 'finance']], note: '3 modules' },
    { old: 'HubSpot', mods: [['CRM & sales', 'crm']], note: '3 modules' },
    { old: 'Asana + Harvest', mods: [['Projects', 'projects']], note: '3 modules' },
    { old: 'Zapier (gluing it all)', mods: [], note: 'not needed — one database' },
]

const migrationFlows: Flow[] = [
    { from: 'CRM', to: 'Finance', event: 'First deal won on FlowFlex', effect: 'First invoice that drafted itself' },
    { from: 'HR', to: 'Payroll', event: 'First hire on FlowFlex', effect: 'Zero forms re-entered downstream' },
    { from: 'You', to: 'Zapier', event: 'Last sync pipeline deleted', effect: 'Nothing to break at 2am anymore' },
]
</script>

<template>
    <Head>
        <title>Switch over</title>
        <meta name="description" content="Tell us what you run today. We map every tool to its FlowFlex modules, import your data, and you go live one department at a time." />
    </Head>

    <section class="ff-hero ff-grid-bg" style="padding-bottom: 72px">
        <div class="wrap">
            <span class="ff-kicker"><span class="sq"></span>Switching</span>
            <h1 style="max-width: 760px">Leave the patchwork.<br /><span class="u">Keep the data.</span></h1>
            <p class="ff-lede">
                Tell us what you run today. We map every tool to its FlowFlex modules, import your data, and you go
                live one department at a time — not in a big-bang weekend.
            </p>
            <div class="ff-hero-ctas">
                <Link href="/contact" class="ff-btn primary lg">Map my stack</Link>
                <a href="#plan" class="ff-arrlink">How migration works <span class="arr">→</span></a>
            </div>
        </div>
    </section>

    <!-- 01 / The map -->
    <section class="ff-section" style="background: var(--card)">
        <div class="wrap">
            <Reveal>
                <p class="ff-tag"><b>01</b> / THE MAP</p>
                <h2>Your stack, translated.</h2>
                <div class="mt-12 overflow-hidden rounded-2xl border" style="border-color: var(--line-strong)">
                    <div
                        v-for="(m, i) in map"
                        :key="m.old"
                        class="grid items-center gap-4 px-6.5 py-4 max-md:grid-cols-1 md:[grid-template-columns:1fr_70px_1fr]"
                        :style="{
                            borderBottom: i < map.length - 1 ? '1px solid var(--line)' : 'none',
                            background: i % 2 ? '#FAF9F5' : '#fff',
                        }"
                    >
                        <span class="text-[15.5px] font-medium line-through" style="color: var(--ink-faint); text-decoration-color: rgba(79,70,229,0.45)">{{ m.old }}</span>
                        <span class="flex max-md:hidden justify-center" style="color: var(--indigo)">
                            <svg width="28" height="12" viewBox="0 0 28 12" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M0 6h24M20 1.5L25 6l-5 4.5" /></svg>
                        </span>
                        <span class="flex flex-wrap items-center gap-2.5">
                            <span v-for="[name, key] in m.mods" :key="name" class="ff-dompill">
                                <span class="chip" :style="{ background: domainColors[key] }"></span>{{ name }}
                            </span>
                            <span class="mono text-[11.5px]" :style="{ color: m.mods.length ? 'var(--ink-faint)' : '#0E8C61', fontWeight: m.mods.length ? 400 : 600 }">{{ m.note }}</span>
                        </span>
                    </div>
                </div>
            </Reveal>
        </div>
    </section>

    <!-- 02 / The plan -->
    <section id="plan" class="ff-section scroll-mt-20">
        <div class="wrap">
            <Reveal>
                <p class="ff-tag"><b>02</b> / THE PLAN</p>
                <h2>Domain by domain, never big-bang.</h2>
                <div class="ff-cells">
                    <div class="ff-cell">
                        <span class="corner"></span>
                        <div class="big">1<em> · week 1</em></div>
                        <h3>Export &amp; map</h3>
                        <p>Pull exports from your current tools. We map fields to FlowFlex modules with you — employees, contacts, open invoices, balances.</p>
                    </div>
                    <div class="ff-cell">
                        <span class="corner"></span>
                        <div class="big">2<em> · week 2</em></div>
                        <h3>Import &amp; verify</h3>
                        <p>Your data lands in a trial workspace. Your team checks it against the old system while both still run.</p>
                    </div>
                    <div class="ff-cell">
                        <span class="corner"></span>
                        <div class="big">3<em> · per domain</em></div>
                        <h3>Go live, cancel one tool</h3>
                        <p>Switch a domain on, cancel the tool it replaces, pocket the difference. Then the next one — at your pace.</p>
                    </div>
                </div>
            </Reveal>
        </div>
    </section>

    <FlowBand
        tag="03"
        title="The day you switch, the flows start."
        lede="The moment two domains share the database, the re-typing between them ends."
        :flows="migrationFlows"
    />

    <CtaBand
        title="Bring one export. We'll show you the rest."
        sub="A mapping call takes 30 minutes and produces your migration plan plus your exact monthly price."
        cta="Plan my switch"
        href="/contact"
    />
</template>
