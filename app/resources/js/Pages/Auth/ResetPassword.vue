<script setup lang="ts">
import FormField from '@/Components/Form/FormField.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import AuthLayout from '@/Components/Layout/AuthLayout.vue'
import BaseButton from '@/Components/UI/BaseButton.vue'
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
    <h1 class="text-center text-2xl font-bold tracking-display">Choose a new password</h1>

    <form class="mt-9 space-y-5" @submit.prevent="submit">
        <FormField label="New password" for="password" :error="form.errors.password" hint="At least 12 characters.">
            <TextInput id="password" v-model="form.password" type="password" required minlength="12" autofocus
                autocomplete="new-password" :invalid="!!form.errors.password" />
        </FormField>
        <FormField label="Confirm password" for="password-confirm">
            <TextInput id="password-confirm" v-model="form.password_confirmation" type="password" required
                autocomplete="new-password" />
        </FormField>
        <BaseButton type="submit" size="lg" class="w-full" :loading="form.processing">
            Reset password
        </BaseButton>
    </form>
</template>
