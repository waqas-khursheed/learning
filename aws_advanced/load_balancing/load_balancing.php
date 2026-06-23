<?php

/**
 * ============================================================================
 *                    AWS LOAD BALANCING — MUKAMMAL GUIDE
 *        Traffic Baantna, Auto Scaling, aur High Availability
 * ============================================================================
 */


// =============================================================================
// 1. LOAD BALANCING KYA HAI?
// =============================================================================

/*
 * Load Balancer = Traffic ka traffic warden
 * Users ki requests ko kai servers mein BARABAR baant deta hai.
 *
 * AASAN MISAAL:
 * ─────────────
 *   Bina Load Balancer:
 *     1000 users → [Ek Server] → Server bhar gaya → CRASH! 💥
 *
 *   Load Balancer ke sath:
 *     1000 users → [Load Balancer] → Server A (333 users)
 *                                  → Server B (333 users)
 *                                  → Server C (334 users)
 *                                  Sab smooth! ✅
 *
 *
 * LOAD BALANCER KYA KAAM KARTA HAI:
 * ─────────────────────────────────
 *   1. Traffic barabar baantna (Round Robin, Least Connections)
 *   2. Health Check — agar server mara toh traffic wahan bhejne band karo
 *   3. SSL Termination — HTTPS yahan handle ho, servers ko HTTP bhejo
 *   4. Session Persistence — ek user hamesha ek hi server par jaye (sticky sessions)
 *
 *
 * FLOW:
 *
 *   Users (Internet)
 *       │
 *       ▼
 *   [Application Load Balancer]
 *       │
 *       ├──→ [EC2 Instance 1 — Laravel]  (Healthy ✅)
 *       ├──→ [EC2 Instance 2 — Laravel]  (Healthy ✅)
 *       └──→ [EC2 Instance 3 — Laravel]  (Unhealthy ❌ → Traffic nahi bhejega)
 */


// =============================================================================
// 2. AWS LOAD BALANCER KI QISMAIN
// =============================================================================

/*
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │  Type                       │  Kab Use Karein                          │
 * ├─────────────────────────────────────────────────────────────────────────┤
 * │  Application LB (ALB)       │  Web apps (HTTP/HTTPS) — TAVSIYA        │
 * │  Network LB (NLB)           │  Ultra-tez TCP/UDP traffic               │
 * │  Gateway LB (GWLB)          │  Third-party appliances                  │
 * │  Classic LB (CLB)           │  Purana — ab mat use karo                │
 * └─────────────────────────────────────────────────────────────────────────┘
 *
 * LARAVEL KE LIYE: ALB (Application Load Balancer) use karo!
 *
 *
 * ALB KI KHAS BAATEIN:
 * ────────────────────
 *   - HTTP/HTTPS traffic ke liye bana hai
 *   - Path-based routing: /api/* → API servers, /admin/* → Admin servers
 *   - Host-based routing: api.domain.com → API servers, www.domain.com → Web servers
 *   - SSL certificate attach kar sakte hain (ACM se free)
 *   - WebSocket support
 *   - Health checks (har server ki sehat check karta hai)
 */


// =============================================================================
// 3. ALB SETUP KARNA (Step-by-Step)
// =============================================================================

/*
 * QADAM 1: Target Group banayen (servers ka group)
 * ─────────────────────────────────────────────────
 *   EC2 → Target Groups → "Create target group"
 *
 *   - Target type: Instances
 *   - Name: my-laravel-targets
 *   - Protocol: HTTP
 *   - Port: 80
 *   - VPC: Apna VPC
 *
 *   Health Check:
 *   - Path: /health (ya /)
 *   - Healthy threshold: 3 (3 baar pass = healthy)
 *   - Unhealthy threshold: 3 (3 baar fail = unhealthy)
 *   - Interval: 30 seconds
 *
 *   Targets register karo:
 *   - Apne EC2 instances select karein
 *   - "Include as pending" dabayein
 *
 *
 * QADAM 2: Load Balancer banayen
 * ──────────────────────────────
 *   EC2 → Load Balancers → "Create" → Application Load Balancer
 *
 *   a) Basic Configuration:
 *      - Name: my-laravel-alb
 *      - Scheme: Internet-facing (public traffic ke liye)
 *      - IP address type: IPv4
 *
 *   b) Network:
 *      - VPC: Apna VPC
 *      - Subnets: Kam az kam 2 AZs select karein
 *        (maslan: ap-south-1a aur ap-south-1b)
 *
 *   c) Security Group:
 *      - Naya banayen ya purana chunen
 *      - Inbound: HTTP (80) aur HTTPS (443) — 0.0.0.0/0 se
 *
 *   d) Listeners:
 *      - HTTP:80 → Default action: Forward to my-laravel-targets
 *      - HTTPS:443 → Forward to my-laravel-targets
 *        (SSL certificate ACM se select karo)
 *
 *   e) HTTP → HTTPS Redirect:
 *      - HTTP:80 listener edit karo
 *      - Action: Redirect to HTTPS:443
 *      - Status code: 301 (permanent redirect)
 *
 *
 * QADAM 3: DNS Update karo
 * ────────────────────────
 *   Route 53 ya apne DNS provider mein:
 *   - yourdomain.com → ALB ka DNS name (ALIAS/CNAME record)
 *   - ALB DNS: my-laravel-alb-1234567.ap-south-1.elb.amazonaws.com
 */


