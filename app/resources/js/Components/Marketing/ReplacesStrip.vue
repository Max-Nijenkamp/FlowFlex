<script setup lang="ts">
import { replaces } from '@/data/marketing'

const items = [...replaces, ...replaces]
</script>

<template>
    <div class="relative overflow-hidden border-b border-line bg-card py-4">
        <span
            class="absolute inset-y-0 left-0 z-[2] flex items-center bg-gradient-to-r from-white from-70% to-transparent pl-6 pr-[26px] font-mono text-[11px] tracking-[0.18em] text-ink-faint md:pl-14">
            REPLACES
        </span>
        <div class="animate-marquee flex w-max gap-10 pl-[230px]">
            <span v-for="(r, i) in items" :key="i"
                class="whitespace-nowrap text-sm font-medium text-ink-faint line-through decoration-accent/45">
                {{ r }}
            </span>
        </div>
    </div>
</template>
