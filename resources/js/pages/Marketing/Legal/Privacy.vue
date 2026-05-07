<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const toc = [
    { id: 'who-we-are', label: '1. Who we are' },
    { id: 'data-collected', label: '2. Personal data we collect' },
    { id: 'legal-basis', label: '3. Legal basis (GDPR Art. 6)' },
    { id: 'retention', label: '4. How long we keep data' },
    { id: 'sharing', label: '5. Who we share data with' },
    { id: 'transfers', label: '6. International transfers' },
    { id: 'rights', label: '7. Your rights under GDPR' },
    { id: 'complaints', label: '8. Right to complain' },
    { id: 'changes', label: '9. Changes to this policy' },
]
</script>

<template>
    <Head title="Privacy Policy — FlowFlex">
        <meta name="description" content="FlowFlex Privacy Policy — how we collect, use, and protect your personal data under GDPR." />
    </Head>

    <div class="bg-slate-50 dark:bg-slate-900 min-h-screen py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <Link href="/" class="text-sm text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors mb-8 inline-block">
                {{ t('legal.backHome') }}
            </Link>

            <div class="flex gap-10 items-start">
                <!-- TOC sidebar (sticky) -->
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
                            <h1 class="text-4xl font-bold text-slate-900 dark:text-white mb-2">{{ t('legal.privacy.title') }}</h1>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ t('legal.lastUpdated') }}: 7 May 2026</p>
                        </div>

                        <section id="who-we-are" class="space-y-4 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">1. Who we are</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                FlowFlex Ltd. is the data controller for personal data processed through the FlowFlex platform and this website. Our registered office is in the Netherlands.
                            </p>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                Data protection contact: <a href="mailto:privacy@flowflex.com" class="text-ocean-600 dark:text-ocean-400 hover:underline">privacy@flowflex.com</a>
                            </p>
                        </section>

                        <section id="data-collected" class="space-y-4 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">2. What personal data we collect</h2>

                            <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200">2.1 Website visitors</h3>
                            <ul class="list-disc pl-6 space-y-1 text-slate-600 dark:text-slate-400">
                                <li>IP address (anonymised after 24 hours)</li>
                                <li>Browser type and operating system</li>
                                <li>Pages visited and time spent (via privacy-respecting analytics)</li>
                                <li>Referring website</li>
                            </ul>

                            <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200">2.2 Demo request form</h3>
                            <ul class="list-disc pl-6 space-y-1 text-slate-600 dark:text-slate-400">
                                <li>Name and email address</li>
                                <li>Company name and size</li>
                                <li>Phone number (optional)</li>
                                <li>Modules of interest and any notes provided</li>
                            </ul>

                            <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200">2.3 FlowFlex customers</h3>
                            <ul class="list-disc pl-6 space-y-1 text-slate-600 dark:text-slate-400">
                                <li>Account registration data (name, work email, company)</li>
                                <li>Billing information (processed by Stripe — we do not store card numbers)</li>
                                <li>All data entered into the platform by you and your team</li>
                                <li>Usage and audit logs for security and compliance purposes</li>
                            </ul>
                        </section>

                        <section id="legal-basis" class="space-y-4 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">3. Legal basis (GDPR Article 6)</h2>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50 dark:bg-slate-700/50">
                                            <th class="text-left p-3 font-semibold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">Processing activity</th>
                                            <th class="text-left p-3 font-semibold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">Legal basis</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-slate-600 dark:text-slate-400">
                                        <tr>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Responding to a demo request</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Legitimate interest / pre-contractual steps (Art. 6(1)(b))</td>
                                        </tr>
                                        <tr class="bg-slate-50/50 dark:bg-slate-700/20">
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Providing the FlowFlex platform</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Performance of a contract (Art. 6(1)(b))</td>
                                        </tr>
                                        <tr>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Marketing communications</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Consent (Art. 6(1)(a))</td>
                                        </tr>
                                        <tr class="bg-slate-50/50 dark:bg-slate-700/20">
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Security and fraud prevention</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Legitimate interest (Art. 6(1)(f))</td>
                                        </tr>
                                        <tr>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Legal obligations (e.g. tax records)</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Legal obligation (Art. 6(1)(c))</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>

                        <section id="retention" class="space-y-4 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">4. How long we keep your data</h2>
                            <ul class="list-disc pl-6 space-y-2 text-slate-600 dark:text-slate-400">
                                <li><strong class="text-slate-800 dark:text-slate-200">Demo requests:</strong> 24 months from the date of enquiry, or until you ask us to delete it.</li>
                                <li><strong class="text-slate-800 dark:text-slate-200">Customer account data:</strong> For the duration of the contract plus 7 years (Dutch tax retention requirements).</li>
                                <li><strong class="text-slate-800 dark:text-slate-200">Audit logs:</strong> 12 months on the platform; archived securely for a further 36 months.</li>
                                <li><strong class="text-slate-800 dark:text-slate-200">Website analytics:</strong> Aggregated and anonymised after 24 hours.</li>
                            </ul>
                        </section>

                        <section id="sharing" class="space-y-4 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">5. Who we share data with</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">We do not sell your data. We share data only with the following categories of third parties:</p>
                            <ul class="list-disc pl-6 space-y-2 text-slate-600 dark:text-slate-400">
                                <li><strong class="text-slate-800 dark:text-slate-200">Stripe:</strong> Payment processing. Stripe processes card data on our behalf and is PCI-DSS Level 1 certified.</li>
                                <li><strong class="text-slate-800 dark:text-slate-200">Amazon Web Services (AWS):</strong> Cloud infrastructure. All data is stored in AWS eu-west-1 (Ireland).</li>
                                <li><strong class="text-slate-800 dark:text-slate-200">Pusher:</strong> Real-time notifications. Minimal data (user ID, workspace ID) used for websocket routing.</li>
                                <li><strong class="text-slate-800 dark:text-slate-200">Legal authorities:</strong> Only if required by law, court order, or regulatory requirement.</li>
                            </ul>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">All third-party processors are bound by Data Processing Agreements and GDPR-compliant terms.</p>
                        </section>

                        <section id="transfers" class="space-y-4 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">6. International transfers</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                Your data is stored and processed within the European Economic Area (EEA). Where any sub-processor operates outside the EEA, we ensure appropriate safeguards are in place, including Standard Contractual Clauses (SCCs) approved by the European Commission.
                            </p>
                        </section>

                        <section id="rights" class="space-y-4 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">7. Your rights under GDPR</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">You have the following rights regarding your personal data:</p>
                            <ul class="list-disc pl-6 space-y-2 text-slate-600 dark:text-slate-400">
                                <li><strong class="text-slate-800 dark:text-slate-200">Right of access:</strong> Request a copy of the data we hold about you.</li>
                                <li><strong class="text-slate-800 dark:text-slate-200">Right to rectification:</strong> Ask us to correct inaccurate data.</li>
                                <li><strong class="text-slate-800 dark:text-slate-200">Right to erasure:</strong> Ask us to delete your personal data (subject to legal retention requirements).</li>
                                <li><strong class="text-slate-800 dark:text-slate-200">Right to portability:</strong> Receive your data in a machine-readable format.</li>
                                <li><strong class="text-slate-800 dark:text-slate-200">Right to object:</strong> Object to processing based on legitimate interest.</li>
                                <li><strong class="text-slate-800 dark:text-slate-200">Right to restrict processing:</strong> Ask us to limit how we process your data.</li>
                                <li><strong class="text-slate-800 dark:text-slate-200">Right to withdraw consent:</strong> Where processing is based on consent, withdraw it at any time.</li>
                            </ul>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                To exercise any of these rights, contact us at <a href="mailto:privacy@flowflex.com" class="text-ocean-600 dark:text-ocean-400 hover:underline">privacy@flowflex.com</a>. We will respond within 30 days.
                            </p>
                        </section>

                        <section id="complaints" class="space-y-4 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">8. Right to complain</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                If you believe we have not handled your data correctly, you have the right to lodge a complaint with the Dutch Data Protection Authority (Autoriteit Persoonsgegevens):
                            </p>
                            <p class="text-slate-600 dark:text-slate-400">
                                <a href="https://autoriteitpersoonsgegevens.nl" target="_blank" rel="noopener noreferrer" class="text-ocean-600 dark:text-ocean-400 hover:underline">autoriteitpersoonsgegevens.nl</a>
                            </p>
                        </section>

                        <section id="changes" class="space-y-4 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">9. Changes to this policy</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                We may update this Privacy Policy from time to time. We will notify active customers of material changes by email at least 30 days in advance. The "Last updated" date at the top of this page always reflects the most recent version.
                            </p>
                        </section>

                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700 text-sm text-slate-400 dark:text-slate-500">
                            Questions about this policy? Email <a href="mailto:privacy@flowflex.com" class="text-ocean-600 dark:text-ocean-400 hover:underline">privacy@flowflex.com</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
