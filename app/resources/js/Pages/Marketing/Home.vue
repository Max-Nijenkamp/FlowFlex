<script setup lang="ts">
import { computed, ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import MarketingLayout from '../../Components/Layout/MarketingLayout.vue'
import ReplacesStrip from '../../Components/Marketing/ReplacesStrip.vue'
import FlowBand from '../../Components/Marketing/FlowBand.vue'
import CtaBand from '../../Components/Marketing/CtaBand.vue'
import FfSwitch from '../../Components/UI/FfSwitch.vue'
import Reveal from '../../Components/UI/Reveal.vue'
import { domainColors, domains, euro } from '../../data/marketing'

defineOptions({ layout: MarketingLayout })

// Hero switchboard — interactive: flipping switches recomputes the total live.
const boardRows = ref([
    { name: 'Employee profiles', domain: 'hr', price: 0, on: true },
    { name: 'Leave & absence', domain: 'hr', price: 150, on: true },
    { name: 'Payroll', domain: 'hr', price: 250, on: false },
    { name: 'Invoicing', domain: 'finance', price: 200, on: true },
    { name: 'Expenses', domain: 'finance', price: 100, on: false },
    { name: 'Pipeline', domain: 'crm', price: 150, on: true },
    { name: 'Projects & boards', domain: 'projects', price: 150, on: false },
])
const users = 80
const perUser = computed(() => boardRows.value.filter((r) => r.on).reduce((s, r) => s + r.price, 0))
const total = computed(() => (perUser.value * users) / 100)

const tiles = [
    { name: 'Employee profiles', domain: 'hr', price: 'included', on: true },
    { name: 'Leave & absence', domain: 'hr', price: '€1,50/user', on: true },
    { name: 'Invoicing', domain: 'finance', price: '€2,00/user', on: true },
    { name: 'Pipeline', domain: 'crm', price: '€1,50/user', on: true },
    { name: 'Payroll', domain: 'hr', price: '€2,50/user', on: false },
    { name: 'Expenses', domain: 'finance', price: '€1,00/user', on: false },
    { name: 'Tickets', domain: 'support', price: '€1,50/user', on: false },
]

const coverage = domains.slice(0, 12)
</script>

<template>
    <Head>
        <title>Run everything. Pay for what's switched on.</title>
        <meta
            name="description"
            content="HR, finance, CRM and 70 more modules on one database. Each one is a switch on your billing page — flip it on when you need it, off when you don't."
        />
    </Head>

    <!-- Hero -->
    <section class="ff-hero ff-grid-bg">
        <div class="wrap">
            <div class="grid items-center gap-11 lg:gap-16 lg:[grid-template-columns:1.05fr_1fr]">
                <div>
                    <span class="ff-kicker"><span class="sq"></span>Per user · per module</span>
                    <h1>Run everything.<br />Pay for what's <span class="u">switched on</span>.</h1>
                    <p class="ff-lede">
                        HR, finance, CRM and 70 more modules on one database. Each one is a switch on your billing
                        page — flip it on when you need it, off when you don't.
                    </p>
                    <div class="ff-hero-ctas">
                        <Link href="/pricing" class="ff-btn primary lg">Build your price</Link>
                        <Link href="/product" class="ff-btn outline lg">See the modules</Link>
                    </div>
                    <p class="ff-hero-meta">teams of 50–500 · no tiers · no lock-in · data portable</p>
                </div>
                <div class="ff-board">
                    <div class="ff-board-head">
                        <span class="t">Your modules</span>
                        <span class="mono" style="font-size: 11px; color: var(--ink-faint)">{{ users }} users</span>
                    </div>
                    <div>
                        <button
                            v-for="r in boardRows"
                            :key="r.name"
                            type="button"
                            class="ff-board-row w-full text-left cursor-pointer"
                            :class="{ off: !r.on }"
                            :aria-pressed="r.on"
                            @click="r.on = !r.on"
                        >
                            <span class="nm"><span class="chip" :style="{ background: domainColors[r.domain] }"></span>{{ r.name }}</span>
                            <span class="pr">{{ r.price === 0 ? 'included' : euro(r.price) + '/user' }}</span>
                            <FfSwitch :on="r.on" />
                        </button>
                    </div>
                    <div class="ff-board-total">
                        <span class="f">{{ euro(perUser) }}/user × {{ users }} users</span>
                        <span class="v">€{{ Math.round(total) }}<em>/month</em></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <ReplacesStrip />

    <!-- 01 / The patchwork tax -->
    <section class="ff-section" style="background: var(--card)">
        <div class="wrap">
            <Reveal>
                <p class="ff-tag"><b>01</b> / THE PATCHWORK TAX</p>
                <h2>Twelve tools, one company, and nothing talks to anything.</h2>
                <p class="ff-lede">
                    Somewhere between 40 and 80 people, the cost of switching, syncing and re-typing quietly outgrows
                    the cost of the tools themselves.
                </p>
                <div class="ff-cells">
                    <div class="ff-cell">
                        <span class="corner"></span>
                        <div class="big">5–15</div>
                        <h3>Separate tools at 100 people</h3>
                        <p>Each with its own login, its own invoice, its own idea of who your employees are.</p>
                    </div>
                    <div class="ff-cell">
                        <span class="corner"></span>
                        <div class="big">×5</div>
                        <h3>Forms per new hire</h3>
                        <p>HR, payroll, IT, the LMS, the project tool. One person, five data entries, five chances to typo.</p>
                    </div>
                    <div class="ff-cell">
                        <span class="corner"></span>
                        <div class="big">0</div>
                        <h3>Integrations to maintain</h3>
                        <p>One database. There is nothing to glue together, so nothing breaks at 2am.</p>
                    </div>
                </div>
            </Reveal>
        </div>
    </section>

    <!-- 02 / Flex -->
    <section class="ff-section ff-grid-bg">
        <div class="wrap">
            <Reveal>
                <p class="ff-tag"><b>02</b> / FLEX</p>
                <h2>Modules are switches, not sales calls.</h2>
                <p class="ff-lede">
                    Flip one on and it's live immediately. Flip it off and billing stops at month-end — your data
                    stays exactly where it was.
                </p>
                <div class="ff-tiles">
                    <div v-for="t in tiles" :key="t.name" class="ff-tile" :class="{ off: !t.on }">
                        <div class="top">
                            <span class="chip" :style="{ background: domainColors[t.domain] }"><span></span></span>
                            <span class="ff-state" :class="t.on ? 'on' : 'off'">{{ t.on ? 'ON' : 'OFF' }}</span>
                        </div>
                        <div class="nm">{{ t.name }}</div>
                        <div class="pr">{{ t.price }}</div>
                    </div>
                    <Link href="/product" class="ff-tile ghost">+ 65 more modules</Link>
                </div>
            </Reveal>
        </div>
    </section>

    <!-- 03 / Flow -->
    <FlowBand />

    <!-- 04 / Coverage -->
    <section class="ff-section" style="background: var(--card)">
        <div class="wrap">
            <Reveal>
                <p class="ff-tag"><b>04</b> / COVERAGE</p>
                <h2>Every department, already inside.</h2>
                <div class="ff-table">
                    <Link v-for="d in coverage" :key="d.key" href="/product" class="ff-trow">
                        <span class="chip" :style="{ background: domainColors[d.key] }"></span>
                        <span class="nm">{{ d.name }}</span>
                        <span class="ct">{{ d.modules }} modules</span>
                        <span class="go">explore →</span>
                    </Link>
                </div>
                <p class="mono mt-4" style="font-size: 12px; color: var(--ink-faint)">
                    + 4 more departments · all on the same database
                </p>
            </Reveal>
        </div>
    </section>

    <!-- 05 / Pricing teaser -->
    <section class="ff-section ff-grid-bg">
        <div class="wrap">
            <div class="grid items-center gap-11 lg:gap-20 lg:[grid-template-columns:1fr_400px]">
                <Reveal>
                    <p class="ff-tag"><b>05</b> / PRICING</p>
                    <h2>Your invoice is a list, not a tier.</h2>
                    <p class="ff-lede">
                        The sum of the modules you switched on, times the people on your team. The per-module price is
                        identical at 50 users or 500 — you pay for more seats, never a higher tier.
                    </p>
                    <div class="ff-hero-ctas">
                        <Link href="/pricing" class="ff-btn primary lg">Build your price</Link>
                    </div>
                </Reveal>
                <div class="ff-receipt" style="transform: rotate(0.6deg)">
                    <div class="rt">FLOWFLEX · MONTHLY</div>
                    <div style="height: 14px"></div>
                    <div class="rl head"><span>module</span><span>/user</span></div>
                    <div class="rl"><span>Employee profiles</span><span>€0,00</span></div>
                    <div class="rl"><span>Leave &amp; absence</span><span>€1,50</span></div>
                    <div class="rl"><span>Invoicing</span><span>€2,00</span></div>
                    <div class="rl"><span>Pipeline</span><span>€1,50</span></div>
                    <div class="rl total"><span>€5,00 × 80 users</span><span>€400</span></div>
                    <div style="height: 8px"></div>
                    <div class="rl justify-center! whitespace-normal! text-center" style="font-size: 11px; color: var(--ink-faint)">
                        change modules any month · no contracts
                    </div>
                </div>
            </div>
        </div>
    </section>

    <CtaBand
        title="Switch on what you need. Nothing else."
        sub="See what your stack would cost on one platform — it takes about a minute."
    />
</template>
