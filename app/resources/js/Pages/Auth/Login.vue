<script setup lang="ts">
import CheckboxInput from '@/Components/Form/CheckboxInput.vue'
import FormField from '@/Components/Form/FormField.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import AuthLayout from '@/Components/Layout/AuthLayout.vue'
import BaseButton from '@/Components/UI/BaseButton.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

defineOptions({ layout: AuthLayout })

const form = useForm({ email: '', password: '', remember: false })

function submit() {
    form.post('/login', { onFinish: () => form.reset('password') })
}
</script>

<template>
    <Head title="Sign in" />
    <h1 class="font-display text-[26px] font-bold tracking-[-0.02em]">Sign in to FlowFlex</h1>
    <p class="mt-1.5 text-[14.5px] text-ink-soft">Welcome back.</p>

    <form class="mt-7 space-y-5" @submit.prevent="submit">
        <FormField label="Work email" for="email" :error="form.errors.email">
            <TextInput id="email" v-model="form.email" type="email" required autofocus
                autocomplete="email" placeholder="you@company.com" :invalid="!!form.errors.email" />
        </FormField>
        <FormField label="Password" for="password">
            <TextInput id="password" v-model="form.password" type="password" required
                autocomplete="current-password" placeholder="••••••••••••" />
            <!-- Below the input so tabbing goes email → password directly (UX rule). -->
            <div class="mt-1.5 text-right">
                <Link href="/forgot-password" class="text-[12.5px] font-semibold text-accent hover:underline">
                    Forgot it?
                </Link>
            </div>
        </FormField>
        <CheckboxInput v-model="form.remember" label="Keep me signed in" />
        <BaseButton type="submit" size="lg" class="w-full" :loading="form.processing">
            Sign in
        </BaseButton>
    </form>

    <p class="mt-7 text-center text-[13px] leading-[1.6] text-ink-faint">
        New here? FlowFlex workspaces are invite-only —<br>
        <Link href="/contact" class="font-semibold text-accent hover:underline">talk to us</Link> to get set up.
    </p>
</template>
