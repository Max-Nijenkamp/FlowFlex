<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import LegalPage from '@/Components/Marketing/LegalPage.vue'
import { Head } from '@inertiajs/vue3'

defineOptions({ layout: MarketingLayout })

defineProps<{ content?: string }>()

const sections = [
    { id: 'service', heading: '1. The service', body: 'FlowFlex provides a modular business platform. Modules are activated per workspace; pricing is per active user, per active module, per month.' },
    { id: 'billing', heading: '2. Billing', body: 'Your invoice is calculated at the start of each billing month from the modules active in your workspace and your active user count. Deactivating a module stops billing at the end of the current month.' },
    { id: 'data', heading: '3. Your data', body: 'Your data remains yours. A full export is available at any time from company settings. Deactivating a module gates access but never deletes data.' },
    { id: 'cancellation', heading: '4. Cancellation', body: 'Cancelling archives your workspace. Data is retained for 90 days for export, then permanently purged in line with our GDPR retention policy.' },
    { id: 'privacy', heading: '5. Privacy & GDPR', body: 'FlowFlex is hosted in the EU and GDPR-compliant. Data subject access requests are processed via the Legal domain and anonymise data across all modules.' },
]
</script>

<template>
    <Head title="Terms of service">
        <meta name="description" content="The terms that govern your use of FlowFlex." />
    </Head>
    <LegalPage title="Terms of service" updated="11 June 2026"
        short-version="you pay for the modules you switch on, your data is always yours and always exportable, and cancelling never holds your data hostage."
        :sections="sections" />
</template>
