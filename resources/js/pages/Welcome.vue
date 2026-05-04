<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="FlowFlex" />
    <div class="min-h-screen bg-white dark:bg-gray-950 flex items-center justify-center px-6">
        <div class="max-w-xl w-full text-center space-y-8">
            <div>
                <h1 class="text-6xl font-bold tracking-tight text-gray-900 dark:text-white">
                    Flow<span class="text-amber-500">Flex</span>
                </h1>
                <p class="mt-3 text-xl text-gray-500 dark:text-gray-400 font-light">
                    Your business, your tools — in flow.
                </p>
            </div>

            <div class="space-y-3 text-gray-600 dark:text-gray-300">
                <p class="leading-relaxed">
                    One login. One data layer. One bill.<br />
                    Every tool your business needs, only the ones you actually want.
                </p>
                <p class="text-sm text-gray-400 dark:text-gray-500">
                    Platform under construction.
                </p>
            </div>

            <div class="pt-2">
                <a
                    href="/admin"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 transition-colors"
                >
                    Admin Panel <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </div>
</template>
