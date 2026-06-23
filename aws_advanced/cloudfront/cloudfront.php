<?php

/**
 * ============================================================================
 *                    AWS CLOUDFRONT — MUKAMMAL GUIDE
 *              CDN Setup, S3 ke sath, Laravel Integration
 * ============================================================================
 */


// =============================================================================
// 1. CLOUDFRONT KYA HAI?
// =============================================================================

/*
 * CloudFront AWS ka CDN (Content Delivery Network) hai.
 *
 * AASAN MISAAL:
 * ─────────────
 *   Bina CDN:  User Pakistan mein → Request US server tak jaati hai → DHEEMA (500ms)
 *   CDN se:    User Pakistan mein → Qareeb ka CDN edge server → TEZ (50ms)
 *
 * Kaise kaam karta hai:
 *
 *   User (Lahore) ──→ CloudFront Edge (Mumbai) ──→ S3/EC2 (US)
 *                     [Cached copy yahan hai]      [Original yahan hai]
 *
 *   Pehli baar: CloudFront original se file lata hai aur apne paas rakhta hai
 *   Baad mein:  Seedha CloudFront se milti hai — bohot tez!
 *
 *
 * CLOUDFRONT KYA KYA KAR SAKTA HAI:
 *   - Static files (images, CSS, JS) tezi se serve karna
 *   - S3 bucket ke samne CDN laga ke speed barhana
 *   - SSL/HTTPS free mein dena (AWS Certificate Manager se)
 *   - DDoS protection (AWS Shield se)
 *   - Custom domain lagana
 *   - Geo-restriction (kuch mulkon se access rok do)
 *
 *
 * CLOUDFRONT KI DUNIYA BHAR MEIN JAGAHEIN:
 *
 *   AWS ke 400+ edge locations hain duniya bhar mein:
 *   - North America: 100+
 *   - Europe: 80+
 *   - Asia: 60+
 *   - South America: 20+
 *   - Middle East: 10+
 *   - Africa: 5+
 *
 *   Matlab: User jahin bhi ho, content qareeb se milta hai!
 */


// =============================================================================
// 2. CLOUDFRONT DISTRIBUTION BANANA
// =============================================================================

/*
 * QADAM 1: AWS Console → CloudFront → "Create distribution"
 *
 * QADAM 2: Origin Settings (Data kahan se aaye ga):
 *
 *   a) S3 Bucket ke liye:
 *      - Origin domain: my-laravel-app-storage.s3.ap-south-1.amazonaws.com
 *      - Origin access: "Origin access control settings (recommended)"
 *      - Create new OAC: Name "my-app-oac"
 *      - ⚠️ Iske baad S3 bucket policy update karni hogi (console bata dega)
 *
 *   b) EC2/ALB ke liye (Dynamic content):
 *      - Origin domain: my-load-balancer-1234.ap-south-1.elb.amazonaws.com
 *      - Protocol: HTTPS only
 *      - HTTP port: 80
 *
 *
 * QADAM 3: Cache Behavior Settings:
 *
 *   ┌──────────────────────────────────────────────────────────────────┐
 *   │  Setting              │  Value                  │  Wazahat       │
 *   ├──────────────────────────────────────────────────────────────────┤
 *   │  Viewer protocol      │  Redirect HTTP to HTTPS │  Hamesha HTTPS │
 *   │  Allowed HTTP methods │  GET, HEAD              │  Static ke liye│
 *   │  Cache policy         │  CachingOptimized       │  Static files  │
 *   │  Compress objects     │  Yes                    │  Gzip/Brotli   │
 *   │  Default TTL          │  86400 (24 ghante)      │  Cache kitni der│
 *   └──────────────────────────────────────────────────────────────────┘
 *
 *
 * QADAM 4: Distribution Settings:
 *
 *   - Price class: "Use all edge locations" (ya sasti ke liye sirf US/Europe)
 *   - Alternate domain: cdn.yourdomain.com (baad mein setup karein ge)
 *   - SSL Certificate: Custom SSL Certificate (ACM se free)
 *   - Default root object: index.html (static sites ke liye)
 *
 *
 * QADAM 5: Create distribution
 *   - Status "Deploying" dikhega — 5-15 minute lagte hain
 *   - Distribution domain milega: d1234abcdef.cloudfront.net
 */


