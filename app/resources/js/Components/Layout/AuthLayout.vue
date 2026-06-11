<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import FlowLine from '@/Components/Marketing/FlowLine.vue'
</script>

<template>
    <!-- Login is a door, not a pitch: centered utility page on the product's
         warm canvas (Notion/Slack/Atlassian pattern — no marketing split). -->
    <div class="relative flex min-h-screen flex-col bg-paper text-ink">
        <div class="flex flex-1 flex-col items-center justify-center px-6 py-16">
            <Link href="/" aria-label="FlowFlex home" class="mb-8">
                <img src="/images/logo/flowflex-icon.svg" alt="FlowFlex" class="h-10 w-10" />
            </Link>

            <div class="w-full max-w-[420px] rounded-2xl border border-line bg-white p-8 sm:p-10 shadow-[0_2px_16px_rgba(17,24,39,0.05)]"
                style="animation: ff-card-in 0.3s cubic-bezier(0, 0, 0.2, 1)">
                <slot />
            </div>

            <div class="mt-8 flex items-center gap-5 text-xs text-ink-faint">
                <Link href="/" class="hover:text-ink transition ease-out duration-150">flowflex.eu</Link>
                <span aria-hidden="true">·</span>
                <Link href="/privacy" class="hover:text-ink transition ease-out duration-150">Privacy</Link>
                <span aria-hidden="true">·</span>
                <Link href="/terms" class="hover:text-ink transition ease-out duration-150">Terms</Link>
            </div>
        </div>

        <!-- Quiet brand signature along the bottom edge. -->
        <div class="pointer-events-none h-16 opacity-50" aria-hidden="true">
            <FlowLine />
        </div>
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
