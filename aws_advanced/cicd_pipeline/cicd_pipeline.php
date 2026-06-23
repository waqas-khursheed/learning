<?php

/**
 * ============================================================================
 *                    CI/CD PIPELINE — MUKAMMAL GUIDE
 *        GitHub Actions se Laravel Auto Deploy AWS par
 * ============================================================================
 */


// =============================================================================
// 1. CI/CD KYA HAI?
// =============================================================================

/*
 * CI = Continuous Integration (Lagatar Jor Milap)
 *   - Har code push par tests automatically chalte hain
 *   - Code quality check hoti hai
 *   - Bugs jaldi pakre jaate hain
 *
 * CD = Continuous Deployment (Lagatar Tainaati)
 *   - Tests pass hone ke baad code automatically server par deploy ho jata hai
 *   - Haath se deploy karne ki zaroorat nahi
 *   - Har approved change foran live ho jata hai
 *
 *
 * AASAN MISAAL:
 * ─────────────
 *   Bina CI/CD:
 *     1. Developer code likhta hai
 *     2. Haath se test karta hai
 *     3. SSH se server mein ghusta hai
 *     4. git pull karta hai
 *     5. composer install karta hai
 *     6. Dua karta hai kuch toota nahi 😰
 *
 *   CI/CD ke sath:
 *     1. Developer code push karta hai
 *     2. ✅ Tests khud-ba-khud chalte hain
 *     3. ✅ Code khud-ba-khud deploy ho jata hai
 *     4. ✅ Developer chain ki chai peeta hai ☕
 *
 *
 * PIPELINE KA FLOW:
 * ─────────────────
 *
 *   Code Push → Build → Test → Deploy
 *       │         │       │       │
 *       ▼         ▼       ▼       ▼
 *   [GitHub]  [Install] [PHPUnit] [Server par]
 *             [deps]    [Lint]    [deploy]
 */


// =============================================================================
// 2. GITHUB ACTIONS — BASICS
// =============================================================================

/*
 * GitHub Actions GitHub ka built-in CI/CD tool hai.
 * Free hai public repos ke liye, private repos ke liye 2000 minutes/month free.
 *
 * BUNIYADI ISTILAHAAT:
 *
 *   Workflow  = Poora pipeline (ek YAML file)
 *   Job       = Kaam ka hissa (maslan: "test", "deploy")
 *   Step      = Ek chota qadam job ke andar
 *   Action    = Doosron ka banaya hua reusable step
 *   Runner    = Machine jis par pipeline chalta hai (GitHub deta hai)
 *   Secret    = Mehfooz variable (password, keys)
 *
 *
 * FILE KAHAN RAKHEIN:
 *   .github/workflows/deploy.yml
 *
 *   ⚠️ Yeh folder project ki root mein hona chahiye
 *   ⚠️ File .yml ya .yaml honi chahiye
 */


// =============================================================================
// 3. LARAVEL CI/CD — GITHUB ACTIONS WORKFLOW
// =============================================================================

