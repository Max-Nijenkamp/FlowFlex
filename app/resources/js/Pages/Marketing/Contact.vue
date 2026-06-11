<script setup lang="ts">
import FormField from '@/Components/Form/FormField.vue'
import SelectInput from '@/Components/Form/SelectInput.vue'
import TextArea from '@/Components/Form/TextArea.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import BaseButton from '@/Components/UI/BaseButton.vue'

import { useForm } from '@inertiajs/vue3'

defineOptions({ layout: MarketingLayout })

const form = useForm({ name: '', email: '', company_size: '', message: '', website: '' })

const sizes = [
    { value: '10-50', label: '10–50 people' },
    { value: '50-150', label: '50–150 people' },
    { value: '150-500', label: '150–500 people' },
    { value: '500+', label: 'More than 500' },
]

function submit() {
    form.post('/contact', { preserveScroll: true, onSuccess: () => form.reset() })
}
</script>

<template>
    <section class="mx-auto max-w-6xl px-6 py-20">
        <div class="grid gap-16 lg:grid-cols-[1fr_1.15fr]">
            <div>
                <p class="section-index">CONTACT</p>
                <h1 class="mt-4 text-4xl sm:text-5xl font-bold tracking-display text-balance">Talk to us.</h1>
                <p class="mt-5 max-w-sm text-ink-soft leading-relaxed">
                    Questions about modules, pricing or moving off your current stack —
                    we reply within one business day.
                </p>

                <div class="mt-10 space-y-4">
                    <div class="rounded-2xl border border-line bg-white p-6">
                        <h2 class="text-sm font-semibold">Considering a switch?</h2>
                        <p class="mt-1.5 text-sm text-ink-soft leading-relaxed">
                            Tell us which tools you run today. We'll map them to modules and
                            give you a real monthly number — usually the same day.
                        </p>
                    </div>
                    <div class="rounded-2xl border border-line bg-white p-6">
                        <h2 class="text-sm font-semibold">Already a customer?</h2>
                        <p class="mt-1.5 text-sm text-ink-soft leading-relaxed">
                            Sign in and reach support from your workspace — it's faster.
                        </p>
                    </div>
                </div>
            </div>

            <form class="h-fit rounded-3xl border border-line bg-white p-8 sm:p-10 shadow-[0_2px_12px_rgba(17,24,39,0.04)] space-y-6"
                @submit.prevent="submit">
                <div class="grid gap-6 sm:grid-cols-2">
                    <FormField label="Name" for="contact-name" :error="form.errors.name">
                        <TextInput id="contact-name" v-model="form.name" type="text" required
                            autocomplete="name" placeholder="Your name" :invalid="!!form.errors.name" />
                    </FormField>
                    <FormField label="Work email" for="contact-email" :error="form.errors.email">
                        <TextInput id="contact-email" v-model="form.email" type="email" required
                            autocomplete="email" placeholder="you@company.com" :invalid="!!form.errors.email" />
                    </FormField>
                </div>
                <FormField label="Company size" for="contact-size" optional>
                    <SelectInput id="contact-size" v-model="form.company_size" :options="sizes"
                        placeholder="How big is your team?" />
                </FormField>
                <FormField label="What's on your mind" for="contact-message" :error="form.errors.message"
                    hint="The tools you use today, what's slowing you down — anything helps.">
                    <TextArea id="contact-message" v-model="form.message" :rows="6" required
                        placeholder="We currently run separate tools for HR and invoicing, and…"
                        :invalid="!!form.errors.message" />
                </FormField>
                <input v-model="form.website" type="text" name="website" class="hidden" tabindex="-1" autocomplete="off" />
                <BaseButton type="submit" size="lg" class="w-full" :loading="form.processing">
                    {{ form.recentlySuccessful ? "Sent. We'll be in touch." : 'Send message' }}
                </BaseButton>
                <p class="text-center text-xs text-ink-faint">No newsletter, no drip campaign — just a reply.</p>
            </form>
        </div>
    </section>
</template>
