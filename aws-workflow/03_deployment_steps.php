<?php

/**
 * ============================================================================
 *          ACTUAL DEPLOYMENT STEPS — REAL COMMANDS KE SATH
 * ============================================================================
 *
 * Ye file "01" aur "02" ke baad aati hai — ab actual deployment commands
 * step-by-step. Assume kar rahe hain: AWS infra (VPC, RDS, EC2, S3) already
 * bana hua hai (dekho aws_advanced/ folder har service ki detail ke liye).
 */


// =============================================================================
// STEP 1 — SERVER PAR PEHLI BAAR SETUP (sirf EK BAAR karna hai)
// =============================================================================

/*
 * SSH se server par connect ho:
 *   ssh -i your-key.pem ubuntu@your-server-ip
 *
 * Zaroori software install karo:
 *   sudo apt update && sudo apt upgrade -y
 *   sudo apt install -y nginx mysql-client redis-tools
 *   sudo apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-redis \
 *        php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath
 *
 *   curl -sS https://getcomposer.org/installer | php
 *   sudo mv composer.phar /usr/local/bin/composer
 *
 *   curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
 *   sudo apt install -y nodejs
 */


// =============================================================================
// STEP 2 — PROJECT CLONE KARNA
// =============================================================================

/*
 *   cd /var/www
 *   sudo git clone https://github.com/your-org/your-project.git
 *   cd your-project
 *   sudo chown -R www-data:www-data /var/www/your-project
 */


// =============================================================================
// STEP 3 — DEPENDENCIES INSTALL KARNA
// =============================================================================

/*
 *   composer install --no-dev --optimize-autoloader
 *
 *   npm install
 *   npm run build
 *
 * --no-dev          → Development packages (PHPUnit, etc.) skip ho jate hain
 * --optimize-autoloader → Autoloading FASTER ho jati hai production mein
 */


// =============================================================================
// STEP 4 — .ENV FILE CONFIGURE KARNA
// =============================================================================

/*
 *   cp .env.example .env
 *   nano .env   (ya koi bhi editor)
 *
 * Production .env mein ye sab DAALO (dekho 01_which_aws_services_when.php
 * agar confusion ho kis service ka endpoint kahan se milta hai):
 */

$productionEnvExample = "
APP_NAME='Your App'
APP_ENV=production
APP_KEY=                          # neeche generate karenge
APP_DEBUG=false
APP_URL=https://yourapp.com

DB_CONNECTION=mysql
DB_HOST=your-rds-endpoint.rds.amazonaws.com
DB_PORT=3306
DB_DATABASE=yourapp
DB_USERNAME=admin
DB_PASSWORD=...

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=your-elasticache-endpoint.cache.amazonaws.com
REDIS_PORT=6379

FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=ap-south-1
AWS_BUCKET=your-bucket-name

MAIL_MAILER=ses

LOG_CHANNEL=stack
LOG_LEVEL=error
";


// =============================================================================
// STEP 5 — LARAVEL SETUP COMMANDS
// =============================================================================

/*
 *   php artisan key:generate            # APP_KEY .env mein khud bhar dega
 *   php artisan storage:link            # public/storage symlink banata hai
 *   php artisan migrate --force         # --force zaroori hai production mein
 *                                          (warna confirmation prompt pe ruk jayega)
 *
 *   # Performance ke liye caching (HAMESHA deploy ke END mein chalao):
 *   php artisan config:cache
 *   php artisan route:cache
 *   php artisan view:cache
 *   php artisan event:cache
 */


// =============================================================================
// STEP 6 — PERMISSIONS THEEK KARNA
// =============================================================================

/*
 *   sudo chown -R www-data:www-data /var/www/your-project
 *   sudo chmod -R 755 /var/www/your-project
 *   sudo chmod -R 775 storage bootstrap/cache
 *
 * storage/ aur bootstrap/cache/ ko WRITABLE hona ZAROORI hai —
 * warna logs, cache, sessions save nahi hongi (white screen error aayega)
 */


// =============================================================================
// STEP 7 — NGINX CONFIGURE KARNA
// =============================================================================

