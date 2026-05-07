<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import { CheckCircle, Clock, Users, ArrowRight, Shield } from 'lucide-vue-next'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const submitted = ref(false)

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    company_name: '',
    company_size: '',
    modules_interested: [] as string[],
    heard_from: '',
    notes: '',
    phone: '',
    consent: false,
})

function submit() {
    form.post('/demo', {
        onSuccess: () => {
            submitted.value = true
        },
    })
}

const companySizes = [
    '1–10 employees',
    '11–50 employees',
    '51–200 employees',
    '201–500 employees',
    '500+ employees',
]

const heardFromOptions = [
    'Google / Search',
    'LinkedIn',
    'Twitter / X',
    'Word of mouth / Referral',
    'Blog or article',
    'Newsletter',
    'Other',
]

const moduleOptions = [
    'HR & People',
    'Projects & Work',
    'Finance',
    'CRM & Sales',
    'Marketing',
    'Operations',
    'Analytics',
    'IT & Security',
    'Legal',
    'E-commerce',
    'Communications',
    'Learning & Dev',
    'Core Platform',
]

const sidebarItems = [
    { icon: Clock, titleKey: 'demo.session30min', descKey: 'demo.session30minDesc' },
    { icon: Users, titleKey: 'demo.noPressure', descKey: 'demo.noPressureDesc' },
    { icon: Shield, titleKey: 'demo.trialEnv', descKey: 'demo.trialEnvDesc' },
]
</script>

