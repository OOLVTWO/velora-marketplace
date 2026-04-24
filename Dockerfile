# Use PHP CLI — avoids all Apache MPM conflicts on Railway
FROM php:8.2-cli

WORKDIR /var/www/html

# Copy all project files
COPY . .

# Railway injects $PORT at runtime — use PHP built-in server
EXPOSE 8080
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t /var/www/html"]
