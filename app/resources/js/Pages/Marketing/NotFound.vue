<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import Switch from '@/Components/UI/Switch.vue'
import { Head, Link } from '@inertiajs/vue3'

defineOptions({ layout: MarketingLayout })
</script>

<template>
    <Head title="Page not found" />

    <section class="bg-bloom">
        <div class="mx-auto flex min-h-[64vh] max-w-3xl flex-col items-center justify-center px-6 py-24 text-center">
            <p class="font-mono text-[64px] font-bold leading-none tracking-[-0.05em] text-ink md:text-[88px]">404</p>
            <div class="mt-7 flex items-center gap-3 rounded-[14px] border border-line-strong bg-card px-5 py-3.5 shadow-[0_1px_2px_rgba(17,24,39,0.04)]">
                <span class="text-sm font-semibold">this page</span>
                <Switch :on="false" />
                <span class="rounded-[5px] bg-[#EFEDE7] px-2 py-[3px] font-mono text-[9.5px] tracking-[0.12em] text-ink-faint">OFF</span>
            </div>
            <h1 class="mt-8 font-display text-[28px] font-bold leading-[1.1] tracking-display md:text-[36px]">
                This page is switched off.
            </h1>
            <p class="mt-3 max-w-[420px] text-[15.5px] leading-[1.65] text-ink-soft">
                Either it moved, or it never existed. Your data is fine — it's just this URL that's empty.
            </p>
            <div class="mt-9 flex flex-col items-stretch gap-3.5 sm:flex-row sm:items-center">
                <Link href="/"
                    class="inline-flex items-center justify-center rounded-[10px] bg-accent px-6 py-3 text-[15px] font-semibold text-white shadow-[0_1px_2px_rgba(79,70,229,0.4),0_8px_20px_-10px_rgba(79,70,229,0.5)] transition ease-out duration-150 hover:bg-accent-deep active:scale-[0.98]">
                    Back to the homepage
                </Link>
                <Link href="/help"
                    class="inline-flex items-center justify-center rounded-[10px] border border-line-strong bg-card px-6 py-3 text-[15px] font-semibold text-ink shadow-[0_1px_2px_rgba(17,24,39,0.04)] transition ease-out duration-150 hover:border-ink-faint active:scale-[0.98]">
                    Help center
                </Link>
            </div>
        </div>
    </section>
</template>
