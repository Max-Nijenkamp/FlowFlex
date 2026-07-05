<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import MarketingLayout from '../../Components/Layout/MarketingLayout.vue'
import LegalPage from '../../Components/Marketing/LegalPage.vue'

defineOptions({ layout: MarketingLayout })

const sections: Array<[string, string]> = [
    ['1. The service', 'FlowFlex provides a modular business platform. Modules are activated per workspace; pricing is per active user, per active module, per month.'],
    ['2. Billing', 'Your invoice is calculated at the start of each billing month from the modules active in your workspace and your active user count. Deactivating a module stops billing at the end of the current month.'],
    ['3. Your data', 'Your data remains yours. A full export is available at any time from company settings. Deactivating a module gates access but never deletes data.'],
    ['4. Cancellation', 'Cancelling archives your workspace. Data is retained for 90 days for export, then permanently purged in line with our GDPR retention policy.'],
    ['5. Privacy & GDPR', 'FlowFlex is hosted in the EU and GDPR-compliant. Data subject access requests are processed via the Legal domain and anonymise data across all modules.'],
]
</script>

<template>
    <Head>
        <title>Terms of service</title>
        <meta name="description" content="FlowFlex terms of service — plain-language summary first, always." />
    </Head>
    <LegalPage
        title="Terms of service"
        updated="11 June 2026"
        short-version="you pay for the modules you switch on, your data is always yours and always exportable, and cancelling never holds your data hostage."
        :sections="sections"
    />
</template>
