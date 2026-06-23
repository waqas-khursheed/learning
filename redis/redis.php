<?php

/**
 * ============================================================================
 *                    MUKAMMAL REDIS GUIDE
 *        Basics se Production tak Laravel & AWS ke sath
 * ============================================================================
 *
 * FEHRIST (TABLE OF CONTENTS):
 * 1.  Redis kya hai?
 * 2.  Redis kaise kaam karta hai? (Architecture)
 * 3.  Redis kyun use karte hain? (Kya masle hal karta hai)
 * 4.  Redis ki Data Structures with Examples
 * 5.  Real-World Use Cases (Asli duniya mein kahan use hota hai)
 * 6.  AWS par Redis (ElastiCache)
 * 7.  Laravel mein Redis — Mukammal Guide
 * 8.  Docker Setup for Redis
 * 9.  Production ke liye Best Practices
 * 10. Performance Tips
 * 11. Interview ke Aam Sawalaat
 */


// =============================================================================
// 1. REDIS KYA HAI?
// =============================================================================

/*
 * Redis = Remote Dictionary Server
 *
 * Redis ek open-source, IN-MEMORY data store hai jo ye kaam karta hai:
 *   - Cache (sab se zyada aam istemal)
 *   - Database
 *   - Message Broker (Pub/Sub)
 *   - Queue System (kaam ki line)
 *   - Session Store (user login data rakhna)
 *
 * KHAS KHUSOBIYAAT:
 * ┌─────────────────────────────────────────────────────────────────────┐
 * │  Feature              │  Wazahat                                   │
 * ├─────────────────────────────────────────────────────────────────────┤
 * │  In-Memory Storage    │  Data RAM mein rehta hai = bohot tez       │
 * │  Key-Value Store      │  PHP associative array jaisa               │
 * │  Single-Threaded      │  Ek waqt mein ek command = koi race nahi   │
 * │  Persistence Options  │  Data disk par bhi save ho sakta hai       │
 * │  Sub-millisecond      │  1 second mein 100,000+ operations        │
 * │  Lightweight          │  Khali instance sirf ~3MB RAM leta hai     │
 * │  Language Agnostic    │  PHP, Python, Node, Java sab ke sath chalta│
 * └─────────────────────────────────────────────────────────────────────┘
 *
 * AASAN MISAAL:
 * ─────────────
 * Redis ko aise samjho jaise ek BOHOT TEZ sticky notes board:
 *
 *   MySQL/PostgreSQL = Filing cabinet (organized, bharosa mand, magar dheema)
 *   Redis            = Aap ki desk par sticky notes (fori access, kam jagah)
 *
 * Aap filing cabinet ko replace nahi karte — sticky notes un cheezon ke liye
 * use karte ho jinhe jaldi pakar na ho, jabke detail wali files cabinet mein
 * rehti hain.
 *
 *
 * REDIS NORMAL DATABASE SE KAISE MUKHTALIF HAI?
 * ┌─────────────────────────────────────────────────────────────────────┐
 * │  MySQL/PostgreSQL             │  Redis                             │
 * ├─────────────────────────────────────────────────────────────────────┤
 * │  Data Disk (HDD) par rakhta   │  Data RAM (Memory) mein rakhta    │
 * │  Read: ~1-10ms                │  Read: ~0.1-0.5ms                  │
 * │  Complex queries (JOINs)      │  Simple key-value lookups          │
 * │  Relational data              │  Flat data structures              │
 * │  By default permanent rakhta  │  By default temporary (permanent   │
 * │                               │  bhi ho sakta hai)                 │
 * │  Complex queries ke liye acha │  Tez reads/writes ke liye acha     │
 * └─────────────────────────────────────────────────────────────────────┘
 */


// =============================================================================
// 2. REDIS KAISE KAAM KARTA HAI? (Architecture)
// =============================================================================

/*
 * REDIS KE BAGHAIR REQUEST FLOW:
 * ──────────────────────────────
 *
 *   User Request
 *       │
 *       ▼
 *   [Application Server (Laravel)]
 *       │
 *       ▼
 *   [MySQL Database]  ← Har request DB ko marti hai (LOAD mein DHEEMA)
 *       │
 *       ▼
 *   User ko Response
 *
 *   Masla: 1000 users ek hi product page maang rahe = 1000 DB queries!
 *
 *
 * REDIS KE SAATH REQUEST FLOW:
 * ────────────────────────────
 *
 *   User Request
 *       │
 *       ▼
 *   [Application Server (Laravel)]
 *       │
 *       ├──→ [Redis Cache] ← PEHLE yahan check karo (0.1ms)
 *       │        │
 *       │        ├── Cache HIT  → Data foran wapas do ✅
 *       │        │
 *       │        └── Cache MISS → MySQL pe jao ↓
 *       │
 *       ├──→ [MySQL Database] ← Sirf jab cache mein na mile (5ms)
 *       │        │
 *       │        └── Result Redis mein rakh do aglay dafa ke liye
 *       │
 *       ▼
 *   User ko Response
 *
 *   Nateeja: 1000 users same page maang rahe = 1 DB query + 999 cache hits!
 *
 *
 * REDIS KI ANDAROONI BANAWAT (Internal Architecture):
 * ───────────────────────────────────────────────────
 *
 *   ┌──────────────────────────────────────────────┐
 *   │              REDIS SERVER                     │
 *   │                                               │
 *   │   ┌──────────────────────────────────────┐   │
 *   │   │         RAM (Memory)                  │   │
 *   │   │                                       │   │
 *   │   │   Key: "user:1"    → {name: "Ali"}   │   │
 *   │   │   Key: "product:5" → {price: 999}    │   │
 *   │   │   Key: "session:x" → {user_id: 1}    │   │
 *   │   │   Key: "queue:emails" → [job1, job2]  │   │
 *   │   │                                       │   │
 *   │   └──────────────────────────────────────┘   │
 *   │                                               │
 *   │   ┌──────────────────────────────────────┐   │
 *   │   │     PERSISTENCE (Ikhtiyari)           │   │
 *   │   │                                       │   │
 *   │   │   RDB: Har N minute baad snapshot     │   │
 *   │   │   AOF: Har write command log karta    │   │
 *   │   └──────────────────────────────────────┘   │
 *   │                                               │
 *   │   Port: 6379 (default)                       │
 *   │   Protocol: RESP (Redis Serialization)       │
 *   └──────────────────────────────────────────────┘
 *
 *
 * PERSISTENCE MODES (Data Bacha-ne ke Tareeqe):
 * ─────────────────────────────────────────────
 *
 * 1) RDB (Redis Database Backup):
 *    - Waqfay waqfay se snapshot leta hai (maslan, har 5 minute baad)
 *    - Restart tez hota hai, magar aakhri kuch minute ka data kho sakta hai
 *    - Acha hai: Caching ke liye (kuch minute ka cache khona theek hai)
 *
 * 2) AOF (Append Only File):
 *    - Har write command ko file mein likhta hai
 *    - Taqreeban koi data loss nahi, magar restart dheema hota hai
 *    - Acha hai: Sessions, queues ke liye (data zyada important hai)
 *
 * 3) RDB + AOF (Production ke liye Tavsiya):
 *    - Dono use karta hai zyada se zyada mehfooz-iyat ke liye
 *    - AOF recovery ke liye, RDB tez backups ke liye
 *
 * 4) Koi Persistence Nahi:
 *    - Data sirf memory mein, restart par kho jata hai
 *    - Acha hai: Pure caching jahan data dubara bana sakte hain
 */


// =============================================================================
// 3. REDIS KYUN USE KARTE HAIN? (Kya Masle Hal Karta Hai)
// =============================================================================

/*
 * MASLA 1: DHEEMI DATABASE QUERIES
 * ─────────────────────────────────
 * Scenario: E-commerce site jis mein 50,000 products hain
 *
 * Redis ke baghair:
 *   - Har page load MySQL se JOINs ke sath query karta hai
 *   - Homepage load hone mein 500ms lagti hai
 *   - Traffic ki bhari mein: 2-5 seconds ya timeout!
 *
 * Redis ke sath:
 *   - Pehli request: MySQL se query (500ms), result Redis mein rakh do
 *   - Agli 10,000 requests: Redis se padho (har ek 0.5ms)
 *   - Baar baar maangay jaane wale data ke liye 1000 guna tez!
 *
 *
 * MASLA 2: MULTI-SERVER SETUP MEIN SESSION MANAGEMENT
 * ───────────────────────────────────────────────────
 *
 * Redis ke baghair:
 *   Server A (user session hai) ← User yahan login karta hai
 *   Server B (session nahi!)    ← Load balancer agla request yahan bhejta hai
 *   Nateeja: User randomly logout ho jata hai!
 *
 * Redis ke sath:
 *   Server A ──┐
 *              ├──→ [Redis: Markazi Session Store] ← Sab servers sessions share karte hain
 *   Server B ──┘
 *   Nateeja: User logged in rehta hai chahe koi bhi server request handle kare!
 *
 *
 * MASLA 3: RATE LIMITING (Hadd Bandhi)
 * ─────────────────────────────────────
 * Scenario: API har user ko 1 minute mein 100 requests ki ijazat deta hai
 *
 * Redis ke baghair:
 *   - MySQL mein counters rakhna → har request ke liye bohot dheema
 *   - PHP memory mein rakhna → requests ke darmiyan kho jata hai, multiple servers par kaam nahi karta
 *
 * Redis ke sath:
 *   SET rate:user:123 → 45  (TTL 60 seconds ke sath)
 *   - Atomic increment: INCR rate:user:123
 *   - 60 seconds baad khud-ba-khud khatam ho jata hai
 *   - Sab servers par kaam karta hai!
 *
 *
 * MASLA 4: REAL-TIME FEATURES (Fori Khususiyaat)
 * ───────────────────────────────────────────────
 * Scenario: Live notification system, chat, leaderboard
 *
 * Redis ke baghair:
 *   - Har second MySQL ko poll karna → database mar jata hai
 *
 * Redis ke sath:
 *   - Pub/Sub real-time notifications ke liye
 *   - Sorted Sets leaderboards ke liye
 *   - Lists chat message queues ke liye
 *
 *
 * MASLA 5: JOB QUEUES (Kaam ki Line)
 * ───────────────────────────────────
 * Scenario: Ek event ke baad 10,000 emails bhejna hai
 *
 * Redis ke baghair:
 *   - Emails synchronously bhejna → page minute-on tak hang rehta hai
 *   - Users samajhte hain app kharab ho gaya
 *
 * Redis ke sath:
 *   - 10,000 email jobs Redis queue mein daal do → user ko foran jawab
 *   - Background workers jobs ek ek karke process karte hain
 *   - User ko foran dikhta hai "Emails bhejay ja rahe hain!"
 */


