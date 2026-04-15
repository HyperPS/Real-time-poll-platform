FROM php:8.2-apache

# Install PHP extensions needed for the app
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite headers deflate expires

# Set the document root to /var/www/html/public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Update Apache config to use our document root
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Allow .htaccess overrides
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Copy application files
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Create logs directory
RUN mkdir -p /var/www/html/logs && chown www-data:www-data /var/www/html/logs

EXPOSE 80

CMD ["apache2-foreground"]
