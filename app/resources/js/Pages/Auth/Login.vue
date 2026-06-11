<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

defineOptions({ layout: MarketingLayout })

const form = useForm({ email: '', password: '', remember: false })

function submit() {
    form.post('/login', { onFinish: () => form.reset('password') })
}
</script>

<template>
    <section class="mx-auto max-w-md px-6 py-20">
        <h1 class="text-3xl font-bold">Sign in</h1>
        <form class="mt-8 space-y-5" @submit.prevent="submit">
            <div>
                <label class="block text-sm font-medium">Email</label>
                <input v-model="form.email" type="email" required autofocus
                    class="mt-1 w-full rounded-lg border-slate-300 focus:border-sky-400 focus:ring-sky-400" />
                <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium">Password</label>
                <input v-model="form.password" type="password" required
                    class="mt-1 w-full rounded-lg border-slate-300 focus:border-sky-400 focus:ring-sky-400" />
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input v-model="form.remember" type="checkbox" class="rounded border-slate-300 text-sky-500" />
                Keep me signed in
            </label>
            <button type="submit" :disabled="form.processing"
                class="w-full rounded-xl bg-sky-500 px-6 py-3 font-semibold text-white hover:bg-sky-600 transition ease-out duration-150 disabled:opacity-60">
                Sign in
            </button>
            <p class="text-center text-sm">
                <Link href="/forgot-password" class="text-sky-600 hover:underline">Forgot your password?</Link>
            </p>
        </form>
    </section>
</template>
