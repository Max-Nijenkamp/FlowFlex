<x-filament-panels::page>
    <div class="p-4">
        <h2 class="text-xl font-bold mb-4">Gantt Chart</h2>
        @php $tasks = $this->getTasks(); @endphp
        @if(empty($tasks))
            <p class="text-gray-500">No tasks with due dates found.</p>
        @else
            <div x-data="ganttChart({{ json_encode($tasks) }})" class="overflow-x-auto">
                <div class="min-w-[800px]">
                    <div class="grid grid-cols-[200px_1fr] gap-2 mb-2">
                        <div class="font-semibold text-sm">Task</div>
                        <div class="relative h-6">
                            <template x-for="month in months" :key="month.label">
                                <div
                                    class="absolute text-xs text-gray-500"
                                    :style="`left: ${month.left}%`"
                                    x-text="month.label"
                                ></div>
                            </template>
                        </div>
                    </div>
                    <template x-for="task in tasks" :key="task.id">
                        <div class="grid grid-cols-[200px_1fr] gap-2 mb-1 items-center">
                            <div
                                class="text-sm truncate"
                                x-text="task.title + ' (' + task.project + ')'"
                            ></div>
                            <div class="relative h-6 bg-gray-100 rounded">
                                <div
                                    class="absolute h-full rounded text-xs text-white flex items-center px-1 truncate"
                                    :class="statusColor(task.status)"
                                    :style="`left: ${task.leftPct}%; width: ${task.widthPct}%`"
                                    x-text="task.title"
                                ></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <script>
            function ganttChart(rawTasks) {
                const windowDays = 90;
                const start = new Date();
                start.setDate(start.getDate() - 15);
                const end = new Date(start);
                end.setDate(end.getDate() + windowDays);

                function datePct(dateStr) {
                    const d = new Date(dateStr);
                    return Math.max(0, Math.min(100, ((d - start) / (end - start)) * 100));
                }

                const tasks = rawTasks.map(t => ({
                    ...t,
                    leftPct: datePct(t.start),
                    widthPct: Math.max(1, datePct(t.end) - datePct(t.start)),
                }));

                const months = [];
                let cur = new Date(start);
                while (cur < end) {
                    months.push({
                        label: cur.toLocaleString('default', { month: 'short', year: '2-digit' }),
                        left: datePct(cur.toISOString().split('T')[0]),
                    });
                    cur.setMonth(cur.getMonth() + 1);
                }

                return {
                    tasks,
                    months,
                    statusColor(s) {
                        return {
                            todo: 'bg-gray-400',
                            in_progress: 'bg-blue-500',
                            in_review: 'bg-yellow-500',
                            done: 'bg-green-500',
                        }[s] || 'bg-gray-400';
                    },
                };
            }
            </script>
        @endif
    </div>
</x-filament-panels::page>