<template>
    <Head title="Request a Demo — FlowFlex">
        <meta name="description" content="Book a 30-minute demo of FlowFlex. We'll walk you through the modules that replace your current tool stack." />
    </Head>

    <!-- Header -->
    <div class="relative bg-[#050E1A] py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-ocean-950 via-[#071828] to-slate-950 pointer-events-none" />
        <div class="absolute top-0 right-0 w-[400px] h-[300px] bg-ocean-600/10 blur-[100px] rounded-full pointer-events-none" />
        <div class="relative max-w-4xl mx-auto text-center space-y-4">            <h1 class="text-5xl md:text-6xl font-black text-white tracking-tighter">{{ t('demo.heading') }}</h1>
            <p class="text-xl text-slate-400">{{ t('demo.subheading') }}</p>
        </div>
    </div>

    <!-- Content -->
    <div class="bg-slate-50 dark:bg-slate-900/50 py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">

            <!-- Success state -->
            <div v-if="submitted" class="max-w-2xl mx-auto text-center space-y-6 py-16">
                <div
                    class="w-20 h-20 rounded-full bg-success-500/10 flex items-center justify-center mx-auto"
                    v-motion
                    :initial="{ scale: 0, opacity: 0 }"
                    :enter="{ scale: 1, opacity: 1, transition: { duration: 500, type: 'spring' } }"
                >
                    <CheckCircle class="w-10 h-10 text-success-500" />
                </div>
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white">{{ t('demo.successTitle') }}</h2>
                <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed">
                    {{ t('demo.successMessage') }}
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                    <a href="/" class="text-ocean-600 dark:text-ocean-400 font-medium hover:underline underline-offset-4">
                        {{ t('demo.backHome') }}
                    </a>
                    <a href="/features" class="inline-flex items-center gap-2 bg-ocean-500 hover:bg-ocean-400 text-white px-6 py-3 rounded-xl font-semibold transition-colors">
                        {{ t('demo.exploreModules') }} <ArrowRight class="w-4 h-4" />
                    </a>
                </div>
            </div>

            <!-- Form + sidebar -->
            <div v-else class="grid lg:grid-cols-5 gap-12">

                <!-- Form (3/5) -->
                <div class="lg:col-span-3">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-8">
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6">{{ t('demo.yourDetails') }}</h2>
                        <form @submit.prevent="submit" class="space-y-5">

                            <!-- Name row -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ t('demo.firstName') }}</label>
                                    <input
                                        id="first_name"
                                        v-model="form.first_name"
                                        type="text"
                                        required
                                        class="w-full rounded-xl border border-slate-200 dark:border-slate-600 px-3 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-ocean-500 focus:border-transparent"
                                        placeholder="Max"
                                    />
                                    <p v-if="form.errors.first_name" class="text-danger-500 text-xs mt-1">{{ form.errors.first_name }}</p>
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ t('demo.lastName') }}</label>
                                    <input
                                        id="last_name"
                                        v-model="form.last_name"
                                        type="text"
                                        required
                                        class="w-full rounded-xl border border-slate-200 dark:border-slate-600 px-3 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-ocean-500 focus:border-transparent"
                                        placeholder="Nijenkamp"
                                    />
                                    <p v-if="form.errors.last_name" class="text-danger-500 text-xs mt-1">{{ form.errors.last_name }}</p>
                                </div>
                            </div>

                            <!-- Work email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ t('demo.workEmail') }}</label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-600 px-3 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-ocean-500 focus:border-transparent"
                                    placeholder="you@company.com"
                                />
                                <p v-if="form.errors.email" class="text-danger-500 text-xs mt-1">{{ form.errors.email }}</p>
                            </div>

                            <!-- Company -->
                            <div>
                                <label for="company_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ t('demo.companyName') }}</label>
                                <input
                                    id="company_name"
                                    v-model="form.company_name"
                                    type="text"
                                    required
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-600 px-3 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-ocean-500 focus:border-transparent"
                                    placeholder="Acme B.V."
                                />
                                <p v-if="form.errors.company_name" class="text-danger-500 text-xs mt-1">{{ form.errors.company_name }}</p>
                            </div>

                            <!-- Company size -->
                            <div>
                                <label for="company_size" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ t('demo.companySize') }}</label>
                                <select
                                    id="company_size"
                                    v-model="form.company_size"
                                    required
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-600 px-3 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-ocean-500 focus:border-transparent"
                                >
                                    <option value="" disabled>{{ t('demo.selectSize') }}</option>
                                    <option v-for="size in companySizes" :key="size" :value="size">{{ size }}</option>
                                </select>
                                <p v-if="form.errors.company_size" class="text-danger-500 text-xs mt-1">{{ form.errors.company_size }}</p>
                            </div>

                            <!-- Modules interested -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ t('demo.modulesInterested') }}</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    <label
                                        v-for="mod in moduleOptions"
                                        :key="mod"
                                        class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 cursor-pointer p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors"
                                    >
                                        <input
                                            type="checkbox"
                                            :value="mod"
                                            v-model="form.modules_interested"
                                            class="rounded border-slate-300 dark:border-slate-600 text-ocean-500 focus:ring-ocean-500"
                                        />
                                        {{ mod }}
                                    </label>
                                </div>
                                <p v-if="form.errors.modules_interested" class="text-danger-500 text-xs mt-1">{{ form.errors.modules_interested }}</p>
                            </div>

                            <!-- Heard from -->
                            <div>
                                <label for="heard_from" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ t('demo.heardFrom') }}</label>
                                <select
                                    id="heard_from"
                                    v-model="form.heard_from"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-600 px-3 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-ocean-500 focus:border-transparent"
                                >
                                    <option value="" disabled>{{ t('demo.selectOption') }}</option>
                                    <option v-for="option in heardFromOptions" :key="option" :value="option">{{ option }}</option>
                                </select>
                            </div>

                            <!-- Phone (optional) -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                    {{ t('demo.phone') }} <span class="text-slate-400 font-normal">{{ t('demo.phoneOptional') }}</span>
                                </label>
                                <input
                                    id="phone"
                                    v-model="form.phone"
                                    type="tel"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-600 px-3 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-ocean-500 focus:border-transparent"
                                    placeholder="+31 6 12345678"
                                />
                            </div>

                            <!-- Notes (optional) -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                    {{ t('demo.notes') }} <span class="text-slate-400 font-normal">{{ t('demo.notesOptional') }}</span>
                                </label>
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="3"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-600 px-3 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-ocean-500 focus:border-transparent resize-none"
                                    :placeholder="t('demo.notesPlaceholder')"
                                />
                            </div>

                            <!-- Consent -->
                            <div>
                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        v-model="form.consent"
                                        required
                                        class="mt-0.5 rounded border-slate-300 dark:border-slate-600 text-ocean-500 focus:ring-ocean-500"
                                    />
                                    <span class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                                        {{ t('demo.consent') }}
                                        <a href="/legal/privacy" class="text-ocean-600 dark:text-ocean-400 hover:underline">Privacy Policy</a>.
                                    </span>
                                </label>
                                <p v-if="form.errors.consent" class="text-danger-500 text-xs mt-1">{{ form.errors.consent }}</p>
                            </div>

                            <!-- Submit -->
                            <button
                                type="submit"
                                :disabled="form.processing || !form.consent"
                                class="w-full bg-ocean-500 hover:bg-ocean-400 disabled:bg-ocean-300 disabled:cursor-not-allowed text-white px-6 py-3 rounded-xl font-semibold transition-colors flex items-center justify-center gap-2 shadow-sm"
                            >
                                <span v-if="form.processing">{{ t('demo.submitting') }}</span>
                                <span v-else class="flex items-center gap-2">{{ t('demo.submitButton') }} <ArrowRight class="w-4 h-4" /></span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Sidebar (2/5) -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- What to expect -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 space-y-4">
                        <h3 class="font-bold text-slate-900 dark:text-white">{{ t('demo.whatToExpect') }}</h3>
                        <ul class="space-y-4">
                            <li v-for="item in sidebarItems" :key="item.titleKey" class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-xl bg-ocean-50 dark:bg-ocean-900/30 flex items-center justify-center shrink-0">
                                    <component :is="item.icon" class="w-4 h-4 text-ocean-600 dark:text-ocean-400" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ t(item.titleKey) }}</p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ t(item.descKey) }}</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Callout -->
                    <div class="bg-ocean-50 dark:bg-ocean-900/20 border border-ocean-100 dark:border-ocean-800/30 rounded-2xl p-6 space-y-3">
                        <div class="text-4xl font-bold text-ocean-600 dark:text-ocean-400">30 min</div>
                        <p class="text-slate-700 dark:text-slate-300 font-semibold">{{ t('demo.callout') }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            {{ t('demo.calloutDesc') }}
                        </p>
                    </div>

                    <!-- Response time -->
                    <div class="bg-slate-900 dark:bg-slate-950 rounded-2xl p-6 text-white space-y-2">
                        <p class="font-bold text-lg">{{ t('demo.responseTime') }}</p>
                        <p class="text-slate-400 text-sm">
                            {{ t('demo.responseTimeDesc') }}
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
