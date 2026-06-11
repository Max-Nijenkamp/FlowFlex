<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import Accordion from '@/Components/UI/Accordion.vue'
import { Link } from '@inertiajs/vue3'
import { computed, reactive, ref } from 'vue'

defineOptions({ layout: MarketingLayout })

const props = defineProps<{
    modules: { key: string; name: string; domain: string; price_cents: number }[]
    base_price_cents: number
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

// First domain open by default; rest collapsed (dropdown UX).
const openDomains = reactive<Record<string, boolean>>({ core: false, hr: true, finance: false, crm: false })

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
</script>

<template>
    <section class="mx-auto max-w-6xl px-6 pt-20 pb-10">
        <p class="section-index">PRICING</p>
        <h1 class="mt-4 max-w-2xl text-4xl sm:text-6xl font-bold tracking-display text-balance">
            No tiers. No bundles. One formula.
        </h1>
        <p class="mt-6 max-w-xl text-lg text-ink-soft leading-relaxed">
            Your invoice is the sum of the modules you switched on, times the people on your team. That's it.
        </p>
        <p class="mt-8 inline-block rounded-lg bg-ink px-5 py-3 font-mono text-sm text-white">
            monthly invoice = Σ(module price) × active users
        </p>
    </section>

    <section class="mx-auto max-w-6xl px-6 pb-24">
        <div class="grid gap-10 lg:grid-cols-[1fr_360px] lg:items-start">
            <!-- Domain accordions -->
            <div class="space-y-4">
                <Accordion v-for="(mods, domain) in byDomain" :key="domain" v-model:open="openDomains[domain]">
                    <template #header>
                        <div class="flex flex-1 items-center justify-between gap-4">
                            <div>
                                <span class="font-semibold">{{ domainLabels[domain] ?? domain }}</span>
                                <span class="ml-3 font-mono text-xs text-ink-faint">{{ mods.length }} modules</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm">
                                <span v-if="domainSelectedCount(domain)"
                                    class="rounded-full bg-accent-soft px-2.5 py-0.5 text-xs font-semibold text-accent">
                                    {{ domainSelectedCount(domain) }} selected
                                </span>
                                <span v-if="domainSubtotalCents(domain)" class="font-mono text-xs text-ink-soft">
                                    +{{ euro(domainSubtotalCents(domain)) }}/user
                                </span>
                            </div>
                        </div>
                    </template>
                    <div class="grid gap-2 sm:grid-cols-2">
                        <button v-for="m in mods" :key="m.key" type="button" @click="toggle(m.key)"
                            class="group flex items-center justify-between rounded-xl border px-4 py-3.5 text-left transition ease-out duration-150"
                            :class="selected.includes(m.key)
                                ? 'border-accent bg-accent-soft'
                                : 'border-line bg-white hover:border-ink-faint/60'">
                            <span class="flex items-center gap-3">
                                <span class="flex h-[18px] w-[18px] items-center justify-center rounded-full border transition ease-out duration-150"
                                    :class="selected.includes(m.key) ? 'border-accent bg-accent' : 'border-line bg-white group-hover:border-ink-faint/60'">
                                    <svg v-if="selected.includes(m.key)" class="h-2.5 w-2.5 text-white" viewBox="0 0 10 10" fill="none">
                                        <path d="M1.5 5.5L4 8L8.5 2.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                    </svg>
                                </span>
                                <span class="text-sm font-medium">{{ m.name }}</span>
                            </span>
                            <span class="font-mono text-xs text-ink-faint">
                                {{ m.price_cents === 0 ? 'included' : euro(m.price_cents) }}
                            </span>
                        </button>
                    </div>
                </Accordion>
            </div>

            <!-- Live invoice -->
            <aside class="lg:sticky lg:top-24">
                <div class="rounded-2xl border border-line bg-white shadow-[0_2px_12px_rgba(17,24,39,0.05)]">
                    <div class="border-b border-line px-6 py-4">
                        <h3 class="font-semibold">Your monthly invoice</h3>
                    </div>
                    <div class="px-6 py-5">
                        <label class="block text-sm text-ink-soft">
                            <span class="flex justify-between">
                                <span>Team size</span>
                                <span class="font-mono font-semibold text-ink">{{ users }} people</span>
                            </span>
                            <input v-model.number="users" type="range" min="10" max="500" step="5"
                                class="mt-3 w-full accent-[#4f46e5]" />
                        </label>

                        <dl class="mt-6 space-y-1.5 font-mono text-[13px]">
                            <div class="flex justify-between text-ink-soft">
                                <dt>Base platform</dt>
                                <dd>{{ euro(base_price_cents) }}</dd>
                            </div>
                            <div v-for="m in chosenModules.filter((m) => m.price_cents > 0)" :key="m.key"
                                class="flex justify-between text-ink-soft">
                                <dt class="truncate pr-4">{{ m.name }}</dt>
                                <dd>{{ euro(m.price_cents) }}</dd>
                            </div>
                            <div class="flex justify-between border-t border-line pt-2 font-semibold text-ink">
                                <dt>Per user</dt>
                                <dd>{{ euro(perUserCents) }}</dd>
                            </div>
                        </dl>

                        <div class="mt-6 rounded-xl bg-paper-deep px-5 py-4">
                            <div class="text-sm text-ink-soft">{{ euro(perUserCents) }} × {{ users }} users</div>
                            <div class="mt-1 text-3xl font-bold tracking-tight">
                                {{ euroShort(monthlyCents) }}<span class="text-base font-normal text-ink-faint">/month</span>
                            </div>
                        </div>

                        <Link href="/contact"
                            class="mt-5 block rounded-full bg-ink px-6 py-3 text-center font-semibold text-white hover:bg-accent transition ease-out duration-150 active:scale-[0.98]">
                            Talk to us
                        </Link>
                        <p class="mt-3 text-center text-xs text-ink-faint">Change modules any month. No contracts.</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <!-- FAQ -->
    <section class="border-t border-line bg-white">
        <div class="mx-auto max-w-3xl px-6 py-20">
            <h2 class="text-2xl font-bold tracking-display">Fair print</h2>
            <dl class="mt-8 divide-y divide-line">
                <div class="py-5">
                    <dt class="font-semibold">What happens when I deactivate a module?</dt>
                    <dd class="mt-1.5 text-sm text-ink-soft leading-relaxed">
                        Billing stops at the end of the month. Your data stays — reactivate and pick up where you left off, or export it.
                    </dd>
                </div>
                <div class="py-5">
                    <dt class="font-semibold">Do prices change as we grow?</dt>
                    <dd class="mt-1.5 text-sm text-ink-soft leading-relaxed">
                        The per-module price stays the same at 50 or 500 users. You pay for more seats, not a higher tier.
                    </dd>
                </div>
                <div class="py-5">
                    <dt class="font-semibold">Can we take our data out?</dt>
                    <dd class="mt-1.5 text-sm text-ink-soft leading-relaxed">
                        Yes — full export, any day, no exit fee. Data portability is a baseline feature, not an enterprise add-on.
                    </dd>
                </div>
            </dl>
        </div>
    </section>
</template>
