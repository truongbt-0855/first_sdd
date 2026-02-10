# Quickstart: Hệ thống Todo List Cơ bản

**Feature**: 001-todo-system-vn  
**Branch**: `001-todo-system-vn`  
**Date**: 2026-02-10

## Tổng quan

Hướng dẫn nhanh để implement hệ thống todo list cơ bản với Laravel 11 backend và Vue 3 frontend. Tài liệu này giúp developer bắt đầu nhanh chóng với các bước thiết yếu.

## Prerequisites

- PHP 8.3+
- Composer
- Node.js 18+ & npm/pnpm
- PostgreSQL 15+
- Git

## 30-Second Overview

```bash
# 1. Create branch
git checkout -b 001-todo-system-vn

# 2. Backend: Migration + Model + Controller + Service + Tests
php artisan make:migration create_todos_table
php artisan make:model Todo
php artisan make:controller Api/V1/TodoController --api
# ... implement code ...

# 3. Frontend: Components + API Service
# Create Vue components in resources/js/Components/Todo/
# Create API service in resources/js/services/todoApi.js

# 4. Run & Test
php artisan test
npm run dev
```

---

## Phase 1: Setup Database

### 1.1 Create Migration

```bash
php artisan make:migration create_todos_table
```

**File**: `database/migrations/2026_02_10_create_todos_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->boolean('completed')->default(false);
            $table->timestamps();
            
            $table->index('completed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
```

### 1.2 Run Migration

```bash
php artisan migrate
```

---

## Phase 2: Backend Implementation

### 2.1 Create Model

**File**: `app/Models/Todo.php`

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'completed'];

    protected $casts = [
        'completed' => 'boolean',
    ];

    public function toggleCompletion(): void
    {
        $this->completed = !$this->completed;
        $this->save();
    }
}
```

### 2.2 Create Service

**File**: `app/Services/TodoService.php`

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Todo;
use Illuminate\Database\Eloquent\Collection;

class TodoService
{
    public function getAll(): Collection
    {
        return Todo::orderBy('created_at', 'desc')->get();
    }

    public function create(string $title): Todo
    {
        return Todo::create([
            'title' => $title,
            'completed' => false,
        ]);
    }

    public function update(Todo $todo, string $title): Todo
    {
        $todo->update(['title' => $title]);
        return $todo->fresh();
    }

    public function toggle(Todo $todo): Todo
    {
        $todo->toggleCompletion();
        return $todo->fresh();
    }

    public function delete(Todo $todo): void
    {
        $todo->delete();
    }
}
```

### 2.3 Create Form Requests

**File**: `app/Http/Requests/StoreTodoRequest.php`

```php
<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề là bắt buộc',
            'title.max' => 'Tiêu đề phải có tối đa 255 ký tự',
        ];
    }
}
```

**File**: `app/Http/Requests/UpdateTodoRequest.php` (same as above)

### 2.4 Create Controller

**File**: `app/Http/Controllers/Api/V1/TodoController.php`

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Models\Todo;
use App\Services\TodoService;
use Illuminate\Http\JsonResponse;

class TodoController extends Controller
{
    public function __construct(
        private TodoService $todoService
    ) {}

    public function index(): JsonResponse
    {
        $todos = $this->todoService->getAll();
        
        return response()->json([
            'success' => true,
            'data' => $todos,
            'message' => 'Todos retrieved successfully',
        ]);
    }

    public function store(StoreTodoRequest $request): JsonResponse
    {
        $todo = $this->todoService->create($request->validated('title'));
        
        return response()->json([
            'success' => true,
            'data' => $todo,
            'message' => 'Todo created successfully',
        ], 201);
    }

    public function show(Todo $todo): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $todo,
            'message' => 'Todo retrieved successfully',
        ]);
    }

    public function update(UpdateTodoRequest $request, Todo $todo): JsonResponse
    {
        $updated = $this->todoService->update($todo, $request->validated('title'));
        
        return response()->json([
            'success' => true,
            'data' => $updated,
            'message' => 'Todo updated successfully',
        ]);
    }

    public function destroy(Todo $todo): JsonResponse
    {
        $this->todoService->delete($todo);
        
        return response()->json([
            'success' => true,
            'message' => 'Todo deleted successfully',
        ]);
    }

    public function toggle(Todo $todo): JsonResponse
    {
        $toggled = $this->todoService->toggle($todo);
        
        return response()->json([
            'success' => true,
            'data' => $toggled,
            'message' => 'Todo completion toggled successfully',
        ]);
    }
}
```

### 2.5 Register Routes

**File**: `routes/api.php`

```php
use App\Http\Controllers\Api\V1\TodoController;

Route::prefix('v1')->group(function () {
    Route::apiResource('todos', TodoController::class);
    Route::patch('todos/{todo}/toggle', [TodoController::class, 'toggle']);
});
```

---

## Phase 3: Frontend Implementation

### 3.1 Create API Service

**File**: `resources/js/services/todoApi.js`

```javascript
import axios from 'axios'

const API_BASE = '/api/v1'

