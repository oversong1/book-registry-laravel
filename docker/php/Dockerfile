FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    libzip-dev libcurl4-openssl-dev libxml2-dev libonig-dev unzip git nodejs npm \
    && docker-php-ext-install pdo pdo_mysql mbstring bcmath curl xml zip \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY vhost.conf /etc/apache2/sites-available/000-default.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]