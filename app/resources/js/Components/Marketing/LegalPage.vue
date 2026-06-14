<script setup lang="ts">
import Kicker from '@/Components/Marketing/Kicker.vue'
import { onMounted, onUnmounted, ref } from 'vue'

const props = defineProps<{
    title: string
    updated: string
    shortVersion: string
    sections: { id: string; heading: string; body: string }[]
}>()

const active = ref(props.sections[0]?.id ?? '')
let observer: IntersectionObserver | null = null

onMounted(() => {
    observer = new IntersectionObserver(
        (entries) => {
            for (const entry of entries) {
                if (entry.isIntersecting) active.value = entry.target.id
            }
        },
        { rootMargin: '-20% 0px -70% 0px' },
    )
    for (const s of props.sections) {
        const el = document.getElementById(s.id)
        if (el) observer.observe(el)
    }
})

onUnmounted(() => observer?.disconnect())
</script>

<template>
    <section class="bg-card">
        <div class="mx-auto max-w-6xl px-6 pt-14 pb-16 md:pt-[84px] md:pb-[104px]">
            <div class="grid items-start gap-12 lg:grid-cols-[260px_1fr] lg:gap-[72px]">
                <div class="lg:sticky lg:top-28">
                    <Kicker>Legal</Kicker>
                    <nav class="mt-7 hidden flex-col gap-0.5 lg:flex">
                        <a v-for="s in sections" :key="s.id" :href="`#${s.id}`"
                            class="whitespace-nowrap border-l-2 px-3 py-[7px] text-[13.5px] transition ease-out duration-150"
                            :class="active === s.id
                                ? 'border-accent font-semibold text-accent'
                                : 'border-line font-medium text-ink-faint hover:text-ink-soft'">
                            {{ s.heading }}
                        </a>
                    </nav>
                </div>
                <div class="max-w-[640px]">
                    <h1 class="font-display text-4xl font-bold tracking-display md:text-[44px]">{{ title }}</h1>
                    <p class="mt-3 font-mono text-xs text-ink-faint">
                        Last updated · {{ updated }} · plain-language summary first, always
                    </p>
                    <div class="mt-[22px] rounded-xl border border-accent/25 bg-accent-soft px-[22px] py-[18px] text-[14.5px] leading-[1.65] text-ink-soft">
                        <b class="text-ink">The short version:</b> {{ shortVersion }}
                    </div>
                    <div v-for="s in sections" :id="s.id" :key="s.id" class="mt-10 scroll-mt-28">
                        <h3 class="font-display text-[19px] font-bold tracking-display">{{ s.heading }}</h3>
                        <p class="mt-2.5 text-[15px] leading-[1.75] text-ink-soft">{{ s.body }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
