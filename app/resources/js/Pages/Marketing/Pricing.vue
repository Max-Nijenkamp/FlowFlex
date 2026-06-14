<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import CtaBand from '@/Components/Marketing/CtaBand.vue'
import Kicker from '@/Components/Marketing/Kicker.vue'
import Receipt from '@/Components/Marketing/Receipt.vue'
import SectionTag from '@/Components/Marketing/SectionTag.vue'
import Switch from '@/Components/UI/Switch.vue'
import { domainColors } from '@/data/marketing'
import { Head, Link } from '@inertiajs/vue3'
import { computed, reactive, ref } from 'vue'

defineOptions({ layout: MarketingLayout })

const props = defineProps<{
    modules: { key: string; name: string; domain: string; price_cents: number }[]
    base_price_cents: number
    open_domain?: string | null
}>()

const users = ref(80)
const selected = ref<string[]>(
    props.modules.filter((m) => ['hr.profiles', 'hr.leave', 'finance.invoicing', 'crm.deals'].includes(m.key)).map((m) => m.key),
)

// Optimistic selection — instant local state, no server round-trip.
function toggle(key: string) {
    selected.value = selected.value.includes(key)
        ? selected.value.filter((k) => k !== key)
        : [...selected.value, key]
}

const domainLabels: Record<string, string> = {
    core: 'Core platform',
    hr: 'HR & people',
    finance: 'Finance & accounting',
    crm: 'CRM & sales',
}

const byDomain = computed(() => {
    const groups: Record<string, typeof props.modules> = {}
    for (const m of props.modules) (groups[m.domain] ??= []).push(m)
    return groups
})

// Deep-linked domain (?domain=) opens; otherwise HR by default (dropdown UX).
const openDomains = reactive<Record<string, boolean>>({
    core: props.open_domain === 'core',
    hr: props.open_domain ? props.open_domain === 'hr' : true,
    finance: props.open_domain === 'finance',
    crm: props.open_domain === 'crm',
})

const chosenModules = computed(() => props.modules.filter((m) => selected.value.includes(m.key)))
const perUserCents = computed(() => props.base_price_cents + chosenModules.value.reduce((sum, m) => sum + m.price_cents, 0))
const monthlyCents = computed(() => perUserCents.value * users.value)

function domainSelectedCount(domain: string) {
    return chosenModules.value.filter((m) => m.domain === domain).length
}

function domainSubtotalCents(domain: string) {
    return chosenModules.value.filter((m) => m.domain === domain).reduce((sum, m) => sum + m.price_cents, 0)
}

const euro = (cents: number) => `€${(cents / 100).toLocaleString('nl-NL', { minimumFractionDigits: 2 })}`
const euroShort = (cents: number) => `€${Math.round(cents / 100).toLocaleString('nl-NL')}`

const faq = [
    { q: 'What happens when I deactivate a module?', a: 'Billing stops at the end of the month. Your data stays — reactivate and pick up where you left off, or export it.' },
    { q: 'Do prices change as we grow?', a: 'The per-module price stays the same at 50 or 500 users. You pay for more seats, not a higher tier.' },
    { q: 'Can we take our data out?', a: 'Yes — full export, any day, no exit fee. Data portability is a baseline feature, not an enterprise add-on.' },
    { q: 'Is there a free trial?', a: 'Yes. Every workspace starts on a trial with all activated modules accessible, so you can test the real thing.' },
    { q: 'What counts as an active user?', a: 'Anyone who can sign in. Deactivated employees stay in your records but are never billed.' },
    { q: 'Are there hidden platform fees?', a: 'No. Core platform — login, roles, notifications, audit log, file storage — is always on and always free.' },
]
</script>

