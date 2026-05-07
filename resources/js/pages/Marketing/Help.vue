<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { Search, BookOpen, ChevronRight } from 'lucide-vue-next'

interface Article {
    id: string
    title: string
    slug: string
    excerpt: string | null
    display_order: number
}

interface Category {
    id: string
    name: string
    slug: string
    description: string | null
    icon: string | null
    articles: Article[]
}

const props = defineProps<{ categories: Category[] }>()

const query = ref('')

const filteredCategories = computed(() => {
    if (!query.value.trim()) return props.categories

    const q = query.value.toLowerCase()
    return props.categories
        .map(cat => ({
            ...cat,
            articles: cat.articles.filter(a =>
                a.title.toLowerCase().includes(q) ||
                (a.excerpt ?? '').toLowerCase().includes(q)
            ),
        }))
        .filter(cat => cat.articles.length > 0 || cat.name.toLowerCase().includes(q))
})

const totalArticles = computed(() => props.categories.reduce((sum, c) => sum + c.articles.length, 0))
</script>

<template>
    <Head title="Help Centre — FlowFlex">
        <meta name="description" content="Find answers, guides, and documentation for FlowFlex. Browse by category or search across all help articles." />
    </Head>

    <!-- Hero -->
    <section class="bg-[#050E1A] py-24 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center space-y-6">
            <h1 class="text-4xl sm:text-5xl font-black text-white leading-tight tracking-tighter">
                How can we help?
            </h1>
            <p class="text-lg text-slate-400">
                Search {{ totalArticles }} articles across {{ categories.length }} categories.
            </p>

            <!-- Search -->
            <div class="relative max-w-xl mx-auto">
                <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 pointer-events-none" />
                <input
                    v-model="query"
                    type="search"
                    placeholder="Search help articles…"
                    class="w-full pl-12 pr-4 py-4 bg-white/10 border border-white/20 rounded-2xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-ocean-400 focus:border-transparent text-base"
                    aria-label="Search help articles"
                />
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="bg-white dark:bg-slate-900 py-16 px-4 sm:px-6 lg:px-8 min-h-[60vh]">
        <div class="max-w-6xl mx-auto">
            <!-- Empty state -->
            <div v-if="filteredCategories.length === 0" class="text-center py-24 space-y-3">
                <BookOpen class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto" />
                <p class="text-slate-500 dark:text-slate-400">No articles match "{{ query }}"</p>
                <button type="button" class="text-ocean-500 text-sm hover:underline" @click="query = ''">Clear search</button>
            </div>

            <div v-else class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="category in filteredCategories"
                    :key="category.id"
                    class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl p-6 hover:shadow-md dark:hover:shadow-slate-900/50 hover:-translate-y-0.5 transition-all"
                >
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-1">{{ category.name }}</h2>
                    <p v-if="category.description" class="text-sm text-slate-500 dark:text-slate-400 mb-4 leading-relaxed">
                        {{ category.description }}
                    </p>

                    <ul class="space-y-2">
                        <li v-for="article in category.articles.slice(0, 5)" :key="article.id">
                            <Link
                                :href="`/help/${article.slug}`"
                                class="flex items-center gap-2 text-sm text-ocean-600 dark:text-ocean-400 hover:text-ocean-700 dark:hover:text-ocean-300 group"
                            >
                                <ChevronRight class="w-3.5 h-3.5 shrink-0 opacity-60 group-hover:translate-x-0.5 transition-transform" />
                                {{ article.title }}
                            </Link>
                        </li>
                        <li v-if="category.articles.length > 5">
                            <span class="text-xs text-slate-400 pl-5">+{{ category.articles.length - 5 }} more articles</span>
                        </li>
                    </ul>

                    <div v-if="category.articles.length === 0" class="text-sm text-slate-400 italic">
                        No articles yet
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="bg-slate-50 dark:bg-slate-800 py-16 px-4 sm:px-6 lg:px-8 border-t border-slate-100 dark:border-slate-700">
        <div class="max-w-2xl mx-auto text-center space-y-4">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Still need help?</h2>
            <p class="text-slate-500 dark:text-slate-400">Our support team is here to help you get the most out of FlowFlex.</p>
            <Link
                href="/contact"
                class="inline-flex items-center gap-2 bg-ocean-500 hover:bg-ocean-400 text-white px-6 py-3 rounded-xl font-semibold transition-all hover:-translate-y-0.5"
            >
                Contact support
            </Link>
        </div>
    </section>
</template>
