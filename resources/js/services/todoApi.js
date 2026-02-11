import axios from 'axios';

const API_BASE = '/api/v1';

/**
 * Todo API service
 */
export const todoApi = {
    /**
     * Get all todos
     * @returns {Promise<Array>}
     */
    async getAll() {
        const response = await axios.get(`${API_BASE}/todos`);
        return response.data.data;
    },

    /**
     * Create a new todo
     * @param {string} title - Todo title (1-255 chars)
     * @returns {Promise<Object>}
     */
    async create(title) {
        const response = await axios.post(`${API_BASE}/todos`, { title });
        return response.data.data;
    },
};
