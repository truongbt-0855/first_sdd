# Data Model: Hệ thống Todo List Cơ bản

**Feature**: 001-todo-system-vn  
**Date**: 2026-02-10  
**Phase**: Phase 1 - Data Model Design

## Entity Overview

Hệ thống todo list có **một entity chính**: `Todo`

```
┌─────────────┐
│    Todo     │
│─────────────│
│ id          │ PK
│ title       │
│ completed   │
│ created_at  │
│ updated_at  │
└─────────────┘
```

---

## Entity: Todo

### Description
Đại diện cho một mục nhiệm vụ (todo item) trong danh sách của người dùng.

### Fields

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | `bigint` | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| `title` | `varchar(255)` | NOT NULL, LENGTH 1-255 | Tiêu đề nhiệm vụ |
| `completed` | `boolean` | NOT NULL, DEFAULT false | Trạng thái hoàn thành |
| `created_at` | `timestamp` | NOT NULL, AUTO | Thời điểm tạo |
| `updated_at` | `timestamp` | NOT NULL, AUTO | Thời điểm cập nhật cuối |

### Indexes

```sql
PRIMARY KEY (id)
INDEX idx_todos_completed (completed)  -- For future filtering by status
```

**Rationale**: 
- Index trên `completed` hỗ trợ queries lọc theo trạng thái (ví dụ: "show only active todos")
- Primary key auto-increment đảm bảo uniqueness
- Timestamps tự động track creation/modification times

---

## Validation Rules

### Create Todo

**Source**: Feature requirements FR-002, FR-003

| Field | Rules | Error Message (Vietnamese) |
|-------|-------|---------------------------|
| `title` | required | "Tiêu đề là bắt buộc" |
| `title` | string | "Tiêu đề phải là chuỗi ký tự" |
| `title` | min:1 | "Tiêu đề không được để trống" |
| `title` | max:255 | "Tiêu đề phải có tối đa 255 ký tự" |

**Initial State**:
- `completed` = `false` (default)
- `created_at` = current timestamp
- `updated_at` = current timestamp

### Update Todo

**Source**: Feature requirement FR-006

| Field | Rules | Error Message (Vietnamese) |
|-------|-------|---------------------------|
| `title` | required | "Tiêu đề là bắt buộc" |
| `title` | string | "Tiêu đề phải là chuỗi ký tự" |
| `title` | min:1 | "Tiêu đề không được để trống" |
| `title` | max:255 | "Tiêu đề phải có tối đa 255 ký tự" |

**Updated Fields**:
- `title` = new value
- `updated_at` = current timestamp (auto)

### Toggle Completion

**Source**: Feature requirements FR-004, FR-005

**Rules**:
- No validation required (boolean toggle)
- Idempotent operation

**State Change**:
- `completed` = NOT `completed` (flip boolean)
- `updated_at` = current timestamp (auto)

### Delete Todo

**Source**: Feature requirement FR-007

**Rules**:
- Todo must exist (404 if not found)
- Confirmation required on frontend (prevent accidental deletion)

---

## State Transitions

### Todo Lifecycle

```
┌─────────────────────────────────────────────────────┐
│                   TODO LIFECYCLE                    │
└─────────────────────────────────────────────────────┘

[CREATE]
   │
   ├─> title (1-255 chars)
   ├─> completed = false
   ├─> created_at = NOW()
   └─> updated_at = NOW()
   │
   ▼
┌──────────────────┐
│  UNCOMPLETED     │ <──┐
│  completed=false │    │
└──────────────────┘    │
   │                    │
   │ [TOGGLE]           │ [TOGGLE]
   │                    │
   ▼                    │
┌──────────────────┐    │
│   COMPLETED      │ ───┘
│  completed=true  │
└──────────────────┘
   │
   │ [UPDATE TITLE]
   │
   ▼
┌──────────────────┐
│   (same state,   │
│  new title)      │
└──────────────────┘
   │
   │ [DELETE]
   │
   ▼
┌──────────────────┐
│    DELETED       │
│  (removed from   │
│   database)      │
└──────────────────┘
```

### State Transition Rules

| From State | Action | To State | Validation |
|-----------|--------|----------|------------|
| N/A | CREATE | UNCOMPLETED | title required, 1-255 chars |
| UNCOMPLETED | TOGGLE | COMPLETED | None |
| COMPLETED | TOGGLE | UNCOMPLETED | None |
| ANY | UPDATE | Same state, new title | title required, 1-255 chars |
| ANY | DELETE | DELETED | Confirmation required (frontend) |

**Invariants**:
1. `completed` luôn là boolean (`true` hoặc `false`)
2. `title` không bao giờ empty sau khi validate
3. `created_at` không bao giờ thay đổi sau khi create
4. `updated_at` tự động update mỗi khi modify

---

## Edge Cases & Business Rules

