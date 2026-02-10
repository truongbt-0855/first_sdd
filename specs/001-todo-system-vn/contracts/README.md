# API Contracts: Todo List System

**Feature**: 001-todo-system-vn  
**Version**: 1.0.0  
**Date**: 2026-02-10

## Overview

Contract definitions cho REST API của hệ thống todo list. Tất cả API endpoints tuân theo chuẩn RESTful và trả về JSON responses nhất quán.

## Files

- **[openapi.yaml](openapi.yaml)**: OpenAPI 3.0 specification - Complete API contract với schemas, examples, và validation rules

## Base URL

```
Development: http://localhost:8000/api/v1
Production:  https://api.example.com/api/v1
```

## Authentication

**MVP**: No authentication required (single-user system)

**Future**: Laravel Sanctum tokens sẽ được thêm vào cho multi-user support
```
Authorization: Bearer {token}
```

## API Endpoints Summary

| Method | Endpoint | Description | Success Code |
|--------|----------|-------------|--------------|
| `GET` | `/todos` | Lấy danh sách todos | 200 |
| `POST` | `/todos` | Tạo todo mới | 201 |
| `GET` | `/todos/{id}` | Lấy một todo | 200 |
| `PUT` | `/todos/{id}` | Cập nhật tiêu đề | 200 |
| `PATCH` | `/todos/{id}/toggle` | Toggle completion | 200 |
| `DELETE` | `/todos/{id}` | Xóa todo | 200 |

## Request/Response Format

### Standard Success Response

```json
{
  "success": true,
  "data": { ... },
  "message": "Human-readable success message"
}
```

### Standard Error Response

```json
{
  "success": false,
  "message": "Human-readable error message",
  "errors": {
    "field_name": ["Error message 1", "Error message 2"]
  }
}
```

## HTTP Status Codes

| Code | Meaning | Usage |
|------|---------|-------|
| 200 | OK | Successful GET, PUT, PATCH, DELETE |
| 201 | Created | Successful POST (resource created) |
| 404 | Not Found | Todo ID doesn't exist |
| 422 | Unprocessable Entity | Validation error |
| 500 | Internal Server Error | Server error |

## Validation Rules

### Create/Update Todo

| Field | Rule | Error Message (VI) |
|-------|------|-------------------|
| `title` | required | "Tiêu đề là bắt buộc" |
| `title` | string | "Tiêu đề phải là chuỗi ký tự" |
| `title` | min:1 | "Tiêu đề không được để trống" |
| `title` | max:255 | "Tiêu đề phải có tối đa 255 ký tự" |

## Example Usage

### 1. Create Todo

**Request**:
```bash
POST /api/v1/todos
Content-Type: application/json

{
  "title": "Học Laravel 11"
}
```

**Response** (201 Created):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Học Laravel 11",
    "completed": false,
    "created_at": "2026-02-10T10:00:00Z",
    "updated_at": "2026-02-10T10:00:00Z"
  },
  "message": "Todo created successfully"
}
```

### 2. List Todos

**Request**:
```bash
GET /api/v1/todos
```

**Response** (200 OK):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Học Laravel 11",
      "completed": false,
      "created_at": "2026-02-10T10:00:00Z",
      "updated_at": "2026-02-10T10:00:00Z"
    },
    {
      "id": 2,
      "title": "Build todo app",
      "completed": true,
      "created_at": "2026-02-10T09:00:00Z",
      "updated_at": "2026-02-10T11:00:00Z"
    }
  ],
  "message": "Todos retrieved successfully"
}
```

### 3. Toggle Completion

**Request**:
```bash
PATCH /api/v1/todos/1/toggle
```

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Học Laravel 11",
    "completed": true,
    "created_at": "2026-02-10T10:00:00Z",
    "updated_at": "2026-02-10T12:00:00Z"
  },
  "message": "Todo completion toggled successfully"
}
```

### 4. Update Title

**Request**:
```bash
PUT /api/v1/todos/1
Content-Type: application/json

