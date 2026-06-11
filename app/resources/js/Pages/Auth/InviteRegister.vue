<script setup lang="ts">
import AuthLayout from '@/Components/Layout/AuthLayout.vue'
import { useForm } from '@inertiajs/vue3'

defineOptions({ layout: AuthLayout })

const props = defineProps<{ token: string; email: string; company: string }>()

const form = useForm({ first_name: '', last_name: '', password: '', password_confirmation: '' })

function submit() {
    form.post(`/register/invite/${props.token}`)
}
</script>

<template>
    <p class="section-index">INVITATION</p>
    <h1 class="mt-3 text-3xl font-bold tracking-display">Join {{ company }}</h1>
    <p class="mt-2 text-sm text-ink-soft">
        Creating your account for <span class="font-medium text-ink">{{ email }}</span>.
    </p>

    <form class="mt-9 space-y-5" @submit.prevent="submit">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="first-name" class="block text-sm font-medium">First name</label>
                <input id="first-name" v-model="form.first_name" type="text" required autofocus autocomplete="given-name"
                    class="mt-1.5 w-full rounded-lg border-line bg-white focus:border-accent focus:ring-accent" />
                <p v-if="form.errors.first_name" class="mt-1 text-sm text-red-600">{{ form.errors.first_name }}</p>
            </div>
            <div>
                <label for="last-name" class="block text-sm font-medium">Last name</label>
                <input id="last-name" v-model="form.last_name" type="text" required autocomplete="family-name"
                    class="mt-1.5 w-full rounded-lg border-line bg-white focus:border-accent focus:ring-accent" />
            </div>
        </div>
        <div>
            <label for="password" class="block text-sm font-medium">Password</label>
            <input id="password" v-model="form.password" type="password" required minlength="12" autocomplete="new-password"
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
            Create account
        </button>
    </form>
</template>
