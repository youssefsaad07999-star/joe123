#!/bin/sh

# 1. Run database migrations automatically without prompting
php artisan migrate --force

# 2. Clear and optimize Laravel caches for Wasmer's filesystem layout
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Start the built-in PHP server (pointing to the public front controller)
exec php -S 0.0.0.0:$PORT -t /app/public