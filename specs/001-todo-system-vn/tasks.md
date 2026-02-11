# Tasks: Hệ thống Todo List Cơ bản

**Feature**: 001-todo-system-vn  
**Branch**: `001-todo-system-vn`  
**Input**: Design documents từ `/specs/001-todo-system-vn/`  
**Date**: 2026-02-10

## Implementation Strategy

**MVP-First Approach**: User Story 1 (P1) là MVP - có thể deploy độc lập và deliver value ngay lập tức.

**Independent Stories**: Mỗi user story có thể implement và test độc lập sau khi foundational phase hoàn thành.

**Parallel Opportunities**: Tasks đánh dấu [P] có thể chạy song song (different files, no dependencies).

## Format: `- [ ] [ID] [P?] [Story] Description`

- **[P]**: Parallelizable task (different files, no blocking dependencies)
- **[Story]**: User story label (US1, US2, US3, US4)
- **ID**: Sequential task number (T001, T002, ...)

---

## Phase 1: Setup (Project Infrastructure)

**Purpose**: Khởi tạo project và cấu hình cơ bản

**Tasks**:

- [X] T001 Verify Laravel 11 installation và PHP 8.3+ environment
- [X] T002 Configure PostgreSQL database connection in `.env`
- [X] T003 Set up Vite + Vue 3 + Tailwind CSS in `vite.config.js` và `tailwind.config.js`
- [X] T004 Install frontend dependencies: `npm install vue@3 axios @vitejs/plugin-vue`
- [X] T005 Install backend dependencies: `composer require laravel/sanctum` (for future auth)
- [X] T006 Configure Laravel Pint for PSR-12 in `pint.json`
- [X] T007 Create Git branch `001-todo-system-vn`

**Completion Criteria**: ✅ All dependencies installed, database connected, environment configured

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Database schema và base models - MUST complete trước khi implement user stories

**Tasks**:

- [X] T008 Create migration `database/migrations/2026_02_10_create_todos_table.php` (id, title, completed, timestamps)
- [X] T009 Run migration: `php artisan migrate`
- [X] T010 [P] Create Todo model in `app/Models/Todo.php` (strict_types, fillable, casts, toggleCompletion method)
- [X] T011 [P] Create TodoFactory in `database/factories/TodoFactory.php` for test data
- [X] T012 [P] Create base Vue app structure in `resources/js/app.js` with Vue 3 imports
- [X] T013 [P] Create base Blade template in `resources/views/app.blade.php` for SPA container
- [X] T014 Verify foundational setup: Run `php artisan tinker` and create a test Todo

**Completion Criteria**: ✅ Database schema exists, Todo model works, Vue app bootstrapped

**Dependency Graph**: Phase 1 → Phase 2 (blocks all user stories)

---

## Phase 3: User Story 1 (P1) - Xem và Tạo Todo

**Story Goal**: Người dùng có thể xem danh sách todos và tạo todo mới với tiêu đề 1-255 ký tự.

**Independent Test**: Hiển thị danh sách trống → Tạo todo → Verify xuất hiện trong danh sách

**Value**: Core MVP - Người dùng bắt đầu quản lý tasks ngay lập tức

### Backend Implementation

- [X] T015 [P] [US1] Create StoreTodoRequest in `app/Http/Requests/StoreTodoRequest.php` (validate title: required, string, max:255, Vietnamese messages)
- [X] T016 [US1] Create TodoService in `app/Services/TodoService.php` (getAll, create methods with strict types)
- [X] T017 [US1] Create TodoController in `app/Http/Controllers/Api/V1/TodoController.php` (index, store methods)
- [X] T018 [US1] Register API routes in `routes/api.php` (GET /api/v1/todos, POST /api/v1/todos)

### Frontend Implementation

