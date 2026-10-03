#!/usr/bin/env sh
set -e

echo "==> Preparing production deployment on Render..."

# Create storage directory structure if missing
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Run database migrations automatically in production
if [ "$RUN_MIGRATIONS" = "true" ] || [ "$APP_ENV" = "production" ]; then
    echo "==> Running database migrations..."
    php artisan migrate --force --isolated || echo "Migration warning: could not connect or already up to date."
fi

# Cache configuration, routes, and views for maximum performance
echo "==> Optimizing caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

PORT=${PORT:-8000}
echo "==> Starting Laravel Octane (FrankenPHP) on port $PORT..."

# Ensure frankenphp binary has no restricted capabilities causing EPERM on container runtimes
if command -v setcap >/dev/null 2>&1; then
    setcap -r /usr/local/bin/frankenphp 2>/dev/null || true
fi
chmod +x /usr/local/bin/frankenphp 2>/dev/null || true

# Start Octane with native TLS if OCTANE_HTTPS is enabled, otherwise on assigned PORT
if [ "$OCTANE_HTTPS" = "true" ] || [ "$ENABLE_TLS" = "true" ]; then
    HTTPS_PORT=${HTTPS_PORT:-443}
    echo "==> Starting Laravel Octane (FrankenPHP) with native TLS on port $HTTPS_PORT..."
    exec php artisan octane:start --server=frankenphp --host=0.0.0.0 --port="$HTTPS_PORT" --https --http-redirect --workers=4
else
    echo "==> Starting Laravel Octane (FrankenPHP) on port $PORT..."
    exec php artisan octane:start --server=frankenphp --host=0.0.0.0 --port="$PORT" --workers=4
fi
