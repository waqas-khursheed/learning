<?php

/**
 * ============================================================================
 *                    AWS EC2 — MUKAMMAL GUIDE
 *          Laravel Project ke liye EC2 Instance Setup karna
 * ============================================================================
 */


// =============================================================================
// 1. EC2 KYA HAI?
// =============================================================================

/*
 * EC2 = Elastic Compute Cloud
 *
 * EC2 AWS ka virtual server hai. Isko samjho jaise aap ne ek computer
 * kiraye par liya hai jo internet par chalta hai.
 *
 * AASAN MISAAL:
 * ─────────────
 *   Apna computer = ghar ka computer (limited resources, sirf aap ke liye)
 *   EC2 Instance  = kiraye ka computer cloud mein (zaroorat ke mutabiq bada/chhota karo)
 *
 * EC2 mein aap:
 *   - Linux ya Windows server chala sakte hain
 *   - Apni Laravel app host kar sakte hain
 *   - Zaroorat ke mutabiq resources barha/ghata sakte hain
 *   - Sirf utna pay karo jitna use karo
 */


// =============================================================================
// 2. EC2 INSTANCE TYPES (Qismain)
// =============================================================================

/*
 * ┌───────────────────────────────────────────────────────────────────────┐
 * │  Type         │  Kab Use Karein             │  Misaal              │
 * ├───────────────────────────────────────────────────────────────────────┤
 * │  t2.micro     │  Testing/Dev (Free Tier)     │  Chhota Laravel app  │
 * │  t3.small     │  Chhoti production app       │  Kam traffic site    │
 * │  t3.medium    │  Darmiyana production        │  Normal traffic site │
 * │  t3.large     │  Bari production app         │  Zyada traffic site  │
 * │  m5.large     │  General purpose production  │  Balanced workload   │
 * │  c5.large     │  CPU-intensive kaam          │  Data processing     │
 * │  r5.large     │  Memory-intensive kaam       │  Bari database cache │
 * └───────────────────────────────────────────────────────────────────────┘
 *
 * FREE TIER:
 * ──────────
 *   - t2.micro: 750 ghante/mahina FREE (pehle 12 mahine)
 *   - 1 vCPU, 1 GB RAM
 *   - Testing aur seekhne ke liye behtareen
 *
 *
 * INSTANCE FAMILIES SAMAJHNA:
 * ──────────────────────────
 *   t = Burstable (aam kaam, kabhi kabhi zyada CPU chahiye)
 *   m = General Purpose (balanced CPU + Memory)
 *   c = Compute Optimized (zyada CPU chahiye)
 *   r = Memory Optimized (zyada RAM chahiye)
 *   i = Storage Optimized (tez disk I/O chahiye)
 *
 *   Maslan: t3.medium
 *           t  = family (burstable)
 *           3  = generation (naya generation = behtar)
 *           medium = size (chhota/darmiyana/bara)
 */


// =============================================================================
// 3. EC2 INSTANCE BANANA (Step-by-Step)
// =============================================================================

