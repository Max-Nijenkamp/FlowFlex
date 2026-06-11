<script setup lang="ts">
import AuthLayout from '@/Components/Layout/AuthLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

defineOptions({ layout: AuthLayout })

const form = useForm({ email: '' })

function submit() {
    form.post('/forgot-password')
}
</script>

<template>
    <h1 class="text-3xl font-bold tracking-display">Reset your password</h1>
    <p class="mt-2 text-sm text-ink-soft">We'll email you a link to choose a new one.</p>

    <form class="mt-9 space-y-5" @submit.prevent="submit">
        <div>
            <label for="email" class="block text-sm font-medium">Work email</label>
            <input id="email" v-model="form.email" type="email" required autofocus autocomplete="email"
                class="mt-1.5 w-full rounded-lg border-line bg-white focus:border-accent focus:ring-accent" />
            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
        </div>
        <button type="submit" :disabled="form.processing"
            class="w-full rounded-full bg-ink px-6 py-3.5 font-semibold text-white hover:bg-accent transition ease-out duration-150 disabled:opacity-60">
            {{ form.recentlySuccessful ? 'Link sent — check your inbox' : 'Send reset link' }}
        </button>
    </form>

    <p class="mt-8 text-center text-sm">
        <Link href="/login" class="text-accent hover:underline">Back to sign in</Link>
    </p>
</template>
