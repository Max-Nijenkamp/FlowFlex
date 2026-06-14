<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import CtaBand from '@/Components/Marketing/CtaBand.vue'
import DomainPill from '@/Components/Marketing/DomainPill.vue'
import Kicker from '@/Components/Marketing/Kicker.vue'
import SectionTag from '@/Components/Marketing/SectionTag.vue'
import Reveal from '@/Components/UI/Reveal.vue'
import Switch from '@/Components/UI/Switch.vue'
import { domainColors, domains as allDomains, euro } from '@/data/marketing'
import { Head, Link } from '@inertiajs/vue3'

defineOptions({ layout: MarketingLayout })

defineProps<{
    domains: {
        key: string
        name: string
        description: string
        modules: { name: string; price_cents: number }[]
        flows: string[]
    }[]
}>()

const nextInLine = allDomains.filter((d) => !['hr', 'finance', 'crm'].includes(d.key)).slice(0, 10)
</script>

<template>
    <Head title="Product">
        <meta name="description"
            content="Every FlowFlex module that ships today — HR, finance, CRM and the core platform. Each one is a switch, not a sales call." />
    </Head>

    <!-- Hero -->
    <section class="bg-bloom border-b border-line">
        <div class="mx-auto max-w-6xl px-6 pt-14 pb-16 md:pt-[84px] md:pb-[92px]">
            <Kicker>Product</Kicker>
            <h1 class="mt-[26px] max-w-[720px] font-display text-[40px] font-bold leading-[1.02] tracking-[-0.03em] md:text-[62px]">
                Four departments today.<br>
                The rest is <span class="[box-shadow:inset_0_-0.16em_0_#C7D2FE]">already wired</span>.
            </h1>
            <p class="mt-[22px] max-w-[500px] text-base leading-[1.65] text-ink-soft md:text-lg">
                Every module below ships today. Each one is a switch on your billing page — not a sales call,
                not an implementation project.
            </p>
        </div>
    </section>

    <!-- Domain sections, alternating white/paper -->
    <section v-for="(domain, i) in domains" :key="domain.key" class="border-b border-line"
        :class="i % 2 === 1 ? 'bg-card' : ''">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[104px]">
            <div class="grid items-start gap-12 lg:grid-cols-2 lg:gap-16">
                <Reveal>
                    <SectionTag :num="String(i + 1).padStart(2, '0')" label="DOMAIN" />
                    <h2 class="mt-4 flex items-center gap-3.5 font-display text-3xl font-bold leading-[1.06] tracking-display md:text-[42px]">
                        <span class="h-3.5 w-3.5 shrink-0 rounded-[4px]"
                            :style="{ background: domainColors[domain.key] ?? '#94A3B8' }" />
                        {{ domain.name }}
                    </h2>
                    <p class="mt-[22px] max-w-[500px] text-[16.5px] leading-[1.65] text-ink-soft">{{ domain.description }}</p>
                    <Link :href="`/product/${domain.key}`"
                        class="group mt-[22px] inline-flex items-center gap-2 text-[15px] font-semibold text-ink">
                        Explore {{ domain.name }}
                        <span class="text-accent transition-transform ease-out duration-150 group-hover:translate-x-1">→</span>
                    </Link>
                    <div v-if="domain.flows.length" class="mt-7">
                        <p class="font-mono text-[11.5px] uppercase tracking-[0.16em] text-ink-faint">Flows automatically</p>
                        <div class="mt-3 flex flex-col gap-2.5">
                            <p v-for="flow in domain.flows" :key="flow"
                                class="flex items-baseline gap-2.5 text-[14.5px] text-ink-soft">
                                <span class="relative -top-px h-[7px] w-[7px] shrink-0 rounded-[2px]"
                                    :style="{ background: domainColors[domain.key] ?? '#94A3B8' }" />
                                {{ flow }}
                            </p>
                        </div>
                    </div>
                </Reveal>
                <Reveal :delay="120">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div v-for="m in domain.modules" :key="m.name"
                            class="flex flex-col gap-1.5 rounded-xl border border-line-strong px-[18px] py-4"
                            :class="i % 2 === 1 ? 'bg-paper' : 'bg-card'">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-sm font-semibold">{{ m.name }}</span>
                                <Switch :on="m.price_cents === 0" sm />
                            </div>
                            <span class="font-mono text-[11.5px] text-ink-faint">
                                {{ m.price_cents === 0 ? 'included' : `${euro(m.price_cents)}/user` }}
                            </span>
                        </div>
                    </div>
                </Reveal>
            </div>
        </div>
    </section>

    <!-- Next in line -->
    <section class="bg-bloom border-b border-line">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[104px]">
            <Reveal>
                <SectionTag :num="String(domains.length + 1).padStart(2, '0')" label="NEXT IN LINE" />
                <h2 class="mt-4 max-w-[640px] font-display text-3xl font-bold leading-[1.06] tracking-display md:text-[42px]">
                    Waiting on the switchboard.
                </h2>
                <p class="mt-[22px] max-w-[500px] text-[16.5px] leading-[1.65] text-ink-soft">
                    Twelve more departments share the same database and the same pricing model, rolling out domain
                    by domain.
                </p>
                <div class="mt-10 flex flex-wrap gap-2.5">
                    <DomainPill v-for="d in nextInLine" :key="d.key" :color="domainColors[d.key]" dashed>
                        {{ d.name }}
                        <span class="font-mono text-[10px] font-normal text-ink-faint">soon</span>
                    </DomainPill>
                </div>
            </Reveal>
        </div>
    </section>

    <CtaBand title="Only pay for the rows you need."
        sub="Start with one domain. The rest will be one switch away." />
</template>
