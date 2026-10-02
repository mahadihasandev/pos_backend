FROM dunglas/frankenphp:latest-php8.3

# Install system dependencies & PostgreSQL development libraries
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    curl \
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
    composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts; \
    fi

# Ensure entrypoint is executable and has unix line endings
RUN sed -i 's/\r$//' /app/entrypoint.sh && chmod +x /app/entrypoint.sh

ENV PORT=8000
EXPOSE 8000

ENTRYPOINT ["/app/entrypoint.sh"]
