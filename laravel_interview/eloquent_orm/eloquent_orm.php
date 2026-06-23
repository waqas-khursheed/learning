<?php

/**
 * ============================================================================
 *                ELOQUENT ORM — INTERVIEW Q&A
 *              (6 Years Experience Level)
 * ============================================================================
 */


// =============================================================================
// S1: N+1 Query Problem kya hai? Kaise fix karein?
// =============================================================================

/*
 * J: Yeh SABSE ZYADA poochha jane wala sawaal hai.
 *
 *    N+1 Problem matlab:
 *    1 query sab posts ke liye + N queries har post ke user ke liye
 */

// ❌ N+1 PROBLEM (100 posts = 101 queries!):
$posts = Post::all();                     // 1 query: SELECT * FROM posts
foreach ($posts as $post) {
    echo $post->user->name;              // 100 queries: SELECT * FROM users WHERE id = ?
}
// Total: 101 queries! 💀

// ✅ EAGER LOADING se fix (Sirf 2 queries):
$posts = Post::with('user')->get();       // Query 1: SELECT * FROM posts
                                           // Query 2: SELECT * FROM users WHERE id IN (1,2,3...)
foreach ($posts as $post) {
    echo $post->user->name;              // Koi nayi query NAHI — pehle se loaded hai
}

// Nested Eager Loading:
$posts = Post::with(['user', 'comments.user', 'tags'])->get();

// Conditional Eager Loading:
$posts = Post::with(['comments' => function ($query) {
    $query->where('approved', true)->orderBy('created_at', 'desc');
}])->get();

// Lazy Eager Loading (baad mein load karo):
$posts = Post::all();
$posts->load('user', 'tags');

/*
 * N+1 DETECT KARNE KA TAREEQA:
 *
 *   1. Laravel Debugbar install karo:
 *      composer require barryvdh/laravel-debugbar --dev
 *
 *   2. preventLazyLoading() use karo (Laravel 9+):
 *      AppServiceProvider boot() mein:
 *      Model::preventLazyLoading(!app()->isProduction());
 *      → Development mein lazy loading par exception phenkega
 */


// =============================================================================
// S2: Eloquent Relationships ki qismain batao with examples.
// =============================================================================

/*
 * J: Laravel mein 7 tarah ki relationships hain:
 */

// 1) hasOne — Ek user ka ek phone
class User extends Model
{
    public function phone(): HasOne
    {
        return $this->hasOne(Phone::class);
        // users.id = phones.user_id
    }
}

// 2) belongsTo — Phone ka ek user (ulta)
class Phone extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

// 3) hasMany — Ek user ke kai posts
class User extends Model
{
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}

// 4) belongsToMany — Many-to-Many (User ke kai roles, role ke kai users)
class User extends Model
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)
            ->withPivot('assigned_at')     // Pivot table ke extra columns
            ->withTimestamps();
    }
}
// Pivot table: role_user (user_id, role_id)

// 5) hasOneThrough — Ek supplier ka ek user account through products
class Supplier extends Model
{
    public function userAccount(): HasOneThrough
    {
        return $this->hasOneThrough(Account::class, Product::class);
    }
}

// 6) hasManyThrough — Country ke through users ke posts
class Country extends Model
{
    public function posts(): HasManyThrough
    {
        return $this->hasManyThrough(Post::class, User::class);
        // country → users → posts
    }
}

// 7) Polymorphic — Ek comment posts AUR videos dono par
class Comment extends Model
{
    public function commentable(): MorphTo
    {
        return $this->morphTo(); // Post ya Video dono ho sakti hai
    }
}

class Post extends Model
{
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}

class Video extends Model
{
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
// comments table: commentable_id, commentable_type (App\Models\Post ya App\Models\Video)


// =============================================================================
// S3: Query Scopes kya hain? Local aur Global mein farq?
// =============================================================================

/*
 * J: Scopes reusable query constraints hain — ek jagah likho, har jagah use karo.
 */

// LOCAL SCOPE — zaroorat par lagao:
class Post extends Model
{
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopePopular(Builder $query, int $minViews = 1000): Builder
    {
        return $query->where('views', '>=', $minViews);
    }

    public function scopeByAuthor(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}

// Istemal:
$posts = Post::published()->popular(500)->byAuthor(1)->get();
// SELECT * FROM posts WHERE status='published' AND views >= 500 AND user_id = 1


// GLOBAL SCOPE — HAMESHA lagta hai (har query par):
class ActiveScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('is_active', true);
    }
}

class Product extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope(new ActiveScope());
    }
}

// Ab HAR product query mein WHERE is_active = true lagega
Product::all();            // SELECT * FROM products WHERE is_active = true
Product::withoutGlobalScope(ActiveScope::class)->get();  // Scope hata ke query


// =============================================================================
// S4: Eloquent Accessors aur Mutators kya hain? (Laravel 9+ style)
// =============================================================================

class Employee extends Model
{
    // ACCESSOR — database se nikal ke format karo
    protected function firstName(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
        );
    }

    // MUTATOR — database mein daalte waqt format karo
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strtolower($value),
        );
    }

    // DONO — get aur set
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->first_name} {$this->last_name}",
            set: fn (string $value) => [
                'first_name' => explode(' ', $value)[0],
                'last_name'  => explode(' ', $value)[1] ?? '',
            ],
        );
    }

    // CASTS — automatic type conversion
    protected function casts(): array
    {
        return [
            'salary'     => 'decimal:2',
            'is_active'  => 'boolean',
            'hired_at'   => 'datetime',
            'metadata'   => 'array',       // JSON column → PHP array
            'address'    => AddressCast::class, // Custom cast
        ];
    }
}


