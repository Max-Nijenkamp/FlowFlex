<script setup lang="ts">
import AppMock from '@/Components/Marketing/AppMock.vue'
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import FlowLine from '@/Components/Marketing/FlowLine.vue'
import Reveal from '@/Components/UI/Reveal.vue'
import SectionHeading from '@/Components/UI/SectionHeading.vue'
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'

defineOptions({ layout: MarketingLayout })

const props = defineProps<{
    domains: { name: string; modules: number }[]
    module_count: number
    sample_modules: { key: string; name: string; domain: string }[]
}>()

// Optimistic module toggling in the "flex" demo — pure client state.
const active = ref<string[]>(props.sample_modules.slice(0, 5).map((m) => m.key))

function toggle(key: string) {
    active.value = active.value.includes(key)
        ? active.value.filter((k) => k !== key)
        : [...active.value, key]
}

const flows = [
    { from: 'CRM', event: 'Deal won', to: 'Finance', effect: 'Draft invoice created with the deal value' },
    { from: 'Finance', event: 'Invoice paid', to: 'CRM', effect: 'Account lifetime value updates' },
    { from: 'HR', event: 'Offer accepted', to: 'Payroll', effect: 'Salary lands in the next payroll run' },
    { from: 'HR', event: 'Leave approved', to: 'Scheduling', effect: 'Shifts unassign, coverage gaps flagged' },
]
</script>

