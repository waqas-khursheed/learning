<?php

/**
 * ============================================================================
 *          AUTHENTICATION & AUTHORIZATION — INTERVIEW Q&A
 *                    (6 Years Experience Level)
 * ============================================================================
 */


// =============================================================================
// S1: Authentication aur Authorization mein kya farq hai?
// =============================================================================

/*
 * J:
 *   Authentication (Shanakht) = "TUM KAUN HO?"
 *     - Login karna, identity verify karna
 *     - Username/password, OAuth, token
 *
 *   Authorization (Ijazat) = "TUMHEIN KYA KARNE KI IJAZAT HAI?"
 *     - Login ke BAAD — kya access hai, kya nahi
 *     - Roles, permissions, gates, policies
 *
 *   Misaal: Hotel
 *     Authentication = Reception par ID dikhana (kaun ho?)
 *     Authorization  = Room card (kis kamre mein ja sakte ho?)
 */


// =============================================================================
// S2: Laravel mein authentication ke kaunse packages/tools hain?
// =============================================================================

/*
 * J:
 *   1) Laravel Breeze       → Simple auth scaffolding (Blade/Inertia/API)
 *   2) Laravel Fortify      → Backend auth logic (bina UI ke)
 *   3) Laravel Jetstream    → Full-featured (teams, 2FA, API tokens)
 *   4) Laravel Sanctum      → SPA aur Mobile API auth (tokens)
 *   5) Laravel Passport      → Full OAuth2 server (third-party apps)
 *   6) Laravel Socialite     → Social login (Google, Facebook, GitHub)
 *
 *   KAB KYA USE KAREIN:
 *   - Simple web app → Breeze
 *   - SPA (React/Vue) + API → Sanctum
 *   - Mobile app API → Sanctum
 *   - Third-party apps ko API access dena → Passport
 *   - Social login → Socialite
 */


// =============================================================================
// S3: Sanctum vs Passport — kya farq hai? Kab kaunsa?
// =============================================================================

/*
 * J:
 * ┌──────────────────────────────────────────────────────────────────┐
 * │  Feature              │  Sanctum            │  Passport          │
 * ├──────────────────────────────────────────────────────────────────┤
 * │  Complexity            │  Simple              │  Complex           │
 * │  Token Type            │  Simple API tokens   │  Full OAuth2       │
 * │  SPA Authentication    │  ✅ Cookie-based     │  ❌                │
 * │  Mobile API            │  ✅ Token-based      │  ✅ Token-based    │
 * │  Third-party Auth      │  ❌                  │  ✅ OAuth2 flows   │
 * │  Token Scopes          │  ✅ (simple)         │  ✅ (advanced)     │
 * │  Token Expiration      │  ✅                  │  ✅                │
 * │  Use Case              │  First-party apps    │  Third-party apps  │
 * └──────────────────────────────────────────────────────────────────┘
 *
 *   TAVSIYA: 95% cases mein Sanctum kafi hai. Passport tab use karo
 *   jab OAuth2 server banana ho (jaise Google/Facebook banate hain).
 */


// =============================================================================
// S4: Gates aur Policies mein kya farq hai?
// =============================================================================

/*
 * J: Dono Authorization ke tareeqe hain:
 *
 *   Gate   = Simple check (closure-based) — chhoti cheezein
 *   Policy = Class-based (model ke sath) — bari cheezein
 */

// GATE — Simple authorization check:
// AppServiceProvider mein:
Gate::define('edit-settings', function (User $user) {
    return $user->is_admin;
});

Gate::define('update-post', function (User $user, Post $post) {
    return $user->id === $post->user_id;
});

// Istemal:
if (Gate::allows('edit-settings')) { /* ... */ }
if (Gate::denies('update-post', $post)) { abort(403); }


// POLICY — Model ke sath (organized):
// php artisan make:policy PostPolicy --model=Post

class PostPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Sab users sab posts dekh sakte hain
    }

    public function view(User $user, Post $post): bool
    {
        return true; // Published post koi bhi dekh sakta hai
    }

    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id; // Sirf apni post edit karo
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->is_admin;
    }

    public function forceDelete(User $user, Post $post): bool
    {
        return $user->is_admin; // Sirf admin permanently delete kar sakta hai
    }
}

// Controller mein istemal:
class PostController extends Controller
{
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post); // 403 agar ijazat nahi

        $post->update($request->validated());
    }

    // Ya middleware se:
    public function __construct()
    {
        $this->authorizeResource(Post::class, 'post');
        // Sab CRUD methods ke liye policy automatically lagegi
    }
}

// Blade mein:
/*
 *   @can('update', $post)
 *       <a href="{{ route('posts.edit', $post) }}">Edit</a>
 *   @endcan
 *
 *   @cannot('delete', $post)
 *       <p>Aap yeh post delete nahi kar sakte</p>
 *   @endcannot
 */


// =============================================================================
// S5: Role-based aur Permission-based access mein farq?
// =============================================================================

/*
 * J:
 *   Role-based:    User ka ROLE check karo (admin, editor, viewer)
 *   Permission-based: User ki PERMISSION check karo (can_edit_posts, can_delete_users)
 *
 *   BEST PRACTICE: Permission-based use karo (zyada flexible)
 *
 *   Popular Package: spatie/laravel-permission
 *
 *   composer require spatie/laravel-permission
 */

// Spatie package ke sath:
$user->assignRole('admin');
$user->givePermissionTo('edit posts');
$user->hasRole('admin');              // true/false
$user->can('edit posts');             // true/false
$user->hasPermissionTo('edit posts'); // true/false

// Middleware:
Route::group(['middleware' => ['role:admin']], function () {
    Route::get('/admin', AdminController::class);
});

Route::group(['middleware' => ['permission:publish articles']], function () {
    Route::post('/articles', [ArticleController::class, 'store']);
});

// Blade:
/*
 *   @role('admin')
 *       <a href="/admin">Admin Panel</a>
 *   @endrole
 *
 *   @hasanyrole('admin|editor')
 *       <a href="/posts/create">Naya Post</a>
 *   @endhasanyrole
 */


// =============================================================================
// S6: Multi-Authentication kaise implement karein?
// =============================================================================

/*
 * J: Multiple guards define karo (maslan: users + admins):
 *
 *   config/auth.php:
 *
 *   'guards' => [
 *       'web' => [
 *           'driver' => 'session',
 *           'provider' => 'users',
 *       ],
 *       'admin' => [
 *           'driver' => 'session',
 *           'provider' => 'admins',
 *       ],
 *   ],
 *
 *   'providers' => [
 *       'users' => [
 *           'driver' => 'eloquent',
 *           'model' => App\Models\User::class,
 *       ],
 *       'admins' => [
 *           'driver' => 'eloquent',
 *           'model' => App\Models\Admin::class,
 *       ],
 *   ],
 *
 *   Routes mein:
 *   Route::middleware('auth:admin')->group(function () {
 *       Route::get('/admin/dashboard', AdminDashboardController::class);
 *   });
 *
 *   Controller mein:
 *   Auth::guard('admin')->user();  // Admin user
 *   Auth::guard('web')->user();    // Normal user
 */
