<script setup lang="ts">
import FormField from '@/Components/Form/FormField.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import AuthLayout from '@/Components/Layout/AuthLayout.vue'
import BaseButton from '@/Components/UI/BaseButton.vue'
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
            <FormField label="First name" for="first-name" :error="form.errors.first_name">
                <TextInput id="first-name" v-model="form.first_name" type="text" required autofocus
                    autocomplete="given-name" :invalid="!!form.errors.first_name" />
            </FormField>
            <FormField label="Last name" for="last-name">
                <TextInput id="last-name" v-model="form.last_name" type="text" required autocomplete="family-name" />
            </FormField>
        </div>
        <FormField label="Password" for="password" :error="form.errors.password" hint="At least 12 characters.">
            <TextInput id="password" v-model="form.password" type="password" required minlength="12"
                autocomplete="new-password" :invalid="!!form.errors.password" />
        </FormField>
        <FormField label="Confirm password" for="password-confirm">
            <TextInput id="password-confirm" v-model="form.password_confirmation" type="password" required
                autocomplete="new-password" />
        </FormField>
        <BaseButton type="submit" size="lg" class="w-full" :loading="form.processing">
            Create account
        </BaseButton>
    </form>
</template>
