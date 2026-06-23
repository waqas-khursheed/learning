<?php

/**
 * ============================================================================
 *              MUKAMMAL DEPLOYMENT GUIDE — A to Z
 *     Laravel Project ko AWS par Production mein Deploy Karna
 * ============================================================================
 *
 * Yeh file POORI deployment ka ROAD MAP hai.
 * Har qadam mein bataya hai KYA karna hai aur detail KAUNSI file mein hai.
 *
 * ⚠️ Yeh guide assume karti hai:
 *   - Aap ke paas ek tayar Laravel project hai (GitHub par)
 *   - AWS account hai
 *   - Domain khareed rakha hai
 */


// =============================================================================
// POORA DEPLOYMENT ROAD MAP
// =============================================================================

/*
 * ┌─────────────────────────────────────────────────────────────────────┐
 * │  QADAM │  KYA KARNA HAI                  │  DETAIL FILE            │
 * ├─────────────────────────────────────────────────────────────────────┤
 * │   1    │  VPC aur Network setup           │  vpc_networking/        │
 * │   2    │  RDS Database banana              │  rds_database/          │
 * │   3    │  ElastiCache (Redis) setup        │  ../../redis/redis.php  │
 * │   4    │  S3 Bucket banana                 │  s3/                    │
 * │   5    │  EC2 Instance ya ECS setup        │  ec2/ aur docker/       │
 * │   6    │  Laravel deploy karna             │  ec2/                   │
 * │   7    │  Load Balancer lagana             │  load_balancing/        │
 * │   8    │  SSL Certificate lagana           │  ssl/                   │
 * │   9    │  Domain link karna                │  domain_setup/          │
 * │  10    │  CloudFront (CDN) lagana          │  cloudfront/            │
 * │  11    │  CI/CD Pipeline banana            │  cicd_pipeline/         │
 * │  12    │  Monitoring aur Alerts            │  (neeche detail hai)    │
 * └─────────────────────────────────────────────────────────────────────┘
 */


// =============================================================================
// QADAM 1: VPC AUR NETWORK SETUP
// =============================================================================

/*
 * Sab se pehle apna private network banayen.
 * (Detail: vpc_networking/vpc_networking.php)
 *
 * Kya banana hai:
 *   1. VPC (10.0.0.0/16)
 *   2. Public subnets (2 AZs mein) — ALB ke liye
 *   3. Private subnets (2 AZs mein) — EC2/ECS ke liye
 *   4. Database subnets (2 AZs mein) — RDS/ElastiCache ke liye
 *   5. Internet Gateway
 *   6. NAT Gateway
 *   7. Route Tables
 *   8. Security Groups (ALB, App, DB, Redis)
 *
 * ⏱️ Waqt: ~30 minute
 */


// =============================================================================
// QADAM 2: RDS DATABASE BANANA
// =============================================================================

/*
 * (Detail: rds_database/rds_database.php)
 *
 * Kya karna hai:
 *   1. RDS → Create database
 *   2. Engine: MySQL 8.0 (ya Aurora)
 *   3. Instance: db.t3.micro (dev) ya db.t3.medium (prod)
 *   4. Multi-AZ: Enable (production ke liye)
 *   5. Subnet group: Database subnets
 *   6. Security group: DB security group (sirf app se access)
 *   7. Endpoint note karo → .env mein DB_HOST mein daalein
 *
 * ⏱️ Waqt: ~15 minute (+ 5-10 minute creation)
 */


// =============================================================================
// QADAM 3: ELASTICACHE (REDIS) SETUP
// =============================================================================

/*
 * (Detail: ../../redis/redis.php — ElastiCache section)
 *
 * Kya karna hai:
 *   1. ElastiCache → Create cluster → Redis
 *   2. Node type: cache.t3.micro (dev) ya cache.r6g.large (prod)
 *   3. Replicas: 1-2 (production ke liye)
 *   4. Subnet group: Database subnets
 *   5. Security group: Redis security group
 *   6. Endpoint note karo → .env mein REDIS_HOST mein daalein
 *
 * ⏱️ Waqt: ~10 minute
 */


// =============================================================================
// QADAM 4: S3 BUCKET BANANA
// =============================================================================

/*
 * (Detail: s3/s3.php)
 *
 * Kya karna hai:
 *   1. S3 → Create bucket (private)
 *   2. Name: my-app-storage-production
 *   3. Encryption enable karo
 *   4. IAM Role banayen (ya IAM user banayen)
 *   5. Laravel mein flysystem-s3 install karo
 *   6. .env mein AWS credentials daalein
 *
 * ⏱️ Waqt: ~10 minute
 */


// =============================================================================
// QADAM 5: EC2 INSTANCE BANANA (ya ECS Setup)
// =============================================================================

