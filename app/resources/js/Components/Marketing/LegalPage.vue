<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue'

const props = defineProps<{
    title: string
    updated: string
    shortVersion: string
    sections: Array<[string, string]>
}>()

const active = ref(0)
let observer: IntersectionObserver | null = null

const slug = (t: string): string => 'sec-' + t.toLowerCase().replace(/[^a-z0-9]+/g, '-')

onMounted(() => {
    observer = new IntersectionObserver(
        (entries) => {
            for (const entry of entries) {
                if (entry.isIntersecting) {
                    const i = props.sections.findIndex(([t]) => slug(t) === entry.target.id)
                    if (i >= 0) active.value = i
                }
            }
        },
        { rootMargin: '-30% 0px -60% 0px' },
    )
    for (const [t] of props.sections) {
        const el = document.getElementById(slug(t))
        if (el) observer.observe(el)
    }
})

onBeforeUnmount(() => observer?.disconnect())
</script>

<template>
    <section class="ff-section" style="background: var(--card); padding: 84px 0 104px">
        <div class="wrap">
            <div class="grid items-start gap-11 lg:gap-[72px] lg:[grid-template-columns:260px_1fr]">
                <div class="hidden lg:block lg:sticky lg:top-28">
                    <span class="ff-kicker"><span class="sq"></span>Legal</span>
                    <nav class="mt-7 flex flex-col gap-0.5" aria-label="Sections">
                        <a
                            v-for="([t], i) in sections"
                            :key="t"
                            :href="'#' + slug(t)"
                            class="whitespace-nowrap px-3 py-[7px] text-[13.5px] transition-colors"
                            :style="{
                                fontWeight: i === active ? 600 : 500,
                                color: i === active ? 'var(--indigo)' : 'var(--ink-faint)',
                                borderLeft: '2px solid ' + (i === active ? 'var(--indigo)' : 'var(--line)'),
                            }"
                        >{{ t }}</a>
                    </nav>
                </div>
                <div style="max-width: 640px">
                    <h1 style="font-size: 44px">{{ title }}</h1>
                    <p class="mono mt-3 text-[12px]" style="color: var(--ink-faint)">Last updated · {{ updated }} · plain-language summary first, always</p>
                    <div class="mt-5.5 rounded-xl px-5.5 py-4.5 text-[14.5px] leading-[1.65]" style="background: var(--indigo-soft); border: 1px solid rgba(79,70,229,0.25); color: var(--ink-soft)">
                        <b style="color: var(--ink)">The short version:</b> {{ shortVersion }}
                    </div>
                    <div v-for="[t, body] in sections" :id="slug(t)" :key="t" class="mt-10 scroll-mt-28">
                        <h3 class="text-[19px]">{{ t }}</h3>
                        <p class="mt-2.5 text-[15px] leading-[1.75]" style="color: var(--ink-soft)">{{ body }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
