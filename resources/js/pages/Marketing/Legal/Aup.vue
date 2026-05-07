<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const toc = [
    { id: 'overview', label: 'Overview' },
    { id: 'permitted', label: 'Permitted use' },
    { id: 'prohibited', label: 'Prohibited activities' },
    { id: 'enforcement', label: 'Enforcement' },
    { id: 'reporting', label: 'Reporting violations' },
]
</script>

<template>
    <Head title="Acceptable Use Policy — FlowFlex">
        <meta name="description" content="FlowFlex Acceptable Use Policy — rules governing how you may use the FlowFlex platform." />
    </Head>

    <div class="bg-slate-50 dark:bg-slate-900 min-h-screen py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <Link href="/" class="text-sm text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors mb-8 inline-block">
                {{ t('legal.backHome') }}
            </Link>

            <div class="flex gap-10 items-start">
                <!-- TOC sidebar -->
                <aside class="hidden lg:block w-56 shrink-0 sticky top-24 self-start">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-4">{{ t('legal.tableOfContents') }}</p>
                        <nav class="space-y-1">
                            <a
                                v-for="item in toc"
                                :key="item.id"
                                :href="`#${item.id}`"
                                class="block text-xs text-slate-600 dark:text-slate-400 hover:text-ocean-600 dark:hover:text-ocean-400 transition-colors py-1 leading-snug"
                            >
                                {{ item.label }}
                            </a>
                        </nav>
                    </div>
                </aside>

                <!-- Main content -->
                <div class="flex-1 min-w-0">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-8 md:p-10 space-y-8">
                        <div class="border-b border-slate-100 dark:border-slate-700 pb-6">
                            <h1 class="text-4xl font-bold text-slate-900 dark:text-white mb-2">{{ t('legal.aup.title') }}</h1>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ t('legal.lastUpdated') }}: 7 May 2026</p>
                        </div>

                        <section id="overview" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Overview</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                This Acceptable Use Policy ("AUP") governs your use of the FlowFlex platform and services. By using FlowFlex, you agree to comply with this policy. Violations may result in suspension or termination of your account.
                            </p>
                        </section>

                        <section id="permitted" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Permitted use</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">You may use FlowFlex for lawful business purposes, including managing your employees, finances, projects, customers, and other business operations as supported by the platform's modules.</p>
                        </section>

                        <section id="prohibited" class="space-y-4 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Prohibited activities</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">You must not use FlowFlex to:</p>

                            <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200">Illegal activities</h3>
                            <ul class="list-disc pl-6 space-y-1 text-slate-600 dark:text-slate-400">
                                <li>Violate any applicable law, regulation, or third-party rights</li>
                                <li>Store, transmit, or process personal data in violation of GDPR or other applicable data protection law</li>
                                <li>Engage in money laundering, fraud, or any financial crime</li>
                                <li>Transmit material that infringes copyright, trademarks, or other intellectual property</li>
                            </ul>

                            <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200">Harmful content</h3>
                            <ul class="list-disc pl-6 space-y-1 text-slate-600 dark:text-slate-400">
                                <li>Upload or transmit malware, viruses, or malicious code</li>
                                <li>Store, process, or distribute child sexual abuse material (CSAM) — this will result in immediate termination and referral to law enforcement</li>
                                <li>Harass, threaten, or abuse other users or third parties</li>
                            </ul>

                            <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200">System abuse</h3>
                            <ul class="list-disc pl-6 space-y-1 text-slate-600 dark:text-slate-400">
                                <li>Attempt to gain unauthorised access to FlowFlex systems or another user's account</li>
                                <li>Conduct security testing, penetration testing, or vulnerability scanning without prior written consent</li>
                                <li>Deliberately degrade platform performance or conduct denial-of-service attacks</li>
                                <li>Scrape, harvest, or systematically extract data from the platform using automated means</li>
                                <li>Reverse-engineer, decompile, or disassemble the FlowFlex software</li>
                            </ul>

                            <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200">Misuse of account</h3>
                            <ul class="list-disc pl-6 space-y-1 text-slate-600 dark:text-slate-400">
                                <li>Share your account credentials with unauthorised individuals</li>
                                <li>Resell or sublicence access to FlowFlex without written permission</li>
                                <li>Create accounts for competitors to benchmark our platform</li>
                            </ul>
                        </section>

                        <section id="enforcement" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Enforcement</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                FlowFlex reserves the right to investigate suspected violations and to take appropriate action, including:
                            </p>
                            <ul class="list-disc pl-6 space-y-1 text-slate-600 dark:text-slate-400">
                                <li>Issuing a written warning</li>
                                <li>Suspending access pending investigation</li>
                                <li>Terminating the subscription immediately without refund</li>
                                <li>Referring the matter to law enforcement authorities</li>
                            </ul>
                        </section>

                        <section id="reporting" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Reporting violations</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                If you become aware of a violation of this AUP, please report it to <a href="mailto:security@flowflex.com" class="text-ocean-600 dark:text-ocean-400 hover:underline">security@flowflex.com</a> (for security issues) or <a href="mailto:abuse@flowflex.com" class="text-ocean-600 dark:text-ocean-400 hover:underline">abuse@flowflex.com</a> (for other violations).
                            </p>
                        </section>

                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700 text-sm text-slate-400 dark:text-slate-500">
                            AUP enquiries: <a href="mailto:legal@flowflex.com" class="text-ocean-600 dark:text-ocean-400 hover:underline">legal@flowflex.com</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
