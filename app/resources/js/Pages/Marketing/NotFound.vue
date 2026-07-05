<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import MarketingLayout from '../../Components/Layout/MarketingLayout.vue'
import FfSwitch from '../../Components/UI/FfSwitch.vue'

defineOptions({ layout: MarketingLayout })
</script>

<template>
    <Head>
        <title>Page not found</title>
    </Head>

    <section class="ff-hero ff-grid-bg flex items-center" style="border-bottom: none; min-height: 70vh">
        <div class="wrap w-full text-center">
            <span class="ff-dompill inline-flex">
                <FfSwitch :on="false" sm />
                <span>this page</span>
                <span class="ff-state off">OFF</span>
            </span>
            <h1 class="mt-6" style="font-size: clamp(72px, 14vw, 120px); line-height: 1">404</h1>
            <p class="mx-auto mt-4 max-w-[380px] text-center text-[17px] leading-relaxed" style="color: var(--ink-soft)">
                This page is switched off — or it never existed. Your data is fine; it's just this URL that flows
                nowhere.
            </p>
            <div class="ff-hero-ctas justify-center">
                <Link href="/" class="ff-btn primary">Back to home</Link>
                <Link href="/contact" class="ff-btn outline">Contact us</Link>
            </div>
            <p class="ff-hero-meta mt-10">error 404 · everything else flows</p>
        </div>
    </section>
</template>
