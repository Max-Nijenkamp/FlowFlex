<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue'

const el = ref<HTMLElement | null>(null)
let observer: IntersectionObserver | null = null

onMounted(() => {
    if (!el.value) return
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        el.value.classList.add('in')
        return
    }
    observer = new IntersectionObserver(
        (entries) => {
            for (const entry of entries) {
                if (entry.isIntersecting) {
                    (entry.target as HTMLElement).classList.add('in')
                    observer?.unobserve(entry.target)
                }
            }
        },
        { threshold: 0.12 },
    )
    observer.observe(el.value)
})

onBeforeUnmount(() => observer?.disconnect())
</script>

<template>
    <div ref="el" class="ff-reveal">
        <slot />
    </div>
</template>
