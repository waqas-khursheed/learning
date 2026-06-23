<?php

/**
 * ============================================================================
 *                    MICROSERVICES — MUKAMMAL GUIDE
 *        Kya hai, Kab Use Karein, AWS par Kaise Banayein
 * ============================================================================
 */


// =============================================================================
// 1. MICROSERVICES KYA HAI?
// =============================================================================

/*
 * MONOLITH vs MICROSERVICES:
 *
 * MONOLITH (Ek Bada Application):
 * ────────────────────────────────
 *   ┌──────────────────────────────────────┐
 *   │         EK LARAVEL APP               │
 *   │                                      │
 *   │  Users + Orders + Payments + Email   │
 *   │  + Notifications + Reports + Chat    │
 *   │                                      │
 *   │  Ek database, ek deployment          │
 *   └──────────────────────────────────────┘
 *
 *   Fayda: Simple, jaldi development, chhoti teams ke liye acha
 *   Nuqsan: Bara ho jaye toh mushkil, ek hissa toota toh sab toota
 *
 *
 * MICROSERVICES (Chhote Alag Services):
 * ─────────────────────────────────────
 *   ┌─────────────┐  ┌─────────────┐  ┌─────────────┐
 *   │ User Service │  │Order Service│  │Payment Svc  │
 *   │  (Laravel)   │  │  (Laravel)  │  │  (Node.js)  │
 *   │  DB: users   │  │  DB: orders │  │  DB: payments│
 *   └─────────────┘  └─────────────┘  └─────────────┘
 *         │                │                │
 *         └────────────────┼────────────────┘
 *                          │
 *                    [API Gateway]
 *                          │
 *                       [Users]
 *
 *   Fayda: Har service alag scale, alag deploy, alag technology
 *   Nuqsan: Complex, network latency, debugging mushkil
 *
 *
 * KAB MICROSERVICES USE KAREIN:
 * ────────────────────────────
 *   ✅ Bari team (10+ developers)
 *   ✅ Bohot zyada traffic (alag alag hisse alag scale)
 *   ✅ Mukhtalif technologies chahiye (PHP + Node + Python)
 *   ✅ Independent deployments chahiye
 *
 *   ❌ Chhoti team (1-5 developers) → Monolith se shuru karo!
 *   ❌ Simple app → Microservices OVERKILL hai
 *   ❌ Pehla project → Monolith seekho pehle
 *
 *
 * ⚠️ AHEM: "Monolith se shuru karo, zaroorat par microservices mein baanto"
 *    — Yeh sab se behtar tareeqa hai. Pehle din se microservices mat banao!
 */


// =============================================================================
// 2. MICROSERVICES ARCHITECTURE AWS PAR
// =============================================================================

/*
 *   ┌──────────────────────────────────────────────────────────────┐
 *   │                        USERS                                 │
 *   │                          │                                   │
 *   │                    [CloudFront]                              │
 *   │                          │                                   │
 *   │                  [API Gateway / ALB]                         │
 *   │                    │         │                               │
 *   │         ┌──────────┘         └──────────┐                   │
 *   │         │                               │                   │
 *   │   /api/users/*                    /api/orders/*             │
 *   │         │                               │                   │
 *   │  ┌──────────────┐            ┌──────────────┐              │
 *   │  │ User Service │            │ Order Service│              │
 *   │  │ (ECS Fargate)│            │ (ECS Fargate)│              │
 *   │  └──────┬───────┘            └──────┬───────┘              │
 *   │         │                           │                       │
 *   │  ┌──────────────┐            ┌──────────────┐              │
 *   │  │ RDS (Users)  │            │ RDS (Orders) │              │
 *   │  └──────────────┘            └──────────────┘              │
 *   │                                                             │
 *   │              ┌──────────────────┐                           │
 *   │              │  SQS / SNS       │ ← Services ke darmiyan   │
 *   │              │  (Message Queue) │   baat cheet              │
 *   │              └──────────────────┘                           │
 *   │                                                             │
 *   │              ┌──────────────────┐                           │
 *   │              │  ElastiCache     │ ← Shared cache            │
 *   │              │  (Redis)         │                           │
 *   │              └──────────────────┘                           │
 *   └──────────────────────────────────────────────────────────────┘
 */


// =============================================================================
// 3. SERVICES KE DARMIYAN BAAT CHEET
// =============================================================================

/*
 * TAREEQA 1: HTTP/REST APIs (Synchronous)
 * ──────────────────────────────────────
 *   User Service → HTTP request → Order Service
 *   User Service response ka intezar karta hai
 *
 *   Fayda: Simple, samajhna aasan
 *   Nuqsan: Dheema (network call), agar Order Service band ho toh masla
 */

