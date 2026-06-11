<script setup lang="ts">
defineProps<{ index?: string; eyebrow: string; title: string }>()
</script>

<template>
    <div class="max-w-2xl">
        <div class="flex items-center gap-3">
            <span v-if="index" class="section-index">{{ index }}</span>
            <span class="h-px w-8 bg-line"></span>
            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-ink-faint">{{ eyebrow }}</span>
        </div>
        <h2 class="mt-4 text-3xl sm:text-4xl font-bold tracking-display text-ink text-balance">{{ title }}</h2>
        <slot />
    </div>
</template>
