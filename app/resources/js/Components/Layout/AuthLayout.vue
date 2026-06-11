<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import Logo from '@/Components/UI/Logo.vue'
import FlowLine from '@/Components/Marketing/FlowLine.vue'
</script>

<template>
    <div class="min-h-screen grid lg:grid-cols-[1fr_1.1fr] bg-paper text-ink">
        <!-- Brand panel -->
        <div class="relative hidden lg:flex flex-col justify-between overflow-hidden bg-ink p-12 text-white">
            <Link href="/" aria-label="FlowFlex home">
                <Logo variant="light" />
            </Link>
            <div class="relative z-10 max-w-sm">
                <p class="text-3xl font-bold tracking-display leading-snug">Everything flows.</p>
                <p class="mt-4 text-white/60 leading-relaxed">
                    One platform. Every tool. Always flexible.
                </p>
            </div>
            <p class="text-xs text-white/40">EU-hosted · GDPR-compliant · Data portable by default</p>
            <div class="absolute inset-x-0 bottom-24 h-32 opacity-60">
                <FlowLine />
            </div>
        </div>

        <!-- Form panel -->
        <div class="flex flex-col">
            <div class="flex items-center justify-between p-6 lg:hidden">
                <Link href="/" aria-label="FlowFlex home"><Logo variant="dark" /></Link>
            </div>
            <div class="flex flex-1 items-center justify-center px-6 py-12">
                <div class="w-full max-w-md">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