export const todoApi = {
  async getAll() {
    const response = await axios.get(`${API_BASE}/todos`)
    return response.data.data
  },

  async create(title) {
    const response = await axios.post(`${API_BASE}/todos`, { title })
    return response.data.data
  },

  async update(id, title) {
    const response = await axios.put(`${API_BASE}/todos/${id}`, { title })
    return response.data.data
  },

  async toggle(id) {
    const response = await axios.patch(`${API_BASE}/todos/${id}/toggle`)
    return response.data.data
  },

  async delete(id) {
    await axios.delete(`${API_BASE}/todos/${id}`)
  }
}
```

### 3.2 Create Components

**File**: `resources/js/Components/Todo/TodoList.vue`

```vue
<script setup>
import { ref, onMounted } from 'vue'
import { todoApi } from '@/services/todoApi'
import TodoItem from './TodoItem.vue'
import TodoForm from './TodoForm.vue'

const todos = ref([])
const loading = ref(false)

const fetchTodos = async () => {
  loading.value = true
  try {
    todos.value = await todoApi.getAll()
  } catch (error) {
    console.error('Failed to fetch todos:', error)
  } finally {
    loading.value = false
  }
}

const handleCreate = async (title) => {
  const newTodo = await todoApi.create(title)
  todos.value.unshift(newTodo)
}

const handleToggle = async (id) => {
  const updated = await todoApi.toggle(id)
  const index = todos.value.findIndex(t => t.id === id)
  if (index !== -1) todos.value[index] = updated
}

const handleUpdate = async (id, title) => {
  const updated = await todoApi.update(id, title)
  const index = todos.value.findIndex(t => t.id === id)
  if (index !== -1) todos.value[index] = updated
}

const handleDelete = async (id) => {
  await todoApi.delete(id)
  todos.value = todos.value.filter(t => t.id !== id)
}

onMounted(fetchTodos)
</script>

<template>
  <div class="max-w-2xl mx-auto p-4 sm:p-6">
    <h1 class="text-2xl sm:text-3xl font-bold mb-6">Todo List</h1>
    
    <TodoForm @create="handleCreate" class="mb-6" />
    
    <div v-if="loading" class="text-center py-8">
      <p class="text-gray-500">Loading...</p>
    </div>
    
    <div v-else-if="todos.length === 0" class="text-center py-8">
      <p class="text-gray-500">Chưa có todo nào. Tạo todo đầu tiên!</p>
    </div>
    
    <div v-else class="space-y-2">
      <TodoItem
        v-for="todo in todos"
        :key="todo.id"
        :todo="todo"
        @toggle="handleToggle"
        @update="handleUpdate"
        @delete="handleDelete"
      />
    </div>
  </div>
</template>
```

**File**: `resources/js/Components/Todo/TodoItem.vue`

```vue
<script setup>
import { ref } from 'vue'

const props = defineProps({
  todo: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['toggle', 'update', 'delete'])

const isEditing = ref(false)
const editTitle = ref(props.todo.title)

const startEdit = () => {
  isEditing.value = true
  editTitle.value = props.todo.title
}

const saveEdit = () => {
  if (editTitle.value.trim()) {
    emit('update', props.todo.id, editTitle.value)
    isEditing.value = false
  }
}

const cancelEdit = () => {
  isEditing.value = false
  editTitle.value = props.todo.title
}

const confirmDelete = () => {
  if (confirm('Bạn có chắc muốn xóa todo này?')) {
    emit('delete', props.todo.id)
  }
}
</script>

<template>
  <div class="flex items-center gap-3 p-3 bg-white rounded-lg shadow-sm border">
    <!-- Checkbox -->
    <input
      type="checkbox"
      :checked="todo.completed"
      @change="emit('toggle', todo.id)"
      class="w-5 h-5 rounded"
    />
    
    <!-- Title (Edit/Display) -->
    <div v-if="isEditing" class="flex-1 flex gap-2">
      <input
        v-model="editTitle"
        type="text"
        class="flex-1 px-3 py-1 border rounded"
        @keyup.enter="saveEdit"
        @keyup.esc="cancelEdit"
      />
      <button @click="saveEdit" class="px-3 py-1 bg-blue-500 text-white rounded">
        Save
      </button>
      <button @click="cancelEdit" class="px-3 py-1 bg-gray-300 rounded">
        Cancel
      </button>
    </div>
    
    <div v-else class="flex-1">
      <span :class="{ 'line-through text-gray-400': todo.completed }">
        {{ todo.title }}
      </span>
    </div>
    
    <!-- Actions -->
    <div v-if="!isEditing" class="flex gap-2">
      <button
        @click="startEdit"
        class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded"
      >
        Edit
      </button>
      <button
        @click="confirmDelete"
        class="px-3 py-1 text-sm bg-red-100 hover:bg-red-200 text-red-700 rounded"
      >
        Delete
      </button>
    </div>
  </div>
</template>
```

**File**: `resources/js/Components/Todo/TodoForm.vue`

```vue
<script setup>
import { ref } from 'vue'

const emit = defineEmits(['create'])

const title = ref('')
const error = ref('')

const validateTitle = (value) => {
  if (!value.trim()) return 'Tiêu đề là bắt buộc'
  if (value.length > 255) return 'Tiêu đề phải có tối đa 255 ký tự'
  return null
}

const handleSubmit = async () => {
  const validationError = validateTitle(title.value)
  if (validationError) {
    error.value = validationError
    return
  }
  
  try {
    await emit('create', title.value)
    title.value = ''
    error.value = ''
  } catch (err) {
    error.value = 'Failed to create todo'
  }
}
</script>

<template>
  <div class="bg-white p-4 rounded-lg shadow-sm border">
    <div class="flex gap-2">
      <div class="flex-1">
        <input
          v-model="title"
          type="text"
          placeholder="Nhập tiêu đề todo..."
          class="w-full px-4 py-2 border rounded-lg"
          @keyup.enter="handleSubmit"
        />
        <p v-if="error" class="text-sm text-red-600 mt-1">{{ error }}</p>
      </div>
      <button
        @click="handleSubmit"
        :disabled="!title.trim()"
        class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50"
      >
        Add
      </button>
    </div>
  </div>
</template>
```

### 3.3 Update Tailwind Config

**File**: `tailwind.config.js`

```javascript
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      // Custom colors if needed
    },
  },
  plugins: [],
}
```

---

## Phase 4: Testing

### 4.1 Create Feature Tests

**File**: `tests/Feature/Api/V1/TodoTest.php`

```php
<?php

