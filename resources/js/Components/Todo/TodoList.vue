<template>
    <div>
        <TodoForm @create="handleCreate" />

        <div v-if="isLoading" class="text-center py-8">
            <p class="text-gray-500">Đang tải...</p>
        </div>

        <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700">
            {{ error }}
        </div>

        <div v-else>
            <TodoEmptyState v-if="todos.length === 0" />

            <ul v-else class="space-y-3">
                <TodoItem
                    v-for="todo in todos"
                    :key="todo.id"
                    :todo="todo"
                />
            </ul>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { todoApi } from '../../services/todoApi.js';
import TodoForm from './TodoForm.vue';
import TodoEmptyState from './TodoEmptyState.vue';
import TodoItem from './TodoItem.vue';

const todos = ref([]);
const isLoading = ref(false);
const error = ref('');

const fetchTodos = async () => {
    isLoading.value = true;
    error.value = '';

    try {
        todos.value = await todoApi.getAll();
    } catch (err) {
        error.value = 'Không thể tải danh sách todos. Vui lòng thử lại.';
        console.error('Failed to fetch todos:', err);
    } finally {
        isLoading.value = false;
    }
};

const handleCreate = async (title) => {
    try {
        const newTodo = await todoApi.create(title);
        todos.value.unshift(newTodo); // Add to beginning (newest first)
    } catch (err) {
        error.value = err.response?.data?.message || 'Không thể tạo todo. Vui lòng thử lại.';
        throw err; // Re-throw để TodoForm handle
    }
};

onMounted(() => {
    fetchTodos();
});
</script>
