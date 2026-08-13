<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProductForm from '@/Components/ProductForm.vue';
import { useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    sku: '',
    description: '',
    price: '',
    stock: 0,
    is_active: true,
});

function submit() {
    form.post('/products');
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="max-w-xl">
            <h1 class="text-xl font-semibold text-gray-900 mb-6">New Product</h1>

            <form @submit.prevent="submit" class="bg-white rounded-lg border border-gray-200 p-6 space-y-6">
                <ProductForm :form="form" />

                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-indigo-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-indigo-500 disabled:opacity-50"
                    >
                        Create Product
                    </button>
                    <Link href="/products" class="text-sm text-gray-500 hover:text-gray-800">
                        Cancel
                    </Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
