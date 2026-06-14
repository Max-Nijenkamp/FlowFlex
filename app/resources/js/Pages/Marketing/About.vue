<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import BlueprintCell from '@/Components/Marketing/BlueprintCell.vue'
import CtaBand from '@/Components/Marketing/CtaBand.vue'
import Kicker from '@/Components/Marketing/Kicker.vue'
import SectionTag from '@/Components/Marketing/SectionTag.vue'
import Logo from '@/Components/UI/Logo.vue'
import Reveal from '@/Components/UI/Reveal.vue'
import Switch from '@/Components/UI/Switch.vue'
import { Head } from '@inertiajs/vue3'

defineOptions({ layout: MarketingLayout })

const values = [
    { name: 'Flexible by design', detail: 'Activate modules one at a time; pay only for what is active.' },
    { name: 'Flow-first UX', detail: 'Speed and clarity on every screen. Nothing blocks the actual job.' },
    { name: 'Unified truth', detail: 'One database, one login. Data flows between departments automatically.' },
    { name: 'Transparent and fair', detail: 'Clear pricing, GDPR-compliant, your data is portable — always.' },
    { name: 'Customer-first', detail: 'Built for department managers and employees, not the IT department.' },
]

const isIsnt = [
    { is: 'A modular platform you grow into', not: 'An ERP that imposes a process model' },
    { is: 'Built for 50 and for 500 people', not: 'Enterprise-only software' },
    { is: 'Focused — every addition serves Flow or Flex', not: 'A feature factory' },
]

const trust = ['EU-hosted', 'GDPR-compliant, DSAR built in', 'Two-factor authentication', 'Full audit log', 'Export your data any day']
</script>

<template>
    <Head title="About">
        <meta name="description"
            content="FlowFlex exists because growing companies shouldn't need fifteen tools to run one business." />
    </Head>

    <!-- Hero -->
    <section class="bg-bloom border-b border-line">
        <div class="mx-auto max-w-6xl px-6 pt-14 pb-14 md:pt-[84px] md:pb-[76px]">
            <Kicker>About</Kicker>
            <h1 class="mt-[26px] max-w-[880px] font-display text-[36px] font-bold leading-[1.06] tracking-[-0.03em] md:text-[56px]">
                Growing companies shouldn't need
                <span class="[box-shadow:inset_0_-0.16em_0_#C7D2FE]">fifteen tools</span> to run one business.
            </h1>
            <div class="mt-9 flex max-w-[640px] flex-col gap-5 text-[16px] leading-[1.7] text-ink-soft md:text-[17.5px]">
                <p>
                    FlowFlex started with that frustration. Somewhere between 40 and 80 employees, every company hits
                    the same wall: the cost of switching, syncing and re-entering data across a patchwork of tools
                    quietly outgrows the cost of the tools themselves.
                </p>
                <p>
                    So we built the alternative — one platform where every operational tool shares one data model,
                    one login and one bill. Modules switch on when you need them and off when you don't. Built in
                    Europe, hosted in Europe, GDPR-compliant from the first line of code.
                </p>
            </div>
        </div>
    </section>

    <!-- 01 / Values -->
    <section class="border-b border-line bg-card">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[104px]">
            <Reveal>
                <SectionTag num="01" label="VALUES" />
                <h2 class="mt-4 max-w-[640px] font-display text-3xl font-bold leading-[1.06] tracking-display md:text-[42px]">
                    What we hold ourselves to.
                </h2>
            </Reveal>
            <Reveal :delay="100">
                <div class="mt-[52px] grid gap-px border border-line-strong bg-line-strong sm:grid-cols-2 md:grid-cols-3">
                    <BlueprintCell v-for="v in values" :key="v.name" :title="v.name" :body="v.detail" />
                    <div class="flex items-center justify-center bg-card px-7 py-8">
                        <Logo variant="dark" />
                    </div>
                </div>
            </Reveal>
        </div>
    </section>

    <!-- 02 / Is / isn't -->
    <section class="border-b border-line">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[104px]">
            <Reveal>
                <SectionTag num="02" label="IS / ISN'T" />
                <h2 class="mt-4 max-w-[640px] font-display text-3xl font-bold leading-[1.06] tracking-display md:text-[42px]">
                    What FlowFlex is — and isn't.
                </h2>
            </Reveal>
            <Reveal :delay="100">
                <div class="mt-11 border-t border-line">
                    <div v-for="row in isIsnt" :key="row.is"
                        class="grid gap-3 border-b border-line py-[22px] md:grid-cols-2 md:gap-6">
                        <p class="flex items-baseline gap-3 text-base font-semibold">
                            <Switch on sm class="relative top-1" />
                            {{ row.is }}
                        </p>
                        <p class="text-[15.5px] text-ink-faint line-through decoration-[rgba(152,160,171,0.5)]">
                            {{ row.not }}
                        </p>
                    </div>
                </div>
            </Reveal>
        </div>
    </section>

    <!-- Trust strip -->
    <section class="bg-bloom border-b border-line">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-center gap-x-9 gap-y-3 px-6 py-16 font-mono text-[12.5px] text-ink-soft">
            <template v-for="(t, i) in trust" :key="t">
                <span>{{ t }}</span>
                <span v-if="i < trust.length - 1" class="text-line-strong">·</span>
            </template>
        </div>
    </section>

    <CtaBand title="Talk to the team."
        sub="Questions about modules, pricing or switching from your current stack — we reply within one business day."
        cta="Contact us" href="/contact" />
</template>
