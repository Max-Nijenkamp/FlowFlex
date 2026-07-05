<script setup lang="ts">
import { computed } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import MarketingLayout from '../../Components/Layout/MarketingLayout.vue'

defineOptions({ layout: MarketingLayout })

const page = usePage<{ flash: { success?: string } }>()
const success = computed(() => page.props.flash?.success)

const form = useForm({
    name: '',
    email: '',
    company_size: '',
    message: '',
    website: '', // honeypot — humans never see or fill this
})

const submit = (): void => {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    })
}
</script>

<template>
    <Head>
        <title>Contact</title>
        <meta name="description" content="Questions about modules, pricing or moving off your current stack — we reply within one business day." />
    </Head>

    <section class="ff-section ff-grid-bg" style="border-bottom: none; padding: 84px 0 104px">
        <div class="wrap">
            <div class="grid items-start gap-11 lg:gap-[72px] lg:[grid-template-columns:1fr_1.15fr]">
                <div>
                    <span class="ff-kicker"><span class="sq"></span>Contact</span>
                    <h1 class="mt-6" style="font-size: clamp(38px, 5vw, 54px); line-height: 1.03; letter-spacing: -0.03em">Talk to us.</h1>
                    <p class="ff-lede" style="max-width: 380px">
                        Questions about modules, pricing or moving off your current stack — we reply within one
                        business day.
                    </p>
                    <div class="mt-10 flex flex-col gap-3.5">
                        <div class="relative rounded-[14px] border bg-(--card) px-6 py-5.5" style="border-color: var(--line-strong)">
                            <span class="absolute -top-px -left-px h-3.5 w-3.5" style="border-top: 2px solid var(--indigo); border-left: 2px solid var(--indigo)"></span>
                            <h3 class="text-[15px]">Considering a switch?</h3>
                            <p class="mt-1.5 text-[14px] leading-relaxed" style="color: var(--ink-soft)">
                                Tell us which tools you run today. We'll map them to modules and give you a real
                                monthly number — usually the same day.
                            </p>
                        </div>
                        <div class="rounded-[14px] border bg-(--card) px-6 py-5.5" style="border-color: var(--line-strong)">
                            <h3 class="text-[15px]">Already a customer?</h3>
                            <p class="mt-1.5 text-[14px] leading-relaxed" style="color: var(--ink-soft)">
                                <a href="/app/login" class="font-semibold" style="color: var(--indigo)">Sign in</a>
                                and reach support from your workspace — it's faster.
                            </p>
                        </div>
                    </div>
                </div>

                <form
                    class="flex flex-col gap-5.5 rounded-[20px] border bg-(--card) p-7 md:p-10"
                    style="border-color: var(--line-strong); box-shadow: 0 1px 2px rgba(17,24,39,0.04), 0 28px 56px -32px rgba(17,24,39,0.18)"
                    novalidate
                    @submit.prevent="submit"
                >
                    <div v-if="success" class="rounded-xl px-4.5 py-3.5 text-[14.5px] font-semibold" style="background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0" role="status">
                        {{ success }}
                    </div>

                    <div class="grid gap-4.5 md:grid-cols-2">
                        <label class="ff-field">
                            <span class="lbl"><span>Name</span></span>
                            <input v-model="form.name" type="text" name="name" autocomplete="name" class="ff-input" :class="{ error: form.errors.name }" placeholder="Your name" required />
                            <span v-if="form.errors.name" class="ff-err">{{ form.errors.name }}</span>
                        </label>
                        <label class="ff-field">
                            <span class="lbl"><span>Work email</span></span>
                            <input v-model="form.email" type="email" name="email" autocomplete="email" class="ff-input" :class="{ error: form.errors.email }" placeholder="you@company.com" required />
                            <span v-if="form.errors.email" class="ff-err">{{ form.errors.email }}</span>
                        </label>
                    </div>

                    <label class="ff-field">
                        <span class="lbl"><span>Company size</span><span class="opt">optional</span></span>
                        <select v-model="form.company_size" name="company_size" class="ff-input" :class="{ error: form.errors.company_size }">
                            <option value="">How big is your team?</option>
                            <option value="1-25">1–25 people</option>
                            <option value="26-50">26–50 people</option>
                            <option value="51-150">51–150 people</option>
                            <option value="151-500">151–500 people</option>
                            <option value="500+">500+ people</option>
                        </select>
                    </label>

                    <label class="ff-field">
                        <span class="lbl"><span>What's on your mind</span></span>
                        <textarea v-model="form.message" name="message" class="ff-input area" :class="{ error: form.errors.message }" placeholder="We currently run separate tools for HR and invoicing, and…" required></textarea>
                        <span v-if="form.errors.message" class="ff-err">{{ form.errors.message }}</span>
                        <span class="ff-hint">The tools you use today, what's slowing you down — anything helps.</span>
                    </label>

                    <!-- Honeypot: hidden from humans, bots fill it -->
                    <input v-model="form.website" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" class="hidden" />

                    <button type="submit" class="ff-btn primary lg w-full" :disabled="form.processing">
                        {{ form.processing ? 'Sending…' : 'Send message' }}
                    </button>
                    <p class="text-center text-[12.5px]" style="color: var(--ink-faint)">
                        No newsletter, no drip campaign — just a reply.
                    </p>
                </form>
            </div>
        </div>
    </section>
</template>
