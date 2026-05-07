<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const toc = [
    { id: 'what-are-cookies', label: 'What are cookies?' },
    { id: 'cookies-we-use', label: 'Cookies we use' },
    { id: 'manage', label: 'How to manage cookies' },
    { id: 'third-party', label: 'Third-party cookies' },
    { id: 'contact', label: 'Contact' },
]
</script>

<template>
    <Head title="Cookie Policy — FlowFlex">
        <meta name="description" content="FlowFlex Cookie Policy — what cookies we use and how to manage your preferences." />
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
                            <h1 class="text-4xl font-bold text-slate-900 dark:text-white mb-2">{{ t('legal.cookies.title') }}</h1>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ t('legal.lastUpdated') }}: 7 May 2026</p>
                        </div>

                        <section id="what-are-cookies" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">What are cookies?</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                Cookies are small text files stored on your device when you visit a website. They help websites remember your preferences, keep you logged in, and understand how you use the site. We use cookies to make the FlowFlex platform and marketing website work correctly and to improve your experience.
                            </p>
                        </section>

                        <section id="cookies-we-use" class="space-y-4 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Cookies we use</h2>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50 dark:bg-slate-700/50">
                                            <th class="text-left p-3 font-semibold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">Cookie name</th>
                                            <th class="text-left p-3 font-semibold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">Type</th>
                                            <th class="text-left p-3 font-semibold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">Purpose</th>
                                            <th class="text-left p-3 font-semibold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">Duration</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-slate-600 dark:text-slate-400">
                                        <tr>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600 font-mono text-xs">flowflex_session</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Strictly necessary</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Maintains your login session within the platform</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Session</td>
                                        </tr>
                                        <tr class="bg-slate-50/50 dark:bg-slate-700/20">
                                            <td class="p-3 border border-slate-200 dark:border-slate-600 font-mono text-xs">XSRF-TOKEN</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Strictly necessary</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Protects against cross-site request forgery attacks</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Session</td>
                                        </tr>
                                        <tr>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600 font-mono text-xs">remember_web_*</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Functional</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Remembers your login if you selected "remember me"</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">400 days</td>
                                        </tr>
                                        <tr class="bg-slate-50/50 dark:bg-slate-700/20">
                                            <td class="p-3 border border-slate-200 dark:border-slate-600 font-mono text-xs">appearance</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Functional</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Stores your light/dark mode preference</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">1 year</td>
                                        </tr>
                                        <tr>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600 font-mono text-xs">_analytics_*</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Analytics</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Privacy-respecting page view analytics (no cross-site tracking)</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">1 year</td>
                                        </tr>
                                        <tr class="bg-slate-50/50 dark:bg-slate-700/20">
                                            <td class="p-3 border border-slate-200 dark:border-slate-600 font-mono text-xs">_marketing_*</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Marketing</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">Tracks visits from marketing campaigns (only with consent)</td>
                                            <td class="p-3 border border-slate-200 dark:border-slate-600">90 days</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>

                        <section id="manage" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">How to manage cookies</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                When you first visit the FlowFlex website, you will see a cookie consent banner. You can accept all cookies, reject optional cookies, or customise your preferences. Strictly necessary cookies cannot be disabled as they are required for the website to function.
                            </p>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                You can change your cookie preferences at any time by clicking "Cookie preferences" in the footer of any page.
                            </p>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                You can also manage cookies through your browser settings. Note that disabling certain cookies may affect the functionality of the website.
                            </p>
                        </section>

                        <section id="third-party" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Third-party cookies</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                We do not embed third-party tracking pixels or advertising networks on our website. Any third-party cookies set by our payment provider (Stripe) are subject to Stripe's own cookie policy.
                            </p>
                        </section>

                        <section id="contact" class="space-y-3 scroll-mt-24">
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Contact</h2>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                Questions about our use of cookies? Email <a href="mailto:privacy@flowflex.com" class="text-ocean-600 dark:text-ocean-400 hover:underline">privacy@flowflex.com</a>.
                            </p>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