namespace Tests\Feature\Api\V1;

use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_todos(): void
    {
        Todo::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/todos');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'title', 'completed', 'created_at', 'updated_at']
                ],
                'message'
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_todo(): void
    {
        $response = $this->postJson('/api/v1/todos', [
            'title' => 'Test todo'
        ]);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => 'Test todo',
                    'completed' => false
                ]
            ]);

        $this->assertDatabaseHas('todos', ['title' => 'Test todo']);
    }

    public function test_cannot_create_todo_with_empty_title(): void
    {
        $response = $this->postJson('/api/v1/todos', ['title' => '']);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('title');
    }

    public function test_can_toggle_todo_completion(): void
    {
        $todo = Todo::factory()->create(['completed' => false]);

        $response = $this->patchJson("/api/v1/todos/{$todo->id}/toggle");

        $response->assertOk()
            ->assertJson([
                'data' => ['completed' => true]
            ]);
    }

    public function test_can_update_todo_title(): void
    {
        $todo = Todo::factory()->create();

        $response = $this->putJson("/api/v1/todos/{$todo->id}", [
            'title' => 'Updated title'
        ]);

        $response->assertOk()
            ->assertJson([
                'data' => ['title' => 'Updated title']
            ]);
    }

    public function test_can_delete_todo(): void
    {
        $todo = Todo::factory()->create();

        $response = $this->deleteJson("/api/v1/todos/{$todo->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('todos', ['id' => $todo->id]);
    }
}
```

### 4.2 Create Factory

**File**: `database/factories/TodoFactory.php`

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TodoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'completed' => fake()->boolean(),
        ];
    }
}
```

### 4.3 Run Tests

```bash
php artisan test
```

---

## Phase 5: Development Workflow

### 5.1 Start Development Server

```bash
# Terminal 1: Laravel backend
php artisan serve

# Terminal 2: Vite frontend
npm run dev
```

### 5.2 Access Application

- **Frontend**: http://localhost:8000
- **API**: http://localhost:8000/api/v1/todos

### 5.3 Code Quality

```bash
# PHP formatting
./vendor/bin/pint

# Run tests
php artisan test
```

---

## Checklist

- [ ] Database migration created and run
- [ ] Todo model with `toggleCompletion()` method
- [ ] TodoService with all business logic
- [ ] Form Requests with Vietnamese error messages
- [ ] API Controller với 6 endpoints
- [ ] Routes registered in `routes/api.php`
- [ ] Vue components: TodoList, TodoItem, TodoForm
- [ ] API service với Axios
- [ ] Tailwind config updated
- [ ] Feature tests written và passing
- [ ] TodoFactory created
- [ ] Manual testing completed
- [ ] Code formatted với Pint
- [ ] All user stories testable
- [ ] Documentation updated

---

## Next Steps

1. **Phase 2**: Run `/speckit.tasks` để generate task breakdown
2. **Implementation**: Follow tasks.md để implement từng user story
3. **Testing**: Test mỗi user story independently
4. **Review**: Code review trước khi merge
5. **Deploy**: Merge vào main branch

---

## Troubleshooting

### Database Connection Error
```bash
# Check .env database credentials
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Axios 404 Errors
- Kiểm tra routes: `php artisan route:list`
- Verify API_BASE trong `todoApi.js`

### Tailwind Not Working
```bash
npm run build  # Rebuild assets
php artisan view:clear  # Clear view cache
```

## References

- [spec.md](spec.md) - Feature specification
- [plan.md](plan.md) - Implementation plan
- [data-model.md](data-model.md) - Data model design
- [contracts/](contracts/) - API contracts
- [research.md](research.md) - Research và decisions
