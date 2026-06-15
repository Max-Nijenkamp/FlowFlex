<script setup lang="ts">
withDefaults(defineProps<{ color: string; dashed?: boolean }>(), { dashed: false })
</script>

<template>
    <span class="group inline-flex items-center gap-2 whitespace-nowrap rounded-full px-4 py-2 text-[13.5px] font-semibold transition duration-200 ease-out hover:-translate-y-0.5 hover:border-ink-faint/60 hover:shadow-[0_8px_18px_-12px_rgba(17,24,39,0.3)]"
        :class="dashed ? 'border border-dashed border-line-strong bg-transparent hover:bg-card' : 'border border-line-strong bg-card'">
        <span class="h-[9px] w-[9px] rounded-[3px] transition-transform duration-200 ease-out group-hover:scale-125" :style="{ background: color }" />
        <slot />
    </span>
</template>
