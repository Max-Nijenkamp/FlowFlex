<script setup lang="ts">
withDefaults(defineProps<{ num: string; label: string; dark?: boolean }>(), { dark: false })
</script>

<template>
    <p class="font-mono text-[11.5px] uppercase tracking-[0.2em]" :class="dark ? 'text-white/40' : 'text-ink-faint'">
        <b class="font-bold" :class="dark ? 'text-flow' : 'text-accent'">{{ num }}</b> / {{ label }}
    </p>
</template>
