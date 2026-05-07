<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'
import {
    Users, FolderKanban, DollarSign, Building2, Megaphone, Settings,
    BarChart3, ShieldCheck, Scale, ShoppingBag, MessageSquare, GraduationCap,
    Layers, ArrowRight, X, Check,
} from 'lucide-vue-next'

const { t } = useI18n()

interface DbModule { name: string; description: string }
interface DbDomain { key: string; count: number; modules: DbModule[] }

interface Domain {
    key: string
    name: string
    gradientFrom: string
    gradientTo: string
    color: string
    icon: unknown
    description: string
    count: number
    modules: DbModule[]
}

const props = defineProps<{ domains: DbDomain[] }>()

const domainVisualConfig: Record<string, { name: string; icon: unknown; gradientFrom: string; gradientTo: string; color: string; description: string }> = {
    hr:             { name: 'HR & People',      icon: Users,         gradientFrom: '#7C3AED', gradientTo: '#9333EA', color: '#7C3AED', description: 'Manage your entire workforce lifecycle — from hiring and onboarding to payroll and offboarding. Built for HR teams that need one reliable system.' },
    projects:       { name: 'Projects & Work',  icon: FolderKanban,  gradientFrom: '#4F46E5', gradientTo: '#6366F1', color: '#4F46E5', description: 'Plan, track, and deliver work across teams. Tasks, timesheets, documents, and time tracking — all connected to clients and invoices.' },
    finance:        { name: 'Finance',          icon: DollarSign,    gradientFrom: '#059669', gradientTo: '#10B981', color: '#059669', description: 'Invoicing, expense tracking, budgets, and financial reporting in one place. Connects to CRM, projects, and payroll so numbers always match.' },
    crm:            { name: 'CRM & Sales',      icon: Building2,     gradientFrom: '#2563EB', gradientTo: '#3B82F6', color: '#2563EB', description: 'Track leads, manage your pipeline, and close deals. Built for how European sales teams actually work — connected to finance and communications.' },
    marketing:      { name: 'Marketing',        icon: Megaphone,     gradientFrom: '#DB2777', gradientTo: '#EC4899', color: '#DB2777', description: 'Run campaigns, manage email lists, and capture leads — all connected to your CRM. No separate marketing stack required.' },
    operations:     { name: 'Operations',       icon: Settings,      gradientFrom: '#D97706', gradientTo: '#F59E0B', color: '#D97706', description: 'Manage inventory, procurement, and supplier relationships. All connected to finance and projects so operations and accounting stay in sync.' },
    analytics:      { name: 'Analytics',        icon: BarChart3,     gradientFrom: '#9333EA', gradientTo: '#A855F7', color: '#9333EA', description: 'Custom dashboards, KPI tracking, and cross-module reports. One analytics layer over every module in your workspace — no data warehouse needed.' },
    it:             { name: 'IT & Security',    icon: ShieldCheck,   gradientFrom: '#475569', gradientTo: '#64748B', color: '#475569', description: 'Asset management, access control, and audit logging for IT teams. Manage hardware, software licences, and security policies without separate tools.' },
    legal:          { name: 'Legal',            icon: Scale,         gradientFrom: '#DC2626', gradientTo: '#EF4444', color: '#DC2626', description: 'Contract management, compliance tracking, and digital signatures — with a full audit trail. Built for European legal and compliance requirements.' },
    ecommerce:      { name: 'E-commerce',       icon: ShoppingBag,   gradientFrom: '#0D9488', gradientTo: '#14B8A6', color: '#0D9488', description: 'Manage products, orders, and your online storefront — with inventory, CRM, and finance modules all connected by default.' },
    communications: { name: 'Communications',   icon: MessageSquare, gradientFrom: '#0284C7', gradientTo: '#0EA5E9', color: '#0284C7', description: 'Internal chat, company announcements, and video meetings — without leaving FlowFlex. Reduce inbox noise and keep team communication in context.' },
    learning:       { name: 'Learning & Dev',   icon: GraduationCap, gradientFrom: '#EA580C', gradientTo: '#F97316', color: '#EA580C', description: 'Build and track employee learning with courses, certifications, and skill development paths — connected to HR for compliance reporting.' },
    core:           { name: 'Core Platform',    icon: Layers,        gradientFrom: '#2199C8', gradientTo: '#4BB3DC', color: '#2199C8', description: 'The foundation of FlowFlex — authentication, permissions, multi-tenancy, API, and workspace settings. Included in every plan.' },
}

