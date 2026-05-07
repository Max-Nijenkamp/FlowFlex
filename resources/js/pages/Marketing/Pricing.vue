<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Check, Zap, Building2, Globe } from 'lucide-vue-next'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const annual = ref(false)

const starterMonthly = 29
const proMonthly = 79
const starterAnnual = 24
const proAnnual = 65

const starterFeatures = [
    'Up to 10 users',
    '5 modules of your choice',
    'Core platform access',
    'Email support (48h response)',
    '5 GB file storage',
    'Basic API access',
    'GDPR compliant',
]

const proFeatures = [
    'Up to 100 users',
    'All modules, unlimited',
    'Advanced analytics & reporting',
    'Priority support (4h response)',
    '100 GB file storage',
    'Full API access + webhooks',
    'Custom roles & permissions',
    'SSO (SAML/OAuth) — coming soon',
    'Audit log',
    'GDPR compliant',
]

const enterpriseFeatures = [
    'Unlimited users',
    'All modules',
    'Dedicated account manager',
    'SLA with guaranteed uptime',
    'Custom integrations',
    'On-premise option (roadmap)',
    'Custom contract & billing',
    'Security review',
    'Onboarding & training',
    'GDPR + DPA included',
]

const faqs = [
    {
        q: 'What is a module?',
        a: 'A module is a self-contained feature area — for example HR Leave Management, Project Tasks, or CRM Pipeline. You activate only the modules you need and pay per active module per user.',
    },
    {
        q: 'Can I switch modules on and off?',
        a: 'Yes. You can activate or deactivate modules at any time from your workspace settings. Billing adjusts at the start of the next billing cycle.',
    },
    {
        q: 'What happens to my data if I deactivate a module?',
        a: 'Your data is preserved. If you reactivate the module, everything is exactly as you left it. You can also export all your data at any time from the settings panel.',
    },
    {
        q: 'Is there a free trial?',
        a: "We don't offer a self-serve free trial yet. Book a demo and we'll give you a 14-day fully-featured trial environment configured for your business.",
    },
    {
        q: 'How does billing work?',
        a: 'You are billed per user per month, multiplied by the number of active modules. Annual billing is charged upfront at a discounted rate equivalent to 10 months (2 months free).',
    },
    {
        q: 'What payment methods do you accept?',
        a: 'We accept all major credit and debit cards via Stripe. Enterprise customers can pay by invoice with NET-30 terms.',
    },
    {
        q: 'Is FlowFlex GDPR compliant?',
        a: 'Yes. All data is stored in the EU (AWS eu-west-1). We have a full Data Processing Agreement available. See our Security and Privacy Policy pages for details.',
    },
    {
        q: 'Can I have multiple workspaces?',
        a: 'Each subscription covers one company workspace. If you operate multiple separate businesses, each would need its own subscription. Contact us for group pricing.',
    },
    {
        q: 'Do you offer discounts for non-profits or startups?',
        a: 'Yes. We offer a 30% discount for registered non-profits and early-stage startups (under 2 years old, under €1M ARR). Contact us with verification to apply.',
    },
    {
        q: 'What support do I get?',
        a: 'Starter plans include email support with a 48-hour response time. Pro plans get priority support with a 4-hour response time and access to our live chat. Enterprise customers get a dedicated account manager.',
    },
]

const openFaq = ref<number | null>(null)

function toggleFaq(index: number) {
    openFaq.value = openFaq.value === index ? null : index
}
</script>

