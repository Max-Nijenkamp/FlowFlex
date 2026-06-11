<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue'

const props = withDefaults(defineProps<{ delay?: number }>(), { delay: 0 })

const el = ref<HTMLElement | null>(null)
let observer: IntersectionObserver | null = null

onMounted(() => {
    if (!el.value) return
    observer = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting && el.value) {
                el.value.style.transitionDelay = `${props.delay}ms`
                el.value.classList.add('is-visible')
                observer?.disconnect()
            }
        },
        { threshold: 0.15 },
    )
    observer.observe(el.value)
})

onUnmounted(() => observer?.disconnect())
</script>

<template>
    <div ref="el" class="reveal"><slot /></div>
</template>
