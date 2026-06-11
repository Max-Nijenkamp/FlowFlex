<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import Reveal from '@/Components/UI/Reveal.vue'
import SectionHeading from '@/Components/UI/SectionHeading.vue'
import { Head, Link } from '@inertiajs/vue3'

defineOptions({ layout: MarketingLayout })

defineProps<{
    domains: {
        key: string
        name: string
        description: string
        modules: string[]
        flows: string[]
    }[]
}>()
</script>

<template>
    <Head title="Product">
        <meta name="description" content="Every FlowFlex module that ships today — HR, finance, CRM and the core platform. Each one is a switch, not a sales call." />
    </Head>
    <section class="mx-auto max-w-6xl px-6 pt-20 pb-12">
        <p class="section-index">PRODUCT</p>
        <h1 class="mt-4 max-w-2xl text-4xl sm:text-6xl font-bold tracking-display text-balance">
            Four departments. One source of truth.
        </h1>
        <p class="mt-6 max-w-xl text-lg text-ink-soft leading-relaxed">
            Every module below ships today. Each one is a switch on your billing page — not a sales call.
        </p>
    </section>

    <section v-for="(domain, i) in domains" :key="domain.name"
        class="border-t border-line" :class="i % 2 === 1 ? 'bg-white' : ''">
        <div class="mx-auto max-w-6xl px-6 py-20">
            <div class="grid gap-12 lg:grid-cols-2">
                <Reveal>
                    <SectionHeading :index="String(i + 1).padStart(2, '0')" eyebrow="Domain" :title="domain.name">
                        <p class="mt-5 text-ink-soft leading-relaxed">{{ domain.description }}</p>
                        <Link :href="`/product/${domain.key}`" class="group mt-6 inline-block font-semibold text-accent">
                            Explore {{ domain.name }}
                            <span class="inline-block transition-transform ease-out duration-150 group-hover:translate-x-1">→</span>
                        </Link>
                        <div v-if="domain.flows.length" class="mt-7 space-y-2.5">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-ink-faint">Flows automatically</p>
                            <p v-for="flow in domain.flows" :key="flow" class="flex gap-2.5 text-sm text-ink-soft">
                                <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-accent"></span>
                                {{ flow }}
                            </p>
                        </div>
                    </SectionHeading>
                </Reveal>
                <Reveal :delay="120">
                    <div class="grid gap-px bg-line border border-line sm:grid-cols-2">
                        <div v-for="m in domain.modules" :key="m" class="bg-paper px-5 py-4 text-sm font-medium">
                            {{ m }}
                        </div>
                    </div>
                </Reveal>
            </div>
        </div>
    </section>

    <section class="border-t border-line bg-paper-deep">
        <div class="mx-auto max-w-6xl px-6 py-20 text-center">
            <h2 class="text-3xl font-bold tracking-display">Only pay for the rows you need.</h2>
            <div class="mt-8">
                <Link href="/pricing"
                    class="rounded-full bg-ink px-8 py-4 font-semibold text-white hover:bg-accent transition ease-out duration-150">
                    Build your price
                </Link>
            </div>
        </div>
    </section>
</template>