// =============================================================================
// 3. LARAVEL MEIN CLOUDFRONT USE KARNA
// =============================================================================

// .env mein add karo:
/*
 *   AWS_CLOUDFRONT_URL=https://d1234abcdef.cloudfront.net
 *   # Ya custom domain:
 *   # AWS_CLOUDFRONT_URL=https://cdn.yourdomain.com
 */

// config/filesystems.php mein S3 config update karo:
$s3ConfigWithCDN = [
    's3' => [
        'driver' => 's3',
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
        'url'    => env('AWS_CLOUDFRONT_URL'),  // CloudFront URL yahan daalein
    ],
];

// Ab Storage::url() CloudFront URL dega:
$imageUrl = Storage::disk('s3')->url('uploads/images/photo.jpg');
// Result: https://d1234abcdef.cloudfront.net/uploads/images/photo.jpg
// Ya:     https://cdn.yourdomain.com/uploads/images/photo.jpg


// Blade template mein:
/*
 *   <img src="{{ Storage::disk('s3')->url($user->avatar) }}" alt="Avatar">
 *
 *   Ya agar ASSET_URL set ki hai:
 *   <img src="{{ asset('images/logo.png') }}" alt="Logo">
 */


// =============================================================================
// 4. CACHE INVALIDATION (Purana Cache Saaf Karna)
// =============================================================================

/*
 * Jab aap S3 par file update karein, CloudFront purana cached version serve karta rehta hai.
 * Cache saaf karne ke liye "Invalidation" create karein:
 *
 * AWS Console se:
 *   CloudFront → Distributions → Apni distribution → Invalidations → Create
 *   - Path: /uploads/images/photo.jpg    (ek file)
 *   - Path: /uploads/images/*            (poora folder)
 *   - Path: /*                           (sab kuch — ⚠️ mahnga!)
 *
 * AWS CLI se:
 *   aws cloudfront create-invalidation \
 *       --distribution-id E1234ABCDEF \
 *       --paths "/uploads/images/photo.jpg"
 *
 * ⚠️ Pehli 1000 invalidation paths free hain per month
 *    Uske baad $0.005 per path
 *
 * BEHTAR TAREEQA — Versioned File Names:
 *   Invalidation ki zaroorat hi nahi agar file name mein version daalein:
 *   - photo.jpg → photo-v2.jpg
 *   - style.css → style.abc123.css (Laravel Mix/Vite yeh khud karta hai)
 */


// =============================================================================
// 5. CUSTOM DOMAIN SETUP (cdn.yourdomain.com)
// =============================================================================

/*
 * QADAM 1: ACM (AWS Certificate Manager) mein SSL certificate banayen
 *   - ⚠️ ZAROOR us-east-1 (N. Virginia) region mein banayen!
 *     (CloudFront SIRF us-east-1 ke certificates accept karta hai)
 *   - Domain: cdn.yourdomain.com
 *   - Validation: DNS validation (Route 53 se automatic)
 *
 * QADAM 2: CloudFront distribution mein:
 *   - Alternate domain names: cdn.yourdomain.com
 *   - Custom SSL certificate: Apna ACM certificate select karein
 *
 * QADAM 3: DNS mein CNAME record banayein:
 *   - Name: cdn
 *   - Type: CNAME
 *   - Value: d1234abcdef.cloudfront.net
 *
 * Ab cdn.yourdomain.com se CloudFront serve karega!
 */


// =============================================================================
// 6. CLOUDFRONT BEST PRACTICES
// =============================================================================

/*
 * ✅ Static assets (images, CSS, JS) ke liye CloudFront use karo
 * ✅ S3 bucket private rakhho, CloudFront OAC se access do
 * ✅ Compression enable karo (Gzip/Brotli)
 * ✅ Custom domain + SSL use karo
 * ✅ Cache headers sahi set karo (Cache-Control, max-age)
 * ✅ Versioned file names use karo invalidation se bachne ke liye
 *
 * ❌ Dynamic API responses CloudFront se mat serve karo (jab tak zaroorat na ho)
 * ❌ Har deployment par /* invalidation mat karo (mahnga hai)
 * ❌ S3 bucket direct public mat karo — CloudFront ke peeche rakhho
 */
