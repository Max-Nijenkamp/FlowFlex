<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { CheckCircle2, AlertTriangle, XCircle, HelpCircle, Database, HardDrive, Server, Cpu, Shield, RefreshCw } from 'lucide-vue-next'
import { computed } from 'vue'

const props = defineProps<{
    checks: Array<{
        name: string
        label: string
        status: 'operational' | 'degraded' | 'outage' | 'maintenance' | 'unknown'
        summary: string
    }>
    overall: 'operational' | 'degraded' | 'outage' | 'unknown'
    last_checked_at: string | null
}>()

function formatDate(iso: string | null): string {
    if (!iso) return 'Never'
    return new Date(iso).toLocaleString('en-GB', {
        day: 'numeric', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit',
    })
}

const overallConfig = computed(() => {
    switch (props.overall) {
        case 'operational': return {
            bg: 'bg-emerald-500',
            border: 'border-emerald-500/30',
            text: 'text-emerald-400',
            label: 'All systems operational',
            desc: 'All services are running normally.',
        }
        case 'degraded': return {
            bg: 'bg-amber-500',
            border: 'border-amber-500/30',
            text: 'text-amber-400',
            label: 'Partial degradation',
            desc: 'Some services are experiencing issues. We are investigating.',
        }
        case 'outage': return {
            bg: 'bg-red-500',
            border: 'border-red-500/30',
            text: 'text-red-400',
            label: 'Service disruption',
            desc: 'We are experiencing a service disruption and are working to restore normal operation.',
        }
        default: return {
            bg: 'bg-slate-500',
            border: 'border-slate-500/30',
            text: 'text-slate-400',
            label: 'Status unknown',
            desc: 'Health checks have not run yet.',
        }
    }
})

function iconFor(name: string) {
    switch (name) {
        case 'Database': return Database
        case 'Cache': return HardDrive
        case 'Redis': return Server
        case 'UsedDiskSpace': return Cpu
        case 'Environment': return Shield
        default: return Server
    }
}

function statusConfig(status: string) {
    switch (status) {
        case 'operational': return { icon: CheckCircle2, color: 'text-emerald-500', bg: 'bg-emerald-500/10', dot: 'bg-emerald-400', label: 'Operational' }
        case 'degraded': return { icon: AlertTriangle, color: 'text-amber-500', bg: 'bg-amber-500/10', dot: 'bg-amber-400', label: 'Degraded' }
        case 'outage': return { icon: XCircle, color: 'text-red-500', bg: 'bg-red-500/10', dot: 'bg-red-400', label: 'Outage' }
        case 'maintenance': return { icon: RefreshCw, color: 'text-blue-500', bg: 'bg-blue-500/10', dot: 'bg-blue-400', label: 'Maintenance' }
        default: return { icon: HelpCircle, color: 'text-slate-400', bg: 'bg-slate-500/10', dot: 'bg-slate-500', label: 'Unknown' }
    }
}
</script>

<template>
    <Head title="System Status — FlowFlex">
        <meta name="description" content="Real-time status of FlowFlex services — database, cache, queue, and more." />
    </Head>

    <!-- Hero -->
    <section class="relative bg-[#050E1A] py-24 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-ocean-950 to-[#050E1A] pointer-events-none" />
        <div class="absolute inset-0 bg-[linear-gradient(rgba(33,153,200,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(33,153,200,0.03)_1px,transparent_1px)] bg-[size:72px_72px] pointer-events-none" />
        <div class="relative max-w-3xl mx-auto text-center space-y-5">
            <h1 class="text-4xl sm:text-6xl font-black text-white tracking-tighter">System Status</h1>
            <p class="text-lg text-slate-400">Real-time health of FlowFlex services.</p>

            <!-- Overall status banner -->
            <div
                class="inline-flex items-center gap-3 px-6 py-3 rounded-2xl border mt-6"
                :class="[overallConfig.bg + '/10', overallConfig.border]"
            >
                <span class="w-2.5 h-2.5 rounded-full animate-pulse" :class="overallConfig.bg" />
                <span class="font-semibold" :class="overallConfig.text">{{ overallConfig.label }}</span>
            </div>

            <p v-if="last_checked_at" class="text-xs text-slate-600 mt-2">
                Last checked: {{ formatDate(last_checked_at) }}
            </p>
            <p v-else class="text-xs text-slate-600 mt-2">
                Checks run every 5 minutes via scheduler.
            </p>
        </div>
    </section>

    <!-- Status grid -->
    <section class="bg-slate-50 dark:bg-slate-900 py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto space-y-4">

            <!-- No data yet -->
            <div v-if="checks.length === 0" class="text-center py-20">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4">
                    <RefreshCw class="w-8 h-8 text-slate-400" />
                </div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No results yet</h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm max-w-xs mx-auto">
                    Health checks run every 5 minutes. Results will appear here after the first run.
                </p>
            </div>

            <!-- Check rows -->
            <div
                v-for="check in checks"
                :key="check.name"
                class="flex items-center justify-between bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl px-6 py-5 hover:shadow-sm transition-shadow"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                        :class="statusConfig(check.status).bg"
                    >
                        <component
                            :is="iconFor(check.name)"
                            class="w-5 h-5"
                            :class="statusConfig(check.status).color"
                        />
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white text-sm">{{ check.label }}</p>
                        <p v-if="check.summary" class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ check.summary }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0 ml-4">
                    <span class="w-2 h-2 rounded-full" :class="statusConfig(check.status).dot" />
                    <span class="text-sm font-medium" :class="statusConfig(check.status).color">
                        {{ statusConfig(check.status).label }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- Uptime note + incident history -->
    <section class="bg-white dark:bg-slate-900 py-16 px-4 sm:px-6 lg:px-8 border-t border-slate-100 dark:border-slate-800">
        <div class="max-w-3xl mx-auto grid md:grid-cols-2 gap-10">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-3">About this page</h2>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Service health checks run automatically every 5 minutes. This page reflects the most recent check results. Checks cover database connectivity, cache layer, Redis, disk space, and environment configuration.
                </p>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-3">Incident notifications</h2>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed mb-4">
                    Want to be notified during incidents? Subscribe to status updates or follow our changelog for post-incident reports.
                </p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <Link
                        href="/changelog"
                        class="inline-flex items-center justify-center gap-2 bg-ocean-500 hover:bg-ocean-400 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors"
                    >
                        View Changelog
                    </Link>
                    <a
                        href="mailto:support@flowflex.com"
                        class="inline-flex items-center justify-center gap-2 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-ocean-400 dark:hover:border-ocean-500 hover:text-ocean-600 dark:hover:text-ocean-400 px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors"
                    >
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>
