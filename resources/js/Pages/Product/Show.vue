<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    product: Object,
});

const page = usePage();
const permissions = computed(() => page.props.auth.permissions ?? []);
const can = (name) => permissions.value.includes(name);
</script>

<template>
    <AuthenticatedLayout>
        <div class="max-w-xl">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-xl font-semibold text-gray-900">{{ product.name }}</h1>
                <Link
                    v-if="can('products.edit')"
                    :href="`/products/${product.id}/edit`"
                    class="text-sm text-indigo-600 hover:underline"
                >
                    Edit
                </Link>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 divide-y divide-gray-100">
                <div class="px-6 py-4 flex justify-between">
                    <span class="text-sm text-gray-500">SKU</span>
                    <span class="text-sm text-gray-900">{{ product.sku }}</span>
                </div>
                <div class="px-6 py-4 flex justify-between">
                    <span class="text-sm text-gray-500">Price</span>
                    <span class="text-sm text-gray-900">${{ Number(product.price).toFixed(2) }}</span>
                </div>
                <div class="px-6 py-4 flex justify-between">
                    <span class="text-sm text-gray-500">Stock</span>
                    <span class="text-sm text-gray-900">{{ product.stock }}</span>
                </div>
                <div class="px-6 py-4 flex justify-between">
                    <span class="text-sm text-gray-500">Status</span>
                    <span class="text-sm text-gray-900">{{ product.is_active ? 'Active' : 'Inactive' }}</span>
                </div>
                <div class="px-6 py-4">
                    <span class="text-sm text-gray-500 block mb-1">Description</span>
                    <p class="text-sm text-gray-900">{{ product.description || '—' }}</p>
                </div>
            </div>

            <Link href="/products" class="inline-block mt-6 text-sm text-gray-500 hover:text-gray-800">
                &larr; Back to products
            </Link>
        </div>
    </AuthenticatedLayout>
</template>
