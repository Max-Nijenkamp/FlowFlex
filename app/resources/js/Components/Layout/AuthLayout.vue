<script setup lang="ts">
import Logo from '@/Components/UI/Logo.vue'
import { Link } from '@inertiajs/vue3'

// Split shell (login, invite): dark brand panel with animated flow pulses left,
// graph-paper form side right. `split: false` = centered card (forgot/reset).
withDefaults(defineProps<{ split?: boolean }>(), { split: true })

const paths = [
    'M-20,120 C 180,120 240,250 460,250',
    'M-20,420 C 200,420 260,330 480,330',
    'M-20,640 C 220,640 280,480 500,480',
]
</script>

<template>
    <div v-if="split" class="grid min-h-screen bg-paper text-ink lg:grid-cols-[620px_1fr]">
        <!-- Dark brand panel -->
        <div class="relative hidden flex-col justify-between overflow-hidden bg-flow-bg p-[52px] text-white lg:flex">
            <div class="pointer-events-none absolute -top-[260px] left-1/2 h-[560px] w-[900px] -translate-x-1/2"
                style="background: radial-gradient(ellipse 50% 50% at 50% 50%, rgba(79, 70, 229, 0.28), rgba(56, 189, 248, 0.05) 55%, transparent 75%)" />
            <svg class="absolute inset-0 h-full w-full" viewBox="0 0 620 900" preserveAspectRatio="none" aria-hidden="true">
                <g v-for="(d, i) in paths" :key="i">
                    <path :d="d" fill="none" stroke="rgba(255,255,255,0.07)" stroke-width="1.5" />
                    <path :d="d" fill="none" stroke-width="1.5" stroke-linecap="round"
                        class="animate-pulse-dash [stroke-dasharray:26_200]"
                        :stroke="i % 2 ? 'rgba(56,189,248,0.8)' : 'rgba(139,137,255,0.8)'"
                        :style="{ animationDelay: `${i * 1.4}s` }" />
                </g>
            </svg>
            <div class="relative">
                <Link href="/" aria-label="FlowFlex home">
                    <Logo variant="light" />
                </Link>
            </div>
            <div class="relative">
                <slot name="panel">
                    <h2 class="font-display text-[42px] font-bold leading-[1.08] tracking-display">
                        Everything<br>flows.
                    </h2>
                    <p class="mt-4 max-w-[320px] text-[15px] leading-[1.65] text-white/55">
                        One login for HR, finance, CRM and every other module your team switched on.
                    </p>
                </slot>
                <div class="mt-7 flex gap-6 font-mono text-[11px] text-white/[0.38]">
                    <slot name="trust">
                        <span>EU-hosted</span><span>·</span><span>GDPR-first</span><span>·</span><span>2FA available</span>
                    </slot>
                </div>
            </div>
        </div>

        <!-- Form side -->
        <div class="bg-bloom flex items-center justify-center px-6 py-16">
            <div class="w-full max-w-[420px] rounded-[20px] border border-line-strong bg-card p-7 shadow-[0_1px_2px_rgba(17,24,39,0.04),0_28px_56px_-32px_rgba(17,24,39,0.16)] sm:p-10"
                style="animation: ff-card-in 0.3s cubic-bezier(0, 0, 0.2, 1)">
                <slot />
            </div>
        </div>
    </div>

    <!-- Centered variant — forgot/reset password -->
    <div v-else class="bg-bloom flex min-h-screen flex-col items-center justify-center gap-7 bg-paper px-6 py-16 text-ink">
        <Link href="/" aria-label="FlowFlex home">
            <Logo variant="dark" />
        </Link>
        <div class="w-full max-w-[440px] rounded-[20px] border border-line-strong bg-card p-7 shadow-[0_1px_2px_rgba(17,24,39,0.04),0_28px_56px_-32px_rgba(17,24,39,0.16)] sm:p-10"
            style="animation: ff-card-in 0.3s cubic-bezier(0, 0, 0.2, 1)">
            <slot />
        </div>
        <p class="font-mono text-[11px] text-ink-faint">EU-hosted · GDPR-first</p>
    </div>
</template>

<style scoped>
@keyframes ff-card-in {
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
    [style*='ff-card-in'] {
        animation: none !important;
    }
}
</style>
