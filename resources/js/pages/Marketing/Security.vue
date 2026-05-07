<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { ShieldCheck, Lock, Globe, Eye, AlertTriangle, Bell } from 'lucide-vue-next'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>

<template>
    <Head title="Security — FlowFlex">
        <meta name="description" content="How FlowFlex keeps your data secure — EU data storage, encryption, access controls, and vulnerability disclosure." />
    </Head>

    <!-- Hero -->
    <section class="relative bg-[#050E1A] py-28 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-ocean-950 to-[#050E1A] pointer-events-none" />
        <div class="absolute inset-0 bg-[linear-gradient(rgba(33,153,200,0.04)_1px,transparent_1px),linear-gradient(90deg,rgba(33,153,200,0.04)_1px,transparent_1px)] bg-[size:72px_72px] pointer-events-none" />
        <div class="relative max-w-4xl mx-auto text-center space-y-6">
            <div class="flex justify-center mb-4">
                <div class="w-20 h-20 rounded-2xl bg-ocean-500/15 border border-ocean-500/20 flex items-center justify-center shadow-[0_0_40px_rgba(33,153,200,0.2)]">
                    <ShieldCheck class="w-10 h-10 text-ocean-400" />
                </div>
            </div>
            <h1 class="text-4xl sm:text-6xl font-black text-white tracking-tighter">{{ t('security.heading') }}</h1>
            <p class="text-xl text-slate-400 max-w-2xl mx-auto leading-relaxed">
                {{ t('security.subheading') }}
            </p>
        </div>
    </section>

    <!-- Quick summary badges -->
    <section class="bg-white dark:bg-slate-900 py-12 px-4 sm:px-6 lg:px-8 border-b border-slate-100 dark:border-slate-800">
        <div class="max-w-5xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div class="space-y-2">
                <div class="text-2xl font-bold text-ocean-500 dark:text-ocean-400">{{ t('security.euStorage') }}</div>
                <div class="text-sm text-slate-600 dark:text-slate-400">{{ t('security.euStorageDesc') }}</div>
            </div>
            <div class="space-y-2">
                <div class="text-2xl font-bold text-ocean-500 dark:text-ocean-400">{{ t('security.encryption') }}</div>
                <div class="text-sm text-slate-600 dark:text-slate-400">{{ t('security.encryptionDesc') }}</div>
            </div>
            <div class="space-y-2">
                <div class="text-2xl font-bold text-ocean-500 dark:text-ocean-400">{{ t('security.tls') }}</div>
                <div class="text-sm text-slate-600 dark:text-slate-400">{{ t('security.tlsDesc') }}</div>
            </div>
            <div class="space-y-2">
                <div class="text-2xl font-bold text-ocean-500 dark:text-ocean-400">{{ t('security.gdpr') }}</div>
                <div class="text-sm text-slate-600 dark:text-slate-400">{{ t('security.gdprDesc') }}</div>
            </div>
        </div>
    </section>

    <!-- Detailed content -->
    <section class="bg-slate-50 dark:bg-slate-800/50 py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto space-y-8">

            <!-- EU Data Storage -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-8 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-ocean-50 dark:bg-ocean-900/30 flex items-center justify-center">
                        <Globe class="w-5 h-5 text-ocean-500 dark:text-ocean-400" />
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">EU data storage</h2>
                </div>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    All Customer Data is stored and processed exclusively within the European Union. Our primary infrastructure runs on Amazon Web Services (AWS) in the eu-west-1 region (Dublin, Ireland). We do not transfer personal data outside the EEA unless required by law, in which case appropriate Standard Contractual Clauses (SCCs) are in place.
                </p>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Database backups are encrypted and stored in the same AWS region. We maintain daily backups with 30-day retention.
                </p>
            </div>

            <!-- Encryption -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-8 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-ocean-50 dark:bg-ocean-900/30 flex items-center justify-center">
                        <Lock class="w-5 h-5 text-ocean-500 dark:text-ocean-400" />
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Encryption</h2>
                </div>
                <h3 class="font-semibold text-slate-800 dark:text-slate-200">Encryption at rest</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    All data stored in FlowFlex databases and file storage is encrypted at rest using AES-256 encryption. Sensitive fields — including salary records, bank details, and API keys — are additionally encrypted at the application layer using separate encryption keys.
                </p>
                <h3 class="font-semibold text-slate-800 dark:text-slate-200">Encryption in transit</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    All data transmitted between your browser and FlowFlex servers is encrypted using TLS 1.3. We enforce HTTPS across the entire platform and use HSTS to prevent downgrade attacks. Our TLS configuration achieves an A+ rating on SSL Labs.
                </p>
            </div>

            <!-- Access Controls -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-8 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-ocean-50 dark:bg-ocean-900/30 flex items-center justify-center">
                        <Eye class="w-5 h-5 text-ocean-500 dark:text-ocean-400" />
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Access controls</h2>
                </div>
                <ul class="space-y-3 text-slate-600 dark:text-slate-400">
                    <li class="flex items-start gap-3">
                        <span class="text-ocean-500 font-bold mt-0.5 shrink-0">•</span>
                        <span><strong class="text-slate-800 dark:text-slate-200">Role-based permissions:</strong> Every resource in FlowFlex is protected by granular role-based access control. Users only see and access what they're permitted to.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-ocean-500 font-bold mt-0.5 shrink-0">•</span>
                        <span><strong class="text-slate-800 dark:text-slate-200">Multi-tenancy isolation:</strong> Every workspace is completely isolated. No data from one company is ever accessible to another, enforced at the database query level via global scopes.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-ocean-500 font-bold mt-0.5 shrink-0">•</span>
                        <span><strong class="text-slate-800 dark:text-slate-200">FlowFlex staff access:</strong> FlowFlex employees cannot access your data without explicit written consent, except where required for legal compliance or to resolve a support issue you've raised.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-ocean-500 font-bold mt-0.5 shrink-0">•</span>
                        <span><strong class="text-slate-800 dark:text-slate-200">MFA for admin access:</strong> All FlowFlex administrative access to production systems requires multi-factor authentication.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-ocean-500 font-bold mt-0.5 shrink-0">•</span>
                        <span><strong class="text-slate-800 dark:text-slate-200">Audit logging:</strong> All write operations, permission changes, and login events are logged in an immutable audit trail.</span>
                    </li>
                </ul>
            </div>

            <!-- Penetration Testing -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-8 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-ocean-50 dark:bg-ocean-900/30 flex items-center justify-center">
                        <AlertTriangle class="w-5 h-5 text-ocean-500 dark:text-ocean-400" />
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Penetration testing</h2>
                </div>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    FlowFlex undergoes third-party penetration testing at least annually, conducted by an independent security firm. Critical and high severity findings are remediated within 7 days of discovery. Medium severity findings are addressed within 30 days.
                </p>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    We also conduct internal security reviews with each major feature release, including OWASP Top 10 assessment and code review.
                </p>
            </div>

            <!-- GDPR -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-8 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-ocean-50 dark:bg-ocean-900/30 flex items-center justify-center">
                        <ShieldCheck class="w-5 h-5 text-ocean-500 dark:text-ocean-400" />
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">GDPR compliance</h2>
                </div>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    FlowFlex is designed from the ground up to support GDPR compliance:
                </p>
                <ul class="list-disc pl-6 space-y-2 text-slate-600 dark:text-slate-400">
                    <li>Data stored exclusively in the EU</li>
                    <li>Data Processing Agreement available to all customers (see <Link href="/legal/dpa" class="text-ocean-600 dark:text-ocean-400 hover:underline">DPA</Link>)</li>
                    <li>Data subject request tools built into the platform</li>
                    <li>Right to erasure supported — including cascading deletion</li>
                    <li>Data portability — export all your data in standard formats at any time</li>
                    <li>Privacy by design and by default</li>
                </ul>
            </div>

            <!-- Vulnerability Disclosure -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-8 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-ocean-50 dark:bg-ocean-900/30 flex items-center justify-center">
                        <Bell class="w-5 h-5 text-ocean-500 dark:text-ocean-400" />
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Vulnerability disclosure</h2>
                </div>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    We welcome responsible disclosure of security vulnerabilities. If you have discovered a potential security issue in FlowFlex, please report it to us privately before making it public.
                </p>
                <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-5 space-y-2">
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">How to report a vulnerability:</p>
                    <ol class="list-decimal pl-5 space-y-1 text-sm text-slate-600 dark:text-slate-400">
                        <li>Email your findings to <a href="mailto:security@flowflex.com" class="text-ocean-600 dark:text-ocean-400 hover:underline">security@flowflex.com</a></li>
                        <li>Use PGP encryption if the finding is sensitive (key available on request)</li>
                        <li>Include steps to reproduce, potential impact, and suggested remediation</li>
                        <li>Allow us 90 days to investigate and remediate before public disclosure</li>
                    </ol>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    We do not offer a formal bug bounty programme at this time, but we acknowledge all valid reports publicly (with your permission) in our security changelog.
                </p>
            </div>

            <!-- Incident Response -->
            <div class="bg-slate-900 dark:bg-slate-950 rounded-2xl p-8 space-y-4 text-white">
                <h2 class="text-2xl font-bold">Incident response</h2>
                <p class="text-slate-300 leading-relaxed">
                    In the event of a security incident affecting Customer Data, we will:
                </p>
                <ul class="list-disc pl-6 space-y-2 text-slate-300">
                    <li>Notify affected customers within 72 hours of becoming aware of a confirmed breach</li>
                    <li>Provide details of what data was affected and the likely impact</li>
                    <li>Take immediate steps to contain and remediate the incident</li>
                    <li>Report to the Dutch Data Protection Authority as required by GDPR Article 33</li>
                    <li>Publish a post-incident report within 30 days</li>
                </ul>
                <p class="text-sm text-slate-400">
                    Security status and incident history: <a href="https://status.flowflex.com" class="text-ocean-400 hover:underline" target="_blank" rel="noopener noreferrer">status.flowflex.com</a>
                </p>
            </div>

        </div>
    </section>

    <!-- Contact CTA -->
    <section class="bg-white dark:bg-slate-900 py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center space-y-4">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ t('security.questionsTitle') }}</h2>
            <p class="text-slate-600 dark:text-slate-400">{{ t('security.questionsDesc') }}</p>
            <a
                href="mailto:security@flowflex.com"
                class="inline-flex items-center gap-2 bg-ocean-500 hover:bg-ocean-400 text-white px-6 py-3 rounded-xl font-semibold transition-all shadow-sm hover:-translate-y-0.5"
            >
                security@flowflex.com
            </a>
        </div>
    </section>
</template>
