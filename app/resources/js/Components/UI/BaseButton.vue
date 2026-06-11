<script setup lang="ts">
withDefaults(defineProps<{
    variant?: 'primary' | 'secondary' | 'ghost'
    size?: 'md' | 'lg'
    loading?: boolean
}>(), { variant: 'primary', size: 'md', loading: false })
</script>

<template>
    <button v-bind="$attrs" :disabled="loading || ($attrs.disabled as boolean | undefined)"
        class="inline-flex items-center justify-center gap-2 rounded-full font-semibold transition ease-out duration-150 focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-accent/25 active:scale-[0.98] disabled:opacity-60 disabled:pointer-events-none"
        :class="[
            variant === 'primary' && 'bg-ink text-white hover:bg-accent shadow-[0_1px_2px_rgba(17,24,39,0.15)]',
            variant === 'secondary' && 'border border-line bg-white text-ink hover:border-ink-faint hover:bg-paper',
            variant === 'ghost' && 'text-ink-soft hover:text-ink',
            size === 'md' && 'px-6 py-2.5 text-sm',
            size === 'lg' && 'px-7 py-3.5 text-[15px]',
        ]">
        <svg v-if="loading" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25" />
            <path d="M22 12a10 10 0 00-10-10" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
        </svg>
        <slot />
    </button>
</template>
