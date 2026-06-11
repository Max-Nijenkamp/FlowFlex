<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import Reveal from '@/Components/UI/Reveal.vue'
import { Head, Link } from '@inertiajs/vue3'

defineOptions({ layout: MarketingLayout })

const values = [
    { name: 'Flexible by design', detail: 'Activate modules one at a time; pay only for what is active.' },
    { name: 'Flow-first UX', detail: 'Speed and clarity on every screen. Nothing blocks the actual job.' },
    { name: 'Unified truth', detail: 'One database, one login. Data flows between departments automatically.' },
    { name: 'Transparent and fair', detail: 'Clear pricing, GDPR-compliant, your data is portable — always.' },
    { name: 'Customer-first', detail: 'Built for department managers and employees, not the IT department.' },
]

const isNot = [
    { is: 'A modular platform you grow into', not: 'An ERP that imposes a process model' },
    { is: 'Built for 50 and for 500 people', not: 'Enterprise-only software' },
    { is: 'Focused — every addition serves Flow or Flex', not: 'A feature factory' },
]
</script>

<template>
    <Head title="About">
        <meta name="description" content="FlowFlex is the all-in-one workspace for growing companies. Built in Europe, GDPR-first, modular by design." />
    </Head>
    <section class="mx-auto max-w-6xl px-6 pt-20 pb-16">
        <p class="section-index">ABOUT</p>
        <h1 class="mt-4 max-w-3xl text-4xl sm:text-6xl font-bold tracking-display text-balance">
            Growing companies shouldn't need fifteen tools to run one business.
        </h1>
        <div class="mt-10 max-w-2xl space-y-5 text-lg text-ink-soft leading-relaxed">
            <p>
                FlowFlex started with that frustration. Somewhere between 40 and 80 employees,
                every company hits the same wall: the cost of switching, syncing and re-entering data
                across a patchwork of tools quietly outgrows the cost of the tools themselves.
            </p>
            <p>
                So we built the alternative — one platform where every operational tool shares one
                data model, one login and one bill. Modules switch on when you need them and off when
                you don't. Built in Europe, hosted in Europe, GDPR-compliant from the first line of code.
            </p>
        </div>
    </section>

    <section class="border-t border-line bg-white">
        <div class="mx-auto max-w-6xl px-6 py-20">
            <Reveal>
                <h2 class="text-2xl font-bold tracking-display">What we hold ourselves to</h2>
            </Reveal>
            <div class="mt-10 grid gap-px bg-line border border-line sm:grid-cols-2 lg:grid-cols-3">
                <Reveal v-for="(value, i) in values" :key="value.name" :delay="i * 60">
                    <div class="bg-white p-7 h-full">
                        <h3 class="font-semibold">{{ value.name }}</h3>
                        <p class="mt-2 text-sm text-ink-soft leading-relaxed">{{ value.detail }}</p>
                    </div>
                </Reveal>
            </div>
        </div>
    </section>

    <section class="border-t border-line">
        <div class="mx-auto max-w-6xl px-6 py-20">
            <Reveal>
                <h2 class="text-2xl font-bold tracking-display">What FlowFlex is — and isn't</h2>
            </Reveal>
            <div class="mt-10 divide-y divide-line border-y border-line">
                <div v-for="row in isNot" :key="row.is" class="grid gap-2 py-6 sm:grid-cols-2">
                    <p class="font-medium">{{ row.is }}</p>
                    <p class="text-ink-faint line-through decoration-ink-faint/40">{{ row.not }}</p>
                </div>
            </div>
            <div class="mt-12">
                <Link href="/contact" class="group font-semibold text-accent">
                    Talk to the team
                    <span class="inline-block transition-transform ease-out duration-150 group-hover:translate-x-1">→</span>
                </Link>
            </div>
        </div>
    </section>
</template>
