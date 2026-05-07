<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const toc = [
    { id: 'acceptance', label: '1. Acceptance' },
    { id: 'definitions', label: '2. Definitions' },
    { id: 'account', label: '3. Account creation' },
    { id: 'billing', label: '4. Billing and payment' },
    { id: 'data-ownership', label: '5. Customer data ownership' },
    { id: 'ip', label: '6. Intellectual property' },
    { id: 'acceptable-use', label: '7. Acceptable use' },
    { id: 'termination', label: '8. Termination' },
    { id: 'liability', label: '9. Limitation of liability' },
    { id: 'governing-law', label: '10. Governing law' },
    { id: 'contact', label: '11. Contact' },
]
</script>

<template>
    <Head title="Terms of Service — FlowFlex">
        <meta name="description" content="FlowFlex Terms of Service — the agreement governing your use of the FlowFlex platform." />
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
                            <h1 class="text-4xl font-bold text-slate-900 dark:text-white mb-2">{{ t('legal.terms.title') }}</h1>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ t('legal.lastUpdated') }}: 7 May 2026</p>
                        </div>

                        <section id="acceptance" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">1. Acceptance</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                By accessing or using the FlowFlex platform ("Service"), you agree to be bound by these Terms of Service. If you do not agree, you may not use the Service. If you are using the Service on behalf of a company or other legal entity, you represent that you have the authority to bind that entity to these Terms.
                            </p>
                        </section>

                        <section id="definitions" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">2. Definitions</h2>
                            <ul class="list-disc pl-6 space-y-2 text-slate-600 dark:text-slate-400">
                                <li><strong class="text-slate-800 dark:text-slate-200">"FlowFlex"</strong> — FlowFlex Ltd., a company registered in the Netherlands.</li>
                                <li><strong class="text-slate-800 dark:text-slate-200">"Platform"</strong> — The FlowFlex software-as-a-service application and all associated APIs, documentation, and services.</li>
                                <li><strong class="text-slate-800 dark:text-slate-200">"Customer"</strong> — The company or individual who has entered into a subscription agreement with FlowFlex.</li>
                                <li><strong class="text-slate-800 dark:text-slate-200">"User"</strong> — Any individual accessing the Platform under a Customer's account.</li>
                                <li><strong class="text-slate-800 dark:text-slate-200">"Customer Data"</strong> — All data submitted, stored, or processed by the Customer or its Users on the Platform.</li>
                            </ul>
                        </section>

                        <section id="account" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">3. Account creation</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                There is no self-registration. The first account is created by FlowFlex upon subscription. The Customer is responsible for maintaining the security of their account credentials and for all activity under their account.
                            </p>
                        </section>

                        <section id="billing" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">4. Billing and payment</h2>
                            <ul class="list-disc pl-6 space-y-2 text-slate-600 dark:text-slate-400">
                                <li>Subscriptions are billed monthly or annually as selected at signup.</li>
                                <li>Annual subscriptions are charged upfront and are non-refundable except as required by law.</li>
                                <li>Monthly subscriptions may be cancelled at any time; access continues until the end of the billing period.</li>
                                <li>Prices are exclusive of VAT unless stated otherwise. Dutch VAT (BTW) applies where required.</li>
                                <li>Failed payments will trigger a retry cycle. After 7 days of non-payment, access may be suspended.</li>
                                <li>Pricing changes will be communicated with at least 30 days' notice.</li>
                            </ul>
                        </section>

                        <section id="data-ownership" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">5. Customer data ownership</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                Customer Data belongs to the Customer. FlowFlex does not claim ownership of any data you store in the Platform. We process Customer Data solely to provide and improve the Service, as described in our <Link href="/legal/privacy" class="text-ocean-600 dark:text-ocean-400 hover:underline">Privacy Policy</Link> and <Link href="/legal/dpa" class="text-ocean-600 dark:text-ocean-400 hover:underline">Data Processing Agreement</Link>.
                            </p>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                You may export your data at any time from the workspace settings. Upon termination, we will retain your data for 30 days to allow for export, after which it will be deleted.
                            </p>
                        </section>

                        <section id="ip" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">6. Intellectual property</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                FlowFlex and its licensors own all intellectual property rights in the Platform, including all software, design, trademarks, and documentation. Nothing in these Terms transfers any IP rights to you. You are granted a limited, non-exclusive, non-transferable licence to use the Platform for your internal business purposes.
                            </p>
                        </section>

                        <section id="acceptable-use" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">7. Acceptable use</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                Your use of the Platform is subject to our <Link href="/legal/aup" class="text-ocean-600 dark:text-ocean-400 hover:underline">Acceptable Use Policy</Link>. You may not use the Platform for any unlawful purpose or in any way that infringes the rights of others.
                            </p>
                        </section>

                        <section id="termination" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">8. Termination</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                Either party may terminate the subscription at any time. FlowFlex may terminate immediately for material breach, non-payment, or violation of the Acceptable Use Policy. Upon termination, your access to the Platform will cease at the end of the current billing period (or immediately for cause).
                            </p>
                        </section>

                        <section id="liability" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">9. Limitation of liability</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                To the maximum extent permitted by applicable law, FlowFlex shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising from your use of the Platform. FlowFlex's total aggregate liability shall not exceed the fees paid by you in the 12 months preceding the claim.
                            </p>
                        </section>

                        <section id="governing-law" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">10. Governing law</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                These Terms are governed by Dutch law. Any disputes will be subject to the exclusive jurisdiction of the courts of Amsterdam, the Netherlands.
                            </p>
                        </section>

                        <section id="contact" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">11. Contact</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                For any questions about these Terms, contact us at <a href="mailto:legal@flowflex.com" class="text-ocean-600 dark:text-ocean-400 hover:underline">legal@flowflex.com</a>.
                            </p>
                        </section>

                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700 text-sm text-slate-400 dark:text-slate-500">
                            Questions about these terms? Email <a href="mailto:legal@flowflex.com" class="text-ocean-600 dark:text-ocean-400 hover:underline">legal@flowflex.com</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
