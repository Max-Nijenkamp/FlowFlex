<script setup lang="ts">
import AuthLayout from '@/Components/Layout/AuthLayout.vue'
import { useForm } from '@inertiajs/vue3'

defineOptions({ layout: AuthLayout })

const props = defineProps<{ token: string; email: string }>()

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
})

function submit() {
    form.post('/reset-password')
}
</script>

<template>
    <h1 class="text-3xl font-bold tracking-display">Choose a new password</h1>

    <form class="mt-9 space-y-5" @submit.prevent="submit">
        <div>
            <label for="password" class="block text-sm font-medium">New password</label>
            <input id="password" v-model="form.password" type="password" required minlength="12" autofocus autocomplete="new-password"
                class="mt-1.5 w-full rounded-lg border-line bg-white focus:border-accent focus:ring-accent" />
            <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
            <p class="mt-1 text-xs text-ink-faint">At least 12 characters.</p>
        </div>
        <div>
            <label for="password-confirm" class="block text-sm font-medium">Confirm password</label>
            <input id="password-confirm" v-model="form.password_confirmation" type="password" required autocomplete="new-password"
                class="mt-1.5 w-full rounded-lg border-line bg-white focus:border-accent focus:ring-accent" />
        </div>
        <button type="submit" :disabled="form.processing"
            class="w-full rounded-full bg-ink px-6 py-3.5 font-semibold text-white hover:bg-accent transition ease-out duration-150 disabled:opacity-60">
            Reset password
        </button>
    </form>
</template>
