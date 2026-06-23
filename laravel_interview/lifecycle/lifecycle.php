<?php

/**
 * ============================================================================
 *              LARAVEL REQUEST LIFECYCLE — INTERVIEW Q&A
 *                    (6 Years Experience Level)
 * ============================================================================
 */


// =============================================================================
// S1: Laravel mein request lifecycle kya hai? Step by step batao.
// =============================================================================

/*
 * J: Jab koi user browser mein URL type karta hai ya API call karta hai,
 *    Laravel mein yeh QADAM hote hain:
 *
 *   QADAM 1: public/index.php
 *   ─────────────────────────
 *   - Sab se pehle request public/index.php par aati hai
 *   - Yeh file Composer autoloader load karti hai
 *   - Laravel application instance banata hai (bootstrap/app.php se)
 *
 *   QADAM 2: HTTP Kernel
 *   ────────────────────
 *   - Request HTTP Kernel (app/Http/Kernel.php) ko jaati hai
 *   - Kernel request ko middleware ki PIPELINE se guzarta hai
 *   - Global middleware sab se pehle chalte hain
 *
 *   QADAM 3: Service Providers Boot
 *   ───────────────────────────────
 *   - Sab Service Providers register hote hain (register method)
 *   - Phir sab ke boot methods chalte hain
 *   - config/app.php mein listed providers load hote hain
 *
 *   QADAM 4: Router
 *   ───────────────
 *   - Request Router ko milti hai
 *   - Router matching route dhundta hai
 *   - Route-specific middleware chalte hain
 *
 *   QADAM 5: Controller / Closure
 *   ─────────────────────────────
 *   - Matched route ka controller ya closure execute hota hai
 *   - Business logic yahan chalti hai
 *   - Response banta hai
 *
 *   QADAM 6: Response
 *   ─────────────────
 *   - Response middleware se wapas guzarti hai (reverse order mein)
 *   - Response user ko bhej di jaati hai
 *   - Terminable middleware chalte hain (maslan: session save)
 *
 *
 *   FLOW DIAGRAM:
 *
 *   User Request
 *       │
 *       ▼
 *   public/index.php
 *       │
 *       ▼
 *   Bootstrap (autoload + app instance)
 *       │
 *       ▼
 *   HTTP Kernel
 *       │
 *       ▼
 *   Global Middleware (pipeline)
 *       │
 *       ▼
 *   Service Providers (register → boot)
 *       │
 *       ▼
 *   Router (route matching)
 *       │
 *       ▼
 *   Route Middleware
 *       │
 *       ▼
 *   Controller / Action
 *       │
 *       ▼
 *   Response (middleware se wapas)
 *       │
 *       ▼
 *   User ko Response
 */


// =============================================================================
// S2: Service Provider mein register() aur boot() mein kya farq hai?
// =============================================================================

/*
 * J: Yeh BOHOT AHEM farq hai aur interview mein aksar poochha jata hai:
 *
 *   register() METHOD:
 *   ──────────────────
 *   - Sirf service container mein cheezein BIND karne ke liye
 *   - Kisi doosri service par depend MAT karo yahan
 *   - Sab providers ke register pehle chalte hain
 *   - Yahan sirf $this->app->bind(), $this->app->singleton() waghaira
 *
 *   boot() METHOD:
 *   ──────────────
 *   - Sab providers register hone ke BAAD chalta hai
 *   - Yahan sab services available hain
 *   - Event listeners, route model binding, view composers yahan lagao
 *   - Kisi bhi doosri service ko use kar sakte ho
 *
 *
 *   MISAAL:
 */

// register() — Sirf bind karo
class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // ✅ Sahi — sirf container mein bind karna
        $this->app->singleton(PaymentGateway::class, function ($app) {
            return new StripePaymentGateway(config('services.stripe.key'));
        });
    }

    public function boot(): void
    {
        // ✅ Sahi — doosri services use kar sakte hain
        Event::listen(OrderPlaced::class, ProcessPayment::class);

        // Route model binding
        Route::model('payment', Payment::class);

        // View composer
        View::composer('checkout.*', CartComposer::class);
    }
}


// =============================================================================
// S3: Middleware kya hai? Kitni qisam ke hote hain?
// =============================================================================

/*
 * J: Middleware ek filter hai jo request aur response ke darmiyan aata hai.
 *    Jaise building ke darwaze par guard — pehle check karega phir andar jane dega.
 *
 *   TEEN QISMAIN:
 *
 *   1) GLOBAL MIDDLEWARE:
 *      - Har request par chalta hai
 *      - app/Http/Kernel.php ke $middleware array mein
 *      - Maslan: TrustProxies, HandleCors, PreventRequestsDuringMaintenance
 *
 *   2) ROUTE MIDDLEWARE:
 *      - Khaas routes par lagao
 *      - Kernel ke $middlewareAliases mein define
 *      - Maslan: auth, throttle, verified
 *
 *   3) MIDDLEWARE GROUPS:
 *      - Kai middleware ka group
 *      - Maslan: 'web' group (session, csrf, cookies)
 *      -         'api' group (throttle, bindings)
 *
 *
 *   BEFORE vs AFTER MIDDLEWARE:
 */

// Before Middleware — Request controller tak pahunchne se PEHLE chalta hai
class CheckAge
{
    public function handle($request, Closure $next)
    {
        if ($request->age < 18) {
            return redirect('home'); // Request controller tak nahi pahunchti
        }

        return $next($request); // Controller ko bhejo
    }
}

