# POS System - Installation Guide

## System Requirements

### Server Requirements
- **Operating System**: Linux (Ubuntu 22.04+ recommended) or macOS
- **PHP**: 8.3 or higher
- **Database**: MySQL 8.0+ or MariaDB 10.6+
- **Cache/Queue**: Redis 6.0+
- **Web Server**: Nginx 1.18+ or Apache 2.4+
- **Node.js**: 18.x or higher (for asset compilation)
- **Composer**: 2.x

### PHP Extensions Required
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Redis (phpredis)
- GD or Imagick
- Zip
- Curl

## Installation Steps

### 1. Clone the Repository
```bash
git clone https://github.com/your-repo/pos-system.git
cd pos-system
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install JavaScript Dependencies
```bash
npm install
```

### 4. Environment Configuration
```bash
# Copy the environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Configure Environment Variables

Edit `.env` file and set the following:

#### Database Configuration
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_system
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

#### Redis Configuration
```env
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

#### Laravel Nova License
```env
NOVA_LICENSE_KEY=your_nova_license_key_here
```

#### Stripe Configuration
```env
STRIPE_KEY=pk_test_your_publishable_key
STRIPE_SECRET=sk_test_your_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret
```

#### Mail Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourpos.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 6. Database Setup
```bash
# Run migrations
php artisan migrate

# Seed the database with initial data
php artisan db:seed
```

### 7. Storage Setup
```bash
# Create symbolic link for storage
php artisan storage:link
```

### 8. Build Frontend Assets
```bash
# For development
npm run dev

# For production
npm run build
```

### 9. Queue Worker Setup

#### Option 1: Using Supervisor (Recommended for Production)
Create supervisor config file: `/etc/supervisor/conf.d/pos-worker.conf`

```ini
[program:pos-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/pos-system/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/path/to/pos-system/storage/logs/worker.log
stopwaitsecs=3600
```

Then:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start pos-worker:*
```

#### Option 2: Development (using artisan)
```bash
php artisan queue:work redis --tries=3
```

### 10. Schedule Setup
Add to crontab:
```bash
crontab -e
```

Add this line:
```
* * * * * cd /path/to/pos-system && php artisan schedule:run >> /dev/null 2>&1
```

### 11. File Permissions
```bash
# Set proper permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## Web Server Configuration

### Nginx Configuration
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/pos-system/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Apache Configuration
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    ServerAdmin admin@your-domain.com
    DocumentRoot /path/to/pos-system/public

    <Directory /path/to/pos-system/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/pos-error.log
    CustomLog ${APACHE_LOG_DIR}/pos-access.log combined
</VirtualHost>
```

## Post-Installation

### 1. Create Admin User
```bash
php artisan tinker
```

Then in tinker:
```php
$user = App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@yourpos.com',
    'password' => bcrypt('your-secure-password'),
]);

$user->assignRole('Super Admin');
```

### 2. Access the Application

- **Admin Panel**: http://your-domain.com/admin
- **POS Interface**: http://your-domain.com/pos
- **API**: http://your-domain.com/api

### 3. Initial Configuration

1. Log in to Nova admin panel
2. Configure store settings
3. Add tax rates
4. Add payment methods
5. Create product categories
6. Add products

## SSL Configuration (Production)

Using Let's Encrypt with Certbot:
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

## Troubleshooting

### Permission Issues
```bash
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Clear Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Redis Connection Issues
```bash
# Check if Redis is running
redis-cli ping

# Should return: PONG
```

### Queue Not Processing
```bash
# Check queue worker status
sudo supervisorctl status pos-worker

# Restart queue worker
sudo supervisorctl restart pos-worker:*
```

## Maintenance Mode

### Enable Maintenance Mode
```bash
php artisan down --secret="your-secret-token"
```

Access the site during maintenance: `http://your-domain.com/your-secret-token`

### Disable Maintenance Mode
```bash
php artisan up
```

## Backup Strategy

### Database Backup
```bash
# Create backup
mysqldump -u username -p pos_system > backup_$(date +%Y%m%d).sql

# Restore backup
mysql -u username -p pos_system < backup_20250101.sql
```

### Full Backup
```bash
# Backup files and database
tar -czf pos-backup-$(date +%Y%m%d).tar.gz \
    /path/to/pos-system \
    --exclude=node_modules \
    --exclude=vendor \
    --exclude=storage/logs
```

## Updating the System

```bash
# Pull latest changes
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Run migrations
php artisan migrate --force

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue workers
sudo supervisorctl restart pos-worker:*
```

## Support

For installation issues:
- Check logs: `storage/logs/laravel.log`
- Review documentation: `docs/`
- Contact support: support@yourpos.com