/*
 * ─── .github/workflows/deploy.yml ───
 *
 *   name: Laravel CI/CD Pipeline
 *
 *   # Kab chalega: main branch par push ya PR
 *   on:
 *     push:
 *       branches: [main]
 *     pull_request:
 *       branches: [main]
 *
 *   jobs:
 *     # ═══════════════════════════════════════
 *     # JOB 1: TEST — Code test karo
 *     # ═══════════════════════════════════════
 *     test:
 *       name: Run Tests
 *       runs-on: ubuntu-latest
 *
 *       services:
 *         mysql:
 *           image: mysql:8.0
 *           env:
 *             MYSQL_ROOT_PASSWORD: password
 *             MYSQL_DATABASE: testing
 *           ports:
 *             - 3306:3306
 *           options: >-
 *             --health-cmd="mysqladmin ping"
 *             --health-interval=10s
 *             --health-timeout=5s
 *             --health-retries=3
 *
 *         redis:
 *           image: redis:7-alpine
 *           ports:
 *             - 6379:6379
 *
 *       steps:
 *         # Code checkout karo
 *         - uses: actions/checkout@v4
 *
 *         # PHP setup karo
 *         - name: Setup PHP
 *           uses: shivammathur/setup-php@v2
 *           with:
 *             php-version: '8.2'
 *             extensions: mbstring, xml, ctype, json, bcmath, pdo_mysql, redis
 *             coverage: xdebug
 *
 *         # Composer cache (tez karne ke liye)
 *         - name: Cache Composer dependencies
 *           uses: actions/cache@v4
 *           with:
 *             path: vendor
 *             key: composer-${{ hashFiles('composer.lock') }}
 *
 *         # Dependencies install karo
 *         - name: Install Dependencies
 *           run: composer install --no-progress --prefer-dist
 *
 *         # Environment setup
 *         - name: Setup Environment
 *           run: |
 *             cp .env.example .env
 *             php artisan key:generate
 *
 *         # Database migrate karo
 *         - name: Run Migrations
 *           env:
 *             DB_CONNECTION: mysql
 *             DB_HOST: 127.0.0.1
 *             DB_PORT: 3306
 *             DB_DATABASE: testing
 *             DB_USERNAME: root
 *             DB_PASSWORD: password
 *           run: php artisan migrate --force
 *
 *         # Tests chalao
 *         - name: Run Tests
 *           env:
 *             DB_CONNECTION: mysql
 *             DB_HOST: 127.0.0.1
 *             DB_PORT: 3306
 *             DB_DATABASE: testing
 *             DB_USERNAME: root
 *             DB_PASSWORD: password
 *             REDIS_HOST: 127.0.0.1
 *           run: php artisan test --parallel
 *
 *         # Code quality check (optional)
 *         - name: Run PHPStan
 *           run: vendor/bin/phpstan analyse --no-progress
 *
 *
 *     # ═══════════════════════════════════════
 *     # JOB 2: BUILD — Docker image banao
 *     # ═══════════════════════════════════════
 *     build:
 *       name: Build Docker Image
 *       runs-on: ubuntu-latest
 *       needs: test                # Sirf test pass hone ke baad
 *       if: github.ref == 'refs/heads/main' && github.event_name == 'push'
 *
 *       steps:
 *         - uses: actions/checkout@v4
 *
 *         # AWS credentials set karo
 *         - name: Configure AWS Credentials
 *           uses: aws-actions/configure-aws-credentials@v4
 *           with:
 *             aws-access-key-id: ${{ secrets.AWS_ACCESS_KEY_ID }}
 *             aws-secret-access-key: ${{ secrets.AWS_SECRET_ACCESS_KEY }}
 *             aws-region: ap-south-1
 *
 *         # ECR mein login karo
 *         - name: Login to Amazon ECR
 *           id: login-ecr
 *           uses: aws-actions/amazon-ecr-login@v2
 *
 *         # Docker image build aur push karo
 *         - name: Build and Push Docker Image
 *           env:
 *             ECR_REGISTRY: ${{ steps.login-ecr.outputs.registry }}
 *             IMAGE_TAG: ${{ github.sha }}
 *           run: |
 *             docker build -t $ECR_REGISTRY/my-laravel-app:$IMAGE_TAG .
 *             docker build -t $ECR_REGISTRY/my-laravel-app:latest .
 *             docker push $ECR_REGISTRY/my-laravel-app:$IMAGE_TAG
 *             docker push $ECR_REGISTRY/my-laravel-app:latest
 *
 *
 *     # ═══════════════════════════════════════
 *     # JOB 3: DEPLOY — Server par deploy karo
 *     # ═══════════════════════════════════════
 *     deploy:
 *       name: Deploy to Production
 *       runs-on: ubuntu-latest
 *       needs: build               # Sirf build hone ke baad
 *
 *       steps:
 *         - uses: actions/checkout@v4
 *
 *         - name: Configure AWS Credentials
 *           uses: aws-actions/configure-aws-credentials@v4
 *           with:
 *             aws-access-key-id: ${{ secrets.AWS_ACCESS_KEY_ID }}
 *             aws-secret-access-key: ${{ secrets.AWS_SECRET_ACCESS_KEY }}
 *             aws-region: ap-south-1
 *
 *         # ECS service update karo (naye image ke sath)
 *         - name: Deploy to ECS
 *           run: |
 *             aws ecs update-service \
 *               --cluster my-laravel-cluster \
 *               --service my-laravel-service \
 *               --force-new-deployment
 *
 *         # Ya EC2 par deploy karo (SSH se)
 *         # - name: Deploy to EC2
 *         #   uses: appleboy/ssh-action@v1
 *         #   with:
 *         #     host: ${{ secrets.EC2_HOST }}
 *         #     username: ubuntu
 *         #     key: ${{ secrets.EC2_SSH_KEY }}
 *         #     script: |
 *         #       cd /var/www/my-laravel-app
 *         #       git pull origin main
 *         #       composer install --no-dev --optimize-autoloader
 *         #       php artisan migrate --force
 *         #       php artisan config:cache
 *         #       php artisan route:cache
 *         #       php artisan view:cache
 *         #       php artisan queue:restart
 *         #       sudo systemctl restart php8.2-fpm
 */


