# Research: Hệ thống Todo List Cơ bản

**Feature**: 001-todo-system-vn  
**Date**: 2026-02-10  
**Phase**: Phase 0 - Technology Research & Decision Documentation

## Executive Summary

Tài liệu này ghi nhận các quyết định công nghệ, best practices, và patterns được áp dụng cho hệ thống todo list dựa trên constitution của project.

---

## 1. Backend Architecture: Laravel 11 + PostgreSQL

### Decision: RESTful API với Laravel 11

**Rationale**:
- Laravel 11 cung cấp built-in support cho API development (API routes, Form Requests, Resources)
- Eloquent ORM đơn giản hóa database operations với PostgreSQL
- Sanctum authentication sẵn sàng cho future multi-user support
- PHPUnit integration cho testing
- Laravel Pint cho code formatting (PSR-12)

**Best Practices Applied**:
1. **API Versioning**: Tất cả endpoints có prefix `/api/v1/` để hỗ trợ versioning
2. **Service Layer Pattern**: 
   - Controllers chỉ làm routing và validation
   - TodoService chứa business logic (create, update, delete, toggle completion)
   - Tách biệt concerns, dễ test
3. **Form Requests**: 
   - `StoreTodoRequest`: Validate title (required, max:255)
   - `UpdateTodoRequest`: Validate title (required, max:255)
   - Centralized validation logic
4. **Consistent Response Structure**:
   ```php
   return response()->json([
       'success' => true,
       'data' => $todo,
       'message' => 'Todo created successfully'
   ], 201);
   ```

**Alternatives Considered**:
- **Alternative**: Repository Pattern
  - **Rejected**: Overkill cho simple CRUD. Eloquent đã đủ abstraction cho use case này
- **Alternative**: GraphQL
  - **Rejected**: REST đơn giản hơn cho CRUD operations, GraphQL phức tạp không cần thiết

---

## 2. Database Design: PostgreSQL

### Decision: PostgreSQL 15+ với Eloquent ORM

**Rationale**:
- PostgreSQL production-ready, reliable cho data persistence
- ACID compliance đảm bảo data integrity
- Indexes trên `user_id` và `completed` cho future scalability
- Laravel migrations cho version control của schema

**Schema Design**:
```sql
CREATE TABLE todos (
    id BIGSERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE INDEX idx_todos_completed ON todos(completed);
```

**Best Practices Applied**:
- Timestamps (`created_at`, `updated_at`) automatic tracking
- Boolean flag cho completion status (simple, efficient queries)
- Index trên `completed` cho future filtering (show only active todos)
- VARCHAR(255) match với validation rules

**Alternatives Considered**:
- **Alternative**: Separate `completed_at` timestamp thay vì boolean
  - **Rejected**: Không cần thiết cho MVP. Boolean đủ cho current requirements
- **Alternative**: SQLite
  - **Rejected**: Constitution chỉ định PostgreSQL cho production readiness

---

## 3. Frontend Architecture: Vue 3 + Tailwind CSS

### Decision: Vue 3 Composition API với Tailwind Utility-First Styling

**Rationale**:
- **Vue 3 Composition API**: Better code organization, TypeScript support, reusable logic
- **`<script setup>`**: Cleaner syntax, less boilerplate
- **Tailwind CSS**: Utility-first eliminates CSS files, mobile-first responsive
- **Vite**: Fast HMR, modern build tool

**Component Architecture**:
```
TodosPage.vue            # Page container, manages state
├── TodoList.vue         # List container, iterates todos
│   └── TodoItem.vue     # Single todo, handles display/edit/delete
├── TodoForm.vue         # Create new todo form
└── TodoEmptyState.vue   # Empty state UI
```

**Best Practices Applied**:
1. **Single Responsibility**: Mỗi component làm một việc duy nhất
2. **Props Validation**: Bắt buộc defineProps với types
   ```vue
   const props = defineProps<{
     todo: {
       id: number
       title: string
       completed: boolean
     }
   }>()
   ```
3. **Composition API Patterns**:
   - `ref()` cho reactive state
   - `computed()` cho derived state
   - `onMounted()` cho data fetching
4. **Tailwind Mobile-First**:
   ```html
   <!-- Base: mobile, md: tablet, lg: desktop -->
   <div class="p-4 md:p-6 lg:p-8">
     <h1 class="text-xl md:text-2xl lg:text-3xl">
   ```

