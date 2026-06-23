<?php

/**
 * ============================================================================
 *                    AWS RDS — MUKAMMAL GUIDE
 *        Database Setup, Options, aur Laravel Integration
 * ============================================================================
 */


// =============================================================================
// 1. RDS KYA HAI?
// =============================================================================

/*
 * RDS = Relational Database Service
 *
 * RDS AWS ki managed database service hai. AWS database ka sara kaam sambhalta hai:
 *   - Installation aur setup
 *   - Backups (automatic daily)
 *   - Patching aur updates
 *   - Failover (kharabi mein khud switch ho jata hai)
 *   - Scaling (bada/chhota karna)
 *   - Monitoring
 *
 * AASAN MISAAL:
 * ─────────────
 *   EC2 par MySQL install = Apni gaari ki service khud karna
 *   AWS RDS              = Gaari service center ko de dena (wo sab karein ge)
 *
 *
 * RDS MEIN KYA KYA DATABASE OPTIONS HAIN:
 * ────────────────────────────────────────
 * ┌─────────────────────────────────────────────────────────────────────┐
 * │  Database Engine    │  Kab Use Karein                               │
 * ├─────────────────────────────────────────────────────────────────────┤
 * │  MySQL              │  Sab se aam, Laravel ka default, free tier    │
 * │  PostgreSQL         │  Advanced features, complex queries           │
 * │  MariaDB            │  MySQL jaisa magar open-source community      │
 * │  Amazon Aurora      │  AWS ka custom engine — MySQL/PG se 5x tez   │
 * │  Oracle             │  Enterprise apps (mahnga)                     │
 * │  SQL Server         │  Microsoft ecosystem (.NET apps)              │
 * └─────────────────────────────────────────────────────────────────────┘
 *
 * LARAVEL KE LIYE TAVSIYA:
 *   Development/Chhoti app → MySQL ya PostgreSQL (Free Tier: db.t3.micro)
 *   Production/Bari app    → Amazon Aurora MySQL (tez + auto-scaling)
 *
 *
 * RDS vs EC2 par Database:
 * ────────────────────────
 * ┌──────────────────────────────────────────────────────────────────┐
 * │  EC2 par Database           │  AWS RDS                          │
 * ├──────────────────────────────────────────────────────────────────┤
 * │  Khud install/manage karo   │  AWS manage karta hai             │
 * │  Khud backup lo             │  Automatic daily backups          │
 * │  Khud failover sambhalo     │  Multi-AZ auto failover           │
 * │  Khud patch lagao           │  Automatic patching               │
 * │  Poora control              │  Kam control, kam kaam            │
 * │  Sasta (EC2 ki qeemat)     │  Thora mahnga (managed service)   │
 * │  Chhoti apps / testing      │  Production apps (TAVSIYA)        │
 * └──────────────────────────────────────────────────────────────────┘
 */


// =============================================================================
// 2. RDS INSTANCE BANANA (Step-by-Step)
// =============================================================================

/*
 * QADAM 1: AWS Console → RDS → "Create database"
 *
 * QADAM 2: Creation method:
 *   - "Standard create" (poora control)
 *
 * QADAM 3: Engine select karein:
 *   - MySQL 8.0 (ya PostgreSQL 15)
 *   - ⚠️ Free Tier eligible check karo
 *
 * QADAM 4: Templates:
 *   - Free tier (testing / dev)
 *   - Production (real apps ke liye)
 *
 * QADAM 5: Settings:
 *   - DB instance identifier: my-laravel-db
 *   - Master username: admin
 *   - Master password: StrongPassword123!
 *   - ⚠️ Password yaad rakhein — baad mein .env mein daalein ge
 *
 * QADAM 6: Instance configuration:
 *   - db.t3.micro (Free Tier — 1 vCPU, 1 GB RAM)
 *   - db.t3.medium (Chhoti production — 2 vCPU, 4 GB RAM)
 *   - db.r5.large (Bari production — 2 vCPU, 16 GB RAM)
 *
 * QADAM 7: Storage:
 *   - Type: General Purpose SSD (gp3)
 *   - Size: 20 GB (Free Tier) ya 100+ GB (production)
 *   - Storage autoscaling: Enable (zaroorat par khud barhay)
 *
 * QADAM 8: Connectivity:
 *   - VPC: Apna VPC (ya default)
 *   - Subnet group: Default
 *   - Public access: NO! (⚠️ RDS ko kabhi public mat karo)
 *   - Security group: Naya banayen ya purana chunen
 *     → Inbound rule: MySQL/Aurora, port 3306, source: EC2 ka security group
 *
 * QADAM 9: Database authentication:
 *   - Password authentication (simple)
 *   - Ya IAM database authentication (zyada mehfooz)
 *
 * QADAM 10: Additional configuration:
 *   - Initial database name: myapp
 *   - Automated backups: Enable
 *   - Backup retention: 7 din
 *   - Encryption: Enable (KMS key)
 *   - Monitoring: Enhanced monitoring enable karo
 *
 * QADAM 11: "Create database" — 5-10 minute lagte hain
 *
 *
 * ENDPOINT HAASIL KARNA:
 * ──────────────────────
 *   RDS → Databases → my-laravel-db → "Endpoint & port"
 *   Endpoint: my-laravel-db.abc123def.ap-south-1.rds.amazonaws.com
 *   Port: 3306
 */


