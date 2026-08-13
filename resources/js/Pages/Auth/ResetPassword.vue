<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post('/reset-password');
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-sm bg-white p-8 rounded-lg border border-gray-200">
            <h1 class="text-lg font-semibold text-gray-900 mb-6">Reset password</h1>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Email</label>
                    <input v-model="form.email" type="email" class="w-full rounded-md border-gray-300 text-sm" />
                    <p v-if="form.errors.email" class="text-xs text-red-600 mt-1">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-1">New password</label>
                    <input v-model="form.password" type="password" class="w-full rounded-md border-gray-300 text-sm" autofocus />
                    <p v-if="form.errors.password" class="text-xs text-red-600 mt-1">{{ form.errors.password }}</p>
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-1">Confirm password</label>
                    <input v-model="form.password_confirmation" type="password" class="w-full rounded-md border-gray-300 text-sm" />
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-indigo-600 text-white text-sm font-medium py-2 rounded-md hover:bg-indigo-500 disabled:opacity-50"
                >
                    Reset password
                </button>
            </form>
        </div>
    </div>
</template>
