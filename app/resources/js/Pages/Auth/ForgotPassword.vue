<script setup lang="ts">
import FormField from '@/Components/Form/FormField.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import AuthLayout from '@/Components/Layout/AuthLayout.vue'
import BaseButton from '@/Components/UI/BaseButton.vue'
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
        <FormField label="Work email" for="email" :error="form.errors.email">
            <TextInput id="email" v-model="form.email" type="email" required autofocus
                autocomplete="email" placeholder="you@company.com" :invalid="!!form.errors.email" />
        </FormField>
        <BaseButton type="submit" size="lg" class="w-full" :loading="form.processing">
            {{ form.recentlySuccessful ? 'Link sent — check your inbox' : 'Send reset link' }}
        </BaseButton>
    </form>

    <p class="mt-8 text-center text-sm">
        <Link href="/login" class="text-accent hover:underline">Back to sign in</Link>
    </p>
</template>
