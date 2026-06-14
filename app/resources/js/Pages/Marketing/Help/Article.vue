<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import Kicker from '@/Components/Marketing/Kicker.vue'
import { Head, Link } from '@inertiajs/vue3'

defineOptions({ layout: MarketingLayout })

defineProps<{
    article: { slug: string; category: string; title: string; summary: string; body: string[] }
}>()
</script>

<template>
    <Head :title="article.title">
        <meta name="description" :content="article.summary" />
    </Head>

    <section class="bg-card min-h-[60vh]">
        <div class="mx-auto max-w-3xl px-6 pt-14 pb-20 md:pt-[84px] md:pb-[104px]">
            <nav class="flex items-center gap-2 text-[13.5px] font-medium text-ink-faint" aria-label="Breadcrumb">
                <Link href="/help" class="transition ease-out duration-150 hover:text-ink">Help center</Link>
                <span>/</span>
                <span class="text-ink-soft">{{ article.category }}</span>
            </nav>
            <h1 class="mt-6 font-display text-[32px] font-bold leading-[1.1] tracking-display md:text-[40px]">
                {{ article.title }}
            </h1>
            <p class="mt-3 text-[15.5px] text-ink-soft">{{ article.summary }}</p>

            <div class="mt-9 space-y-5 border-t border-line pt-8 text-[15.5px] leading-[1.75] text-ink-soft">
                <p v-for="p in article.body" :key="p.slice(0, 40)">{{ p }}</p>
            </div>

            <div class="mt-12 rounded-[14px] border border-line-strong bg-paper px-6 py-5">
                <p class="text-sm font-semibold">Still stuck?</p>
                <p class="mt-1 text-sm leading-relaxed text-ink-soft">
                    <Link href="/contact" class="font-semibold text-accent hover:underline">Send us the question</Link>
                    — a human replies within one business day, and the answer usually ends up on this page.
                </p>
            </div>
        </div>
    </section>
</template>
