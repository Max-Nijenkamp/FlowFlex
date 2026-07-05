<script setup lang="ts">
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import MarketingLayout from '../../Components/Layout/MarketingLayout.vue'
import CtaBand from '../../Components/Marketing/CtaBand.vue'
import FlowBand from '../../Components/Marketing/FlowBand.vue'
import Reveal from '../../Components/UI/Reveal.vue'
import { domainColors } from '../../data/marketing'
import { productDomains } from '../../data/productContent'

defineOptions({ layout: MarketingLayout })

const props = defineProps<{ domain: string }>()

const d = computed(() => productDomains.find((x) => x.key === props.domain) ?? productDomains[0])
const color = computed(() => domainColors[d.value.key])
</script>

<template>
    <Head>
        <title>{{ d.name }}</title>
        <meta name="description" :content="d.lede" />
    </Head>

    <!-- Hero -->
    <section class="ff-hero ff-grid-bg" style="padding-bottom: 76px">
        <div class="wrap">
            <div class="ff-crumb">
                <Link href="/product">Product</Link><span>/</span><span class="here">{{ d.name }}</span>
            </div>
            <h1 class="mt-5.5 flex items-center gap-4.5">
                <span class="h-[18px] w-[18px] flex-none rounded-[5px]" :style="{ background: color }"></span>
                {{ d.name }}
            </h1>
            <p class="ff-lede">{{ d.lede }}</p>
            <div class="ff-hero-ctas">
                <Link href="/pricing" class="ff-btn primary lg">Price these modules</Link>
                <Link href="/product" class="ff-arrlink">See all departments <span class="arr">→</span></Link>
            </div>
        </div>
    </section>

    <!-- 01 / Modules -->
    <section class="ff-section" style="background: var(--card)">
        <div class="wrap">
            <Reveal>
                <p class="ff-tag"><b>01</b> / MODULES</p>
                <h2>What's in {{ d.name }}.</h2>
                <div class="mt-12 grid gap-3.5 md:grid-cols-2 lg:grid-cols-3">
                    <div v-for="m in d.detailModules" :key="m.name" class="ff-tile" :class="{ off: !m.on }" style="padding: 22px">
                        <div class="top">
                            <span class="chip" :style="{ background: color }"><span></span></span>
                            <span class="ff-state" :class="m.on ? 'on' : 'off'">{{ m.on ? 'ON' : 'OFF' }}</span>
                        </div>
                        <div class="nm" style="font-size: 15.5px">{{ m.name }}</div>
                        <p class="mt-1.5 text-[13.5px] leading-relaxed" style="color: var(--ink-soft)">{{ m.desc }}</p>
                        <div class="pr" style="margin-top: 12px">{{ m.price === 'included' ? 'included' : m.price + '/user/month' }}</div>
                    </div>
                </div>
            </Reveal>
        </div>
    </section>

    <!-- 02 / Flow -->
    <FlowBand
        tag="02"
        :title="`${d.name.split(' ')[0]} tells the rest of the company itself.`"
        lede="No exports, no Zapier. These happen because everything shares one database."
        :flows="d.detailFlows"
    />

    <!-- 03 / Plays well with -->
    <section class="ff-section">
        <div class="wrap">
            <Reveal>
                <p class="ff-tag"><b>03</b> / PLAYS WELL WITH</p>
                <h2>Strongest alongside.</h2>
                <div class="mt-9 flex flex-wrap gap-2.5">
                    <span v-for="[k, label] in d.playsWellWith" :key="k" class="ff-dompill">
                        <span class="chip" :style="{ background: domainColors[k] }"></span>{{ label }}
                    </span>
                </div>
            </Reveal>
        </div>
    </section>

    <CtaBand :title="d.ctaTitle" :sub="d.ctaSub" />
</template>
