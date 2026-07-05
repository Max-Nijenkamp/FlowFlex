<script setup lang="ts">
import { replaces } from '../../data/marketing'

const items = [...replaces, ...replaces]
</script>

<template>
    <div class="ff-strip" aria-hidden="true">
        <span class="label">REPLACES</span>
        <div class="ff-strip-track">
            <span v-for="(r, i) in items" :key="i">{{ r }}</span>
        </div>
    </div>
</template>
