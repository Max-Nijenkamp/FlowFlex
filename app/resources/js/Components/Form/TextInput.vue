<script setup lang="ts">
const model = defineModel<string>({ required: true })

withDefaults(defineProps<{ invalid?: boolean }>(), { invalid: false })
</script>

<template>
    <input
        v-model="model"
        v-bind="$attrs"
        class="form-input block h-11 w-full rounded-xl border bg-white px-3.5 text-[15px] text-ink shadow-[inset_0_1px_2px_rgba(17,24,39,0.03)] placeholder:text-ink-faint transition ease-out duration-150 focus:outline-none focus:ring-[3px]"
        :class="invalid
            ? 'border-red-400 focus:border-red-500 focus:ring-red-100'
            : 'border-line hover:border-ink-faint/60 focus:border-accent focus:ring-accent/15'"
    />
</template>
