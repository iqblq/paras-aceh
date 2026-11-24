FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    libzip-dev \
    && docker-php-ext-install pdo_sqlite zip bcmath

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure Apache DocumentRoot to Laravel /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Copy .env.example to .env if not exists
RUN cp -n .env.example .env || true

# Install composer production packages
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set proper ownership and permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Copy startup script
COPY render-start.sh /usr/local/bin/render-start.sh
RUN tr -d '\r' < /usr/local/bin/render-start.sh > /usr/local/bin/render-start-clean.sh && \
    mv /usr/local/bin/render-start-clean.sh /usr/local/bin/render-start.sh && \
    chmod +x /usr/local/bin/render-start.sh

ENV PORT=10000
EXPOSE 10000

CMD ["/usr/local/bin/render-start.sh"]
