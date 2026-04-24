# PHP CLI - No Apache, no MPM conflicts
FROM php:8.2-cli

WORKDIR /app

# Copy all project files
COPY . .

# Railway injects PORT env var — shell form expands it properly
CMD php -S 0.0.0.0:$PORT -t /app