// =============================================================================
// 4. GITHUB SECRETS SETUP KARNA
// =============================================================================

/*
 * Secrets = Mehfooz variables jo pipeline mein use hote hain
 * (Passwords, API keys, SSH keys — code mein nahi dikhte)
 *
 * KAHAN SET KAREIN:
 *   GitHub → Apna repo → Settings → Secrets and variables → Actions → New repository secret
 *
 * KYA SECRETS DAALEIN:
 *
 * ┌────────────────────────────────────────────────────────────────┐
 * │  Secret Name             │  Value                              │
 * ├────────────────────────────────────────────────────────────────┤
 * │  AWS_ACCESS_KEY_ID       │  AKIA... (IAM user ki key)          │
 * │  AWS_SECRET_ACCESS_KEY   │  wJalrX... (IAM user ka secret)     │
 * │  EC2_HOST                │  54.123.45.67 (EC2 ka IP)           │
 * │  EC2_SSH_KEY             │  -----BEGIN RSA... (pem file content)│
 * │  DB_PASSWORD             │  StrongPassword123!                  │
 * └────────────────────────────────────────────────────────────────┘
 *
 * ⚠️ Secrets code mein kabhi hardcode mat karo
 * ⚠️ Secrets log mein print nahi hote (GitHub khud chhupata hai)
 */


// =============================================================================
// 5. EC2 PAR SEEDHA DEPLOY (Bina Docker ke)
// =============================================================================

/*
 * Agar Docker use nahi karna toh EC2 par seedha deploy karo:
 *
 * ─── .github/workflows/deploy-ec2.yml ───
 *
 *   name: Deploy to EC2
 *
 *   on:
 *     push:
 *       branches: [main]
 *
 *   jobs:
 *     deploy:
 *       runs-on: ubuntu-latest
 *
 *       steps:
 *         - uses: actions/checkout@v4
 *
 *         - name: Deploy via SSH
 *           uses: appleboy/ssh-action@v1
 *           with:
 *             host: ${{ secrets.EC2_HOST }}
 *             username: ubuntu
 *             key: ${{ secrets.EC2_SSH_KEY }}
 *             script: |
 *               cd /var/www/my-laravel-app
 *
 *               # Maintenance mode ON karo
 *               php artisan down --retry=60
 *
 *               # Latest code laao
 *               git pull origin main
 *
 *               # Dependencies install karo
 *               composer install --no-dev --optimize-autoloader
 *
 *               # Frontend build karo
 *               npm ci && npm run build
 *
 *               # Database migrate karo
 *               php artisan migrate --force
 *
 *               # Cache saaf aur dubara banayen
 *               php artisan config:cache
 *               php artisan route:cache
 *               php artisan view:cache
 *               php artisan event:cache
 *
 *               # Queue workers restart karo
 *               php artisan queue:restart
 *
 *               # Permissions fix karo
 *               sudo chown -R www-data:www-data storage bootstrap/cache
 *
 *               # PHP-FPM restart karo
 *               sudo systemctl restart php8.2-fpm
 *
 *               # Maintenance mode OFF karo
 *               php artisan up
 *
 *               echo "Deploy mukammal ho gaya! ✅"
 */