/*
 * (Detail: ec2/ec2.php aur docker/docker.php)
 *
 * OPTION A — EC2 (Simple, beginners ke liye):
 *   1. EC2 → Launch instance
 *   2. Ubuntu 22.04, t3.medium
 *   3. Security group: App security group
 *   4. PHP, Nginx, Composer, Node.js install karo
 *
 * OPTION B — Docker + ECS (Professional):
 *   1. Dockerfile banayen
 *   2. ECR mein image push karo
 *   3. ECS Fargate cluster banayen
 *   4. Task definition aur service banayen
 *
 * ⏱️ Waqt: ~30-60 minute
 */


// =============================================================================
// QADAM 6: LARAVEL DEPLOY KARNA
// =============================================================================

/*
 * (Detail: ec2/ec2.php — section 6)
 *
 * EC2 par:
 *   1. git clone project
 *   2. composer install --no-dev
 *   3. npm install && npm run build
 *   4. .env configure karo (sab endpoints daalein)
 *   5. php artisan key:generate
 *   6. php artisan migrate --force
 *   7. php artisan optimize
 *   8. Nginx configure karo
 *   9. Supervisor setup karo (queue workers)
 *   10. Permissions fix karo
 *
 * .ENV FILE KI POORI MISAAL:
 */

$envExample = "
APP_NAME='My Laravel App'
APP_ENV=production
APP_KEY=base64:...generated-key...
APP_DEBUG=false
APP_URL=https://myapp.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=my-laravel-db.abc123.ap-south-1.rds.amazonaws.com
DB_PORT=3306
DB_DATABASE=myapp
DB_USERNAME=admin
DB_PASSWORD=StrongPassword123!

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=my-redis.abc123.cache.amazonaws.com
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=ses
MAIL_FROM_ADDRESS=info@myapp.com
MAIL_FROM_NAME='My App'

AWS_ACCESS_KEY_ID=AKIA...
AWS_SECRET_ACCESS_KEY=wJalrX...
AWS_DEFAULT_REGION=ap-south-1
AWS_BUCKET=my-app-storage-production
AWS_CLOUDFRONT_URL=https://cdn.myapp.com

SESSION_DOMAIN=.myapp.com
TRUSTED_PROXIES=*
";


// =============================================================================
// QADAM 7-8-9: LOAD BALANCER + SSL + DOMAIN
// =============================================================================

/*
 * (Detail: load_balancing/, ssl/, domain_setup/)
 *
 * QADAM 7: ALB Setup
 *   1. Target group banayen (EC2 instances register karo)
 *   2. ALB banayen (public subnets mein)
 *   3. Health check /health path par lagao
 *   4. Auto Scaling Group setup karo
 *
 * QADAM 8: SSL Certificate
 *   1. ACM mein certificate request karo
 *      - myapp.com + *.myapp.com
 *      - ⚠️ CloudFront ke liye us-east-1 mein banayen
 *   2. DNS validation karo
 *   3. ALB ke HTTPS listener mein certificate lagao
 *   4. HTTP → HTTPS redirect lagao
 *
 * QADAM 9: Domain Link
 *   1. Route 53 mein hosted zone banayen
 *   2. Domain provider mein nameservers update karo
 *   3. A/ALIAS record: myapp.com → ALB
 *   4. CNAME: www.myapp.com → ALB
 *   5. CNAME: cdn.myapp.com → CloudFront
 *
 * ⏱️ Waqt: ~45 minute (+ DNS propagation 24-48 ghante)
 */


// =============================================================================
// QADAM 10: CLOUDFRONT CDN
// =============================================================================

/*
 * (Detail: cloudfront/cloudfront.php)
 *
 * Kya karna hai:
 *   1. CloudFront distribution banayen
 *   2. Origin: S3 bucket (OAC se)
 *   3. Custom domain: cdn.myapp.com
 *   4. SSL: ACM certificate (us-east-1)
 *   5. Cache behavior configure karo
 *   6. Laravel mein ASSET_URL=https://cdn.myapp.com
 *
 * ⏱️ Waqt: ~20 minute
 */


// =============================================================================
// QADAM 11: CI/CD PIPELINE
// =============================================================================

/*
 * (Detail: cicd_pipeline/cicd_pipeline.php aur git_actions/git_actions.php)
 *
 * Kya karna hai:
 *   1. .github/workflows/deploy.yml banayen
 *   2. GitHub Secrets mein AWS credentials daalein
 *   3. Pipeline: Test → Build → Deploy
 *   4. Branch protection rules lagao
 *   5. Staging environment setup karo
 *
 * ⏱️ Waqt: ~30 minute
 */


// =============================================================================
// QADAM 12: MONITORING AUR ALERTS
// =============================================================================

