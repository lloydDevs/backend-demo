<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
</script>

<template>
    <AuthenticatedLayout>
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h1 class="text-xl font-semibold text-gray-900">Dashboard</h1>
            <p class="mt-2 text-sm text-gray-600">
                You're logged in. Head to
                <a href="/products" class="text-indigo-600 hover:underline">Products</a>
                to see the sample CRUD module in action.
            </p>
        </div>
    </AuthenticatedLayout>
</template>
