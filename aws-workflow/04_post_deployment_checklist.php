<?php

/**
 * ============================================================================
 *     DEPLOYMENT KE BAAD KYA CHECK KARNA HAI — POST-DEPLOYMENT CHECKLIST
 * ============================================================================
 *
 * Deploy ho gaya — lekin kaam abhi khatam nahi hua. Ye checklist follow
 * karo taake pata chale sab SAHI chal raha hai, aur kal koi bara problem
 * surprise na bane.
 */


// =============================================================================
// 1. IMMEDIATE SMOKE TESTING (Deploy ke 5 minute baad)
// =============================================================================

/*
 * ☐ Homepage load ho raha hai (https://yourapp.com kholo)
 * ☐ Login/Register kaam kar raha hai
 * ☐ Database se data aa raha hai (kuch listing page check karo)
 * ☐ File upload kaam kar raha hai (agar S3 use kiya hai, upload karke
 *   dekho file S3 bucket mein pohanch rahi hai)
 * ☐ Email bhej kar dekho (registration/password reset try karo) —
 *   SES se email aa raha hai ya nahi
 * ☐ Critical user flows manually test karo (checkout, payment, jo
 *   bhi tumhare app ka MAIN feature hai)
 * ☐ Mobile aur Desktop dono par check karo
 * ☐ Browser console mein koi JS error to nahi (F12 → Console tab)
 */


// =============================================================================
// 2. SERVER-SIDE VERIFICATION
// =============================================================================

/*
 * ☐ Queue workers chal rahe hain:
 *     sudo supervisorctl status
 *   (laravel-worker:00, laravel-worker:01 — "RUNNING" dikhna chahiye)
 *
 * ☐ Laravel logs mein koi naya ERROR to nahi aa raha:
 *     tail -f storage/logs/laravel.log
 *
 * ☐ Nginx error logs check karo:
 *     sudo tail -f /var/log/nginx/error.log
 *
 * ☐ PHP-FPM chal raha hai:
 *     sudo systemctl status php8.3-fpm
 *
 * ☐ Disk space check karo (logs/cache se full na ho gaya ho):
 *     df -h
 */


// =============================================================================
// 3. DATABASE VERIFICATION
// =============================================================================

/*
 * ☐ Migrations sahi se chal gayi hain:
 *     php artisan migrate:status
 * ☐ RDS automated backups ON hain (AWS Console → RDS → Maintenance & backups)
 * ☐ Database connections normal range mein hain (CloudWatch → RDS metrics)
 *   (agar connections max ke qareeb hain to connection pooling check karo)
 */


// =============================================================================
// 4. PERFORMANCE CHECKING
// =============================================================================

/*
 * ☐ Page load speed check karo (Google PageSpeed Insights ya GTmetrix)
 * ☐ Response time normal hai (ALB metrics mein "Target Response Time" dekho)
 * ☐ CPU/Memory usage normal range mein hai (CloudWatch dashboard)
 * ☐ Redis cache hit-rate check karo (agar bohot kam hai to caching
 *   strategy review karo)
 * ☐ Load test kiya hai agar bara traffic expect ho raha hai
 *   (tools: k6, Apache Bench — staging par pehle try karo)
 */


// =============================================================================
// 5. SECURITY VERIFICATION
// =============================================================================

/*
 * ☐ SSL Labs test kiya (https://www.ssllabs.com/ssltest/) — Grade A milna
 *   chahiye
 * ☐ Security headers check kiye (securityheaders.com) — HSTS, CSP, etc.
 * ☐ APP_DEBUG=false confirm kiya (galti se error page par stack trace
 *   to nahi dikh raha)
 * ☐ .env file publicly accessible to nahi (https://yourapp.com/.env
 *   khol kar check karo — 404 aana chahiye)
 * ☐ Admin panel/sensitive routes par authentication lagi hai
 */


// =============================================================================
// 6. MONITORING/ALERTS VERIFICATION
// =============================================================================

/*
 * ☐ CloudWatch Alarms test kiye (ek FAKE alert trigger karke dekho
 *   notification mil raha hai ya nahi — SNS topic se email aata hai)
 * ☐ Error tracking tool (Sentry/Bugsnag) mein test error bhej kar
 *   confirm kiya ke dashboard par dikh raha hai
 * ☐ Health check endpoint (/health) ALB se sahi respond kar raha hai
 *   (Target Group → Targets → "healthy" status dikhna chahiye)
 * ☐ Uptime monitoring service setup hai (jaise UptimeRobot, Pingdom)
 *   taake site down hone par TURANT pata chale
 */


// =============================================================================
// 7. DNS VERIFICATION
// =============================================================================

/*
 * ☐ Domain sahi se resolve ho raha hai (nslookup yourapp.com)
 * ☐ www aur non-www dono kaam kar rahe hain
 * ☐ HTTP → HTTPS redirect ho raha hai
 * ☐ DNS propagation complete hai (24-48 ghante lag sakte hain — agar
 *   abhi NEW domain link kiya hai to wait karo)
 */


// =============================================================================
// 8. COST MONITORING SETUP
// =============================================================================

/*
 * ☐ AWS Billing Alarm laga hai (jaise $200/month se zyada ho to alert)
 * ☐ AWS Cost Explorer mein services ka breakdown dekh liya hai
 * ☐ Koi UNUSED resources to nahi chal rahe (purane EC2 instances,
 *   unattached EBS volumes — paisa waste karte hain)
 */


// =============================================================================
// 9. TEAM DOCUMENTATION
// =============================================================================

/*
 * ☐ Deploy ka record rakha hai (kab, kya changes, kis ne kiya — Slack/
 *   Notion mein log)
 * ☐ Runbook update kiya hai (agar koi NAYA step add hua deployment mein,
 *   doosre team members ke liye likh do)
 * ☐ Rollback steps DOCUMENTED hain (agar emergency ho to koi bhi follow
 *   kar sake)
 */


// =============================================================================
// 10. AGAR KUCH GALAT HO JAYE — ROLLBACK PROCEDURE
// =============================================================================

/*
 * Agar deploy ke baad MAJOR issue mile:
 *
 *   1. php artisan down   (turant maintenance mode ON karo)
 *   2. Pichla stable git commit/tag par wapas jao:
 *        git checkout <previous-stable-tag>
 *   3. composer install --no-dev (purani dependencies wapas)
 *   4. Agar migration issue hai: php artisan migrate:rollback
 *   5. php artisan config:cache (cache rebuild)
 *   6. php artisan up   (maintenance mode OFF)
 *   7. Team ko inform karo, issue ka root cause dhoondo, fix karke
 *      DOBARA deploy karo (staging par pehle test karke)
 */


// =============================================================================
// QUICK SUMMARY — PEHLE 24 GHANTE KA WATCH ROUTINE
// =============================================================================

/*
 * Deploy ke pehle 24 ghante mein har 1-2 ghante baad ye 3 cheezen dekho:
 *
 * 1. CloudWatch dashboard (CPU, Memory, Errors)
 * 2. Laravel error logs (tail -f storage/logs/laravel.log)
 * 3. Error tracking tool (Sentry/Bugsnag) ka dashboard
 *
 * Agar 24 ghante stable raha, to deployment SUCCESSFUL maan sakte ho.
 */
