<template>
    <form @submit.prevent="handleSubmit" class="mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <input
                v-model="title"
                type="text"
                placeholder="Thêm todo mới..."
                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm md:text-base"
                :class="{ 'border-red-500': error }"
                maxlength="255"
            />
            <button
                type="submit"
                :disabled="!title.trim() || isSubmitting"
                class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors text-sm md:text-base"
            >
                {{ isSubmitting ? 'Đang thêm...' : 'Thêm' }}
            </button>
        </div>
        <p v-if="error" class="mt-2 text-sm text-red-600">
            {{ error }}
        </p>
    </form>
</template>

<script setup>
import { ref } from 'vue';

const emit = defineEmits(['create']);

const title = ref('');
const error = ref('');
const isSubmitting = ref(false);

const handleSubmit = async () => {
    if (!title.value.trim()) {
        error.value = 'Vui lòng nhập tiêu đề.';
        return;
    }

    if (title.value.length > 255) {
        error.value = 'Tiêu đề không được vượt quá 255 ký tự.';
        return;
    }

    error.value = '';
    isSubmitting.value = true;

    try {
        await emit('create', title.value.trim());
        title.value = '';
    } catch (err) {
        error.value = err.response?.data?.message || 'Đã xảy ra lỗi. Vui lòng thử lại.';
    } finally {
        isSubmitting.value = false;
    }
};
</script>