// =============================================================================
// 4. REDIS KI DATA STRUCTURES WITH EXAMPLES
// =============================================================================

/*
 * Redis sirf ek simple key-value store NAHI hai.
 * Yeh ameer data structures support karta hai:
 *
 *
 * 4.1 STRINGS — Sab se buniyadi type
 * ───────────────────────────────────
 *   SET name "Waqas"              → Ek string store karo
 *   GET name                      → "Waqas"
 *   SET counter 0                 → Ek number store karo
 *   INCR counter                  → 1 (atomic increment — ek se barhao)
 *   INCRBY counter 5              → 6
 *   DECR counter                  → 5
 *   SET session:abc123 "user_data" EX 3600   → 1 ghante mein expire ho jaye gi
 *   MSET key1 "val1" key2 "val2"  → Ek sath kai keys set karo
 *   MGET key1 key2                → ["val1", "val2"]
 *
 *   Istemal: Caching, counters, rate limiting, session tokens
 *
 *
 * 4.2 HASHES — PHP associative arrays / objects jaisi
 * ───────────────────────────────────────────────────
 *   HSET user:1 name "Ali" age 30 city "Lahore"
 *   HGET user:1 name                → "Ali"
 *   HGETALL user:1                  → {name: "Ali", age: "30", city: "Lahore"}
 *   HINCRBY user:1 age 1           → 31
 *   HDEL user:1 city               → city field hata do
 *   HEXISTS user:1 name            → 1 (haan, maujood hai)
 *
 *   Istemal: User profiles, product details, settings
 *
 *   Asli duniya ki misaal — Shopping cart:
 *   HSET cart:user:5 product:101 2    → product 101 ki 2 units
 *   HSET cart:user:5 product:205 1    → product 205 ki 1 unit
 *   HGETALL cart:user:5               → Poora cart dikha do
 *   HINCRBY cart:user:5 product:101 1 → Miqdar barha ke 3 karo
 *   HDEL cart:user:5 product:205      → Product cart se hata do
 *
 *
 * 4.3 LISTS — Tarteeb wala majmua (PHP arrays jaisa)
 * ──────────────────────────────────────────────────
 *   LPUSH notifications:user:1 "Naya order mila"       → Baayen push karo
 *   RPUSH notifications:user:1 "Payment confirm hui"   → Daayen push karo
 *   LRANGE notifications:user:1 0 -1   → Sab items nikalo
 *   LPOP notifications:user:1           → Pehla item nikalo aur hata do
 *   RPOP notifications:user:1           → Aakhri item nikalo aur hata do
 *   LLEN notifications:user:1           → Items gino
 *
 *   Istemal: Activity feeds, message queues, recent items
 *
 *   Asli duniya ki misaal — Haal hi ki activity feed:
 *   LPUSH feed:user:1 "Photo #42 ko like kiya"
 *   LPUSH feed:user:1 "Post #15 par comment kiya"
 *   LPUSH feed:user:1 "User #8 ko follow kiya"
 *   LRANGE feed:user:1 0 9             → Aakhri 10 activities nikalo
 *   LTRIM feed:user:1 0 99             → Sirf aakhri 100 items rakhho
 *
 *
 * 4.4 SETS — Be-tarteeb unique values ka majmua
 * ──────────────────────────────────────────────
 *   SADD online:users "user:1" "user:2" "user:3"
 *   SMEMBERS online:users       → {"user:1", "user:2", "user:3"}
 *   SISMEMBER online:users "user:1"  → 1 (haan)
 *   SCARD online:users          → 3 (ginti)
 *   SREM online:users "user:2"  → user:2 hata do
 *
 *   Set Operations:
 *   SINTER setA setB            → DONO sets mein mojood items (intersection)
 *   SUNION setA setB            → KISI BHI set mein mojood items (union)
 *   SDIFF setA setB             → A mein hai magar B mein nahi
 *
 *   Istemal: Tags, unique visitors, online users, mutual friends
 *
 *   Asli duniya ki misaal — Mushtarka Dost (Mutual Friends):
 *   SADD friends:ali "waqas" "ahmed" "sara" "fatima"
 *   SADD friends:waqas "ali" "ahmed" "usman" "ayesha"
 *   SINTER friends:ali friends:waqas   → {"ahmed"} (mushtarka dost)
 *
 *
 * 4.5 SORTED SETS — Sets jaisa magar har member ka score hai
 * ──────────────────────────────────────────────────────────
 *   ZADD leaderboard 1500 "player:ali"
 *   ZADD leaderboard 2300 "player:waqas"
 *   ZADD leaderboard 1800 "player:ahmed"
 *
 *   ZRANGE leaderboard 0 -1 WITHSCORES   → Sab players score se sorted (chhota se bara)
 *   ZREVRANGE leaderboard 0 2             → Top 3 players (bara se chhota)
 *   ZRANK leaderboard "player:waqas"      → 2 (0 se shuru hone wali rank)
 *   ZSCORE leaderboard "player:ali"       → 1500
 *   ZINCRBY leaderboard 500 "player:ali"  → 2000 (score barhao)
 *
 *   Istemal: Leaderboards, priority queues, time-series data, trending items
 *
 *   Asli duniya ki misaal — Trending Products:
 *   ZINCRBY trending:products 1 "product:iphone"   → Kisi ne iPhone dekha
 *   ZINCRBY trending:products 1 "product:macbook"   → Kisi ne MacBook dekha
 *   ZINCRBY trending:products 1 "product:iphone"    → Ek aur iPhone view
 *   ZREVRANGE trending:products 0 9                 → Top 10 trending products
 *
 *
 * 4.6 PUB/SUB — Publish/Subscribe messaging (Paigham bhejne/sunne ka nizam)
 * ─────────────────────────────────────────────────────────────────────────
 *   SUBSCRIBE notifications        → "notifications" channel par paigham suno
 *   PUBLISH notifications "Hello"  → Sab subscribers ko paigham bhejo
 *
 *   Istemal: Real-time notifications, chat, live updates, WebSocket backends
 *
 *
 * 4.7 STREAMS — Sirf likhne wala log (halka Kafka jaisa)
 * ─────────────────────────────────────────────────────
 *   XADD orders * product "iPhone" quantity 1 user "ali"
 *   XADD orders * product "MacBook" quantity 2 user "waqas"
 *   XRANGE orders - +               → Sab entries padho
 *   XLEN orders                     → Entries gino
 *
 *   Istemal: Event sourcing, activity logs, message streaming
 */


// =============================================================================
// 5. ASLI DUNIYA MEIN ISTEMAL (Real-World Use Cases)
// =============================================================================

/*
 * 5.1 E-COMMERCE (Daraz, Amazon, Shopify)
 * ─────────────────────────────────────────
 *   - Product page caching (har page view par DB query se bacho)
 *   - Shopping cart storage (HASH ke sath tez reads/writes)
 *   - Flash sale inventory counting (atomic DECR se overselling roko)
 *   - Session storage (user sab servers par logged in rahe)
 *   - Order processing queues (background mein email/SMS/invoice banayi)
 *
 *
 * 5.2 SOCIAL MEDIA (Facebook, Instagram, Twitter)
 * ────────────────────────────────────────────────
 *   - News feed caching (pehle se bani feed Redis LIST mein rakhi)
 *   - Like/view counters (INCR se real-time counts)
 *   - Online user tracking (SET se unique online users)
 *   - Trending hashtags (SORTED SET jis mein score = mention count)
 *   - Notification queues (LIST mein pending notifications)
 *
 *
 * 5.3 GAMING (Kheyl)
 * ──────────────────
 *   - Real-time leaderboards (SORTED SET — lakhon players foran rank ho jayein)
 *   - Player session management
 *   - Matchmaking queues (khilariyon ko milane ki line)
 *   - In-game chat (Pub/Sub)
 *
 *
 * 5.4 RIDE-SHARING (Careem, Uber)
 * ────────────────────────────────
 *   - Driver location tracking (GEO commands — GEOADD, GEORADIUS)
 *   - Surge pricing cache (bhari qeemat ka cache)
 *   - Ride request queues
 *   - ETA caching (pahunchne ka waqt cache karna)
 *
 *
 * 5.5 BANKING / FINTECH (Bank aur Maali Technology)
 * ──────────────────────────────────────────────────
 *   - API calls par rate limiting
 *   - OTP storage jis mein auto-expiry ho (SET with TTL)
 *   - Transaction locks (distributed locking SETNX se)
 *   - Real-time fraud detection counters (dhoka rokne ke counters)
 *
 *
 * 5.6 FOOD DELIVERY (Foodpanda, Uber Eats)
 * ──────────────────────────────────────────
 *   - Restaurant menu caching
 *   - Order status tracking (order ki halat dekhna)
 *   - Delivery driver queue management
 *   - Real-time order updates (Pub/Sub)
 */


// =============================================================================
// 6. AWS PAR REDIS (ElastiCache)
// =============================================================================

