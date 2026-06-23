<?php

/**
 * ============================================================================
 *                    DOCKER + AWS — MUKAMMAL GUIDE
 *        Laravel Dockerize karna aur AWS par Deploy karna
 * ============================================================================
 */


// =============================================================================
// 1. DOCKER KYUN USE KAREIN AWS PAR?
// =============================================================================

/*
 * MASLA BINA DOCKER KE:
 *   Developer ka laptop: PHP 8.2, MySQL 8.0, Redis 7 — sab kaam karta hai
 *   EC2 Server: PHP 8.1, MySQL 5.7, Redis 6 — KUCH KAAM NAHI KARTA!
 *   "Mere machine par toh chal raha tha!" 😫
 *
 * DOCKER SE HAL:
 *   Docker container = chhota sa dabba jis mein sab kuch hai
 *   - PHP 8.2 ✓
 *   - Nginx ✓
 *   - Saari extensions ✓
 *   - Sab settings ✓
 *   Yeh dabba kahin bhi chalao — laptop, EC2, ECS — SAME result!
 *
 *
 * DOCKER KI BUNIYADI CHEEZEIN:
 * ────────────────────────────
 *   Dockerfile    = Nuskha (recipe) — container kaise banayen
 *   Image         = Tayar package (recipe se bana hua)
 *   Container     = Chalne wala instance (package se shuru kiya hua)
 *   Docker Hub    = Images ki dukaan (download/upload)
 *   ECR           = AWS ki apni images ki dukaan (private)
 *   docker-compose = Kai containers ek sath chalane ka tool
 */


// =============================================================================
// 2. LARAVEL KA DOCKERFILE
// =============================================================================

/*
 * Project ki root mein "Dockerfile" banayen:
 *
 * ─── Dockerfile ───
 *
 *   # Stage 1: PHP Dependencies install karo
 *   FROM composer:2 AS composer
 *   WORKDIR /app
 *   COPY composer.json composer.lock ./
 *   RUN composer install --no-dev --optimize-autoloader --no-scripts
 *
 *   # Stage 2: Frontend build karo
 *   FROM node:20-alpine AS node
 *   WORKDIR /app
 *   COPY package.json package-lock.json ./
 *   RUN npm ci
 *   COPY . .
 *   RUN npm run build
 *
 *   # Stage 3: Final production image
 *   FROM php:8.2-fpm-alpine
 *
 *   # System dependencies
 *   RUN apk add --no-cache \
 *       nginx \
 *       supervisor \
 *       libpng-dev \
 *       libjpeg-turbo-dev \
 *       libzip-dev \
 *       icu-dev \
 *       oniguruma-dev
 *
 *   # PHP extensions
 *   RUN docker-php-ext-configure gd --with-jpeg \
 *       && docker-php-ext-install -j$(nproc) \
 *           pdo_mysql \
 *           mbstring \
 *           zip \
 *           gd \
 *           bcmath \
 *           intl \
 *           opcache
 *
 *   # Redis extension
 *   RUN pecl install redis && docker-php-ext-enable redis
 *
 *   # Working directory set karo
 *   WORKDIR /var/www/html
 *
 *   # Composer dependencies copy karo
 *   COPY --from=composer /app/vendor ./vendor
 *
 *   # Frontend build copy karo
 *   COPY --from=node /app/public/build ./public/build
 *
 *   # Application code copy karo
 *   COPY . .
 *
 *   # Permissions set karo
 *   RUN chown -R www-data:www-data storage bootstrap/cache \
 *       && chmod -R 775 storage bootstrap/cache
 *
 *   # Nginx config copy karo
 *   COPY docker/nginx.conf /etc/nginx/http.d/default.conf
 *
 *   # Supervisor config copy karo
 *   COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
 *
 *   # PHP optimize karo
 *   RUN php artisan config:cache \
 *       && php artisan route:cache \
 *       && php artisan view:cache
 *
 *   EXPOSE 80
 *
 *   CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
 *
 *
 * ─── docker/nginx.conf ───
 *
 *   server {
 *       listen 80;
 *       root /var/www/html/public;
 *       index index.php;
 *
 *       location / {
 *           try_files $uri $uri/ /index.php?$query_string;
 *       }
 *
 *       location ~ \.php$ {
 *           fastcgi_pass 127.0.0.1:9000;
 *           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
 *           include fastcgi_params;
 *       }
 *   }
 *
 *
 * ─── docker/supervisord.conf ───
 *
 *   [supervisord]
 *   nodaemon=true
 *
 *   [program:nginx]
 *   command=nginx -g "daemon off;"
 *   autostart=true
 *   autorestart=true
 *
 *   [program:php-fpm]
 *   command=php-fpm
 *   autostart=true
 *   autorestart=true
 *
 *   [program:queue-worker]
 *   command=php /var/www/html/artisan queue:work redis --sleep=3 --tries=3
 *   autostart=true
 *   autorestart=true
 *   numprocs=2
 *   process_name=%(program_name)s_%(process_num)02d
 */


// =============================================================================
// 3. DOCKER-COMPOSE (Local Development)
// =============================================================================

