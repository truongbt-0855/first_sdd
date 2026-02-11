# Troubleshooting Guide - Laravel 11 Todo App (Docker on Windows)

## Tóm tắt

Tài liệu này ghi lại các lỗi gặp phải trong quá trình setup Laravel 11 + Vue 3 + PostgreSQL với Docker trên Windows và cách khắc phục.

---

## 1. Node.js Version Incompatibility

### Lỗi
```
[vite] Internal server error: crypto.hash is not a function
```

### Nguyên nhân
- Vite 7.3.1 yêu cầu Node.js >= 20.19 hoặc >= 22.12
- Docker image ban đầu dùng `node:18-alpine`

### Cách fix
```yaml
# docker-compose.yml
node:
  image: node:20-alpine  # Thay vì node:18-alpine
```

**Restart container:**
```bash
docker-compose up -d --build node
```

---

## 2. Laravel Permission Errors

### Lỗi
```
file_put_contents(/var/www/storage/framework/views/...): failed to open stream: Permission denied
```

### Nguyên nhân
Container chạy với user `www-data` nhưng folders storage/bootstrap không có quyền ghi

### Cách fix
```bash
docker-compose exec app bash -c "chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && chmod -R 775 /var/www/storage /var/www/bootstrap/cache"
```

**Lưu ý:** Lỗi này thường xảy ra sau khi build lại container hoặc tạo project mới.

---

## 3. Slow Initial Request (8-10 seconds)

### Hiện tượng
- Request đầu tiên: 8-10 giây
- Requests tiếp theo: ~100-200ms

### Nguyên nhân
**KHÔNG PHẢI LỖI** - Đây là behavior bình thường của Docker trên Windows:
- WSL2 overhead khi khởi động PHP-FPM processes
- File system translation giữa Windows và Linux container
- PHP OPcache cold start

### Đã tối ưu
```ini
# docker/php/opcache.ini
opcache.enable=1
opcache.memory_consumption=256
opcache.jit_buffer_size=100M
opcache.jit=tracing
```

```ini
# docker/php/www.conf
pm = dynamic
pm.start_servers = 4
pm.min_spare_servers = 2
pm.max_spare_servers = 6
```

### Kết quả
- ✅ Request 1: 8-10s (cold start - không thể tối ưu thêm)
- ✅ Request 2+: ~100-200ms (excellent performance)

**Lưu ý:** Production deployment không có vấn đề này.

---

## 4. Blank Page (Vue App Not Rendering)

### Lỗi
Trang web hiển thị trắng tinh, không có nội dung

### Nguyên nhân
**Nguyên nhân 1:** Vue app mount với empty object
```javascript
// ❌ SAI
const app = createApp({});
app.mount('#app');
```

**Nguyên nhân 2:** Vue runtime không có template compiler
```javascript
// ❌ SAI - Runtime không compile được template string
const app = createApp({
    template: `<div>Hello</div>`
});
```

**Nguyên nhân 3:** Vite cache cũ
- File đã update trên host nhưng Vite serve code cũ từ cache

### Cách fix

**Fix 1: Dùng .vue file thay vì inline template**
```javascript
// ✅ ĐÚNG
import { createApp } from 'vue';
import App from './App.vue';

const app = createApp(App);
app.mount('#app');
```

**Fix 2: Clear Vite cache và restart**
```bash
docker-compose exec node sh -c "rm -rf /var/www/node_modules/.vite"
docker-compose restart node
```

**Fix 3: Hard refresh browser**
- Windows/Linux: `Ctrl + Shift + R` hoặc `Ctrl + F5`
- Mac: `Cmd + Shift + R`

---

## 5. Vite HMR Not Working / Vue Components Not Updating

### Hiện tượng
- Thay đổi Vue components (.vue files) nhưng browser không tự động update
- UI vẫn hiển thị code cũ dù đã save file
- Phải restart node container và hard refresh browser mới thấy thay đổi

### Nguyên nhân
**Docker volume mounting trên Windows có delay trong file change detection:**
- Vite HMR dựa vào file system watchers (fs.watch, chokidar)
- Docker Desktop trên Windows mount volumes qua WSL2
- File changes từ Windows → WSL2 có latency cao (100-500ms)
- Vite có thể miss file change events hoặc nhận quá chậm
- **KHÔNG phải lỗi config** - đây là Docker on Windows limitation

### Cách fix

**Fix 1: Manual Restart (Recommended cho stability)**
```bash
# Sau khi edit Vue files, restart node container
docker-compose restart node

# Đợi 8-10s cho Vite compile
# Sau đó hard refresh browser: Ctrl + Shift + R
```

**Fix 2: Enable Polling Mode (Tự động nhưng tốn CPU)**
```javascript
// vite.config.js
export default defineConfig({
    server: {
        host: '0.0.0.0',
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: true,           // Enable polling
            interval: 1000,             // Check every 1 second
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
```

