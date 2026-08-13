<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    products: Object,
});

const page = usePage();
const permissions = computed(() => page.props.auth.permissions ?? []);
const can = (name) => permissions.value.includes(name);

function destroy(id) {
    if (confirm('Delete this product?')) {
        router.delete(`/products/${id}`);
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl font-semibold text-gray-900">Products</h1>
            <Link
                v-if="can('products.create')"
                href="/products/create"
                class="bg-indigo-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-indigo-500"
            >
                New Product
            </Link>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-500">
                    <tr>
                        <th class="px-4 py-3 font-medium">Name</th>
                        <th class="px-4 py-3 font-medium">SKU</th>
                        <th class="px-4 py-3 font-medium">Price</th>
                        <th class="px-4 py-3 font-medium">Stock</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="product in products.data" :key="product.id">
                        <td class="px-4 py-3">
                            <Link :href="`/products/${product.id}`" class="text-indigo-600 hover:underline">
                                {{ product.name }}
                            </Link>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ product.sku }}</td>
                        <td class="px-4 py-3">${{ Number(product.price).toFixed(2) }}</td>
                        <td class="px-4 py-3">{{ product.stock }}</td>
                        <td class="px-4 py-3">
                            <span
                                class="inline-block px-2 py-0.5 rounded-full text-xs"
                                :class="product.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                            >
                                {{ product.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <Link
                                v-if="can('products.edit')"
                                :href="`/products/${product.id}/edit`"
                                class="text-gray-500 hover:text-gray-800"
                            >
                                Edit
                            </Link>
                            <button
                                v-if="can('products.delete')"
                                @click="destroy(product.id)"
                                class="text-red-500 hover:text-red-700"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                    <tr v-if="products.data.length === 0">
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                            No products yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex justify-center gap-1 mt-6">
            <Link
                v-for="link in products.links"
                :key="link.label"
                :href="link.url ?? '#'"
                v-html="link.label"
                class="px-3 py-1 text-sm rounded-md border"
                :class="[
                    link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'text-gray-600 border-gray-200',
                    !link.url ? 'opacity-40 pointer-events-none' : 'hover:bg-gray-50',
                ]"
            />
        </div>
    </AuthenticatedLayout>
</template>
