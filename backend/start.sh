#!/bin/bash

# Set proper permissions for Laravel storage and cache directories
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Create sessions directory with proper permissions
mkdir -p /var/lib/php/sessions && chown -R www-data:www-data /var/lib/php/sessions

# Generate application key if not already set
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php /var/www/html/artisan key:generate --force
fi

# Run database migrations
echo "Running database migrations..."
php /var/www/html/artisan migrate --force

# Start supervisord
echo "Starting supervisord..."
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