<template>
    <Head title="Pricing — FlowFlex">
        <meta name="description" content="Simple, transparent pricing. Three plans, pay only for the modules you activate. No hidden fees." />
    </Head>

    <!-- Hero -->
    <section class="relative bg-[#050E1A] py-24 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-ocean-950 via-[#071828] to-[#050E1A] pointer-events-none" />
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-ocean-500/8 blur-[100px] rounded-full pointer-events-none" />
        <div class="relative max-w-4xl mx-auto text-center space-y-6">
            <h1 class="text-4xl sm:text-6xl md:text-7xl font-black text-white tracking-tighter leading-tight">{{ t('pricing.pageHeading') }}</h1>
            <p class="text-xl text-slate-400 max-w-2xl mx-auto">
                {{ t('pricing.pageSubheading') }}
            </p>

            <!-- Annual/Monthly toggle pill -->
            <div class="flex items-center justify-center gap-4 mt-8">
                <span :class="['text-sm font-medium transition-colors', !annual ? 'text-white' : 'text-slate-400']">{{ t('pricing.monthly') }}</span>
                <button
                    type="button"
                    :class="[
                        'relative inline-flex h-7 w-12 items-center rounded-full transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ocean-500',
                        annual ? 'bg-ocean-500' : 'bg-slate-600',
                    ]"
                    @click="annual = !annual"
                    role="switch"
                    :aria-checked="annual"
                    aria-label="Toggle annual billing"
                >
                    <span
                        :class="[
                            'inline-block h-5 w-5 rounded-full bg-white transition-transform shadow',
                            annual ? 'translate-x-6' : 'translate-x-1',
                        ]"
                    />
                </button>
                <span :class="['text-sm font-medium transition-colors flex items-center gap-2', annual ? 'text-white' : 'text-slate-400']">
                    {{ t('pricing.annual') }}
                    <Transition
                        enter-active-class="transition-all duration-300 ease-out"
                        enter-from-class="opacity-0 scale-90"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition-all duration-200 ease-in"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-90"
                    >
                        <span v-if="annual" class="bg-success-500 text-white text-xs px-2 py-0.5 rounded-full font-semibold">{{ t('pricing.saveMonths') }}</span>
                    </Transition>
                </span>
            </div>
        </div>
    </section>

    <!-- Plan cards -->
    <section class="bg-white dark:bg-slate-900 pb-24 px-4 sm:px-6 lg:px-8 -mt-8">
        <div class="max-w-6xl mx-auto">
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Starter -->
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-8 space-y-6 hover:shadow-md transition-all">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-4">
                            <Zap class="w-5 h-5 text-slate-600 dark:text-slate-300" />
                        </div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ t('pricing.starter.name') }}</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ t('pricing.starter.tagline') }}</p>
                        <div class="mt-4">
                            <span class="text-4xl font-bold text-slate-900 dark:text-white">
                                €{{ annual ? starterAnnual : starterMonthly }}
                            </span>
                            <span class="text-slate-500 dark:text-slate-400 text-sm">{{ t('pricing.perUserMonth') }}</span>
                        </div>
                        <p v-if="annual" class="text-xs text-slate-400 mt-1">{{ t('pricing.billedAnnually') }}</p>
                    </div>
                    <Link
                        href="/demo"
                        class="block text-center border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 px-4 py-3 rounded-xl text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                    >
                        {{ t('pricing.requestAccess') }}
                    </Link>
                    <ul class="space-y-3">
                        <li v-for="feature in starterFeatures" :key="feature" class="flex items-start gap-3">
                            <Check class="w-4 h-4 text-success-500 mt-0.5 shrink-0" />
                            <span class="text-sm text-slate-600 dark:text-slate-400">{{ feature }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Pro -->
                <div class="border-2 border-ocean-500 rounded-2xl p-8 space-y-6 relative ring-2 ring-ocean-500/20 shadow-xl bg-white dark:bg-slate-800">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                        <span class="bg-ocean-500 text-white text-xs font-bold px-4 py-1.5 rounded-full uppercase tracking-wide">{{ t('pricing.mostPopular') }}</span>
                    </div>
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-ocean-50 dark:bg-ocean-900/30 flex items-center justify-center mb-4">
                            <Building2 class="w-5 h-5 text-ocean-600 dark:text-ocean-400" />
                        </div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ t('pricing.pro.name') }}</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ t('pricing.pro.tagline') }}</p>
                        <div class="mt-4">
                            <span class="text-4xl font-bold text-slate-900 dark:text-white">
                                €{{ annual ? proAnnual : proMonthly }}
                            </span>
                            <span class="text-slate-500 dark:text-slate-400 text-sm">{{ t('pricing.perUserMonth') }}</span>
                        </div>
                        <p v-if="annual" class="text-xs text-slate-400 mt-1">{{ t('pricing.billedAnnually') }}</p>
                    </div>
                    <Link
                        href="/demo"
                        class="block text-center bg-ocean-500 hover:bg-ocean-400 text-white px-4 py-3 rounded-xl text-sm font-semibold transition-colors shadow-sm"
                    >
                        {{ t('pricing.requestAccess') }}
                    </Link>
                    <ul class="space-y-3">
                        <li v-for="feature in proFeatures" :key="feature" class="flex items-start gap-3">
                            <Check class="w-4 h-4 text-ocean-500 mt-0.5 shrink-0" />
                            <span class="text-sm text-slate-600 dark:text-slate-400">{{ feature }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Enterprise -->
                <div class="border border-slate-200 dark:border-slate-700 rounded-2xl p-8 space-y-6 bg-slate-900 dark:bg-slate-800/80">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center mb-4">
                            <Globe class="w-5 h-5 text-ocean-300" />
                        </div>
                        <h2 class="text-xl font-bold text-white">{{ t('pricing.enterprise.name') }}</h2>
                        <p class="text-sm text-slate-400 mt-1">{{ t('pricing.enterprise.tagline') }}</p>
                        <div class="mt-4">
                            <span class="text-4xl font-bold text-white">{{ t('pricing.enterprise.price') }}</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">{{ t('pricing.enterprise.priceNote') }}</p>
                    </div>
                    <Link
                        href="/demo"
                        class="block text-center bg-white text-slate-900 px-4 py-3 rounded-xl text-sm font-semibold hover:bg-slate-100 transition-colors"
                    >
                        {{ t('pricing.talkToSales') }}
                    </Link>
                    <ul class="space-y-3">
                        <li v-for="feature in enterpriseFeatures" :key="feature" class="flex items-start gap-3">
                            <Check class="w-4 h-4 text-ocean-400 mt-0.5 shrink-0" />
                            <span class="text-sm text-slate-300">{{ feature }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="bg-slate-50 dark:bg-slate-800/50 py-24 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <h2 class="text-3xl font-bold text-slate-900 dark:text-white text-center mb-12">{{ t('pricing.faq.heading') }}</h2>
            <div class="space-y-3">
                <div
                    v-for="(faq, index) in faqs"
                    :key="index"
                    class="border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden bg-white dark:bg-slate-800"
                >
                    <button
                        type="button"
                        class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors"
                        @click="toggleFaq(index)"
                    >
                        <span class="font-semibold text-slate-900 dark:text-white pr-4">{{ faq.q }}</span>
                        <span
                            class="text-slate-400 text-xl ml-4 shrink-0 transition-transform duration-200 font-light"
                            :class="openFaq === index ? 'rotate-45' : ''"
                        >+</span>
                    </button>
                    <Transition
                        enter-active-class="transition-all duration-200 ease-out"
                        enter-from-class="opacity-0 max-h-0"
                        enter-to-class="opacity-100 max-h-96"
                        leave-active-class="transition-all duration-150 ease-in"
                        leave-from-class="opacity-100 max-h-96"
                        leave-to-class="opacity-0 max-h-0"
                    >
                        <div v-if="openFaq === index" class="px-6 pb-5 overflow-hidden">
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">{{ faq.a }}</p>
                        </div>
                    </Transition>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="bg-gradient-to-br from-ocean-600 via-ocean-500 to-ocean-700 py-24 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center space-y-6">
            <h2 class="text-4xl font-bold text-white">{{ t('cta.getStarted') }}</h2>
            <p class="text-xl text-white/80">{{ t('cta.getStartedSubheading') }}</p>
            <Link
                href="/demo"
                class="inline-flex items-center gap-2 bg-white text-ocean-900 px-8 py-4 rounded-xl font-semibold hover:bg-ocean-50 transition-all shadow-xl"
            >
                {{ t('cta.button') }}
            </Link>
        </div>
    </section>
</template>
