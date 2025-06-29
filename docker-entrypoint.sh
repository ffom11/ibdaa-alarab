#!/bin/bash
set -e

# Create .env file from environment variables if it doesn't exist
if [ ! -f /var/www/html/.env ]; then
    echo "Creating .env file from environment variables..."
    cat > /var/www/html/.env <<EOL
# إعدادات البيئة
APP_ENV=${APP_ENV:-production}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}

# إعدادات قاعدة البيانات
DB_HOST=${DB_HOST:-db}
DB_NAME=${DB_NAME:-ibdaa_alarab}
DB_USER=${DB_USER:-user}
DB_PASS=${DB_PASS:-password}
DB_CHARSET=${DB_CHARSET:-utf8mb4}

# إعدادات البريد الإلكتروني
MAIL_HOST=${MAIL_HOST:-smtp.mailtrap.io}
MAIL_PORT=${MAIL_PORT:-2525}
MAIL_USERNAME=${MAIL_USERNAME:-}
MAIL_PASSWORD=${MAIL_PASSWORD:-}
MAIL_FROM_EMAIL=${MAIL_FROM_EMAIL:-noreply@ibdaa-alarab.com}
MAIL_FROM_NAME="${MAIL_FROM_NAME:-إبداع العرب}"

# مفاتيح API
RECAPTCHA_SITE_KEY=${RECAPTCHA_SITE_KEY:-}
RECAPTCHA_SECRET_KEY=${RECAPTCHA_SECRET_KEY:-}
GOOGLE_MAPS_API_KEY=${GOOGLE_MAPS_API_KEY:-}

# إعدادات الجلسة
SESSION_NAME=ebdaa_sess
SESSION_LIFETIME=7200
SESSION_SECURE=false
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=Lax

# إعدادات التطبيق
SITE_NAME="${SITE_NAME:-إبداع العرب}"
ADMIN_EMAIL=${ADMIN_EMAIL:-admin@ibdaa-alarab.com}
EOL
else
    echo ".env file already exists, skipping creation."
fi

# Create storage and cache directories if they don't exist
mkdir -p /var/www/html/storage/framework/{sessions,views,cache}
mkdir -p /var/www/html/bootstrap/cache

# Set file permissions
chown -R www-data:www-data /var/www/html
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Run the original command (Apache in this case)
exec "$@"
