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
            <p class="ff-lede mx-auto mt-4 text-center">
                This page is switched off — or it never existed.
            </p>
            <div class="ff-hero-ctas justify-center">
                <Link href="/" class="ff-btn primary lg">Back home</Link>
                <Link href="/contact" class="ff-btn outline lg">Contact us</Link>
            </div>
            <p class="ff-hero-meta mt-10">error 404 · everything else flows</p>
        </div>
    </section>
</template>
