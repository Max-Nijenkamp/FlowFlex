<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { Clock, Calendar, ArrowLeft, ArrowRight } from 'lucide-vue-next'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps<{
    post: {
        id: string
        title: string
        body: string
        excerpt: string
        featured_image: string | null
        published_at: string
        reading_time: number
        category: { name: string; slug: string }
        seo_title: string | null
        seo_description: string | null
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
    <Head :title="post.seo_title ?? `${post.title} — FlowFlex Blog`">
        <meta name="description" :content="post.seo_description ?? post.excerpt" />
    </Head>

    <article class="bg-white dark:bg-slate-900">

        <!-- Back link -->
        <div class="max-w-3xl mx-auto px-4 sm:px-6 pt-10">
            <Link href="/blog" class="inline-flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors">
                <ArrowLeft class="w-4 h-4" /> {{ t('blog.backToBlog') }}
            </Link>
        </div>

        <!-- Header -->
        <header class="max-w-3xl mx-auto px-4 sm:px-6 pt-8 pb-10 space-y-5">
            <span class="inline-block text-xs font-semibold text-ocean-600 dark:text-ocean-400 bg-ocean-50 dark:bg-ocean-900/30 px-2.5 py-1 rounded-full">
                {{ post.category.name }}
            </span>
            <h1 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white leading-tight">
                {{ post.title }}
            </h1>
            <div class="flex items-center gap-5 text-sm text-slate-500 dark:text-slate-400">
                <span class="flex items-center gap-1.5">
                    <Calendar class="w-4 h-4" />
                    {{ formatDate(post.published_at) }}
                </span>
                <span class="flex items-center gap-1.5">
                    <Clock class="w-4 h-4" />
                    {{ post.reading_time }} {{ t('blog.minRead') }}
                </span>
            </div>
        </header>

        <!-- Featured image -->
        <div v-if="post.featured_image" class="max-w-4xl mx-auto px-4 sm:px-6 mb-12">
            <div class="aspect-video rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800">
                <img :src="post.featured_image" :alt="post.title" class="w-full h-full object-cover" />
            </div>
        </div>
        <div v-else class="max-w-4xl mx-auto px-4 sm:px-6 mb-12">
            <div class="aspect-video rounded-2xl overflow-hidden bg-gradient-to-br from-ocean-100 to-ocean-50 dark:from-ocean-900/40 dark:to-ocean-800/20" />
        </div>

        <!-- Body -->
        <div
            class="max-w-3xl mx-auto px-4 sm:px-6 text-lg leading-relaxed text-slate-700 dark:text-slate-300 space-y-6
                   [&_h2]:text-2xl [&_h2]:font-bold [&_h2]:text-slate-900 dark:[&_h2]:text-white [&_h2]:mt-10 [&_h2]:mb-4
                   [&_h3]:text-xl [&_h3]:font-bold [&_h3]:text-slate-900 dark:[&_h3]:text-white [&_h3]:mt-8 [&_h3]:mb-3
                   [&_p]:leading-relaxed
                   [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:space-y-2
                   [&_ol]:list-decimal [&_ol]:pl-6 [&_ol]:space-y-2
                   [&_a]:text-ocean-600 dark:[&_a]:text-ocean-400 [&_a]:underline [&_a]:underline-offset-2 [&_a]:hover:text-ocean-700
                   [&_blockquote]:border-l-4 [&_blockquote]:border-ocean-300 [&_blockquote]:pl-4 [&_blockquote]:italic [&_blockquote]:text-slate-500
                   [&_code]:bg-slate-100 dark:[&_code]:bg-slate-800 [&_code]:px-1.5 [&_code]:py-0.5 [&_code]:rounded [&_code]:text-sm [&_code]:font-mono
                   [&_pre]:bg-slate-900 [&_pre]:text-slate-100 [&_pre]:p-4 [&_pre]:rounded-xl [&_pre]:overflow-x-auto [&_pre]:text-sm"
            v-html="post.body"
        />

        <!-- CTA block -->
        <div class="max-w-3xl mx-auto px-4 sm:px-6 mt-16 mb-16">
            <div class="bg-ocean-50 dark:bg-ocean-900/20 border border-ocean-100 dark:border-ocean-800/30 rounded-2xl p-8 flex flex-col sm:flex-row items-center justify-between gap-6">
                <div>
                    <p class="font-bold text-slate-900 dark:text-white text-lg">{{ t('blog.wantToSee') }}</p>
                    <p class="text-slate-600 dark:text-slate-400 text-sm mt-1">{{ t('blog.wantToSeeDesc') }}</p>
                </div>
                <Link
                    href="/demo"
                    class="shrink-0 inline-flex items-center gap-2 bg-ocean-500 hover:bg-ocean-400 text-white px-6 py-3 rounded-xl font-semibold transition-colors"
                >
                    {{ t('blog.requestDemo') }} <ArrowRight class="w-4 h-4" />
                </Link>
            </div>
        </div>

        <!-- Back to blog -->
        <div class="max-w-3xl mx-auto px-4 sm:px-6 pb-16 border-t border-slate-100 dark:border-slate-800 pt-8">
            <Link href="/blog" class="inline-flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors">
                <ArrowLeft class="w-4 h-4" /> {{ t('blog.allArticles') }}
            </Link>
        </div>
    </article>
</template>
