<script setup lang="ts">
import FormField from '@/Components/Form/FormField.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import AuthLayout from '@/Components/Layout/AuthLayout.vue'
import BaseButton from '@/Components/UI/BaseButton.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { h } from 'vue'

defineOptions({
    layout: (_h: typeof h, page: ReturnType<typeof h>) => h(AuthLayout, { split: false }, () => page),
})

const form = useForm({ email: '' })

function submit() {
    form.post('/forgot-password')
}
</script>

<template>
    <Head title="Reset your password" />
    <h1 class="font-display text-2xl font-bold tracking-[-0.02em]">Reset your password</h1>
    <p class="mt-2 text-[14.5px] leading-[1.6] text-ink-soft">
        Type the work email you sign in with. If it has a FlowFlex account, a reset link is on its way.
    </p>

    <form class="mt-6 space-y-5" @submit.prevent="submit">
        <FormField label="Work email" for="email" :error="form.errors.email">
            <TextInput id="email" v-model="form.email" type="email" required autofocus
                autocomplete="email" placeholder="you@company.com" :invalid="!!form.errors.email" />
        </FormField>
        <BaseButton type="submit" size="lg" class="w-full" :loading="form.processing">
            {{ form.recentlySuccessful ? 'Link sent — check your inbox' : 'Send reset link' }}
        </BaseButton>
    </form>

    <p class="mt-6 text-center text-[13px] text-ink-faint">
        Remembered it?
        <Link href="/login" class="font-semibold text-accent hover:underline">Back to sign in</Link>
    </p>
</template>