const domainOrder = ['hr', 'projects', 'finance', 'crm', 'marketing', 'operations', 'analytics', 'it', 'legal', 'ecommerce', 'communications', 'learning', 'core']

const domains = computed<Domain[]>(() => {
    const dbMap = Object.fromEntries(props.domains.map(d => [d.key, d]))
    return domainOrder
        .filter(key => dbMap[key])
        .map(key => ({
            ...domainVisualConfig[key],
            key,
            count: dbMap[key].count,
            modules: dbMap[key].modules,
        }))
})


const selectedDomain = ref<Domain | null>(null)

function openModal(domain: Domain) {
    selectedDomain.value = domain
    document.body.style.overflow = 'hidden'
}

function closeModal() {
    selectedDomain.value = null
    document.body.style.overflow = ''
}

function onKeydown(e: KeyboardEvent) {
    if (e.key === 'Escape') closeModal()
}

onMounted(() => window.addEventListener('keydown', onKeydown))
onUnmounted(() => {
    window.removeEventListener('keydown', onKeydown)
    document.body.style.overflow = ''
})
</script>

<template>
    <Head title="Features & Modules — FlowFlex">
        <meta name="description" content="Explore FlowFlex's 13 domains and 99+ modules. HR, Finance, Projects, CRM, and more — all on one platform." />
    </Head>

    <!-- Hero -->
    <section class="relative bg-[#050E1A] py-28 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-[#0a1525] to-ocean-950 pointer-events-none" />
        <!-- Floating module icons in background -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
            <div v-for="(d, i) in domains.slice(0, 6)" :key="d.name"
                class="absolute w-12 h-12 rounded-2xl flex items-center justify-center opacity-10"
                :style="`background: linear-gradient(135deg, ${d.gradientFrom}, ${d.gradientTo}); top: ${15 + i * 14}%; left: ${(i % 2 === 0 ? 5 : 85) + (i * 2)}%; transform: rotate(${-15 + i * 8}deg)`"
            >
                <component :is="d.icon" class="w-6 h-6 text-white" />
            </div>
        </div>
        <div class="relative max-w-4xl mx-auto text-center space-y-6">
            <h1 class="text-4xl sm:text-6xl md:text-7xl font-black text-white leading-tight tracking-tighter">
                {{ t('features.heading') }}
            </h1>
            <p class="text-xl text-slate-400 max-w-2xl mx-auto leading-relaxed">
                {{ t('features.subheading') }}
            </p>
            <Link href="/demo" class="inline-flex items-center gap-2.5 bg-ocean-500 hover:bg-ocean-400 text-white px-8 py-4 rounded-2xl font-bold transition-all shadow-[0_0_40px_rgba(33,153,200,0.3)] hover:-translate-y-0.5">
                {{ t('features.seeInAction') }} <ArrowRight class="w-4 h-4" />
            </Link>
        </div>
    </section>

    <!-- Domain grid -->
    <section class="bg-white dark:bg-slate-900 py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="domain in domains"
                    :key="domain.name"
                    class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl overflow-hidden hover:shadow-lg dark:hover:shadow-slate-900/50 hover:-translate-y-1 transition-all group"
                >
                    <div class="p-6 space-y-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-transform group-hover:scale-110"
                                :style="`background: linear-gradient(135deg, ${domain.gradientFrom}, ${domain.gradientTo})`"
                            >
                                <component :is="domain.icon" class="w-5 h-5 text-white" />
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ domain.name }}</h3>
                        </div>
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ domain.description }}</p>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="mod in domain.modules.slice(0, 3)"
                                :key="mod.name"
                                class="text-xs px-2.5 py-1 rounded-full font-medium"
                                :style="`background-color: ${domain.color}18; color: ${domain.color}`"
                            >
                                {{ mod.name }}
                            </span>
                            <span
                                v-if="domain.modules.length > 3"
                                class="text-xs px-2.5 py-1 rounded-full font-medium bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400"
                            >
                                +{{ domain.modules.length - 3 }} more
                            </span>
                        </div>
                        <button
                            type="button"
                            class="inline-flex items-center gap-1 text-sm font-medium transition-all group-hover:gap-2"
                            :style="`color: ${domain.color}`"
                            @click="openModal(domain)"
                        >
                            {{ t('features.explore') }} <ArrowRight class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="bg-gradient-to-br from-ocean-600 via-ocean-500 to-ocean-700 py-24 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center space-y-6">
            <h2 class="text-4xl font-bold text-white">{{ t('features.ctaHeading') }}</h2>
            <p class="text-xl text-white/80">{{ t('features.ctaSubheading') }}</p>
            <Link href="/demo" class="inline-flex items-center gap-2 bg-white text-ocean-900 px-8 py-4 rounded-xl font-semibold hover:bg-ocean-50 transition-all shadow-xl hover:-translate-y-0.5">
                {{ t('features.ctaButton') }} <ArrowRight class="w-5 h-5" />
            </Link>
        </div>
    </section>

    <!-- Domain detail modal -->
    <Teleport to="body">
        <Transition
            enter-active-class="transition-all duration-250 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="selectedDomain"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                role="dialog"
                :aria-label="`${selectedDomain.name} modules`"
                @click.self="closeModal"
            >
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm" @click="closeModal" />

                <!-- Panel -->
                <Transition
                    enter-active-class="transition-all duration-300 ease-out"
                    enter-from-class="opacity-0 scale-95 translate-y-4"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                    leave-active-class="transition-all duration-200 ease-in"
                    leave-from-class="opacity-100 scale-100 translate-y-0"
                    leave-to-class="opacity-0 scale-95 translate-y-2"
                    appear
                >
                    <div
                        v-if="selectedDomain"
                        class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-700"
                    >
                        <!-- Modal header with gradient -->
                        <div
                            class="relative px-8 pt-8 pb-6 overflow-hidden"
                            :style="`background: linear-gradient(135deg, ${selectedDomain.gradientFrom}18, ${selectedDomain.gradientTo}08)`"
                        >
                            <div class="absolute inset-0 pointer-events-none" :style="`background: radial-gradient(circle at 80% 50%, ${selectedDomain.color}12, transparent 70%)`" />
                            <button
                                type="button"
                                class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
                                @click="closeModal"
                                aria-label="Close"
                            >
                                <X class="w-4 h-4" />
                            </button>
                            <div class="relative flex items-start gap-4">
                                <div
                                    class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 shadow-lg"
                                    :style="`background: linear-gradient(135deg, ${selectedDomain.gradientFrom}, ${selectedDomain.gradientTo})`"
                                >
                                    <component :is="selectedDomain.icon" class="w-7 h-7 text-white" />
                                </div>
                                <div>
                                    <h2 class="text-2xl font-black text-slate-900 dark:text-white">{{ selectedDomain.name }}</h2>
                                    <p class="text-slate-600 dark:text-slate-400 mt-1 leading-relaxed text-sm">{{ selectedDomain.description }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Module list -->
                        <div class="px-8 py-6">
                            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-4">
                                {{ selectedDomain.modules.length }} modules in this domain
                            </p>
                            <div class="grid gap-3">
                                <div
                                    v-for="mod in selectedDomain.modules"
                                    :key="mod.name"
                                    class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60 hover:border-slate-200 dark:hover:border-slate-600 transition-colors"
                                >
                                    <div
                                        class="w-6 h-6 rounded-md flex items-center justify-center shrink-0 mt-0.5"
                                        :style="`background: ${selectedDomain.color}18`"
                                    >
                                        <Check class="w-3.5 h-3.5" :style="`color: ${selectedDomain.color}`" />
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900 dark:text-white text-sm">{{ mod.name }}</p>
                                        <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5 leading-relaxed">{{ mod.description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="px-8 pb-8">
                            <div class="flex items-center gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                                <Link
                                    href="/demo"
                                    class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl font-bold text-white text-sm transition-all hover:-translate-y-0.5"
                                    :style="`background: linear-gradient(135deg, ${selectedDomain.gradientFrom}, ${selectedDomain.gradientTo}); box-shadow: 0 4px 20px ${selectedDomain.color}30`"
                                    @click="closeModal"
                                >
                                    See {{ selectedDomain.name }} in action <ArrowRight class="w-4 h-4" />
                                </Link>
                                <button
                                    type="button"
                                    class="px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
                                    @click="closeModal"
                                >
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
