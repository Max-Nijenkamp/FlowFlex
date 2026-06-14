<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import BlueprintCell from '@/Components/Marketing/BlueprintCell.vue'
import CtaBand from '@/Components/Marketing/CtaBand.vue'
import DomainPill from '@/Components/Marketing/DomainPill.vue'
import Kicker from '@/Components/Marketing/Kicker.vue'
import SectionTag from '@/Components/Marketing/SectionTag.vue'
import Reveal from '@/Components/UI/Reveal.vue'
import { Head, Link } from '@inertiajs/vue3'

defineOptions({ layout: MarketingLayout })

defineProps<{
    study: {
        slug: string
        company: string
        industry: string
        size: string
        quote: string
        quotee: string
        summary: string
        stats: { big: string; title: string; body: string }[]
        modules: string[]
        story: string[]
    }
}>()
</script>

<template>
    <Head :title="`${study.company} — customer story`">
        <meta name="description" :content="study.summary" />
    </Head>

    <!-- Hero -->
    <section class="bg-bloom border-b border-line">
        <div class="mx-auto max-w-6xl px-6 pt-14 pb-14 md:pt-[84px] md:pb-[72px]">
            <Kicker>Customer story</Kicker>
            <h1 class="mt-[26px] font-display text-[40px] font-bold leading-[1.02] tracking-[-0.03em] md:text-[56px]">
                {{ study.company }}
            </h1>
            <p class="mt-4 font-mono text-xs text-ink-faint">{{ study.industry }} · {{ study.size }}</p>
            <blockquote class="mt-9 max-w-[760px] font-display text-[24px] font-bold leading-[1.3] tracking-display text-ink md:text-[30px]">
                “{{ study.quote }}”
            </blockquote>
            <p class="mt-4 text-[14.5px] font-medium text-ink-soft">— {{ study.quotee }}</p>
        </div>
    </section>

    <!-- 01 / The numbers -->
    <section class="border-b border-line bg-card">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[104px]">
            <Reveal>
                <SectionTag num="01" label="THE NUMBERS" />
                <h2 class="mt-4 max-w-[640px] font-display text-3xl font-bold leading-[1.06] tracking-display md:text-[42px]">
                    What changed, counted.
                </h2>
            </Reveal>
            <Reveal :delay="100">
                <div class="mt-[52px] grid gap-px border border-line-strong bg-line-strong md:grid-cols-3">
                    <BlueprintCell v-for="s in study.stats" :key="s.title" :big="s.big" :title="s.title" :body="s.body" />
                </div>
            </Reveal>
        </div>
    </section>

    <!-- 02 / The story -->
    <section class="border-b border-line">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[104px]">
            <Reveal>
                <SectionTag num="02" label="THE STORY" />
                <div class="mt-8 max-w-[660px] space-y-5 text-[16.5px] leading-[1.7] text-ink-soft">
                    <p v-for="p in study.story" :key="p.slice(0, 40)">{{ p }}</p>
                </div>
            </Reveal>
        </div>
    </section>

    <!-- 03 / Their board -->
    <section class="bg-bloom border-b border-line">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[88px]">
            <Reveal>
                <SectionTag num="03" label="THEIR BOARD" />
                <h2 class="mt-4 max-w-[640px] font-display text-3xl font-bold leading-[1.06] tracking-display md:text-[42px]">
                    Switched on at {{ study.company }}.
                </h2>
                <div class="mt-9 flex flex-wrap gap-2.5">
                    <DomainPill v-for="m in study.modules" :key="m" color="#4F46E5">{{ m }}</DomainPill>
                </div>
                <p class="mt-8 text-[15px] text-ink-soft">
                    Thinking about the same move?
                    <Link href="/switch-over" class="font-semibold text-accent hover:underline">How switching over works →</Link>
                </p>
            </Reveal>
        </div>
    </section>

    <CtaBand title="Your stack, mapped to modules." sub="Tell us what you run today and get the before/after invoice — usually the same day."
        cta="Map my stack" href="/contact" />
</template>