/*
 * ─── docker-compose.yml ───
 *
 *   version: '3.8'
 *
 *   services:
 *     app:
 *       build:
 *         context: .
 *         dockerfile: Dockerfile
 *       ports:
 *         - "8000:80"
 *       volumes:
 *         - .:/var/www/html
 *       environment:
 *         - APP_ENV=local
 *         - DB_HOST=mysql
 *         - REDIS_HOST=redis
 *       depends_on:
 *         - mysql
 *         - redis
 *
 *     mysql:
 *       image: mysql:8.0
 *       environment:
 *         MYSQL_ROOT_PASSWORD: secret
 *         MYSQL_DATABASE: myapp
 *       ports:
 *         - "3306:3306"
 *       volumes:
 *         - mysql_data:/var/lib/mysql
 *
 *     redis:
 *       image: redis:7-alpine
 *       ports:
 *         - "6379:6379"
 *
 *   volumes:
 *     mysql_data:
 *
 *
 * COMMANDS:
 *   docker compose up -d           # Sab shuru karo
 *   docker compose down            # Sab band karo
 *   docker compose logs app        # App ke logs dekho
 *   docker compose exec app sh     # Container mein ghuso
 */


// =============================================================================
// 4. AWS ECR (Elastic Container Registry)
// =============================================================================

/*
 * ECR = AWS ki private Docker image ki dukaan
 *
 * Docker Hub jaisa hai magar:
 *   - Private (sirf aap ki images)
 *   - AWS ke andar (tez pull/push)
 *   - IAM se integrated (mehfooz)
 *
 *
 * ECR SETUP:
 * ──────────
 *
 * QADAM 1: ECR repository banayen
 *   AWS Console → ECR → "Create repository"
 *   - Name: my-laravel-app
 *   - Scan on push: Enable (security vulnerabilities check karega)
 *
 * QADAM 2: Docker image build karo
 *   docker build -t my-laravel-app .
 *
 * QADAM 3: ECR mein login karo
 *   aws ecr get-login-password --region ap-south-1 | \
 *       docker login --username AWS --password-stdin \
 *       123456789.dkr.ecr.ap-south-1.amazonaws.com
 *
 * QADAM 4: Image tag karo
 *   docker tag my-laravel-app:latest \
 *       123456789.dkr.ecr.ap-south-1.amazonaws.com/my-laravel-app:latest
 *
 * QADAM 5: Image push karo
 *   docker push 123456789.dkr.ecr.ap-south-1.amazonaws.com/my-laravel-app:latest
 *
 *
 * Ab yeh image ECS ya EC2 se pull kar sakte hain!
 */


// =============================================================================
// 5. AWS ECS (Elastic Container Service) — Docker Containers AWS par
// =============================================================================

/*
 * ECS = AWS ki container orchestration service
 * Docker containers ko manage aur scale karta hai
 *
 * DO MODES:
 *
 * 1) EC2 Launch Type:
 *    - Aap ki EC2 instances par containers chalte hain
 *    - Aap EC2 instances manage karte hain
 *    - Sasta (EC2 ki qeemat)
 *
 * 2) Fargate Launch Type (TAVSIYA):
 *    - Serverless — koi EC2 manage nahi karna
 *    - AWS khud servers manage karta hai
 *    - Sirf container ke resources ka paisa
 *    - Setup aur manage bohot aasan
 *
 *
 * ECS KI BUNIYADI CHEEZEIN:
 *
 *   Cluster     = Containers ka group (maslan: "production")
 *   Task Def    = Container ka blueprint (kaunsi image, kitni memory, ports)
 *   Service     = Tasks ko chalane aur manage karne wala (kitne tasks, load balancer)
 *   Task        = Chalne wala container instance
 *
 *
 * ECS FARGATE SETUP:
 * ──────────────────
 *
 * QADAM 1: Cluster banayen
 *   ECS → Clusters → Create Cluster
 *   - Name: my-laravel-cluster
 *   - Infrastructure: AWS Fargate
 *
 * QADAM 2: Task Definition banayen
 *   ECS → Task Definitions → Create
 *   - Name: my-laravel-task
 *   - Launch type: Fargate
 *   - CPU: 0.5 vCPU
 *   - Memory: 1 GB
 *   - Container:
 *     - Name: laravel-app
 *     - Image: 123456789.dkr.ecr.ap-south-1.amazonaws.com/my-laravel-app:latest
 *     - Port: 80
 *     - Environment variables: .env ki saari values
 *
 * QADAM 3: Service banayen
 *   Cluster → Services → Create
 *   - Task definition: my-laravel-task
 *   - Desired tasks: 2 (do containers chalen ge)
 *   - Load balancer: Application Load Balancer attach karo
 *   - Auto-scaling: Min 2, Max 10 (traffic ke hisab se)
 *
 *
 * FARGATE KI QEEMAT (ap-south-1):
 *   0.5 vCPU + 1 GB RAM ≈ $15-20/month
 *   1 vCPU + 2 GB RAM   ≈ $30-40/month
 */


// =============================================================================
// 6. DOCKER BEST PRACTICES
// =============================================================================

/*
 * ✅ Multi-stage builds use karo (image size chhoti rahe)
 * ✅ Alpine images use karo (chhoti aur mehfooz)
 * ✅ .dockerignore file banayen (node_modules, .git, .env exclude karo)
 * ✅ Non-root user se container chalao
 * ✅ Health checks add karo
 * ✅ Environment variables use karo (secrets hardcode mat karo)
 * ✅ Image scanning enable karo ECR mein
 *
 * ❌ Latest tag production mein mat use karo (version daalein: v1.2.3)
 * ❌ Secrets Dockerfile mein mat daalein
 * ❌ Unnecessary packages install mat karo
 *
 *
 * ─── .dockerignore ───
 *
 *   node_modules
 *   vendor
 *   .git
 *   .env
 *   .env.local
 *   storage/logs/*
 *   storage/framework/cache/*
 *   tests
 *   docker-compose.yml
 */
