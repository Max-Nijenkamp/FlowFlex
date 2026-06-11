<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import { useForm } from '@inertiajs/vue3'

defineOptions({ layout: MarketingLayout })

const form = useForm({ email: '' })

function submit() {
    form.post('/forgot-password')
}
</script>

<template>
    <section class="mx-auto max-w-md px-6 py-20">
        <h1 class="text-3xl font-bold">Reset your password</h1>
        <p class="mt-2 text-slate-600">We'll email you a reset link.</p>
        <form class="mt-8 space-y-5" @submit.prevent="submit">
            <div>
                <label class="block text-sm font-medium">Email</label>
                <input v-model="form.email" type="email" required autofocus
                    class="mt-1 w-full rounded-lg border-slate-300 focus:border-sky-400 focus:ring-sky-400" />
                <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
            </div>
            <button type="submit" :disabled="form.processing"
                class="w-full rounded-xl bg-sky-500 px-6 py-3 font-semibold text-white hover:bg-sky-600 transition ease-out duration-150 disabled:opacity-60">
                {{ form.recentlySuccessful ? 'Link sent ✓' : 'Send reset link' }}
            </button>
        </form>
    </section>
</template>
