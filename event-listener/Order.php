<?php

// ============================================================================
// "JAHAN SY EVENT HOWA" — YE WAHI ASAL SHURUAT HAI
// ============================================================================

// Yahan tak humne dekha: Event class, Listener class, EventServiceProvider.
// Lekin EVENT ASAL MEIN HOTA KAHAN HAI? — Wo is Model ke andar hota hai,
// jab Order successfully DATABASE mein save ho jata hai.

// Real Life Flow (chote se bade tak):
// 1. User "Place Order" button dabata hai (Frontend)
// 2. Request Controller tak pohanchti hai (routes/web.php → OrderController)
// 3. Controller is Order::create() ko call karta hai (YE FILE)
// 4. Order DB mein save ho jata hai
// 5. Yahi par OrderPlaced::dispatch($order) call hota hai — EVENT YAHAN "HOTA" HAI
// 6. Laravel automatically Listeners ko chala deta hai


// ============================================================================
// REAL LARAVEL PROJECT MEIN YE FILE KAHAN HOTI HAI?
// ============================================================================

// app/Models/Order.php

// Banane ka artisan command:
// php artisan make:model Order -m   (-m se migration bhi sath ban jati hai)


// ============================================================================
// MIGRATION — DATABASE TABLE STRUCTURE (asal data yahan store hota hai)
// ============================================================================

// database/migrations/2024_01_01_000000_create_orders_table.php
//
// public function up(): void
// {
//     Schema::create('orders', function (Blueprint $table) {
//         $table->id();
//         $table->foreignId('user_id')->constrained();
//         $table->decimal('total', 10, 2);
//         $table->string('status')->default('pending');
//         $table->timestamps();
//     });
// }


// ============================================================================
// MODEL CLASS — REAL CODE EXAMPLE
// ============================================================================

namespace App\Models;

use App\Events\OrderPlaced;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    // Kaunsi fields mass-assignment (Order::create([...])) ke through
    // set karne ki ijazat hain — security ke liye zaroori hai.
    protected $fillable = ['user_id', 'total', 'status'];

    // ----- OPTION A: Model Events (Laravel ka built-in hook system) -----
    //
    // Laravel khud Eloquent Models ke liye built-in events deta hai:
    // creating, created, updating, updated, saving, saved, deleting, deleted
    //
    // Inhe "boot()" method mein attach kiya ja sakta hai — Order
    // create hote hi YAHIN SE custom Event fire kar sakte hain:
    protected static function booted(): void
    {
        static::created(function (Order $order) {
            // Order DB mein save hone ke FAUREN BAAD ye chalega
            OrderPlaced::dispatch($order);
        });
    }

    // ----- Relations -----
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}


// ============================================================================
// OPTION A vs OPTION B — EVENT FIRE KARNE KE 2 TAREEQE
// ============================================================================

// OPTION A (upar wala): Model ke andar booted() mein automatic —
// FAYDA: Jahan se bhi Order::create() ho (Controller, Job, Seeder,
//        Tinker), event HAMESHA fire hoga — bhoolne ka chance nahi.
// NUKSAN: "Magic" lagta hai — naye developer ko pata nahi chalta
//         ke Order create karne se Email/SMS bhi chala jata hai.

// OPTION B (UsageExample.php mein dikhaya gaya): Controller mein
// MANUALLY OrderPlaced::dispatch($order) likhna —
// FAYDA: EXPLICIT hai — code padh kar pata chal jata hai kya ho raha hai.
// NUKSAN: Agar kahin aur se bhi Order create ho (Seeder, Job) aur
//         wahan dispatch() likhna bhool jao, to event fire NAHI hoga.

// SENIOR DECISION: Agar event "HAMESHA" har Order create par chalna
// chahiye (jaise stock update) → Option A (Model event) use karo.
// Agar event sirf EK SPECIFIC FLOW (jaise sirf checkout page se) par
// chalna chahiye → Option B (Controller mein manual dispatch) use karo.
