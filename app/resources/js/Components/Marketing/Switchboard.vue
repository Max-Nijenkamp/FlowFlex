<script setup lang="ts">
import Switch from '@/Components/UI/Switch.vue'

export interface BoardRow {
    key: string
    name: string
    color: string
    price: string
    on: boolean
}

defineProps<{
    rows: BoardRow[]
    title: string
    meta?: string
    formula: string
    total: string
    totalSuffix?: string
}>()

defineEmits<{ toggle: [key: string] }>()
</script>

<template>
    <div
        class="overflow-hidden rounded-2xl border border-line-strong bg-card shadow-[0_1px_2px_rgba(17,24,39,0.04),0_28px_56px_-28px_rgba(17,24,39,0.22)]">
        <div class="flex items-center justify-between border-b border-line px-[22px] py-4">
            <span class="font-display text-sm font-bold">{{ title }}</span>
            <span v-if="meta" class="font-mono text-[11px] text-ink-faint">{{ meta }}</span>
        </div>
        <div>
            <div v-for="(r, i) in rows" :key="r.key"
                class="grid grid-cols-[1fr_auto_auto] items-center gap-4 border-b border-line px-[22px] py-[13px]"
                :class="i % 2 === 0 ? 'bg-[#FAF9F5]' : ''">
                <span class="flex items-center gap-2.5 whitespace-nowrap text-sm font-semibold"
                    :class="!r.on && 'opacity-45'">
                    <span class="h-2.5 w-2.5 shrink-0 rounded-[3px]" :style="{ background: r.color }" />
                    {{ r.name }}
                </span>
                <span class="whitespace-nowrap font-mono text-xs text-ink-faint" :class="!r.on && 'opacity-45'">
                    {{ r.price }}
                </span>
                <Switch :on="r.on" interactive @toggle="$emit('toggle', r.key)" />
            </div>
        </div>
        <div class="flex items-baseline justify-between gap-3 bg-ink px-[22px] py-[18px] text-white">
            <span class="whitespace-nowrap font-mono text-xs text-white/65">{{ formula }}</span>
            <span class="whitespace-nowrap font-mono text-[22px] font-bold">
                {{ total }}<em v-if="totalSuffix" class="text-xs font-normal not-italic text-white/55">{{ totalSuffix }}</em>
            </span>
        </div>
    </div>
</template>
