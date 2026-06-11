<script setup lang="ts">
const model = defineModel<boolean>({ required: true })

defineProps<{ label: string }>()
</script>

<template>
    <label class="group flex cursor-pointer items-center gap-2.5 text-sm text-ink-soft select-none">
        <input v-model="model" type="checkbox"
            class="form-checkbox h-[18px] w-[18px] rounded-md border-line text-accent shadow-sm transition ease-out duration-150 focus:ring-accent/25 focus:ring-offset-0 group-hover:border-ink-faint/60" />
        {{ label }}
    </label>
</template>
