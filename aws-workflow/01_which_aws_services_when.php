<?php

/**
 * ============================================================================
 *     KONSI AWS SERVICE, KAB USE KARNI HAI? — LARAVEL PROJECT KE LIYE
 * ============================================================================
 *
 * Boss ne bola "bara Laravel project hai" — is file mein har AWS service ka
 * KAAM, KAB ZAROORAT PARTI HAI, aur Laravel ke sath kaise jurti hai — sab
 * explain kiya gaya hai. Categories mein divide kiya hai taake confuse na ho.
 */


// =============================================================================
// CATEGORY 1: COMPUTE (Jahan Laravel application CHALTI hai)
// =============================================================================

/*
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ Service          │ Kya Hai                  │ Kab Use Karein            │
 * ├─────────────────────────────────────────────────────────────────────────┤
 * │ EC2               │ Virtual server (VM)       │ Full control chahiye,    │
 * │                    │ jahan tum khud sab        │ team ko server admin     │
 * │                    │ install/configure karo    │ ka experience hai        │
 * ├─────────────────────────────────────────────────────────────────────────┤
 * │ ECS Fargate        │ Docker containers,        │ Docker use kar rahe ho,  │
 * │                    │ server manage karne ki    │ scaling automatic chahiye│
 * │                    │ zaroorat nahi (serverless)│ Production-grade setup   │
 * ├─────────────────────────────────────────────────────────────────────────┤
 * │ Elastic Beanstalk  │ AWS khud EC2+LB+ASG       │ Jaldi deploy karna ho,   │
 * │                    │ manage karta hai           │ infra ki tafseel mein   │
 * │                    │                            │ jaana nahi              │
 * ├─────────────────────────────────────────────────────────────────────────┤
 * │ Lightsail          │ Sasta, simple VPS          │ Chhota project, MVP,    │
 * │                    │                            │ kam budget               │
 * └─────────────────────────────────────────────────────────────────────────┘
 *
 * BORE-KE-LIYE BARA PROJECT KE LIYE RECOMMENDATION:
 *   → EC2 (Auto Scaling Group ke sath) AGAR team server manage kar sakti hai
 *   → ECS Fargate AGAR Docker-based, hands-off scaling chahiye
 *   (Dono detail: aws_advanced/ec2/ec2.php, aws_advanced/docker/docker.php)
 */


// =============================================================================
// CATEGORY 2: DATABASE
// =============================================================================

/*
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ Service           │ Kya Hai                  │ Kab Use Karein            │
 * ├─────────────────────────────────────────────────────────────────────────┤
 * │ RDS (MySQL/        │ Managed database —       │ DEFAULT CHOICE har Laravel│
 * │ PostgreSQL)        │ AWS backup, patching,    │ project ke liye. Backups, │
 * │                    │ failover khud sambhalta  │ Multi-AZ, Read Replicas   │
 * │                    │ hai                       │ available hain            │
 * ├─────────────────────────────────────────────────────────────────────────┤
 * │ Aurora             │ RDS ka AWS-optimized      │ Bohot zyada traffic,     │
 * │                    │ version — tez aur         │ high-availability        │
 * │                    │ zyada reliable             │ critical ho, budget ho   │
 * ├─────────────────────────────────────────────────────────────────────────┤
 * │ Aurora Serverless  │ Database jo khud scale    │ Traffic UNPREDICTABLE ho │
 * │                    │ hoti hai (up/down)        │ (kabhi spike, kabhi idle)│
 * └─────────────────────────────────────────────────────────────────────────┘
 *
 * BARA PROJECT RECOMMENDATION:
 *   → RDS MySQL (db.t3.medium se start, Multi-AZ ENABLE karo production mein)
 *   → Traffic bohot bara ho to Aurora pe migrate karo baad mein
 *   (Detail: aws_advanced/rds_database/rds_database.php)
 */


// =============================================================================
// CATEGORY 3: CACHING / SESSIONS / QUEUES — REDIS / ELASTICACHE
// =============================================================================

