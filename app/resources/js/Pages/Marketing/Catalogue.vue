<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import CtaBand from '@/Components/Marketing/CtaBand.vue'
import DomainPill from '@/Components/Marketing/DomainPill.vue'
import Kicker from '@/Components/Marketing/Kicker.vue'
import ModuleTile from '@/Components/Marketing/ModuleTile.vue'
import SectionTag from '@/Components/Marketing/SectionTag.vue'
import Reveal from '@/Components/UI/Reveal.vue'
import { domainColors, domains as allDomains, euro } from '@/data/marketing'
import { Head, Link } from '@inertiajs/vue3'

defineOptions({ layout: MarketingLayout })

const props = defineProps<{
    domains: {
        key: string
        name: string
        description: string
        modules: { name: string; price_cents: number }[]
        flows: string[]
    }[]
}>()

const moduleCount = props.domains.reduce((sum, d) => sum + d.modules.length, 0)
const upcoming = allDomains.filter((d) => !['hr', 'finance', 'crm'].includes(d.key))
</script>

<template>
    <Head title="Module catalogue">
        <meta name="description"
            content="Every FlowFlex module on one board — name, price per user, and the switch it lives behind." />
    </Head>

    <!-- Hero -->
    <section class="bg-bloom border-b border-line">
        <div class="mx-auto max-w-6xl px-6 pt-14 pb-16 md:pt-[84px] md:pb-[76px]">
            <Kicker>Module catalogue</Kicker>
            <h1 class="mt-[26px] max-w-[760px] font-display text-[40px] font-bold leading-[1.02] tracking-[-0.03em] md:text-[62px]">
                Every switch <span class="[box-shadow:inset_0_-0.16em_0_#C7D2FE]">on the board</span>.
            </h1>
            <p class="mt-[22px] max-w-[500px] text-base leading-[1.65] text-ink-soft md:text-lg">
                The full catalogue — every module that ships today, its price per user, and nothing hidden behind
                a sales call.
            </p>
            <p class="mt-5 font-mono text-xs text-ink-faint">{{ moduleCount }} modules live · prices per user per month · change any month</p>
        </div>
    </section>

    <!-- Per-domain boards -->
    <section v-for="(domain, i) in domains" :key="domain.key" class="border-b border-line"
        :class="i % 2 === 0 ? 'bg-card' : ''">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[88px]">
            <Reveal>
                <SectionTag :num="String(i + 1).padStart(2, '0')" :label="domain.key.toUpperCase()" />
                <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
                    <h2 class="flex items-center gap-3.5 font-display text-3xl font-bold leading-[1.06] tracking-display md:text-[36px]">
                        <span class="h-3.5 w-3.5 shrink-0 rounded-[4px]"
                            :style="{ background: domainColors[domain.key] ?? '#94A3B8' }" />
                        {{ domain.name }}
                    </h2>
                    <Link v-if="['hr', 'finance', 'crm'].includes(domain.key)" :href="`/product/${domain.key}`"
                        class="group inline-flex items-center gap-2 text-[15px] font-semibold text-ink">
                        Explore
                        <span class="text-accent transition-transform ease-out duration-150 group-hover:translate-x-1">→</span>
                    </Link>
                </div>
                <p class="mt-3 max-w-[560px] text-[15px] leading-[1.6] text-ink-soft">{{ domain.description }}</p>
            </Reveal>
            <Reveal :delay="100">
                <div class="mt-9 grid grid-cols-2 gap-2.5 md:grid-cols-4 md:gap-3.5">
                    <ModuleTile v-for="m in domain.modules" :key="m.name" :name="m.name"
                        :color="domainColors[domain.key] ?? '#94A3B8'"
                        :price="m.price_cents === 0 ? 'included' : `${euro(m.price_cents)}/user`"
                        :on="m.price_cents === 0" />
                </div>
            </Reveal>
        </div>
    </section>

    <!-- Next in line -->
    <section class="bg-bloom border-b border-line">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[88px]">
            <Reveal>
                <SectionTag :num="String(domains.length + 1).padStart(2, '0')" label="NEXT IN LINE" />
                <h2 class="mt-4 max-w-[640px] font-display text-3xl font-bold leading-[1.06] tracking-display md:text-[42px]">
                    The rest of the company is already wired.
                </h2>
                <p class="mt-[22px] max-w-[500px] text-[16.5px] leading-[1.65] text-ink-soft">
                    These departments share the same database and the same pricing model, rolling out domain by domain.
                </p>
                <div class="mt-10 flex flex-wrap gap-2.5">
                    <DomainPill v-for="d in upcoming" :key="d.key" :color="domainColors[d.key]" dashed>
                        {{ d.name }}
                        <span class="font-mono text-[10px] font-normal text-ink-faint">soon</span>
                    </DomainPill>
                </div>
            </Reveal>
        </div>
    </section>

    <CtaBand title="Pick your rows. See your number."
        sub="The pricing page turns this catalogue into your monthly invoice — it takes about a minute." />
</template>
