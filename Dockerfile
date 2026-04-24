# Use official PHP with Apache
FROM php:8.2-apache

# Fix Apache MPM conflict — disable mpm_event, enable mpm_prefork, then enable rewrite
RUN a2dismod mpm_event && a2enmod mpm_prefork && a2enmod rewrite

# Copy all project files to Apache web root
COPY . /var/www/html/

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80
