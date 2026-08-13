<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProductForm from '@/Components/ProductForm.vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    product: Object,
});

const form = useForm({
    name: props.product.name,
    sku: props.product.sku,
    description: props.product.description,
    price: props.product.price,
    stock: props.product.stock,
    is_active: props.product.is_active,
});

function submit() {
    form.put(`/products/${props.product.id}`);
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="max-w-xl">
            <h1 class="text-xl font-semibold text-gray-900 mb-6">Edit Product</h1>

            <form @submit.prevent="submit" class="bg-white rounded-lg border border-gray-200 p-6 space-y-6">
                <ProductForm :form="form" />

                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-indigo-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-indigo-500 disabled:opacity-50"
                    >
                        Save Changes
                    </button>
                    <Link :href="`/products/${product.id}`" class="text-sm text-gray-500 hover:text-gray-800">
                        Cancel
                    </Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
