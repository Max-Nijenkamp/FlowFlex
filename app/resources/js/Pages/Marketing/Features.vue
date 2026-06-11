<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'

defineOptions({ layout: MarketingLayout })

defineProps<{ domains: { name: string; description: string; modules: string[] }[] }>()
</script>

<template>
    <section class="mx-auto max-w-6xl px-6 py-20">
        <h1 class="text-center text-4xl font-bold">Everything your team needs</h1>
        <div class="mt-14 space-y-10">
            <div v-for="domain in domains" :key="domain.name"
                class="rounded-2xl border border-slate-100 p-8 shadow-sm">
                <h2 class="text-2xl font-semibold">{{ domain.name }}</h2>
                <p class="mt-2 text-slate-600">{{ domain.description }}</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <span v-for="m in domain.modules" :key="m"
                        class="rounded-full bg-sky-50 px-3 py-1 text-sm text-sky-700">{{ m }}</span>
                </div>
            </div>
        </div>
    </section>
</template>