/*
 * QADAM 1: AWS Console mein Login karein
 * ──────────────────────────────────────
 *   - aws.amazon.com par jayein
 *   - Console mein login karein
 *   - Region select karein (maslan: ap-south-1 = Mumbai, us-east-1 = Virginia)
 *
 *   ⚠️ REGION AHEM HAI:
 *   - Apne users ke qareeb wala region chunen
 *   - Pakistan ke liye: ap-south-1 (Mumbai) ya me-south-1 (Bahrain)
 *   - Har region ki qeemat mukhtalif hai
 *
 *
 * QADAM 2: EC2 Dashboard par jayein
 * ──────────────────────────────────
 *   - Services → EC2 → "Launch Instance" button dabayein
 *
 *
 * QADAM 3: Instance Configure karein
 * ──────────────────────────────────
 *
 *   a) NAME:
 *      - Name: "my-laravel-app" (koi bhi naam do)
 *
 *   b) AMI (Amazon Machine Image) — Operating System chunen:
 *      - Ubuntu 22.04 LTS (Laravel ke liye TAVSIYA)
 *      - Amazon Linux 2023
 *      - ⚠️ Free Tier eligible wala chunen
 *
 *   c) INSTANCE TYPE:
 *      - t2.micro (Free Tier / Testing)
 *      - t3.small (Chhoti Production)
 *      - t3.medium (Production TAVSIYA)
 *
 *   d) KEY PAIR — SSH login ke liye:
 *      - "Create new key pair" dabayein
 *      - Name: "my-laravel-key"
 *      - Type: RSA
 *      - Format: .pem (Mac/Linux) ya .ppk (Windows/PuTTY)
 *      - ⚠️ ZAROOR DOWNLOAD KAREIN! Yeh dubara nahi milegi!
 *      - Is file ko mehfooz rakhein — iske baghair server mein login nahi ho ga
 *
 *   e) NETWORK SETTINGS:
 *      - VPC: Default VPC (ya apna custom VPC)
 *      - Auto-assign Public IP: Enable
 *      - Security Group: Naya banayen ya pehle se maujood chunen
 *
 *   f) SECURITY GROUP RULES:
 *      ┌────────────────────────────────────────────────────────┐
 *      │  Type        │  Port  │  Source        │  Maqsad       │
 *      ├────────────────────────────────────────────────────────┤
 *      │  SSH         │  22    │  My IP         │  Server login  │
 *      │  HTTP        │  80    │  0.0.0.0/0     │  Website       │
 *      │  HTTPS       │  443   │  0.0.0.0/0     │  SSL Website   │
 *      │  Custom TCP  │  8000  │  0.0.0.0/0     │  Laravel Dev   │
 *      └────────────────────────────────────────────────────────┘
 *      ⚠️ SSH (port 22) ko "My IP" rakhein — "0.0.0.0/0" KABHI MAT karein
 *         (warna duniya bhar se log login try karein ge)
 *
 *   g) STORAGE:
 *      - 20-30 GB General Purpose SSD (gp3)
 *      - Free Tier: 30 GB tak free
 *      - Production: 50-100 GB
 *
 *   h) "Launch Instance" dabayein!
 */


// =============================================================================
// 4. EC2 MEIN SSH SE LOGIN KARNA
// =============================================================================

/*
 * WINDOWS SE (PowerShell ya Git Bash):
 * ────────────────────────────────────
 *
 *   # Pehle key file ki permission set karo
 *   chmod 400 my-laravel-key.pem
 *
 *   # SSH se login karo
 *   ssh -i "my-laravel-key.pem" ubuntu@YOUR_EC2_PUBLIC_IP
 *
 *   Maslan:
 *   ssh -i "my-laravel-key.pem" ubuntu@54.123.45.67
 *
 *   ⚠️ Ubuntu AMI ke liye username "ubuntu" hai
 *      Amazon Linux ke liye username "ec2-user" hai
 *
 *
 * WINDOWS SE (PuTTY):
 * ───────────────────
 *   1. PuTTYgen se .pem ko .ppk mein convert karo
 *   2. PuTTY kholein
 *   3. Host Name: ubuntu@YOUR_EC2_PUBLIC_IP
 *   4. Connection → SSH → Auth → Private key file: apni .ppk file chunen
 *   5. "Open" dabayein
 *
 *
 * PUBLIC IP KAHAN MILEGA?
 * ──────────────────────
 *   EC2 Dashboard → Instances → Apna instance select karein
 *   → "Public IPv4 address" column mein dikhega
 *
 *
 * ELASTIC IP (Permanent IP):
 * ──────────────────────────
 *   By default EC2 ka public IP restart par badal jata hai.
 *   Permanent IP ke liye Elastic IP use karein:
 *
 *   1. EC2 Dashboard → Elastic IPs → "Allocate Elastic IP address"
 *   2. Naya IP allocate karein
 *   3. "Associate Elastic IP address" → Apna instance select karein
 *   4. Ab yeh IP hamesha same rahega
 *
 *   ⚠️ Elastic IP FREE hai jab tak kisi running instance se jura ho.
 *      Agar allocate kiya magar kisi instance se nahi jora toh CHARGE lagega!
 */


