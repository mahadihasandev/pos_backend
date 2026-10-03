FROM dunglas/frankenphp:1-php8.4-bookworm

# Install system dependencies, PostgreSQL dev libraries & libcap
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    libcap2-bin \
    && install-php-extensions \
    pdo_mysql \
    mysqli \
    pdo_pgsql \
    pgsql \
    redis \
    opcache \
    pcntl \
    bcmath \
    zip \
    && setcap -r /usr/local/bin/frankenphp || true \
    && chmod +x /usr/local/bin/frankenphp \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configure production OPcache
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.interned_strings_buffer=64" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.max_accelerated_files=30000" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

WORKDIR /app

# Copy application files
COPY . /app

# Ensure storage directories exist with write permissions
RUN mkdir -p /app/storage/framework/cache/data \
    /app/storage/framework/sessions \
    /app/storage/framework/views \
    /app/storage/logs \
    /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Install composer production dependencies if composer.json exists
RUN if [ -f composer.json ]; then \
    composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts --ignore-platform-req=php+; \
    fi

# Ensure entrypoint is executable and has unix line endings
RUN sed -i 's/\r$//' /app/entrypoint.sh && chmod +x /app/entrypoint.sh

ENV PORT=8000
EXPOSE 8000 443 80

ENTRYPOINT ["/app/entrypoint.sh"]
