FROM php:8.3-fpm-alpine

# libs p/ extensões
RUN apk add --no-cache bash git unzip icu-dev libpq-dev libzip-dev oniguruma-dev $PHPIZE_DEPS

# extensões essenciais
RUN docker-php-ext-install intl pcntl bcmath opcache pdo pdo_pgsql zip

# redis opcional
RUN pecl install redis && docker-php-ext-enable redis

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# otimizações básicas
RUN echo "memory_limit=${PHP_MEMORY_LIMIT:-512M}" > /usr/local/etc/php/conf.d/memory.ini \
  && printf "opcache.enable=1\nopcache.jit_buffer_size=64M\nopcache.jit=tracing\n" > /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www/html