**Alternatives Considered**:
- **Alternative**: Options API
  - **Rejected**: Constitution chỉ định Composition API
- **Alternative**: CSS Modules / Styled Components
  - **Rejected**: Constitution bắt buộc Tailwind utility-first

---

## 4. API Design Patterns

### Decision: RESTful CRUD với Standard HTTP Methods

**Endpoints Design**:
```
GET    /api/v1/todos          # List all todos
POST   /api/v1/todos          # Create new todo
GET    /api/v1/todos/{id}     # Get single todo (optional)
PUT    /api/v1/todos/{id}     # Update todo title
PATCH  /api/v1/todos/{id}/toggle  # Toggle completion
DELETE /api/v1/todos/{id}     # Delete todo
```

**Best Practices Applied**:
1. **HTTP Verbs**: Semantic meaning (GET=read, POST=create, PUT=update, DELETE=delete)
2. **Status Codes**:
   - 200: Success (GET, PUT, PATCH, DELETE)
   - 201: Created (POST)
   - 422: Validation Error
   - 404: Not Found
3. **Error Handling**:
   ```json
   {
     "success": false,
     "message": "Validation failed",
     "errors": {
       "title": ["The title field is required."]
     }
   }
   ```
4. **Idempotency**: PUT/DELETE operations idempotent

**Alternatives Considered**:
- **Alternative**: PATCH cho update thay vì PUT
  - **Decision**: Dùng cả hai - PUT cho full update, PATCH cho toggle (partial update)
- **Alternative**: POST `/api/v1/todos/{id}/complete` và `/uncomplete`
  - **Rejected**: Toggle endpoint đơn giản hơn, ít endpoints hơn

---

## 5. State Management: Local Component State

### Decision: Vue Component State (ref/reactive) - No Pinia

**Rationale**:
- Todo list state đơn giản, không cần global state management
- Component props/emits đủ cho parent-child communication
- Fetch from API on mount, update local state on mutations
- Pinia overkill cho current scope

**State Flow**:
```
TodosPage.vue (owns todos array)
  ├── Fetch todos từ API on mount
  ├── Pass todos to TodoList via props
  ├── Listen to events from children (create, update, delete)
  └── Update local state & sync with API
```

**Best Practices Applied**:
1. **Single Source of Truth**: `TodosPage` owns todos array
2. **Unidirectional Data Flow**: Props down, events up
3. **Optimistic Updates**: Update UI immediately, rollback on API error
4. **Loading States**: Show loading spinner during API calls

**Alternatives Considered**:
- **Alternative**: Pinia store
  - **Rejected**: Over-engineering cho simple CRUD. Thêm vào khi cần share state across routes
- **Alternative**: Vuex
  - **Rejected**: Vuex deprecated in favor of Pinia

---

## 6. Testing Strategy

### Decision: Laravel Feature Tests + Unit Tests

**Test Coverage**:

**Backend**:
1. **Feature Tests** (`tests/Feature/Api/V1/TodoTest.php`):
   ```php
   // Test all API endpoints
   test_can_list_todos()
   test_can_create_todo_with_valid_title()
   test_cannot_create_todo_with_empty_title()
   test_cannot_create_todo_with_title_over_255_chars()
   test_can_toggle_todo_completion()
   test_can_update_todo_title()
   test_can_delete_todo()
   test_returns_404_for_nonexistent_todo()
   ```

2. **Unit Tests** (`tests/Unit/Services/TodoServiceTest.php`):
   ```php
   // Test business logic in isolation
   test_service_creates_todo_with_correct_defaults()
   test_service_toggles_completion_correctly()
   ```

**Best Practices Applied**:
1. **AAA Pattern**: Arrange, Act, Assert
2. **Database Transactions**: Rollback sau mỗi test (RefreshDatabase trait)
3. **Factories**: TodoFactory cho test data
4. **Test Data Independence**: Mỗi test setup own data

**Alternatives Considered**:
- **Alternative**: Pest PHP
  - **Considered**: Modern syntax, nhưng PHPUnit là Laravel default
- **Alternative**: Frontend unit tests (Vitest)
  - **Deferred**: Focus on backend tests first, add frontend tests in Phase 2

