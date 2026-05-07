<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import {
    Users, FolderKanban, DollarSign, Building2, Megaphone, Settings,
    BarChart3, ShieldCheck, Scale, ShoppingBag, MessageSquare, GraduationCap,
    Layers, ArrowRight, ChevronDown, AlertCircle, TrendingDown, Database,
    ToggleRight, Share2, TrendingUp, Quote, CheckCircle2, Zap,
} from 'lucide-vue-next'

const { t } = useI18n()

interface DbModule { name: string; description: string }
interface DbDomain { key: string; count: number; modules: DbModule[] }

const props = defineProps<{
    domains: DbDomain[]
    moduleCount: number
    domainCount: number
}>()

const { moduleCount, domainCount } = props

const domainVisualConfig: Record<string, { name: string; icon: unknown; gradientFrom: string; gradientTo: string; color: string; description: string }> = {
    hr:             { name: 'HR & People',      icon: Users,         gradientFrom: '#7C3AED', gradientTo: '#9333EA', color: '#7C3AED', description: 'Full workforce lifecycle — hiring, onboarding, leave, payroll.' },
    projects:       { name: 'Projects & Work',  icon: FolderKanban,  gradientFrom: '#4F46E5', gradientTo: '#6366F1', color: '#4F46E5', description: 'Plan, track, and deliver across teams with tasks and timesheets.' },
    finance:        { name: 'Finance',          icon: DollarSign,    gradientFrom: '#059669', gradientTo: '#10B981', color: '#059669', description: 'Invoicing, expenses, budgets, and financial reporting in one place.' },
    crm:            { name: 'CRM & Sales',      icon: Building2,     gradientFrom: '#2563EB', gradientTo: '#3B82F6', color: '#2563EB', description: 'Track leads, manage pipeline, and close deals — all connected.' },
    marketing:      { name: 'Marketing',        icon: Megaphone,     gradientFrom: '#DB2777', gradientTo: '#EC4899', color: '#DB2777', description: 'Run campaigns, manage email lists, and capture leads.' },
    operations:     { name: 'Operations',       icon: Settings,      gradientFrom: '#D97706', gradientTo: '#F59E0B', color: '#D97706', description: 'Inventory, procurement, and supplier management — all connected.' },
    analytics:      { name: 'Analytics',        icon: BarChart3,     gradientFrom: '#9333EA', gradientTo: '#A855F7', color: '#9333EA', description: 'Custom dashboards and KPI tracking across every module.' },
    it:             { name: 'IT & Security',    icon: ShieldCheck,   gradientFrom: '#475569', gradientTo: '#64748B', color: '#475569', description: 'Asset management, access control, and audit logging.' },
    legal:          { name: 'Legal',            icon: Scale,         gradientFrom: '#DC2626', gradientTo: '#EF4444', color: '#DC2626', description: 'Contract management, compliance, and digital signatures.' },
    ecommerce:      { name: 'E-commerce',       icon: ShoppingBag,   gradientFrom: '#0D9488', gradientTo: '#14B8A6', color: '#0D9488', description: 'Products, orders, and storefront — connected to CRM and finance.' },
    communications: { name: 'Communications',   icon: MessageSquare, gradientFrom: '#0284C7', gradientTo: '#0EA5E9', color: '#0284C7', description: 'Internal chat, announcements, and meetings without leaving FlowFlex.' },
    learning:       { name: 'Learning & Dev',   icon: GraduationCap, gradientFrom: '#EA580C', gradientTo: '#F97316', color: '#EA580C', description: 'Build employee learning with courses, certs, and skill paths.' },
    core:           { name: 'Core Platform',    icon: Layers,        gradientFrom: '#2199C8', gradientTo: '#4BB3DC', color: '#2199C8', description: 'The foundation — auth, permissions, multi-tenancy, API, settings.' },
}

const domainOrder = ['hr', 'projects', 'finance', 'crm', 'marketing', 'operations', 'analytics', 'it', 'legal', 'ecommerce', 'communications', 'learning', 'core']

