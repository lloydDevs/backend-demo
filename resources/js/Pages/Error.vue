<script setup>
const props = defineProps({
    status: Number,
});

const messages = {
    403: {
        title: "You don't have access to this",
        description: 'Your account doesn\'t have permission to view this page. If you think this is a mistake, ask an admin to check your role.',
    },
    404: {
        title: 'Page not found',
        description: "The page you're looking for doesn't exist or may have been moved.",
    },
    419: {
        title: 'Your session expired',
        description: 'Please refresh the page and try again.',
    },
    429: {
        title: 'Too many requests',
        description: 'Slow down a little and try again in a moment.',
    },
    500: {
        title: 'Something went wrong',
        description: "An unexpected error occurred on our end. It's not you — try again shortly.",
    },
    503: {
        title: "We'll be right back",
        description: 'The app is temporarily down for maintenance.',
    },
};

const content = messages[props.status] ?? messages[500];
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-md text-center">
            <p class="text-6xl font-semibold text-gray-300 mb-4">{{ status }}</p>
            <h1 class="text-xl font-semibold text-gray-900 mb-2">{{ content.title }}</h1>
            <p class="text-sm text-gray-500 mb-6">{{ content.description }}</p>
            
             <a href="/dashboard"
                class="inline-block bg-indigo-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-indigo-500"
            >
                Back to dashboard
            </a>
        </div>
    </div>
</template>