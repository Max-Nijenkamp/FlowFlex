<script setup lang="ts">
// Switchboard+ buttons — primary = accent + glow, dark = ink, secondary = white outline.
withDefaults(defineProps<{
    variant?: 'primary' | 'dark' | 'secondary' | 'ghost'
    size?: 'sm' | 'md' | 'lg'
    loading?: boolean
}>(), { variant: 'primary', size: 'md', loading: false })
</script>

<template>
    <button v-bind="$attrs" :disabled="loading || ($attrs.disabled as boolean | undefined)"
        class="inline-flex items-center justify-center gap-2 whitespace-nowrap font-semibold transition ease-out duration-150 focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-accent/25 active:scale-[0.98] disabled:opacity-60 disabled:pointer-events-none"
        :class="[
            variant === 'primary' && 'bg-accent text-white hover:bg-accent-deep shadow-[0_1px_2px_rgba(79,70,229,0.4),0_8px_20px_-10px_rgba(79,70,229,0.5)]',
            variant === 'dark' && 'bg-ink text-white hover:bg-accent',
            variant === 'secondary' && 'border border-line-strong bg-card text-ink shadow-[0_1px_2px_rgba(17,24,39,0.04)] hover:border-ink-faint hover:bg-paper',
            variant === 'ghost' && 'text-ink-soft hover:text-ink',
            size === 'sm' && 'rounded-lg px-4 py-2 text-[13.5px]',
            size === 'md' && 'rounded-[10px] px-6 py-3 text-[15px]',
            size === 'lg' && 'rounded-xl px-8 py-4 text-base',
        ]">
        <svg v-if="loading" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25" />
            <path d="M22 12a10 10 0 00-10-10" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
        </svg>
        <slot />
    </button>
</template>
