<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import MarketingLayout from '../../Components/Layout/MarketingLayout.vue'
import CtaBand from '../../Components/Marketing/CtaBand.vue'
import Logo from '../../Components/UI/Logo.vue'
import Reveal from '../../Components/UI/Reveal.vue'

defineOptions({ layout: MarketingLayout })

const values = [
    { name: 'Flexible by design', detail: 'Activate modules one at a time; pay only for what is active.' },
    { name: 'Flow-first UX', detail: 'Speed and clarity on every screen. Nothing blocks the actual job.' },
    { name: 'Unified truth', detail: 'One database, one login. Data flows between departments automatically.' },
    { name: 'Transparent and fair', detail: 'Clear pricing, GDPR-compliant, your data is portable — always.' },
    { name: 'Customer-first', detail: 'Built for department managers and employees, not the IT department.' },
]

const isnt = [
    { is: 'A modular platform you grow into', not: 'An ERP that imposes a process model' },
    { is: 'Built for 50 and for 500 people', not: 'Enterprise-only software' },
    { is: 'Focused — every addition serves Flow or Flex', not: 'A feature factory' },
]

const trust = ['EU-hosted', 'GDPR-compliant, DSAR built in', 'Two-factor authentication', 'Full audit log', 'Export your data any day']
</script>

<template>
    <Head>
        <title>About</title>
        <meta name="description" content="FlowFlex — one platform where every operational tool shares one data model, one login and one bill. Built in Europe, GDPR-compliant from the first line of code." />
    </Head>

    <!-- Hero -->
    <section class="ff-hero ff-grid-bg" style="padding-bottom: 76px">
        <div class="wrap">
            <span class="ff-kicker"><span class="sq"></span>About</span>
            <h1 style="max-width: 880px; font-size: clamp(36px, 5vw, 56px)">
                Growing companies shouldn't need <span class="u">fifteen tools</span> to run one business.
            </h1>
            <div class="mt-9 flex max-w-[640px] flex-col gap-5 text-[17.5px] leading-[1.7]" style="color: var(--ink-soft)">
                <p>
                    FlowFlex started with that frustration. Somewhere between 40 and 80 employees, every company hits
                    the same wall: the cost of switching, syncing and re-entering data across a patchwork of tools
                    quietly outgrows the cost of the tools themselves.
                </p>
                <p>
                    So we built the alternative — one platform where every operational tool shares one data model, one
                    login and one bill. Modules switch on when you need them and off when you don't. Built in Europe,
                    hosted in Europe, GDPR-compliant from the first line of code.
                </p>
            </div>
        </div>
    </section>

    <!-- 01 / Values -->
    <section class="ff-section" style="background: var(--card)">
        <div class="wrap">
            <Reveal>
                <p class="ff-tag"><b>01</b> / VALUES</p>
                <h2>What we hold ourselves to.</h2>
                <div class="ff-cells">
                    <div v-for="v in values" :key="v.name" class="ff-cell">
                        <span class="corner"></span>
                        <h3 style="margin-top: 0">{{ v.name }}</h3>
                        <p>{{ v.detail }}</p>
                    </div>
                    <div class="ff-cell flex items-center justify-center">
                        <Logo :size="30" />
                    </div>
                </div>
            </Reveal>
        </div>
    </section>

    <!-- 02 / Is / isn't -->
    <section class="ff-section">
        <div class="wrap">
            <Reveal>
                <p class="ff-tag"><b>02</b> / IS / ISN'T</p>
                <h2>What FlowFlex is — and isn't.</h2>
                <div class="mt-11" style="border-top: 1px solid var(--line)">
                    <div
                        v-for="row in isnt"
                        :key="row.is"
                        class="grid gap-3 py-5.5 md:grid-cols-2 md:gap-6"
                        style="border-bottom: 1px solid var(--line)"
                    >
                        <p class="flex items-baseline gap-3 text-[16px] font-semibold">
                            <span class="ff-sw on sm relative top-1"></span>{{ row.is }}
                        </p>
                        <p class="text-[15.5px] line-through" style="color: var(--ink-faint); text-decoration-color: rgba(152,160,171,0.5)">
                            {{ row.not }}
                        </p>
                    </div>
                </div>
            </Reveal>
        </div>
    </section>

    <!-- Trust strip -->
    <section class="ff-section ff-grid-bg" style="padding: 64px 0">
        <div class="wrap mono flex flex-wrap justify-center gap-x-9 gap-y-3 text-[12.5px]" style="color: var(--ink-soft)">
            <template v-for="(t, i) in trust" :key="t">
                <span v-if="i > 0" style="color: var(--line-strong)">·</span>
                <span>{{ t }}</span>
            </template>
        </div>
    </section>

    <CtaBand
        title="Talk to the team."
        sub="Questions about modules, pricing or switching from your current stack — we reply within one business day."
        cta="Contact us"
        href="/contact"
    />
</template>
