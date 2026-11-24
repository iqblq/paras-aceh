#!/bin/bash
set -e

PORT="${PORT:-10000}"

# Adjust Apache listening port dynamically for Render
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/" /etc/apache2/sites-available/000-default.conf

# Ensure database directory and sqlite file permissions
touch /var/www/html/database/database.sqlite || true
chown -R www-data:www-data /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache

# Optimize Laravel
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "Starting Apache on port ${PORT}..."
exec apache2-foreground
