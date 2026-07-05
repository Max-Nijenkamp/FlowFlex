<script setup lang="ts">
import { computed, ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import MarketingLayout from '../../Components/Layout/MarketingLayout.vue'
import CtaBand from '../../Components/Marketing/CtaBand.vue'
import Reveal from '../../Components/UI/Reveal.vue'

defineOptions({ layout: MarketingLayout })

interface ToolRow {
    name: string
    cat: string
    cost: number // whole euros per month
}

// Editable repeater, seeded with the typical 80-person stack from the design.
const tools = ref<ToolRow[]>([
    { name: 'BambooHR', cat: 'HR', cost: 499 },
    { name: 'Xero', cat: 'Accounting', cost: 185 },
    { name: 'HubSpot Starter', cat: 'CRM', cost: 368 },
    { name: 'Asana', cat: 'Projects', cost: 299 },
    { name: 'Zapier', cat: 'Glue', cost: 189 },
    { name: 'Freshdesk', cat: 'Support', cost: 236 },
])

const users = ref(80)

const addTool = (): void => {
    tools.value.push({ name: '', cat: '', cost: 0 })
}
const removeTool = (i: number): void => {
    tools.value.splice(i, 1)
}

const todayTotal = computed(() => tools.value.reduce((s, t) => s + (Number(t.cost) || 0), 0))
// Rough replacement estimate: each real tool maps to ~2 modules at ~€0,60 avg —
// ≈ €1,20/user per tool, matching the design's 6-tool / €960 reference point.
const flowflexModules = computed(() => Math.max(tools.value.filter((t) => t.name.trim() !== '').length * 2 - 1, 2))
const flowflexTotal = computed(() => Math.round((flowflexModules.value * 60 * users.value) / 100 / 10) * 10)
const keep = computed(() => Math.max(todayTotal.value - flowflexTotal.value, 0))

const fmt = (n: number): string => '€' + n.toLocaleString('nl-NL')
</script>

<template>
    <Head>
        <title>The patchwork tax calculator</title>
        <meta name="description" content="Add the tools you pay for today. We line them up against the FlowFlex modules that replace them — subscriptions and the hidden tax both." />
    </Head>

    <section class="ff-hero ff-grid-bg" style="padding-bottom: 64px">
        <div class="wrap">
            <span class="ff-kicker"><span class="sq"></span>The patchwork tax</span>
            <h1 style="max-width: 760px">What is your stack<br /><span class="u">really</span> costing you?</h1>
            <p class="ff-lede">
                Add the tools you pay for today. We'll line them up against the FlowFlex modules that replace them —
                subscriptions and the hidden tax both.
            </p>
        </div>
    </section>

    <section class="ff-section" style="background: var(--card)">
        <div class="wrap">
            <div class="grid items-start gap-10 lg:gap-12 lg:[grid-template-columns:1fr_400px]">
                <div>
                    <p class="ff-tag"><b>01</b> / YOUR TOOLS TODAY</p>
                    <div class="mt-6 overflow-hidden rounded-[14px] border" style="border-color: var(--line-strong)">
                        <div
                            v-for="(t, i) in tools"
                            :key="i"
                            class="grid items-center gap-3.5 px-5.5 py-3 [grid-template-columns:1fr_110px_100px_36px] max-md:[grid-template-columns:1fr_84px_36px]"
                            :style="{ borderBottom: '1px solid var(--line)', background: i % 2 ? '#FAF9F5' : '#fff' }"
                        >
                            <input v-model="t.name" type="text" placeholder="Tool name" class="w-full bg-transparent text-[14.5px] font-semibold outline-none placeholder:text-(--ink-faint)" :aria-label="`Tool ${i + 1} name`" />
                            <input v-model="t.cat" type="text" placeholder="Category" class="w-full bg-transparent text-[12.5px] font-medium outline-none max-md:hidden placeholder:text-(--ink-faint)" style="color: var(--ink-faint)" :aria-label="`Tool ${i + 1} category`" />
                            <span class="mono flex items-center justify-end gap-1 text-[13px]" style="color: var(--ink-soft)">
                                €<input v-model.number="t.cost" type="number" min="0" class="w-14 bg-transparent text-right outline-none" :aria-label="`Tool ${i + 1} monthly cost`" />/mo
                            </span>
                            <button type="button" class="cursor-pointer text-center text-[16px] transition-colors hover:text-(--ink)" style="color: var(--ink-faint)" :aria-label="`Remove ${t.name || 'tool'}`" @click="removeTool(i)">×</button>
                        </div>
                        <button
                            type="button"
                            class="flex w-full cursor-pointer items-center gap-2.5 px-5.5 py-3 text-[14px] font-medium transition-colors hover:text-(--ink)"
                            style="color: var(--ink-faint); border-top: 1px dashed var(--line-strong)"
                            @click="addTool"
                        >
                            <span class="inline-flex h-[22px] w-[22px] items-center justify-center rounded-md text-[14px]" style="border: 1px dashed var(--line-strong)">+</span>
                            Add another tool…
                        </button>
                    </div>

                    <div class="mt-8">
                        <p class="ff-tag"><b>02</b> / THE HIDDEN PART</p>
                        <div class="ff-cells" style="margin-top: 22px">
                            <div class="ff-cell" style="padding: 22px">
                                <span class="corner"></span>
                                <div class="big" style="font-size: 30px">~6 h<em>/week</em></div>
                                <p class="mt-1.5">Re-typing the same data between systems, at {{ users }} people.</p>
                            </div>
                            <div class="ff-cell" style="padding: 22px">
                                <span class="corner"></span>
                                <div class="big" style="font-size: 30px">{{ tools.length }}×</div>
                                <p class="mt-1.5">Vendors to chase when something breaks between two tools.</p>
                            </div>
                            <div class="ff-cell" style="padding: 22px">
                                <span class="corner"></span>
                                <div class="big" style="font-size: 30px">0</div>
                                <p class="mt-1.5">Reports that can join HR, sales and finance data today.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ff-receipt lg:sticky lg:top-24">
                    <div class="rt">SIDE BY SIDE · MONTHLY</div>
                    <div style="height: 16px"></div>
                    <div style="font-family: var(--font-sans)">
                        <div class="flex justify-between text-[13.5px] font-semibold">
                            <span>Team size</span>
                            <span class="mono font-bold">{{ users }} people</span>
                        </div>
                        <input v-model.number="users" type="range" min="10" max="500" step="5" class="mt-2.5 w-full accent-(--indigo)" aria-label="Team size" />
                    </div>
                    <div style="height: 14px"></div>
                    <div class="rl head"><span>today</span><span></span></div>
                    <div class="rl"><span>{{ tools.length }} subscriptions</span><span>{{ fmt(todayTotal) }}</span></div>
                    <div class="rl"><span class="dim">+ sync glue</span><span class="dim">included above</span></div>
                    <div style="height: 14px"></div>
                    <div class="rl head"><span>flowflex</span><span></span></div>
                    <div class="rl"><span>~{{ flowflexModules }} modules × {{ users }} users</span><span>{{ fmt(flowflexTotal) }}</span></div>
                    <div class="rl"><span class="dim">integrations needed</span><span class="dim">€0</span></div>
                    <div class="rl total"><span>you keep</span><span style="color: #0E8C61">{{ fmt(keep) }}/mo</span></div>
                    <div style="height: 8px"></div>
                    <div class="rl justify-center! whitespace-normal! text-center" style="font-size: 11px; color: var(--ink-faint)">
                        ≈ {{ fmt(keep * 12) }} a year, before the hidden part
                    </div>
                    <div style="height: 12px"></div>
                    <Link href="/contact" class="ff-btn primary w-full">Check my numbers with the team</Link>
                </div>
            </div>
        </div>
    </section>

    <CtaBand
        title="Numbers this good deserve a second opinion."
        sub="Send us your real stack — we'll do the mapping live on a 30-minute call."
        cta="Book the call"
        href="/contact"
    />
</template>