/*
 * AWS ElastiCache for Redis ek MANAGED Redis service hai.
 * "Managed" ka matlab hai AWS yeh sab sambhalta hai:
 *   - Server setup
 *   - Patching aur updates
 *   - Monitoring (nigrani)
 *   - Failover aur recovery (kharabi se bahal hona)
 *   - Backups
 *   - Scaling (barha choti karna)
 *
 *
 * ELASTICACHE KHUD SE MANAGE KARNE SE BEHTAR KYUN HAI?
 * ────────────────────────────────────────────────────
 *
 * ┌──────────────────────────────────────────────────────────────────┐
 * │  Khud Manage Redis (EC2)        │  ElastiCache for Redis        │
 * ├──────────────────────────────────────────────────────────────────┤
 * │  Aap install aur configure karo │  AWS aap ke liye set karta hai│
 * │  Aap backups sambhalo           │  Rozana automatic backups     │
 * │  Aap failover manage karo       │  Seconds mein auto failover   │
 * │  Aap security updates lagao     │  AWS khud-ba-khud lagata hai  │
 * │  Aap monitor aur scale karo     │  CloudWatch + auto-scaling    │
 * │  Poora control                  │  Kam control, kam sar dardi   │
 * │  Chhoti scale par sasta         │  Bari scale par sasta         │
 * └──────────────────────────────────────────────────────────────────┘
 *
 *
 * ELASTICACHE ARCHITECTURE (Banawat):
 * ───────────────────────────────────
 *
 *   ┌─────────────────────────────────────────────────────┐
 *   │                 AWS VPC                              │
 *   │                                                      │
 *   │  ┌──────────┐     ┌──────────────────────────────┐  │
 *   │  │ EC2/ECS  │────→│  ElastiCache Redis Cluster    │  │
 *   │  │ (Laravel)│     │                                │  │
 *   │  └──────────┘     │  Primary Node (Read/Write)     │  │
 *   │                    │       │                        │  │
 *   │  ┌──────────┐     │       ├── Replica 1 (Read)     │  │
 *   │  │ EC2/ECS  │────→│       │                        │  │
 *   │  │ (Laravel)│     │       └── Replica 2 (Read)     │  │
 *   │  └──────────┘     │                                │  │
 *   │                    └──────────────────────────────┘  │
 *   │                                                      │
 *   │  ┌──────────┐                                       │
 *   │  │   RDS    │  ← Main database (MySQL/PostgreSQL)   │
 *   │  │ (MySQL)  │                                       │
 *   │  └──────────┘                                       │
 *   └─────────────────────────────────────────────────────┘
 *
 *   Note: ElastiCache PRIVATE hai — sirf aap ke VPC ke andar se access ho sakta hai.
 *         Aap apni local machine se seedha connect nahi kar sakte.
 *
 *
 * ELASTICACHE DEPLOYMENT MODES (Lagane ke Tareeqe):
 * ─────────────────────────────────────────────────
 *
 * 1) Single Node (Development/Testing ke liye):
 *    - Ek Redis node
 *    - Koi failover nahi, koi replication nahi
 *    - Sab se sasta option
 *    - ⚠️ Production ke liye NAHI!
 *
 * 2) Cluster Mode Disabled (Replication Group):
 *    - 1 Primary + 5 tak Read Replicas
 *    - Auto failover: agar primary mar jaye, replica primary ban jata hai
 *    - Sara data ek node mein samata hai
 *    - Acha hai: Zyada-tar applications ke liye (~100GB data tak)
 *
 *    ┌─────────────────────────────────────────┐
 *    │  Primary (Read/Write)                    │
 *    │       │                                  │
 *    │       ├──→ Replica 1 (Sirf Read)         │
 *    │       ├──→ Replica 2 (Sirf Read)         │
 *    │       └──→ Replica 3 (Sirf Read)         │
 *    └─────────────────────────────────────────┘
 *
 * 3) Cluster Mode Enabled (Sharding — data baant-na):
 *    - Data kai shards mein banta hai
 *    - Har shard ka 1 primary + replicas hain
 *    - Horizontally scale hota hai (zyada shards = zyada capacity)
 *    - Acha hai: Bari datasets, zyada throughput ki zaroorat
 *
 *    ┌──────────────────────────────────────────────────┐
 *    │  Shard 1                    Shard 2              │
 *    │  ┌─────────────────┐       ┌─────────────────┐  │
 *    │  │ Primary (keys A-M)│     │ Primary (keys N-Z)│ │
 *    │  │  └── Replica     │      │  └── Replica     │  │
 *    │  └─────────────────┘       └─────────────────┘  │
 *    └──────────────────────────────────────────────────┘
 *
 *
 * ELASTICACHE NODE TYPES (Aam qisam):
 * ───────────────────────────────────
 *   cache.t3.micro   → 0.5 GB  (Free tier, testing ke liye)
 *   cache.t3.small   → 1.5 GB  (Chhoti production apps)
 *   cache.r6g.large  → 13.1 GB (Production workloads)
 *   cache.r6g.xlarge → 26.3 GB (Zyada memory wali production)
 *
 *
 * ELASTICACHE SETUP KARNA (Qadam ba Qadam):
 * ─────────────────────────────────────────
 *
 * Qadam 1: AWS Console → ElastiCache → Create Cluster
 * Qadam 2: Engine mein "Redis" chunen
 * Qadam 3: Configure karein:
 *   - Name: my-app-redis
 *   - Node type: cache.t3.micro (dev ke liye) ya cache.r6g.large (prod ke liye)
 *   - Replicas ki tadaad: 2 (production ke liye)
 *   - Multi-AZ: Enable karein (high availability ke liye)
 *   - Encryption: Dono enable karein (at-rest aur in-transit)
 * Qadam 4: Apna VPC aur subnet group chunen
 * Qadam 5: Security group set karein (port 6379 apni app servers se allow karein)
 * Qadam 6: Create!
 *
 * Qadam 7: Endpoint haasil karein:
 *   Primary endpoint: my-app-redis.abc123.ng.0001.use1.cache.amazonaws.com:6379
 *
 * Qadam 8: Apni Laravel .env configure karein:
 *   REDIS_HOST=my-app-redis.abc123.ng.0001.use1.cache.amazonaws.com
 *   REDIS_PORT=6379
 *   REDIS_PASSWORD=your-auth-token (agar encryption enable hai)
 *
 *
 * PRODUCTION KE LIYE ELASTICACHE FEATURES:
 * ────────────────────────────────────────
 *
 * 1) Multi-AZ (Multi Availability Zone):
 *    - Primary AZ-1 mein, Replica AZ-2 mein
 *    - Agar AZ-1 band ho jaye, auto-failover AZ-2 par
 *    - Kharabion ke dauran zero downtime
 *
 * 2) Automatic Backups (Khud-ba-khud Nashust):
 *    - Rozana snapshots
 *    - Retention: 1-35 din
 *    - Retention window ke andar kisi bhi point par restore kar sakte hain
 *
 * 3) Encryption (Ramz-bandi):
 *    - At-rest: Data disk par encrypted (AES-256)
 *    - In-transit: App aur Redis ke darmiyan TLS/SSL
 *    - AUTH token: Password se mehfooz
 *
 * 4) CloudWatch Monitoring (Nigrani):
 *    - CPU utilization
 *    - Memory usage (Redis ke liye sab se ahem!)
 *    - Cache hit/miss ratio (kitni baar cache se mila ya nahi mila)
 *    - Evictions (jab Redis ki memory bhar jaye)
 *    - Network bandwidth
 *
 * 5) Parameter Groups:
 *    - maxmemory-policy: allkeys-lru (sab se purani use ki gayi keys nikalo)
 *    - timeout: 300 (5 minute baad bekar connections band karo)
 *    - tcp-keepalive: 60
 *
 *
 * ELASTICACHE vs MEMORYDB vs KHUD HOST KARNA:
 * ────────────────────────────────────────────
 * ┌───────────────────────────────────────────────────────────────────┐
 * │  Feature         │ ElastiCache    │ MemoryDB       │ Khud Host   │
 * ├───────────────────────────────────────────────────────────────────┤
 * │  Buniyadi Istemal│ Cache          │ Database       │ Koi bhi     │
 * │  Mazbooti        │ Guarantee nahi │ Poori mazboot  │ Configurable│
 * │  Replication     │ Async          │ Multi-AZ sync  │ Manual      │
 * │  Data Loss Risk  │ Mumkin hai     │ Nahi           │ Depend karta│
 * │  Qeemat          │ $$             │ $$$            │ $ (+ ops)   │
 * │  Behtar hai      │ Caching/Session│ Primary DB     │ Poora control│
 * └───────────────────────────────────────────────────────────────────┘
 *
 *  MemoryDB: Jab Redis ko apna PRIMARY database banana ho (mazboot durability)
 *  ElastiCache: Jab Redis cache/session layer ho aap ke database ke SAMNE
 */


// =============================================================================
// 7. LARAVEL MEIN REDIS — MUKAMMAL GUIDE
// =============================================================================

// ─────────────────────────────────────────────
// 7.1 INSTALLATION AUR CONFIGURATION (Nasb aur Tarteeb)
// ─────────────────────────────────────────────

/*
 * Qadam 1: predis package install karein (PHP Redis client):
 *
 *   composer require predis/predis
 *
 *   YA phpredis PHP extension use karein (tez, C-based):
 *   - php-redis extension install karein
 *   - pecl install redis
 *
 *
 * Qadam 2: .env file configure karein:
 *
 *   CACHE_DRIVER=redis
 *   SESSION_DRIVER=redis
 *   QUEUE_CONNECTION=redis
 *
 *   REDIS_HOST=127.0.0.1          (development ke liye localhost)
 *   REDIS_PASSWORD=null
 *   REDIS_PORT=6379
 *   REDIS_CLIENT=predis            (ya extension ke liye 'phpredis')
 *
 *   # AWS ElastiCache ke liye:
 *   # REDIS_HOST=my-cluster.abc123.cache.amazonaws.com
 *   # REDIS_PASSWORD=your-auth-token
 *   # REDIS_PORT=6379
 *   # REDIS_SCHEME=tls              (encrypted connections ke liye)
 *
 *
 * Qadam 3: Laravel ki Redis configuration (config/database.php):
 */

// config/database.php — Redis section
$redisConfig = [
    'redis' => [
        'client' => env('REDIS_CLIENT', 'predis'), // 'predis' ya 'phpredis'

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix'  => env('REDIS_PREFIX', 'myapp_'), // Sab keys ke aage lagao taake takraar na ho
        ],

        // Default connection — aam cache operations ke liye use hoti hai
        'default' => [
            'url'      => env('REDIS_URL'),
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port'     => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'), // Redis mein 16 databases hain (0-15)
        ],

        // Cache connection — khaas tor par caching ke liye
        'cache' => [
            'url'      => env('REDIS_URL'),
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port'     => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'), // Cache ke liye alag DB
        ],

        // Session connection
        'session' => [
            'url'      => env('REDIS_URL'),
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port'     => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_SESSION_DB', '2'),
        ],

        // Queue connection
        'queue' => [
            'url'      => env('REDIS_URL'),
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port'     => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_QUEUE_DB', '3'),
        ],
    ],
];

