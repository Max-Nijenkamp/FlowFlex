<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import LegalPage from '@/Components/Marketing/LegalPage.vue'
import { Head } from '@inertiajs/vue3'

defineOptions({ layout: MarketingLayout })

defineProps<{ content?: string }>()

const sections = [
    { id: 'collect', heading: '1. What we collect', body: 'Only what the service needs: account details, the business data your workspace stores, and operational logs. We never sell data and we run no third-party ad trackers.' },
    { id: 'hosting', heading: '2. Where it lives', body: 'FlowFlex is hosted in the EU. Your data does not leave the EU for processing, and backups stay inside the same region.' },
    { id: 'gdpr', heading: '3. GDPR rights', body: 'Data subject access requests, consent records and erasure cascades are product features, not paperwork. DSARs are processed via the Legal domain and anonymise data across all modules.' },
    { id: 'retention', heading: '4. Retention', body: 'A cancelled workspace is retained for 90 days for export, then permanently purged. Module deactivation gates access but never deletes data.' },
    { id: 'contact', heading: '5. Questions', body: 'Privacy questions reach a human, not a queue — contact us and we reply within one business day.' },
]
</script>

<template>
    <Head title="Privacy policy">
        <meta name="description" content="How FlowFlex handles your data — EU-hosted and GDPR-first." />
    </Head>
    <LegalPage title="Privacy policy" updated="11 June 2026"
        short-version="we collect only what the service needs, everything is hosted in the EU, and GDPR rights are product features — not paperwork."
        :sections="sections" />
</template>
