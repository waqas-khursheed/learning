<?php

/**
 * ============================================================================
 *                    DOMAIN SETUP — MUKAMMAL GUIDE
 *        Route 53, DNS Records, aur Domain Link karna
 * ============================================================================
 */


// =============================================================================
// 1. DOMAIN KAISE LINK KAREIN AWS SE?
// =============================================================================

/*
 * FLOW:
 *   User browser mein likhta hai: www.myapp.com
 *       │
 *       ▼
 *   [DNS — Route 53] → "myapp.com ka IP kya hai?"
 *       │
 *       ▼
 *   [ALB / EC2 / CloudFront] → Website dikhao!
 *
 *
 * OPTION 1: AWS Route 53 (TAVSIYA)
 * ─────────────────────────────────
 *   - AWS ka apna DNS service
 *   - AWS services ke sath behtareen integration
 *   - Health checks + routing policies
 *   - Domain bhi khareed sakte hain
 *
 * OPTION 2: Bahari DNS Provider (GoDaddy, Namecheap, Cloudflare)
 * ──────────────────────────────────────────────────────────────
 *   - Domain pehle se khareed rakha hai
 *   - Nameservers Route 53 ki taraf point karo
 *   - Ya seedha CNAME/A records daalein
 */


// =============================================================================
// 2. ROUTE 53 SETUP (Step-by-Step)
// =============================================================================

/*
 * QADAM 1: Hosted Zone banayen
 *   Route 53 → Hosted zones → "Create hosted zone"
 *   - Domain name: myapp.com
 *   - Type: Public hosted zone
 *
 *   4 Nameservers milein ge (NS records):
 *   - ns-123.awsdns-45.com
 *   - ns-678.awsdns-90.net
 *   - ns-111.awsdns-22.org
 *   - ns-333.awsdns-44.co.uk
 *
 * QADAM 2: Domain provider mein nameservers update karein
 *   GoDaddy/Namecheap → Domain settings → Custom nameservers
 *   → Route 53 ke 4 nameservers daalein
 *   ⚠️ Propagation mein 24-48 ghante lag sakte hain
 *
 *
 * QADAM 3: DNS Records banayen:
 *
 * ┌────────────────────────────────────────────────────────────────────────┐
 * │  Record Type │  Name              │  Value                  │ Maqsad  │
 * ├────────────────────────────────────────────────────────────────────────┤
 * │  A (Alias)   │  myapp.com         │  ALB DNS name           │ Main    │
 * │  A (Alias)   │  www.myapp.com     │  ALB DNS name           │ www     │
 * │  CNAME       │  api.myapp.com     │  ALB DNS name           │ API     │
 * │  CNAME       │  cdn.myapp.com     │  CloudFront DNS         │ CDN     │
 * │  CNAME       │  admin.myapp.com   │  ALB DNS name           │ Admin   │
 * │  MX          │  myapp.com         │  Mail server            │ Email   │
 * │  TXT         │  myapp.com         │  SPF/DKIM records       │ Email   │
 * └────────────────────────────────────────────────────────────────────────┘
 *
 *
 * A RECORD vs CNAME vs ALIAS:
 * ───────────────────────────
 *   A Record: Domain → IP address (maslan: 54.123.45.67)
 *   CNAME:    Domain → Doosra domain (maslan: my-alb.elb.amazonaws.com)
 *   ALIAS:    Domain → AWS resource (A Record jaisa magar AWS DNS names ke liye)
 *
 *   ⚠️ Root domain (myapp.com) par CNAME nahi laga sakte!
 *      Root domain ke liye ALIAS record use karo (Route 53 ka khaas feature)
 *
 *
 * SEEDHA EC2 SE LINK KARNA (bina ALB ke):
 * ──────────────────────────────────────
 *   A Record: myapp.com → EC2 ka Elastic IP (54.123.45.67)
 *   ⚠️ Elastic IP zaruri hai — bina iske restart par IP badal jata hai
 */


// =============================================================================
// 3. MULTIPLE SUBDOMAINS SETUP
// =============================================================================

/*
 * ARCHITECTURE:
 *
 *   myapp.com          → Main website (Laravel)
 *   www.myapp.com      → Main website (redirect ya same)
 *   api.myapp.com      → API server (ya same ALB, path-based routing)
 *   admin.myapp.com    → Admin panel
 *   cdn.myapp.com      → Static files (CloudFront)
 *   staging.myapp.com  → Staging environment
 *
 *
 * ALB MEIN PATH/HOST-BASED ROUTING:
 *
 *   ALB Listener Rules:
 *   1. IF host = api.myapp.com    → Forward to API target group
 *   2. IF host = admin.myapp.com  → Forward to Admin target group
 *   3. IF path = /api/*           → Forward to API target group
 *   4. Default                    → Forward to Web target group
 */


// =============================================================================
// 4. LARAVEL MEIN DOMAIN CONFIGURATION
// =============================================================================

// .env mein:
/*
 *   APP_URL=https://myapp.com
 *   ASSET_URL=https://cdn.myapp.com
 *   SESSION_DOMAIN=.myapp.com        # Sab subdomains par session share
 */

// config/cors.php mein:
$corsConfig = [
    'paths' => ['api/*'],
    'allowed_origins' => [
        'https://myapp.com',
        'https://www.myapp.com',
        'https://admin.myapp.com',
    ],
    'allowed_methods' => ['*'],
    'allowed_headers' => ['*'],
];

// routes/web.php mein subdomain routing:
/*
 *   // Admin subdomain
 *   Route::domain('admin.myapp.com')->group(function () {
 *       Route::get('/', [AdminController::class, 'dashboard']);
 *   });
 *
 *   // API subdomain
 *   Route::domain('api.myapp.com')->group(function () {
 *       Route::get('/users', [UserController::class, 'index']);
 *   });
 */


// =============================================================================
// 5. DOMAIN SETUP BEST PRACTICES
// =============================================================================

/*
 * ✅ Route 53 use karo AWS services ke sath behtar integration ke liye
 * ✅ Wildcard SSL (*.myapp.com) banayen sab subdomains ke liye
 * ✅ www → non-www redirect lagao (ya ulta — ek consistent rakhho)
 * ✅ Elastic IP use karo agar seedha EC2 se link kar rahe ho
 * ✅ DNS propagation ka intezar karo (24-48 ghante tak lag sakte hain)
 * ✅ TTL chhota rakhho DNS changes ke dauran (300 seconds)
 *
 * ❌ EC2 ka dynamic IP domain se link mat karo (restart par badal jata hai)
 * ❌ DNS changes production mein bina testing ke mat karo
 */
