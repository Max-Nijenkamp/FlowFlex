<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { ArrowLeft, ArrowRight, Check } from 'lucide-vue-next'

interface SubModule {
    id: string
    name: string
    description: string | null
    sort_order: number
}

interface Module {
    id: string
    key: string
    name: string
    description: string | null
    domain: string
    panel_id: string
    icon: string | null
    color: string | null
    sub_modules: SubModule[]
}

const props = defineProps<{ module: Module }>()

const color = props.module.color ?? '#2199C8'

const domainNames: Record<string, string> = {
    hr: 'HR & People',
    projects: 'Projects & Work',
    finance: 'Finance',
    crm: 'CRM & Sales',
    marketing: 'Marketing',
    operations: 'Operations',
    analytics: 'Analytics',
    it: 'IT & Security',
    legal: 'Legal',
    ecommerce: 'E-commerce',
    communications: 'Communications',
    learning: 'Learning & Dev',
    core: 'Core Platform',
}
</script>

<template>
    <Head :title="`${module.name} — FlowFlex`">
        <meta name="description" :content="module.description ?? `Explore the ${module.name} module in FlowFlex.`" />
    </Head>

    <!-- Hero -->
    <section class="relative bg-[#050E1A] py-24 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" :style="`background: radial-gradient(ellipse at 60% 0%, ${color}20, transparent 60%)`" />
        <div class="relative max-w-4xl mx-auto">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm text-slate-400 mb-8">
                <Link href="/features" class="hover:text-white transition-colors flex items-center gap-1.5">
                    <ArrowLeft class="w-3.5 h-3.5" /> Features
                </Link>
                <span>/</span>
                <span class="text-slate-300">{{ domainNames[module.domain] ?? module.domain }}</span>
                <span>/</span>
                <span class="text-white">{{ module.name }}</span>
            </nav>

            <!-- Heading -->
            <div class="space-y-4">
                <div
                    class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest px-3 py-1.5 rounded-full"
                    :style="`background: ${color}20; color: ${color}`"
                >
                    {{ domainNames[module.domain] ?? module.domain }}
                </div>
                <h1 class="text-4xl sm:text-6xl font-black text-white leading-tight tracking-tighter">
                    {{ module.name }}
                </h1>
                <p v-if="module.description" class="text-xl text-slate-400 max-w-2xl leading-relaxed">
                    {{ module.description }}
                </p>
            </div>

            <div class="mt-8 flex flex-wrap gap-4">
                <Link
                    href="/demo"
                    class="inline-flex items-center gap-2 text-white px-6 py-3.5 rounded-xl font-bold transition-all hover:-translate-y-0.5 shadow-lg"
                    :style="`background: ${color}; box-shadow: 0 4px 24px ${color}40`"
                >
                    Request a demo <ArrowRight class="w-4 h-4" />
                </Link>
                <Link
                    href="/pricing"
                    class="inline-flex items-center gap-2 border border-white/20 text-white px-6 py-3.5 rounded-xl font-semibold hover:bg-white/10 transition-all"
                >
                    See pricing
                </Link>
            </div>
        </div>
    </section>

    <!-- Sub-modules -->
    <section v-if="module.sub_modules?.length" class="bg-white dark:bg-slate-900 py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <h2 class="text-3xl font-black text-slate-900 dark:text-white mb-2 tracking-tight">What's included</h2>
            <p class="text-slate-500 dark:text-slate-400 mb-10">
                Everything in {{ module.name }} — {{ module.sub_modules.length }} sub-modules, all included.
            </p>

            <div class="grid sm:grid-cols-2 gap-4">
                <div
                    v-for="sub in module.sub_modules"
                    :key="sub.id"
                    class="flex items-start gap-4 p-5 rounded-2xl border border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 hover:border-slate-200 dark:hover:border-slate-600 transition-colors"
                >
                    <div
                        class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 mt-0.5"
                        :style="`background: ${color}18`"
                    >
                        <Check class="w-4 h-4" :style="`color: ${color}`" />
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white text-sm">{{ sub.name }}</p>
                        <p v-if="sub.description" class="text-slate-500 dark:text-slate-400 text-xs mt-0.5 leading-relaxed">
                            {{ sub.description }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-24 px-4 sm:px-6 lg:px-8" :style="`background: linear-gradient(135deg, ${color}18, ${color}06)`">
        <div class="max-w-2xl mx-auto text-center space-y-6">
            <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                Ready to try {{ module.name }}?
            </h2>
            <p class="text-slate-600 dark:text-slate-400">
                Request a demo and our team will walk you through everything.
            </p>
            <Link
                href="/demo"
                class="inline-flex items-center gap-2 text-white px-8 py-4 rounded-xl font-bold transition-all hover:-translate-y-0.5 shadow-lg"
                :style="`background: ${color}; box-shadow: 0 4px 24px ${color}40`"
            >
                Request a demo <ArrowRight class="w-4 h-4" />
            </Link>
        </div>
    </section>
</template>