{
  "title": "Học Laravel 11 & Vue 3"
}
```

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Học Laravel 11 & Vue 3",
    "completed": true,
    "created_at": "2026-02-10T10:00:00Z",
    "updated_at": "2026-02-10T13:00:00Z"
  },
  "message": "Todo updated successfully"
}
```

### 5. Delete Todo

**Request**:
```bash
DELETE /api/v1/todos/1
```

**Response** (200 OK):
```json
{
  "success": true,
  "message": "Todo deleted successfully"
}
```

### 6. Validation Error

**Request**:
```bash
POST /api/v1/todos
Content-Type: application/json

{
  "title": ""
}
```

**Response** (422 Unprocessable Entity):
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "title": [
      "Tiêu đề là bắt buộc"
    ]
  }
}
```

### 7. Not Found Error

**Request**:
```bash
GET /api/v1/todos/999
```

**Response** (404 Not Found):
```json
{
  "success": false,
  "message": "Todo not found"
}
```

## Data Model

### Todo Entity

```typescript
interface Todo {
  id: number              // Unique identifier
  title: string           // Todo title (1-255 chars)
  completed: boolean      // Completion status
  created_at: string      // ISO 8601 timestamp
  updated_at: string      // ISO 8601 timestamp
}
```

## Frontend Integration

### Axios Example

```javascript
// resources/js/services/todoApi.js
import axios from 'axios'

const API_BASE = '/api/v1'

export const todoApi = {
  // Lấy tất cả todos
  async getAll() {
    const response = await axios.get(`${API_BASE}/todos`)
    return response.data.data
  },

  // Tạo todo mới
  async create(title) {
    const response = await axios.post(`${API_BASE}/todos`, { title })
    return response.data.data
  },

  // Toggle completion
  async toggle(id) {
    const response = await axios.patch(`${API_BASE}/todos/${id}/toggle`)
    return response.data.data
  },

  // Cập nhật title
  async update(id, title) {
    const response = await axios.put(`${API_BASE}/todos/${id}`, { title })
    return response.data.data
  },

  // Xóa todo
  async delete(id) {
    await axios.delete(`${API_BASE}/todos/${id}`)
  }
}
```

## Testing the API

### Manual Testing with cURL

```bash
# List todos
curl http://localhost:8000/api/v1/todos

# Create todo
curl -X POST http://localhost:8000/api/v1/todos \
  -H "Content-Type: application/json" \
  -d '{"title":"Test todo"}'

# Toggle completion
curl -X PATCH http://localhost:8000/api/v1/todos/1/toggle

# Update title
curl -X PUT http://localhost:8000/api/v1/todos/1 \
  -H "Content-Type: application/json" \
  -d '{"title":"Updated title"}'

# Delete todo
curl -X DELETE http://localhost:8000/api/v1/todos/1
```

### Automated Testing (Laravel)

```php
// tests/Feature/Api/V1/TodoTest.php
public function test_can_create_todo(): void
{
    $response = $this->postJson('/api/v1/todos', [
        'title' => 'Test todo'
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'data' => [
                'title' => 'Test todo',
                'completed' => false
            ]
        ]);
}
```

## Contract Validation

OpenAPI contract có thể validated bằng:

1. **Swagger Editor**: https://editor.swagger.io/ (import `openapi.yaml`)
2. **OpenAPI CLI**: 
   ```bash
   npm install -g @openapitools/openapi-generator-cli
   openapi-generator-cli validate -i openapi.yaml
   ```
3. **Laravel**: Use `spectator` package để validate API responses against contract

## Changelog

### Version 1.0.0 (2026-02-10)
- Initial API contract
- 6 endpoints: List, Create, Get, Update, Toggle, Delete
- Standard response format
- Vietnamese validation messages
- OpenAPI 3.0 specification

## Related Documents

- [spec.md](../spec.md) - Feature specification
- [data-model.md](../data-model.md) - Database schema và entity definitions
- [plan.md](../plan.md) - Implementation plan
- [research.md](../research.md) - Technology research và decisions