// =============================================================================
// 5. EC2 PAR LARAVEL KA ENVIRONMENT SETUP
// =============================================================================

/*
 * Server mein login hone ke baad yeh sab install karein:
 */

// ── STEP 1: System Update ──

/*
 *   sudo apt update && sudo apt upgrade -y
 */


// ── STEP 2: PHP 8.2+ Install karo ──

/*
 *   # PHP repository add karo
 *   sudo add-apt-repository ppa:ondrej/php -y
 *   sudo apt update
 *
 *   # PHP aur zaruri extensions install karo
 *   sudo apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-common \
 *       php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl \
 *       php8.2-xml php8.2-bcmath php8.2-redis php8.2-intl php8.2-readline
 *
 *   # PHP version check karo
 *   php -v
 */


// ── STEP 3: Composer Install karo ──

/*
 *   curl -sS https://getcomposer.org/installer | php
 *   sudo mv composer.phar /usr/local/bin/composer
 *   composer --version
 */


// ── STEP 4: Nginx Install karo (Web Server) ──

/*
 *   sudo apt install nginx -y
 *   sudo systemctl start nginx
 *   sudo systemctl enable nginx
 *
 *   # Browser mein apna EC2 IP daal ke dekho — Nginx welcome page aana chahiye
 */


// ── STEP 5: MySQL Client Install karo ──

/*
 *   sudo apt install mysql-client -y
 *
 *   # ⚠️ Production mein MySQL EC2 par MAT install karo!
 *   # AWS RDS use karo (alag section mein samjhaya hai)
 *
 *   # Agar testing ke liye EC2 par hi MySQL chahiye:
 *   sudo apt install mysql-server -y
 *   sudo mysql_secure_installation
 */


// ── STEP 6: Redis Install karo ──

/*
 *   sudo apt install redis-server -y
 *   sudo systemctl start redis-server
 *   sudo systemctl enable redis-server
 *   redis-cli ping    # "PONG" aana chahiye
 *
 *   # ⚠️ Production mein AWS ElastiCache use karo
 */


// ── STEP 7: Node.js Install karo (Frontend build ke liye) ──

/*
 *   curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
 *   sudo apt install -y nodejs
 *   node -v
 *   npm -v
 */


// ── STEP 8: Git Install karo ──

/*
 *   sudo apt install git -y
 *   git --version
 */


// =============================================================================
// 6. LARAVEL PROJECT EC2 PAR DEPLOY KARNA
// =============================================================================

/*
 * ── STEP 1: Project clone karo ──
 *
 *   cd /var/www
 *   sudo git clone https://github.com/YOUR_USERNAME/your-laravel-project.git
 *   cd your-laravel-project
 *
 *   # Permissions set karo
 *   sudo chown -R www-data:www-data /var/www/your-laravel-project
 *   sudo chmod -R 775 storage bootstrap/cache
 *
 *
 * ── STEP 2: Dependencies install karo ──
 *
 *   composer install --optimize-autoloader --no-dev
 *   npm install && npm run build
 *
 *
 * ── STEP 3: .env file banayen ──
 *
 *   cp .env.example .env
 *   php artisan key:generate
 *
 *   # .env edit karo:
 *   sudo nano .env
 *
 *   # Yeh values set karo:
 *   APP_NAME="My Laravel App"
 *   APP_ENV=production
 *   APP_DEBUG=false                    # ⚠️ Production mein HAMESHA false!
 *   APP_URL=https://yourdomain.com
 *
 *   DB_CONNECTION=mysql
 *   DB_HOST=your-rds-endpoint.amazonaws.com   # RDS ka endpoint
 *   DB_PORT=3306
 *   DB_DATABASE=myapp
 *   DB_USERNAME=admin
 *   DB_PASSWORD=your-secure-password
 *
 *   CACHE_DRIVER=redis
 *   SESSION_DRIVER=redis
 *   QUEUE_CONNECTION=redis
 *
 *   REDIS_HOST=your-elasticache-endpoint.cache.amazonaws.com
 *   REDIS_PORT=6379
 *
 *
 * ── STEP 4: Laravel optimize karo ──
 *
 *   php artisan migrate --force
 *   php artisan config:cache
 *   php artisan route:cache
 *   php artisan view:cache
 *   php artisan storage:link
 *   php artisan optimize
 */