/*
 * ALAG DATABASES KYUN?
 * ────────────────────
 * Redis mein 16 databases hain (0-15). Alag databases use karne se:
 *   - DB 0: Aam maqsad / default
 *   - DB 1: Cache (sessions ko mutasir kiye baghair flush kar sakte hain)
 *   - DB 2: Sessions (cache se alag)
 *   - DB 3: Queues (sab se alag)
 *
 * Fayda: Aap cache flush kar sakte hain (DB 1 par FLUSHDB) baghair
 * user sessions (DB 2) ya pending jobs (DB 3) khoye!
 */


// ─────────────────────────────────────────────
// 7.2 LARAVEL MEIN REDIS SE CACHING
// ─────────────────────────────────────────────

use Illuminate\Support\Facades\Cache;

// BUNIYADI CACHING OPERATIONS
// ───────────────────────────

// 60 minute ke liye ek value cache mein rakhho
Cache::put('key', 'value', now()->addMinutes(60));

// Hamesha ke liye rakhho (jab tak haath se na mita-en)
Cache::forever('site_settings', ['theme' => 'dark', 'language' => 'en']);

// Value haasil karo
$value = Cache::get('key');                    // Agar na mile toh null milta hai
$value = Cache::get('key', 'default_value');   // Agar na mile toh 'default_value' milta hai

// Check karo key maujood hai ya nahi
if (Cache::has('key')) {
    // Key cache mein maujood hai
}

// Ek key mita do
Cache::forget('key');

// Sara cache mita do
Cache::flush(); // ⚠️ Production mein ehtiyat se! Cache database ki SAB cheez mit jaye gi

// Barhao / Ghatao
Cache::increment('page_views');       // +1
Cache::increment('page_views', 5);    // +5
Cache::decrement('stock_count');      // -1


// REMEMBER PATTERN (Sab se zyada use hone wala!)
// ──────────────────────────────────────────────
// "Pehle cache check karo, agar nahi hai toh query chalao aur nateeja rakh lo"

$products = Cache::remember('all_products', now()->addHours(2), function () {
    // Yeh callback SIRF tab chalta hai jab cache khali ho (cache miss)
    return Product::with('category', 'brand')
        ->where('active', true)
        ->orderBy('name')
        ->get();
});
// Pehli baar:  Query chalti hai, nateeja Redis mein jata hai, data wapas aata hai (~500ms)
// Agli baar:   Cached data Redis se wapas aata hai (~0.5ms)
// 2 ghante baad: Cache expire hota hai, query dubara chalti hai


// Hamesha ke liye yaad rakhho (jab tak haath se saaf na karein)
$settings = Cache::rememberForever('app_settings', function () {
    return Setting::all()->pluck('value', 'key');
});


// ASLI DUNIYA KI CACHING MISALEIN
// ────────────────────────────────

// Misaal 1: User ki profile cache karo
function getUserProfile(int $userId): array
{
    return Cache::remember("user_profile:{$userId}", now()->addMinutes(30), function () use ($userId) {
        return User::with(['posts', 'followers', 'following'])
            ->withCount(['posts', 'followers', 'following'])
            ->findOrFail($userId)
            ->toArray();
    });
}

// Misaal 2: Product listing pagination ke sath cache karo
function getProductsPage(int $page, int $perPage = 20): mixed
{
    $cacheKey = "products:page:{$page}:per:{$perPage}";

    return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($page, $perPage) {
        return Product::with('category')
            ->where('active', true)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    });
}

// Misaal 3: Tags ke sath cache karo (milti julti cache entries ko group karo)
// Tags se aap ek sath milti julti cache entries flush kar sakte hain
Cache::tags(['products', 'category:electronics'])->put(
    'featured_electronics',
    Product::where('category', 'electronics')->featured()->get(),
    now()->addHours(1)
);

Cache::tags(['products', 'category:clothing'])->put(
    'featured_clothing',
    Product::where('category', 'clothing')->featured()->get(),
    now()->addHours(1)
);

// Sirf electronics products ka cache saaf karo (clothing ka cache rahega!)
Cache::tags(['category:electronics'])->flush();

// SARE product caches saaf karo (electronics aur clothing dono)
Cache::tags(['products'])->flush();


// CACHE INVALIDATION (Cache ko taaza rakhna)
// ──────────────────────────────────────────
// Caching ka sab se mushkil masla: Cache kab saaf/update karein?

// Tareeqa 1: Data badalne par cache saaf karo (Event-based)
// Apne Product model mein:
class Product extends Model
{
    protected static function booted()
    {
        // Jab bhi product create, update ya delete ho cache saaf karo
        static::saved(function (Product $product) {
            Cache::forget("product:{$product->id}");
            Cache::tags(['products'])->flush();
        });

        static::deleted(function (Product $product) {
            Cache::forget("product:{$product->id}");
            Cache::tags(['products'])->flush();
        });
    }
}

// Tareeqa 2: Observer ke zariye cache saaf karo
class ProductObserver
{
    public function saved(Product $product): void
    {
        Cache::forget("product:{$product->id}");
        Cache::forget("products:featured");
        Cache::tags(['products'])->flush();
    }

    public function deleted(Product $product): void
    {
        Cache::forget("product:{$product->id}");
        Cache::tags(['products'])->flush();
    }
}

// Tareeqa 3: Jaldi badalne wale data ke liye chhota TTL rakhho
$liveScores = Cache::remember('live_scores', now()->addSeconds(10), function () {
    return Score::where('status', 'live')->get();
});

// Tareeqa 4: Cache warming (pehle se cache bhar do)
// Scheduled command se chalao: php artisan cache:warm
class WarmCacheCommand extends Command
{
    protected $signature = 'cache:warm';

    public function handle(): void
    {
        // Aksar maange jaane wala data pehle se cache kar do
        Cache::put('homepage_products', Product::featured()->limit(20)->get(), now()->addHours(4));
        Cache::put('categories', Category::with('subcategories')->get(), now()->addHours(12));
        Cache::put('site_settings', Setting::all(), now()->addDay());

        $this->info('Cache kamyabi se garam ho gaya!');
    }
}


// ─────────────────────────────────────────────
// 7.3 REDIS SE SESSION MANAGEMENT
// ─────────────────────────────────────────────

/*
 * .env mein configure karein:
 *   SESSION_DRIVER=redis
 *   SESSION_CONNECTION=session    ('session' Redis connection use karega)
 *   SESSION_LIFETIME=120          (minute)
 *
 * config/session.php mein configure karein:
 *   'driver' => env('SESSION_DRIVER', 'redis'),
 *   'connection' => env('SESSION_CONNECTION', 'session'),
 *
 * Bas itna hi! Laravel khud-ba-khud sessions Redis mein rakhta hai.
 * Koi code ki tabdeeli nahi chahiye — bilkul file/database sessions jaisa kaam karta hai.
 *
 *
 * SESSIONS KE LIYE REDIS KYUN?
 * ────────────────────────────
 * 1) Tezi: Har request par session read hota hai — Redis DB se 10 guna tez hai
 * 2) Scalability: Kai servers ek hi session store share kar sakte hain
 * 3) Auto-expiry: Redis TTL khud-ba-khud expired sessions saaf karta hai
 * 4) Safai ki zaroorat nahi: File sessions ke bar-aks, garbage collection nahi chahiye
 *
 *
 * ANDAROONI TOR PAR KAISE KAAM KARTA HAI:
 * ───────────────────────────────────────
 *
 * Jab user login karta hai:
 *   1. Laravel session ID banata hai: "abc123xyz"
 *   2. Redis mein rakhta hai: SET myapp_session:abc123xyz "{user_id:1, ...}" EX 7200
 *   3. Browser ko cookie bhejta hai: laravel_session=abc123xyz
 *
 * Agli request par:
 *   1. Browser cookie bhejta hai: laravel_session=abc123xyz
 *   2. Laravel Redis se padhta hai: GET myapp_session:abc123xyz
 *   3. User data foran mil jata hai (~0.1ms)
 *
 * 120 minute baad (SESSION_LIFETIME):
 *   1. Redis TTL expire ho jata hai
 *   2. Key khud-ba-khud delete ho jati hai
 *   3. User logout ho jata hai
 */

// Sessions use karna (hamesha jaisa hi — driver transparent hai)
session(['cart' => ['product_1' => 2, 'product_2' => 1]]);
$cart = session('cart');
session()->forget('cart');
session()->flush(); // Sara session data saaf karo


// ─────────────────────────────────────────────
// 7.4 LARAVEL MEIN REDIS SE QUEUES
// ─────────────────────────────────────────────

/*
 * .env mein configure karein:
 *   QUEUE_CONNECTION=redis
 *
 * config/queue.php mein configure karein:
 *   'redis' => [
 *       'driver'       => 'redis',
 *       'connection'   => 'queue',         ('queue' Redis connection use karega)
 *       'queue'        => 'default',
 *       'retry_after'  => 90,              (fail hone ke baad itne seconds baad dubara try)
 *       'block_for'    => null,
 *   ],
 *
 *
 * QUEUES KE LIYE REDIS KYUN?
 * ──────────────────────────
 * 1) Tez job push karna (Redis LIST mein daalta hai — microseconds)
 * 2) Bharosemand delivery (Redis queue data structure sambhalta hai)
 * 3) Priority queues (ahem kaam pehle process karo)
 * 4) Delayed jobs (kuch der baad kaam chalao)
 * 5) Rate limiting (kaam kitni tezi se process ho, control karo)
 */

// Qadam 1: Ek job banaen
// php artisan make:job SendWelcomeEmail

class SendWelcomeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user
    ) {}

    public function handle(): void
    {
        Mail::to($this->user->email)->send(new WelcomeEmail($this->user));
    }

    // Fail hone par 3 baar dubara try karo
    public int $tries = 3;

    // Retries ke darmiyan 60 seconds intezar karo
    public int $backoff = 60;

    // Zyada se zyada chalne ka waqt
    public int $timeout = 30;
}

// Qadam 2: Jobs dispatch karein (bhejein)

// Buniyadi dispatch — default queue mein jata hai
SendWelcomeEmail::dispatch($user);

// Khaas queue mein dispatch karo
SendWelcomeEmail::dispatch($user)->onQueue('emails');

// Delay ke sath dispatch karo (10 minute baad email bhejo)
SendWelcomeEmail::dispatch($user)->delay(now()->addMinutes(10));

// Kai jobs dispatch karo (maslan, bulk import ke baad)
$users = User::where('welcome_sent', false)->get();
foreach ($users as $user) {
    SendWelcomeEmail::dispatch($user)->onQueue('emails');
}