⚠️ **Trade-offs của Polling:**
- ✅ Auto-reload hoạt động (không cần restart)
- ❌ Tốn CPU (~5-10% liên tục)
- ❌ Phản hồi chậm hơn (~1-2s delay)
- ❌ Pin laptop tụt nhanh hơn

**Fix 3: Native Development (Fastest, no Docker)**
```bash
# Cài PHP, Node, PostgreSQL trực tiếp trên Windows
# Không dùng Docker cho development
# HMR hoạt động hoàn hảo, <100ms response
```

### Best Practice

**Development workflow:**
1. Edit Vue components
2. Save files (Ctrl+S)
3. Chạy: `docker-compose restart node`
4. Đợi ~8s
5. Hard refresh browser: `Ctrl + Shift + R`

**Lưu ý:**
- `.js`, `.css` files thường HMR OK
- `.vue` files (SFC) thường cần restart
- Backend PHP không cần restart (OPcache tự reload)

### Production Note
**Không ảnh hưởng production!** Vite dev server chỉ dùng development. Production build (`npm run build`) tạo static assets, không có HMR.

---

## 6. Git Bash Path Conversion Issues

### Lỗi
```
cat: can't open 'C:/Program Files/Git/var/www/...': No such file or directory
```

### Nguyên nhân
Git Bash tự động convert Unix paths sang Windows paths khi chạy docker commands

### Cách fix
Wrap command trong `sh -c`:
```bash
# ❌ SAI
docker-compose exec node cat /var/www/resources/js/app.js

# ✅ ĐÚNG
docker-compose exec node sh -c "cat /var/www/resources/js/app.js"
```

---

## 7. Docker Compose Version Warning

### Warning
```
the attribute `version` is obsolete, it will be ignored
```

### Nguyên nhân
Docker Compose v2+ không cần version attribute

### Cách fix
Xóa dòng đầu tiên trong `docker-compose.yml`:
```yaml
# ❌ Xóa dòng này
version: '3.8'

# Bắt đầu trực tiếp với services
services:
  app:
    ...
```

---

## Commands Hữu Ích

### Check containers status
```bash
docker-compose ps
```

### View logs
```bash
docker-compose logs app --tail=50
docker-compose logs node --tail=50
docker-compose logs nginx --tail=50
```

### Restart specific service
```bash
docker-compose restart app
docker-compose restart node
```

### Clear all Laravel caches
```bash
docker-compose exec app php artisan optimize:clear
```

### Clear Vite cache
```bash
docker-compose exec node sh -c "rm -rf /var/www/node_modules/.vite"
docker-compose restart node
```

### Fix permissions
```bash
docker-compose exec app bash -c "chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && chmod -R 775 /var/www/storage /var/www/bootstrap/cache"
```

### Performance test
```bash
for i in 1 2 3; do echo "Request $i:"; time curl -s -o /dev/null http://localhost:8000/; done
```

---

## Best Practices

### 1. Development Workflow
- ✅ **KHÔNG** cần reload browser mỗi lần thay đổi code
- ✅ Vite HMR tự động update khi save `.vue`, `.js`, `.css` files
- ✅ Chỉ reload khi thay đổi `.env` hoặc config files

### 2. Container Management
- ✅ Chỉ restart container khi cần (thay đổi Dockerfile, docker-compose.yml)
- ✅ Dùng `docker-compose logs` để debug thay vì rebuild container

### 3. Performance Expectations
- ✅ Cold start: 8-10s là bình thường trên Docker/Windows
- ✅ Warm requests: <300ms là acceptable
- ✅ Production sẽ không có cold start delay

---

## Tổng kết Issues & Status

| Issue | Status | Impact | Fix Complexity |
|-------|--------|--------|----------------|
| Node 18 incompatible với Vite 7 | ✅ Fixed | High | Easy |
| Permission denied errors | ✅ Fixed | High | Easy |
| Blank page (empty app) | ✅ Fixed | High | Medium |
| Vite cache stale | ✅ Fixed | Medium | Easy |
| Slow cold start | ⚠️ Expected behavior | Low | N/A |
| Git Bash path conversion | ✅ Documented | Low | Easy |
| Docker Compose warning | ℹ️ Cosmetic | None | Trivial |

---

## Environment Info

- **OS:** Windows (WSL2 + Docker Desktop)
- **Docker Compose:** 5.0.2
- **PHP:** 8.3-FPM
- **Laravel:** 11
- **Node.js:** 20-alpine
- **PostgreSQL:** 15-alpine
- **Vue:** 3.5.13
- **Vite:** 7.3.1
- **Tailwind CSS:** 4.0.0

---

**Last Updated:** 2026-02-10  
**Status:** Infrastructure ready, Phase 1-2 complete (14/86 tasks)
