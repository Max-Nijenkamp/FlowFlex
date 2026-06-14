<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

withDefaults(defineProps<{ title: string; sub?: string; cta?: string; href?: string }>(), {
    cta: 'Build your price',
    href: '/pricing',
})
</script>

<template>
    <section class="relative overflow-hidden bg-accent py-24 text-center text-white">
        <div class="bg-bloom-accent absolute inset-0" />
        <div class="relative mx-auto max-w-6xl px-6">
            <h2 class="font-display text-[32px] font-bold tracking-[-0.03em] md:text-[50px]">{{ title }}</h2>
            <p v-if="sub" class="mx-auto mt-[18px] max-w-[440px] text-[16.5px] leading-relaxed text-white/78">{{ sub }}</p>
            <Link :href="href"
                class="mt-[34px] inline-flex items-center justify-center gap-2 rounded-xl bg-white px-8 py-4 text-base font-semibold text-accent transition ease-out duration-150 hover:bg-white/90 active:scale-[0.98]">
                {{ cta }}
            </Link>
        </div>
    </section>
</template>
