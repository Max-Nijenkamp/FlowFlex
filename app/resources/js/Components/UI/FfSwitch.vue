<script setup lang="ts">
withDefaults(defineProps<{ on?: boolean; sm?: boolean }>(), { on: false, sm: false })
</script>

<template>
    <span class="ff-sw" :class="{ on, sm }" aria-hidden="true"></span>
</template>
