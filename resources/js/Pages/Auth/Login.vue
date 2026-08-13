<script setup>
import { useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-sm bg-white p-8 rounded-lg border border-gray-200">
            <h1 class="text-lg font-semibold text-gray-900 mb-6">Log in</h1>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        class="w-full rounded-md border-gray-300 text-sm"
                        autofocus
                    />
                    <p v-if="form.errors.email" class="text-xs text-red-600 mt-1">
                        {{ form.errors.email }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-1">Password</label>
                    <input
                        v-model="form.password"
                        type="password"
                        class="w-full rounded-md border-gray-300 text-sm"
                    />
                    <p v-if="form.errors.password" class="text-xs text-red-600 mt-1">
                        {{ form.errors.password }}
                    </p>
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input v-model="form.remember" type="checkbox" />
                    Remember me
                </label>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-indigo-600 text-white text-sm font-medium py-2 rounded-md hover:bg-indigo-500 disabled:opacity-50"
                >
                    Log in
                </button>
            </form>

            <p class="text-sm text-gray-500 mt-4">
                No account?
                <Link href="/register" class="text-indigo-600 hover:underline">Register</Link>
            </p>

            <p class="text-xs text-gray-400 mt-6">
                Demo login: demo@example.com / password
            </p>
        </div>
    </div>
</template>
