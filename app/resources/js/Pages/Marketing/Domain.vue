<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import CtaBand from '@/Components/Marketing/CtaBand.vue'
import DomainPill from '@/Components/Marketing/DomainPill.vue'
import FlowBand from '@/Components/Marketing/FlowBand.vue'
import ModuleTile from '@/Components/Marketing/ModuleTile.vue'
import SectionTag from '@/Components/Marketing/SectionTag.vue'
import Reveal from '@/Components/UI/Reveal.vue'
import { domainColors, euro, type MarketingFlow } from '@/data/marketing'
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'

defineOptions({ layout: MarketingLayout })

const props = defineProps<{
    domain: { key: string; name: string; description: string; flows: string[] }
    modules: { key: string; name: string; price_cents: number }[]
}>()

const color = computed(() => domainColors[props.domain.key] ?? '#94A3B8')

// Controller flows are "Event — effect" strings; the Flow band wants both parts.
const bandFlows = computed<MarketingFlow[]>(() =>
    props.domain.flows.map((f) => {
        const [event, ...rest] = f.split(' — ')
        return { event, effect: rest.join(' — ') }
    }),
)

const playsWith: Record<string, [string, string][]> = {
    hr: [['finance', 'Finance — payroll & expense flows'], ['projects', 'Projects — capacity & time'], ['lms', 'Learning — certifications'], ['it', 'IT — provisioning']],
    finance: [['crm', 'CRM — deals become invoices'], ['projects', 'Projects — billable hours'], ['hr', 'HR — payroll postings'], ['operations', 'Operations — purchase orders']],
    crm: [['finance', 'Finance — invoicing & LTV'], ['support', 'Support — account health'], ['marketing', 'Marketing — campaign attribution'], ['projects', 'Projects — kickoff scaffolding']],
    core: [['hr', 'HR — one employee record'], ['finance', 'Finance — one ledger'], ['crm', 'CRM — one contact base']],
}
</script>

<template>
    <Head :title="domain.name" />

    <!-- Hero -->
    <section class="bg-bloom border-b border-line">
        <div class="mx-auto max-w-6xl px-6 pt-14 pb-14 md:pt-[84px] md:pb-[76px]">
            <nav class="flex items-center gap-2 text-[13.5px] font-medium text-ink-faint" aria-label="Breadcrumb">
                <Link href="/features" class="transition ease-out duration-150 hover:text-ink">Product</Link>
                <span>/</span>
                <span class="text-ink-soft">{{ domain.name }}</span>
            </nav>
            <h1 class="mt-[22px] flex items-center gap-3 font-display text-[40px] font-bold leading-[1.02] tracking-[-0.03em] md:gap-[18px] md:text-[62px]">
                <span class="h-[18px] w-[18px] shrink-0 rounded-[5px]" :style="{ background: color }" />
                {{ domain.name }}
            </h1>
            <p class="mt-[22px] max-w-[500px] text-base leading-[1.65] text-ink-soft md:text-lg">{{ domain.description }}</p>
            <div class="mt-[34px] flex flex-col items-stretch gap-3.5 sm:flex-row sm:items-center sm:flex-wrap">
                <Link :href="`/pricing?domain=${domain.key}`"
                    class="inline-flex items-center justify-center rounded-xl bg-accent px-8 py-4 text-base font-semibold text-white shadow-[0_1px_2px_rgba(79,70,229,0.4),0_8px_20px_-10px_rgba(79,70,229,0.5)] transition ease-out duration-150 hover:bg-accent-deep active:scale-[0.98]">
                    Price these modules
                </Link>
                <Link href="/features" class="group inline-flex items-center justify-center gap-2 text-[15px] font-semibold text-ink">
                    See all departments
                    <span class="text-accent transition-transform ease-out duration-150 group-hover:translate-x-1">→</span>
                </Link>
            </div>
        </div>
    </section>

    <!-- 01 / Modules -->
    <section class="border-b border-line bg-card">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[104px]">
            <Reveal>
                <SectionTag num="01" label="MODULES" />
                <h2 class="mt-4 max-w-[640px] font-display text-3xl font-bold leading-[1.06] tracking-display md:text-[42px]">
                    What's in {{ domain.name }}.
                </h2>
            </Reveal>
            <Reveal :delay="100">
                <div class="mt-12 grid gap-3.5 sm:grid-cols-2 lg:grid-cols-3">
                    <ModuleTile v-for="(m, i) in modules" :key="m.key" :name="m.name" :color="color"
                        :price="m.price_cents === 0 ? 'included' : `${euro(m.price_cents)}/user/month`"
                        :on="m.price_cents === 0 || i < 2" />
                </div>
            </Reveal>
        </div>
    </section>

    <!-- 02 / Flow band -->
    <FlowBand v-if="bandFlows.length" tag="02" :title="`${domain.name} tells the rest of the company itself.`"
        lede="No exports, no Zapier. These happen because everything shares one database." :flows="bandFlows" />

    <!-- 03 / Plays well with -->
    <section class="border-b border-line">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[104px]">
            <Reveal>
                <SectionTag :num="bandFlows.length ? '03' : '02'" label="PLAYS WELL WITH" />
                <h2 class="mt-4 max-w-[640px] font-display text-3xl font-bold leading-[1.06] tracking-display md:text-[42px]">
                    Strongest alongside.
                </h2>
                <div class="mt-9 flex flex-wrap gap-2.5">
                    <DomainPill v-for="[key, label] in playsWith[domain.key] ?? []" :key="key" :color="domainColors[key]">
                        {{ label }}
                    </DomainPill>
                </div>
            </Reveal>
        </div>
    </section>

    <CtaBand :title="`Start with ${domain.name}. Grow from there.`"
        sub="Switch on a module or two and you're running — the rest stays one flip away."
        :href="`/pricing?domain=${domain.key}`" />
</template>
