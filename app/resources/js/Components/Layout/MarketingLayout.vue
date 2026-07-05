<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import Logo from '../UI/Logo.vue'
import { domainColors } from '../../data/marketing'

const page = usePage()
const menuOpen = ref(false)
const openDrop = ref<string | null>(null)
let hoverTimer: ReturnType<typeof setTimeout> | null = null

// The nav explains FlowFlex (departments + how the model works) rather than
// pushing a sale — SaaS mega-menu practice: educate, describe, group by the
// visitor's problem. Hover-intent open, click toggle, esc/outside close.
const productDepartments = [
    { key: 'hr', name: 'HR & people', desc: 'Profiles, leave, payroll — one employee record.', href: '/product/hr' },
    { key: 'finance', name: 'Finance & accounting', desc: 'Invoices and books on a live ledger.', href: '/product/finance' },
    { key: 'crm', name: 'CRM & sales', desc: 'Pipeline that sees tickets and invoices.', href: '/product/crm' },
    { key: 'projects', name: 'Projects & work', desc: 'Boards and time, aware of leave and deals.', href: '/product/projects' },
]

const productHow = [
    { name: 'How FlowFlex works', desc: 'One database, modules as switches.', href: '/product' },
    { name: 'Switching from another stack', desc: 'Domain by domain, never big-bang.', href: '/switch' },
    { name: 'What your stack costs today', desc: 'The patchwork-tax calculator.', href: '/calculator' },
]

const resources = [
    { name: 'Help center', desc: 'Answers, written by humans.', href: '/help' },
    { name: 'Customer story', desc: 'Nine tools became one at Veldkamp.', href: '/customers/veldkamp-logistics' },
    { name: 'Trust & security', desc: 'EU-hosted, GDPR by design, exportable.', href: '/trust' },
    { name: 'Changelog', desc: 'New on the switchboard, as it ships.', href: '/changelog' },
    { name: 'Status', desc: 'Uptime by domain, incidents post-mortemed.', href: '/status' },
]

const enterDrop = (name: string): void => {
    if (hoverTimer) clearTimeout(hoverTimer)
    openDrop.value = name
}
const leaveDrop = (): void => {
    if (hoverTimer) clearTimeout(hoverTimer)
    hoverTimer = setTimeout(() => (openDrop.value = null), 160)
}
const toggleDrop = (name: string): void => {
    openDrop.value = openDrop.value === name ? null : name
}

const onKeydown = (e: KeyboardEvent): void => {
    if (e.key === 'Escape') openDrop.value = null
}
const onDocClick = (e: MouseEvent): void => {
    if (!(e.target as HTMLElement).closest('.ff-nav')) openDrop.value = null
}
onMounted(() => {
    document.addEventListener('keydown', onKeydown)
    document.addEventListener('click', onDocClick)
})
onBeforeUnmount(() => {
    document.removeEventListener('keydown', onKeydown)
    document.removeEventListener('click', onDocClick)
})

const isActive = (href: string): boolean => page.url === href || page.url.startsWith(href + '/')
</script>

