<script setup lang="ts">
withDefaults(defineProps<{ color: string; dashed?: boolean }>(), { dashed: false })
</script>

<template>
    <span class="inline-flex items-center gap-2 whitespace-nowrap rounded-full px-4 py-2 text-[13.5px] font-semibold"
        :class="dashed ? 'border border-dashed border-line-strong bg-transparent' : 'border border-line-strong bg-card'">
        <span class="h-[9px] w-[9px] rounded-[3px]" :style="{ background: color }" />
        <slot />
    </span>
</template>
