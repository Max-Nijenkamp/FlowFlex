<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import { Link } from '@inertiajs/vue3'

defineOptions({ layout: MarketingLayout })

defineProps<{ domains: { name: string; modules: number }[] }>()
</script>

<template>
    <section class="mx-auto max-w-6xl px-6 py-24 text-center">
        <h1 class="text-5xl font-bold tracking-tight">
            Run your whole company<br />
            <span class="text-sky-500">from one workspace</span>
        </h1>
        <p class="mx-auto mt-6 max-w-2xl text-lg text-slate-600">
            HR, finance, CRM and more — modular, pay only for what you use.
            Built for teams of 50 to 500.
        </p>
        <div class="mt-10 flex justify-center gap-4">
            <Link href="/pricing"
                class="rounded-xl bg-sky-500 px-6 py-3 font-semibold text-white hover:bg-sky-600 transition ease-out duration-150">
                See pricing
            </Link>
            <Link href="/features"
                class="rounded-xl border border-slate-200 px-6 py-3 font-semibold text-slate-700 hover:bg-slate-50 transition ease-out duration-150">
                Explore features
            </Link>
        </div>
    </section>

    <section class="bg-slate-50 py-20">
        <div class="mx-auto max-w-6xl px-6">
            <h2 class="text-center text-3xl font-bold">Every department. One platform.</h2>
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="(domain, i) in domains" :key="domain.name"
                    class="rounded-2xl bg-white p-6 shadow-sm border border-slate-100 transition ease-out duration-200 hover:shadow-md"
                    :style="{ animationDelay: `${i * 30}ms` }">
                    <h3 class="font-semibold">{{ domain.name }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ domain.modules }} modules</p>
                </div>
            </div>
        </div>
    </section>
</template>