### Edge Case 1: Duplicate Titles

**Scenario**: Người dùng tạo nhiều todos với title giống hệt nhau

**Business Rule**: **ALLOWED** - Không có unique constraint trên title

**Rationale**: 
- User có thể có multiple todos cho cùng task (ví dụ: "Mua sữa" cho tuần này và tuần sau)
- `id` là unique identifier, không phải title

### Edge Case 2: Empty String vs NULL

**Scenario**: Title = "" (empty string)

**Business Rule**: **REJECTED** - Validation rule `min:1` prevents empty strings

**Database Behavior**:
- `title` column NOT NULL → không chấp nhận NULL
- Validation layer → không chấp nhận empty string

### Edge Case 3: Special Characters in Title

**Scenario**: Title chứa emoji, dấu tiếng Việt, ký tự đặc biệt

**Business Rule**: **ALLOWED** - VARCHAR supports UTF-8

**Examples**:
- ✅ "Mua sữa 🥛"
- ✅ "Học tiếng Việt: ĂĂĂĂĂ"
- ✅ "Fix bug #123 @urgent"
- ❌ "" (empty - validation error)
- ❌ (256+ chars - validation error)

### Edge Case 4: Soft Delete vs Hard Delete

**Scenario**: Xóa todo

**Business Rule**: **HARD DELETE** - Permanently remove from database

**Rationale**:
- Spec không yêu cầu restore/undo functionality
- Hard delete đơn giản hơn cho MVP
- Future: Có thể thêm `deleted_at` column cho soft deletes

### Edge Case 5: Concurrent Updates

**Scenario**: Hai requests update cùng todo đồng thời

**Business Rule**: **LAST WRITE WINS** - No optimistic locking for MVP

**Rationale**:
- Single-user system trong MVP
- PostgreSQL transaction isolation handles concurrency
- Future: Thêm version column nếu cần optimistic locking

### Edge Case 6: Toggle Idempotency

**Scenario**: Toggle completion nhiều lần liên tục

**Business Rule**: **IDEMPOTENT** - Mỗi request flips state

**Behavior**:
```
Initial: completed = false
Toggle 1: completed = true
Toggle 2: completed = false
Toggle 3: completed = true
```

**Note**: Không phải truly idempotent (multiple calls → different results), nhưng predictable behavior

---

## Database Schema (PostgreSQL)

### Migration: Create Todos Table

```sql
CREATE TABLE todos (
    id              BIGSERIAL PRIMARY KEY,
    title           VARCHAR(255) NOT NULL CHECK (char_length(title) >= 1),
    completed       BOOLEAN NOT NULL DEFAULT FALSE,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Index for filtering by completion status
CREATE INDEX idx_todos_completed ON todos(completed);

-- Trigger to auto-update updated_at
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_todos_updated_at
BEFORE UPDATE ON todos
FOR EACH ROW
EXECUTE FUNCTION update_updated_at_column();
```

**Laravel Migration Equivalent**:

```php
// database/migrations/2026_02_10_create_todos_table.php
public function up(): void
{
    Schema::create('todos', function (Blueprint $table) {
        $table->id();
        $table->string('title', 255);
        $table->boolean('completed')->default(false);
        $table->timestamps(); // created_at, updated_at
        
        $table->index('completed'); // For filtering
    });
}
```

---

## Eloquent Model (Laravel)

```php
// app/Models/Todo.php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Todo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'completed',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'completed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Toggle the completion status.
     */
    public function toggleCompletion(): void
    {
        $this->completed = !$this->completed;
        $this->save();
    }
}
```

---

## TypeScript Interface (Frontend)

```typescript
// resources/js/types/todo.ts

export interface Todo {
  id: number
  title: string
  completed: boolean
  created_at: string  // ISO 8601 timestamp
  updated_at: string  // ISO 8601 timestamp
}

export interface CreateTodoRequest {
  title: string
}

export interface UpdateTodoRequest {
  title: string
}

export interface TodoValidationErrors {
  title?: string[]
}
```

---

## Summary

### Entities Count: 1
- **Todo**: Core entity với CRUD operations

### Validation Summary
- **Create**: title required (1-255 chars)
- **Update**: title required (1-255 chars)
- **Toggle**: No validation
- **Delete**: Existence check only

### State Transitions: 2
- UNCOMPLETED ↔ COMPLETED (toggle)
- ANY → DELETED (delete)

### Business Rules
1. ✅ Duplicate titles allowed
2. ❌ Empty titles rejected
3. ✅ Special characters/UTF-8 allowed
4. ✅ Hard delete (no soft delete)
5. ⚠️ Last write wins (no optimistic locking)

### Performance Considerations
- Index trên `completed` cho future filtering
- Timestamps auto-managed (no manual updates)
- Simple boolean toggle (fast operation)

**Next Step**: Contract design (API endpoints specification)
