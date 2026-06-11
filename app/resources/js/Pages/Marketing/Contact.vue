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
    <section class="mx-auto max-w-xl px-6 py-20">
        <h1 class="text-4xl font-bold">Talk to us</h1>
        <p class="mt-3 text-slate-600">Questions about FlowFlex? We reply within one business day.</p>

        <form class="mt-8 space-y-5" @submit.prevent="submit">
            <div>
                <label class="block text-sm font-medium">Name</label>
                <input v-model="form.name" type="text" required
                    class="mt-1 w-full rounded-lg border-slate-300 focus:border-sky-400 focus:ring-sky-400" />
                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium">Email</label>
                <input v-model="form.email" type="email" required
                    class="mt-1 w-full rounded-lg border-slate-300 focus:border-sky-400 focus:ring-sky-400" />
                <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium">Message</label>
                <textarea v-model="form.message" rows="5" required
                    class="mt-1 w-full rounded-lg border-slate-300 focus:border-sky-400 focus:ring-sky-400"></textarea>
                <p v-if="form.errors.message" class="mt-1 text-sm text-red-600">{{ form.errors.message }}</p>
            </div>
            <!-- Honeypot -->
            <input v-model="form.website" type="text" name="website" class="hidden" tabindex="-1" autocomplete="off" />
            <button type="submit" :disabled="form.processing"
                class="rounded-xl bg-sky-500 px-6 py-3 font-semibold text-white hover:bg-sky-600 transition ease-out duration-150 disabled:opacity-60">
                {{ form.recentlySuccessful ? 'Sent ✓' : 'Send message' }}
            </button>
        </form>
    </section>
</template>