// User Service mein Order Service ko call karna:
class OrderClient
{
    public function getUserOrders(int $userId): array
    {
        $response = Http::timeout(5)
            ->retry(3, 100)
            ->get("http://order-service.internal/api/orders", [
                'user_id' => $userId,
            ]);

        if ($response->failed()) {
            return []; // Fallback — khali list
        }

        return $response->json();
    }
}


/*
 * TAREEQA 2: Message Queues (Asynchronous) — TAVSIYA
 * ──────────────────────────────────────────────────
 *   User Service → SQS Queue → Order Service
 *   User Service intezar NAHI karta — message bhej ke chala jata hai
 *
 *   Fayda: Tez, services alag rehti hain, ek band ho toh doosri par asar nahi
 *   Nuqsan: Thora complex, eventual consistency
 *
 *
 * AWS SQS (Simple Queue Service):
 *
 *   User registers → SNS Topic "user-registered" → SQS Queues:
 *     → Order Service queue (default order banao)
 *     → Email Service queue (welcome email bhejo)
 *     → Analytics Service queue (registration track karo)
 */


// =============================================================================
// 4. AWS API GATEWAY
// =============================================================================

/*
 * API Gateway = Sab services ke liye ek darwaza
 *
 *   Bina API Gateway:
 *     Client ko har service ka URL pata hona chahiye
 *     users.myapp.com, orders.myapp.com, payments.myapp.com
 *     ↑ Complex aur mushkil
 *
 *   API Gateway ke sath:
 *     Client sirf ek URL jaanta hai: api.myapp.com
 *     API Gateway khud route karta hai:
 *       /users/*    → User Service
 *       /orders/*   → Order Service
 *       /payments/* → Payment Service
 *
 *
 * API GATEWAY KYA KARTA HAI:
 *   - Request routing (kaunsi service ko bhejein)
 *   - Authentication (API key, JWT verify karna)
 *   - Rate limiting (bohot zyada requests rok do)
 *   - Request/Response transformation
 *   - Caching
 *   - Monitoring aur logging
 *
 *
 * ALB BHI API GATEWAY JAISA KAAM KAR SAKTA HAI:
 *   ALB → Listener Rules → Host/Path based routing
 *   Chhote projects ke liye ALB kafi hai
 *   Bare projects ke liye AWS API Gateway use karo
 */


// =============================================================================
// 5. LARAVEL MEIN MICROSERVICES PATTERN
// =============================================================================

/*
 * TAREEQA 1: ALAG LARAVEL APPS
 * ─────────────────────────────
 *   Har service apni alag Laravel app:
 *   - user-service/      (Laravel app)
 *   - order-service/     (Laravel app)
 *   - payment-service/   (Node.js app)
 *
 *   Har ek ka apna:
 *   - Git repo
 *   - Database
 *   - Docker image
 *   - ECS service
 *   - CI/CD pipeline
 *
 *
 * TAREEQA 2: LARAVEL MODULES (Monolith-to-Micro)
 * ──────────────────────────────────────────────
 *   Ek Laravel app mein modules banayen:
 *   - app/Modules/User/
 *   - app/Modules/Order/
 *   - app/Modules/Payment/
 *
 *   Pehle monolith mein modules banao, baad mein zaroorat par
 *   modules ko alag services mein nikaal lo.
 *   ↑ YEH SAB SE BEHTAR APPROACH HAI beginners ke liye
 */

// Module structure example:
/*
 *   app/
 *   ├── Modules/
 *   │   ├── User/
 *   │   │   ├── Controllers/
 *   │   │   ├── Models/
 *   │   │   ├── Services/
 *   │   │   ├── Routes/
 *   │   │   └── Events/
 *   │   ├── Order/
 *   │   │   ├── Controllers/
 *   │   │   ├── Models/
 *   │   │   ├── Services/
 *   │   │   ├── Routes/
 *   │   │   └── Events/
 *   │   └── Payment/
 *   │       ├── Controllers/
 *   │       ├── Models/
 *   │       ├── Services/
 *   │       ├── Routes/
 *   │       └── Events/
 */


// =============================================================================
// 6. MICROSERVICES BEST PRACTICES
// =============================================================================

/*
 * ✅ Monolith se shuru karo, zaroorat par microservices mein baanto
 * ✅ Har service ka apna database ho (database per service)
 * ✅ Asynchronous communication use karo (SQS/SNS)
 * ✅ API Gateway use karo (ek entry point)
 * ✅ Health checks har service mein lagao
 * ✅ Centralized logging (CloudWatch)
 * ✅ Circuit breaker pattern use karo (ek service band ho toh doosri na rukey)
 * ✅ Docker + ECS use karo deployment ke liye
 *
 * ❌ Pehle din se microservices mat banao
 * ❌ Services ke darmiyan database share mat karo
 * ❌ Synchronous calls ki chain mat banao (A → B → C → D)
 * ❌ Bina monitoring ke microservices mat chalao
 */