/*
 * AWS CLOUDWATCH:
 * ───────────────
 *   1. EC2 Monitoring:
 *      - CPU Utilization > 80% → Alert
 *      - Memory > 85% → Alert (custom metric chahiye)
 *      - Disk > 90% → Alert
 *
 *   2. RDS Monitoring:
 *      - CPU > 70% → Alert
 *      - Free Storage < 5 GB → Alert
 *      - Database connections > 80% of max → Alert
 *
 *   3. ALB Monitoring:
 *      - 5xx errors > 10/minute → Alert
 *      - Response time > 3 seconds → Alert
 *      - Unhealthy host count > 0 → Alert
 *
 *   4. Application Logging:
 *      - Laravel logs → CloudWatch Logs
 *      - Nginx access/error logs → CloudWatch Logs
 *
 *
 * SETUP:
 *   CloudWatch → Alarms → Create alarm
 *   SNS Topic banayen: "production-alerts"
 *   Email subscribe karein
 *   Alarm action: SNS topic par notify karo
 */


// =============================================================================
// POORI ARCHITECTURE DIAGRAM
// =============================================================================

/*
 *
 *   ┌──────────────────────────────────────────────────────────────────────┐
 *   │                                                                      │
 *   │   [Users]                                                           │
 *   │      │                                                              │
 *   │      ▼                                                              │
 *   │   [Route 53 — DNS]  myapp.com → ALB, cdn.myapp.com → CloudFront    │
 *   │      │                    │                                         │
 *   │      ▼                    ▼                                         │
 *   │   [ALB + SSL]      [CloudFront]                                     │
 *   │   (HTTPS)           (Static CDN)                                    │
 *   │      │                    │                                         │
 *   │      ▼                    ▼                                         │
 *   │   ┌──────────┐     [S3 Bucket]                                     │
 *   │   │ Auto     │     (Images, CSS, JS)                               │
 *   │   │ Scaling  │                                                      │
 *   │   │ Group    │                                                      │
 *   │   │          │                                                      │
 *   │   │ [EC2 #1] │──→ [RDS MySQL]     ← Primary + Read Replica        │
 *   │   │ [EC2 #2] │──→ [ElastiCache]   ← Redis (Cache + Sessions)      │
 *   │   │ [EC2 #3] │──→ [SQS]           ← Queue Jobs                    │
 *   │   └──────────┘                                                      │
 *   │                                                                      │
 *   │   [CloudWatch]  ← Monitoring + Alerts                               │
 *   │   [GitHub Actions] ← CI/CD Pipeline                                 │
 *   │                                                                      │
 *   └──────────────────────────────────────────────────────────────────────┘
 *
 *
 * ESTIMATED MONTHLY COST (Chhoti Production App):
 * ──────────────────────────────────────────────
 *
 * ┌──────────────────────────────────────────────┐
 * │  Service              │  Qeemat/Month         │
 * ├──────────────────────────────────────────────┤
 * │  EC2 (2x t3.medium)  │  ~$60                 │
 * │  RDS (db.t3.medium)  │  ~$50                 │
 * │  ElastiCache (t3.small)│  ~$25               │
 * │  ALB                  │  ~$25                 │
 * │  S3 + CloudFront     │  ~$5-20               │
 * │  Route 53            │  ~$1                   │
 * │  NAT Gateway         │  ~$35                  │
 * │  Data Transfer        │  ~$10-50              │
 * ├──────────────────────────────────────────────┤
 * │  TOTAL                │  ~$210-270/month      │
 * └──────────────────────────────────────────────┘
 *
 * ⚠️ Paisa bachane ke tips:
 *   - Reserved Instances khareedein (30-60% bachat)
 *   - Dev/staging environments raat ko band karo
 *   - S3 lifecycle rules lagao
 *   - CloudFront caching optimize karo
 *   - NAT Gateway ki jagah NAT Instance use karo (sasta)
 *
 *
 * DEPLOYMENT CHECKLIST:
 * ─────────────────────
 *   ✅ VPC + Subnets + Security Groups banayen
 *   ✅ RDS database chalu hai aur accessible hai
 *   ✅ ElastiCache Redis chalu hai
 *   ✅ S3 bucket bana hai aur permissions sahi hain
 *   ✅ EC2/ECS par Laravel deploy hai
 *   ✅ Nginx configure hai aur chal raha hai
 *   ✅ Queue workers Supervisor se chal rahe hain
 *   ✅ ALB bana hai aur healthy targets hain
 *   ✅ SSL certificate laga hai (HTTPS chal raha hai)
 *   ✅ Domain link hai aur resolve ho raha hai
 *   ✅ CloudFront static assets serve kar raha hai
 *   ✅ CI/CD pipeline kaam kar raha hai
 *   ✅ CloudWatch alarms lage hain
 *   ✅ Backups enable hain (RDS + S3)
 *   ✅ APP_DEBUG=false hai
 *   ✅ .env file git mein nahi hai
 *   ✅ Auto Scaling Group configure hai
 *   ✅ Health check endpoints kaam kar rahe hain
 */
