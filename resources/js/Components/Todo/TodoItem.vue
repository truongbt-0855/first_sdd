<template>
    <li class="flex items-center gap-3 p-4 bg-white border border-gray-200 rounded-lg hover:shadow-md transition-shadow">
        <div class="flex-1">
            <p class="text-gray-900 text-sm md:text-base">
                {{ todo.title }}
            </p>
        </div>
    </li>
</template>

<script setup>
defineProps({
    todo: {
        type: Object,
        required: true,
    },
});
</script>
