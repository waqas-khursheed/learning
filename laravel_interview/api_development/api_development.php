<?php

/**
 * ============================================================================
 *              API DEVELOPMENT — INTERVIEW Q&A
 *                (6 Years Experience Level)
 * ============================================================================
 */


// =============================================================================
// S1: RESTful API kya hai? Laravel mein kaise banayen?
// =============================================================================

/*
 * J: REST = Representational State Transfer
 *
 *   REST ke usool:
 *   - Stateless (har request mukammal ho apne aap)
 *   - Resource-based URLs (/users, /posts)
 *   - HTTP methods use karo (GET, POST, PUT, DELETE)
 *   - JSON response
 *
 * ┌──────────────────────────────────────────────────────────────┐
 * │  HTTP Method │  URL            │  Kaam          │  Status    │
 * ├──────────────────────────────────────────────────────────────┤
 * │  GET         │  /api/posts     │  Sab posts      │  200      │
 * │  GET         │  /api/posts/1   │  Ek post         │  200      │
 * │  POST        │  /api/posts     │  Nayi post banayen│  201     │
 * │  PUT/PATCH   │  /api/posts/1   │  Post update     │  200      │
 * │  DELETE      │  /api/posts/1   │  Post delete     │  204      │
 * └──────────────────────────────────────────────────────────────┘
 */


// =============================================================================
// S2: API Resource kya hai? Kab use karein?
// =============================================================================

/*
 * J: API Resource data ko JSON mein TRANSFORM karta hai.
 *    Model ka sara data blindly mat bhejo — sirf zaruri fields.
 */

// php artisan make:resource UserResource
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'avatar_url' => $this->avatar ? Storage::url($this->avatar) : null,
            'joined'     => $this->created_at->diffForHumans(),
            'posts_count' => $this->whenCounted('posts'),
            'posts'      => PostResource::collection($this->whenLoaded('posts')),
            'role'       => $this->when($request->user()?->is_admin, $this->role),
        ];
    }
}

// Collection Resource:
class UserCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->total(),
                'per_page' => $this->perPage(),
            ],
        ];
    }
}

// Controller mein:
class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('posts')->paginate(20);
        return UserResource::collection($users);
    }

    public function show(User $user)
    {
        $user->load('posts', 'profile');
        return new UserResource($user);
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());
        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }
}


// =============================================================================
// S3: API Versioning kaise karein?
// =============================================================================

/*
 * J: 3 tareeqe hain:
 *
 *   1) URL Versioning (TAVSIYA): /api/v1/users, /api/v2/users
 *   2) Header Versioning: Accept: application/vnd.myapp.v1+json
 *   3) Query Parameter: /api/users?version=1
 */

// URL Versioning setup:
// routes/api.php:
Route::prefix('v1')->group(function () {
    Route::apiResource('users', App\Http\Controllers\Api\V1\UserController::class);
});

Route::prefix('v2')->group(function () {
    Route::apiResource('users', App\Http\Controllers\Api\V2\UserController::class);
});

// Folder structure:
// app/Http/Controllers/Api/V1/UserController.php
// app/Http/Controllers/Api/V2/UserController.php


// =============================================================================
// S4: API mein proper error handling kaise karein?
// =============================================================================

// app/Exceptions/Handler.php ya bootstrap/app.php:
class ApiExceptionHandler
{
    // Consistent error response format:
    public static function render(Throwable $e): JsonResponse
    {
        if ($e instanceof ValidationException) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        }

        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'message' => 'Resource nahi mila',
            ], 404);
        }

        if ($e instanceof AuthenticationException) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        if ($e instanceof AuthorizationException) {
            return response()->json([
                'message' => 'Aap ko ijazat nahi hai',
            ], 403);
        }

        // Production mein internal errors ka detail mat do:
        return response()->json([
            'message' => app()->isProduction()
                ? 'Server error ho gaya'
                : $e->getMessage(),
        ], 500);
    }
}


// =============================================================================
// S5: Sanctum se API Authentication kaise karein?
// =============================================================================

// Login aur token generate karna:
class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email ya password ghalat hai'
            ], 401);
        }

        $token = $user->createToken(
            'api-token',
            ['*'],                      // Abilities/scopes
            now()->addDays(30)          // Expiry
        )->plainTextToken;

        return response()->json([
            'user'  => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}

// Protected routes:
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => new UserResource($request->user()));
    Route::apiResource('posts', PostController::class);
});

// Token ke sath request bhejo:
// Header: Authorization: Bearer YOUR_TOKEN_HERE


// =============================================================================
// S6: API Pagination kaise karein? Best practice kya hai?
// =============================================================================

// Cursor Pagination (TAVSIYA — large datasets ke liye):
$users = User::orderBy('id')->cursorPaginate(20);
// Response mein next_cursor milega — offset-based se BOHOT tez

// Offset Pagination (simple, chhoti datasets):
$users = User::paginate(20);

// Simple Pagination (total count nahi — tez):
$users = User::simplePaginate(20);

/*
 * ⚠️ SENIOR LEVEL JAWAB:
 *   - Large datasets ke liye: cursorPaginate (O(1) performance)
 *   - Regular pagination ke liye: paginate (total count milta hai)
 *   - Infinite scroll ke liye: simplePaginate (next/prev sirf)
 *
 *   paginate() par COUNT(*) query chalti hai — 10 million rows par dheema!
 *   cursorPaginate() mein COUNT nahi — hamesha tez.
 */
