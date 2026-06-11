<script setup lang="ts">
import MarketingLayout from '@/Components/Layout/MarketingLayout.vue'
import { useForm } from '@inertiajs/vue3'

defineOptions({ layout: MarketingLayout })

const form = useForm({ name: '', email: '', message: '', website: '' })

function submit() {
    form.post('/contact', { preserveScroll: true, onSuccess: () => form.reset() })
}
</script>

<template>
    <section class="mx-auto max-w-6xl px-6 py-20">
        <div class="grid gap-16 lg:grid-cols-[1fr_1.2fr]">
            <div>
                <p class="section-index">CONTACT</p>
                <h1 class="mt-4 text-4xl sm:text-5xl font-bold tracking-display text-balance">Talk to us.</h1>
                <p class="mt-5 max-w-sm text-ink-soft leading-relaxed">
                    Questions about modules, pricing or moving off your current stack —
                    we reply within one business day.
                </p>
                <dl class="mt-10 space-y-6 text-sm">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-[0.18em] text-ink-faint">Considering a switch?</dt>
                        <dd class="mt-1.5 text-ink-soft leading-relaxed">
                            Tell us which tools you run today. We'll map them to modules and give you a real number.
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-[0.18em] text-ink-faint">Already a customer?</dt>
                        <dd class="mt-1.5 text-ink-soft leading-relaxed">
                            Sign in and reach support from your workspace — it's faster.
                        </dd>
                    </div>
                </dl>
            </div>

            <form class="rounded-2xl border border-line bg-white p-8 space-y-5" @submit.prevent="submit">
                <div>
                    <label for="contact-name" class="block text-sm font-medium">Name</label>
                    <input id="contact-name" v-model="form.name" type="text" required
                        class="mt-1.5 w-full rounded-lg border-line bg-paper focus:border-accent focus:ring-accent" />
                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                </div>
                <div>
                    <label for="contact-email" class="block text-sm font-medium">Work email</label>
                    <input id="contact-email" v-model="form.email" type="email" required
                        class="mt-1.5 w-full rounded-lg border-line bg-paper focus:border-accent focus:ring-accent" />
                    <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                </div>
                <div>
                    <label for="contact-message" class="block text-sm font-medium">What's on your mind</label>
                    <textarea id="contact-message" v-model="form.message" rows="6" required
                        class="mt-1.5 w-full rounded-lg border-line bg-paper focus:border-accent focus:ring-accent"></textarea>
                    <p v-if="form.errors.message" class="mt-1 text-sm text-red-600">{{ form.errors.message }}</p>
                </div>
                <input v-model="form.website" type="text" name="website" class="hidden" tabindex="-1" autocomplete="off" />
                <button type="submit" :disabled="form.processing"
                    class="w-full rounded-full bg-ink px-6 py-3.5 font-semibold text-white hover:bg-accent transition ease-out duration-150 disabled:opacity-60">
                    {{ form.recentlySuccessful ? 'Sent. We\'ll be in touch.' : 'Send message' }}
                </button>
            </form>
        </div>
    </section>
</template>
