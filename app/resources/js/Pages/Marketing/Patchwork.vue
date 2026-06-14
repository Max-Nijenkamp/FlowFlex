<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import CtaBand from '@/Components/Marketing/CtaBand.vue'
import Kicker from '@/Components/Marketing/Kicker.vue'
import Receipt from '@/Components/Marketing/Receipt.vue'
import SectionTag from '@/Components/Marketing/SectionTag.vue'
import Switch from '@/Components/UI/Switch.vue'
import { euro, euroShort } from '@/data/marketing'
import { Head, Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

defineOptions({ layout: MarketingLayout })

const props = defineProps<{
    modules: { key: string; name: string; price_cents: number }[]
    base_price_cents: number
}>()

// Approximate list prices per user per month *(assumed)* — the point is the
// order of magnitude, footnoted on the receipt.
const tools = ref([
    { name: 'BambooHR', cents: 750, modules: ['hr.profiles', 'hr.leave'], on: true },
    { name: 'Xero', cents: 400, modules: ['finance.invoicing', 'finance.reporting'], on: true },
    { name: 'HubSpot Sales', cents: 2000, modules: ['crm.contacts', 'crm.pipeline'], on: true },
    { name: 'Asana', cents: 1100, modules: ['projects.boards'], on: true },
    { name: 'Freshdesk', cents: 1500, modules: ['support.tickets'], on: false },
    { name: 'Expensify', cents: 500, modules: ['finance.expenses'], on: false },
    { name: 'Mailchimp', cents: 500, modules: ['marketing.campaigns'], on: false },
    { name: 'Notion', cents: 800, modules: ['dms.library'], on: false },
    { name: 'Zapier (the glue)', cents: 600, modules: [], on: true },
])

const users = ref(80)

function toggle(name: string) {
    const t = tools.value.find((t) => t.name === name)
    if (t) t.on = !t.on
}

const selected = computed(() => tools.value.filter((t) => t.on))
const patchworkPerUser = computed(() => selected.value.reduce((sum, t) => sum + t.cents, 0))
const patchworkMonthly = computed(() => patchworkPerUser.value * users.value)

// FlowFlex equivalent: union of mapped modules that exist in the real catalog.
const flowModules = computed(() => {
    const keys = new Set(selected.value.flatMap((t) => t.modules))
    return props.modules.filter((m) => keys.has(m.key))
})
const flowPerUser = computed(() => props.base_price_cents + flowModules.value.reduce((sum, m) => sum + m.price_cents, 0))
const flowMonthly = computed(() => flowPerUser.value * users.value)
const savings = computed(() => patchworkMonthly.value - flowMonthly.value)
</script>

<template>
    <Head title="The patchwork calculator">
        <meta name="description"
            content="Add up what your separate tools cost per month — then see the same capabilities as modules on one switchboard." />
    </Head>

    <!-- Hero -->
    <section class="bg-bloom border-b border-line">
        <div class="mx-auto max-w-6xl px-6 pt-14 pb-12 md:pt-[84px] md:pb-[64px]">
            <Kicker>The patchwork tax</Kicker>
            <h1 class="mt-[26px] max-w-[760px] font-display text-[40px] font-bold leading-[1.02] tracking-[-0.03em] md:text-[62px]">
                What does your patchwork
                <span class="[box-shadow:inset_0_-0.16em_0_#C7D2FE]">actually cost</span>?
            </h1>
            <p class="mt-[22px] max-w-[520px] text-base leading-[1.65] text-ink-soft md:text-lg">
                Tick the tools you pay for today. The receipt adds them up — and shows the same capabilities
                as modules on one switchboard.
            </p>
        </div>
    </section>

    <!-- 01 / Calculator -->
    <section class="border-b border-line bg-card">
        <div class="mx-auto max-w-6xl px-6 py-[68px] md:py-[104px]">
            <div class="grid items-start gap-12 lg:grid-cols-[1fr_380px]">
                <div>
                    <SectionTag num="01" label="YOUR STACK TODAY" />
                    <div class="mt-8 grid gap-2.5 sm:grid-cols-2">
                        <button v-for="t in tools" :key="t.name" type="button"
                            class="flex items-center justify-between gap-2.5 rounded-[10px] border px-3.5 py-3 text-left transition ease-out duration-150 active:scale-[0.98]"
                            :class="t.on ? 'border-accent/45 bg-accent-soft' : 'border-line bg-card hover:border-ink-faint/60'"
                            @click="toggle(t.name)">
                            <span class="flex items-center gap-2.5 text-sm font-semibold">
                                <Switch :on="t.on" sm />
                                {{ t.name }}
                            </span>
                            <span class="whitespace-nowrap font-mono text-[11.5px] text-ink-faint">~{{ euro(t.cents) }}/user</span>
                        </button>
                    </div>
                    <label class="mt-9 block max-w-md">
                        <span class="flex justify-between text-[13.5px] font-semibold">
                            <span>Team size</span>
                            <span class="font-mono font-bold">{{ users }} people</span>
                        </span>
                        <input v-model.number="users" type="range" min="10" max="500" step="5" class="mt-3 w-full accent-accent" />
                        <span class="mt-1 flex justify-between font-mono text-[10.5px] text-ink-faint"><span>10</span><span>500</span></span>
                    </label>
                    <p class="mt-8 max-w-[520px] text-[14.5px] leading-[1.65] text-ink-soft">
                        And this is only the subscription line. The real patchwork tax is the re-typing, the syncing,
                        and the five forms every new hire touches — none of which shows up on an invoice.
                    </p>
                </div>

                <!-- Comparison receipt -->
                <aside class="lg:sticky lg:top-24">
                    <Receipt title="PATCHWORK · MONTHLY">
                        <div class="h-3.5" />
                        <div class="mb-1.5 flex justify-between gap-4 whitespace-nowrap border-b border-dashed border-line-strong py-[7px] font-bold text-ink">
                            <span>tool</span><span>/user</span>
                        </div>
                        <div v-for="t in selected" :key="t.name" class="flex justify-between gap-4 py-[6px] text-ink-soft">
                            <span class="truncate line-through decoration-accent/45">{{ t.name }}</span>
                            <span>{{ euro(t.cents) }}</span>
                        </div>
                        <div class="mt-2 flex justify-between gap-4 whitespace-nowrap border-t border-dashed border-line-strong pt-3 pb-[5px] font-bold text-ink">
                            <span>{{ euro(patchworkPerUser) }} × {{ users }}</span>
                            <span>{{ euroShort(patchworkMonthly) }}</span>
                        </div>
                        <div class="h-4" />
                        <div class="text-center text-[11px] font-bold tracking-[0.2em] text-ink-faint">FLOWFLEX · SAME CAPABILITIES</div>
                        <div class="h-1.5" />
                        <div v-for="m in flowModules" :key="m.key" class="flex justify-between gap-4 py-[6px] text-ink-soft">
                            <span class="truncate">{{ m.name }}</span>
                            <span>{{ euro(m.price_cents) }}</span>
                        </div>
                        <div class="flex justify-between gap-4 py-[6px] text-ink-faint">
                            <span>Core platform</span><span>{{ euro(base_price_cents) }}</span>
                        </div>
                        <div class="mt-1 flex justify-between gap-4 whitespace-nowrap border-t border-dashed border-line-strong pt-3 pb-[5px] text-base font-bold text-ink">
                            <span>{{ euro(flowPerUser) }} × {{ users }}</span>
                            <span>{{ euroShort(flowMonthly) }}</span>
                        </div>
                        <div v-if="savings > 0" class="mt-1 flex justify-between gap-4 py-[5px] font-bold text-[#0E8C61]">
                            <span>you keep</span><span>{{ euroShort(savings) }}/month</span>
                        </div>
                        <div class="h-3.5" />
                        <Link href="/pricing"
                            class="flex w-full items-center justify-center rounded-[10px] bg-accent px-6 py-3 font-sans text-[15px] font-semibold text-white shadow-[0_1px_2px_rgba(79,70,229,0.4),0_8px_20px_-10px_rgba(79,70,229,0.5)] transition ease-out duration-150 hover:bg-accent-deep active:scale-[0.98]">
                            Build the real number
                        </Link>
                        <div class="h-2.5" />
                        <p class="text-center text-[10.5px] text-ink-faint">tool prices are approximate list prices · your mileage decides</p>
                    </Receipt>
                </aside>
            </div>
        </div>
    </section>

    <CtaBand title="Twelve invoices or one." sub="See the modules behind the comparison — every price is public."
        cta="See the catalogue" href="/modules" />
</template>
