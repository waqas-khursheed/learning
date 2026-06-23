<?php

/**
 * ============================================================================
 *             DATABASE & MIGRATIONS — INTERVIEW Q&A
 *                  (6 Years Experience Level)
 * ============================================================================
 */


// =============================================================================
// S1: Migration kya hai? Seeders aur Factories se kya farq?
// =============================================================================

/*
 * J:
 *   Migration = Database ka VERSION CONTROL
 *     - Tables banayen, badlein, mitten — code mein (SQL nahi)
 *     - Team sab ko same database structure milta hai
 *     - Rollback kar sakte hain
 *
 *   Seeder = Test/default data daalna
 *     - Admin user banana, categories daalna, demo data
 *
 *   Factory = FAKE data banana (testing ke liye)
 *     - 1000 fake users banayen testing ke liye
 *     - Faker library se realistic data
 *
 *   FLOW:
 *   Migration (structure) → Seeder (default data) → Factory (test data)
 */


// =============================================================================
// S2: Production mein migration ka kya protocol hona chahiye?
// =============================================================================

/*
 * J: BOHOT AHEM — production mein ghalti se data kho sakta hai!
 *
 *   ✅ SAHI TAREEQA:
 *
 *   1. KABHI production par seedha migration mat chalao
 *      → Pehle staging par test karo
 *
 *   2. Destructive migrations se bacho:
 *      ❌ dropColumn, dropTable (pehle data migrate karo)
 *      ✅ Pehle naya column add karo → data copy karo → purana hata do
 *
 *   3. php artisan migrate --force (production mein)
 *      → --force is liye lagta hai ke production mein confirm nahi mangta
 *
 *   4. Down migration HAMESHA likho (rollback ke liye)
 *
 *   5. Large tables par migration SLOW hoti hai:
 *      → 10 million rows par ALTER TABLE minutes le sakta hai
 *      → Maintenance window mein karo
 *      → Ya online schema change tools use karo
 */


// =============================================================================
// S3: Database Transactions kya hain? Kab use karein?
// =============================================================================

/*
 * J: Transaction = Ya toh SAB queries kamyab hon, ya KOI nahi.
 *    "All or Nothing" — beech mein kuch fail ho toh sab wapas (rollback).
 */

// MISAAL: Paise transfer — dono queries honi chahiye ya koi nahi
DB::transaction(function () {
    $sender = Account::where('id', 1)->lockForUpdate()->first();
    $receiver = Account::where('id', 2)->lockForUpdate()->first();

    if ($sender->balance < 1000) {
        throw new InsufficientBalanceException();
    }

    $sender->decrement('balance', 1000);    // Paise kato
    $receiver->increment('balance', 1000);  // Paise daalo

    Transfer::create([
        'from' => 1,
        'to' => 2,
        'amount' => 1000,
    ]);
});
// Agar koi bhi line fail ho → sab wapas (rollback)!

// Manual transaction:
DB::beginTransaction();
try {
    // Queries...
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    throw $e;
}

/*
 * ⚠️ lockForUpdate() kya karta hai:
 *   - Row ko LOCK kar deta hai jab tak transaction khatam na ho
 *   - Doosra process isi row ko update nahi kar sakta (wait karega)
 *   - Race condition rokta hai
 *   - Maslan: 2 log ek sath ek hi product khareedein → stock ghalat na ho
 */


// =============================================================================
// S4: Query Builder vs Eloquent — kab kya use karein?
// =============================================================================

/*
 * J:
 *   Eloquent ORM:
 *   ✅ Relationships, events, accessors, mutators
 *   ✅ Readable code
 *   ❌ Heavy objects bante hain (memory zyada)
 *   ❌ Complex queries mushkil
 *
 *   Query Builder (DB::table):
 *   ✅ Tez (Eloquent se 2-3x)
 *   ✅ Complex queries aasan (joins, subqueries)
 *   ✅ Kam memory
 *   ❌ No relationships, no events
 *
 *   KAB KYA:
 *   - CRUD operations → Eloquent
 *   - Reports, analytics → Query Builder
 *   - Bulk operations (insert 10,000 rows) → Query Builder
 *   - Simple lookups → Eloquent
 *   - Complex joins → Query Builder
 */

// Eloquent (readable, magar 10,000 objects bante hain):
$users = User::with('orders')->where('active', true)->get();

// Query Builder (tez, kam memory):
$stats = DB::table('orders')
    ->join('users', 'users.id', '=', 'orders.user_id')
    ->select(
        'users.name',
        DB::raw('COUNT(orders.id) as total_orders'),
        DB::raw('SUM(orders.total) as total_revenue')
    )
    ->groupBy('users.id', 'users.name')
    ->orderByDesc('total_revenue')
    ->limit(10)
    ->get();

// Bulk Insert — Query Builder BOHOT tez:
DB::table('logs')->insert($thousandsOfRecords);
// Ya Eloquent upsert:
User::upsert($data, ['email'], ['name', 'updated_at']);


// =============================================================================
// S5: Polymorphic Relationships kab use karein? Example do.
// =============================================================================

/*
 * J: Jab MUKHTALIF models ko SAME table se relate karna ho.
 *
 *   Maslan: Comments → Posts par bhi, Videos par bhi, Photos par bhi
 *   Agar har ek ke liye alag table banayen → 3 tables!
 *   Polymorphic se → 1 table kafi hai!
 *
 *   comments table:
 *   id | body | commentable_type | commentable_id
 *   1  | Nice | App\Models\Post  | 5
 *   2  | Cool | App\Models\Video | 3
 */

// Many-to-Many Polymorphic (Tags system):
// taggables table: tag_id | taggable_id | taggable_type

class Tag extends Model
{
    public function posts(): MorphToMany
    {
        return $this->morphedByMany(Post::class, 'taggable');
    }

    public function videos(): MorphToMany
    {
        return $this->morphedByMany(Video::class, 'taggable');
    }
}

class Post extends Model
{
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}

// Istemal:
$post->tags()->attach([1, 2, 3]);   // Post ko tags lagao
$tag->posts;                         // Is tag wali sab posts
$tag->videos;                        // Is tag wali sab videos
