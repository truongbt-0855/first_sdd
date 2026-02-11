import './bootstrap';
import { createApp } from 'vue';
import TodosPage from './Pages/TodosPage.vue';

const app = createApp(TodosPage);

app.mount('#app');

