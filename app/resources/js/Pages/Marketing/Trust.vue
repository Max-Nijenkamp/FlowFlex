<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import BlueprintCell from '@/Components/Marketing/BlueprintCell.vue'
import CtaBand from '@/Components/Marketing/CtaBand.vue'
import Kicker from '@/Components/Marketing/Kicker.vue'
import SectionTag from '@/Components/Marketing/SectionTag.vue'
import Reveal from '@/Components/UI/Reveal.vue'
import { Head, Link } from '@inertiajs/vue3'

defineOptions({ layout: MarketingLayout })

const cells = [
    { big: 'EU', title: 'Hosted in the EU, full stop', body: 'Application, database and backups live in EU regions. Your data does not leave the EU for processing.' },
    { big: 'GDPR', title: 'Compliance as product features', body: 'Data subject access requests, consent records and erasure cascades are built in — handled in the product, not by a lawyer with a spreadsheet.' },
    { big: '2FA', title: 'Two-factor for everyone', body: 'TOTP two-factor on every account, with recovery codes. Owners can make it mandatory for the whole workspace.' },
    { big: '100%', title: 'Everything audited', body: 'Every change to every record carries who, what and when. The audit log is itself a module you can read.' },
    { big: 'AES', title: 'Sensitive fields encrypted', body: 'National IDs, salaries, IBANs and dates of birth are encrypted at the column level — invisible even in a database dump.' },
    { big: '90d', title: 'Cancelling is safe', body: 'A cancelled workspace stays exportable for 90 days, then is permanently purged. Your data is never the hostage.' },
]

const trust = ['EU-hosted', 'GDPR-compliant, DSAR built in', 'Two-factor authentication', 'Full audit log', 'Daily encrypted backups', 'Export your data any day']
</script>

<template>
    <Head title="Trust & security">
        <meta name="description"
            content="How FlowFlex handles your company's data: EU hosting, GDPR by construction, two-factor, column-level encryption, full audit log." />
    </Head>

    <!-- Hero -->
    <section class="bg-bloom border-b border-line">
        <div class="mx-auto max-w-6xl px-6 pt-14 pb-16 md:pt-[84px] md:pb-[76px]">
            <Kicker>Trust</Kicker>
            <h1 class="mt-[26px] max-w-[820px] font-display text-[40px] font-bold leading-[1.02] tracking-[-0.03em] md:text-[56px]">
                Your whole company runs here.<br>
                We treat it <span class="[box-shadow:inset_0_-0.16em_0_#C7D2FE]">that way</span>.
            </h1>
            <p class="mt-[22px] max-w-[520px] text-base leading-[1.65] text-ink-soft md:text-lg">
                One platform holding HR, finance and customer data is only a good idea if it is built like it.
                This page is the unglamorous list of how.
            </p>
        </div>
    </section>

    <!-- 01 / Security -->
    <section class="border-b border-line bg-card">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[104px]">
            <Reveal>
                <SectionTag num="01" label="SECURITY" />
                <h2 class="mt-4 max-w-[640px] font-display text-3xl font-bold leading-[1.06] tracking-display md:text-[42px]">
                    The unglamorous list.
                </h2>
            </Reveal>
            <Reveal :delay="100">
                <div class="mt-[52px] grid gap-px border border-line-strong bg-line-strong sm:grid-cols-2 md:grid-cols-3">
                    <BlueprintCell v-for="c in cells" :key="c.title" :big="c.big" :title="c.title" :body="c.body" />
                </div>
            </Reveal>
        </div>
    </section>

    <!-- 02 / Operations -->
    <section class="border-b border-line">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[104px]">
            <Reveal>
                <SectionTag num="02" label="OPERATIONS" />
                <h2 class="mt-4 max-w-[640px] font-display text-3xl font-bold leading-[1.06] tracking-display md:text-[42px]">
                    Boring on purpose.
                </h2>
                <div class="mt-10 max-w-[640px] space-y-5 text-[16px] leading-[1.7] text-ink-soft">
                    <p>
                        Backups run daily, encrypted, to a second EU region — and we restore from them routinely,
                        because a backup you never restored is a hope, not a plan.
                    </p>
                    <p>
                        Staff access to the platform console is invite-only, two-factor enforced, IP-restricted in
                        production and fully audited. Nobody at FlowFlex reads your data to "check something".
                    </p>
                    <p>
                        Live system health is public on the
                        <Link href="/status" class="font-semibold text-accent hover:underline">status page</Link> —
                        the same checks our own alerting runs on.
                    </p>
                </div>
            </Reveal>
        </div>
    </section>

    <!-- Trust strip -->
    <section class="bg-bloom border-b border-line">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-center gap-x-9 gap-y-3 px-6 py-16 font-mono text-[12.5px] text-ink-soft">
            <template v-for="(t, i) in trust" :key="t">
                <span>{{ t }}</span>
                <span v-if="i < trust.length - 1" class="text-line-strong">·</span>
            </template>
        </div>
    </section>

    <CtaBand title="Questions a checklist can't answer?"
        sub="Security reviews, DPAs, processor lists — send them over and a human replies within one business day."
        cta="Contact us" href="/contact" />
</template>
