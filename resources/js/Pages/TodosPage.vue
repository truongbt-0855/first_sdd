<template>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900">
                    Todo List
                </h1>
                <p class="mt-2 text-sm md:text-base text-gray-600">
                    Quản lý công việc của bạn một cách dễ dàng
                </p>
            </header>

            <main class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
                <TodoList />
            </main>
        </div>
    </div>
</template>

<script setup>
import TodoList from '../Components/Todo/TodoList.vue';
</script>