// Qadam 3: Jobs process karein (workers chalao)
// php artisan queue:work redis --queue=emails,default
// php artisan queue:work redis --queue=high,default --tries=3

/*
 * QUEUE PRIORITY KI MISAAL:
 * ─────────────────────────
 * php artisan queue:work redis --queue=high,medium,low
 *
 * Workers pehle 'high' queue process karte hain, phir 'medium', phir 'low'.
 * Payment confirmation (high) newsletter (low) se pehle process hoti hai.
 */

// Priority dispatch ki misaal
ProcessPayment::dispatch($order)->onQueue('high');
SendNewsletter::dispatch($subscribers)->onQueue('low');
GenerateReport::dispatch($report)->onQueue('medium');


// Job Chaining — jobs ko silsila-war chalao
Bus::chain([
    new ProcessPodcast($podcast),
    new OptimizePodcast($podcast),
    new PublishPodcast($podcast),
])->dispatch();
// Har job sirf pichli job kamyab hone ke baad chalti hai

// Job Batching — kai jobs ek sath chalao, progress track karo
Bus::batch([
    new ImportChunk($chunk1),
    new ImportChunk($chunk2),
    new ImportChunk($chunk3),
])->then(function (Batch $batch) {
    // Sab jobs kamyabi se mukammal ho gayin
    Notification::send($admin, new ImportComplete());
})->catch(function (Batch $batch, Throwable $e) {
    // Koi job fail ho gayi
})->finally(function (Batch $batch) {
    // Batch khatam ho gayi (kamyabi ya nakaam)
})->dispatch();


// ─────────────────────────────────────────────
// 7.5 LARAVEL MEIN REDIS SEEDHA USE KARNA
// ─────────────────────────────────────────────

use Illuminate\Support\Facades\Redis;

// Buniyadi operations
Redis::set('name', 'Waqas');
$name = Redis::get('name'); // "Waqas"

// Expiry ke sath set karo (60 seconds)
Redis::setex('otp:user:1', 60, '123456');

// Check karo key maujood hai ya nahi
$exists = Redis::exists('name'); // 1 (haan) ya 0 (nahi)

// Ek key mita do
Redis::del('name');

// Sirf tab set karo jab maujood NA ho (locks ke liye faida mand)
$locked = Redis::setnx('lock:order:123', 'processing');
// 1 return karta hai agar lock mil gaya, 0 agar pehle se locked hai

// Hash operations
Redis::hset('user:1', 'name', 'Ali');
Redis::hset('user:1', 'email', 'ali@example.com');
$user = Redis::hgetall('user:1'); // ['name' => 'Ali', 'email' => 'ali@example.com']

// List operations
Redis::lpush('queue:notifications', json_encode(['type' => 'order', 'id' => 1]));
Redis::lpush('queue:notifications', json_encode(['type' => 'payment', 'id' => 2]));
$notification = Redis::rpop('queue:notifications'); // Sab se purani notification nikalo

// Set operations (unique values)
Redis::sadd('online:users', 'user:1', 'user:2', 'user:3');
$onlineUsers = Redis::smembers('online:users');
$isOnline = Redis::sismember('online:users', 'user:1'); // 1 (haan)
$count = Redis::scard('online:users'); // 3

// Sorted Set operations
Redis::zadd('leaderboard', 100, 'player:ali');
Redis::zadd('leaderboard', 250, 'player:waqas');
Redis::zadd('leaderboard', 175, 'player:ahmed');
$topPlayers = Redis::zrevrange('leaderboard', 0, 9); // Top 10

// Barhao / Ghatao
Redis::incr('page:views:homepage');         // +1
Redis::incrby('page:views:homepage', 10);   // +10
Redis::decr('product:stock:101');            // -1

// Ek key ko expire karo
Redis::expire('session:abc', 3600); // 1 ghante mein expire

// Baqi TTL dekho
$ttl = Redis::ttl('session:abc'); // Kitne seconds bache hain


// PIPELINES — Ek sath kai commands bhejo (tez!)
// ──────────────────────────────────────────────
// Pipeline ke baghair: 100 commands = Redis tak 100 round trips
// Pipeline ke sath: 100 commands = sirf 1 round trip!

$results = Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 100; $i++) {
        $pipe->set("key:{$i}", "value:{$i}");
    }
    // Sab 100 SET commands ek hi round trip mein bhej diye
});

// Asli duniya ki pipeline: Kai user profiles ek sath laao
$userIds = [1, 2, 3, 4, 5];
$profiles = Redis::pipeline(function ($pipe) use ($userIds) {
    foreach ($userIds as $id) {
        $pipe->hgetall("user:{$id}");
    }
});


// TRANSACTIONS — Atomic multi-command execution
// ──────────────────────────────────────────────
// Ya toh sab commands chalen ya koi na chale

Redis::transaction(function ($tx) {
    $tx->set('key1', 'value1');
    $tx->set('key2', 'value2');
    $tx->incr('counter');
    // Ya toh teeno execute hon, ya koi na ho
});


// ─────────────────────────────────────────────
// 7.6 REDIS SE RATE LIMITING (Hadd Bandhi)
// ─────────────────────────────────────────────

use Illuminate\Support\Facades\RateLimiter;

// Rate limiter define karein (AppServiceProvider ya RouteServiceProvider mein)
RateLimiter::for('api', function ($request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});

// Routes par lagao
// Route::middleware('throttle:api')->group(function () { ... });

// Redis se seedha custom rate limiting
function customRateLimit(string $key, int $maxAttempts, int $windowSeconds): bool
{
    $currentAttempts = Redis::incr($key);

    if ($currentAttempts === 1) {
        Redis::expire($key, $windowSeconds);
    }

    return $currentAttempts <= $maxAttempts;
}

// Istemal:
$userId = auth()->id();
if (!customRateLimit("rate:api:{$userId}", 100, 60)) {
    abort(429, 'Bohot zyada requests. Ek minute intezar karein.');
}


// ─────────────────────────────────────────────
// 7.7 REDIS SE DISTRIBUTED LOCKS (Taqseem Shuda Taalay)
// ─────────────────────────────────────────────

use Illuminate\Support\Facades\Cache;

// Race conditions roko — ek waqt mein sirf ek process yeh code chala sake
$lock = Cache::lock('process-order:123', 10); // Zyada se zyada 10 seconds ke liye lock

if ($lock->get()) {
    try {
        // Sirf ek server/process ek waqt mein yeh chala sakta hai
        $order = Order::find(123);
        $order->status = 'processing';
        $order->save();

        processPayment($order);
    } finally {
        $lock->release();
    }
} else {
    // Koi aur process pehle se is order ko handle kar raha hai
    return response()->json(['message' => 'Order process ho raha hai'], 409);
}

// Lock ka intezar karo aur ruko (5 seconds tak)
$lock = Cache::lock('generate-report', 30);

$lock->block(5, function () {
    // Yeh code tab chalta hai jab lock mil jaye
    generateMonthlyReport();
});


// ─────────────────────────────────────────────
// 7.8 LARAVEL MEIN PUB/SUB (Real-time Events)
// ─────────────────────────────────────────────

// Events publish karna (bhejana)
Redis::publish('notifications', json_encode([
    'user_id' => 1,
    'message' => 'Aap ka order ship ho gaya hai!',
    'type'    => 'order_update',
]));

// Events subscribe karna — sunnna (alag process/command mein chalao)
Redis::subscribe(['notifications'], function (string $message, string $channel) {
    $data = json_decode($message, true);
    // Notification process karo
    // User ko WebSocket push bhejo
    echo "{$channel} par mila: {$message}\n";
});

// Note: Laravel mein real-time features ke liye Laravel Echo + Broadcasting behtar hai
// jo andar se Redis Pub/Sub use karta hai magar saaf API ke sath.


// ─────────────────────────────────────────────
// 7.9 AMALI ASLI DUNIYA KI MISALEIN
// ─────────────────────────────────────────────

// ── Misaal 1: API Response Caching Middleware ──

class CacheApiResponse
{
    public function handle($request, Closure $next, int $minutes = 5)
    {
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        $cacheKey = 'api:' . md5($request->fullUrl() . serialize($request->all()));

        return Cache::remember($cacheKey, now()->addMinutes($minutes), function () use ($request, $next) {
            return $next($request);
        });
    }
}

// Routes mein istemal:
// Route::get('/products', [ProductController::class, 'index'])->middleware('cache.api:10');


// ── Misaal 2: View Counter (Buffered Writes — jama karke likhna) ──

class ViewCounterService
{
    // Har page view par MySQL update karne ki bajaye,
    // Redis mein counts jama karo aur waqfay waqfay se MySQL mein daal do
    public function recordView(string $type, int $id): void
    {
        $key = "views:{$type}:{$id}";
        Redis::incr($key);
    }

    // Scheduler se har 5 minute chalao: $schedule->call([ViewCounterService::class, 'flushToDatabase'])->everyFiveMinutes();
    public function flushToDatabase(): void
    {
        $keys = Redis::keys('views:*');

        foreach ($keys as $key) {
            $count = Redis::getdel($key); // Haasil karo aur ek sath mita do

            if ($count > 0) {
                // Key parse karo: "views:post:42"
                [, $type, $id] = explode(':', $key);

                DB::table($type . 's')
                    ->where('id', $id)
                    ->increment('view_count', (int) $count);
            }
        }
    }
}


// ── Misaal 3: Feature Flags (Feature ke Jhande) ──

class FeatureFlagService
{
    public function isEnabled(string $feature, ?int $userId = null): bool
    {
        // Check karo feature globally enable hai ya nahi
        $enabled = Redis::hget('features', $feature);

        if ($enabled === 'all') return true;
        if ($enabled === 'none') return false;

        // Check karo user rollout group mein hai ya nahi
        if ($userId) {
            return (bool) Redis::sismember("feature:{$feature}:users", $userId);
        }

        return false;
    }

    public function enable(string $feature, string $scope = 'all'): void
    {
        Redis::hset('features', $feature, $scope);
    }

    public function enableForUsers(string $feature, array $userIds): void
    {
        Redis::hset('features', $feature, 'partial');
        Redis::sadd("feature:{$feature}:users", ...$userIds);
    }
}