// After Middleware — Controller ke response ke BAAD chalta hai
class AddSecurityHeaders
{
    public function handle($request, Closure $next)
    {
        $response = $next($request); // Pehle controller chale

        // Response mein headers add karo
        $response->header('X-Frame-Options', 'DENY');
        $response->header('X-Content-Type-Options', 'nosniff');

        return $response;
    }
}

// Terminable Middleware — Response bhejne ke BAAD chalta hai
class LogRequest
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response): void
    {
        // Response bheji ja chuki hai, ab background mein log karo
        Log::info('Request completed', [
            'url' => $request->url(),
            'status' => $response->status(),
        ]);
    }
}


// =============================================================================
// S4: php artisan optimize kya karta hai?
// =============================================================================

/*
 * J: optimize command Laravel ki performance barhane ke liye caching karta hai:
 *
 *   php artisan optimize
 *   ↑ Yeh andar se yeh sab chalata hai:
 *
 *   1. config:cache  → Saari config files ek file mein cache
 *   2. route:cache   → Sab routes compile karke cache
 *   3. view:cache    → Blade templates compile karke cache
 *   4. event:cache   → Events aur listeners cache
 *
 *   Cache saaf karne ke liye:
 *   php artisan optimize:clear
 *
 *   ⚠️ Production mein HAMESHA optimize chalao
 *   ⚠️ Development mein MAT chalao (changes dikhenge nahi)
 *
 *
 *   INDIVIDUAL COMMANDS:
 *   php artisan config:cache    → Config cache (env() sirf config files mein chalega)
 *   php artisan route:cache     → Route cache (closures kaam nahi karein gi)
 *   php artisan view:cache      → View cache (Blade pre-compiled)
 *   php artisan event:cache     → Event listeners cache
 */


// =============================================================================
// S5: Laravel mein Facade kya hai? Kaise kaam karta hai?
// =============================================================================

/*
 * J: Facade ek static-like interface hai jo service container ki classes ko
 *    aasan tareeqe se access deta hai.
 *
 *   Cache::get('key')
 *   ↑ Yeh static nahi hai — yeh FACADE hai!
 *
 *   Andar se kya hota hai:
 *   1. Cache:: call hota hai
 *   2. Facade getFacadeAccessor() method chalta hai → 'cache' return karta hai
 *   3. Service container se 'cache' resolve hota hai
 *   4. Asli object ka get('key') method chalta hai
 *
 *   Matlab: Cache::get('key') === app('cache')->get('key')
 *
 *   Facade ka fayda:
 *   - Code chhota aur saaf hota hai
 *   - Testing mein aasani se mock ho sakte hain
 *   - Service container ke through resolve hote hain
 *
 *   ⚠️ 6 saal ke experience mein interviewer expect karta hai ke aap
 *      Dependency Injection prefer karein Facade se:
 */

// Facade (kaam karta hai magar DI behtar hai)
class OrderController extends Controller
{
    public function index()
    {
        $orders = Cache::get('orders'); // Facade
        return view('orders.index', compact('orders'));
    }
}

// Dependency Injection (TAVSIYA — senior level)
class OrderControllerBetter extends Controller
{
    public function __construct(
        private CacheManager $cache  // DI — testing mein aasani
    ) {}

    public function index()
    {
        $orders = $this->cache->get('orders');
        return view('orders.index', compact('orders'));
    }
}


// =============================================================================
// S6: Laravel bootstrapping process mein kya hota hai?
// =============================================================================

/*
 * J: Jab Laravel application shuru hoti hai (bootstrap/app.php):
 *
 *   1. Application instance banta hai
 *      $app = new Illuminate\Foundation\Application(...)
 *
 *   2. Important bindings register hote hain:
 *      - HTTP Kernel
 *      - Console Kernel
 *      - Exception Handler
 *
 *   3. Bootstrappers chalte hain (is tartib se):
 *      a) LoadEnvironmentVariables → .env file load
 *      b) LoadConfiguration → config/ files load
 *      c) HandleExceptions → Error/exception handler set
 *      d) RegisterFacades → Facades register
 *      e) RegisterProviders → Service providers register
 *      f) BootProviders → Service providers boot
 *
 *   4. Request handle hoti hai
 *   5. Response bheji jaati hai
 *   6. Terminable middleware chalte hain
 *
 *   ⚠️ Yeh order IMPORTANT hai — interview mein exact order pooch sakte hain
 */


// =============================================================================
// S7: artisan serve aur Nginx/Apache mein kya farq hai?
// =============================================================================

/*
 * J:
 *   php artisan serve:
 *   - PHP ka built-in development server
 *   - Single-threaded (ek waqt mein ek request)
 *   - SIRF development ke liye
 *   - ❌ Production mein KABHI use mat karo
 *
 *   Nginx + PHP-FPM:
 *   - Professional web server
 *   - Multiple requests ek sath handle karta hai
 *   - Static files efficiently serve karta hai
 *   - Reverse proxy, load balancing kar sakta hai
 *   - ✅ Production ke liye ZARURI hai
 *
 *   Apache + mod_php:
 *   - Purana magar still popular
 *   - .htaccess files support karta hai
 *   - Nginx se thora dheema heavy load mein
 */
