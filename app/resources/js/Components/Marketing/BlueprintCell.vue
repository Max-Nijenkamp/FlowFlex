<script setup lang="ts">
// One white cell inside a 1px-gap grid over line-strong (the parent supplies
// `grid gap-px border border-line-strong bg-line-strong`).
withDefaults(defineProps<{ big?: string; title?: string; body?: string; corner?: boolean }>(), { corner: true })
</script>

<template>
    <div class="group relative bg-card px-7 py-8 transition duration-200 ease-out hover:bg-accent-soft/30">
        <span v-if="corner" class="absolute -left-px -top-px h-3.5 w-3.5 border-l-2 border-t-2 border-accent transition-all duration-200 ease-out group-hover:h-5 group-hover:w-5" />
        <div v-if="big" class="font-mono text-[44px] font-bold tracking-[-0.05em]">{{ big }}</div>
        <h3 v-if="title" class="font-display text-base font-bold tracking-display" :class="big ? 'mt-3' : ''">{{ title }}</h3>
        <p v-if="body" class="mt-2 text-sm leading-relaxed text-ink-soft">{{ body }}</p>
        <slot />
    </div>
</template>
