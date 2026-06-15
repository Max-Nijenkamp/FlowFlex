<script setup lang="ts">
withDefaults(defineProps<{
    name?: string
    color?: string
    price?: string
    on?: boolean
    desc?: string
    ghost?: boolean
    ghostLabel?: string
}>(), { on: false, ghost: false })
</script>

<template>
    <div v-if="ghost"
        class="flex min-h-[108px] items-center justify-center rounded-xl border border-dashed border-line-strong text-[13.5px] font-medium text-ink-faint transition duration-200 ease-out hover:border-accent/50 hover:text-accent">
        {{ ghostLabel }}
    </div>
    <div v-else class="group rounded-xl transition duration-200 ease-out hover:-translate-y-1" :class="[
        desc ? 'p-[22px]' : 'p-[18px]',
        on
            ? 'border border-line-strong bg-card shadow-[0_1px_2px_rgba(17,24,39,0.03)] hover:border-accent/50 hover:shadow-[0_14px_30px_-16px_rgba(17,24,39,0.25)]'
            : 'border border-dashed border-line-strong bg-transparent hover:border-ink-faint/70 hover:bg-card/60',
    ]">
        <div class="flex items-center justify-between">
            <span class="flex h-[22px] w-[22px] items-center justify-center rounded-md transition-transform duration-200 ease-out group-hover:scale-110"
                :style="{ background: on ? color : '#E3E0D8' }">
                <span class="h-2 w-2 rounded-[2px] bg-white" />
            </span>
            <span class="rounded-[5px] px-2 py-[3px] font-mono text-[9.5px] tracking-[0.12em]"
                :class="on ? 'bg-accent-soft text-accent' : 'bg-[#EFEDE7] text-ink-faint'">
                {{ on ? 'ON' : 'OFF' }}
            </span>
        </div>
        <div class="mt-3.5 text-sm font-semibold" :class="[desc && 'text-[15.5px]', !on && 'text-ink-faint']">{{ name }}</div>
        <p v-if="desc" class="mt-1.5 text-[13.5px] leading-[1.55] text-ink-soft">{{ desc }}</p>
        <div class="font-mono text-[11.5px] text-ink-faint" :class="desc ? 'mt-3' : 'mt-1'">{{ price }}</div>
    </div>
</template>
