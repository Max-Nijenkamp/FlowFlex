<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'

defineOptions({ layout: MarketingLayout })

defineProps<{ content: string }>()
</script>

<template>
    <section class="mx-auto max-w-3xl px-6 py-20">
        <h1 class="text-4xl font-bold">Privacy of service</h1>
        <div class="prose prose-slate mt-8" v-text="content"></div>
    </section>
</template>
