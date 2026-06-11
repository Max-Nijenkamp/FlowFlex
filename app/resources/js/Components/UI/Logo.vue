<script setup lang="ts">
withDefaults(defineProps<{ variant?: 'dark' | 'light'; iconOnly?: boolean }>(), {
    variant: 'dark',
    iconOnly: false,
})
</script>

<template>
    <img v-if="iconOnly" src="/images/logo/flowflex-icon.svg" alt="FlowFlex" class="h-7 w-7" />
    <img v-else :src="`/images/logo/flowflex-logo-${variant}.svg`" alt="FlowFlex" class="h-9 w-auto min-w-[120px]" />
</template>
