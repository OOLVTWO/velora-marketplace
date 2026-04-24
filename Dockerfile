# PHP CLI - No Apache, no MPM conflicts
FROM php:8.2-cli

WORKDIR /app

# Copy all project files
COPY . .

# PORT=8080 is set in Railway variables — hardcode to avoid shell expansion issues
CMD ["php", "-S", "0.0.0.0:8080", "-t", "/app"]
