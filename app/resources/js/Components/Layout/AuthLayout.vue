<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import Logo from '@/Components/UI/Logo.vue'
</script>

<template>
    <div class="min-h-screen grid lg:grid-cols-[1fr_1.1fr] bg-paper text-ink">
        <!-- Brand panel — tagline + three flow chips, lots of air. -->
        <div class="relative hidden lg:flex flex-col overflow-hidden bg-ink p-12 text-white">
            <div aria-hidden="true" class="absolute inset-0 opacity-[0.10]"
                style="background-image: radial-gradient(circle, #ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>

            <Link href="/" aria-label="FlowFlex home" class="relative z-10 shrink-0">
                <Logo variant="light" />
            </Link>

            <div class="relative z-10 flex flex-1 flex-col justify-center">
                <p class="text-4xl font-bold tracking-display leading-snug">Everything flows.</p>
                <p class="mt-4 max-w-xs text-white/55 leading-relaxed">
                    One platform. Every tool. Always flexible.
                </p>

                <div class="mt-14 space-y-3.5" aria-hidden="true">
                    <div class="flex w-fit items-center gap-2.5 rounded-full border border-white/10 bg-white/[0.07] px-4 py-2 text-[12px]"
                        style="animation: ff-chip-in 0.5s cubic-bezier(0, 0, 0.2, 1) 0.25s backwards">
                        <span class="h-1.5 w-1.5 rounded-full bg-flow"></span>
                        <span class="font-mono uppercase tracking-wide text-flow">CRM → Finance</span>
                        <span class="text-white/75">Deal won — invoice drafted</span>
                    </div>
                    <div class="ml-7 flex w-fit items-center gap-2.5 rounded-full border border-white/10 bg-white/[0.07] px-4 py-2 text-[12px]"
                        style="animation: ff-chip-in 0.5s cubic-bezier(0, 0, 0.2, 1) 0.5s backwards">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        <span class="font-mono uppercase tracking-wide text-emerald-300">HR → Payroll</span>
                        <span class="text-white/75">Offer accepted — salary scheduled</span>
                    </div>
                    <div class="ml-3 flex w-fit items-center gap-2.5 rounded-full border border-white/10 bg-white/[0.07] px-4 py-2 text-[12px]"
                        style="animation: ff-chip-in 0.5s cubic-bezier(0, 0, 0.2, 1) 0.75s backwards">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-300"></span>
                        <span class="font-mono uppercase tracking-wide text-amber-200">Finance → CRM</span>
                        <span class="text-white/75">Invoice paid — lifetime value updated</span>
                    </div>
                </div>
            </div>

            <p class="relative z-10 shrink-0 text-xs text-white/40">EU-hosted · GDPR-compliant · Data portable by default</p>
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

<style scoped>
@keyframes ff-chip-in {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: none;
    }
}

@media (prefers-reduced-motion: reduce) {
    [style*='ff-chip-in'] {
        animation: none !important;
    }
}
</style>
