<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import MarketingLayout from '../../Components/Layout/MarketingLayout.vue'
import CtaBand from '../../Components/Marketing/CtaBand.vue'
import FfSwitch from '../../Components/UI/FfSwitch.vue'
import Reveal from '../../Components/UI/Reveal.vue'
import { domainColors, domains } from '../../data/marketing'
import { productDomains } from '../../data/productContent'

defineOptions({ layout: MarketingLayout })

const upcoming = domains.slice(4, 14)
</script>

<template>
    <Head>
        <title>Product</title>
        <meta name="description" content="HR, finance, CRM and projects ship today — each module a switch on your billing page. Twelve more departments already wired." />
    </Head>

    <!-- Hero -->
    <section class="ff-hero ff-grid-bg">
        <div class="wrap">
            <span class="ff-kicker"><span class="sq"></span>Product</span>
            <h1 style="max-width: 720px">Four departments today.<br />The rest is <span class="u">already wired</span>.</h1>
            <p class="ff-lede">
                Every module below ships today. Each one is a switch on your billing page — not a sales call, not an
                implementation project.
            </p>
            <div class="mono mt-7 flex flex-wrap items-center gap-x-5 gap-y-2 text-[12px]" style="color: var(--ink-faint)">
                <span v-for="d in productDomains" :key="d.key" class="flex items-center gap-2">
                    <span class="h-[9px] w-[9px] rounded-[3px]" :style="{ background: domainColors[d.key] }"></span>{{ d.name }}
                </span>
                <span style="color: var(--line-strong)">·</span>
                <span>live today</span>
            </div>
        </div>
    </section>

    <!-- Domain sections -->
    <section
        v-for="(d, i) in productDomains"
        :key="d.key"
        class="ff-section"
        :style="{ background: i % 2 ? 'var(--card)' : 'transparent' }"
    >
        <div class="wrap">
            <Reveal>
                <div class="grid items-start gap-11 lg:gap-16 lg:grid-cols-2">
                    <div>
                        <p class="ff-tag"><b>{{ String(i + 1).padStart(2, '0') }}</b> / DOMAIN</p>
                        <h2 class="flex items-center gap-3.5">
                            <span class="h-3.5 w-3.5 flex-none rounded" :style="{ background: domainColors[d.key] }"></span>
                            {{ d.name }}
                        </h2>
                        <p class="ff-lede">{{ d.desc }}</p>
                        <Link :href="`/product/${d.key}`" class="ff-arrlink mt-5.5 inline-flex">Explore {{ d.name }} <span class="arr">→</span></Link>
                        <div class="mt-7">
                            <p class="ff-tag" style="letter-spacing: 0.16em">FLOWS AUTOMATICALLY</p>
                            <div class="mt-3 flex flex-col gap-2.5">
                                <p v-for="f in d.flowBullets" :key="f" class="flex items-baseline gap-2.5 text-[14.5px]" style="color: var(--ink-soft)">
                                    <span class="relative -top-px h-[7px] w-[7px] flex-none rounded-[2px]" :style="{ background: domainColors[d.key] }"></span>{{ f }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div
                            v-for="[name, price] in d.mods"
                            :key="name"
                            class="flex flex-col gap-1.5 rounded-xl border px-4.5 py-4"
                            :style="{ borderColor: 'var(--line-strong)', background: i % 2 ? 'var(--paper)' : 'var(--card)' }"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-[14px] font-semibold">{{ name }}</span>
                                <FfSwitch :on="price === 'included'" sm />
                            </div>
                            <span class="mono text-[11.5px]" style="color: var(--ink-faint)">{{ price === 'included' ? 'included' : price + '/user' }}</span>
                        </div>
                    </div>
                </div>
            </Reveal>
        </div>
    </section>

    <!-- 05 / Next in line -->
    <section class="ff-section ff-grid-bg">
        <div class="wrap">
            <Reveal>
                <p class="ff-tag"><b>05</b> / NEXT IN LINE</p>
                <h2>Waiting on the switchboard.</h2>
                <p class="ff-lede">
                    Twelve more departments share the same database and the same pricing model, rolling out domain by
                    domain.
                </p>
                <div class="mt-10 flex flex-wrap gap-2.5">
                    <span v-for="d in upcoming" :key="d.key" class="ff-dompill" style="border-style: dashed; background: transparent">
                        <span class="chip" :style="{ background: domainColors[d.key] }"></span>
                        {{ d.name }}
                        <span class="mono text-[10px]" style="color: var(--ink-faint)">soon</span>
                    </span>
                </div>
            </Reveal>
        </div>
    </section>

    <CtaBand title="Only pay for the rows you need." sub="Start with one domain. The rest will be one switch away." />
</template>
