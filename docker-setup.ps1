# Docker Setup Script for Laravel 11 Todo Application (PowerShell)
# This script initializes the Laravel project inside Docker containers

Write-Host "🐳 Starting Docker Setup for Laravel 11 Todo App..." -ForegroundColor Cyan

# Step 1: Build and start containers
Write-Host "📦 Building Docker containers..." -ForegroundColor Yellow
docker-compose up -d --build

# Step 2: Wait for database to be ready
Write-Host "⏳ Waiting for PostgreSQL to be ready..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Step 3: Create Laravel project (if not exists)
if (-not (Test-Path "composer.json")) {
    Write-Host "🚀 Creating Laravel 11 project..." -ForegroundColor Yellow
    docker-compose exec app composer create-project laravel/laravel . --prefer-dist
} else {
    Write-Host "✅ Laravel project already exists, installing dependencies..." -ForegroundColor Green
    docker-compose exec app composer install
}

# Step 4: Set up environment file
if (-not (Test-Path ".env")) {
    Write-Host "📝 Creating .env file..." -ForegroundColor Yellow
    docker-compose exec app cp .env.example .env
}

# Step 5: Generate application key
Write-Host "🔑 Generating application key..." -ForegroundColor Yellow
docker-compose exec app php artisan key:generate

# Step 6: Configure database in .env manually (PowerShell version)
Write-Host "🗄️  Configuring database connection..." -ForegroundColor Yellow
docker-compose exec app bash -c "sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=pgsql/' .env"
docker-compose exec app bash -c "sed -i 's/DB_HOST=.*/DB_HOST=db/' .env"
docker-compose exec app bash -c "sed -i 's/DB_PORT=.*/DB_PORT=5432/' .env"
docker-compose exec app bash -c "sed -i 's/DB_DATABASE=.*/DB_DATABASE=laravel_todo/' .env"
docker-compose exec app bash -c "sed -i 's/DB_USERNAME=.*/DB_USERNAME=laravel/' .env"
docker-compose exec app bash -c "sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=secret/' .env"

# Step 7: Set proper permissions
Write-Host "🔒 Setting permissions..." -ForegroundColor Yellow
docker-compose exec app chown -R www-data:www-data /var/www
docker-compose exec app chmod -R 755 /var/www/storage
docker-compose exec app chmod -R 755 /var/www/bootstrap/cache

# Step 8: Install frontend dependencies
Write-Host "📦 Installing Node.js dependencies..." -ForegroundColor Yellow
docker-compose exec node npm install

# Step 9: Run migrations
Write-Host "🗃️  Running database migrations..." -ForegroundColor Yellow
docker-compose exec app php artisan migrate --force

Write-Host ""
Write-Host "✅ Docker setup complete!" -ForegroundColor Green
Write-Host ""
Write-Host "🌐 Application URLs:" -ForegroundColor Cyan
Write-Host "   - Laravel: http://localhost:8000"
Write-Host "   - Vite Dev Server: http://localhost:5173"
Write-Host "   - PostgreSQL: localhost:5432"
Write-Host ""
Write-Host "📋 Useful commands:" -ForegroundColor Cyan
Write-Host "   docker-compose exec app php artisan [command]  - Run Artisan commands"
Write-Host "   docker-compose exec app composer [command]     - Run Composer commands"
Write-Host "   docker-compose exec node npm [command]         - Run NPM commands"
Write-Host "   docker-compose logs -f                         - View logs"
Write-Host "   docker-compose down                            - Stop containers"
Write-Host ""
Write-Host "🚀 Ready to start implementing tasks from specs/001-todo-system-vn/tasks.md" -ForegroundColor Green
