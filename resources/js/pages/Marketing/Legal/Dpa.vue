<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const toc = [
    { id: 'definitions', label: '1. Definitions' },
    { id: 'subject-matter', label: '2. Subject matter' },
    { id: 'categories', label: '3. Categories of data' },
    { id: 'obligations', label: '4. Processor obligations' },
    { id: 'sub-processors', label: '5. Sub-processors' },
    { id: 'security', label: '6. Security measures' },
    { id: 'breach', label: '7. Data breach notification' },
    { id: 'governing-law', label: '8. Governing law' },
]
</script>

<template>
    <Head title="Data Processing Agreement — FlowFlex">
        <meta name="description" content="FlowFlex Data Processing Agreement (DPA) — GDPR Article 28 processor agreement." />
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
                            <h1 class="text-4xl font-bold text-slate-900 dark:text-white mb-2">{{ t('legal.dpa.title') }}</h1>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ t('legal.lastUpdated') }}: 7 May 2026</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Incorporated into and forming part of the FlowFlex Terms of Service</p>
                        </div>

                        <div class="bg-ocean-50 dark:bg-ocean-900/20 border border-ocean-100 dark:border-ocean-800/30 rounded-xl p-5 text-sm text-ocean-900 dark:text-ocean-300">
                            This Data Processing Agreement ("DPA") is entered into by FlowFlex Ltd. ("Processor") and the Customer ("Controller") as defined in the Terms of Service. This DPA forms part of the agreement between the parties and is governed by Regulation (EU) 2016/679 (GDPR), Article 28.
                        </div>

                        <section id="definitions" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">1. Definitions</h2>
                            <ul class="list-disc pl-6 space-y-2 text-slate-600 dark:text-slate-400">
                                <li><strong class="text-slate-800 dark:text-slate-200">"Controller"</strong> — The Customer who determines the purposes and means of processing personal data.</li>
                                <li><strong class="text-slate-800 dark:text-slate-200">"Processor"</strong> — FlowFlex Ltd., who processes personal data on behalf of the Controller.</li>
                                <li><strong class="text-slate-800 dark:text-slate-200">"Personal Data"</strong> — Any information relating to an identified or identifiable natural person, as defined in GDPR Article 4(1).</li>
                                <li><strong class="text-slate-800 dark:text-slate-200">"Processing"</strong> — Any operation performed on personal data, as defined in GDPR Article 4(2).</li>
                                <li><strong class="text-slate-800 dark:text-slate-200">"Sub-processor"</strong> — Any third party engaged by FlowFlex to process personal data.</li>
                            </ul>
                        </section>

                        <section id="subject-matter" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">2. Subject matter and nature of processing</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                FlowFlex processes personal data on behalf of the Customer solely for the purpose of providing the FlowFlex platform and services as described in the Terms of Service. The nature of processing includes storage, retrieval, display, and backup of Customer Data entered into the platform.
                            </p>
                        </section>

                        <section id="categories" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">3. Categories of data subjects and data</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">The Customer may submit personal data to FlowFlex relating to the following categories of data subjects:</p>
                            <ul class="list-disc pl-6 space-y-1 text-slate-600 dark:text-slate-400">
                                <li>Employees and contractors of the Customer</li>
                                <li>Customers and prospects of the Customer</li>
                                <li>Suppliers and business contacts of the Customer</li>
                            </ul>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed mt-3">The categories of personal data may include: names, email addresses, telephone numbers, employment data, financial data, and any other data the Customer chooses to store.</p>
                        </section>

                        <section id="obligations" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">4. Processor obligations</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">FlowFlex shall:</p>
                            <ul class="list-disc pl-6 space-y-2 text-slate-600 dark:text-slate-400">
                                <li>Process personal data only on documented instructions from the Controller</li>
                                <li>Ensure that persons authorised to process personal data are bound by confidentiality obligations</li>
                                <li>Implement appropriate technical and organisational security measures</li>
                                <li>Assist the Controller in fulfilling data subject requests within 72 hours of receiving a forwarded request</li>
                                <li>Delete or return all personal data to the Controller upon termination of the agreement</li>
                                <li>Make available all information necessary to demonstrate compliance with GDPR Article 28</li>
                                <li>Allow for and contribute to audits conducted by the Controller or an appointed auditor</li>
                            </ul>
                        </section>

                        <section id="sub-processors" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">5. Sub-processors</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">FlowFlex uses the following sub-processors:</p>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50 dark:bg-slate-700/50">
                                            <th class="text-left p-3 font-semibold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">Sub-processor</th>
                                            <th class="text-left p-3 font-semibold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">Purpose</th>
                                            <th class="text-left p-3 font-semibold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">Location</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-slate-600 dark:text-slate-400">
                                        <tr>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Amazon Web Services</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Cloud hosting and storage</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">EU (Ireland)</td>
                                        </tr>
                                        <tr class="bg-slate-50/50 dark:bg-slate-700/20">
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Stripe</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Payment processing</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">EU/US (SCCs)</td>
                                        </tr>
                                        <tr>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Pusher</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Real-time notifications</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">EU</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                FlowFlex will notify the Controller of any intended changes to sub-processors at least 14 days in advance. The Controller has the right to object to such changes.
                            </p>
                        </section>

                        <section id="security" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">6. Security measures</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">FlowFlex implements the following technical and organisational measures:</p>
                            <ul class="list-disc pl-6 space-y-2 text-slate-600 dark:text-slate-400">
                                <li>Encryption at rest (AES-256) and in transit (TLS 1.3)</li>
                                <li>Access control and role-based permissions</li>
                                <li>Multi-factor authentication for administrative access</li>
                                <li>Regular security assessments and penetration testing</li>
                                <li>Incident response procedures with 72-hour breach notification</li>
                                <li>Employee security training and confidentiality agreements</li>
                            </ul>
                        </section>

                        <section id="breach" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">7. Data breach notification</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                In the event of a personal data breach, FlowFlex will notify the Controller without undue delay and within 72 hours of becoming aware of the breach, providing all information required under GDPR Article 33(3).
                            </p>
                        </section>

                        <section id="governing-law" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">8. Governing law</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                This DPA is governed by Dutch law and GDPR. Any disputes relating to this DPA shall be subject to the exclusive jurisdiction of the courts of Amsterdam, the Netherlands.
                            </p>
                        </section>

                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700 text-sm text-slate-400 dark:text-slate-500">
                            DPA enquiries: <a href="mailto:privacy@flowflex.com" class="text-ocean-600 dark:text-ocean-400 hover:underline">privacy@flowflex.com</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