// =============================================================================
// 3. LARAVEL SE RDS CONNECT KARNA
// =============================================================================

// .env file mein yeh add karo:
/*
 *   DB_CONNECTION=mysql
 *   DB_HOST=my-laravel-db.abc123def.ap-south-1.rds.amazonaws.com
 *   DB_PORT=3306
 *   DB_DATABASE=myapp
 *   DB_USERNAME=admin
 *   DB_PASSWORD=StrongPassword123!
 */

// Test karo connection:
/*
 *   php artisan migrate:status
 *
 *   Agar connect ho jaye toh migration tables ki list aaye gi.
 *   Agar error aaye:
 *     - Security group check karo (EC2 se 3306 allow hai?)
 *     - VPC check karo (EC2 aur RDS ek hi VPC mein hain?)
 *     - Endpoint sahi hai?
 *     - Password sahi hai?
 */


// =============================================================================
// 4. MULTI-AZ DEPLOYMENT (High Availability)
// =============================================================================

/*
 * Multi-AZ matlab: Database ki copy do mukhtalif data centers mein
 *
 *   ┌──────────────────────────────────────────────────┐
 *   │  AZ-1 (Mumbai-1a)        AZ-2 (Mumbai-1b)       │
 *   │  ┌──────────────┐       ┌──────────────┐        │
 *   │  │ Primary DB   │ ───→  │ Standby DB   │        │
 *   │  │ (Read/Write) │ sync  │ (Backup copy)│        │
 *   │  └──────────────┘       └──────────────┘        │
 *   └──────────────────────────────────────────────────┘
 *
 *   Agar Primary mar jaye → Standby khud PRIMARY ban jata hai (60-120 seconds)
 *   Laravel ko pata bhi nahi chalta — endpoint same rehta hai!
 *
 *   Kab use karein:
 *   - Production apps mein HAMESHA enable karo
 *   - Thora mahnga hai magar downtime se bacha leta hai
 *
 *
 * READ REPLICAS:
 * ──────────────
 *   ┌──────────────────────────────────────────────────┐
 *   │  Primary DB (Read/Write)                         │
 *   │       │                                          │
 *   │       ├──→ Read Replica 1 (Sirf Read)            │
 *   │       ├──→ Read Replica 2 (Sirf Read)            │
 *   │       └──→ Read Replica 3 (Sirf Read)            │
 *   └──────────────────────────────────────────────────┘
 *
 *   - Write operations → Primary DB
 *   - Read operations → Read Replicas (load kam hota hai)
 *   - 5 Read Replicas tak bana sakte hain
 *
 * Laravel mein Read/Write splitting:
 */

// config/database.php:
$dbConfig = [
    'mysql' => [
        'read' => [
            'host' => [
                'replica-1.abc123.rds.amazonaws.com',
                'replica-2.abc123.rds.amazonaws.com',
            ],
        ],
        'write' => [
            'host' => [
                'primary.abc123.rds.amazonaws.com',
            ],
        ],
        'driver'   => 'mysql',
        'database' => 'myapp',
        'username' => 'admin',
        'password' => 'StrongPassword123!',
        'charset'  => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'sticky'   => true,  // Write ke baad same request mein read bhi primary se
    ],
];


// =============================================================================
// 5. AMAZON AURORA (Premium Database)
// =============================================================================

/*
 * Aurora AWS ka khud ka database engine hai:
 *   - MySQL se 5 guna tez
 *   - PostgreSQL se 3 guna tez
 *   - Automatic storage scaling (10 GB se 128 TB tak)
 *   - 6 copies of data across 3 AZs
 *   - Auto-healing storage
 *
 * AURORA SERVERLESS:
 * ─────────────────
 *   - Use hone par khud scale up
 *   - Use na hone par khud scale down (0 tak)
 *   - Sirf use ke hisab se paisa
 *   - Dev/staging environments ke liye behtareen (raat ko band, din ko chalu)
 *
 *   Tavsiya:
 *   - Chhoti apps: Regular RDS MySQL (sasta)
 *   - Medium apps: Aurora MySQL (tez + reliable)
 *   - Variable load: Aurora Serverless (scale hota hai)
 */


// =============================================================================
// 6. RDS BEST PRACTICES
// =============================================================================

/*
 * ✅ Multi-AZ enable karo production mein
 * ✅ Automated backups enable rakhho (7+ din retention)
 * ✅ Encryption enable karo (at-rest aur in-transit)
 * ✅ Public access OFF rakhho — sirf VPC ke andar se access
 * ✅ Security group mein sirf EC2 se access allow karo
 * ✅ Enhanced monitoring enable karo
 * ✅ Read Replicas use karo zyada read traffic ke liye
 * ✅ Parameter groups optimize karo
 *
 * ❌ RDS ko public internet par expose mat karo
 * ❌ Master password simple mat rakhho
 * ❌ Backups disable mat karo
 * ❌ Production mein db.t2.micro mat use karo
 */