// Istemal:
// $featureFlags->enable('dark_mode', 'all');
// $featureFlags->enableForUsers('new_checkout', [1, 2, 3, 4, 5]);
// if ($featureFlags->isEnabled('new_checkout', auth()->id())) { ... }


// ── Misaal 4: Online Users Tracker (Online Users ka Pata) ──

class OnlineUsersService
{
    private const TTL = 300; // 5 minute

    public function markOnline(int $userId): void
    {
        // Sorted set use karo jis mein timestamp score hai
        Redis::zadd('users:online', now()->timestamp, $userId);
    }

    public function getOnlineUsers(): array
    {
        // Un users ko hata do jo 5 minute se nazar nahi aaye
        Redis::zremrangebyscore('users:online', '-inf', now()->subMinutes(5)->timestamp);

        return Redis::zrevrange('users:online', 0, -1);
    }

    public function getOnlineCount(): int
    {
        Redis::zremrangebyscore('users:online', '-inf', now()->subMinutes(5)->timestamp);

        return Redis::zcard('users:online');
    }

    public function isOnline(int $userId): bool
    {
        $lastSeen = Redis::zscore('users:online', $userId);

        if (!$lastSeen) return false;

        return $lastSeen > now()->subMinutes(5)->timestamp;
    }
}


// ── Misaal 5: Autocomplete / Search Suggestions (Talash ke Mashware) ──

class SearchSuggestionService
{
    public function recordSearch(string $query): void
    {
        $normalized = strtolower(trim($query));
        Redis::zincrby('search:popular', 1, $normalized);
    }

    public function getSuggestions(string $prefix, int $limit = 10): array
    {
        // Sab popular searches lo aur prefix se filter karo
        // Production ke liye Redis SCAN ya dedicated search engine sochein
        $allSearches = Redis::zrevrange('search:popular', 0, 499, 'WITHSCORES');

        $suggestions = [];
        $prefix = strtolower($prefix);

        foreach ($allSearches as $term => $score) {
            if (str_starts_with($term, $prefix)) {
                $suggestions[] = ['term' => $term, 'score' => (int) $score];
                if (count($suggestions) >= $limit) break;
            }
        }

        return $suggestions;
    }

    public function getTopSearches(int $limit = 20): array
    {
        return Redis::zrevrange('search:popular', 0, $limit - 1, 'WITHSCORES');
    }
}


// =============================================================================
// 8. REDIS KE LIYE DOCKER SETUP
// =============================================================================

/*
 * 8.1 SIMPLE DOCKER SETUP (docker-compose.yml)
 * ─────────────────────────────────────────────
 *
 *   # docker-compose.yml
 *   version: '3.8'
 *
 *   services:
 *     app:
 *       build: .
 *       ports:
 *         - "8000:8000"
 *       depends_on:
 *         - redis
 *         - mysql
 *       environment:
 *         REDIS_HOST: redis
 *         REDIS_PORT: 6379
 *
 *     redis:
 *       image: redis:7-alpine          # Alpine = chhoti image (~30MB)
 *       ports:
 *         - "6379:6379"                 # Local development ke liye expose karo
 *       volumes:
 *         - redis_data:/data            # Restart ke baad bhi data rahe
 *       command: redis-server --appendonly yes   # AOF persistence enable karo
 *       restart: unless-stopped
 *       healthcheck:
 *         test: ["CMD", "redis-cli", "ping"]
 *         interval: 10s
 *         timeout: 5s
 *         retries: 3
 *
 *     mysql:
 *       image: mysql:8.0
 *       ports:
 *         - "3306:3306"
 *       environment:
 *         MYSQL_ROOT_PASSWORD: secret
 *         MYSQL_DATABASE: myapp
 *       volumes:
 *         - mysql_data:/var/lib/mysql
 *
 *   volumes:
 *     redis_data:
 *     mysql_data:
 *
 *
 * 8.2 PRODUCTION DOCKER SETUP (password aur config ke sath)
 * ─────────────────────────────────────────────────────────
 *
 *   redis:
 *     image: redis:7-alpine
 *     ports:
 *       - "6379:6379"
 *     volumes:
 *       - redis_data:/data
 *       - ./redis/redis.conf:/usr/local/etc/redis/redis.conf
 *     command: redis-server /usr/local/etc/redis/redis.conf
 *     restart: unless-stopped
 *     deploy:
 *       resources:
 *         limits:
 *           memory: 512M               # Memory ki hadd lagao
 *         reservations:
 *           memory: 256M
 *     healthcheck:
 *       test: ["CMD", "redis-cli", "-a", "${REDIS_PASSWORD}", "ping"]
 *       interval: 10s
 *       timeout: 5s
 *       retries: 3
 *     networks:
 *       - backend                      # Alag network
 *
 *
 * 8.3 REDIS CONFIGURATION FILE (redis.conf)
 * ──────────────────────────────────────────
 *
 *   # Mehfooz-iyat (Security)
 *   requirepass YourStrongPasswordHere
 *   bind 0.0.0.0
 *   protected-mode yes
 *
 *   # Memory
 *   maxmemory 256mb
 *   maxmemory-policy allkeys-lru      # Memory bhar jane par purani keys nikalo
 *
 *   # Persistence (Data bachana)
 *   appendonly yes                      # AOF enable karo
 *   appendfsync everysec               # Har second disk par sync karo
 *   save 900 1                         # RDB: save karo agar 900s mein 1 key badli
 *   save 300 10                        # RDB: save karo agar 300s mein 10 keys badlein
 *   save 60 10000                      # RDB: save karo agar 60s mein 10000 keys badlein
 *
 *   # Performance (Kargurzagi)
 *   tcp-keepalive 60
 *   timeout 300                        # 5 minute baad bekar connections band karo
 *   databases 16
 *
 *   # Logging
 *   loglevel notice
 *   logfile ""                         # stdout par log karo (Docker ise pakarta hai)
 *
 *
 * 8.4 POORA LARAVEL + REDIS DOCKER SETUP
 * ──────────────────────────────────────
 *
 *   # docker-compose.yml
 *   version: '3.8'
 *
 *   services:
 *     # Laravel Application
 *     app:
 *       build:
 *         context: .
 *         dockerfile: Dockerfile
 *       ports:
 *         - "8000:8000"
 *       volumes:
 *         - .:/var/www/html
 *       depends_on:
 *         redis:
 *           condition: service_healthy
 *         mysql:
 *           condition: service_healthy
 *       environment:
 *         APP_ENV: local
 *         CACHE_DRIVER: redis
 *         SESSION_DRIVER: redis
 *         QUEUE_CONNECTION: redis
 *         REDIS_HOST: redis
 *         REDIS_PORT: 6379
 *         REDIS_PASSWORD: ${REDIS_PASSWORD:-null}
 *         DB_HOST: mysql
 *         DB_DATABASE: myapp
 *         DB_USERNAME: root
 *         DB_PASSWORD: secret
 *
 *     # Queue Worker (Kaam ka karkun)
 *     queue-worker:
 *       build:
 *         context: .
 *         dockerfile: Dockerfile
 *       command: php artisan queue:work redis --queue=high,default,low --tries=3 --timeout=90
 *       volumes:
 *         - .:/var/www/html
 *       depends_on:
 *         - redis
 *       restart: unless-stopped
 *
 *     # Scheduler (Waqt ka nizam)
 *     scheduler:
 *       build:
 *         context: .
 *         dockerfile: Dockerfile
 *       command: php artisan schedule:work
 *       volumes:
 *         - .:/var/www/html
 *       depends_on:
 *         - redis
 *       restart: unless-stopped
 *
 *     # Redis
 *     redis:
 *       image: redis:7-alpine
 *       ports:
 *         - "6379:6379"
 *       volumes:
 *         - redis_data:/data
 *       command: redis-server --appendonly yes --requirepass ${REDIS_PASSWORD:-}
 *       restart: unless-stopped
 *       healthcheck:
 *         test: ["CMD", "redis-cli", "ping"]
 *         interval: 10s
 *         timeout: 5s
 *         retries: 3
 *
 *     # MySQL
 *     mysql:
 *       image: mysql:8.0
 *       ports:
 *         - "3306:3306"
 *       environment:
 *         MYSQL_ROOT_PASSWORD: secret
 *         MYSQL_DATABASE: myapp
 *       volumes:
 *         - mysql_data:/var/lib/mysql
 *       healthcheck:
 *         test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
 *         interval: 10s
 *         timeout: 5s
 *         retries: 3
 *
 *     # Redis Commander (Redis ke liye Web UI — sirf development ke liye)
 *     redis-commander:
 *       image: rediscommander/redis-commander:latest
 *       ports:
 *         - "8081:8081"
 *       environment:
 *         REDIS_HOSTS: local:redis:6379
 *       depends_on:
 *         - redis
 *       profiles:
 *         - debug                      # Sirf is se shuru hota hai: docker compose --profile debug up
 *
 *   volumes:
 *     redis_data:
 *     mysql_data:
 *
 *
 * KAAM KE DOCKER COMMANDS:
 * ────────────────────────
 *   docker compose up -d                    # Sab services shuru karo
 *   docker compose up -d redis              # Sirf Redis shuru karo
 *   docker compose exec redis redis-cli     # Redis CLI kholo
 *   docker compose exec redis redis-cli MONITOR    # Sab commands real-time mein dekho
 *   docker compose exec redis redis-cli INFO       # Server ki maloomaat aur stats
 *   docker compose logs redis               # Redis ke logs dekho
 *   docker compose down                     # Sab services band karo
 *   docker compose down -v                  # Band karo aur volumes hata do (⚠️ data mit jata hai)
 */


// =============================================================================
// 9. PRODUCTION KE LIYE BEST PRACTICES (Behtar Tareeqe)
// =============================================================================