// =============================================================================
// 7. NGINX CONFIGURATION FOR LARAVEL
// =============================================================================

/*
 * Nginx config file banayen:
 *
 *   sudo nano /etc/nginx/sites-available/laravel
 *
 * Yeh content paste karein:
 *
 *   server {
 *       listen 80;
 *       server_name yourdomain.com www.yourdomain.com;
 *       root /var/www/your-laravel-project/public;
 *
 *       add_header X-Frame-Options "SAMEORIGIN";
 *       add_header X-Content-Type-Options "nosniff";
 *
 *       index index.php;
 *
 *       charset utf-8;
 *
 *       location / {
 *           try_files $uri $uri/ /index.php?$query_string;
 *       }
 *
 *       location = /favicon.ico { access_log off; log_not_found off; }
 *       location = /robots.txt  { access_log off; log_not_found off; }
 *
 *       error_page 404 /index.php;
 *
 *       location ~ \.php$ {
 *           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
 *           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
 *           include fastcgi_params;
 *       }
 *
 *       location ~ /\.(?!well-known).* {
 *           deny all;
 *       }
 *   }
 *
 *
 * Site enable karo:
 *
 *   sudo ln -s /etc/nginx/sites-available/laravel /etc/nginx/sites-enabled/
 *   sudo rm /etc/nginx/sites-enabled/default    # Default site hata do
 *   sudo nginx -t                                # Config test karo
 *   sudo systemctl restart nginx                 # Nginx restart karo
 *
 *
 * Ab browser mein apna EC2 IP ya domain daal ke dekho — Laravel app chalegi!
 */


// =============================================================================
// 8. QUEUE WORKER SETUP (Supervisor)
// =============================================================================

/*
 * Laravel queues ke liye Supervisor use karo (worker band hone par khud shuru kare):
 *
 *   sudo apt install supervisor -y
 *
 *   # Config file banayen:
 *   sudo nano /etc/supervisor/conf.d/laravel-worker.conf
 *
 *   # Yeh paste karo:
 *   [program:laravel-worker]
 *   process_name=%(program_name)s_%(process_num)02d
 *   command=php /var/www/your-laravel-project/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
 *   autostart=true
 *   autorestart=true
 *   stopasgroup=true
 *   killasgroup=true
 *   user=www-data
 *   numprocs=2
 *   redirect_stderr=true
 *   stdout_logfile=/var/www/your-laravel-project/storage/logs/worker.log
 *   stopwaitsecs=3600
 *
 *   # Supervisor ko batayen:
 *   sudo supervisorctl reread
 *   sudo supervisorctl update
 *   sudo supervisorctl start laravel-worker:*
 *
 *   # Status check karo:
 *   sudo supervisorctl status
 */


// =============================================================================
// 9. EC2 SECURITY BEST PRACTICES
// =============================================================================

/*
 * ✅ SSH key pair use karo (password authentication band karo)
 * ✅ Security Group mein sirf zaruri ports kholein
 * ✅ SSH (port 22) sirf apne IP se allow karo
 * ✅ Regular system updates chalao
 * ✅ IAM Roles use karo (access keys EC2 par mat rakhho)
 * ✅ CloudWatch monitoring enable karo
 * ✅ Elastic IP use karo permanent address ke liye
 *
 * ❌ Root user se login mat karo
 * ❌ Security group mein 0.0.0.0/0 se SSH mat kholein
 * ❌ .env file ya credentials git mein mat daalein
 * ❌ APP_DEBUG=true production mein kabhi mat karo
 *
 *
 * FIREWALL (UFW) SETUP:
 * ─────────────────────
 *   sudo ufw allow OpenSSH
 *   sudo ufw allow 'Nginx Full'
 *   sudo ufw enable
 *   sudo ufw status
 */