// =============================================================================
// S5: Soft Deletes kya hai? Kab use karein?
// =============================================================================

/*
 * J: Soft Delete = database se asal mein DELETE nahi hota, sirf "deleted" MARK hota hai.
 *    deleted_at column mein timestamp aa jata hai.
 */

class Order extends Model
{
    use SoftDeletes; // Bas itna karo

    // Ab Order::find(1)->delete() se deleted_at fill hoga
    // Magar row database mein rahegi

    // Soft deleted samet sab nikalo:
    // Order::withTrashed()->get()

    // SIRF soft deleted:
    // Order::onlyTrashed()->get()

    // Restore karo:
    // $order->restore()

    // PERMANENTLY delete:
    // $order->forceDelete()
}

/*
 * KAB USE KAREIN:
 *   ✅ Orders, invoices (legal records — delete nahi kar sakte)
 *   ✅ Users (deactivate karo, data rakhho)
 *   ✅ Content (ghalti se delete ho toh restore kar sako)
 *
 *   ❌ Logs (bohot zyada data — permanently delete karo)
 *   ❌ Temporary data (sessions, OTPs)
 */


// =============================================================================
// S6: Eloquent Events kya hain? Observer kya hai?
// =============================================================================

/*
 * J: Eloquent Events model ke lifecycle mein trigger hote hain:
 *
 *   creating → created
 *   updating → updated
 *   saving   → saved     (create + update dono par)
 *   deleting → deleted
 *   restoring → restored (soft delete restore)
 *
 *   "-ing" wale PEHLE chalte hain (roka ja sakta hai)
 *   "-ed" wale BAAD mein chalte hain
 */

// OBSERVER — sab events ek class mein:
class OrderObserver
{
    public function creating(Order $order): void
    {
        $order->order_number = 'ORD-' . uniqid();
    }

    public function created(Order $order): void
    {
        // Email bhejo, notification bhejo
        SendOrderConfirmation::dispatch($order);
    }

    public function updating(Order $order): void
    {
        if ($order->isDirty('status')) {
            $order->status_changed_at = now();
        }
    }

    public function deleting(Order $order): bool
    {
        if ($order->status === 'processing') {
            return false; // DELETE rok do — processing order delete nahi hona chahiye
        }
        return true;
    }
}

// Register karo AppServiceProvider mein:
// Order::observe(OrderObserver::class);


// =============================================================================
// S7: Chunking, Cursor, aur Lazy Collections mein farq?
// =============================================================================

/*
 * J: Lakhon records process karne ke liye — memory bachao:
 */

// ❌ GHALAT — 1 million records memory mein load:
$users = User::all(); // 💀 Memory bhar jaye gi

// ✅ CHUNK — 1000 ke groups mein:
User::chunk(1000, function ($users) {
    foreach ($users as $user) {
        $user->update(['processed' => true]);
    }
});
// LIMIT 1000 OFFSET 0, LIMIT 1000 OFFSET 1000, ...
// ⚠️ Update karte waqt masla ho sakta hai (offset badal jata hai)

// ✅ CHUNK BY ID — ID ke hisab se (update ke liye behtar):
User::chunkById(1000, function ($users) {
    foreach ($users as $user) {
        $user->update(['processed' => true]);
    }
});
// WHERE id > 0 LIMIT 1000, WHERE id > 1000 LIMIT 1000, ...

// ✅ CURSOR — ek ek karke memory mein (sab se kam memory):
foreach (User::cursor() as $user) {
    // Ek waqt mein sirf 1 user memory mein
    $user->update(['processed' => true]);
}

// ✅ LAZY — Cursor jaisa magar collection methods available:
User::lazy(1000)->each(function ($user) {
    $user->update(['processed' => true]);
});

/*
 * KAUNSA KAB USE KAREIN:
 *   chunk()     → Jab groups mein process karna ho
 *   chunkById() → Jab update karte waqt chunk karna ho
 *   cursor()    → Jab sab se kam memory chahiye
 *   lazy()      → Cursor + collection methods chahiye
 */


// =============================================================================
// S8: Eloquent mein Mass Assignment aur $fillable/$guarded kya hai?
// =============================================================================

/*
 * J: Mass Assignment = ek sath kai fields fill karna.
 *    $fillable aur $guarded SECURITY ke liye hain — SQL injection nahi, magar
 *    koi user ghalat fields (maslan: is_admin) inject na kar sake.
 */

class User extends Model
{
    // OPTION 1: $fillable — SIRF ye fields mass assign ho sakti hain
    protected $fillable = ['name', 'email', 'password'];
    // User::create(['name' => 'Ali', 'is_admin' => true])
    // is_admin IGNORE hoga ✅ mehfooz

    // OPTION 2: $guarded — Ye fields mass assign NAHI ho sakti
    // protected $guarded = ['id', 'is_admin'];
    // Baqi sab allowed hain

    // ⚠️ protected $guarded = [];  → Kuch bhi guarded nahi — KHATARNAAK!
}

/*
 * SENIOR LEVEL JAWAB:
 *   - $fillable use karo (explicit — pata hai kya allowed hai)
 *   - $guarded se bachein (implicit — bhool sakte ho kuch add karna)
 *   - Form Request validation PEHLE karo, phir $request->validated() use karo
 */

class StoreUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ];
    }
}

// Controller mein:
User::create($request->validated());  // Sirf validated fields jayein gi ✅
