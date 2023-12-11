FROM composer:latest

RUN docker-php-ext-install mysqli pdo pdo_mysql

WORKDIR /app
COPY . .

RUN composer update
