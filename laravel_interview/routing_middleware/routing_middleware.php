<?php

/**
 * ============================================================================
 *              ROUTING & MIDDLEWARE — INTERVIEW Q&A
 *                  (6 Years Experience Level)
 * ============================================================================
 */


// =============================================================================
// S1: Route Model Binding kya hai? Implicit vs Explicit?
// =============================================================================

/*
 * J: Route Model Binding = URL ke parameter se khud model resolve ho jaye.
 */

// IMPLICIT BINDING — Laravel KHUD resolve karta hai:
// Route: /users/{user}
Route::get('/users/{user}', function (User $user) {
    return $user;  // Laravel ne {user} ID se User::findOrFail() khud kar diya
});
// /users/5 → User::findOrFail(5)
// Agar na mile → 404 automatically

// Custom column se resolve:
Route::get('/posts/{post:slug}', function (Post $post) {
    return $post;  // slug column se dhundega
});
// /posts/my-first-post → Post::where('slug', 'my-first-post')->firstOrFail()

// Model mein default column badlo:
class Post extends Model
{
    public function getRouteKeyName(): string
    {
        return 'slug'; // Ab hamesha slug se resolve hoga
    }
}


// EXPLICIT BINDING — Service Provider mein khud define karo:
// AppServiceProvider ya RouteServiceProvider ke boot() mein:
Route::bind('user', function (string $value) {
    return User::where('username', $value)
        ->where('is_active', true)
        ->firstOrFail();
});


// SCOPED BINDING — Nested resources mein parent-child check:
Route::get('/users/{user}/posts/{post:slug}', function (User $user, Post $post) {
    return $post; // Post ZAROOR is user ki honi chahiye
});
// Auto check: post.user_id = user.id


// =============================================================================
// S2: Rate Limiting kaise kaam karta hai?
// =============================================================================

// AppServiceProvider ya RouteServiceProvider mein define karo:
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});

// Custom rate limiters:
RateLimiter::for('login', function (Request $request) {
    return [
        Limit::perMinute(5)->by($request->ip()),            // 5 attempts per IP
        Limit::perMinute(10)->by($request->input('email')), // 10 per email
    ];
});

RateLimiter::for('uploads', function (Request $request) {
    return $request->user()->isPremium()
        ? Limit::none()                    // Premium users: no limit
        : Limit::perMinute(10);            // Free users: 10 per minute
});

// Route par lagao:
Route::post('/login', LoginController::class)->middleware('throttle:login');


// =============================================================================
// S3: Route Groups, Prefixes, aur Namespaces kaise use karein?
// =============================================================================

// Prefix + Middleware + Name:
Route::prefix('admin')
    ->middleware(['auth', 'admin'])
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        // URL: /admin/dashboard, Name: admin.dashboard

        Route::resource('products', AdminProductController::class);
        // admin.products.index, admin.products.create, etc.
    });

// Controller Group (Laravel 9+):
Route::controller(OrderController::class)->group(function () {
    Route::get('/orders', 'index');
    Route::post('/orders', 'store');
    Route::get('/orders/{order}', 'show');
});

// API Versioning:
Route::prefix('api/v1')->name('api.v1.')->group(function () {
    Route::apiResource('users', Api\V1\UserController::class);
});

Route::prefix('api/v2')->name('api.v2.')->group(function () {
    Route::apiResource('users', Api\V2\UserController::class);
});


// =============================================================================
// S4: Custom Middleware kaise banayen? Real example do.
// =============================================================================

// php artisan make:middleware EnsureUserIsAdmin

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->is_admin) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            abort(403, 'Aap ko yeh page dekhne ki ijazat nahi hai.');
        }

        return $next($request);
    }
}

// Middleware with Parameters:
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()->hasAnyRole($roles)) {
            abort(403);
        }
        return $next($request);
    }
}
// Route::get('/reports', ReportController::class)->middleware('role:admin,manager');


// =============================================================================
// S5: Resource Controllers aur API Resource Controllers mein farq?
// =============================================================================

/*
 * J:
 *   Route::resource()     → 7 routes (index, create, store, show, edit, update, destroy)
 *   Route::apiResource()  → 5 routes (create aur edit NAHI — API mein form nahi chahiye)
 *
 * ┌────────────────────────────────────────────────────────────────────┐
 * │  Method    │  URI                │  Action  │  Resource │ API     │
 * ├────────────────────────────────────────────────────────────────────┤
 * │  GET       │  /posts             │  index   │  ✅       │ ✅      │
 * │  GET       │  /posts/create      │  create  │  ✅       │ ❌      │
 * │  POST      │  /posts             │  store   │  ✅       │ ✅      │
 * │  GET       │  /posts/{post}      │  show    │  ✅       │ ✅      │
 * │  GET       │  /posts/{post}/edit │  edit    │  ✅       │ ❌      │
 * │  PUT/PATCH │  /posts/{post}      │  update  │  ✅       │ ✅      │
 * │  DELETE    │  /posts/{post}      │  destroy │  ✅       │ ✅      │
 * └────────────────────────────────────────────────────────────────────┘
 *
 *   Partial Resource (sirf kuch routes):
 *   Route::resource('posts', PostController::class)->only(['index', 'show']);
 *   Route::resource('posts', PostController::class)->except(['destroy']);
 */


// =============================================================================
// S6: Middleware Priority kaise kaam karti hai?
// =============================================================================

/*
 * J: Laravel mein middleware ki ek FIXED priority order hai:
 *
 *   Kernel.php mein $middlewarePriority array:
 *   1. StartSession
 *   2. ShareErrorsFromSession
 *   3. AuthenticateSession
 *   4. SubstituteBindings
 *   5. Authorize
 *   6. (Aap ke custom middleware)
 *
 *   Chahe aap kisi bhi order mein lagao, Laravel PRIORITY order follow karega.
 *   Yeh ensure karta hai ke session pehle load ho auth se, etc.
 *
 *   Custom middleware ki priority set karne ke liye:
 *   Kernel.php mein $middlewarePriority array mein add karo.
 */
