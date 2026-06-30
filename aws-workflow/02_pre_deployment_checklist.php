<?php

/**
 * ============================================================================
 *      DEPLOYMENT SE PEHLE KYA CHECK KARNA HAI — PRE-DEPLOYMENT CHECKLIST
 * ============================================================================
 *
 * Production par deploy karne se PEHLE ye SAARI cheezen check karo.
 * In mein se koi bhi cheez miss hui to production mein bug/security issue
 * aa sakta hai.
 */


// =============================================================================
// 1. CODE READINESS
// =============================================================================

/*
 * ☐ Saare tests pass ho rahe hain (php artisan test)
 * ☐ Code review ho chuka hai (PR approve hua)
 * ☐ composer.lock file COMMIT hai (taake same versions production mein install hon)
 * ☐ package-lock.json bhi commit hai (frontend dependencies ke liye)
 * ☐ Koi "dd()", "dump()", "var_dump()" debug statements code mein NAHI hain
 * ☐ TODO/FIXME comments review kar liye (kuch critical to nahi reh gaya)
 * ☐ .env file GIT MEIN NAHI hai (.gitignore mein hona chahiye)
 * ☐ .env.example file UPDATE hai (saari naye keys jo add hui hain)
 */


// =============================================================================
// 2. SECURITY CHECKS
// =============================================================================

/*
 * ☐ APP_DEBUG=false (production .env mein) — agar true raha to errors
 *   PUBLICLY visible honge jisme database credentials tak leak ho sakte hain!
 * ☐ APP_ENV=production
 * ☐ APP_KEY generate hui hai aur SAFE hai (php artisan key:generate)
 * ☐ Saari sensitive cheezen (API keys, passwords) .env mein hain,
 *   CODE mein hardcoded NAHI hain
 * ☐ CORS settings sahi hain (config/cors.php) — sirf trusted domains allowed
 * ☐ Rate limiting lagi hai API routes par (throttle middleware)
 * ☐ SQL Injection se bachne ke liye Eloquent/Query Builder use ho raha hai,
 *   RAW queries mein parameter binding use ho raha hai
 * ☐ File upload validation hai (size limit, allowed extensions)
 * ☐ Mass assignment protection hai ($fillable/$guarded models mein set hai)
 * ☐ HTTPS force ho raha hai (URL::forceScheme('https') ya middleware)
 */


// =============================================================================
// 3. DATABASE READINESS
// =============================================================================

/*
 * ☐ Saari migrations LOCAL/STAGING par test ho chuki hain
 * ☐ Migrations ROLLBACK bhi ho sakti hain (down() methods sahi hain)
 * ☐ Production database ka BACKUP plan hai (RDS automated backups ON hain)
 * ☐ Seeders mein DUMMY/TEST data nahi hai jo production mein chala jaye
 * ☐ Indexes lage hain frequently-queried columns par (performance)
 * ☐ Large tables ke liye migration "downtime" ka andaza laga liya hai
 *   (agar lakhon rows hain to ALTER TABLE slow ho sakta hai)
 */


// =============================================================================
// 4. INFRASTRUCTURE READINESS (AWS Side)
// =============================================================================

/*
 * ☐ VPC, Subnets, Security Groups plan/bana chuke hain
 *   (dekho: aws_advanced/vpc_networking/vpc_networking.php)
 * ☐ RDS instance ready hai, endpoint note kar liya hai
 * ☐ ElastiCache (Redis) ready hai (agar use kar rahe ho)
 * ☐ S3 bucket bana hai, permissions sahi hain (PUBLIC nahi, sirf zaroori
 *   files public hain jaise CDN assets)
 * ☐ IAM roles/users LEAST PRIVILEGE follow karte hain (sirf zaroori
 *   permissions, "AdministratorAccess" pura kabhi mat do production app ko)
 * ☐ Domain registered hai aur DNS access hai (Route 53 ya provider)
 * ☐ SSL certificate request kar liya hai (ACM) — DNS validation pending na ho
 * ☐ Server (EC2/ECS) PHP version Laravel project ke requirement se match
 *   karta hai (composer.json check karo)
 */


// =============================================================================
// 5. PERFORMANCE READINESS
// =============================================================================

/*
 * ☐ npm run build chal chuka hai (production assets minified/compiled hain)
 * ☐ Composer "--no-dev --optimize-autoloader" flags ka plan hai deploy
 *   script mein
 * ☐ Config/Route/View caching ka plan hai (php artisan optimize)
 * ☐ Queue workers ka plan hai (kitne workers chahiye, Supervisor config ready)
 * ☐ N+1 query problems check kar liye hain (Laravel Debugbar/Telescope
 *   se local/staging par verify kiya)
 * ☐ Heavy operations (PDF generate, image processing, emails) QUEUE
 *   mein jaa rahi hain, synchronous request mein NAHI (dekho: event-listener/)
 */


// =============================================================================
// 6. ROLLBACK / SAFETY PLAN
// =============================================================================

/*
 * ☐ STAGING environment par pehle FULL deploy test ho chuka hai
 * ☐ Maintenance mode ka plan hai (php artisan down --secret="...")
 * ☐ ROLLBACK PLAN clear hai — agar deploy fail ho jaye to:
 *     - Pehle wala code version kahan se milega (git tag/release)
 *     - Database migration rollback kaise hoga
 * ☐ Deploy LOW-TRAFFIC time par schedule hai (raat/off-peak hours)
 * ☐ Team ko pata hai deploy ho raha hai (communication ho chuki hai)
 */


// =============================================================================
// 7. MONITORING/LOGGING READY HONA CHAHIYE (Deploy se pehle hi setup karo)
// =============================================================================

/*
 * ☐ CloudWatch Alarms create ho chuke hain (CPU, Memory, Disk, RDS, ALB errors)
 * ☐ Error tracking tool connect hai (Sentry/Bugsnag) — agar use kar rahe ho
 * ☐ Laravel log channel "stack" configure hai (storage/logs + CloudWatch)
 * ☐ Health check endpoint bana hai (/health route jo 200 OK return kare)
 *   — Load Balancer isay use karega ye check karne ke liye server zinda hai
 */


// =============================================================================
// QUICK ONE-LINE SUMMARY
// =============================================================================

/*
 * Deploy se pehle apne aap se 3 sawal pucho:
 *
 * 1. "Agar ye deploy FAIL ho jaye, kya main 5 minute mein wapas
 *     purani working state par aa sakta hoon?" (Rollback plan)
 *
 * 2. "Agar koi error aaye, kya mujhe TURANT pata chal jayega?"
 *     (Monitoring/Alerts)
 *
 * 3. "Kya koi sensitive data (.env, passwords, API keys) galti se
 *     publicly expose to nahi ho raha?" (Security check)
 *
 * Agar teeno ka jawab "HAAN" hai, to deploy karne ke liye ready ho.
 */
