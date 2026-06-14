<script setup lang="ts">
const model = defineModel<string>({ required: true })

defineProps<{ options: { value: string; label: string }[]; placeholder?: string }>()
</script>

<template>
    <div class="relative">
        <select v-model="model" v-bind="$attrs"
            class="form-select block h-[46px] w-full appearance-none rounded-[10px] border border-line-strong bg-card px-3.5 pr-10 text-[15px] shadow-[0_1px_2px_rgba(17,24,39,0.03)] transition ease-out duration-150 hover:border-ink-faint/60 focus:border-accent focus:outline-none focus:ring-[3px] focus:ring-accent/15"
            :class="model === '' ? 'text-ink-faint' : 'text-ink'">
            <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
            <option v-for="option in options" :key="option.value" :value="option.value">{{ option.label }}</option>
        </select>
        <svg class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-faint" viewBox="0 0 16 16" fill="none">
            <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </div>
</template>
