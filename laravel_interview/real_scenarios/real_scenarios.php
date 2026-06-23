<?php

/**
 * ============================================================================
 *              REAL-WORLD SCENARIO QUESTIONS — INTERVIEW Q&A
 *          (6 Years Experience Level — Senior Developer ke sawalaat)
 * ============================================================================
 *
 * Yeh wo sawalaat hain jo interviewer EXPERIENCE check karne ke liye poochhta hai.
 * Code nahi — SOCH aur approach check karta hai.
 */


// =============================================================================
// S1: Production mein app suddenly slow ho gayi — kya karein ge?
// =============================================================================

/*
 * J: Step by step approach:
 *
 *   1. FORAN CHECK:
 *      - Server resources: CPU, Memory, Disk (CloudWatch / htop)
 *      - Database: Slow queries (SHOW PROCESSLIST, slow query log)
 *      - Redis: Memory full? (redis-cli INFO memory)
 *      - Queue: Jobs stuck hain? (Laravel Horizon / queue:failed)
 *
 *   2. APPLICATION LEVEL:
 *      - Laravel Telescope → Recent slow requests dekho
 *      - N+1 queries check karo (Debugbar)
 *      - Cache hit ratio check karo (low ratio = problem)
 *      - Recent deployment hua? → Naya code mein bug ho sakta hai
 *
 *   3. INFRASTRUCTURE:
 *      - Auto Scaling trigger hua? (traffic spike?)
 *      - RDS connections limit? (max_connections)
 *      - Network latency badhi?
 *
 *   4. FORI HAL:
 *      - Cache flush aur dubara warm karo
 *      - Queue workers restart karo
 *      - Agar recent deploy → Rollback karo
 *      - Auto Scaling manually trigger karo (zyada servers)
 *
 *   5. LAMBI MUDDAT KA HAL:
 *      - Slow queries optimize karo (indexes, eager loading)
 *      - Caching strategy improve karo
 *      - Database read replicas add karo
 *      - Monitoring aur alerts improve karo
 */


// =============================================================================
// S2: Aap ko ek payment system design karna hai — kaise karein ge?
// =============================================================================

/*
 * J:
 *   1. INTERFACE BANAYEN (Strategy Pattern):
 *      - PaymentGatewayInterface
 *      - Implementations: Stripe, JazzCash, Easypaisa
 *      - Service Provider mein bind karo
 *
 *   2. IDEMPOTENCY:
 *      - Har payment request ka unique idempotency key
 *      - Double charge se bacho (user ne 2 baar button dabaya)
 *      - Redis lock ya database unique constraint
 *
 *   3. ASYNC PROCESSING:
 *      - Payment queue mein dalo (user ko intezar mat karao)
 *      - Webhook se payment status update karo
 *      - Retry mechanism (3 attempts, exponential backoff)
 *
 *   4. DATABASE DESIGN:
 *      - payments table: id, order_id, amount, status, gateway,
 *        transaction_id, idempotency_key, metadata
 *      - payment_logs table: har attempt ka record
 *
 *   5. SECURITY:
 *      - Webhook signature verify karo
 *      - Amount server-side calculate karo (client par trust mat karo)
 *      - PCI compliance (card data khud store mat karo)
 *      - Transactions use karo (payment + order update atomic)
 *
 *   6. ERROR HANDLING:
 *      - Payment fail → user ko clear message
 *      - Gateway down → doosra gateway try karo (fallback)
 *      - Partial payment → handle karo
 *      - Refund mechanism
 */


// =============================================================================
// S3: 10 Million rows ki table slow hai — kya karein ge?
// =============================================================================

/*
 * J:
 *   1. INDEXES:
 *      - EXPLAIN query chalao — kya index use ho raha hai?
 *      - Missing indexes add karo
 *      - Composite indexes banao (frequently used WHERE combinations)
 *
 *   2. QUERY OPTIMIZATION:
 *      - SELECT * ki jagah specific columns
 *      - Pagination use karo (OFFSET-based nahi, cursor-based)
 *      - Subqueries ki jagah JOINs
 *      - OR conditions ki jagah UNION
 *
 *   3. CACHING:
 *      - Frequently accessed data Redis mein cache karo
 *      - Query results cache karo
 *      - Computed values pre-calculate karke rakhho
 *
 *   4. DATABASE LEVEL:
 *      - Read Replicas use karo (reads alag server par)
 *      - Table partitioning (date-wise partition)
 *      - Archive purana data alag table mein
 *
 *   5. APPLICATION LEVEL:
 *      - Eager loading (N+1 fix)
 *      - Chunk/Cursor use karo large datasets ke liye
 *      - Queue mein heavy reports process karo
 *
 *   6. ADVANCED:
 *      - Elasticsearch use karo search ke liye
 *      - Materialized views banao reporting ke liye
 *      - Database sharding (extreme scale)
 */


// =============================================================================
// S4: Aap ki team mein junior developers hain — code quality kaise ensure karein?
// =============================================================================

/*
 * J:
 *   1. CODE STANDARDS:
 *      - PSR-12 coding standards
 *      - Laravel Pint (formatter) — CI/CD mein lagao
 *      - PHPStan / Larastan (static analysis) — bugs jaldi pakro
 *
 *   2. CODE REVIEW:
 *      - PR (Pull Request) mandatory
 *      - Branch protection rules (review zaruri)
 *      - Kam az kam 1 approval
 *
 *   3. TESTING:
 *      - Feature tests mandatory har PR mein
 *      - CI/CD mein tests chalein
 *      - Coverage threshold set karo (80%+)
 *
 *   4. ARCHITECTURE:
 *      - Service Layer pattern follow karo
 *      - Fat controllers na banayen
 *      - Form Request validation use karo
 *      - API Resources use karo
 *
 *   5. DOCUMENTATION:
 *      - API documentation (Swagger/Scribe)
 *      - README mein setup instructions
 *      - ADR (Architecture Decision Records)
 */