- [X] T019 [P] [US1] Create todoApi service in `resources/js/services/todoApi.js` (getAll, create methods with Axios)
- [X] T020 [P] [US1] Create TodoForm component in `resources/js/Components/Todo/TodoForm.vue` (input, validation, emit create event)
- [X] T021 [P] [US1] Create TodoEmptyState component in `resources/js/Components/Todo/TodoEmptyState.vue` (Tailwind styling)
- [X] T022 [P] [US1] Create TodoItem component in `resources/js/Components/Todo/TodoItem.vue` (display title only, no edit/delete yet)
- [X] T023 [US1] Create TodoList component in `resources/js/Components/Todo/TodoList.vue` (fetch on mount, render items, handle create)
- [X] T024 [US1] Create TodosPage in `resources/js/Pages/TodosPage.vue` (main page container with Tailwind layout)
- [X] T025 [US1] Add Tailwind utilities for form styling (mobile-first: p-4 sm:p-6, text-sm md:text-base)

### Testing

- [X] T026 [US1] Write feature test in `tests/Feature/Api/V1/TodoTest.php` (test_can_list_todos, test_can_create_todo)
- [X] T027 [US1] Write validation tests (test_cannot_create_todo_with_empty_title, test_cannot_create_todo_with_title_over_255_chars)
- [X] T028 [US1] Run tests: `php artisan test --filter TodoTest`

**Completion Criteria**: 
✅ User can view empty list  
✅ User can create todo with valid title (1-255 chars)  
✅ Validation errors show in Vietnamese  
✅ Created todo appears in list immediately  
✅ All tests pass

**MVP Checkpoint**: 🎯 Deliverable after this phase - Users can start managing todos!

---

## Phase 4: User Story 2 (P2) - Đánh dấu Hoàn thành Todo

**Story Goal**: Người dùng có thể toggle todo completion status.

**Independent Test**: Tạo todo → Toggle completion → Verify visual indicator changes

**Value**: Essential cho task tracking - users see progress

### Backend Implementation

- [ ] T029 [US2] Add toggle method to TodoService in `app/Services/TodoService.php` (call toggleCompletion on model)
- [ ] T030 [US2] Add toggle endpoint to TodoController in `app/Http/Controllers/Api/V1/TodoController.php`
- [ ] T031 [US2] Register toggle route in `routes/api.php` (PATCH /api/v1/todos/{id}/toggle)

### Frontend Implementation

- [ ] T032 [P] [US2] Add toggle method to todoApi service in `resources/js/services/todoApi.js`
- [ ] T033 [US2] Update TodoItem component in `resources/js/Components/Todo/TodoItem.vue` (add checkbox, emit toggle event, line-through styling)
- [ ] T034 [US2] Update TodoList component to handle toggle event in `resources/js/Components/Todo/TodoList.vue`
- [ ] T035 [US2] Add Tailwind conditional styling (line-through text-gray-400 for completed)

### Testing

- [ ] T036 [US2] Write toggle tests in `tests/Feature/Api/V1/TodoTest.php` (test_can_toggle_todo_completion, test_toggle_is_idempotent)
- [ ] T037 [US2] Test completion state persistence after page refresh
- [ ] T038 [US2] Run tests: `php artisan test --filter TodoTest`

**Completion Criteria**:
✅ Uncompleted todo → Toggle → Shows completed (visual indicator)  
✅ Completed todo → Toggle → Shows uncompleted  
✅ State persists across page refresh  
✅ All tests pass

**Dependency**: Requires US1 (create todos to toggle)

---

## Phase 5: User Story 3 (P3) - Chỉnh sửa Tiêu đề Todo

**Story Goal**: Người dùng có thể edit todo title với validation rules giống create.

**Independent Test**: Tạo todo → Edit title → Save → Verify updated title

**Value**: Flexibility - users can fix typos và update descriptions

### Backend Implementation

- [ ] T039 [P] [US3] Create UpdateTodoRequest in `app/Http/Requests/UpdateTodoRequest.php` (same validation as create)
- [ ] T040 [US3] Add update method to TodoService in `app/Services/TodoService.php`
- [ ] T041 [US3] Add update endpoint to TodoController in `app/Http/Controllers/Api/V1/TodoController.php`
- [ ] T042 [US3] Register update route in `routes/api.php` (PUT /api/v1/todos/{id})

