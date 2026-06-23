<?php

/**
 * ============================================================================
 *           PERFORMANCE OPTIMIZATION — INTERVIEW Q&A
 *                  (6 Years Experience Level)
 * ============================================================================
 */


// =============================================================================
// S1: Laravel app slow hai — kaise debug aur optimize karein?
// =============================================================================

/*
 * J: Yeh SABSE AHEM sawaal hai senior interviews mein. Step by step:
 *
 *   QADAM 1: Masla dhundho (Diagnosis)
 *   ──────────────────────────────────
 *   - Laravel Debugbar install karo → Queries, time, memory dekho
 *   - Laravel Telescope install karo → Requests, jobs, queries monitor karo
 *   - php artisan route:list → Kitni routes hain
 *
 *   QADAM 2: Database Optimize karo
 *   ───────────────────────────────
 *   - N+1 queries fix karo (Eager Loading)
 *   - Indexes lagao (jis column par WHERE/ORDER BY hai)
 *   - SELECT * ki jagah specific columns chunen
 *   - Pagination use karo (sab records ek sath mat laao)
 *
 *   QADAM 3: Caching lagao
 *   ──────────────────────
 *   - Database queries cache karo (Cache::remember)
 *   - Config, routes, views cache karo
 *   - Full-page caching ya API response caching
 *
 *   QADAM 4: Production optimize karo
 *   ─────────────────────────────────
 *   - php artisan optimize
 *   - composer install --optimize-autoloader --no-dev
 *   - OPcache enable karo
 */


// =============================================================================
// S2: Database query optimize karne ke tareeqe batao.
// =============================================================================

// ❌ GHALAT: SELECT * — sab columns laana
$users = User::all();

// ✅ SAHI: Sirf zaruri columns
$users = User::select('id', 'name', 'email')->get();

// ❌ GHALAT: Loop mein queries
foreach (Order::all() as $order) {
    echo $order->user->name;        // N+1!
    echo $order->items->count();    // N+1 again!
}

// ✅ SAHI: Eager loading + withCount
$orders = Order::with('user')
    ->withCount('items')
    ->get();

// ❌ GHALAT: Bina index ke WHERE clause
// SELECT * FROM orders WHERE status = 'pending' AND user_id = 5
// (Index na ho toh FULL TABLE SCAN — bohot dheema!)

// ✅ SAHI: Migration mein index lagao:
Schema::table('orders', function (Blueprint $table) {
    $table->index('status');
    $table->index(['user_id', 'status']); // Composite index
});

// RAW QUERY jab Eloquent dheema ho:
$topSellers = DB::select("
    SELECT users.name, COUNT(orders.id) as order_count
    FROM users
    JOIN orders ON users.id = orders.user_id
    WHERE orders.created_at >= ?
    GROUP BY users.id
    ORDER BY order_count DESC
    LIMIT 10
", [now()->subMonth()]);

// EXPLAIN query dekhein kya ho raha hai:
User::where('email', 'test@test.com')->explain()->dd();


// =============================================================================
// S3: Caching strategies kya hain? Kaunsi kab use karein?
// =============================================================================

/*
 * J:
 *   1) Cache-Aside (Lazy Loading) — Sab se aam:
 *      Pehle cache check karo, nahi mila toh DB se laao aur cache karo
 */

$products = Cache::remember('products:featured', 3600, function () {
    return Product::featured()->with('category')->get();
});

/*
 *   2) Write-Through:
 *      Data save karte waqt hi cache update karo
 */

class ProductService
{
    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        Cache::put("product:{$product->id}", $product, 3600);
        Cache::forget('products:featured'); // Related cache bhi saaf
        return $product;
    }
}

/*
 *   3) Cache Tags — Related cache group mein saaf karo:
 */

Cache::tags(['products'])->put('products:featured', $featured, 3600);
Cache::tags(['products'])->put('products:new', $newProducts, 3600);
Cache::tags(['products'])->flush(); // Sab product caches saaf