---

## 7. Validation & Error Handling

### Decision: Laravel Form Requests + Frontend Validation

**Backend Validation** (Laravel Form Requests):
```php
// StoreTodoRequest
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
```

**Frontend Validation** (Vue):
```javascript
// Real-time validation
const validateTitle = (title) => {
  if (!title.trim()) return 'Tiêu đề là bắt buộc'
  if (title.length > 255) return 'Tiêu đề phải có tối đa 255 ký tự'
  return null
}
```

**Best Practices Applied**:
1. **Defense in Depth**: Validate both frontend (UX) và backend (security)
2. **User-Friendly Messages**: Vietnamese error messages
3. **Immediate Feedback**: Frontend shows errors on blur/input
4. **Prevent Invalid Submissions**: Disable submit button when invalid

---

## 8. Responsive Design: Mobile-First Tailwind

### Decision: Mobile-First Breakpoints với Tailwind

**Breakpoint Strategy**:
```javascript
// tailwind.config.js
module.exports = {
  theme: {
    screens: {
      'sm': '640px',   // Tablet
      'md': '768px',   // Small desktop
      'lg': '1024px',  // Desktop
    }
  }
}
```

**Mobile-First Example**:
```html
<!-- Base: mobile (full width, small padding) -->
<!-- sm: tablet (max-width container, medium padding) -->
<!-- lg: desktop (narrower container, large padding) -->
<div class="w-full sm:max-w-2xl lg:max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
  <input class="text-sm sm:text-base lg:text-lg">
</div>
```

**Best Practices Applied**:
1. **Touch-Friendly**: Buttons min 44x44px (Apple HIG)
2. **Readable Text**: Base 16px, scale up on larger screens
3. **Stacking on Mobile**: Flex column on mobile, row on desktop
4. **Adequate Spacing**: More padding on desktop

---

## 9. Performance Optimization

### Decision: Simple Optimizations for <100ms Toggle Response

**Strategies**:
1. **Database Indexes**: Index trên `completed` column
2. **Eager Loading**: N/A (single table, no relations yet)
3. **Optimistic UI Updates**: Update UI trước API response
4. **Debouncing**: Debounce search/filter (future feature)
5. **Pagination**: Limit 100 todos per page (future feature)

**Performance Targets** (from success criteria):
- Toggle completion: <100ms (SC-002)
- Support 1000 todos/user: PostgreSQL handles easily (SC-003)
- 95% success rate: Error handling + retries (SC-004)

**Best Practices Applied**:
1. **Lazy Loading**: Components loaded on-demand
2. **Minimize Re-Renders**: Vue's reactivity handles this
3. **API Response Size**: Return only needed fields

---

## 10. Development Workflow

### Decision: Git Flow với Feature Branches

**Workflow**:
1. Branch: `001-todo-system-vn`
2. Development:
   - Backend first (migrations, models, services, controllers, tests)
   - Frontend second (components, API integration, styling)
3. Testing: All tests pass before merge
4. Code Quality: Laravel Pint before commit
5. Merge: PR to main after review

**Tools**:
- **Laravel Pint**: Auto-format PHP (PSR-12)
- **Composer**: PHP dependency management
- **npm/pnpm**: JavaScript dependency management
- **Vite**: Hot reload during development

---

## Summary: Key Decisions

| Decision Area | Choice | Rationale |
|--------------|--------|-----------|
| **Backend** | Laravel 11 API | Constitution requirement, proven framework |
| **Database** | PostgreSQL | Constitution requirement, production-ready |
| **Frontend** | Vue 3 Composition API | Constitution requirement, modern patterns |
| **Styling** | Tailwind CSS | Constitution requirement, utility-first |
| **API Pattern** | RESTful CRUD | Simple, standard, fits todo operations |
| **State Management** | Component state | No global state needed for MVP |
| **Testing** | Feature + Unit tests | Constitution requirement, comprehensive coverage |
| **Validation** | Backend + Frontend | Defense in depth, good UX |
| **Responsive** | Mobile-first Tailwind | Constitution requirement, modern practice |

**No unresolved clarifications** - Tất cả tech stack đã define trong constitution.

---

**Next Steps**: Proceed to Phase 1 - Data Model & Contract Design