/*
 * 9.1 KEY NAAM RAKHNE KE USOOL
 * ────────────────────────────
 *
 *   ACHA:
 *   ✅ user:1:profile           (object:id:field)
 *   ✅ cache:products:page:1    (maqsad:entity:qualifier)
 *   ✅ session:abc123           (type:identifier)
 *   ✅ queue:emails:high        (type:name:priority)
 *   ✅ rate:api:user:42         (maqsad:scope:identifier)
 *
 *   BURA:
 *   ❌ u1                       (bohot chhota, be-maaini)
 *   ❌ user_1_profile_data_v2   (bohot lamba, separators mein farq)
 *   ❌ myAppUserProfileCacheData (camelCase Redis mein acha nahi lagta)
 *
 *   Usool:
 *   - Colons (:) separator ke tor par use karo
 *   - Keys 100 bytes se kam rakhho
 *   - Ek jaisa pattern rakhho: type:id:field
 *   - Apni app ke liye prefix lagao: myapp:user:1 (shared Redis mein takraar se bacho)
 *
 *
 * 9.2 MEMORY MANAGEMENT (Yaaddaasht ka Intezam)
 * ──────────────────────────────────────────────
 *
 *   ❗ Redis SAB KUCH RAM mein rakhta hai — memory aap ka sab se ahem zariya hai!
 *
 *   a) redis.conf mein maxmemory set karo:
 *      maxmemory 512mb                  # Redis ko system ki saari RAM use karne se roko
 *
 *   b) Eviction policy chunen (maxmemory-policy):
 *      ┌───────────────────────────────────────────────────────────────┐
 *      │  Policy              │  Bartao                                │
 *      ├───────────────────────────────────────────────────────────────┤
 *      │  noeviction          │  Memory bhar jane par error do         │
 *      │  allkeys-lru         │  Sab se purani use ki gayi key nikalo ✅│
 *      │  allkeys-lfu         │  Sab se kam use hone wali key nikalo   │
 *      │  volatile-lru        │  Expiry wali LRU key nikalo            │
 *      │  volatile-ttl        │  Sab se chhoti TTL wali key nikalo     │
 *      │  allkeys-random      │  Koi bhi random key nikalo             │
 *      └───────────────────────────────────────────────────────────────┘
 *
 *      Tavsiya: Caching ke liye allkeys-lru
 *
 *   c) Cache keys par hamesha TTL set karo:
 *      - Cache ko be-hadd barhne mat do
 *      - "Hamesha" wale caches ko bhi lambi TTL do (maslan, 24 ghante)
 *
 *   d) Memory istemal ki nigrani karo:
 *      redis-cli INFO memory
 *      - used_memory: asal mein kitni memory use ho rahi hai
 *      - used_memory_peak: sab se zyada memory istemal
 *      - maxmemory: set ki gayi hadd
 *      - evicted_keys: nikali gayi keys ki tadaad (0 ya kam honi chahiye)
 *
 *
 * 9.3 MEHFOOZ-IYAT (Security)
 * ───────────────────────────
 *
 *   a) Hamesha password set karo:
 *      requirepass YourStrongRandomPasswordHere
 *
 *   b) Redis ko kabhi internet par expose mat karo:
 *      bind 127.0.0.1            # Sirf local connections
 *      protected-mode yes        # Extra hifazat
 *
 *   c) Connections ke liye TLS/SSL use karo (khaas tor par ElastiCache ke sath):
 *      Laravel .env mein REDIS_SCHEME=tls
 *
 *   d) Production mein khatrnaak commands band karo:
 *      rename-command FLUSHDB ""     # Ghalti se FLUSHDB hone se roko
 *      rename-command FLUSHALL ""    # Ghalti se FLUSHALL hone se roko
 *      rename-command KEYS ""        # KEYS O(n) hai aur Redis ko rok sakta hai
 *      rename-command DEBUG ""
 *
 *   e) Mukhtalif maqasid ke liye alag Redis instances ya databases use karo
 *
 *
 * 9.4 NIGRANI (Monitoring)
 * ────────────────────────
 *
 *   Dekhne ke liye Ahem Meezan (Key Metrics):
 *
 *   1) Memory Istemal:
 *      - used_memory maxmemory se kam honi chahiye
 *      - Jab > 80% maxmemory ho alert do
 *
 *   2) Cache Hit Ratio:
 *      - keyspace_hits / (keyspace_hits + keyspace_misses)
 *      - Sehat mand cache ke liye > 90% hona chahiye
 *      - Kam ratio = aap ghalat cheezein cache kar rahe ya TTL bohot chhota hai
 *
 *   3) Jure Hue Clients:
 *      - Ghairmamuli barhav connection leaks ki nishani ho sakti hai
 *      - maxclients munasib tareeqe se set karo
 *
 *   4) Evictions (Nikali gayi keys):
 *      - Zyada eviction rate = Redis ko aur memory chahiye
 *
 *   5) Taakhir (Latency):
 *      - redis-cli --latency
 *      - Local ke liye < 1ms, network ke liye < 5ms honi chahiye
 *
 *   6) Dheema Log (Slow Log):
 *      - SLOWLOG GET 10
 *      - Wo commands dikhata hai jinhe bohot waqt laga
 *
 *   Nigrani ke commands:
 *      redis-cli INFO                  # Poori server ki maloomaat
 *      redis-cli INFO memory           # Memory ki tafseel
 *      redis-cli INFO stats            # Hit/miss stats
 *      redis-cli INFO clients          # Jure hue clients
 *      redis-cli MONITOR               # Real-time command stream (⚠️ performance par asar)
 *      redis-cli SLOWLOG GET 10        # Dheemi queries
 *      redis-cli DBSIZE                # Keys ki kul tadaad
 *
 *
 * 9.5 AAM GHALTIYAN JIN SE BACHNA HAI
 * ────────────────────────────────────
 *
 *   ❌ Production mein KEYS command use karna:
 *      - KEYS * SAARI keys scan karti hai — agar lakhon keys hain toh Redis ruk jata hai
 *      - Is ki jagah SCAN use karo (batches mein iterate karti hai)
 *
 *   ❌ Bari objects store karna:
 *      - Poori Eloquent models sab relationships ke sath store mat karo
 *      - Sirf woh rakhho jo chahiye (khaas fields)
 *      - Values 1MB se kam rakhho (behtareen: 100KB se kam)
 *
 *   ❌ TTL set na karna:
 *      - TTL ke baghair cache hamesha barhta rehta hai jab tak Redis ki memory na bhar jaye
 *      - Hamesha munasib expiration set karo
 *
 *   ❌ Cache stampede / thundering herd:
 *      - Jab ek maqbool cache key expire hoti hai, 1000 requests ek sath DB ko maarti hain
 *      - Hal: Locks use karo (Cache::lock) ya TTLs ko random jitter se bhatkao
 *
 *   ❌ Zyada caching:
 *      - Har second badalne wali cheezein cache mat karo
 *      - Kam access hone wali cheezein cache mat karo
 *      - Wo cheezein cache karo jo: mehnga compute + aksar access + kam badlein
 *
 *   ❌ Connection failures handle na karna:
 *      - Redis band hone par hamesha ek fallback rakhho
 *      - Laravel cache ke liye khud fallback karta hai, magar custom Redis calls exception de sakte hain
 */


// =============================================================================
// 10. PERFORMANCE TIPS (Kargurzagi ke Mashware)
// =============================================================================

/*
 * TIP 1: BULK OPERATIONS KE LIYE PIPELINES USE KARO
 * ──────────────────────────────────────────────────
 * Pipeline ke baghair: 100 operations × 0.5ms RTT = 50ms
 * Pipeline ke sath:    100 operations × 1 trip    = 1ms
 */

// Bura — 100 round trips
foreach ($userIds as $id) {
    Redis::hgetall("user:{$id}");
}

// Acha — 1 round trip
$results = Redis::pipeline(function ($pipe) use ($userIds) {
    foreach ($userIds as $id) {
        $pipe->hgetall("user:{$id}");
    }
});


/*
 * TIP 2: MUNASIB DATA STRUCTURES USE KARO
 * ────────────────────────────────────────
 *
 * User profile store karna:
 *   ❌ SET user:1 '{"name":"Ali","age":30,"city":"Lahore"}'  → Poora JSON deserialize karna paray ga
 *   ✅ HSET user:1 name Ali age 30 city Lahore              → Alag alag fields padh/update kar sakte hain
 *
 * Leaderboard store karna:
 *   ❌ LIST mein rakh ke haath se sort karna
 *   ✅ SORTED SET use karna — Redis khud sort karta hai
 *
 * Unique items store karna:
 *   ❌ LIST use karna aur haath se duplicates check karna
 *   ✅ SET use karna — duplicates khud-ba-khud handle hote hain
 *
 *
 * TIP 3: BARI VALUES KE LIYE COMPRESSION USE KARO
 * ────────────────────────────────────────────────
 */

// Store karne se pehle compress karo
$data = json_encode($largeDataSet);
$compressed = gzcompress($data, 6);
Redis::set('large:data', $compressed);

// Padhne par decompress karo
$compressed = Redis::get('large:data');
$data = json_decode(gzuncompress($compressed), true);


/*
 * TIP 4: KEYS KI JAGAH REDIS SCAN USE KARO
 * ──────────────────────────────────────────
 */

// Bura — saari keys ke liye Redis ruk jata hai
$keys = Redis::keys('user:*'); // ⚠️ Production mein kabhi nahi!

// Acha — batches mein iterate karta hai
$cursor = 0;
$allKeys = [];
do {
    [$cursor, $keys] = Redis::scan($cursor, ['MATCH' => 'user:*', 'COUNT' => 100]);
    $allKeys = array_merge($allKeys, $keys ?? []);
} while ($cursor != 0);


/*
 * TIP 5: CONNECTION POOLING
 * ─────────────────────────
 * Har request par dubara connect hone se bachne ke liye persistent connections use karo.
 *
 * config/database.php Redis options mein:
 *   'persistent' => true,
 *
 * Zyada traffic wali apps ke liye Twemproxy ya Redis Sentinel jaisa connection pooler sochein.
 *
 *
 * TIP 6: COMPLEX ATOMIC OPERATIONS KE LIYE LUA SCRIPTS USE KARO
 * ─────────────────────────────────────────────────────────────
 */

// Atomic "get aur set" shart ke sath — alag commands se nahi ho sakta
$script = <<<'LUA'
    local current = redis.call('GET', KEYS[1])
    if current == ARGV[1] then
        redis.call('SET', KEYS[1], ARGV[2])
        return 1
    end
    return 0
LUA;

// Compare-and-swap: Sirf tab update karo jab current value expected se match kare
Redis::eval($script, 1, 'my-key', 'expected-value', 'new-value');


/*
 * TIP 7: CACHE STAMPEDE SE BACHAO
 * ───────────────────────────────
 */

