<script setup lang="ts">
import CheckboxInput from '@/Components/Form/CheckboxInput.vue'
import FormField from '@/Components/Form/FormField.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import AuthLayout from '@/Components/Layout/AuthLayout.vue'
import BaseButton from '@/Components/UI/BaseButton.vue'
import { Link, useForm } from '@inertiajs/vue3'

defineOptions({ layout: AuthLayout })

const form = useForm({ email: '', password: '', remember: false })

function submit() {
    form.post('/login', { onFinish: () => form.reset('password') })
}
</script>

<template>
    <h1 class="text-2xl font-bold tracking-display">Sign in to FlowFlex</h1>
    <p class="mt-1.5 text-sm text-ink-soft">Welcome back.</p>

    <form class="mt-9 space-y-5" @submit.prevent="submit">
        <FormField label="Work email" for="email" :error="form.errors.email">
            <TextInput id="email" v-model="form.email" type="email" required autofocus
                autocomplete="email" placeholder="you@company.com" :invalid="!!form.errors.email" />
        </FormField>
        <FormField label="Password" for="password">
            <template #corner>
                <Link href="/forgot-password" class="text-[13px] font-medium text-accent hover:underline">Forgot it?</Link>
            </template>
            <TextInput id="password" v-model="form.password" type="password" required
                autocomplete="current-password" placeholder="••••••••••••" />
        </FormField>
        <CheckboxInput v-model="form.remember" label="Keep me signed in" />
        <BaseButton type="submit" size="lg" class="w-full" :loading="form.processing">
            Sign in
        </BaseButton>
    </form>

    <p class="mt-8 text-center text-sm text-ink-faint">
        New here? FlowFlex workspaces are invite-only —
        <Link href="/contact" class="text-accent hover:underline">talk to us</Link> to get set up.
    </p>
</template>