### Frontend Implementation

- [ ] T043 [P] [US3] Add update method to todoApi service in `resources/js/services/todoApi.js`
- [ ] T044 [US3] Add edit mode to TodoItem component in `resources/js/Components/Todo/TodoItem.vue` (isEditing state, input field, save/cancel buttons)
- [ ] T045 [US3] Update TodoList component to handle update event in `resources/js/Components/Todo/TodoList.vue`
- [ ] T046 [US3] Add Tailwind styling for edit mode (border, focus states, button colors)

### Testing

- [ ] T047 [US3] Write update tests in `tests/Feature/Api/V1/TodoTest.php` (test_can_update_todo_title, test_cannot_update_with_empty_title)
- [ ] T048 [US3] Test cancel edit restores original title
- [ ] T049 [US3] Run tests: `php artisan test --filter TodoTest`

**Completion Criteria**:
✅ User can enter edit mode  
✅ User can save valid title (1-255 chars)  
✅ User can cancel edit (original title restored)  
✅ Validation errors show for invalid input  
✅ All tests pass

**Dependency**: Requires US1 (create todos to edit)

---

## Phase 6: User Story 4 (P4) - Xóa Todo

**Story Goal**: Người dùng có thể delete todos với confirmation.

**Independent Test**: Tạo todo → Delete with confirmation → Verify removed from list

**Value**: Cleanup - keeps list focused on current tasks

### Backend Implementation

- [ ] T050 [US4] Add delete method to TodoService in `app/Services/TodoService.php`
- [ ] T051 [US4] Add destroy endpoint to TodoController in `app/Http/Controllers/Api/V1/TodoController.php`
- [ ] T052 [US4] Verify DELETE route in `routes/api.php` (already registered by apiResource)

### Frontend Implementation

- [ ] T053 [P] [US4] Add delete method to todoApi service in `resources/js/services/todoApi.js`
- [ ] T054 [P] [US4] Create ConfirmModal component in `resources/js/Components/Shared/ConfirmModal.vue` (Tailwind modal styling)
- [ ] T055 [US4] Add delete button to TodoItem component in `resources/js/Components/Todo/TodoItem.vue`
- [ ] T056 [US4] Add confirmation dialog before delete (native confirm or ConfirmModal)
- [ ] T057 [US4] Update TodoList component to handle delete event in `resources/js/Components/Todo/TodoList.vue`
- [ ] T058 [US4] Add Tailwind styling for delete button (bg-red-100 hover:bg-red-200 text-red-700)

### Testing

- [ ] T059 [US4] Write delete tests in `tests/Feature/Api/V1/TodoTest.php` (test_can_delete_todo, test_delete_nonexistent_returns_404)
- [ ] T060 [US4] Test empty state shows after deleting last todo
- [ ] T061 [US4] Run tests: `php artisan test --filter TodoTest`

**Completion Criteria**:
✅ User can delete todo after confirmation  
✅ User can cancel delete (todo remains)  
✅ Empty state shows when last todo deleted  
✅ All tests pass

**Dependency**: Requires US1 (create todos to delete)

---

## Phase 7: Polish & Cross-Cutting Concerns

**Purpose**: Quality, performance, và production readiness

### Code Quality

- [ ] T062 [P] Run Laravel Pint: `./vendor/bin/pint` (format all PHP to PSR-12)
- [ ] T063 [P] Add strict_types declaration to all PHP files
- [ ] T064 [P] Verify all Vue component props have validation
- [ ] T065 [P] Add loading states to all async operations in TodoList.vue

### Error Handling

- [ ] T066 [P] Add try-catch blocks to all API calls in todoApi.js
- [ ] T067 [P] Show error messages to user when API calls fail (Toast/Alert component)
- [ ] T068 [P] Handle network errors gracefully (retry logic or user feedback)

### Performance

- [ ] T069 [P] Add index on `completed` column (already in migration - verify)
- [ ] T070 [P] Test with 1000 todos (success criterion SC-003)
- [ ] T071 [P] Verify toggle response time <100ms (success criterion SC-002)

