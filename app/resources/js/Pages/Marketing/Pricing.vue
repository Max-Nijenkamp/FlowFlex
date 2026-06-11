<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import { computed, ref } from 'vue'

defineOptions({ layout: MarketingLayout })

const props = defineProps<{
    modules: { key: string; name: string; domain: string; price_cents: number }[]
    base_price_cents: number
}>()

const users = ref(50)
const selected = ref<string[]>([])

function toggle(key: string) {
    selected.value = selected.value.includes(key)
        ? selected.value.filter((k) => k !== key)
        : [...selected.value, key]
}

const monthlyCents = computed(() => {
    const moduleCents = props.modules
        .filter((m) => selected.value.includes(m.key))
        .reduce((sum, m) => sum + m.price_cents, 0)
    return (props.base_price_cents + moduleCents) * users.value
})

const byDomain = computed(() => {
    const groups: Record<string, typeof props.modules> = {}
    for (const m of props.modules) (groups[m.domain] ??= []).push(m)
    return groups
})

const euro = (cents: number) => `€${(cents / 100).toFixed(2)}`
</script>

<template>
    <section class="mx-auto max-w-6xl px-6 py-20">
        <h1 class="text-center text-4xl font-bold">Pay only for what you use</h1>
        <p class="mt-4 text-center text-slate-600">Per user, per module, per month. No bundles, no surprises.</p>

        <div class="mt-12 grid gap-10 lg:grid-cols-[1fr_320px]">
            <div class="space-y-8">
                <div v-for="(mods, domain) in byDomain" :key="domain">
                    <h2 class="font-semibold uppercase tracking-wide text-sm text-slate-400">{{ domain }}</h2>
                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                        <!-- Optimistic toggle: selection updates instantly, no server round-trip -->
                        <button v-for="m in mods" :key="m.key" type="button" @click="toggle(m.key)"
                            class="flex items-center justify-between rounded-xl border p-4 text-left transition ease-out duration-150"
                            :class="selected.includes(m.key)
                                ? 'border-sky-400 ring-1 ring-sky-400 bg-sky-50'
                                : 'border-slate-200 hover:border-slate-300'">
                            <span class="font-medium text-sm">{{ m.name }}</span>
                            <span class="text-sm text-slate-500">
                                {{ m.price_cents === 0 ? 'Included' : euro(m.price_cents) + '/user' }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <aside class="lg:sticky lg:top-24 h-fit rounded-2xl border border-slate-200 p-6 shadow-sm">
                <h3 class="font-semibold">Your estimate</h3>
                <label class="mt-4 block text-sm text-slate-600">
                    Team size: <span class="font-semibold text-slate-900">{{ users }}</span>
                    <input v-model.number="users" type="range" min="10" max="500" step="10" class="mt-2 w-full accent-sky-500" />
                </label>
                <div class="mt-6 border-t border-slate-100 pt-4">
                    <div class="flex justify-between text-sm text-slate-600">
                        <span>Base platform</span><span>{{ euro(base_price_cents) }}/user</span>
                    </div>
                    <div class="flex justify-between text-sm text-slate-600 mt-1">
                        <span>{{ selected.length }} modules selected</span>
                    </div>
                    <div class="mt-4 text-3xl font-bold">{{ euro(monthlyCents) }}<span class="text-base font-normal text-slate-500">/month</span></div>
                </div>
            </aside>
        </div>
    </section>
</template>
