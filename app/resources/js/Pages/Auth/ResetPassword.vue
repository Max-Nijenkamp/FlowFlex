<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import { useForm } from '@inertiajs/vue3'

defineOptions({ layout: MarketingLayout })

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
    <section class="mx-auto max-w-md px-6 py-20">
        <h1 class="text-3xl font-bold">Choose a new password</h1>
        <form class="mt-8 space-y-5" @submit.prevent="submit">
            <div>
                <label class="block text-sm font-medium">New password</label>
                <input v-model="form.password" type="password" required minlength="12" autofocus
                    class="mt-1 w-full rounded-lg border-slate-300 focus:border-sky-400 focus:ring-sky-400" />
                <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium">Confirm password</label>
                <input v-model="form.password_confirmation" type="password" required
                    class="mt-1 w-full rounded-lg border-slate-300 focus:border-sky-400 focus:ring-sky-400" />
            </div>
            <button type="submit" :disabled="form.processing"
                class="w-full rounded-xl bg-sky-500 px-6 py-3 font-semibold text-white hover:bg-sky-600 transition ease-out duration-150 disabled:opacity-60">
                Reset password
            </button>
        </form>
    </section>
</template>
