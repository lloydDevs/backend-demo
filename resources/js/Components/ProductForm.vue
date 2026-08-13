<script setup>
defineProps({
    form: Object,
});
</script>

<template>
    <div class="space-y-4">
        <div>
            <label class="block text-sm text-gray-700 mb-1">Name</label>
            <input v-model="form.name" type="text" class="w-full rounded-md border-gray-300 text-sm" />
            <p v-if="form.errors.name" class="text-xs text-red-600 mt-1">{{ form.errors.name }}</p>
        </div>

        <div>
            <label class="block text-sm text-gray-700 mb-1">SKU</label>
            <input v-model="form.sku" type="text" class="w-full rounded-md border-gray-300 text-sm" />
            <p v-if="form.errors.sku" class="text-xs text-red-600 mt-1">{{ form.errors.sku }}</p>
        </div>

        <div>
            <label class="block text-sm text-gray-700 mb-1">Description</label>
            <textarea v-model="form.description" rows="3" class="w-full rounded-md border-gray-300 text-sm"></textarea>
            <p v-if="form.errors.description" class="text-xs text-red-600 mt-1">{{ form.errors.description }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-700 mb-1">Price</label>
                <input v-model="form.price" type="number" step="0.01" min="0" class="w-full rounded-md border-gray-300 text-sm" />
                <p v-if="form.errors.price" class="text-xs text-red-600 mt-1">{{ form.errors.price }}</p>
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">Stock</label>
                <input v-model="form.stock" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm" />
                <p v-if="form.errors.stock" class="text-xs text-red-600 mt-1">{{ form.errors.stock }}</p>
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input v-model="form.is_active" type="checkbox" />
            Active
        </label>
    </div>
</template>
