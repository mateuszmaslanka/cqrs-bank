FROM php:8.4-cli-alpine
WORKDIR /app
COPY --from=composer:lts /usr/bin/composer /usr/local/bin/composer

RUN apk --no-cache add git zip \
    && docker-php-ext-install pdo pdo_mysql
