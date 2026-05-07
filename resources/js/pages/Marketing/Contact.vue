<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import { CheckCircle, Mail, Briefcase, Wrench, CreditCard, Newspaper, Handshake, Lock } from 'lucide-vue-next'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const submitted = ref(false)

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
})

function submit() {
    form.post('/contact', {
        onSuccess: () => {
            submitted.value = true
        },
    })
}

const contactReasons = [
    {
        icon: Briefcase,
        titleKey: 'contact.reasons.sales.title',
        descKey: 'contact.reasons.sales.description',
        email: 'sales@flowflex.com',
        color: '#2199C8',
    },
    {
        icon: Wrench,
        titleKey: 'contact.reasons.support.title',
        descKey: 'contact.reasons.support.description',
        email: 'support@flowflex.com',
        color: '#059669',
    },
    {
        icon: CreditCard,
        titleKey: 'contact.reasons.billing.title',
        descKey: 'contact.reasons.billing.description',
        email: 'billing@flowflex.com',
        color: '#D97706',
    },
    {
        icon: Newspaper,
        titleKey: 'contact.reasons.press.title',
        descKey: 'contact.reasons.press.description',
        email: 'press@flowflex.com',
        color: '#7C3AED',
    },
    {
        icon: Handshake,
        titleKey: 'contact.reasons.partnerships.title',
        descKey: 'contact.reasons.partnerships.description',
        email: 'partners@flowflex.com',
        color: '#DB2777',
    },
    {
        icon: Lock,
        titleKey: 'contact.reasons.security.title',
        descKey: 'contact.reasons.security.description',
        email: 'security@flowflex.com',
        color: '#DC2626',
    },
]

const subjectOptions = [
    'General enquiry',
    'Sales / Pricing',
    'Technical support',
    'Billing',
    'Partnership',
    'Press / Media',
    'Security',
    'Other',
]
</script>

<template>
    <Head title="Contact — FlowFlex">
        <meta name="description" content="Get in touch with the FlowFlex team. Sales, support, billing, partnerships — we're here to help." />
    </Head>

    <!-- Hero -->
    <section class="relative bg-[#050E1A] py-24 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-ocean-950 to-[#050E1A] pointer-events-none" />
        <div class="absolute bottom-0 left-1/3 w-[400px] h-[300px] bg-ocean-500/8 blur-[100px] rounded-full pointer-events-none" />
        <div class="relative max-w-4xl mx-auto text-center space-y-4">            <h1 class="text-4xl sm:text-6xl font-black text-white tracking-tighter">{{ t('contact.heading') }}</h1>
            <p class="text-xl text-slate-400">{{ t('contact.subheading') }}</p>
        </div>
    </section>

    <!-- Contact reasons grid -->
    <section class="bg-white dark:bg-slate-900 py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white text-center mb-10">{{ t('contact.whoToContact') }}</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="reason in contactReasons"
                    :key="reason.titleKey"
                    class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 hover:shadow-md dark:hover:shadow-slate-900/50 hover:-translate-y-0.5 transition-all space-y-3"
                >
                    <div
                        class="w-10 h-10 rounded-xl flex items-center justify-center"
                        :style="`background: ${reason.color}18`"
                    >
                        <component :is="reason.icon" class="w-5 h-5" :style="`color: ${reason.color}`" />
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white">{{ t(reason.titleKey) }}</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ t(reason.descKey) }}</p>
                    <a
                        :href="`mailto:${reason.email}`"
                        class="flex items-center gap-2 text-sm text-ocean-600 dark:text-ocean-400 hover:text-ocean-700 dark:hover:text-ocean-300 font-medium transition-colors"
                    >
                        <Mail class="w-4 h-4" />
                        {{ reason.email }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- General contact form -->
    <section class="bg-slate-50 dark:bg-slate-800/50 py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white text-center mb-8">{{ t('contact.sendMessage') }}</h2>

            <!-- Success state -->
            <div v-if="submitted" class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-12 text-center space-y-6">
                <div class="w-16 h-16 rounded-full bg-success-500/10 flex items-center justify-center mx-auto">
                    <CheckCircle class="w-8 h-8 text-success-500" />
                </div>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ t('contact.successTitle') }}</h3>
                <p class="text-slate-600 dark:text-slate-400">
                    {{ t('contact.successMessage') }}
                </p>
            </div>

            <!-- Form -->
            <div v-else class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-8">
                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label for="contact_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ t('contact.yourName') }}</label>
                        <input
                            id="contact_name"
                            v-model="form.name"
                            type="text"
                            required
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 px-3 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-ocean-500 focus:border-transparent"
                            placeholder="Max Nijenkamp"
                        />
                        <p v-if="form.errors.name" class="text-danger-500 text-xs mt-1">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ t('contact.emailAddress') }}</label>
                        <input
                            id="contact_email"
                            v-model="form.email"
                            type="email"
                            required
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 px-3 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-ocean-500 focus:border-transparent"
                            placeholder="you@company.com"
                        />
                        <p v-if="form.errors.email" class="text-danger-500 text-xs mt-1">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label for="contact_subject" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ t('contact.subject') }}</label>
                        <select
                            id="contact_subject"
                            v-model="form.subject"
                            required
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 px-3 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-ocean-500 focus:border-transparent"
                        >
                            <option value="" disabled>{{ t('contact.selectSubject') }}</option>
                            <option v-for="opt in subjectOptions" :key="opt" :value="opt">{{ opt }}</option>
                        </select>
                        <p v-if="form.errors.subject" class="text-danger-500 text-xs mt-1">{{ form.errors.subject }}</p>
                    </div>

                    <div>
                        <label for="contact_message" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ t('contact.message') }}</label>
                        <textarea
                            id="contact_message"
                            v-model="form.message"
                            rows="5"
                            required
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 px-3 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-ocean-500 focus:border-transparent resize-none"
                            placeholder="Tell us how we can help..."
                        />
                        <p v-if="form.errors.message" class="text-danger-500 text-xs mt-1">{{ form.errors.message }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-ocean-500 hover:bg-ocean-400 disabled:bg-ocean-300 disabled:cursor-not-allowed text-white px-6 py-3 rounded-xl font-semibold transition-colors shadow-sm"
                    >
                        {{ form.processing ? t('contact.sending') : t('contact.send') }}
                    </button>
                </form>
            </div>
        </div>
    </section>
</template>
