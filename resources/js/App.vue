<template>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-2xl mx-auto px-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Todo List</h1>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600">Vue App đang chạy! ✅</p>
                <p class="text-sm text-gray-500 mt-2">{{ message }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const message = ref('Infrastructure ready. Building features...');
</script>
