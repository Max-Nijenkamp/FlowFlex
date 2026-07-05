<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import MarketingLayout from '../../Components/Layout/MarketingLayout.vue'
import CtaBand from '../../Components/Marketing/CtaBand.vue'
import Reveal from '../../Components/UI/Reveal.vue'

defineOptions({ layout: MarketingLayout })

const cells: Array<[string, string]> = [
    ['EU-hosted', 'All data lives in EU data centres. It never leaves the region — not for backups, not for support.'],
    ['GDPR by design', 'DSAR handling is a built-in workflow: one request anonymises a person across every module at once.'],
    ['2FA & SSO', 'Two-factor for everyone, enforced for staff. SSO via your identity provider on request.'],
    ['Full audit log', 'Every read and write, by whom, from where. Core platform — free, always on, exportable.'],
    ['Backups & uptime', 'Continuous backups with point-in-time restore. Status page public, incidents post-mortemed.'],
    ['Export any day', 'Your entire workspace as open formats, self-serve, no exit fee. Portability is the product.'],
]

const faq: Array<[string, string]> = [
    ['Where exactly is our data stored?', 'In EU data centres (Netherlands primary, Ireland backup). Region pinning per workspace is on the roadmap.'],
    ['Who can see our data?', 'Your team, per the roles you set. FlowFlex staff access requires your consent per incident and is logged in your audit trail.'],
    ['What happens on deletion?', 'Cancelling archives your workspace for 90 days for export, then a hard purge — backups included — in line with GDPR retention.'],
    ['Do you use subprocessors?', 'A short, public list (hosting, email delivery, payments) — all EU-based or under SCCs. Changes announced 30 days ahead.'],
]
</script>

<template>
    <Head>
        <title>Trust &amp; security</title>
        <meta name="description" content="EU-hosted, GDPR by design, full audit log, export any day. Security and portability are baseline features, not enterprise add-ons." />
    </Head>

    <section class="ff-hero ff-grid-bg" style="padding-bottom: 72px">
        <div class="wrap">
            <span class="ff-kicker"><span class="sq"></span>Trust &amp; security</span>
            <h1 style="max-width: 720px">Your data.<br />Your rules. <span class="acc">Our job.</span></h1>
            <p class="ff-lede">
                FlowFlex holds your HR files, your books and your customer records in one place. That concentration is
                exactly why security and portability are baseline features, not enterprise add-ons.
            </p>
        </div>
    </section>

    <section class="ff-section" style="background: var(--card)">
        <div class="wrap">
            <Reveal>
                <p class="ff-tag"><b>01</b> / GUARANTEES</p>
                <h2>Six things that are always true.</h2>
                <div class="ff-cells">
                    <div v-for="[t, body] in cells" :key="t" class="ff-cell">
                        <span class="corner"></span>
                        <h3 style="margin-top: 0">{{ t }}</h3>
                        <p>{{ body }}</p>
                    </div>
                </div>
            </Reveal>
        </div>
    </section>

    <section class="ff-section">
        <div class="wrap">
            <Reveal>
                <p class="ff-tag"><b>02</b> / STRAIGHT ANSWERS</p>
                <h2>What procurement will ask.</h2>
                <dl class="ff-faq">
                    <div v-for="[q, a] in faq" :key="q" class="ff-faq-row">
                        <dt>{{ q }}</dt>
                        <dd>{{ a }}</dd>
                    </div>
                </dl>
            </Reveal>
        </div>
    </section>

    <CtaBand
        title="Need the paperwork?"
        sub="DPA, subprocessor list and security overview — ready to send to your DPO today."
        cta="Request the pack"
        href="/contact"
    />
</template>
