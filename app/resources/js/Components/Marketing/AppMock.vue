<template>
    <!-- Handcrafted product mock — miniature FlowFlex workspace. Decorative. -->
    <div aria-hidden="true"
        class="select-none overflow-hidden rounded-2xl border border-line bg-white shadow-[0_24px_60px_-24px_rgba(17,24,39,0.25)]">
        <!-- Window chrome -->
        <div class="flex items-center gap-2 border-b border-line bg-paper px-4 py-2.5">
            <span class="h-2.5 w-2.5 rounded-full bg-line"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-line"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-line"></span>
            <span class="ml-3 rounded-md bg-white border border-line px-2.5 py-0.5 font-mono text-[10px] text-ink-faint">app.flowflex.eu/finance</span>
        </div>
        <div class="grid grid-cols-[150px_1fr]">
            <!-- Sidebar -->
            <div class="border-r border-line bg-paper p-3 text-[11px]">
                <div class="flex items-center gap-1.5 px-2 pb-3">
                    <img src="/images/logo/flowflex-icon.svg" class="h-3.5 w-3.5" alt="" />
                    <span class="font-bold text-ink text-xs">FlowFlex</span>
                </div>
                <div class="space-y-0.5 font-medium text-ink-soft">
                    <div class="rounded-md px-2 py-1.5">Dashboard</div>
                    <div class="rounded-md bg-accent-soft px-2 py-1.5 text-accent border-l-2 border-accent">Invoices</div>
                    <div class="rounded-md px-2 py-1.5">Expenses</div>
                    <div class="rounded-md px-2 py-1.5">Cash flow</div>
                    <div class="rounded-md px-2 py-1.5">Reports</div>
                </div>
            </div>
            <!-- Content -->
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-[13px] font-bold text-ink">Invoices</div>
                        <div class="text-[10px] text-ink-faint">June 2026</div>
                    </div>
                    <div class="rounded-full bg-ink px-3 py-1 text-[10px] font-semibold text-white">New invoice</div>
                </div>
                <div class="mt-3 grid grid-cols-3 gap-2">
                    <div class="rounded-lg border border-line p-2.5">
                        <div class="text-[9px] text-ink-faint">Outstanding</div>
                        <div class="font-mono text-[13px] font-bold text-ink">€48.250</div>
                    </div>
                    <div class="rounded-lg border border-line p-2.5">
                        <div class="text-[9px] text-ink-faint">Paid this month</div>
                        <div class="font-mono text-[13px] font-bold text-ink">€112.800</div>
                    </div>
                    <div class="rounded-lg border border-line p-2.5">
                        <div class="text-[9px] text-ink-faint">Overdue</div>
                        <div class="font-mono text-[13px] font-bold text-red-500">€3.120</div>
                    </div>
                </div>
                <div class="mt-3 overflow-hidden rounded-lg border border-line text-[10px]">
                    <div class="grid grid-cols-[1fr_64px_56px] gap-2 border-b border-line bg-paper px-3 py-1.5 font-semibold uppercase tracking-wide text-[8.5px] text-ink-faint">
                        <span>Customer</span><span>Amount</span><span>Status</span>
                    </div>
                    <div v-for="row in rows" :key="row.name"
                        class="grid grid-cols-[1fr_64px_56px] gap-2 border-b border-line/60 px-3 py-1.5 last:border-0 items-center">
                        <span class="font-medium text-ink truncate">{{ row.name }}</span>
                        <span class="font-mono text-ink-soft">{{ row.amount }}</span>
                        <span class="justify-self-start rounded-full px-1.5 py-px text-[8.5px] font-semibold"
                            :class="row.cls">{{ row.status }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
const rows = [
    { name: 'Veldkamp Logistics', amount: '€12.400', status: 'Paid', cls: 'bg-emerald-50 text-emerald-700' },
    { name: 'Bakker & Zn', amount: '€8.950', status: 'Sent', cls: 'bg-sky-50 text-sky-700' },
    { name: 'Atelier Noor', amount: '€3.120', status: 'Overdue', cls: 'bg-red-50 text-red-600' },
    { name: 'GroenWerk BV', amount: '€21.700', status: 'Paid', cls: 'bg-emerald-50 text-emerald-700' },
]
</script>
