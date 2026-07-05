<script setup lang="ts">
withDefaults(defineProps<{ light?: boolean; size?: number }>(), { light: false, size: 25 })
</script>

<template>
    <span class="inline-flex items-center" :style="{ gap: '9px' }">
        <svg :width="size" :height="size" viewBox="0 0 48 48" fill="none" aria-hidden="true">
            <path
                d="M10 14 C10 9 14 6 19 6 L38 6 C41 6 42 8 42 10 C42 12 41 14 38 14 L20 14 C17 14 16 15 16 18 L16 30 C16 39 9 42 4 40"
                stroke="#4F46E5" stroke-width="6" stroke-linecap="round" fill="none"
            />
            <path
                d="M16 24 L31 24 C34 24 36 26 36 28.5 C36 31 34 33 31 33 L24 33 C20 33 18 35 18 38"
                stroke="#4F46E5" stroke-width="6" stroke-linecap="round" fill="none" opacity="0.45"
            />
        </svg>
        <span
            :style="{
                fontFamily: 'var(--font-display)',
                fontWeight: 700,
                fontSize: size * 0.72 + 'px',
                letterSpacing: '-0.02em',
                color: light ? '#FFFFFF' : '#111827',
            }"
        >FlowFlex</span>
    </span>
</template>
