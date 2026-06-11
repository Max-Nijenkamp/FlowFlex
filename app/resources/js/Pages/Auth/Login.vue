<script setup lang="ts">
import AuthLayout from '@/Components/Layout/AuthLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

defineOptions({ layout: AuthLayout })

const form = useForm({ email: '', password: '', remember: false })

function submit() {
    form.post('/login', { onFinish: () => form.reset('password') })
}
</script>

<template>
    <h1 class="text-3xl font-bold tracking-display">Sign in</h1>
    <p class="mt-2 text-sm text-ink-soft">Welcome back. Your workspace is waiting.</p>

    <form class="mt-9 space-y-5" @submit.prevent="submit">
        <div>
            <label for="email" class="block text-sm font-medium">Work email</label>
            <input id="email" v-model="form.email" type="email" required autofocus autocomplete="email"
                class="mt-1.5 w-full rounded-lg border-line bg-white focus:border-accent focus:ring-accent" />
            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
        </div>
        <div>
            <div class="flex items-baseline justify-between">
                <label for="password" class="block text-sm font-medium">Password</label>
                <Link href="/forgot-password" class="text-sm text-accent hover:underline">Forgot it?</Link>
            </div>
            <input id="password" v-model="form.password" type="password" required autocomplete="current-password"
                class="mt-1.5 w-full rounded-lg border-line bg-white focus:border-accent focus:ring-accent" />
        </div>
        <label class="flex items-center gap-2.5 text-sm text-ink-soft">
            <input v-model="form.remember" type="checkbox" class="rounded border-line text-accent focus:ring-accent" />
            Keep me signed in
        </label>
        <button type="submit" :disabled="form.processing"
            class="w-full rounded-full bg-ink px-6 py-3.5 font-semibold text-white hover:bg-accent transition ease-out duration-150 disabled:opacity-60">
            Sign in
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-ink-faint">
        New here? FlowFlex workspaces are invite-only —
        <Link href="/contact" class="text-accent hover:underline">talk to us</Link> to get set up.
    </p>
</template>