// =============================================================================
// 6. ZERO-DOWNTIME DEPLOYMENT
// =============================================================================

/*
 * MASLA: Deploy ke dauran website kuch seconds ke liye band rehti hai
 *
 * HAL 1: Laravel Envoy (Simple)
 * ─────────────────────────────
 *   composer require laravel/envoy --dev
 *
 *   ─── Envoy.blade.php ───
 *
 *   @servers(['web' => 'ubuntu@YOUR_EC2_IP'])
 *
 *   @setup
 *       $repository = 'git@github.com:YOUR_USERNAME/your-app.git';
 *       $releases_dir = '/var/www/releases';
 *       $release = date('YmdHis');
 *       $new_release_dir = $releases_dir .'/'. $release;
 *       $app_dir = '/var/www/current';
 *   @endsetup
 *
 *   @story('deploy')
 *       clone_repository
 *       run_composer
 *       update_symlinks
 *       optimize
 *       cleanup
 *   @endstory
 *
 *   @task('clone_repository')
 *       echo 'Naya release clone ho raha hai...'
 *       [ -d {{ $releases_dir }} ] || mkdir {{ $releases_dir }}
 *       git clone --depth 1 {{ $repository }} {{ $new_release_dir }}
 *   @endtask
 *
 *   @task('run_composer')
 *       echo 'Composer install ho raha hai...'
 *       cd {{ $new_release_dir }}
 *       composer install --no-dev --optimize-autoloader
 *   @endtask
 *
 *   @task('update_symlinks')
 *       echo 'Symlinks update ho rahe hain...'
 *       # Shared files link karo
 *       ln -nfs /var/www/shared/.env {{ $new_release_dir }}/.env
 *       ln -nfs /var/www/shared/storage {{ $new_release_dir }}/storage
 *
 *       # Current symlink naye release par point karo
 *       ln -nfs {{ $new_release_dir }} {{ $app_dir }}
 *       # ↑ Yeh INSTANT hai — zero downtime!
 *   @endtask
 *
 *   @task('optimize')
 *       cd {{ $app_dir }}
 *       php artisan migrate --force
 *       php artisan config:cache
 *       php artisan route:cache
 *       php artisan view:cache
 *       php artisan queue:restart
 *       sudo systemctl restart php8.2-fpm
 *   @endtask
 *
 *   @task('cleanup')
 *       echo 'Purane releases saaf ho rahe hain...'
 *       cd {{ $releases_dir }}
 *       ls -dt */ | tail -n +6 | xargs rm -rf
 *   @endtask
 *
 *
 * HAL 2: ECS Rolling Deployment (Docker ke sath)
 * ──────────────────────────────────────────────
 *   - ECS Service mein "Rolling update" deployment type
 *   - Purane containers chaltay rehtay hain jab tak naye tayyar na hon
 *   - Koi downtime nahi!
 */


// =============================================================================
// 7. CI/CD BEST PRACTICES
// =============================================================================

/*
 * ✅ Tests hamesha deploy se pehle chalao
 * ✅ Secrets GitHub Secrets mein rakhho (code mein nahi)
 * ✅ main branch protect karo (direct push band, PR zaruri)
 * ✅ Docker images ko version tag do (git SHA use karo)
 * ✅ Rollback plan rakhho (purani image par wapas ja sakein)
 * ✅ Notifications lagao (Slack/email — deploy fail hone par)
 * ✅ Staging environment mein pehle deploy karo, phir production mein
 *
 * ❌ Production mein seedha push mat karo
 * ❌ Tests skip mat karo
 * ❌ Secrets log mein print mat karo
 * ❌ Bina review ke main mein merge mat karo
 */
