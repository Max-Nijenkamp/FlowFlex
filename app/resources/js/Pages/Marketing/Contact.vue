<script setup lang="ts">
import FormField from '@/Components/Form/FormField.vue'
import SelectInput from '@/Components/Form/SelectInput.vue'
import TextArea from '@/Components/Form/TextArea.vue'
import TextInput from '@/Components/Form/TextInput.vue'
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import Kicker from '@/Components/Marketing/Kicker.vue'
import BaseButton from '@/Components/UI/BaseButton.vue'

import { Head, useForm } from '@inertiajs/vue3'

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
    <Head title="Contact">
        <meta name="description"
            content="Questions about modules, pricing or switching from your current stack — we reply within one business day." />
    </Head>
    <section class="bg-bloom">
        <div class="mx-auto max-w-6xl px-6 pt-14 pb-16 md:pt-[84px] md:pb-[104px]">
            <div class="grid items-start gap-12 lg:grid-cols-[1fr_1.15fr] lg:gap-[72px]">
                <div>
                    <Kicker>Contact</Kicker>
                    <h1 class="mt-6 font-display text-[40px] font-bold leading-[1.03] tracking-[-0.03em] md:text-[54px]">
                        Talk to us.
                    </h1>
                    <p class="mt-[22px] max-w-[380px] text-[16.5px] leading-[1.65] text-ink-soft">
                        Questions about modules, pricing or moving off your current stack — we reply within one
                        business day.
                    </p>

                    <div class="mt-10 flex flex-col gap-3.5">
                        <div class="relative rounded-[14px] border border-line-strong bg-card px-6 py-[22px]">
                            <span class="absolute -left-px -top-px h-3.5 w-3.5 border-l-2 border-t-2 border-accent" />
                            <h2 class="font-display text-[15px] font-bold">Considering a switch?</h2>
                            <p class="mt-1.5 text-sm leading-relaxed text-ink-soft">
                                Tell us which tools you run today. We'll map them to modules and give you a real
                                monthly number — usually the same day.
                            </p>
                        </div>
                        <div class="rounded-[14px] border border-line-strong bg-card px-6 py-[22px]">
                            <h2 class="font-display text-[15px] font-bold">Already a customer?</h2>
                            <p class="mt-1.5 text-sm leading-relaxed text-ink-soft">
                                Sign in and reach support from your workspace — it's faster.
                            </p>
                        </div>
                    </div>
                </div>

                <form class="flex h-fit flex-col gap-[22px] rounded-[20px] border border-line-strong bg-card p-7 shadow-[0_1px_2px_rgba(17,24,39,0.04),0_28px_56px_-32px_rgba(17,24,39,0.18)] sm:p-10"
                    @submit.prevent="submit">
                    <div class="grid gap-[18px] sm:grid-cols-2">
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
                    <p class="text-center text-[12.5px] text-ink-faint">No newsletter, no drip campaign — just a reply.</p>
                </form>
            </div>
        </div>
    </section>
</template>
