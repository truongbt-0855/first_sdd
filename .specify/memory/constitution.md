# Project Constitution

## Technical Stack

### Backend
- **Framework**: Laravel 11 (PHP 8.3+)
- **Database**: PostgreSQL 15+
- **API**: RESTful API với JSON response
- **Authentication**: Laravel Sanctum (cho SPA)
- **Validation**: Laravel Form Requests
- **Testing**: PHPUnit, Laravel HTTP Tests

### Frontend
- **Framework**: Vue 3 với Composition API (`<script setup>`)
- **Build Tool**: Vite 5+
- **Styling**: Tailwind CSS 3+
  - Sử dụng utility-first approach
  - Cấu hình theme tùy chỉnh trong `tailwind.config.js`
  - Responsive design với mobile-first
  - Dark mode support (nếu cần)
- **Component Structure**: 
  - Đặt tại `resources/js/Components/`
  - Đặt tên theo PascalCase (ví dụ: `TodoList.vue`, `TodoItem.vue`)
- **State Management**: Pinia (nếu cần quản lý state phức tạp)
- **HTTP Client**: Axios hoặc Fetch API
- **Type Safety**: TypeScript (khuyến nghị) hoặc JSDoc

### Development Tools
- **Code Style**: Laravel Pint (PHP), ESLint + Prettier (JavaScript/Vue)
- **Version Control**: Git
- **Package Manager**: Composer (PHP), npm/pnpm (JavaScript)

## Nguyên Tắc Kiến Trúc

### 1. API-First Architecture
- Backend và Frontend giao tiếp **thuần túy** qua REST API
- Mọi endpoint phải có prefix `/api/v1/`
- Response luôn trả về JSON với structure nhất quán:
  ```json
  {
    "success": true/false,
    "data": {},
    "message": "Human-readable message",
    "errors": {}
  }
  ```
- Sử dụng đúng HTTP status codes (200, 201, 400, 404, 422, 500...)

### 2. Single Responsibility Principle
- Mỗi Class/Component chỉ làm **một việc duy nhất**
- Controller chỉ làm routing, validation và gọi Services
- Business logic đặt trong Service classes
- Component Vue không quá 200 dòng code

### 3. Type Safety
- **PHP**: Bắt buộc `declare(strict_types=1);` ở đầu mỗi file
- **PHP**: Type hints cho tất cả parameters và return types
- **Vue**: Sử dụng TypeScript hoặc JSDoc để định nghĩa props types

## Quy Tắc Frontend với Tailwind CSS

### Nguyên Tắc Utility-First
- **BẮT BUỘC** sử dụng Tailwind utility classes cho styling
- **KHÔNG** viết custom CSS trừ khi thực sự cần thiết và không thể làm bằng Tailwind
- **KHÔNG** tạo duplicate utilities đã có sẵn trong Tailwind

### Cấu Hình Theme
- Colors, spacing, fonts phải được định nghĩa trong `tailwind.config.js`:
  ```js
  theme: {
    extend: {
      colors: {
        primary: {...},
        secondary: {...}
      }
    }
  }
  ```
- Sử dụng CSS variables cho dynamic colors nếu cần

### Responsive Design
- Thiết kế **mobile-first** (base styles cho mobile)
- Sử dụng breakpoint modifiers: `sm:`, `md:`, `lg:`, `xl:`, `2xl:`
- Ví dụ: `class="text-sm md:text-base lg:text-lg"`

### Component Styling Best Practices
- Sử dụng `@apply` directive **tiết kiệm**, chỉ cho các pattern lặp lại nhiều
- Ưu tiên composition components hơn là `@apply`
- Tránh inline styles, luôn dùng Tailwind classes

## Formatting & Standards

### PHP (Laravel)
- Tuân theo chuẩn **PSR-12**
- Chạy `php artisan pint` trước khi commit
- File structure:
  ```
  app/
  ├── Http/Controllers/     # Thin controllers
  ├── Services/            # Business logic
  ├── Models/              # Eloquent models
  └── Http/Requests/       # Form validation
  ```

### Vue Components
- Đặt trong `resources/js/Components/` với cấu trúc:
  ```
  Components/
  ├── Todo/
  │   ├── TodoList.vue
  │   ├── TodoItem.vue
  │   └── TodoForm.vue
  └── Shared/
      ├── Button.vue
      └── Modal.vue
  ```
- Sử dụng Composition API với `<script setup>`
- Props validation bắt buộc

### Testing Requirements
- **Backend**: Feature tests cho mọi API endpoint
- **Backend**: Unit tests cho Services có logic phức tạp
- Tất cả tests phải pass trước khi merge vào main branch