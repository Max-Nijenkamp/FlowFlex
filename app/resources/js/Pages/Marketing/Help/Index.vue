<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import CtaBand from '@/Components/Marketing/CtaBand.vue'
import Kicker from '@/Components/Marketing/Kicker.vue'
import { Head, Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

defineOptions({ layout: MarketingLayout })

const props = defineProps<{
    articles: { slug: string; category: string; title: string; summary: string }[]
}>()

const query = ref('')

const filtered = computed(() => {
    const q = query.value.trim().toLowerCase()
    if (!q) return props.articles
    return props.articles.filter((a) => `${a.title} ${a.summary} ${a.category}`.toLowerCase().includes(q))
})

const byCategory = computed(() => {
    const groups: Record<string, typeof props.articles> = {}
    for (const a of filtered.value) (groups[a.category] ??= []).push(a)
    return groups
})
</script>

<template>
    <Head title="Help center">
        <meta name="description" content="Short, human answers about modules, billing, security and your data." />
    </Head>

    <!-- Hero + search -->
    <section class="bg-bloom border-b border-line">
        <div class="mx-auto max-w-6xl px-6 pt-14 pb-12 md:pt-[84px] md:pb-[64px]">
            <Kicker>Help center</Kicker>
            <h1 class="mt-[26px] max-w-[680px] font-display text-[40px] font-bold leading-[1.02] tracking-[-0.03em] md:text-[56px]">
                Short answers, written by humans.
            </h1>
            <div class="mt-8 max-w-[480px]">
                <input v-model="query" type="search" placeholder="Search the help center…"
                    class="w-full rounded-[10px] border border-line-strong bg-card px-4 py-3 text-[15px] shadow-[0_1px_2px_rgba(17,24,39,0.03)] placeholder:text-ink-faint focus:border-accent focus:outline-none focus:ring-[3px] focus:ring-accent/15" />
            </div>
        </div>
    </section>

    <!-- Articles by category -->
    <section class="border-b border-line bg-card">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[88px]">
            <div v-if="Object.keys(byCategory).length" class="space-y-12">
                <div v-for="(items, category) in byCategory" :key="category">
                    <p class="font-mono text-[11.5px] uppercase tracking-[0.18em] text-ink-faint">{{ category }}</p>
                    <div class="mt-4 grid gap-3.5 md:grid-cols-2">
                        <Link v-for="a in items" :key="a.slug" :href="`/help/${a.slug}`"
                            class="group rounded-[14px] border border-line-strong bg-card p-5 shadow-[0_1px_2px_rgba(17,24,39,0.03)] transition ease-out duration-150 hover:border-accent/45">
                            <h2 class="font-display text-base font-bold tracking-display group-hover:text-accent">{{ a.title }}</h2>
                            <p class="mt-1.5 text-sm leading-relaxed text-ink-soft">{{ a.summary }}</p>
                        </Link>
                    </div>
                </div>
            </div>
            <div v-else class="py-16 text-center">
                <p class="font-display text-lg font-bold">Nothing matches “{{ query }}”</p>
                <p class="mt-2 text-sm text-ink-soft">
                    Try another word, or
                    <Link href="/contact" class="font-semibold text-accent hover:underline">ask us directly</Link>
                    — we reply within one business day.
                </p>
            </div>
        </div>
    </section>

    <CtaBand title="Didn't find it?" sub="No bots between you and an answer — a human reads every message." cta="Contact us"
        href="/contact" />
</template>
