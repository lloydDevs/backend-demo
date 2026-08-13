<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({ email: '' });

function submit() {
    form.post('/forgot-password');
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-sm bg-white p-8 rounded-lg border border-gray-200">
            <h1 class="text-lg font-semibold text-gray-900 mb-2">Forgot your password?</h1>
            <p class="text-sm text-gray-500 mb-6">
                We'll email you a link to reset it.
            </p>

            <div v-if="form.recentlySuccessful" class="text-sm text-green-700 mb-4">
                A reset link has been sent to your email.
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Email</label>
                    <input v-model="form.email" type="email" class="w-full rounded-md border-gray-300 text-sm" autofocus />
                    <p v-if="form.errors.email" class="text-xs text-red-600 mt-1">{{ form.errors.email }}</p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-indigo-600 text-white text-sm font-medium py-2 rounded-md hover:bg-indigo-500 disabled:opacity-50"
                >
                    Email password reset link
                </button>
            </form>
        </div>
    </div>
</template>