<template>
    <!-- ── Hero ── -->
    <section class="relative overflow-hidden">
        <div class="mx-auto max-w-6xl px-6 pt-20 pb-20 sm:pt-28">
            <div class="grid items-center gap-14 lg:grid-cols-[1.1fr_1fr]">
                <div>
                    <p class="section-index">EVERYTHING FLOWS</p>
                    <h1 class="mt-5 text-5xl sm:text-6xl font-bold tracking-display leading-[1.04] text-balance">
                        Run the whole company.<br />
                        <span class="text-accent">Drop the other 12 tools.</span>
                    </h1>
                    <p class="mt-7 max-w-xl text-lg leading-relaxed text-ink-soft">
                        HR, finance, CRM and {{ module_count }} more modules on one platform —
                        one login, one database, one bill. Activate a module when you need it.
                        Switch it off when you don't.
                    </p>
                    <div class="mt-9 flex flex-wrap items-center gap-4">
                        <Link href="/pricing"
                            class="rounded-full bg-ink px-7 py-3.5 font-semibold text-white hover:bg-accent transition ease-out duration-150 active:scale-[0.98]">
                            Build your price
                        </Link>
                        <Link href="/features" class="group font-semibold text-ink">
                            See what's inside
                            <span class="inline-block transition-transform ease-out duration-150 group-hover:translate-x-1">→</span>
                        </Link>
                    </div>
                    <p class="mt-6 text-sm text-ink-faint">For teams of 50–500 · per user, per module · no tiers, no lock-in</p>
                </div>
                <Reveal :delay="150" class="hidden lg:block">
                    <AppMock />
                </Reveal>
            </div>
        </div>
        <div class="absolute inset-x-0 bottom-0 h-24 pointer-events-none opacity-70">
            <FlowLine />
        </div>
    </section>

    <!-- ── 01 The problem ── -->
    <section class="border-t border-line bg-white">
        <div class="mx-auto max-w-6xl px-6 py-24">
            <div class="grid gap-14 lg:grid-cols-2 lg:items-start">
                <Reveal>
                    <SectionHeading index="01" eyebrow="The problem" title="Your company runs on a patchwork." >
                        <p class="mt-5 text-ink-soft leading-relaxed">
                            A 100-person company typically pays for 5 to 15 disconnected tools.
                            HR lives in one, invoices in another, deals in a third. Every gap between
                            them is filled with CSV exports, copy-paste and someone's Tuesday afternoon.
                        </p>
                    </SectionHeading>
                </Reveal>
                <Reveal :delay="120">
                    <dl class="grid grid-cols-2 gap-px bg-line border border-line">
                        <div class="bg-white p-7">
                            <dt class="text-sm text-ink-faint">Typical SaaS tools at 100 people</dt>
                            <dd class="mt-2 font-mono text-4xl font-bold tracking-tight">5–15</dd>
                        </div>
                        <div class="bg-white p-7">
                            <dt class="text-sm text-ink-faint">Logins your team juggles</dt>
                            <dd class="mt-2 font-mono text-4xl font-bold tracking-tight">1<span class="text-ink-faint text-2xl"> here</span></dd>
                        </div>
                        <div class="bg-white p-7">
                            <dt class="text-sm text-ink-faint">Integrations to maintain</dt>
                            <dd class="mt-2 font-mono text-4xl font-bold tracking-tight">0</dd>
                        </div>
                        <div class="bg-white p-7">
                            <dt class="text-sm text-ink-faint">Modules on FlowFlex today</dt>
                            <dd class="mt-2 font-mono text-4xl font-bold tracking-tight">{{ module_count }}</dd>
                        </div>
                    </dl>
                </Reveal>
            </div>
        </div>
    </section>

    <!-- ── 02 Flex: activate what you need ── -->
    <section class="border-t border-line">
        <div class="mx-auto max-w-6xl px-6 py-24">
            <Reveal>
                <SectionHeading index="02" eyebrow="Flex" title="Activate modules one by one. Pay for exactly that.">
                    <p class="mt-5 text-ink-soft leading-relaxed">
                        A 50-person team might start with three modules. At 200 people you might run fifteen.
                        Try it — tap a module:
                    </p>
                </SectionHeading>
            </Reveal>
            <Reveal :delay="100">
                <div class="mt-10 flex flex-wrap gap-2.5">
                    <button v-for="m in sample_modules" :key="m.key" type="button" @click="toggle(m.key)"
                        class="rounded-full border px-4 py-2 text-sm font-medium transition ease-out duration-150"
                        :class="active.includes(m.key)
                            ? 'border-accent bg-accent text-white'
                            : 'border-line bg-white text-ink-soft hover:border-ink-faint'">
                        {{ m.name }}
                    </button>
                </div>
                <p class="mt-6 font-mono text-sm text-ink-faint">
                    {{ active.length }} active modules · billed per user, per module, per month · change any time
                </p>
            </Reveal>
        </div>
    </section>

    <!-- ── 03 Flow: data moves itself ── -->
    <section class="border-t border-line bg-ink text-white">
        <div class="mx-auto max-w-6xl px-6 py-24">
            <Reveal>
                <div class="max-w-2xl">
                    <div class="flex items-center gap-3">
                        <span class="section-index">03</span>
                        <span class="h-px w-8 bg-white/20"></span>
                        <span class="text-xs font-semibold uppercase tracking-[0.2em] text-white/40">Flow</span>
                    </div>
                    <h2 class="mt-4 text-3xl sm:text-4xl font-bold tracking-display text-balance">
                        One database. Data moves between departments on its own.
                    </h2>
                    <p class="mt-5 text-white/60 leading-relaxed">
                        These aren't integrations you configure — they're how the platform works.
                    </p>
                </div>
            </Reveal>
            <div class="mt-12 divide-y divide-white/10 border-y border-white/10">
                <Reveal v-for="(flow, i) in flows" :key="flow.event" :delay="i * 80">
                    <div class="grid gap-2 py-6 sm:grid-cols-[140px_200px_1fr] sm:items-baseline">
                        <span class="font-mono text-xs uppercase tracking-[0.15em] text-flow">{{ flow.from }} → {{ flow.to }}</span>
                        <span class="font-semibold">{{ flow.event }}</span>
                        <span class="text-white/60 text-sm leading-relaxed">{{ flow.effect }}</span>
                    </div>
                </Reveal>
            </div>
        </div>
    </section>

    <!-- ── 04 Coverage ── -->
    <section class="border-t border-line bg-white">
        <div class="mx-auto max-w-6xl px-6 py-24">
            <Reveal>
                <SectionHeading index="04" eyebrow="Coverage" title="Every department, already inside." />
            </Reveal>
            <div class="mt-12 grid gap-px bg-line border border-line sm:grid-cols-2 lg:grid-cols-4">
                <Reveal v-for="(domain, i) in domains" :key="domain.name" :delay="i * 60">
                    <Link href="/features" class="group block bg-white p-7 h-full hover:bg-paper transition ease-out duration-200">
                        <h3 class="font-semibold">{{ domain.name }}</h3>
                        <p class="mt-1.5 font-mono text-sm text-ink-faint">{{ domain.modules }} modules</p>
                        <span class="mt-5 inline-block text-sm font-medium text-accent opacity-0 group-hover:opacity-100 transition ease-out duration-200">
                            Explore →
                        </span>
                    </Link>
                </Reveal>
            </div>
        </div>
    </section>

    <!-- ── Trust strip ── -->
    <section class="border-t border-line">
        <div class="mx-auto max-w-6xl px-6 py-14">
            <div class="flex flex-wrap items-center justify-center gap-x-12 gap-y-4 text-sm text-ink-soft">
                <span>EU-hosted</span><span class="text-line">·</span>
                <span>GDPR-compliant, DSAR built in</span><span class="text-line">·</span>
                <span>Two-factor authentication</span><span class="text-line">·</span>
                <span>Full audit log</span><span class="text-line">·</span>
                <span>Export your data any day</span>
            </div>
        </div>
    </section>

    <!-- ── Final CTA ── -->
    <section class="border-t border-line bg-paper-deep">
        <div class="mx-auto max-w-6xl px-6 py-24 text-center">
            <Reveal>
                <h2 class="text-4xl sm:text-5xl font-bold tracking-display text-balance">Everything flows.</h2>
                <p class="mx-auto mt-5 max-w-md text-ink-soft">
                    See what your stack would cost on one platform — it takes about a minute.
                </p>
                <div class="mt-9">
                    <Link href="/pricing"
                        class="rounded-full bg-ink px-8 py-4 font-semibold text-white hover:bg-accent transition ease-out duration-150">
                        Build your price
                    </Link>
                </div>
            </Reveal>
        </div>
    </section>
</template>
