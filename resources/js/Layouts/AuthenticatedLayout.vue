<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

function logout() {
    router.post('/logout');
}
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-6xl mx-auto px-4 flex items-center justify-between h-14">
                <div class="flex items-center gap-6">
                    <Link href="/dashboard" class="font-semibold text-gray-900">
                        Laravel Modular
                    </Link>
                    <Link
                        href="/products"
                        class="text-sm text-gray-600 hover:text-gray-900"
                    >
                        Products
                    </Link>
                </div>
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-gray-500">{{ user?.name }}</span>
                    <button
                        @click="logout"
                        class="text-gray-600 hover:text-gray-900"
                    >
                        Log out
                    </button>
                </div>
            </div>
        </nav>

        <div
            v-if="flashSuccess"
            class="max-w-6xl mx-auto mt-4 px-4"
        >
            <div class="rounded-md bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3">
                {{ flashSuccess }}
            </div>
        </div>
        <div
            v-if="flashError"
            class="max-w-6xl mx-auto mt-4 px-4"
        >
            <div class="rounded-md bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3">
                {{ flashError }}
            </div>
        </div>

        <main class="max-w-6xl mx-auto px-4 py-8">
            <slot />
        </main>
    </div>
</template>
