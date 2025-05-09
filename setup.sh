#!/bin/bash

# Print each command that is executed
set -x

# Stop on error
set -e

echo "🚀 Starting project setup..."

# Check if .env file exists, if not copy from example
if [ ! -f .env ]; then
    echo "📄 Creating .env file from .env.example..."
    cp .env.example .env

    # Generate app key if running in Laravel context
    if grep -q "APP_KEY=" .env; then
        echo "🔑 Generating application key..."
        docker compose run --rm laravel.test php artisan key:generate
    fi
else
    echo "✅ .env file already exists."
fi

# Load environment variables
echo "📥 Loading environment variables..."
export $(grep -v '^#' .env | xargs)

# Start containers in detached mode if they're not running
if ! docker compose ps | grep -q "laravel.test" || ! docker compose ps | grep -q "Up"; then
    echo "🐳 Starting Docker containers..."
    docker compose up -d
fi

# Install PHP dependencies with Composer
echo "📦 Installing PHP dependencies..."
docker compose run --rm composer install --ignore-platform-reqs --no-interaction --prefer-dist --no-scripts

# Install Node.js dependencies
echo "📦 Installing Node.js dependencies..."
docker compose run --rm node npm install

# Build frontend assets
echo "🔨 Building frontend assets..."
docker compose run --rm node npm run build

echo "✨ Setup complete! Your application is ready to run."
echo "📝 You can access your application at: http://localhost:${APP_PORT:-80}"
