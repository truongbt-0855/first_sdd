<template>
    <li class="bg-white border border-gray-200 rounded-lg hover:shadow-md transition-shadow">
        <!-- View Mode -->
        <div v-if="!isEditing" class="flex items-center gap-3 p-4">
            <input
                type="checkbox"
                :checked="todo.completed"
                @change="$emit('toggle', todo.id)"
                class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer"
            />
            <div class="flex-1">
                <p
                    class="text-sm md:text-base transition-colors cursor-pointer"
                    :class="todo.completed ? 'line-through text-gray-400' : 'text-gray-900'"
                    @dblclick="startEdit"
                >
                    {{ todo.title }}
                </p>
            </div>
            <button
                @click="startEdit"
                class="text-gray-400 hover:text-blue-600 transition-colors p-1"
                title="Chỉnh sửa"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </button>
        </div>

        <!-- Edit Mode -->
        <div v-else class="flex items-center gap-2 p-4">
            <input
                v-model="editTitle"
                type="text"
                class="flex-1 px-3 py-2 border border-blue-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                maxlength="255"
                @keydown="handleKeydown"
                ref="editInput"
            />
            <button
                @click="saveEdit"
                class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-1"
                title="Lưu"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </button>
            <button
                @click="cancelEdit"
                class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors flex items-center gap-1"
                title="Hủy"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </li>
</template>

<script setup>
import { ref, nextTick } from 'vue';

const props = defineProps({
    todo: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['toggle', 'update']);

const isEditing = ref(false);
const editTitle = ref('');
const editInput = ref(null);

/**
 * Enter edit mode
 */
const startEdit = async () => {
    editTitle.value = props.todo.title;
    isEditing.value = true;
    
    // Focus input after DOM update
    await nextTick();
    editInput.value?.focus();
};

/**
 * Save edited title
 */
const saveEdit = () => {
    const trimmed = editTitle.value.trim();
    if (trimmed && trimmed !== props.todo.title) {
        emit('update', props.todo.id, trimmed);
    }
    isEditing.value = false;
};

/**
 * Cancel editing
 */
const cancelEdit = () => {
    isEditing.value = false;
    editTitle.value = '';
};

/**
 * Handle keyboard shortcuts
 */
const handleKeydown = (event) => {
    if (event.key === 'Enter') {
        saveEdit();
    } else if (event.key === 'Escape') {
        cancelEdit();
    }
};
</script>
