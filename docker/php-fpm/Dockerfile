FROM php:8.4-fpm-alpine
WORKDIR /app
COPY --from=composer:lts /usr/bin/composer /usr/local/bin/composer

RUN apk --no-cache add git zip