// =============================================================================
// 4. LARAVEL MEIN HEALTH CHECK ENDPOINT
// =============================================================================

// routes/web.php mein add karo:
/*
 *   Route::get('/health', function () {
 *       try {
 *           // Database check
 *           DB::connection()->getPdo();
 *
 *           // Redis check
 *           Redis::ping();
 *
 *           return response()->json([
 *               'status' => 'healthy',
 *               'timestamp' => now()->toISOString(),
 *           ], 200);
 *       } catch (\Exception $e) {
 *           return response()->json([
 *               'status' => 'unhealthy',
 *               'error' => $e->getMessage(),
 *           ], 503);
 *       }
 *   });
 */

// LARAVEL TRUSTED PROXIES (ALB ke peeche zaruri hai):
// app/Http/Middleware/TrustProxies.php:
/*
 *   protected $proxies = '*';  // Sab proxies trust karo (ALB ke liye zaruri)
 *
 *   // Ya .env mein:
 *   TRUSTED_PROXIES=*
 */


// =============================================================================
// 5. AUTO SCALING GROUP (Khud-ba-khud Scale)
// =============================================================================

/*
 * Auto Scaling = Traffic barhay toh servers barhao, ghatay toh ghatao
 *
 *   Subah 9 baje:  100 users   → 2 servers kafi hain
 *   Dopahar 1 baje: 5000 users → 8 servers chahiye → Auto Scaling 6 naye daalta hai!
 *   Raat 11 baje:  50 users    → 2 servers → Extra 6 band kar deta hai (paisa bachta hai)
 *
 *
 * SETUP:
 *
 * QADAM 1: Launch Template banayen
 *   EC2 → Launch Templates → Create
 *   - Name: my-laravel-template
 *   - AMI: Apna configured AMI (Laravel install shuda)
 *   - Instance type: t3.medium
 *   - Key pair: apna key pair
 *   - Security group: app security group
 *   - User data script (instance shuru hone par chale):
 *
 *     #!/bin/bash
 *     cd /var/www/my-laravel-app
 *     git pull origin main
 *     composer install --no-dev --optimize-autoloader
 *     php artisan config:cache
 *     php artisan route:cache
 *     sudo systemctl restart php8.2-fpm
 *     sudo systemctl restart nginx
 *
 *
 * QADAM 2: Auto Scaling Group banayen
 *   EC2 → Auto Scaling Groups → Create
 *   - Name: my-laravel-asg
 *   - Launch template: my-laravel-template
 *   - VPC: Apna VPC
 *   - Subnets: Multiple AZs select karo
 *   - Load balancer: Apna ALB target group attach karo
 *
 *   Capacity:
 *   - Minimum: 2 (kam az kam 2 servers hamesha)
 *   - Desired: 2 (abhi 2 chahiye)
 *   - Maximum: 10 (zyada se zyada 10 tak)
 *
 *   Scaling Policy:
 *   - Target tracking: CPU 70% par naya server daalein
 *   - Ya request count: 1000 requests per target par naya server
 *
 *
 * AUTO SCALING POLICIES:
 * ──────────────────────
 *
 *   1) Target Tracking (TAVSIYA):
 *      - CPU 70% se zyada ho → Naya server daalein
 *      - CPU 30% se kam ho → Server hata do
 *
 *   2) Step Scaling:
 *      - CPU 60-70% → 1 server daalein
 *      - CPU 70-80% → 2 server daalein
 *      - CPU 80%+   → 3 server daalein
 *
 *   3) Scheduled Scaling:
 *      - Har subah 9 baje → 5 servers
 *      - Har raat 10 baje → 2 servers
 *      - Black Friday → 20 servers
 *
 *
 * COMPLETE ARCHITECTURE:
 *
 *   Internet
 *     │
 *     ▼
 *   [Route 53 — DNS]
 *     │
 *     ▼
 *   [CloudFront — CDN]
 *     │
 *     ▼
 *   [ALB — Load Balancer]
 *     │
 *     ├──→ [EC2 #1] ──→ [RDS — Primary]
 *     ├──→ [EC2 #2] ──→ [RDS — Replica]
 *     └──→ [EC2 #3] ──→ [ElastiCache — Redis]
 *     (Auto Scaling Group)
 */


// =============================================================================
// 6. LOAD BALANCING BEST PRACTICES
// =============================================================================

/*
 * ✅ Kam az kam 2 AZs mein servers rakhho
 * ✅ Health checks zaroor configure karo
 * ✅ HTTPS enable karo ALB par (ACM se free SSL)
 * ✅ HTTP → HTTPS redirect lagao
 * ✅ Auto Scaling use karo (haath se scale mat karo)
 * ✅ Connection draining enable karo (server hatane se pehle purani requests mukammal hon)
 * ✅ Access logs enable karo (S3 mein)
 * ✅ Laravel mein TRUSTED_PROXIES set karo
 *
 * ❌ Ek hi AZ mein sab servers mat rakhho
 * ❌ Health check path ghalat mat rakhho
 * ❌ Auto Scaling minimum 1 mat rakhho (kam az kam 2 — failover ke liye)
 */