/*
 * ElastiCache = AWS ki MANAGED Redis/Memcached service.
 * (Khud EC2 par Redis install karne ke bajaye, AWS isay manage karta hai)
 *
 * LARAVEL MEIN REDIS 3 ALAG KAAMON KE LIYE USE HOTA HAI:
 *
 * 1. CACHE_DRIVER=redis
 *    → Query results, computed values cache karne ke liye
 *    → Kab zaroorat: Jab DB par baar baar same query load par hai
 *
 * 2. SESSION_DRIVER=redis
 *    → User sessions store karne ke liye
 *    → Kab zaroorat: Jab MULTIPLE EC2 servers hon (Load Balancer ke peeche)
 *      Agar session file mein store ho aur 2 servers hon, to user
 *      KABHI Server A KABHI Server B par jayega — login TOOT JAYEGA!
 *      Redis SHARED session store deta hai — ye production mein ZAROORI hai
 *      jaise hi tum 1 se zyada server use karte ho.
 *
 * 3. QUEUE_CONNECTION=redis
 *    → Background jobs (email, notification, processing) ke liye
 *    → Kab zaroorat: Jab time-consuming kaam (PDF, email, image processing)
 *      ko background mein bhejna ho (dekho: event-listener/ folder example)
 *
 * KAB ELASTICACHE (Managed Redis) USE KARO vs KHUD EC2 PAR REDIS INSTALL:
 *   → BARA/PRODUCTION project: HAMESHA ElastiCache (managed, auto-backup,
 *     auto-failover, AWS khud patch karta hai — tumhe sirf endpoint
 *     .env mein daalni hai)
 *   → Chhota/dev project: EC2 par khud Redis chala sakte ho (sasta)
 *
 * (Detail: redis/redis.php — agar exist na kare to ElastiCache console
 *  docs follow karo: cache.t3.micro se start karo)
 */


// =============================================================================
// CATEGORY 4: FILE STORAGE — S3
// =============================================================================

/*
 * S3 = User uploads (images, PDFs, videos, documents) store karne ke liye.
 *
 * KAB ZAROORI HAI:
 *   - User profile pictures, product images upload hote hain
 *   - Invoice/PDF generate hoti hain
 *   - MULTIPLE servers (Load Balancer ke peeche) hain — agar files
 *     EC2 ki local disk par save karo, to Server A par upload hone wali
 *     file Server B par DIKHEGI HI NAHI! S3 = SHARED storage solution.
 *
 * LARAVEL SETUP:
 *   composer require league/flysystem-aws-s3-v3
 *   FILESYSTEM_DISK=s3 (.env mein)
 *
 * (Detail: aws_advanced/s3/s3.php)
 */


// =============================================================================
// CATEGORY 5: CDN — CLOUDFRONT
// =============================================================================

/*
 * CloudFront = CDN (Content Delivery Network) — static files (CSS, JS,
 * Images) ko DUNYA BHAR ke "edge locations" se SERVE karta hai, taake
 * user APNE QAREEB wale server se file le, na ke seedha Pakistan/US
 * wale server se (FAST loading).
 *
 * KAB ZAROORI HAI:
 *   - User base MULTIPLE COUNTRIES/REGIONS mein hai
 *   - Bohot saari images/videos serve ho rahi hain
 *   - S3 ke direct access se SLOW load ho raha hai
 *
 * KAB ZAROORI NAHI (skip kar sakte ho):
 *   - Sirf EK country/region ke users hain (jaise sirf Pakistan)
 *   - Chhota MVP project hai, abhi optimize karne ki zaroorat nahi
 *
 * (Detail: aws_advanced/cloudfront/cloudfront.php)
 */


// =============================================================================
// CATEGORY 6: LOAD BALANCING + SCALING
// =============================================================================

/*
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ ALB (Application    │ HTTP/HTTPS traffic ko multiple EC2 servers ke    │
 * │ Load Balancer)      │ beech distribute karta hai                       │
 * ├─────────────────────────────────────────────────────────────────────────┤
 * │ Auto Scaling Group  │ Traffic badhne par AUTOMATICALLY naye EC2        │
 * │                      │ servers add karta hai, kam hone par hata deta   │
 * └─────────────────────────────────────────────────────────────────────────┘
 *
 * KAB ZAROORI HAI:
 *   - "BARA project" ka matlab hai ye 99% ZAROORI hai — agar EK hi server
 *     down ho jaye to poora app down ho jayega. ALB + ASG = HIGH AVAILABILITY
 *
 * (Detail: aws_advanced/load_balancing/load_balancing.php)
 */


// =============================================================================
// CATEGORY 7: NETWORKING — VPC
// =============================================================================

/*
 * VPC = Tumhara PRIVATE NETWORK AWS ke andar — jahan EC2, RDS, ElastiCache
 * sab ek dusre se SECURELY baat karte hain, bahar ki duniya se ISOLATED.
 *
 * KAB ZAROORI HAI: HAMESHA — production project ke liye VPC setup karna
 * BUNYADI (fundamental) step hai. Database/Redis ko PUBLIC internet se
 * isolate karna security ke liye zaroori hai.
 *
 * (Detail: aws_advanced/vpc_networking/vpc_networking.php)
 */


