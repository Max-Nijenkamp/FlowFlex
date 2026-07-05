<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import MarketingLayout from '../../Components/Layout/MarketingLayout.vue'
import LegalPage from '../../Components/Marketing/LegalPage.vue'

defineOptions({ layout: MarketingLayout })

const sections: Array<[string, string]> = [
    ['1. What we collect', 'Account data (name, work email), workspace data your company puts into its modules, and the minimum technical logs needed to run the service securely. No advertising trackers, ever.'],
    ['2. Where it lives', 'All data is stored and processed inside the European Union. Subprocessors are EU-based or bound by EU standard contractual clauses; the current list is available on request.'],
    ['3. Who can see it', 'Your workspace data is visible only to the people your admins authorise, scoped by roles and per-module permissions. FlowFlex staff access is break-glass only and fully audited.'],
    ['4. Your rights', 'Access, rectification, erasure, portability — data subject requests are handled through your workspace admin or privacy@flowflex.eu and anonymise data across every module.'],
    ['5. Retention', 'Active workspace data is kept as long as your subscription runs. After cancellation your workspace is retained 90 days for export, then permanently purged.'],
    ['6. Cookies', 'One session cookie to keep you signed in and one CSRF token to keep forms safe. No third-party cookies, no cross-site tracking.'],
]
</script>

<template>
    <Head>
        <title>Privacy policy</title>
        <meta name="description" content="FlowFlex privacy policy — EU-hosted, GDPR-first, no advertising trackers." />
    </Head>
    <LegalPage
        title="Privacy policy"
        updated="11 June 2026"
        short-version="your data stays in the EU, we collect only what the service needs, there are no trackers, and every GDPR right works across all modules at once."
        :sections="sections"
    />
</template>