### Documentation

- [ ] T072 [P] Add JSDoc comments to todoApi methods
- [ ] T073 [P] Add PHPDoc blocks to TodoService methods
- [ ] T074 [P] Update README.md with setup instructions (reference quickstart.md)

### Final Testing

- [ ] T075 Run full test suite: `php artisan test`
- [ ] T076 Manual testing: Test all user stories end-to-end
- [ ] T077 Test edge cases: Concurrent toggles, rapid creates, duplicate titles
- [ ] T078 Test special characters: Create todos with emoji (🎯📝), Vietnamese diacritics (ĂĂĂĂĂ), special symbols (@#$)
- [ ] T079 Verify all acceptance scenarios from spec.md
- [ ] T080 Test responsive design on mobile/tablet/desktop
- [ ] T081 Manual UX test: Verify SC-001 (can create todo within 10 seconds of accessing app)

### Deployment Prep

- [ ] T082 [P] Review .env.example for production settings
- [ ] T083 [P] Generate API documentation from OpenAPI spec
- [ ] T084 Verify all console.log removed from production code
- [ ] T085 Run production build: `npm run build`
- [ ] T086 Final code review checklist completion

**Completion Criteria**:
✅ All code formatted (PSR-12, no eslint errors)  
✅ All tests pass (100% endpoint coverage)  
✅ Performance targets met  
✅ Error handling comprehensive  
✅ Ready for production deployment

---

## Dependency Graph

```
Phase 1 (Setup)
    ↓
Phase 2 (Foundational) ← BLOCKS all user stories
    ↓
    ├─→ Phase 3: US1 (P1) ← MVP
    │       ↓
    │       ├─→ Phase 4: US2 (P2)
    │       ├─→ Phase 5: US3 (P3)
    │       └─→ Phase 6: US4 (P4)
    │
    └─→ Phase 7 (Polish)
```

**Critical Path**: Phase 1 → Phase 2 → Phase 3 (US1) → Phase 7

**Parallel Opportunities**:
- After Phase 2: US2, US3, US4 có thể implement parallel (chỉ depend on US1 for testing context)
- Within each phase: Tasks đánh dấu [P] có thể run concurrently

---

## Parallel Execution Examples

### Phase 3 (US1) - Backend & Frontend Parallel

**Track 1 (Backend)**: T015 → T016 → T017 → T018  
**Track 2 (Frontend)**: T019 → T020, T021, T022 (parallel) → T023 → T024 → T025  
**Track 3 (Tests)**: T026 → T027 → T028 (wait for Track 1 & 2)

### Phase 4-6 (US2, US3, US4) - Story Parallelization

Sau khi US1 complete, có thể assign 3 developers:
- **Dev A**: Implement US2 (T029-T038)
- **Dev B**: Implement US3 (T039-T049)
- **Dev C**: Implement US4 (T050-T061)

### Phase 7 - Full Parallelization

Tất cả tasks T062-T074 đều [P] → có thể chạy đồng thời

---

## Task Summary

**Total Tasks**: 86

### By Phase:
- Phase 1 (Setup): 7 tasks
- Phase 2 (Foundational): 7 tasks
- Phase 3 (US1 - P1): 14 tasks  
- Phase 4 (US2 - P2): 10 tasks
- Phase 5 (US3 - P3): 11 tasks
- Phase 6 (US4 - P4): 12 tasks
- Phase 7 (Polish): 25 tasks

### By Type:
- Backend: 24 tasks
- Frontend: 28 tasks
- Testing: 17 tasks
- Infrastructure: 7 tasks
- Quality/Polish: 10 tasks

### Parallelizable:
- [P] tasks: 38 tasks (44% can run in parallel)
- Sequential tasks: 48 tasks

---

## MVP Scope (Recommended First Delivery)

**MVP = Phase 1 + Phase 2 + Phase 3 (US1)**

**Tasks**: T001-T028 (28 tasks)  
**Estimated Effort**: ~6-8 hours for solo developer  
**Value**: Users can create và view todos immediately

**Post-MVP**: Add US2 (toggle), US3 (edit), US4 (delete) incrementally based on user feedback

---

## Testing Strategy

### Test Coverage by Phase

**Phase 3 (US1)**:
- ✅ List todos (empty & with data)
- ✅ Create todo (valid title)
- ✅ Validation errors (empty, >255 chars)

**Phase 4 (US2)**:
- ✅ Toggle completion (both directions)
- ✅ State persistence

**Phase 5 (US3)**:
- ✅ Update title (valid & invalid)
- ✅ Cancel edit

**Phase 6 (US4)**:
- ✅ Delete todo
- ✅ Empty state after delete

**Phase 7**:
- ✅ Edge cases (special chars, concurrent ops)
- ✅ Performance (1000 todos, <100ms toggle)
- ✅ End-to-end scenarios

### Test Commands

```bash
# Run all tests
php artisan test

# Run specific suite
php artisan test --filter TodoTest

# Run with coverage
php artisan test --coverage

# Frontend tests (if added later)
npm run test
```

---

## Implementation Workflow

### Daily Workflow

1. **Pick next task** from current phase
2. **Create feature branch** (if not exists): `git checkout -b 001-todo-system-vn`
3. **Implement task** following constitution guidelines
4. **Run tests**: `php artisan test`
5. **Format code**: `./vendor/bin/pint`
6. **Commit**: `git commit -m "[T0XX] Task description"`
7. **Mark task complete**: Check off in this file
8. **Repeat** until phase complete

### Phase Completion Checklist

After each phase:
- [ ] All phase tasks checked off
- [ ] All tests passing
- [ ] Code formatted (Pint)
- [ ] Completion criteria met
- [ ] Git commit: `git commit -m "Complete Phase X: [Phase Name]"`

### Final Delivery

Before merging to main:
- [ ] All 84 tasks completed
- [ ] Full test suite passing
- [ ] Manual testing done
- [ ] Code review completed
- [ ] Documentation updated
- [ ] PR created with summary

---

## Constitution Compliance Verification

### ✅ API-First Architecture
- Tasks T018, T031, T042, T052: All endpoints in `/api/v1/*`
- Tasks T017, T030, T041, T051: Controllers return JSON responses

### ✅ Single Responsibility
- Task T016: TodoService (business logic only)
- Tasks T017, T030, T041, T051: Controllers (routing only)
- Tasks T020-T024: Each component single purpose

### ✅ Type Safety
- Task T063: strict_types in all PHP files
- Tasks T010, T016, T029, T040, T050: Type hints required
- Task T064: Vue props validation

### ✅ Tailwind Utility-First
- Tasks T025, T035, T046, T058: All styling with Tailwind utilities
- Task T003: Tailwind config for theme customization
- Mobile-first: All components use responsive breakpoints

### ✅ PSR-12 & Code Standards
- Task T006: Laravel Pint configured
- Task T062: Format all code before completion
- File structure follows Laravel conventions

### ✅ Testing Requirements
- Tasks T026-T028, T036-T038, T047-T049, T059-T061: Feature tests
- Task T075: Full test suite
- Task T079: Verify all acceptance scenarios

**All constitutional requirements addressed in task breakdown** ✅

---

## References

- **[spec.md](spec.md)**: User stories và acceptance criteria
- **[plan.md](plan.md)**: Technical context và architecture
- **[data-model.md](data-model.md)**: Entity definitions và validation rules
- **[contracts/](contracts/)**: API specifications (OpenAPI)
- **[quickstart.md](quickstart.md)**: Implementation examples và code snippets
- **[research.md](research.md)**: Technology decisions và best practices
- **[constitution.md](../../.specify/memory/constitution.md)**: Project principles

---

**Status**: 📋 Ready for Implementation  
**Next Action**: Start with Phase 1 Task T001  
**Est. Completion**: 16-24 hours (solo dev, all phases)