// =============================================================================
// CATEGORY 8: DNS + SSL
// =============================================================================

/*
 * Route 53  → Domain ka DNS management (myapp.com → server ka IP/ALB)
 * ACM       → FREE SSL certificate (HTTPS lagane ke liye)
 *
 * KAB ZAROORI HAI: HAMESHA — bina HTTPS ke production app chalana
 * security risk hai (browsers warning dikhate hain, SEO bhi affect hota hai)
 *
 * (Detail: aws_advanced/domain_setup/domain_setup.php, aws_advanced/ssl/ssl.php)
 */


// =============================================================================
// CATEGORY 9: QUEUE (Heavy Background Jobs ke liye)
// =============================================================================

/*
 * SQS (Simple Queue Service) vs REDIS QUEUE:
 *
 * REDIS QUEUE use karo jab:
 *   - Already ElastiCache use kar rahe ho
 *   - Jobs ki history Laravel Horizon se dekhni ho (best monitoring UI)
 *
 * SQS use karo jab:
 *   - EXTREMELY high volume jobs hain (millions/day)
 *   - Multiple ALAG services (microservices) ek hi queue share kar rahe hon
 *   - Guaranteed delivery + dead-letter-queue chahiye
 *
 * BARA LARAVEL PROJECT KE LIYE: Redis Queue + Laravel Horizon
 * (zyada simple, zyada Laravel-native, monitoring built-in)
 */


// =============================================================================
// CATEGORY 10: EMAIL — SES
// =============================================================================

/*
 * SES (Simple Email Service) = Transactional emails bhejne ke liye
 * (welcome email, password reset, order confirmation — dekho event-listener/ folder)
 *
 * KAB ZAROORI HAI: Jab bhi Laravel se email bhejni ho production mein.
 * Gmail SMTP use NA karo production mein — limits hain aur spam-flag hone
 * ka risk hai. SES sasta hai ($0.10 per 1000 emails) aur reliable hai.
 */


// =============================================================================
// CATEGORY 11: MONITORING + SECURITY
// =============================================================================

/*
 * CloudWatch       → Server CPU/Memory/Disk, RDS health, ALB errors — sab
 *                     monitor karta hai, alerts bhejta hai
 * Secrets Manager   → .env ke sensitive values (DB password, API keys)
 *                     securely store karne ke liye (.env file mein direct
 *                     hardcode karne se zyada secure)
 * IAM               → Kis user/service ko KYA access hai — least privilege
 *                      principle follow karo (sirf utna access do jitni zaroorat hai)
 * WAF               → Web Application Firewall — SQL injection, bot attacks
 *                      se bachata hai (BARA project ke liye recommended)
 *
 * KAB ZAROORI HAI: CloudWatch + IAM → HAMESHA (din 1 se)
 * Secrets Manager + WAF → Project bara/sensitive ho (payment, user data) to ZAROORI
 */


// =============================================================================
// QUICK DECISION TABLE — PROJECT SIZE KE HISAB SE
// =============================================================================

/*
 * ┌────────────────────┬───────────────────────────────────────────────────┐
 * │ Project Stage       │ Services Use Karo                                 │
 * ├────────────────────┼───────────────────────────────────────────────────┤
 * │ MVP / Startup        │ Lightsail/Single EC2, RDS (single AZ), S3,        │
 * │ (kam traffic)        │ Route53, ACM. ElastiCache/CloudFront SKIP karo    │
 * ├────────────────────┼───────────────────────────────────────────────────┤
 * │ Growing Business     │ EC2 (2+) + ALB + Auto Scaling, RDS Multi-AZ,      │
 * │ (medium traffic)     │ ElastiCache Redis, S3 + CloudFront, SES           │
 * ├────────────────────┼───────────────────────────────────────────────────┤
 * │ Large/Enterprise     │ ECS Fargate/EKS, Aurora (Multi-AZ + Read          │
 * │ (bara, high-traffic) │ Replicas), ElastiCache Cluster mode, CloudFront,  │
 * │                      │ SQS, WAF, Secrets Manager, Multi-region (agar    │
 * │                      │ zaroorat ho)                                      │
 * └────────────────────┴───────────────────────────────────────────────────┘
 *
 * Boss ka "bara project" agar abhi NAYA hai (users kam hain), to "Growing
 * Business" column se start karo — seedha "Large/Enterprise" pe mat jao,
 * warna bewajah zyada paisa aur complexity lagegi (Over-engineering).
 * Jaise jaise traffic badhe, scale karte raho (dekho: 05_scaling_roadmap_and_cost.php)
 */
