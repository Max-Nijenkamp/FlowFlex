<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import Kicker from '@/Components/Marketing/Kicker.vue'
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'

defineOptions({ layout: MarketingLayout })

const props = defineProps<{
    checks: { label: string; ok: boolean; message: string }[]
    checked_at: string
}>()

const allOk = computed(() => props.checks.every((c) => c.ok))
const checkedAt = computed(() => new Date(props.checked_at).toLocaleString('nl-NL'))
</script>

<template>
    <Head title="System status">
        <meta name="description" content="Live FlowFlex platform status — the same health checks our own alerting runs on." />
    </Head>

    <section class="bg-bloom min-h-[60vh]">
        <div class="mx-auto max-w-3xl px-6 pt-14 pb-20 md:pt-[84px] md:pb-[104px]">
            <Kicker>Status</Kicker>
            <h1 class="mt-[26px] font-display text-[36px] font-bold leading-[1.05] tracking-[-0.03em] md:text-[48px]">
                System status
            </h1>

            <div class="mt-8 flex items-center gap-3 rounded-[14px] border px-5 py-4"
                :class="allOk ? 'border-[#10B981]/30 bg-[#E5F5EE]' : 'border-amber-300 bg-[#FDF1DC]'">
                <span class="h-2.5 w-2.5 rounded-full" :class="allOk ? 'bg-[#0E8C61]' : 'bg-[#B45309]'" />
                <p class="font-display text-base font-bold" :class="allOk ? 'text-[#0E8C61]' : 'text-[#B45309]'">
                    {{ allOk ? 'All systems operational' : 'Some systems are degraded' }}
                </p>
            </div>

            <div class="mt-6 overflow-hidden rounded-[14px] border border-line-strong bg-card shadow-[0_1px_2px_rgba(17,24,39,0.03)]">
                <div v-for="(c, i) in checks" :key="c.label"
                    class="flex items-center justify-between gap-4 border-b border-line px-5 py-3.5 last:border-b-0"
                    :class="i % 2 === 0 ? 'bg-[#FAF9F5]' : ''">
                    <span class="text-sm font-semibold">{{ c.label }}</span>
                    <span class="flex items-center gap-2 rounded-full px-3 py-1 text-[11.5px] font-semibold"
                        :class="c.ok ? 'bg-[#E5F5EE] text-[#0E8C61]' : 'bg-[#FDF1DC] text-[#B45309]'">
                        <span class="h-1.5 w-1.5 rounded-full bg-current" />
                        {{ c.message }}
                    </span>
                </div>
            </div>

            <p class="mt-5 font-mono text-[11px] text-ink-faint">
                checked {{ checkedAt }} · same checks our own alerting runs on · refreshes every minute
            </p>
            <p class="mt-8 text-[14.5px] text-ink-soft">
                Seeing something we're not?
                <Link href="/contact" class="font-semibold text-accent hover:underline">Tell us</Link> — a human reads it.
            </p>
        </div>
    </section>
</template>
