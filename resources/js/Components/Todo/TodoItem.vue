<template>
    <li class="flex items-center gap-3 p-4 bg-white border border-gray-200 rounded-lg hover:shadow-md transition-shadow">
        <input
            type="checkbox"
            :checked="todo.completed"
            @change="$emit('toggle', todo.id)"
            class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer"
        />
        <div class="flex-1">
            <p
                class="text-sm md:text-base transition-colors"
                :class="todo.completed ? 'line-through text-gray-400' : 'text-gray-900'"
            >
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

defineEmits(['toggle']);
</script>