/*
 *   ⚠️ SENIOR JAWAB:
 *   - Zyada change hone wala data → Chhota TTL (5-15 min)
 *   - Kam change hone wala data → Lamba TTL (1-24 hours)
 *   - Config/settings → Cache forever, change par invalidate
 *   - User-specific data → Cache mein user_id key mein
 */


// =============================================================================
// S4: OPcache kya hai? Production mein kyun zaruri hai?
// =============================================================================

/*
 * J: OPcache PHP ka bytecode cache hai.
 *
 *   Bina OPcache:
 *   Har request → PHP file padho → Parse karo → Compile karo → Execute karo
 *   ↑ Yeh process har request par BAAR BAAR hota hai
 *
 *   OPcache ke sath:
 *   Pehli request → Parse + Compile → CACHE mein rakhho
 *   Baad ki requests → SEEDHA cache se execute → 3-4x TEZ!
 *
 *   Enable karo php.ini mein:
 *   opcache.enable=1
 *   opcache.memory_consumption=256
 *   opcache.max_accelerated_files=20000
 *   opcache.validate_timestamps=0    # Production mein (deploy par restart karo)
 *
 *   ⚠️ validate_timestamps=0 ka matlab:
 *   File badli toh bhi purana cached code chalay ga
 *   Deploy ke baad PHP-FPM restart karo: sudo systemctl restart php8.2-fpm
 */


// =============================================================================
// S5: Database Indexing kab aur kaise karein?
// =============================================================================

/*
 * J: Index = Kitab ka fehrist (table of contents)
 *    Bina index ke MySQL POORI table scan karta hai — lakhon rows par BOHOT DHEEMA.
 *
 *   KAB INDEX LAGAO:
 *   ✅ WHERE clause mein use hone wale columns
 *   ✅ JOIN conditions mein use hone wale columns
 *   ✅ ORDER BY mein use hone wale columns
 *   ✅ Foreign keys (Laravel khud lagata hai)
 *
 *   KAB MAT LAGAO:
 *   ❌ Bohot chhoti tables (< 1000 rows)
 *   ❌ Columns jo bohot zyada update hote hain
 *   ❌ Columns jis mein bohot kam unique values hain (maslan: gender)
 */

// Migration mein:
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->index();  // FK + Index
    $table->string('status')->index();                      // Status par search
    $table->decimal('total', 10, 2);
    $table->timestamps();

    // Composite index — jab dono columns SATH mein use hon:
    $table->index(['user_id', 'status']);    // user_id + status SATH mein
    $table->index(['created_at', 'status']); // Date + status SATH mein
});

/*
 * ⚠️ COMPOSITE INDEX KA ORDER MATTER KARTA HAI:
 *   index(['user_id', 'status'])
 *   → WHERE user_id = 5                    ✅ Index use hoga
 *   → WHERE user_id = 5 AND status = 'paid' ✅ Index use hoga
 *   → WHERE status = 'paid'                 ❌ Index use NAHI hoga!
 *   (Left-most column pehle hona chahiye)
 */


// =============================================================================
// S6: Laravel app mein memory leak kaise dhundein?
// =============================================================================

/*
 * J:
 *   1. Queue workers mein memory leak aam hai
 *      → --max-jobs=1000 ya --max-memory=128 use karo
 *
 *   2. Global variables ya static properties mein data jama hota raha
 *      → Har request ke baad clean karo
 *
 *   3. Event listeners mein heavy objects store karna
 *      → Weak references use karo ya listeners unregister karo
 *
 *   4. Laravel Octane mein KHAAS ehtiyat:
 *      → Singleton services mein state mat rakhho
 *      → Request-scoped data properly clean karo
 *
 *   DEBUG KARO:
 *   memory_get_usage(true)  → Current memory
 *   memory_get_peak_usage() → Peak memory
 *
 *   Queue worker mein:
 *   php artisan queue:work --max-memory=128    → 128MB se zyada ho toh restart
 *   php artisan queue:work --max-jobs=500      → 500 jobs ke baad restart
 */
