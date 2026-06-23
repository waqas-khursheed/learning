<?php

/**
 * ============================================================================
 *                    SSL / HTTPS — MUKAMMAL GUIDE
 *        AWS Certificate Manager, Domain SSL, aur Setup
 * ============================================================================
 */


// =============================================================================
// 1. SSL/HTTPS KYA HAI?
// =============================================================================

/*
 * SSL = Secure Sockets Layer (ab TLS = Transport Layer Security)
 *
 * AASAN MISAAL:
 * ─────────────
 *   HTTP  = Postcard — koi bhi raaste mein padh sakta hai
 *   HTTPS = Band lifafa — sirf bhejne aur pane wala padh sakta hai
 *
 * KYUN ZARURI HAI:
 *   ✅ Data encrypted hota hai (passwords, credit cards mehfooz)
 *   ✅ Google ranking mein behtar (SEO)
 *   ✅ Browser mein 🔒 lock icon dikhta hai (users ka bharosa)
 *   ✅ Modern features chahiye hain (HTTP/2, service workers)
 *   ❌ HTTP sites par Chrome "Not Secure" dikhata hai
 */


// =============================================================================
// 2. AWS CERTIFICATE MANAGER (ACM) — FREE SSL
// =============================================================================

/*
 * ACM = AWS Certificate Manager
 * AWS mein SSL certificates FREE milte hain!
 *
 *   ⚠️ Sirf AWS services ke sath free (ALB, CloudFront, API Gateway)
 *   ⚠️ EC2 par seedha use nahi kar sakte (ALB ke peeche rakhho)
 *
 *
 * SSL CERTIFICATE BANANA:
 * ───────────────────────
 *
 * QADAM 1: AWS Console → Certificate Manager → "Request"
 *
 * QADAM 2: Certificate type:
 *   - "Request a public certificate"
 *
 * QADAM 3: Domain names add karein:
 *   - yourdomain.com
 *   - *.yourdomain.com (wildcard — sab subdomains ke liye)
 *   - Maslan: api.yourdomain.com, admin.yourdomain.com, cdn.yourdomain.com
 *
 * QADAM 4: Validation method:
 *   - DNS validation (TAVSIYA — Route 53 se automatic)
 *   - Email validation (domain owner ke email par)
 *
 * QADAM 5: DNS validation ke liye:
 *   - ACM ek CNAME record dega
 *   - Yeh record apne DNS mein add karo
 *   - Route 53 use kar rahe ho toh "Create records in Route 53" button dabao
 *   - 5-30 minute mein validate ho jaye ga
 *
 * QADAM 6: Status "Issued" hone ka intezar karo ✅
 *
 *
 * ⚠️ CLOUDFRONT KE LIYE:
 *   Certificate ZAROOR us-east-1 (N. Virginia) mein banayen!
 *   CloudFront sirf is region ke certificates accept karta hai.
 *
 *   ALB ke liye: Apne server wale region mein banayen
 */


// =============================================================================
// 3. SSL LAGANA — ALB PAR
// =============================================================================

/*
 * QADAM 1: ALB → Listeners → Add listener
 *   - Protocol: HTTPS
 *   - Port: 443
 *   - Default action: Forward to target group
 *   - SSL Certificate: ACM se apna certificate select karo
 *   - SSL Policy: ELBSecurityPolicy-TLS13-1-2-2021-06 (TAVSIYA)
 *
 * QADAM 2: HTTP → HTTPS Redirect:
 *   - HTTP:80 listener edit karo
 *   - Default action: Redirect
 *   - Protocol: HTTPS
 *   - Port: 443
 *   - Status code: 301
 *
 * Ab sab HTTP traffic HTTPS par redirect ho jaye ga!
 */


// =============================================================================
// 4. SSL LAGANA — EC2 PAR (Bina ALB ke — Let's Encrypt)
// =============================================================================

/*
 * Agar ALB use nahi kar rahe aur seedha EC2 par SSL chahiye:
 * Let's Encrypt se FREE SSL certificate haasil karein.
 *
 *   # Certbot install karo
 *   sudo apt install certbot python3-certbot-nginx -y
 *
 *   # Certificate haasil karo (Nginx ke liye)
 *   sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
 *
 *   # Email daalein, terms accept karein
 *   # Certbot khud Nginx config update kar dega!
 *
 *   # Auto-renewal check karo
 *   sudo certbot renew --dry-run
 *
 *   # Certbot ka cron automatic hai — har 60 din mein certificate renew hota hai
 *
 *
 * CERTBOT KE BAAD NGINX CONFIG AISI LAGAY GI:
 *
 *   server {
 *       listen 443 ssl;
 *       server_name yourdomain.com;
 *       root /var/www/your-laravel-app/public;
 *
 *       ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
 *       ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
 *       include /etc/letsencrypt/options-ssl-nginx.conf;
 *       ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;
 *
 *       # ... baqi Laravel config ...
 *   }
 *
 *   server {
 *       listen 80;
 *       server_name yourdomain.com;
 *       return 301 https://$server_name$request_uri;
 *   }
 */


// =============================================================================
// 5. LARAVEL MEIN HTTPS FORCE KARNA
// =============================================================================

// AppServiceProvider mein (ya middleware mein):
/*
 *   // app/Providers/AppServiceProvider.php
 *   public function boot(): void
 *   {
 *       if ($this->app->environment('production')) {
 *           \URL::forceScheme('https');
 *       }
 *   }
 *
 *   // .env mein:
 *   APP_URL=https://yourdomain.com
 *
 *   // config/session.php mein:
 *   'secure' => env('SESSION_SECURE_COOKIE', true),
 *   // Production mein cookies sirf HTTPS par bhejein
 */


// =============================================================================
// 6. SSL BEST PRACTICES
// =============================================================================

/*
 * ✅ Hamesha HTTPS use karo (HTTP → HTTPS redirect lagao)
 * ✅ ACM use karo ALB/CloudFront ke sath (free + auto-renew)
 * ✅ Wildcard certificate banayen (*.yourdomain.com)
 * ✅ TLS 1.2+ force karo (purane versions band karo)
 * ✅ HSTS header lagao (browser ko batao hamesha HTTPS use karo)
 * ✅ Session cookies secure rakhho
 *
 * ❌ Self-signed certificates production mein mat use karo
 * ❌ SSL certificate expire hone mat do (ACM auto-renew karta hai)
 * ❌ Mixed content mat rakhho (HTTP images HTTPS page par)
 */
