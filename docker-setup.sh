#!/bin/bash

# Docker Setup Script for Laravel 11 Todo Application
# This script initializes the Laravel project inside Docker containers

set -e

echo "🐳 Starting Docker Setup for Laravel 11 Todo App..."

# Step 1: Build and start containers
echo "📦 Building Docker containers..."
docker-compose up -d --build

# Step 2: Wait for database to be ready
echo "⏳ Waiting for PostgreSQL to be ready..."
sleep 10

# Step 3: Create Laravel project (if not exists)
if [ ! -f "composer.json" ]; then
    echo "🚀 Creating Laravel 11 project..."
    docker-compose exec app composer create-project laravel/laravel . --prefer-dist
else
    echo "✅ Laravel project already exists, installing dependencies..."
    docker-compose exec app composer install
fi

# Step 4: Set up environment file
if [ ! -f ".env" ]; then
    echo "📝 Creating .env file..."
    docker-compose exec app cp .env.example .env
fi

# Step 5: Generate application key
echo "🔑 Generating application key..."
docker-compose exec app php artisan key:generate

# Step 6: Configure database in .env
echo "🗄️  Configuring database connection..."
docker-compose exec app sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=pgsql/' .env
docker-compose exec app sed -i 's/DB_HOST=.*/DB_HOST=db/' .env
docker-compose exec app sed -i 's/DB_PORT=.*/DB_PORT=5432/' .env
docker-compose exec app sed -i 's/DB_DATABASE=.*/DB_DATABASE=laravel_todo/' .env
docker-compose exec app sed -i 's/DB_USERNAME=.*/DB_USERNAME=laravel/' .env
docker-compose exec app sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=secret/' .env

# Step 7: Set proper permissions
echo "🔒 Setting permissions..."
docker-compose exec app chown -R www-data:www-data /var/www
docker-compose exec app chmod -R 755 /var/www/storage
docker-compose exec app chmod -R 755 /var/www/bootstrap/cache

# Step 8: Install frontend dependencies
echo "📦 Installing Node.js dependencies..."
docker-compose exec node npm install

# Step 9: Run migrations
echo "🗃️  Running database migrations..."
docker-compose exec app php artisan migrate --force

echo ""
echo "✅ Docker setup complete!"
echo ""
echo "🌐 Application URLs:"
echo "   - Laravel: http://localhost:8000"
echo "   - Vite Dev Server: http://localhost:5173"
echo "   - PostgreSQL: localhost:5432"
echo ""
echo "📋 Useful commands:"
echo "   docker-compose exec app php artisan [command]  - Run Artisan commands"
echo "   docker-compose exec app composer [command]     - Run Composer commands"
echo "   docker-compose exec node npm [command]         - Run NPM commands"
echo "   docker-compose logs -f                         - View logs"
echo "   docker-compose down                            - Stop containers"
echo ""
echo "🚀 Ready to start implementing tasks from specs/001-todo-system-vn/tasks.md"
