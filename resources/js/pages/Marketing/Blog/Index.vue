<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { Search, Clock, Calendar, PenLine } from 'lucide-vue-next'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

defineProps<{
    posts?: {
        data: Array<{
            id: string
            title: string
            slug: string
            excerpt: string
            featured_image: string | null
            published_at: string
            reading_time: number
            category: { name: string; slug: string }
        }>
        links: unknown
    }
}>()

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    })
}
</script>

<template>
    <Head title="Blog — FlowFlex">
        <meta name="description" content="Insights on building better businesses, product updates, and thoughts from the FlowFlex team." />
    </Head>

    <!-- Hero -->
    <section class="relative bg-[#050E1A] py-24 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-ocean-950 to-slate-950 pointer-events-none" />
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[500px] h-[250px] bg-ocean-600/8 blur-[80px] rounded-full pointer-events-none" />
        <div class="relative max-w-4xl mx-auto text-center space-y-6">            <h1 class="text-4xl sm:text-6xl font-black text-white tracking-tighter">{{ t('blog.heading') }}</h1>
            <p class="text-xl text-slate-400 max-w-2xl mx-auto">
                {{ t('blog.subheading') }}
            </p>
            <!-- Search (UI only) -->
            <div class="relative max-w-md mx-auto mt-6">
                <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" />
                <input
                    type="text"
                    :placeholder="t('blog.searchPlaceholder')"
                    class="w-full pl-10 pr-4 py-3 rounded-xl border border-white/10 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-ocean-500 focus:border-transparent bg-white/5 backdrop-blur-sm"
                />
            </div>
        </div>
    </section>

    <!-- Posts grid -->
    <section class="bg-white dark:bg-slate-900 py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">

            <!-- Empty state -->
            <div v-if="!posts || posts.data.length === 0" class="text-center py-24">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4">
                    <PenLine class="w-8 h-8 text-slate-400" />
                </div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">{{ t('blog.noPostsTitle') }}</h2>
                <p class="text-slate-500 dark:text-slate-400">{{ t('blog.noPostsDesc') }}</p>
            </div>

            <!-- Posts -->
            <div v-else>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <article
                        v-for="post in posts.data"
                        :key="post.id"
                        class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden hover:shadow-lg dark:hover:shadow-slate-900/50 hover:-translate-y-0.5 transition-all group"
                    >
                        <!-- Featured image -->
                        <div class="aspect-video bg-slate-100 dark:bg-slate-700 relative overflow-hidden">
                            <img
                                v-if="post.featured_image"
                                :src="post.featured_image"
                                :alt="post.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            />
                            <div v-else class="w-full h-full bg-gradient-to-br from-ocean-200 to-ocean-100 dark:from-ocean-800/40 dark:to-ocean-900/40" />
                        </div>

                        <div class="p-6 space-y-3">
                            <!-- Category chip -->
                            <span class="inline-block text-xs font-semibold text-ocean-600 dark:text-ocean-400 bg-ocean-50 dark:bg-ocean-900/30 px-2.5 py-1 rounded-full">
                                {{ post.category.name }}
                            </span>

                            <!-- Title -->
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white leading-snug group-hover:text-ocean-600 dark:group-hover:text-ocean-400 transition-colors">
                                <Link :href="`/blog/${post.slug}`">{{ post.title }}</Link>
                            </h2>

                            <!-- Excerpt -->
                            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed line-clamp-3">
                                {{ post.excerpt }}
                            </p>

                            <!-- Meta -->
                            <div class="flex items-center gap-4 text-xs text-slate-400 pt-1">
                                <span class="flex items-center gap-1">
                                    <Calendar class="w-3.5 h-3.5" />
                                    {{ formatDate(post.published_at) }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <Clock class="w-3.5 h-3.5" />
                                    {{ post.reading_time }} {{ t('blog.minRead') }}
                                </span>
                            </div>

                            <Link :href="`/blog/${post.slug}`" class="inline-flex items-center gap-1 text-sm font-medium text-ocean-600 dark:text-ocean-400 hover:text-ocean-700 dark:hover:text-ocean-300 transition-colors">
                                {{ t('blog.readMore') }} →
                            </Link>
                        </div>
                    </article>
                </div>

                <!-- Pagination -->
                <div v-if="posts.links" class="mt-12 flex justify-center">
                    <!-- Pagination handled by backend links -->
                </div>
            </div>
        </div>
    </section>
</template>
