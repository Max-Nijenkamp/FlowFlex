<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import { useForm } from '@inertiajs/vue3'

defineOptions({ layout: MarketingLayout })

const props = defineProps<{ token: string; email: string; company: string }>()

const form = useForm({ first_name: '', last_name: '', password: '', password_confirmation: '' })

function submit() {
    form.post(`/register/invite/${props.token}`)
}
</script>

<template>
    <section class="mx-auto max-w-md px-6 py-20">
        <h1 class="text-3xl font-bold">Join {{ company }}</h1>
        <p class="mt-2 text-slate-600">Create your account for <strong>{{ email }}</strong>.</p>

        <form class="mt-8 space-y-5" @submit.prevent="submit">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">First name</label>
                    <input v-model="form.first_name" type="text" required autofocus
                        class="mt-1 w-full rounded-lg border-slate-300 focus:border-sky-400 focus:ring-sky-400" />
                    <p v-if="form.errors.first_name" class="mt-1 text-sm text-red-600">{{ form.errors.first_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium">Last name</label>
                    <input v-model="form.last_name" type="text" required
                        class="mt-1 w-full rounded-lg border-slate-300 focus:border-sky-400 focus:ring-sky-400" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium">Password</label>
                <input v-model="form.password" type="password" required minlength="12"
                    class="mt-1 w-full rounded-lg border-slate-300 focus:border-sky-400 focus:ring-sky-400" />
                <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                <p class="mt-1 text-xs text-slate-500">At least 12 characters.</p>
            </div>
            <div>
                <label class="block text-sm font-medium">Confirm password</label>
                <input v-model="form.password_confirmation" type="password" required
                    class="mt-1 w-full rounded-lg border-slate-300 focus:border-sky-400 focus:ring-sky-400" />
            </div>
            <button type="submit" :disabled="form.processing"
                class="w-full rounded-xl bg-sky-500 px-6 py-3 font-semibold text-white hover:bg-sky-600 transition ease-out duration-150 disabled:opacity-60">
                Create account
            </button>
        </form>
    </section>
</template>