<template>
    <Head title="Pricing">
        <meta name="description"
            content="No tiers, no bundles. Your invoice is the sum of the modules you switched on, times your team size." />
    </Head>

    <!-- Hero -->
    <section class="bg-bloom border-b border-line">
        <div class="mx-auto max-w-6xl px-6 pt-14 pb-14 md:pt-[84px] md:pb-[72px]">
            <Kicker>Pricing</Kicker>
            <h1 class="mt-[26px] max-w-[760px] font-display text-[40px] font-bold leading-[1.02] tracking-[-0.03em] md:text-[62px]">
                No tiers. No bundles.<br><span class="text-accent">One formula.</span>
            </h1>
            <p class="mt-[22px] max-w-[500px] text-base leading-[1.65] text-ink-soft md:text-lg">
                Your invoice is the sum of the modules you switched on, times the people on your team. That's it.
            </p>
            <p class="mt-[30px] inline-block rounded-xl bg-ink px-6 py-3.5 font-mono text-[12px] text-white md:text-[15px]">
                invoice = <span class="text-[#A5A3FF]">Σ(module price)</span> × <span class="text-flow">active users</span>
            </p>
        </div>
    </section>

    <!-- 01 / Calculator -->
    <section class="border-b border-line bg-card">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[104px]">
            <div class="grid items-start gap-12 lg:grid-cols-[1fr_380px]">
                <div class="flex flex-col gap-3.5">
                    <!-- Core platform — always on, always free -->
                    <div class="overflow-hidden rounded-[14px] border border-line-strong bg-card shadow-[0_1px_2px_rgba(17,24,39,0.03)]">
                        <button type="button" class="flex w-full items-center justify-between gap-3 px-[22px] py-4 text-left"
                            :class="openDomains.core && 'border-b border-line'" @click="openDomains.core = !openDomains.core">
                            <span class="flex items-center gap-3">
                                <span class="h-[11px] w-[11px] rounded-[3px] bg-[#94A3B8]" />
                                <span class="font-display text-[15.5px] font-bold">Core platform</span>
                                <span class="font-mono text-[11px] text-ink-faint">{{ (byDomain.core ?? []).length }} modules</span>
                            </span>
                            <span class="flex items-center gap-3.5">
                                <span class="font-mono text-[10.5px] text-ink-faint">always on, always free</span>
                                <svg class="h-[13px] w-[13px] transition-transform" :class="openDomains.core && 'rotate-180'"
                                    viewBox="0 0 16 16" fill="none" stroke="#98A0AB" stroke-width="1.8" stroke-linecap="round">
                                    <path d="M4 6l4 4 4-4" />
                                </svg>
                            </span>
                        </button>
                        <div v-if="openDomains.core" class="grid gap-2.5 p-[18px] sm:grid-cols-2">
                            <div v-for="m in byDomain.core" :key="m.key"
                                class="flex items-center justify-between gap-2.5 rounded-[10px] border border-line bg-paper-deep/60 px-3.5 py-[11px]">
                                <span class="flex items-center gap-2.5 text-sm font-semibold text-ink-soft">
                                    <Switch on sm />
                                    {{ m.name }}
                                </span>
                                <span class="whitespace-nowrap font-mono text-[11.5px] text-ink-faint">included</span>
                            </div>
                        </div>
                    </div>

                    <!-- Paid domain groups -->
                    <div v-for="domain in ['hr', 'finance', 'crm']" :key="domain"
                        class="overflow-hidden rounded-[14px] border border-line-strong bg-card shadow-[0_1px_2px_rgba(17,24,39,0.03)]">
                        <button type="button" class="flex w-full items-center justify-between gap-3 px-[22px] py-4 text-left"
                            :class="openDomains[domain] && 'border-b border-line'"
                            @click="openDomains[domain] = !openDomains[domain]">
                            <span class="flex items-center gap-3">
                                <span class="h-[11px] w-[11px] rounded-[3px]" :style="{ background: domainColors[domain] }" />
                                <span class="font-display text-[15.5px] font-bold">{{ domainLabels[domain] }}</span>
                                <span class="font-mono text-[11px] text-ink-faint">{{ (byDomain[domain] ?? []).length }} modules</span>
                            </span>
                            <span class="flex items-center gap-3.5">
                                <span v-if="domainSelectedCount(domain)"
                                    class="rounded-full bg-accent-soft px-2.5 py-[3px] text-[11.5px] font-bold text-accent">
                                    {{ domainSelectedCount(domain) }} on
                                </span>
                                <span v-if="domainSubtotalCents(domain)" class="font-mono text-[11.5px] text-ink-soft">
                                    +{{ euro(domainSubtotalCents(domain)) }}/user
                                </span>
                                <svg class="h-[13px] w-[13px] transition-transform" :class="openDomains[domain] && 'rotate-180'"
                                    viewBox="0 0 16 16" fill="none" stroke="#98A0AB" stroke-width="1.8" stroke-linecap="round">
                                    <path d="M4 6l4 4 4-4" />
                                </svg>
                            </span>
                        </button>
                        <div v-if="openDomains[domain]" class="grid gap-2.5 p-[18px] sm:grid-cols-2">
                            <button v-for="m in byDomain[domain]" :key="m.key" type="button"
                                class="flex items-center justify-between gap-2.5 rounded-[10px] border px-3.5 py-[11px] text-left transition ease-out duration-150"
                                :class="selected.includes(m.key) ? 'border-accent/45 bg-accent-soft' : 'border-line bg-card hover:border-ink-faint/60'"
                                @click="toggle(m.key)">
                                <span class="flex items-center gap-2.5 text-sm font-semibold">
                                    <Switch :on="selected.includes(m.key)" sm />
                                    {{ m.name }}
                                </span>
                                <span class="whitespace-nowrap font-mono text-[11.5px] text-ink-faint">
                                    {{ m.price_cents === 0 ? 'included' : euro(m.price_cents) }}
                                </span>
                            </button>
                        </div>
                    </div>
                    <p class="px-1 py-2 font-mono text-xs text-ink-faint">
                        + 12 more departments in the marketplace once your workspace is live
                    </p>
                </div>

                <!-- Sticky receipt -->
                <aside class="lg:sticky lg:top-24">
                    <Receipt title="YOUR MONTHLY INVOICE">
                        <div class="h-[18px]" />
                        <div class="font-sans">
                            <label class="block">
                                <span class="flex justify-between text-[13.5px] font-semibold">
                                    <span>Team size</span>
                                    <span class="font-mono font-bold">{{ users }} people</span>
                                </span>
                                <input v-model.number="users" type="range" min="10" max="500" step="5"
                                    class="mt-3 w-full accent-accent" />
                            </label>
                            <div class="mt-1 flex justify-between font-mono text-[10.5px] text-ink-faint">
                                <span>10</span><span>500</span>
                            </div>
                        </div>
                        <div class="h-[18px]" />
                        <div class="mb-1.5 flex justify-between gap-4 whitespace-nowrap border-b border-dashed border-line-strong py-[7px] font-bold text-ink">
                            <span>module</span><span>/user</span>
                        </div>
                        <div v-for="m in chosenModules" :key="m.key"
                            class="flex justify-between gap-4 py-[7px] text-ink-soft">
                            <span class="truncate">{{ m.name }}</span><span>{{ euro(m.price_cents) }}</span>
                        </div>
                        <div class="flex justify-between gap-4 whitespace-nowrap py-[7px] text-ink-faint">
                            <span>Core platform</span><span>{{ euro(base_price_cents) }}</span>
                        </div>
                        <div class="mt-2 flex justify-between gap-4 whitespace-nowrap border-t border-dashed border-line-strong pt-3.5 pb-[7px] text-base font-bold text-ink">
                            <span>{{ euro(perUserCents) }} × {{ users }}</span><span>{{ euroShort(monthlyCents) }}</span>
                        </div>
                        <div class="h-4" />
                        <Link href="/contact"
                            class="flex w-full items-center justify-center rounded-[10px] bg-accent px-6 py-3 font-sans text-[15px] font-semibold text-white shadow-[0_1px_2px_rgba(79,70,229,0.4),0_8px_20px_-10px_rgba(79,70,229,0.5)] transition ease-out duration-150 hover:bg-accent-deep active:scale-[0.98]">
                            Talk to us
                        </Link>
                        <div class="h-2.5" />
                        <p class="text-center text-[10.5px] text-ink-faint">change modules any month · no contracts</p>
                    </Receipt>
                </aside>
            </div>
        </div>
    </section>

    <!-- 02 / Fair print -->
    <section class="border-b border-line">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[104px]">
            <SectionTag num="02" label="FAIR PRINT" />
            <h2 class="mt-4 max-w-[640px] font-display text-3xl font-bold leading-[1.06] tracking-display md:text-[42px]">
                The fine print, minus the fine.
            </h2>
            <dl class="mt-12 border-t border-line">
                <div v-for="f in faq" :key="f.q"
                    class="grid gap-2 border-b border-line py-6 md:grid-cols-[1fr_1.4fr] md:gap-8">
                    <dt class="font-display text-base font-bold tracking-[-0.01em]">{{ f.q }}</dt>
                    <dd class="text-[14.5px] leading-[1.65] text-ink-soft">{{ f.a }}</dd>
                </div>
            </dl>
        </div>
    </section>

    <CtaBand title="Your number is one minute away."
        sub="Pick your modules, set your team size, and the receipt writes itself." cta="Talk to us" href="/contact" />
</template>