// =============================================================================
// S5: Microservices ya Monolith — aap kya chunein ge aur kyun?
// =============================================================================

/*
 * J: Yeh TRICK QUESTION hai — sahi jawab "depend karta hai":
 *
 *   MONOLITH CHUNEIN jab:
 *   - Team chhoti hai (1-5 developers)
 *   - Product abhi naya hai (MVP stage)
 *   - Domain abhi clear nahi hai
 *   - Quick iteration chahiye
 *   - Simple deployment chahiye
 *
 *   MICROSERVICES CHUNEIN jab:
 *   - Team bari hai (10+ developers, multiple teams)
 *   - Mukhtalif parts alag scale karne hain
 *   - Independent deployments zaruri hain
 *   - Domain boundaries clear hain
 *   - Organization support hai (DevOps team)
 *
 *   ⚠️ BEHTAREEN JAWAB:
 *   "Monolith se shuru karein, MODULAR rakhein, zaroorat par
 *    specific modules ko microservices mein nikaalein.
 *    Premature microservices se badi companies bhi tabaah hoi hain."
 */


// =============================================================================
// S6: Ek E-commerce app design karein — high level architecture batao.
// =============================================================================

/*
 * J:
 *
 *   LAYERS:
 *   ───────
 *   1. PRESENTATION: Blade/Inertia/API → Controllers
 *   2. APPLICATION:  Services → Business Logic
 *   3. DOMAIN:       Models, Events, Value Objects
 *   4. INFRASTRUCTURE: Repositories, External APIs, Queue
 *
 *
 *   MODULES:
 *   ────────
 *   - User Module    (Registration, Auth, Profile)
 *   - Product Module (Catalog, Categories, Search)
 *   - Cart Module    (Add/Remove, Calculate)
 *   - Order Module   (Checkout, Status, History)
 *   - Payment Module (Gateway Integration, Refunds)
 *   - Shipping Module (Calculation, Tracking)
 *   - Notification Module (Email, SMS, Push)
 *   - Admin Module   (Dashboard, Reports, CRUD)
 *
 *
 *   KEY DECISIONS:
 *   ──────────────
 *   - Session-based cart (guests) + DB cart (logged in)
 *   - Payment: Strategy pattern (multiple gateways)
 *   - Search: Elasticsearch / Meilisearch (Scout)
 *   - Images: S3 + CloudFront CDN
 *   - Email: Queue-based (SES)
 *   - Cache: Redis (products, categories, sessions)
 *   - Queue: Redis + Horizon (orders, emails, reports)
 *
 *
 *   DATABASE TABLES (Core):
 *   ───────────────────────
 *   users, products, categories, orders, order_items,
 *   payments, addresses, carts, cart_items, coupons,
 *   reviews, wishlists, notifications
 *
 *
 *   INFRASTRUCTURE:
 *   ───────────────
 *   - EC2 / ECS (App servers)
 *   - RDS MySQL / Aurora (Database)
 *   - ElastiCache Redis (Cache + Sessions + Queues)
 *   - S3 + CloudFront (Static files + CDN)
 *   - ALB + Auto Scaling (Load balancing)
 *   - SES (Emails)
 *   - CloudWatch (Monitoring)
 *   - GitHub Actions (CI/CD)
 */


// =============================================================================
// S7: Apne career ke sabse mushkil bug ke baare mein batao.
// =============================================================================

/*
 * J: Yeh PERSONAL question hai — interviewer dekhna chahta hai:
 *
 *   1. Aap ne systematic debugging ki ya random try kiya?
 *   2. Root cause analysis ki ya sirf symptom fix kiya?
 *   3. Kya seekha us se?
 *   4. Team ke sath kaise communicate kiya?
 *
 *   JAWAB KA FORMAT:
 *
 *   "Ek baar production mein [SITUATION explain karo].
 *    Masla yeh tha ke [PROBLEM batao].
 *    Maine pehle [STEPS batao — logs dekhe, reproduce kiya, etc.].
 *    Root cause nikla [ROOT CAUSE batao].
 *    Fix yeh kiya [SOLUTION batao].
 *    Is se yeh seekha ke [LEARNING batao].
 *    Baad mein humne [PREVENTION steps batao — monitoring, tests, etc.]"
 *
 *   ⚠️ Honest raho — interviewer ko pata hai production bugs hote hain.
 *      Wo dekhna chahta hai aap ne KAISE handle kiya.
 */


// =============================================================================
// S8: Aap apne project mein coding standards kaise maintain karte hain?
// =============================================================================

/*
 * J:
 *   1. AUTOMATED TOOLS (CI/CD mein):
 *      - Laravel Pint → Code formatting
 *      - PHPStan Level 6+ → Static analysis
 *      - PHP CS Fixer → PSR-12 compliance
 *
 *   2. GIT HOOKS:
 *      - Pre-commit: Pint + PHPStan
 *      - Pre-push: Tests
 *
 *   3. PR PROCESS:
 *      - Template: What, Why, How, Testing
 *      - Review checklist
 *      - Tests mandatory
 *
 *   4. ARCHITECTURE RULES:
 *      - Controllers THIN rakhho (sirf request/response)
 *      - Business logic Services mein
 *      - Validation Form Requests mein
 *      - API responses Resources mein
 *      - Constants Enums mein
 *      - Complex queries Scopes ya Repository mein
 */
