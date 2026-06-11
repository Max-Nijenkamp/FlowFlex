<script setup lang="ts">
const open = defineModel<boolean>('open', { default: false })
</script>

<template>
    <div class="overflow-hidden rounded-2xl border border-line bg-white transition ease-out duration-200"
        :class="open ? 'shadow-[0_2px_8px_rgba(17,24,39,0.05)]' : ''">
        <button type="button" class="flex w-full items-center justify-between gap-4 px-6 py-5 text-left"
            :aria-expanded="open" @click="open = !open">
            <slot name="header" />
            <svg class="h-4 w-4 shrink-0 text-ink-faint transition-transform ease-out duration-200"
                :class="open ? 'rotate-180' : ''" viewBox="0 0 16 16" fill="none">
                <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
        <div v-show="open" class="border-t border-line px-6 py-5">
            <slot />
        </div>
    </div>
</template>
