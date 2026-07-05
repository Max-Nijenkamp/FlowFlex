<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

withDefaults(
    defineProps<{
        title: string
        sub?: string
        cta?: string
        href?: string
    }>(),
    { sub: '', cta: 'Build your price', href: '/pricing' },
)
</script>

<template>
    <section class="ff-band">
        <div class="wrap">
            <h2>{{ title }}</h2>
            <p v-if="sub">{{ sub }}</p>
            <Link :href="href" class="ff-btn white lg">{{ cta }}</Link>
        </div>
    </section>
</template>
