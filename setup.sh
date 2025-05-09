#!/bin/bash

echo "Laravel Project Setup Script"
echo "This script will set up the project with Docker."

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "Error: Docker is not installed."
    echo "Please install Docker before running this script."
    exit 1
fi

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env

    # Generate application key
    docker compose run --rm laravel.test php artisan key:generate
else
    echo ".env file already exists."
fi

# Start the containers
echo "Starting Docker containers..."
docker compose up -d

# Install Composer dependencies
echo "Installing Composer dependencies..."
docker compose exec laravel.test composer install

# Install NPM dependencies and build assets
echo "Installing NPM dependencies..."
docker compose exec laravel.test npm install
echo "Building frontend assets..."
docker compose exec laravel.test npm run build

# Run migrations
echo "Running database migrations..."
docker compose exec laravel.test php artisan migrate

echo "Setup completed successfully!"
echo "Your application is now running at http://localhost"
echo ""
echo "Common commands:"
echo "- Start all services: ./vendor/bin/sail up -d"
echo "- Stop all services: ./vendor/bin/sail down"
echo "- Run Artisan commands: ./vendor/bin/sail artisan [command]"
echo "- Run Horizon: ./vendor/bin/sail artisan horizon"
echo "- Run Queue worker: ./vendor/bin/sail artisan queue:work"
