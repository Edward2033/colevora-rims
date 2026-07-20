#!/bin/bash

set -e

echo "Starting Colevora Restaurant ERP Container..."

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
until php artisan db:show 2>/dev/null; do
    echo "MySQL is unavailable - sleeping"
    sleep 2
done

echo "MySQL is ready!"

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Create storage link
echo "Creating storage link..."
php artisan storage:link --force

# Clear and cache configurations for production
echo "Optimizing application..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo "Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Colevora Restaurant ERP is ready!"
echo "Application running at http://localhost"

# Execute the main container command
exec "$@"
