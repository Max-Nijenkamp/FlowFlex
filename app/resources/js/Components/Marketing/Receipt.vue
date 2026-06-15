<script setup lang="ts">
defineProps<{ title: string }>()
</script>

<template>
    <div
        class="receipt-edge rounded-t border border-line-strong bg-card px-7 pb-[34px] pt-7 font-mono text-[13px] shadow-[0_20px_40px_-20px_rgba(17,24,39,0.25)] transition duration-300 ease-out hover:-translate-y-1 hover:shadow-[0_28px_56px_-24px_rgba(17,24,39,0.35)]">
        <div class="text-center text-[11px] font-bold tracking-[0.2em] text-ink-faint">{{ title }}</div>
        <slot />
    </div>
</template>
