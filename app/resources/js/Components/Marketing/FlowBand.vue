<script setup lang="ts">
import SectionTag from '@/Components/Marketing/SectionTag.vue'
import { flows as defaultFlows, type MarketingFlow } from '@/data/marketing'
import { computed } from 'vue'

const props = withDefaults(defineProps<{
    tag?: string
    title?: string
    lede?: string
    flows?: MarketingFlow[]
}>(), {
    tag: '03',
    title: 'Data moves between departments on its own.',
    lede: "These aren't integrations you configure. They're how a single database behaves.",
})

const list = computed(() => props.flows ?? defaultFlows)
// Domain pages pass event/effect only — drop the route column entirely then.
const hasRoutes = computed(() => list.value.some((f) => f.from && f.to))
</script>

<template>
    <section class="relative overflow-hidden border-b border-line bg-flow-bg text-[#F4F5F7]">
        <div class="pointer-events-none absolute -top-[300px] left-1/2 h-[600px] w-[1000px] -translate-x-1/2"
            style="background: radial-gradient(ellipse 50% 50% at 50% 50%, rgba(79, 70, 229, 0.25), rgba(56, 189, 248, 0.05) 55%, transparent 75%)" />
        <div class="relative mx-auto max-w-6xl px-6 py-[68px] md:py-[104px]">
            <SectionTag :num="tag" label="FLOW" dark />
            <h2 class="mt-4 max-w-[640px] font-display text-3xl font-bold leading-[1.06] tracking-display text-white md:text-[42px]">
                {{ title }}
            </h2>
            <p class="mt-[22px] max-w-[500px] text-[16.5px] leading-[1.65] text-[#F4F5F7]/60">{{ lede }}</p>
            <div class="relative mt-14">
                <span class="absolute bottom-[26px] top-[26px] left-[13px] w-px"
                    :class="hasRoutes ? 'md:left-[229px]' : 'md:left-[29px]'"
                    style="background: linear-gradient(180deg, transparent, rgba(109, 106, 246, 0.55) 12%, rgba(56, 189, 248, 0.55) 88%, transparent)" />
                <div v-for="(f, i) in list" :key="f.event"
                    class="grid grid-cols-[28px_1fr] items-center py-[13px] md:py-4"
                    :class="hasRoutes ? 'md:grid-cols-[200px_60px_1fr]' : 'md:grid-cols-[60px_1fr]'">
                    <span v-if="hasRoutes"
                        class="hidden whitespace-nowrap text-right font-mono text-[11px] uppercase tracking-[0.12em] text-flow md:block">
                        {{ f.from }} → {{ f.to }}
                    </span>
                    <span class="flex justify-center">
                        <span class="h-[11px] w-[11px] rounded-full border-2 bg-flow-bg" :class="i % 2
                            ? 'border-flow shadow-[0_0_14px_rgba(56,189,248,0.8)]'
                            : 'border-[#6D6AF6] shadow-[0_0_14px_rgba(109,106,246,0.8)]'" />
                    </span>
                    <span>
                        <span class="block font-display text-[17px] font-bold">{{ f.event }}</span>
                        <span class="mt-[3px] block text-[14.5px] text-[#F4F5F7]/62">{{ f.effect }}</span>
                    </span>
                </div>
            </div>
        </div>
    </section>
</template>
