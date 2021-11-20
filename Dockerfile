FROM php:7.4-fpm

# Set working directory
WORKDIR /var/www/html/

RUN apt-get update && apt-get install -y git curl libzip-dev

# Install PHP dependencies
RUN docker-php-ext-install pdo pdo_mysql zip

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory permissions
RUN chown -R www:www /var/www/html

EXPOSE 9000

CMD ["php-fpm"]