const domains = computed(() => {
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

const toolsReplaced = [
    { name: 'Personio', color: '#FF7A3D' },
    { name: 'Jira', color: '#4A90D9' },
    { name: 'Exact', color: '#E84A5F' },
    { name: 'HubSpot', color: '#FF8C69' },
    { name: 'Slack', color: '#C084FC' },
    { name: 'Asana', color: '#F47B7B' },
    { name: 'Moneybird', color: '#3BBFB0' },
    { name: 'Trello', color: '#5BA4CF' },
]

const testimonials = [
    {
        quote: "We replaced Personio, Exact Online, and three spreadsheets. FlowFlex gave us one system where HR, finance, and projects actually talk to each other.",
        name: "Lisa van den Berg",
        role: "HR Director",
        company: "Meridian Logistics B.V.",
        initials: "LB",
        color: "#7C3AED",
    },
    {
        quote: "The modular approach was exactly what we needed. We started with HR and payroll, added projects six months later. No migration, no extra setup.",
        name: "Thomas Hargreaves",
        role: "Operations Manager",
        company: "Hargreaves & Co. Ltd.",
        initials: "TH",
        color: "#2199C8",
    },
    {
        quote: "Our team of 45 went from 6 different logins to one. The time we get back just from not copying data between systems is remarkable.",
        name: "Sophie Vermeer",
        role: "CEO",
        company: "Studio Vermeer",
        initials: "SV",
        color: "#059669",
    },
]

const features = [
    {
        icon: ToggleRight,
        label: 'Activate what you need',
        title: "Start small. Scale when you're ready.",
        desc: 'Turn modules on and off from your workspace settings. Add HR, then Finance, then CRM — at your own pace. Billing adjusts automatically. No re-contracting, no data migrations.',
        color: '#2199C8',
        checklist: [`${props.moduleCount}+ modules across ${props.domainCount} domains`, 'Billing adjusts per billing cycle', 'All your data preserved when toggling'],
    },
    {
        icon: Share2,
        label: 'One data layer',
        title: 'Every module speaks the same language.',
        desc: 'Your HR data feeds payroll. Your CRM pipeline feeds finance reports. Your projects timesheets feed invoices. No integrations, no Zapier, no CSV exports — it just works.',
        color: '#7C3AED',
        checklist: ['Real-time data across all modules', 'No API glue or middleware needed', 'Single source of truth for your business'],
    },
    {
        icon: TrendingUp,
        label: 'Built for Europe',
        title: 'GDPR-first, Dutch & UK market ready.',
        desc: 'All data stored in the EU. Full GDPR Article 28 DPA included. Dutch and English interface. Built by a European team for European compliance requirements.',
        color: '#059669',
        checklist: ['Data stored in AWS eu-west-1', 'DPA included on all plans', 'EN + NL interface, more languages coming'],
    },
]

const steps = [
    { icon: ToggleRight, num: '01', title: 'Activate your modules', desc: 'Choose the modules your business needs from the module marketplace. Start with one, add more any time.' },
    { icon: Share2, num: '02', title: 'Invite your team', desc: 'Add employees and assign roles. Each person sees only what they need. Permissions are granular and audited.' },
    { icon: TrendingUp, num: '03', title: 'Run your business', desc: 'Everything connected. HR, finance, projects, CRM — all talking to each other on one shared data layer.' },
]
</script>

<template>
    <Head>
        <title>FlowFlex — Your business, your tools in flow.</title>
        <meta name="description" content="Replace 8 disconnected tools with one modular platform. HR, finance, projects, CRM, and 99+ modules — activate only what you need." />
    </Head>

    <!-- ─── HERO ─────────────────────────────────────────────────────── -->
    <section class="relative min-h-screen flex flex-col items-center justify-center overflow-hidden bg-[#050E1A]">
        <!-- Deep-space gradient backdrop -->
        <div class="absolute inset-0 bg-gradient-to-br from-ocean-950 via-[#071828] to-slate-950 pointer-events-none" />

        <!-- Glowing blob — ocean -->
        <div class="absolute top-[-10%] left-[-5%] w-[700px] h-[700px] rounded-full bg-ocean-700/20 blur-[120px] pointer-events-none" />
        <!-- Glowing blob — purple accent -->
        <div class="absolute bottom-[-10%] right-[-5%] w-[500px] h-[500px] rounded-full bg-violet-800/15 blur-[100px] pointer-events-none" />
        <!-- Subtle grid -->
        <div class="absolute inset-0 bg-[linear-gradient(rgba(33,153,200,0.04)_1px,transparent_1px),linear-gradient(90deg,rgba(33,153,200,0.04)_1px,transparent_1px)] bg-[size:72px_72px] pointer-events-none" />

        <div
            class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8 pt-24 pb-16"
            v-motion
            :initial="{ opacity: 0, y: 50 }"
            :enter="{ opacity: 1, y: 0, transition: { duration: 800 } }"
        >
            <!-- Main headline -->
            <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white leading-[1.05] tracking-tighter">
                One platform.<br />
                <span class="relative">
                    <span class="bg-gradient-to-r from-ocean-300 via-ocean-400 to-sky-300 bg-clip-text text-transparent">Every tool</span>
                </span>
                <span class="text-white"> you need.</span>
            </h1>

            <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto leading-relaxed">
                {{ t('hero.subtitle') }}
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
                <Link
                    href="/demo"
                    class="group inline-flex items-center gap-2.5 bg-ocean-500 hover:bg-ocean-400 text-white px-8 py-4 rounded-2xl font-bold text-base transition-all shadow-[0_0_40px_rgba(33,153,200,0.35)] hover:shadow-[0_0_60px_rgba(33,153,200,0.5)] hover:-translate-y-0.5"
                >
                    {{ t('hero.cta') }}
                    <ArrowRight class="w-4 h-4 transition-transform group-hover:translate-x-1" />
                </Link>
                <Link
                    href="/features"
                    class="inline-flex items-center gap-2 text-slate-300 hover:text-white border border-white/10 hover:border-white/20 bg-white/5 hover:bg-white/10 px-8 py-4 rounded-2xl font-medium text-base transition-all"
                >
                    {{ t('hero.secondaryCta') }}
                </Link>
            </div>
            <p class="text-slate-600 text-sm">{{ t('hero.trustNote') }}</p>
        </div>

        <!-- Product chrome placeholder -->
        <div
            class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 w-full pb-8"
            v-motion
            :initial="{ opacity: 0, y: 60 }"
            :enter="{ opacity: 1, y: 0, transition: { duration: 900, delay: 300 } }"
        >
            <div class="w-full rounded-2xl border border-white/10 bg-gradient-to-b from-slate-800/80 to-slate-900/80 backdrop-blur-sm shadow-2xl overflow-hidden">
                <!-- Browser chrome bar -->
                <div class="flex items-center gap-2 px-4 py-3 border-b border-white/10 bg-slate-900/60">
                    <span class="w-3 h-3 rounded-full bg-red-400/60" />
                    <span class="w-3 h-3 rounded-full bg-yellow-400/60" />
                    <span class="w-3 h-3 rounded-full bg-green-400/60" />
                    <div class="ml-4 flex-1 bg-white/5 border border-white/10 rounded-md h-6 flex items-center px-3">
                        <span class="text-slate-500 text-xs">app.flowflex.com/dashboard</span>
                    </div>
                </div>
                <!-- Dashboard mockup -->
                <div class="p-4 sm:p-6 flex gap-4 h-44 sm:h-64">
                    <!-- Sidebar — hidden on xs -->
                    <div class="hidden sm:flex w-44 bg-white/5 border border-white/8 rounded-xl p-3 flex-col gap-2 shrink-0">
                        <div class="w-8 h-8 rounded-lg bg-ocean-500/20 border border-ocean-500/30" />
                        <div class="h-2 w-full bg-white/10 rounded" />
                        <div class="h-2 w-3/4 bg-white/8 rounded" />
                        <div class="h-2 w-1/2 bg-white/6 rounded" />
                        <div class="mt-3 space-y-1.5">
                            <div v-for="si in 4" :key="si" class="h-2 rounded" :class="si === 2 ? 'bg-ocean-500/30' : 'bg-white/8'" :style="`width: ${70 + si * 5}%`" />
                        </div>
                    </div>
                    <!-- Main content -->
                    <div class="flex-1 grid grid-rows-2 gap-3 min-w-0">
                        <div class="grid grid-cols-3 gap-3">
                            <div v-for="(d, di) in [{ val: '142', color: '#2199C8' }, { val: '38', color: '#7C3AED' }, { val: '€24k', color: '#059669' }]" :key="di"
                                class="bg-white/5 border border-white/8 rounded-xl p-2 sm:p-4 flex flex-col justify-between">
                                <div class="h-1.5 w-2/3 bg-white/10 rounded" />
                                <div>
                                    <div class="text-base sm:text-2xl font-bold" :style="`color: ${d.color}`">{{ d.val }}</div>
                                    <div class="h-1 w-full bg-white/8 rounded mt-1" />
                                </div>
                            </div>
                        </div>
                        <div class="bg-white/5 border border-white/8 rounded-xl p-2 sm:p-4 flex items-end gap-1">
                            <div v-for="bh in [30, 55, 40, 70, 85, 60, 90, 75, 95, 65, 80, 70]" :key="bh"
                                class="flex-1 rounded-t bg-gradient-to-t from-ocean-600/60 to-ocean-400/30"
                                :style="`height: ${bh}%`" />
                        </div>
                    </div>
                </div>
            </div>
            <!-- Glow under mockup -->
            <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 w-2/3 h-16 bg-ocean-500/10 blur-3xl rounded-full" />
        </div>

        <!-- Scroll indicator -->
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex flex-col items-center gap-1 text-white/20 animate-bounce">
            <ChevronDown class="w-5 h-5" />
        </div>
    </section>

    <!-- ─── TOOLS REPLACED ─────────────────────────────────────────── -->
    <section class="bg-slate-950 border-y border-white/5 py-10 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="max-w-5xl mx-auto space-y-4">
            <p class="text-center text-slate-500 text-xs font-semibold uppercase tracking-widest">{{ t('toolsReplaced.label') }}</p>
            <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-3">
                <span
                    v-for="tool in toolsReplaced"
                    :key="tool.name"
                    class="flex items-center gap-2 text-sm font-medium text-white/50 hover:text-white/80 transition-colors cursor-default"
                >
                    <span class="w-2 h-2 rounded-sm shrink-0" :style="`background: ${tool.color}`" />
                    {{ tool.name }}
                </span>
                <span class="text-slate-600 text-xs">+ many more</span>
            </div>
        </div>
    </section>

    <!-- ─── PROBLEM ────────────────────────────────────────────────── -->
    <section class="bg-white dark:bg-slate-900 py-28 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-center">
            <div
                v-motion
                :initial="{ opacity: 0, x: -30 }"
                :visible="{ opacity: 1, x: 0, transition: { duration: 700 } }"
            >
                <h2 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white leading-tight mb-6">
                    {{ t('problem.heading') }}
                </h2>
                <p class="text-slate-600 dark:text-slate-400 text-lg leading-relaxed mb-8">
                    {{ t('problem.resolution') }}
                </p>
                <Link href="/demo" class="inline-flex items-center gap-2 text-ocean-600 dark:text-ocean-400 font-semibold hover:gap-3 transition-all">
                    {{ t('problem.cta') }} <ArrowRight class="w-4 h-4" />
                </Link>
            </div>
            <div class="space-y-4">
                <div
                    v-for="(card, i) in [
                        { icon: AlertCircle, titleKey: 'problem.tooManyTools.title', descKey: 'problem.tooManyTools.description', color: '#EF4444', bg: '#FEF2F2', darkBg: '#1F1315' },
                        { icon: TrendingDown, titleKey: 'problem.payingForWaste.title', descKey: 'problem.payingForWaste.description', color: '#D97706', bg: '#FFFBEB', darkBg: '#1C1610' },
                        { icon: Database, titleKey: 'problem.dataEverywhere.title', descKey: 'problem.dataEverywhere.description', color: '#9333EA', bg: '#F5F3FF', darkBg: '#16101F' },
                    ]"
                    :key="card.titleKey"
                    class="flex items-start gap-5 p-6 rounded-2xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-800/50 hover:shadow-md dark:hover:shadow-slate-900/60 transition-all hover:-translate-y-0.5"
                    v-motion
                    :initial="{ opacity: 0, x: 30 }"
                    :visible="{ opacity: 1, x: 0, transition: { duration: 600, delay: i * 120 } }"
                >
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0" :style="`background: ${card.color}18`">
                        <component :is="card.icon" class="w-6 h-6" :style="`color: ${card.color}`" />
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white mb-1">{{ t(card.titleKey) }}</h3>
                        <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">{{ t(card.descKey) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── FEATURES SHOWCASE ─────────────────────────────────────── -->
    <section class="bg-slate-50 dark:bg-slate-900/60 py-28 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-24">
            <div class="text-center max-w-2xl mx-auto">
                <h2 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white">{{ t('features.showcaseHeading') }}</h2>
            </div>

            <div
                v-for="(feature, i) in features"
                :key="feature.title"
                class="grid lg:grid-cols-2 gap-12 items-center"
                v-motion
                :initial="{ opacity: 0, y: 40 }"
                :visible="{ opacity: 1, y: 0, transition: { duration: 700 } }"
            >
                <!-- Text -->
                <div class="space-y-6" :class="i % 2 === 1 ? 'lg:order-2' : ''">
                    <div class="inline-flex items-center gap-2.5 rounded-full px-4 py-2 text-sm font-semibold" :style="`background: ${feature.color}14; color: ${feature.color}`">
                        <component :is="feature.icon" class="w-4 h-4" />
                        {{ feature.label }}
                    </div>
                    <h3 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white leading-tight">{{ feature.title }}</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-lg leading-relaxed">{{ feature.desc }}</p>
                    <ul class="space-y-2.5">
                        <li v-for="item in feature.checklist" :key="item" class="flex items-center gap-3 text-slate-700 dark:text-slate-300 text-sm">
                            <CheckCircle2 class="w-4 h-4 shrink-0" :style="`color: ${feature.color}`" />
                            {{ item }}
                        </li>
                    </ul>
                </div>

                <!-- Visual card — distinct per feature -->
                <div class="relative" :class="i % 2 === 1 ? 'lg:order-1' : ''">
                    <div class="absolute inset-0 rounded-3xl blur-3xl opacity-15" :style="`background: ${feature.color}`" />
                    <div class="relative rounded-3xl border border-white/10 bg-gradient-to-br from-slate-800 to-slate-900 p-7 shadow-2xl">
                        <!-- Card header -->
                        <div class="flex items-center gap-3 pb-5 mb-5 border-b border-white/8">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center" :style="`background: ${feature.color}25`">
                                <component :is="feature.icon" class="w-4 h-4" :style="`color: ${feature.color}`" />
                            </div>
                            <div class="h-2.5 w-32 rounded bg-white/20" />
                            <div class="ml-auto flex gap-1.5">
                                <div class="w-2 h-2 rounded-full bg-white/10" />
                                <div class="w-2 h-2 rounded-full bg-white/10" />
                                <div class="w-2 h-2 rounded-full" :style="`background: ${feature.color}60`" />
                            </div>
                        </div>

                        <!-- Feature 0: Module toggle panel -->
                        <template v-if="i === 0">
                            <div class="space-y-2">
                                <div
                                    v-for="mod in [
                                        { name: 'HR & People', on: true, sub: 'Employees · Leave · Payroll' },
                                        { name: 'Finance', on: true, sub: 'Invoicing · Budgets · Reports' },
                                        { name: 'Projects & Work', on: true, sub: 'Tasks · Timesheets · Docs' },
                                        { name: 'CRM & Sales', on: false, sub: 'Contacts · Pipeline · Deals' },
                                        { name: 'E-commerce', on: false, sub: 'Products · Orders · Store' },
                                    ]"
                                    :key="mod.name"
                                    class="flex items-center justify-between px-4 py-3 rounded-xl transition-colors"
                                    :class="mod.on ? 'bg-white/6' : 'bg-transparent opacity-50'"
                                >
                                    <div>
                                        <p class="text-sm font-medium text-white leading-tight">{{ mod.name }}</p>
                                        <p class="text-xs text-white/35 mt-0.5">{{ mod.sub }}</p>
                                    </div>
                                    <!-- Toggle -->
                                    <div
                                        class="relative w-11 h-6 rounded-full shrink-0 transition-colors"
                                        :style="mod.on ? `background: ${feature.color}` : 'background: rgba(255,255,255,0.12)'"
                                    >
                                        <div
                                            class="absolute top-1 w-4 h-4 rounded-full bg-white shadow-sm transition-all"
                                            :class="mod.on ? 'right-1' : 'left-1'"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 flex items-center gap-2 px-1">
                                <div class="w-1.5 h-1.5 rounded-full animate-pulse" :style="`background: ${feature.color}`" />
                                <span class="text-xs text-white/35">3 modules active · billing adjusts next cycle</span>
                            </div>
                        </template>

                        <!-- Feature 1: Data flow diagram -->
                        <template v-else-if="i === 1">
                            <div class="space-y-3">
                                <!-- Satellite modules top row -->
                                <div class="grid grid-cols-3 gap-3">
                                    <div v-for="node in [
                                        { label: 'HR & People', color: '#7C3AED', icon: '👤' },
                                        { label: 'Finance', color: '#059669', icon: '€' },
                                        { label: 'Projects', color: '#4F46E5', icon: '#' },
                                    ]" :key="node.label"
                                        class="flex flex-col items-center gap-2 p-3 rounded-2xl border border-white/8 bg-white/4"
                                    >
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-bold text-white" :style="`background: ${node.color}40`">{{ node.icon }}</div>
                                        <span class="text-xs text-white/50 font-medium text-center leading-tight">{{ node.label }}</span>
                                    </div>
                                </div>
                                <!-- Connecting arrows down -->
                                <div class="flex justify-around px-8">
                                    <div class="w-px h-6 bg-gradient-to-b from-white/20 to-transparent" />
                                    <div class="w-px h-6 bg-gradient-to-b from-white/20 to-transparent" />
                                    <div class="w-px h-6 bg-gradient-to-b from-white/20 to-transparent" />
                                </div>
                                <!-- Central FlowFlex core -->
                                <div class="flex items-center justify-center">
                                    <div
                                        class="flex items-center gap-3 px-6 py-4 rounded-2xl border"
                                        :style="`background: ${feature.color}18; border-color: ${feature.color}50`"
                                    >
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center" :style="`background: ${feature.color}35`">
                                            <Share2 class="w-4 h-4" :style="`color: ${feature.color}`" />
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-white">FlowFlex Core</p>
                                            <p class="text-xs text-white/40">Shared data layer</p>
                                        </div>
                                        <div class="w-2 h-2 rounded-full animate-pulse ml-2" :style="`background: ${feature.color}`" />
                                    </div>
                                </div>
                                <!-- Connecting arrows down -->
                                <div class="flex justify-around px-8">
                                    <div class="w-px h-6 bg-gradient-to-t from-white/20 to-transparent" />
                                    <div class="w-px h-6 bg-gradient-to-t from-white/20 to-transparent" />
                                    <div class="w-px h-6 bg-gradient-to-t from-white/20 to-transparent" />
                                </div>
                                <!-- Satellite modules bottom row -->
                                <div class="grid grid-cols-3 gap-3">
                                    <div v-for="node in [
                                        { label: 'CRM & Sales', color: '#2563EB', icon: 'C' },
                                        { label: 'Analytics', color: '#9333EA', icon: '~' },
                                        { label: 'Operations', color: '#D97706', icon: 'O' },
                                    ]" :key="node.label"
                                        class="flex flex-col items-center gap-2 p-3 rounded-2xl border border-white/8 bg-white/4"
                                    >
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-bold text-white" :style="`background: ${node.color}40`">{{ node.icon }}</div>
                                        <span class="text-xs text-white/50 font-medium text-center leading-tight">{{ node.label }}</span>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Feature 2: GDPR compliance dashboard -->
                        <template v-else>
                            <div class="space-y-4">
                                <div v-for="(item, ci) in [
                                    { label: 'EU Data Storage', value: 100, note: 'AWS eu-west-1 (Dublin)' },
                                    { label: 'GDPR Article 28 DPA', value: 100, note: 'Included on all plans' },
                                    { label: 'Encryption at rest', value: 100, note: 'AES-256' },
                                    { label: 'Encryption in transit', value: 100, note: 'TLS 1.3' },
                                ]" :key="item.label" class="space-y-1.5"
                                    v-motion
                                    :initial="{ opacity: 0, x: -10 }"
                                    :visible="{ opacity: 1, x: 0, transition: { duration: 500, delay: ci * 100 } }"
                                >
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-white/80">{{ item.label }}</span>
                                        <span class="text-xs text-white/35">{{ item.note }}</span>
                                    </div>
                                    <div class="h-1.5 w-full rounded-full bg-white/8 overflow-hidden">
                                        <div class="h-full rounded-full" :style="`width: ${item.value}%; background: linear-gradient(90deg, ${feature.color}, ${feature.color}bb)`" />
                                    </div>
                                </div>
                                <div class="mt-4 grid grid-cols-2 gap-3">
                                    <div v-for="badge in [
                                        { label: 'Right to Erasure', ok: true },
                                        { label: 'Data Portability', ok: true },
                                        { label: 'Privacy by Design', ok: true },
                                        { label: 'EN + NL Interface', ok: true },
                                    ]" :key="badge.label"
                                        class="flex items-center gap-2 px-3 py-2 rounded-xl bg-white/5 border border-white/8"
                                    >
                                        <CheckCircle2 class="w-3.5 h-3.5 shrink-0" :style="`color: ${feature.color}`" />
                                        <span class="text-xs text-white/60 font-medium leading-tight">{{ badge.label }}</span>
                                    </div>
                                </div>
                            </div>
                        </template>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── MODULE GRID ────────────────────────────────────────────── -->
    <section class="bg-white dark:bg-slate-900 py-28 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div
                class="text-center mb-16 max-w-2xl mx-auto"
                v-motion
                :initial="{ opacity: 0, y: 30 }"
                :visible="{ opacity: 1, y: 0, transition: { duration: 600 } }"
            >
                <h2 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white mb-4">{{ t('modules.heading') }}</h2>
                <p class="text-slate-600 dark:text-slate-400 text-lg">{{ t('modules.subheading') }}</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                <div
                    v-for="(domain, i) in domains"
                    :key="domain.name"
                    class="group relative bg-white dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60 rounded-2xl p-6 hover:shadow-xl dark:hover:shadow-slate-950/60 hover:-translate-y-1.5 transition-all overflow-hidden"
                    v-motion
                    :initial="{ opacity: 0, scale: 0.94 }"
                    :visible="{ opacity: 1, scale: 1, transition: { duration: 450, delay: i * 35 } }"
                >
                    <!-- Color accent line -->
                    <div class="absolute top-0 left-0 right-0 h-0.5 opacity-0 group-hover:opacity-100 transition-opacity" :style="`background: linear-gradient(90deg, ${domain.gradientFrom}, ${domain.gradientTo})`" />
                    <!-- Glow background on hover -->
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity" :style="`background: radial-gradient(circle at 20% 20%, ${domain.color}08, transparent 70%)`" />

                    <div class="relative">
                        <div
                            class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4 transition-transform group-hover:scale-110 shadow-sm"
                            :style="`background: linear-gradient(135deg, ${domain.gradientFrom}, ${domain.gradientTo})`"
                        >
                            <component :is="domain.icon" class="w-6 h-6 text-white" />
                        </div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-base mb-2">{{ domain.name }}</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-xs leading-relaxed mb-4">{{ domain.description }}</p>
                        <div class="flex flex-wrap gap-1.5">
                            <span
                                v-for="mod in domain.modules.slice(0, 3)"
                                :key="mod.name"
                                class="text-xs px-2 py-0.5 rounded-full font-medium"
                                :style="`background: ${domain.color}14; color: ${domain.color}`"
                            >{{ mod.name }}</span>
                            <span v-if="domain.modules.length > 3" class="text-xs px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 font-medium">+{{ domain.modules.length - 3 }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-12">
                <Link href="/features" class="inline-flex items-center gap-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-6 py-3 rounded-xl font-semibold hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors">
                    {{ t('modules.seeAll') }} <ArrowRight class="w-4 h-4" />
                </Link>
            </div>
        </div>
    </section>

    <!-- ─── STATS ──────────────────────────────────────────────────── -->
    <section class="bg-gradient-to-br from-ocean-950 via-ocean-900 to-slate-950 py-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <div class="absolute inset-0 bg-[linear-gradient(rgba(33,153,200,0.06)_1px,transparent_1px),linear-gradient(90deg,rgba(33,153,200,0.06)_1px,transparent_1px)] bg-[size:56px_56px] pointer-events-none" />
        <div class="relative max-w-5xl mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div v-for="(stat, i) in [
                    { value: `${moduleCount}+`, labelKey: 'stats.modules' },
                    { value: domainCount.toString(), labelKey: 'stats.domains' },
                    { value: '300+', labelKey: 'stats.features' },
                    { value: '1', labelKey: 'stats.dataLayer' },
                ]" :key="stat.labelKey"
                    v-motion
                    :initial="{ opacity: 0, y: 20 }"
                    :visible="{ opacity: 1, y: 0, transition: { duration: 500, delay: i * 100 } }"
                >
                    <div class="text-6xl font-black text-white mb-2 leading-none">{{ stat.value }}</div>
                    <div class="text-ocean-400 text-sm font-medium">{{ t(stat.labelKey) }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── TESTIMONIALS ───────────────────────────────────────────── -->
    <section class="bg-white dark:bg-slate-900 py-28 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div
                class="text-center mb-16"
                v-motion
                :initial="{ opacity: 0, y: 30 }"
                :visible="{ opacity: 1, y: 0, transition: { duration: 600 } }"
            >
                <h2 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white">{{ t('testimonials.heading') }}</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                <div
                    v-for="(t2, i) in testimonials"
                    :key="t2.name"
                    class="relative bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60 rounded-3xl p-8 flex flex-col justify-between hover:shadow-lg dark:hover:shadow-slate-950/50 transition-all hover:-translate-y-1"
                    v-motion
                    :initial="{ opacity: 0, y: 30 }"
                    :visible="{ opacity: 1, y: 0, transition: { duration: 600, delay: i * 120 } }"
                >
                    <Quote class="w-8 h-8 mb-6 opacity-20" :style="`color: ${t2.color}`" />
                    <p class="text-slate-700 dark:text-slate-300 leading-relaxed text-base flex-1 mb-8">
                        "{{ t2.quote }}"
                    </p>
                    <div class="flex items-center gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold shrink-0" :style="`background: linear-gradient(135deg, ${t2.color}, ${t2.color}cc)`">
                            {{ t2.initials }}
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-white text-sm">{{ t2.name }}</p>
                            <p class="text-slate-500 dark:text-slate-400 text-xs">{{ t2.role }} · {{ t2.company }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── HOW IT WORKS ───────────────────────────────────────────── -->
    <section class="bg-slate-50 dark:bg-slate-900/60 py-28 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <div
                class="text-center mb-20"
                v-motion
                :initial="{ opacity: 0, y: 30 }"
                :visible="{ opacity: 1, y: 0, transition: { duration: 600 } }"
            >
                <h2 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white">{{ t('howItWorks.heading') }}</h2>
            </div>
            <div class="relative">
                <!-- Connecting line -->
                <div class="absolute top-8 left-1/2 -translate-x-1/2 hidden lg:block w-[calc(100%-200px)] h-px bg-gradient-to-r from-transparent via-ocean-400/40 to-transparent" />
                <div class="grid lg:grid-cols-3 gap-12">
                    <div
                        v-for="(step, i) in steps"
                        :key="step.num"
                        class="flex flex-col items-center text-center"
                        v-motion
                        :initial="{ opacity: 0, y: 30 }"
                        :visible="{ opacity: 1, y: 0, transition: { duration: 600, delay: i * 150 } }"
                    >
                        <!-- Step indicator -->
                        <div class="relative mb-6">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-ocean-500 to-ocean-600 flex items-center justify-center shadow-lg shadow-ocean-500/30">
                                <component :is="step.icon" class="w-7 h-7 text-white" />
                            </div>
                            <div class="absolute -top-2 -right-3 w-6 h-6 rounded-full bg-white dark:bg-slate-800 border-2 border-ocean-400 flex items-center justify-center">
                                <span class="text-ocean-600 dark:text-ocean-400 text-xs font-black">{{ i + 1 }}</span>
                            </div>
                        </div>
                        <div class="font-mono text-ocean-400/60 text-xs mb-3 tracking-widest">{{ step.num }}</div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">{{ step.title }}</h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-sm">{{ step.desc }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── PRICING TEASER ─────────────────────────────────────────── -->
    <section class="bg-white dark:bg-slate-900 py-28 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <div
                class="text-center mb-16"
                v-motion
                :initial="{ opacity: 0, y: 30 }"
                :visible="{ opacity: 1, y: 0, transition: { duration: 600 } }"
            >
                <h2 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white mb-4">{{ t('pricing.heading') }}</h2>
                <p class="text-slate-600 dark:text-slate-400 text-lg">{{ t('pricing.subheading') }}</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl p-8 space-y-4 hover:shadow-md transition-all">
                    <Zap class="w-8 h-8 text-slate-500 dark:text-slate-400" />
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ t('pricing.starter.name') }}</h3>
                    <div class="text-4xl font-black text-slate-900 dark:text-white">€29<span class="text-base font-normal text-slate-500">/user/mo</span></div>
                    <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                        <li class="flex items-center gap-2"><CheckCircle2 class="w-4 h-4 text-ocean-500 shrink-0" /> Up to 10 users</li>
                        <li class="flex items-center gap-2"><CheckCircle2 class="w-4 h-4 text-ocean-500 shrink-0" /> 5 modules</li>
                        <li class="flex items-center gap-2"><CheckCircle2 class="w-4 h-4 text-ocean-500 shrink-0" /> Email support</li>
                    </ul>
                    <Link href="/demo" class="block text-center border-2 border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-300 px-4 py-2.5 rounded-xl text-sm font-semibold hover:border-ocean-400 hover:text-ocean-600 dark:hover:text-ocean-400 transition-colors">
                        {{ t('pricing.getStarted') }}
                    </Link>
                </div>

                <!-- Pro — highlighted -->
                <div class="relative bg-gradient-to-b from-ocean-500 to-ocean-600 rounded-2xl p-8 space-y-4 shadow-2xl shadow-ocean-500/30">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-white text-ocean-600 text-xs font-bold px-4 py-1.5 rounded-full uppercase tracking-wide shadow-sm">
                        {{ t('pricing.mostPopular') }}
                    </div>
                    <Building2 class="w-8 h-8 text-white/70" />
                    <h3 class="text-xl font-bold text-white">{{ t('pricing.pro.name') }}</h3>
                    <div class="text-4xl font-black text-white">€79<span class="text-base font-normal text-white/60">/user/mo</span></div>
                    <ul class="space-y-2 text-sm text-white/80">
                        <li class="flex items-center gap-2"><CheckCircle2 class="w-4 h-4 text-white shrink-0" /> Up to 100 users</li>
                        <li class="flex items-center gap-2"><CheckCircle2 class="w-4 h-4 text-white shrink-0" /> All modules</li>
                        <li class="flex items-center gap-2"><CheckCircle2 class="w-4 h-4 text-white shrink-0" /> Priority support</li>
                    </ul>
                    <Link href="/demo" class="block text-center bg-white text-ocean-600 px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-ocean-50 transition-colors">
                        {{ t('pricing.getStarted') }}
                    </Link>
                </div>

                <div class="bg-slate-900 dark:bg-slate-950 border border-slate-700 rounded-2xl p-8 space-y-4 hover:shadow-md transition-all">
                    <Layers class="w-8 h-8 text-ocean-400" />
                    <h3 class="text-xl font-bold text-white">{{ t('pricing.enterprise.name') }}</h3>
                    <div class="text-4xl font-black text-white">{{ t('pricing.enterprise.price') }}</div>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li class="flex items-center gap-2"><CheckCircle2 class="w-4 h-4 text-ocean-400 shrink-0" /> Unlimited everything</li>
                        <li class="flex items-center gap-2"><CheckCircle2 class="w-4 h-4 text-ocean-400 shrink-0" /> Dedicated support</li>
                        <li class="flex items-center gap-2"><CheckCircle2 class="w-4 h-4 text-ocean-400 shrink-0" /> Custom integrations</li>
                    </ul>
                    <Link href="/demo" class="block text-center border-2 border-slate-700 text-slate-300 px-4 py-2.5 rounded-xl text-sm font-semibold hover:border-ocean-500 hover:text-ocean-400 transition-colors">
                        {{ t('pricing.talkToSales') }}
                    </Link>
                </div>
            </div>
            <div class="text-center mt-10">
                <Link href="/pricing" class="inline-flex items-center gap-2 text-ocean-600 dark:text-ocean-400 font-semibold hover:gap-3 transition-all">
                    {{ t('pricing.seeFullPricing') }} <ArrowRight class="w-4 h-4" />
                </Link>
            </div>
        </div>
    </section>

    <!-- ─── FINAL CTA ──────────────────────────────────────────────── -->
    <section class="relative bg-[#050E1A] py-32 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-ocean-950 via-ocean-900/60 to-slate-950 pointer-events-none" />
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-ocean-500/10 blur-[120px] rounded-full pointer-events-none" />
        <div class="absolute inset-0 bg-[linear-gradient(rgba(33,153,200,0.04)_1px,transparent_1px),linear-gradient(90deg,rgba(33,153,200,0.04)_1px,transparent_1px)] bg-[size:64px_64px] pointer-events-none" />

        <div
            class="relative max-w-3xl mx-auto text-center space-y-8"
            v-motion
            :initial="{ opacity: 0, y: 30 }"
            :visible="{ opacity: 1, y: 0, transition: { duration: 700 } }"
        >
            <h2 class="text-5xl md:text-6xl font-black text-white leading-tight">
                {{ t('cta.heading') }}
            </h2>
            <p class="text-xl text-slate-400 leading-relaxed">
                {{ t('cta.subheading') }}
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <Link
                    href="/demo"
                    class="group inline-flex items-center gap-2.5 bg-ocean-500 hover:bg-ocean-400 text-white px-10 py-4 rounded-2xl font-bold text-lg transition-all shadow-[0_0_60px_rgba(33,153,200,0.4)] hover:shadow-[0_0_80px_rgba(33,153,200,0.6)] hover:-translate-y-0.5"
                >
                    {{ t('cta.button') }}
                    <ArrowRight class="w-5 h-5 transition-transform group-hover:translate-x-1" />
                </Link>
                <Link href="/features" class="text-slate-400 hover:text-white font-medium transition-colors text-sm">
                    {{ t('cta.or') }} {{ t('hero.secondaryCta') }} →
                </Link>
            </div>
            <p class="text-slate-600 text-sm">{{ t('cta.trustNote') }}</p>
        </div>
    </section>
</template>

<style scoped>
/* intentionally empty — all utility classes above */
</style>
