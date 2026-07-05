<script setup lang="ts">
import { computed, reactive, ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import MarketingLayout from '../../Components/Layout/MarketingLayout.vue'
import CtaBand from '../../Components/Marketing/CtaBand.vue'
import FfSwitch from '../../Components/UI/FfSwitch.vue'
import Reveal from '../../Components/UI/Reveal.vue'
import { domainColors, euro } from '../../data/marketing'

defineOptions({ layout: MarketingLayout })

interface CalcModule {
    name: string
    price: number
    on: boolean
    locked?: boolean
}

interface CalcGroup {
    key: string
    name: string
    note?: string
    open: boolean
    mods: CalcModule[]
}

const groups = reactive<CalcGroup[]>([
    {
        key: 'core', name: 'Core platform', note: 'always on, always free', open: false,
        mods: [
            { name: 'Authentication & identity', price: 0, on: true, locked: true },
            { name: 'Notifications', price: 0, on: true, locked: true },
            { name: 'Audit log', price: 0, on: true, locked: true },
            { name: 'Roles & permissions', price: 0, on: true, locked: true },
        ],
    },
    {
        key: 'hr', name: 'HR & people', open: true,
        mods: [
            { name: 'Employee profiles', price: 0, on: true },
            { name: 'Leave & absence', price: 150, on: true },
            { name: 'Payroll', price: 250, on: false },
            { name: 'Recruiting', price: 150, on: false },
            { name: 'Onboarding', price: 100, on: false },
            { name: 'Time tracking', price: 100, on: false },
        ],
    },
    {
        key: 'finance', name: 'Finance & accounting', open: false,
        mods: [
            { name: 'Invoicing', price: 200, on: true },
            { name: 'Expenses', price: 100, on: false },
            { name: 'AP / AR', price: 150, on: false },
            { name: 'Reporting', price: 100, on: false },
        ],
    },
    {
        key: 'crm', name: 'CRM & sales', open: false,
        mods: [
            { name: 'Contacts', price: 0, on: true },
            { name: 'Pipeline', price: 150, on: true },
            { name: 'Deals & quotes', price: 150, on: false },
        ],
    },
])

const teamSize = ref(80)

const selectedLines = computed(() =>
    groups
        .filter((g) => g.key !== 'core')
        .flatMap((g) => g.mods.filter((m) => m.on))
        .sort((a, b) => a.name.localeCompare(b.name)),
)
const perUser = computed(() => selectedLines.value.reduce((s, m) => s + m.price, 0))
const monthly = computed(() => Math.round((perUser.value * teamSize.value) / 100))

const groupSubtotal = (g: CalcGroup): number => g.mods.filter((m) => m.on).reduce((s, m) => s + m.price, 0)
const groupOnCount = (g: CalcGroup): number => g.mods.filter((m) => m.on).length

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
    <Head>
        <title>Pricing</title>
        <meta name="description" content="No tiers. No bundles. One formula: the modules you switched on, times the people on your team." />
    </Head>

    <!-- Hero -->
    <section class="ff-hero ff-grid-bg" style="padding-bottom: 72px">
        <div class="wrap">
            <span class="ff-kicker"><span class="sq"></span>Pricing</span>
            <h1 style="max-width: 760px">No tiers. No bundles.<br /><span class="acc">One formula.</span></h1>
            <p class="ff-lede">
                Your invoice is the sum of the modules you switched on, times the people on your team. That's it.
            </p>
            <div class="mono mt-8 inline-block rounded-xl px-6 py-3.5 text-[15px] max-md:text-[11.5px]" style="background: var(--ink); color: #fff">
                invoice = <span style="color: #A5A3FF">Σ(module price)</span> × <span style="color: var(--sky)">active users</span>
            </div>
        </div>
    </section>

    <!-- Calculator -->
    <section class="ff-section" style="background: var(--card)">
        <div class="wrap">
            <div class="grid items-start gap-10 lg:gap-12 lg:[grid-template-columns:1fr_380px]">
                <div class="flex flex-col gap-3.5">
                    <div
                        v-for="g in groups"
                        :key="g.key"
                        class="overflow-hidden rounded-[14px] border bg-(--card)"
                        style="border-color: var(--line-strong); box-shadow: 0 1px 2px rgba(17,24,39,0.03)"
                    >
                        <button
                            type="button"
                            class="flex w-full cursor-pointer items-center justify-between gap-3 px-5.5 py-4 text-left"
                            :style="{ borderBottom: g.open ? '1px solid var(--line)' : 'none' }"
                            :aria-expanded="g.open"
                            @click="g.open = !g.open"
                        >
                            <span class="flex items-center gap-3">
                                <span class="h-[11px] w-[11px] rounded-[3px]" :style="{ background: domainColors[g.key] ?? '#94A3B8' }"></span>
                                <span class="text-[15.5px] font-bold" style="font-family: var(--font-display)">{{ g.name }}</span>
                                <span class="mono text-[11px]" style="color: var(--ink-faint)">{{ g.mods.length }} modules</span>
                            </span>
                            <span class="flex items-center gap-3.5">
                                <span v-if="g.note" class="mono text-[10.5px]" style="color: var(--ink-faint)">{{ g.note }}</span>
                                <span
                                    v-if="!g.note && groupOnCount(g) > 0"
                                    class="rounded-full px-2.5 py-0.5 text-[11.5px] font-bold"
                                    style="color: var(--indigo); background: var(--indigo-soft)"
                                >{{ groupOnCount(g) }} on</span>
                                <span v-if="groupSubtotal(g) > 0" class="mono text-[11.5px]" style="color: var(--ink-soft)">+{{ euro(groupSubtotal(g)) }}/user</span>
                                <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="#98A0AB" stroke-width="1.8" stroke-linecap="round" :style="{ transform: g.open ? 'rotate(180deg)' : 'none', transition: 'transform 0.15s ease-out' }"><path d="M4 6l4 4 4-4" /></svg>
                            </span>
                        </button>
                        <div v-if="g.open" class="grid gap-2.5 p-4.5 md:grid-cols-2">
                            <button
                                v-for="m in g.mods"
                                :key="m.name"
                                type="button"
                                class="flex items-center justify-between gap-2.5 rounded-[10px] border px-3.5 py-2.75 text-left"
                                :class="{ 'cursor-pointer': !m.locked, 'cursor-default': m.locked }"
                                :style="{
                                    borderColor: m.on ? 'rgba(79,70,229,0.45)' : 'var(--line)',
                                    background: m.on ? 'var(--indigo-soft)' : 'var(--card)',
                                }"
                                :aria-pressed="m.on"
                                :disabled="m.locked"
                                @click="!m.locked && (m.on = !m.on)"
                            >
                                <span class="flex items-center gap-2.5 text-[14px] font-semibold">
                                    <FfSwitch :on="m.on" sm />
                                    {{ m.name }}
                                </span>
                                <span class="mono whitespace-nowrap text-[11.5px]" style="color: var(--ink-faint)">{{ m.price === 0 ? 'included' : euro(m.price) }}</span>
                            </button>
                        </div>
                    </div>
                    <p class="mono px-1 py-2 text-[12px]" style="color: var(--ink-faint)">
                        + 12 more departments in the marketplace once your workspace is live
                    </p>
                </div>

                <!-- Sticky receipt -->
                <div class="ff-receipt lg:sticky lg:top-24">
                    <div class="rt">YOUR MONTHLY INVOICE</div>
                    <div style="height: 18px"></div>
                    <div style="font-family: var(--font-sans)">
                        <div class="flex justify-between text-[13.5px] font-semibold">
                            <span>Team size</span>
                            <span class="mono font-bold">{{ teamSize }} people</span>
                        </div>
                        <input
                            v-model.number="teamSize"
                            type="range"
                            min="10"
                            max="500"
                            step="5"
                            class="mt-3 w-full accent-(--indigo)"
                            aria-label="Team size"
                        />
                        <div class="mono mt-1 flex justify-between text-[10.5px]" style="color: var(--ink-faint)">
                            <span>10</span><span>500</span>
                        </div>
                    </div>
                    <div style="height: 18px"></div>
                    <div class="rl head"><span>module</span><span>/user</span></div>
                    <div v-for="m in selectedLines" :key="m.name" class="rl">
                        <span>{{ m.name }}</span><span>{{ euro(m.price) }}</span>
                    </div>
                    <div class="rl"><span class="dim">Core platform</span><span class="dim">€0,00</span></div>
                    <div class="rl total">
                        <span>{{ euro(perUser) }} × {{ teamSize }}</span><span>€{{ monthly }}</span>
                    </div>
                    <div style="height: 16px"></div>
                    <Link href="/contact" class="ff-btn primary w-full">Talk to us</Link>
                    <div style="height: 10px"></div>
                    <div class="rl justify-center!" style="font-size: 10.5px; color: var(--ink-faint)">
                        change modules any month · no contracts
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 02 / Fair print -->
    <section class="ff-section">
        <div class="wrap">
            <Reveal>
                <p class="ff-tag"><b>02</b> / FAIR PRINT</p>
                <h2>The fine print, minus the fine.</h2>
                <dl class="ff-faq">
                    <div v-for="f in faq" :key="f.q" class="ff-faq-row">
                        <dt>{{ f.q }}</dt>
                        <dd>{{ f.a }}</dd>
                    </div>
                </dl>
            </Reveal>
        </div>
    </section>

    <CtaBand
        title="Your number is one minute away."
        sub="Pick your modules, set your team size, and the receipt writes itself."
        cta="Talk to us"
        href="/contact"
    />
</template>