// TTL mein random jitter daalo taake sab cache ek sath expire na ho
function cacheWithJitter(string $key, int $baseMinutes, Closure $callback): mixed
{
    $jitter = random_int(0, $baseMinutes * 60 * 0.1); // 10% random jitter
    $ttl = ($baseMinutes * 60) + $jitter;

    return Cache::remember($key, $ttl, $callback);
}

// Hot keys par stampede rokne ke liye lock use karo
function cacheWithLock(string $key, int $minutes, Closure $callback): mixed
{
    $value = Cache::get($key);

    if ($value !== null) {
        return $value;
    }

    $lock = Cache::lock("lock:{$key}", 10);

    if ($lock->get()) {
        try {
            // Lock milne ke baad dubara check karo
            $value = Cache::get($key);
            if ($value !== null) {
                return $value;
            }

            $value = $callback();
            Cache::put($key, $value, now()->addMinutes($minutes));

            return $value;
        } finally {
            $lock->release();
        }
    }

    // Koi aur process cache dubara bana raha hai; intezar karo aur dubara try karo
    usleep(100_000); // 100ms
    return Cache::get($key) ?? $callback();
}


// =============================================================================
// 11. INTERVIEW KE AAM SAWALAAT
// =============================================================================

/*
 * S1: Redis kya hai aur yeh tez kyun hai?
 * J: Redis ek in-memory data store hai. Yeh tez hai kyunke yeh data RAM mein rakhta hai
 *    (disk par nahi), single-threaded hai (locking ka koi bojh nahi), aur optimized
 *    data structures use karta hai. Yeh 1 second mein 100,000+ operations handle kar sakta hai.
 *
 *
 * S2: Redis aur Memcached mein kya farq hai?
 * J:
 * ┌────────────────────────────────────────────────────────────┐
 * │  Feature          │  Redis            │  Memcached         │
 * ├────────────────────────────────────────────────────────────┤
 * │  Data Structures  │  Strings, Hashes, │  Sirf Strings      │
 * │                   │  Lists, Sets,     │                    │
 * │                   │  Sorted Sets, etc │                    │
 * │  Persistence      │  RDB + AOF        │  Koi nahi          │
 * │  Pub/Sub          │  Haan             │  Nahi              │
 * │  Replication      │  Built-in         │  Nahi              │
 * │  Lua Scripting    │  Haan             │  Nahi              │
 * │  Max Value Size   │  512MB            │  1MB               │
 * │  Clustering       │  Built-in         │  Sirf client-side  │
 * │  Istemal          │  Cache + bohot kuch│ Simple caching     │
 * └────────────────────────────────────────────────────────────┘
 *
 *
 * S3: Cache invalidation kya hai aur mushkil kyun hai?
 * J: Cache invalidation ka matlab hai DECIDE karna ke cached data KAB saaf/update karein.
 *    Mushkil hai kyunke:
 *    - Bohot jaldi saaf karo → fuzool DB queries (caching ka maqsad khatam)
 *    - Bohot der se saaf karo → users purana/ghalat data dekhte hain
 *    - Kai services ek hi data update kar sakti hain
 *    Tareeqe: TTL-based, event-based (likhne par saaf karo), write-through cache.
 *
 *
 * S4: Cache stampede kya hai?
 * J: Jab ek maqbool cache key expire hoti hai, bohot sari requests ek sath ise
 *    dubara banane ki koshish karti hain, database par bhari bojh dal deti hain. Hal:
 *    - Distributed locks (sirf ek request dubara banaye)
 *    - Jaldi expire karna (asal TTL se pehle refresh karo)
 *    - Random jitter ke sath TTLs bhatkana
 *
 *
 * S5: Redis persistence kaise kaam karti hai?
 * J: Do tareeqe:
 *    - RDB: Waqfay waqfay snapshots (tez recovery, data loss mumkin)
 *    - AOF: Har write log karti hai (kam se kam data loss, dheemi recovery)
 *    Production: Dono use karo (RDB backups ke liye, AOF recovery ke liye)
 *
 *
 * S6: Redis concurrency kaise handle karta hai jab yeh single-threaded hai?
 * J: Redis commands ek waqt mein ek, ek hi thread mein process karta hai, matlab
 *    har command atomic hai. Lock ki zaroorat nahi. Single-threaded hone ke bawajood,
 *    tez hai kyunke:
 *    - Sara data memory mein hai (disk I/O block nahi karta)
 *    - I/O multiplexing kai connections efficiently handle karta hai
 *    - Operations simple aur tez hain (microseconds)
 *
 *
 * S7: Jab Redis ki memory bhar jaye toh kya hota hai?
 * J: maxmemory-policy par depend karta hai:
 *    - noeviction: Write commands ke liye errors deta hai
 *    - allkeys-lru: Sab se purani use ki gayi keys nikalta hai
 *    - volatile-lru: TTL wali LRU keys nikalta hai
 *    - allkeys-random: Random keys nikalta hai
 *    Best practice: maxmemory set karo aur cache ke liye allkeys-lru use karo.
 *
 *
 * S8: Redis Sentinel aur Redis Cluster mein kya farq hai?
 * J:
 *    Sentinel: High availability (failover) ek Redis instance ke liye
 *      - Primary aur replicas ki nigrani karta hai
 *      - Primary fail hone par khud-ba-khud replica ko promote karta hai
 *      - Sara data ek node mein samata hai
 *
 *    Cluster: Horizontal scaling (sharding — data baant-na)
 *      - Data kai nodes (shards) mein banta hai
 *      - Har shard ke apne replicas ho sakte hain
 *      - Jab data ek node mein na samaye
 *
 *
 * S9: Laravel application mein Redis kaise use karte hain?
 * J: Laravel Redis ko in cheezon ke liye support karta hai:
 *    1) Caching: CACHE_DRIVER=redis, Cache::remember()
 *    2) Sessions: SESSION_DRIVER=redis (servers mein shared)
 *    3) Queues: QUEUE_CONNECTION=redis, Redis queues mein jobs dispatch karo
 *    4) Rate limiting: Built-in throttle middleware Redis use karta hai
 *    5) Broadcasting: Redis Pub/Sub se real-time events
 *    6) Locks: Cache::lock() distributed locking ke liye
 *    7) Seedha: Redis facade custom data structures ke liye
 *
 *
 * S10: ElastiCache kya hai aur kab use karna chahiye?
 * J: ElastiCache AWS ki managed Redis service hai. Tab use karein jab:
 *    - Redis infrastructure khud manage nahi karna chahtay
 *    - Automatic failover aur backups chahiye
 *    - Aap pehle se AWS par hain
 *    - Multi-AZ high availability chahiye
 *    Modes: Cluster Mode Disabled (ek shard, 5 tak replicas)
 *           Cluster Mode Enabled (bari datasets ke liye kai shards)
 *
 *
 * S11: Dheemi Redis instance ko kaise debug karein ge?
 * J: Qadam ba qadam:
 *    1) redis-cli INFO memory → Memory istemal check karo
 *    2) redis-cli SLOWLOG GET 10 → Dheeme commands dhundho
 *    3) redis-cli INFO stats → Hit/miss ratio, evictions check karo
 *    4) redis-cli INFO clients → Connection count check karo
 *    5) KEYS commands check karo (is ki jagah SCAN use karo)
 *    6) Bari keys check karo: redis-cli --bigkeys
 *    7) Network taakhir check karo: redis-cli --latency
 *    8) Persistence settings review karo (AOF fsync frequency)
 *
 *
 * S12: Redis Streams kya hain aur kab use karein ge?
 * J: Streams ek append-only log data structure hai (halka Kafka jaisa).
 *    Istemal:
 *    - Event sourcing (har halat ki tabdeeli record karna)
 *    - Activity logs (user ke amal ki tareekh)
 *    - Microservices ke darmiyan baat cheet
 *    - Processing pipelines (consumer groups yaqeeni banate hain har paigham ek baar process ho)
 *    Commands: XADD, XREAD, XRANGE, XGROUP (consumer groups)
 */


// =============================================================================
// KHULAASA CHEAT SHEET
// =============================================================================

/*
 * ┌─────────────────────────────────────────────────────────────────────┐
 * │                    REDIS CHEAT SHEET                                │
 * ├─────────────────────────────────────────────────────────────────────┤
 * │                                                                     │
 * │  KYA HAI:  In-memory data store (cache, DB, message broker)        │
 * │  TEZI:    ~0.1ms reads, 100K+ ops/sec                             │
 * │  PORT:    6379 (default)                                           │
 * │                                                                     │
 * │  DATA STRUCTURES:                                                   │
 * │    String  → Cache values, counters, locks                         │
 * │    Hash    → Objects (user profiles, settings)                     │
 * │    List    → Queues, activity feeds                                │
 * │    Set     → Unique items, tags, online users                      │
 * │    ZSet    → Leaderboards, trending, priority queues               │
 * │    Stream  → Event logs, message streaming                         │
 * │                                                                     │
 * │  LARAVEL INTEGRATION:                                               │
 * │    Cache:   CACHE_DRIVER=redis + Cache::remember()                 │
 * │    Session: SESSION_DRIVER=redis (koi code tabdeeli nahi)          │
 * │    Queue:   QUEUE_CONNECTION=redis + dispatch()                    │
 * │    Seedha:  Redis::set(), Redis::get(), waghaira                   │
 * │                                                                     │
 * │  AWS ELASTICACHE:                                                   │
 * │    Managed Redis — auto failover, backups, scaling                 │
 * │    Cluster Disabled: 1 primary + 5 tak replicas                    │
 * │    Cluster Enabled:  Bari datasets ke liye kai shards              │
 * │                                                                     │
 * │  PRODUCTION ZAROORIYAAT:                                            │
 * │    ✅ maxmemory + eviction policy (allkeys-lru) set karo           │
 * │    ✅ Persistence use karo (RDB + AOF)                             │
 * │    ✅ Passwords set karo + khatrnaak commands band karo            │
 * │    ✅ Nigrani: memory, hit ratio, evictions, slow log              │
 * │    ✅ Bulk operations ke liye pipelines use karo                   │
 * │    ✅ Cache keys par hamesha TTL set karo                          │
 * │    ❌ Production mein KEYS kabhi use mat karo (SCAN use karo)      │
 * │    ❌ Redis ko kabhi internet par expose mat karo                  │
 * │    ❌ Bari objects kabhi store mat karo (values < 100KB rakhho)    │
 * │                                                                     │
 * └─────────────────────────────────────────────────────────────────────┘
 */
