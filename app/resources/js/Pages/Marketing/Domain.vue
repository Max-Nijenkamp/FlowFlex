<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import Reveal from '@/Components/UI/Reveal.vue'
import { Head, Link } from '@inertiajs/vue3'

defineOptions({ layout: MarketingLayout })

defineProps<{
    domain: { key: string; name: string; description: string; flows: string[] }
    modules: { key: string; name: string; price_cents: number }[]
}>()

const euro = (cents: number) => `€${(cents / 100).toLocaleString('nl-NL', { minimumFractionDigits: 2 })}`
</script>

<template>
    <Head :title="domain.name" />

    <section class="mx-auto max-w-6xl px-6 pt-20 pb-14">
        <nav class="flex items-center gap-2 text-sm text-ink-faint" aria-label="Breadcrumb">
            <Link href="/features" class="hover:text-ink transition ease-out duration-150">Product</Link>
            <span>/</span>
            <span class="text-ink-soft">{{ domain.name }}</span>
        </nav>
        <h1 class="mt-5 max-w-2xl text-4xl sm:text-6xl font-bold tracking-display text-balance">{{ domain.name }}</h1>
        <p class="mt-6 max-w-xl text-lg text-ink-soft leading-relaxed">{{ domain.description }}</p>
        <div class="mt-9 flex flex-wrap items-center gap-4">
            <Link :href="`/pricing?domain=${domain.key}`"
                class="rounded-full bg-ink px-7 py-3.5 font-semibold text-white hover:bg-accent transition ease-out duration-150 active:scale-[0.98]">
                Price these modules
            </Link>
            <Link href="/features" class="group font-semibold text-ink">
                See all modules
                <span class="inline-block transition-transform ease-out duration-150 group-hover:translate-x-1">→</span>
            </Link>
        </div>
    </section>

    <!-- Modules in this domain -->
    <section class="border-t border-line bg-white">
        <div class="mx-auto max-w-6xl px-6 py-20">
            <Reveal>
                <h2 class="text-2xl font-bold tracking-display">What's in {{ domain.name }}</h2>
                <p class="mt-2 text-sm text-ink-faint">{{ modules.length }} modules — each one a switch on your billing page.</p>
            </Reveal>
            <div class="mt-10 grid gap-px bg-line border border-line sm:grid-cols-2 lg:grid-cols-3">
                <Reveal v-for="(m, i) in modules" :key="m.key" :delay="Math.min(i * 40, 320)">
                    <div class="flex h-full items-center justify-between gap-4 bg-white px-6 py-5">
                        <span class="text-sm font-semibold">{{ m.name }}</span>
                        <span class="font-mono text-xs text-ink-faint whitespace-nowrap">
                            {{ m.price_cents === 0 ? 'included' : euro(m.price_cents) + '/user' }}
                        </span>
                    </div>
                </Reveal>
            </div>
        </div>
    </section>

    <!-- What flows -->
    <section v-if="domain.flows.length" class="border-t border-line bg-ink text-white">
        <div class="mx-auto max-w-6xl px-6 py-20">
            <Reveal>
                <h2 class="text-2xl font-bold tracking-display">Flows automatically</h2>
                <p class="mt-2 text-sm text-white/50">No integrations to configure — this is how the platform works.</p>
            </Reveal>
            <div class="mt-10 divide-y divide-white/10 border-y border-white/10">
                <Reveal v-for="(flow, i) in domain.flows" :key="flow" :delay="i * 80">
                    <p class="flex gap-3 py-5 text-white/80">
                        <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-flow"></span>
                        {{ flow }}
                    </p>
                </Reveal>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="border-t border-line bg-paper-deep">
        <div class="mx-auto max-w-6xl px-6 py-20 text-center">
            <h2 class="text-3xl font-bold tracking-display">Start with {{ domain.name }}. Grow from there.</h2>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <Link :href="`/pricing?domain=${domain.key}`"
                    class="rounded-full bg-ink px-8 py-4 font-semibold text-white hover:bg-accent transition ease-out duration-150 active:scale-[0.98]">
                    Build your price
                </Link>
                <Link href="/features"
                    class="rounded-full border border-line bg-white px-8 py-4 font-semibold text-ink hover:border-ink-faint transition ease-out duration-150">
                    See all modules
                </Link>
            </div>
        </div>
    </section>
</template>
