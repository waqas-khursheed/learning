<?php

// ============================================================================
// LISTENER KYA HOTA HAI?
// ============================================================================

// Listener wo class hai jo Event "sunti" hai aur Event hone ke baad
// ACTUAL KAAM karti hai (email bhejna, SMS bhejna, DB update karna, etc.)

// Real Life Example (continue from OrderPlaced.php):
// Waiter ne "Order Ready!" bola (Event) → ab Kitchen Staff (Listener) ye kaam karega:
// - Plate tayyar karna
// - Customer ki table par bhejna

// Listener hamesha Event ko "handle()" method ke through receive karta hai.


// ============================================================================
// REAL LARAVEL PROJECT MEIN YE FILE KAHAN HOTI HAI?
// ============================================================================

// app/Listeners/SendOrderConfirmationEmail.php

// Banane ka artisan command:
// php artisan make:listener SendOrderConfirmationEmail --event=OrderPlaced


// ============================================================================
// LISTENER CLASS - REAL CODE EXAMPLE
// ============================================================================

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Mail\OrderConfirmationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

// ShouldQueue lagane se ye Listener QUEUE mein chala jayega,
// matlab email bhejne ka kaam BACKGROUND mein hoga,
// user ko wait nahi karna paray ga (response foran milega).
class SendOrderConfirmationEmail implements ShouldQueue
{
    // Kitni baar retry karna hai agar fail ho jaye
    public int $tries = 3;

    // Konsi queue connection/name use karni hai (optional)
    public string $queue = 'emails';

    public function __construct()
    {
        // Yahan koi dependency inject ki ja sakti hai (constructor injection)
        // Laravel Service Container automatically resolve kar dega.
    }

    // Jab bhi OrderPlaced event fire hoga,
    // Laravel automatically is method ko call karega
    // aur Event object (sara data ke sath) is mein pass kar dega.
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;

        // Asal kaam yahan hota hai:
        Mail::to($order->user->email)
            ->send(new OrderConfirmationMail($order));
    }

    // Agar job fail ho jaye (saare retries ke baad bhi)
    // to ye method call hota hai — logging / alert ke liye useful.
    public function failed(OrderPlaced $event, \Throwable $exception): void
    {
        // Example: Log::error('Order email fail ho gaya', [...]);
    }
}


// ============================================================================
// YAAD RAKHO
// ============================================================================

// - Listener = "Ab kya karna hai" (Naam hamesha ACTION mein: SendOrderConfirmationEmail)
// - handle() method automatically Laravel call karta hai
// - ShouldQueue lagane se kaam background mein chala jata hai (fast response)
// - Ek Event ke liye MULTIPLE Listeners ban sakte hain (har ek alag file)
