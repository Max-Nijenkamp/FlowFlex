<script setup lang="ts">
import FormField from '@/Components/Form/FormField.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import AuthLayout from '@/Components/Layout/AuthLayout.vue'
import Kicker from '@/Components/Marketing/Kicker.vue'
import BaseButton from '@/Components/UI/BaseButton.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

defineOptions({ layout: AuthLayout })

const props = defineProps<{ token: string; email: string; company: string }>()

const form = useForm({ first_name: '', last_name: '', password: '', password_confirmation: '' })

// 4-segment strength meter — length + character classes, purely indicative.
const strength = computed(() => {
    const pw = form.password
    if (!pw) return 0
    let score = 0
    if (pw.length >= 12) score++
    if (/[a-z]/.test(pw) && /[A-Z]/.test(pw)) score++
    if (/\d/.test(pw)) score++
    if (/[^a-zA-Z0-9]/.test(pw)) score++
    return score
})

const strengthLabel = computed(() => ['', 'weak', 'okay', 'good', 'strong'][strength.value])

function submit() {
    form.post(`/register/invite/${props.token}`)
}
</script>

<template>
    <Head :title="`Join ${company}`" />
    <Kicker>You're invited</Kicker>
    <h1 class="mt-3.5 font-display text-2xl font-bold tracking-[-0.02em]">Join {{ company }}</h1>
    <p class="mt-1.5 text-[14.5px] leading-[1.6] text-ink-soft">
        You've been invited to the {{ company }} workspace. Set a name and password and you're in.
    </p>

    <form class="mt-6 space-y-5" @submit.prevent="submit">
        <FormField label="Work email" for="invite-email">
            <div class="flex h-[46px] w-full items-center justify-between rounded-[10px] border border-line-strong bg-paper-deep px-3.5 text-[15px] text-ink-soft">
                {{ email }}
                <svg class="h-[13px] w-[13px] shrink-0" viewBox="0 0 16 16" fill="none" stroke="#98A0AB" stroke-width="1.6">
                    <rect x="3" y="7" width="10" height="7" rx="1.5" />
                    <path d="M5.5 7V5.5a2.5 2.5 0 015 0V7" />
                </svg>
            </div>
        </FormField>
        <div class="grid grid-cols-2 gap-4">
            <FormField label="First name" for="first-name" :error="form.errors.first_name">
                <TextInput id="first-name" v-model="form.first_name" type="text" required autofocus
                    autocomplete="given-name" :invalid="!!form.errors.first_name" />
            </FormField>
            <FormField label="Last name" for="last-name">
                <TextInput id="last-name" v-model="form.last_name" type="text" required autocomplete="family-name" />
            </FormField>
        </div>
        <FormField label="Choose a password" for="password" :error="form.errors.password" hint="At least 12 characters.">
            <TextInput id="password" v-model="form.password" type="password" required minlength="12"
                autocomplete="new-password" :invalid="!!form.errors.password" />
            <div v-if="form.password" class="mt-2.5 flex items-center gap-[5px]">
                <span v-for="i in 4" :key="i" class="h-1 flex-1 rounded-sm"
                    :class="i <= strength ? 'bg-[#10B981]' : 'bg-line'" />
                <span class="ml-1.5 text-[11.5px] font-semibold"
                    :class="strength >= 3 ? 'text-[#10B981]' : 'text-ink-faint'">{{ strengthLabel }}</span>
            </div>
        </FormField>
        <FormField label="Confirm password" for="password-confirm">
            <TextInput id="password-confirm" v-model="form.password_confirmation" type="password" required
                autocomplete="new-password" />
        </FormField>
        <BaseButton type="submit" size="lg" class="w-full" :loading="form.processing">
            Create account &amp; join
        </BaseButton>
    </form>

    <p class="mt-5 text-center text-[12.5px] text-ink-faint">
        By joining you accept the workspace's terms and our privacy policy.
    </p>
</template>
