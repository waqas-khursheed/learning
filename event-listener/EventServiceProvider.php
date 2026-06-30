<?php

// ============================================================================
// EVENT KO LISTENER SE "CONNECT" KAISE KARTE HAIN?
// ============================================================================

// Event aur Listener khud se ek dusre ko nahi jante.
// Inko jorne (map/register karne) ka kaam EventServiceProvider karta hai.

// Real Life Example:
// Restaurant ka Manager (EventServiceProvider) ek list rakhta hai:
// "Jab bhi 'Order Ready' ka elaan ho, Kitchen Staff ko bula lena"
// Ye mapping Manager ke pass likhi hoti hai — Waiter ya Kitchen Staff ko khud pata nahi hota.


// ============================================================================
// REAL LARAVEL PROJECT MEIN YE FILE KAHAN HOTI HAI?
// ============================================================================

// Laravel 11/12 (new structure) mein alag se EventServiceProvider
// file nahi hoti by default — registration is file mein hoti hai:
//
//      app/Providers/AppServiceProvider.php   (boot() method ke andar)
//
// Laravel 10 aur usse purane projects mein ye file already maujood hoti hai:
//
//      app/Providers/EventServiceProvider.php


// ============================================================================
// OPTION 1 — MANUAL MAPPING (EventServiceProvider.php — Laravel 10 style)
// ============================================================================

namespace App\Providers;

use App\Events\OrderPlaced;
use App\Listeners\SendOrderConfirmationEmail;
use App\Listeners\UpdateStockAfterOrder;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    // Yahan Event => [Listeners] ka array banta hai.
    // Ek Event ke saamne ek se zyada Listeners likhe ja sakte hain (array mein).
    protected $listen = [
        OrderPlaced::class => [
            SendOrderConfirmationEmail::class,
            UpdateStockAfterOrder::class,   // Ek hi event, dusra kaam
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}


// ============================================================================
// OPTION 2 — LARAVEL 11/12 STYLE (AppServiceProvider.php ke andar)
// ============================================================================

// namespace App\Providers;
//
// use App\Events\OrderPlaced;
// use App\Listeners\SendOrderConfirmationEmail;
// use App\Listeners\UpdateStockAfterOrder;
// use Illuminate\Support\Facades\Event;
// use Illuminate\Support\ServiceProvider;
//
// class AppServiceProvider extends ServiceProvider
// {
//     public function boot(): void
//     {
//         Event::listen(OrderPlaced::class, SendOrderConfirmationEmail::class);
//         Event::listen(OrderPlaced::class, UpdateStockAfterOrder::class);
//     }
// }


// ============================================================================
// OPTION 3 — AUTO-DISCOVERY (Manual register karne ki zaroorat hi nahi)
// ============================================================================

// Agar Listener ke handle() method mein Event ka type-hint sahi diya ho
// (jaise hamari SendOrderConfirmationEmail::handle(OrderPlaced $event) mein hai),
// to Laravel khud automatically detect kar leta hai — koi mapping likhne
// ki zaroorat nahi parti. Ye sabse modern aur recommended tareeqa hai.


// ============================================================================
// YAAD RAKHO
// ============================================================================

// - EventServiceProvider = "Konsa Event, kis Listener ko bulaye ga" ki list
// - Laravel 11/12 mein ye AppServiceProvider ke boot() mein likha jata hai
// - Ya phir Auto-Discovery use karke kuch likhna hi nahi parta
// - Ek Event => Multiple Listeners possible (array mein sab likho)