/*
 * /etc/nginx/sites-available/yourapp.com mein:
 *
 *   server {
 *       listen 80;
 *       server_name yourapp.com;
 *       root /var/www/your-project/public;
 *
 *       index index.php;
 *
 *       location / {
 *           try_files $uri $uri/ /index.php?$query_string;
 *       }
 *
 *       location ~ \.php$ {
 *           fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
 *           fastcgi_index index.php;
 *           include fastcgi_params;
 *           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
 *       }
 *
 *       location ~ /\.ht {
 *           deny all;
 *       }
 *   }
 *
 *   sudo ln -s /etc/nginx/sites-available/yourapp.com /etc/nginx/sites-enabled/
 *   sudo nginx -t          # config test karo
 *   sudo systemctl restart nginx
 */


// =============================================================================
// STEP 8 — QUEUE WORKERS CHALANA (Supervisor se)
// =============================================================================

/*
 * Heavy kaam (email, notification — dekho event-listener/ folder) ke liye
 * queue worker HAMESHA chalna chahiye. Supervisor isay "crash ho to phir
 * se chala do" automatically karta hai.
 *
 *   sudo apt install supervisor
 *
 * /etc/supervisor/conf.d/laravel-worker.conf:
 *
 *   [program:laravel-worker]
 *   process_name=%(program_name)s_%(process_num)02d
 *   command=php /var/www/your-project/artisan queue:work redis --sleep=3 --tries=3
 *   autostart=true
 *   autorestart=true
 *   numprocs=2
 *   user=www-data
 *   redirect_stderr=true
 *   stdout_logfile=/var/www/your-project/storage/logs/worker.log
 *
 *   sudo supervisorctl reread
 *   sudo supervisorctl update
 *   sudo supervisorctl start laravel-worker:*
 */


// =============================================================================
// STEP 9 — SSL CERTIFICATE LAGANA
// =============================================================================

/*
 * AGAR ALB use kar rahe ho: ACM certificate ALB ke HTTPS listener mein
 * laga do (console se) — server par kuch karne ki zaroorat nahi.
 *
 * AGAR direct EC2 par (bina ALB ke): Let's Encrypt use karo:
 *   sudo apt install certbot python3-certbot-nginx
 *   sudo certbot --nginx -d yourapp.com -d www.yourapp.com
 *
 * (Detail: aws_advanced/ssl/ssl.php)
 */


// =============================================================================
// STEP 10 — CRON JOBS (Laravel Scheduler ke liye)
// =============================================================================

/*
 *   crontab -e
 *
 *   * * * * * cd /var/www/your-project && php artisan schedule:run >> /dev/null 2>&1
 *
 * Ye Laravel ko har minute "check" karne deta hai ke koi scheduled
 * task (jaise database backup, report generate) chalani hai ya nahi.
 */


// =============================================================================
// STEP 11 — CI/CD KE SATH AUTOMATE KARNA (Manual steps khatam karna)
// =============================================================================

/*
 * Upar wale saare steps HAR BAAR manually karna time-waste hai.
 * GitHub Actions (ya GitLab CI) se automate karo:
 *
 *   1. Code push hota hai → main branch
 *   2. GitHub Actions automatically:
 *      - Tests chalata hai
 *      - SSH se server par connect hota hai
 *      - git pull, composer install, npm build,
 *        migrate, cache clear/rebuild — sab automatic
 *
 * (Detail: aws_advanced/cicd_pipeline/cicd_pipeline.php,
 *  aws_advanced/git_actions/git_actions.php)
 */


// =============================================================================
// DEPLOY SCRIPT — SAB EK SATH (deploy.sh)
// =============================================================================

$deployScript = "
#!/bin/bash
set -e   # koi command fail ho to script turant ruk jaye

echo 'Maintenance mode ON...'
php artisan down

echo 'Latest code khinch rahe hain...'
git pull origin main

echo 'Dependencies install ho rahi hain...'
composer install --no-dev --optimize-autoloader
npm install && npm run build

echo 'Migrations chal rahi hain...'
php artisan migrate --force

echo 'Cache rebuild ho raha hai...'
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo 'Queue workers restart ho rahe hain...'
php artisan queue:restart

echo 'Maintenance mode OFF...'
php artisan up

echo 'Deploy complete!'
";

// Is script ko CI/CD pipeline ke andar ya manually SSH karke chala sakte ho.