<template>
    <div class="ff min-h-screen flex flex-col">
        <a
            href="#main"
            class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:bg-white focus:px-4 focus:py-2 focus:rounded-lg focus:shadow"
        >Skip to content</a>

        <header class="ff-nav-outer">
            <div class="wrap">
                <nav class="ff-nav" aria-label="Main">
                    <Link href="/" aria-label="FlowFlex home"><Logo :size="25" /></Link>

                    <div class="ff-nav-links items-center">
                        <!-- Product dropdown: show the departments, explain the model -->
                        <div class="relative" @mouseenter="enterDrop('product')" @mouseleave="leaveDrop()">
                            <button
                                type="button"
                                class="ff-nav-item"
                                :class="{ on: isActive('/product') || isActive('/modules') }"
                                aria-haspopup="true"
                                :aria-expanded="openDrop === 'product'"
                                @click="toggleDrop('product')"
                            >
                                Product
                                <svg class="caret" width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 6l4 4 4-4" /></svg>
                            </button>

                            <div v-if="openDrop === 'product'" class="ff-drop text-left">
                                <div class="ff-drop-cols">
                                    <div class="ff-drop-col">
                                        <p class="ff-drop-label">Departments — live today</p>
                                        <Link v-for="d in productDepartments" :key="d.key" :href="d.href" class="ff-drop-row" @click="openDrop = null">
                                            <span class="chip" :style="{ background: domainColors[d.key] }"></span>
                                            <span>
                                                <span class="t">{{ d.name }}</span>
                                                <span class="d">{{ d.desc }}</span>
                                            </span>
                                        </Link>
                                    </div>
                                    <div class="ff-drop-col">
                                        <p class="ff-drop-label">How it works</p>
                                        <Link v-for="r in productHow" :key="r.href" :href="r.href" class="ff-drop-row" @click="openDrop = null">
                                            <span>
                                                <span class="t">{{ r.name }}</span>
                                                <span class="d">{{ r.desc }}</span>
                                            </span>
                                        </Link>
                                    </div>
                                </div>
                                <div class="ff-drop-foot">
                                    <span>73 modules · one database · 0 integrations</span>
                                    <Link href="/modules" @click="openDrop = null">All modules <span class="arr">→</span></Link>
                                </div>
                            </div>
                        </div>

                        <Link href="/pricing" :class="{ on: isActive('/pricing') }">Pricing</Link>

                        <!-- Resources dropdown -->
                        <div class="relative" @mouseenter="enterDrop('resources')" @mouseleave="leaveDrop()">
                            <button
                                type="button"
                                class="ff-nav-item"
                                aria-haspopup="true"
                                :aria-expanded="openDrop === 'resources'"
                                @click="toggleDrop('resources')"
                            >
                                Resources
                                <svg class="caret" width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 6l4 4 4-4" /></svg>
                            </button>

                            <div v-if="openDrop === 'resources'" class="ff-drop text-left">
                                <div class="ff-drop-col" style="width: 300px">
                                    <Link v-for="r in resources" :key="r.href" :href="r.href" class="ff-drop-row" @click="openDrop = null">
                                        <span>
                                            <span class="t">{{ r.name }}</span>
                                            <span class="d">{{ r.desc }}</span>
                                        </span>
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <Link href="/about" :class="{ on: isActive('/about') }">About</Link>
                        <Link href="/contact" :class="{ on: isActive('/contact') }">Contact</Link>
                    </div>

                    <div class="flex items-center" style="gap: 18px">
                        <a href="/app/login" class="hidden md:inline text-[14.5px] font-medium text-(--ink-soft) hover:text-(--ink) transition-colors">Sign in</a>
                        <Link href="/contact" class="ff-btn sm hidden md:inline-flex">Talk to us</Link>
                        <button
                            type="button"
                            class="md:hidden p-1"
                            :aria-expanded="menuOpen"
                            aria-label="Menu"
                            @click="menuOpen = !menuOpen"
                        >
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#111827" stroke-width="1.8" stroke-linecap="round">
                                <path v-if="!menuOpen" d="M4 7h16M4 12h16M4 17h16" />
                                <path v-else d="M6 6l12 12M18 6L6 18" />
                            </svg>
                        </button>
                    </div>
                </nav>
            </div>

            <!-- Mobile: same content as the dropdowns, grouped accordion-style -->
            <div v-if="menuOpen" class="ff-nav-mobile md:hidden">
                <p class="grp">Departments</p>
                <Link v-for="d in productDepartments" :key="d.key" :href="d.href" class="sub" @click="menuOpen = false">
                    <span class="chip" :style="{ background: domainColors[d.key] }"></span>{{ d.name }}
                </Link>
                <Link href="/modules" class="sub" @click="menuOpen = false">All 73 modules</Link>
                <p class="grp">How it works</p>
                <Link v-for="r in productHow" :key="r.href" :href="r.href" class="sub" @click="menuOpen = false">{{ r.name }}</Link>
                <Link href="/pricing" class="sub" @click="menuOpen = false">Pricing</Link>
                <p class="grp">Resources</p>
                <Link v-for="r in resources" :key="r.href" :href="r.href" class="sub" @click="menuOpen = false">{{ r.name }}</Link>
                <p class="grp">Company</p>
                <Link href="/about" class="sub" @click="menuOpen = false">About</Link>
                <Link href="/contact" class="sub" @click="menuOpen = false">Contact</Link>
                <a href="/app/login" class="sub" @click="menuOpen = false">Sign in</a>
            </div>
        </header>

        <main id="main" class="flex-1">
            <slot />
        </main>

        <footer class="ff-footer">
            <div class="wrap">
                <div class="ff-footer-grid">
                    <div>
                        <Link href="/" aria-label="FlowFlex home"><Logo light :size="22" /></Link>
                        <p class="mt-4 text-[13.5px] leading-relaxed text-white/50 max-w-[240px]">
                            One platform. Every tool. Always flexible. Built for teams of 50 to 500.
                        </p>
                    </div>
                    <div>
                        <h4>Product</h4>
                        <Link class="lnk" href="/modules">All modules</Link>
                        <Link class="lnk" href="/pricing">Pricing</Link>
                        <Link class="lnk" href="/switch">Switching over</Link>
                        <Link class="lnk" href="/calculator">Patchwork calculator</Link>
                        <Link class="lnk" href="/changelog">Changelog</Link>
                        <a class="lnk" href="/app/login">Sign in</a>
                    </div>
                    <div>
                        <h4>Company</h4>
                        <Link class="lnk" href="/about">About</Link>
                        <Link class="lnk" href="/customers/veldkamp-logistics">Customers</Link>
                        <Link class="lnk" href="/contact">Contact</Link>
                        <Link class="lnk" href="/help">Help center</Link>
                    </div>
                    <div>
                        <h4>Legal &amp; trust</h4>
                        <Link class="lnk" href="/trust">Trust &amp; security</Link>
                        <Link class="lnk" href="/status">Status</Link>
                        <Link class="lnk" href="/terms">Terms</Link>
                        <Link class="lnk" href="/privacy">Privacy</Link>
                    </div>
                </div>
                <div class="ff-footer-base">
                    <span>© 2026 FlowFlex — everything flows</span>
                    <span>EU-hosted · GDPR-first · data portable</span>
                </div>
            </div>
        </footer>
    </div>
</template>
