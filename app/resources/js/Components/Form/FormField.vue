<script setup lang="ts">
defineProps<{ label: string; for?: string; error?: string; hint?: string; optional?: boolean }>()
</script>

<template>
    <div>
        <div class="flex items-baseline justify-between">
            <label :for="$props.for" class="text-[13px] font-semibold text-ink">
                {{ label }}
                <span v-if="optional" class="ml-1 font-normal text-ink-faint">optional</span>
            </label>
            <slot name="corner" />
        </div>
        <div class="mt-1.5">
            <slot />
        </div>
        <p v-if="error" class="mt-1.5 flex items-center gap-1.5 text-[13px] text-red-600">
            <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 16 16" fill="currentColor">
                <path d="M8 1a7 7 0 100 14A7 7 0 008 1zm0 3.5c.4 0 .75.34.72.75l-.18 3.5a.54.54 0 01-1.08 0l-.18-3.5A.72.72 0 018 4.5zM8 11.5A.75.75 0 118 10a.75.75 0 010 1.5z" />
            </svg>
            {{ error }}
        </p>
        <p v-else-if="hint" class="mt-1.5 text-[13px] text-ink-faint">{{ hint }}</p>
    </div>
</template>
