<?php

// ============================================================================
// EVENT KYA HOTA HAI?
// ============================================================================

// Event ek "signal" hota hai ke "kuch ho chuka hai" (something happened).
// Event khud koi kaam nahi karta — ye sirf batata hai ke "ye cheez ho gayi"

// Real Life Example:
// Jab tum restaurant mein order place karte ho, waiter "Order Ready!" bolta hai.
// Waiter ye nahi sochta ke ab kya karna hai (SMS bhejna, kitchen ko batana, etc.)
// Waiter sirf announce karta hai. Baki log (Listeners) apna kaam khud karte hain.

// Order Placed   →   Event   →   "Order ho gaya hai" ka elaan (announcement)
// Send Email     →   Listener 1 → Email bhej dega
// Send SMS       →   Listener 2 → SMS bhej dega
// Update Stock   →   Listener 3 → Stock kam kar dega

// Ek Event ke peechay MULTIPLE Listeners ho sakte hain.
// Event ko ye pata bhi nahi hota ke iske baad kaun kaun se listeners chalenge.
// Ye loose-coupling hai — Event aur Listener ek dusre se independent hain.


// ============================================================================
// REAL LARAVEL PROJECT MEIN YE FILE KAHAN HOTI HAI?
// ============================================================================

// app/Events/OrderPlaced.php

// Banane ka artisan command:
// php artisan make:event OrderPlaced


// ============================================================================
// EVENT CLASS - REAL CODE EXAMPLE
// ============================================================================

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Event ke sath jo bhi data Listeners tak pohanchana hai,
    // wo yahan public property ke through pass hota hai.
    public function __construct(
        public Order $order
    ) {
        // Bas itna kaam — order ko store kar liya.
        // Yahan koi business logic NAHI likhni (email/sms etc.)
        // Wo sab kaam Listener mein hoga.
    }

    // Agar real-time broadcasting (Pusher / WebSocket) chahiye ho
    // to ye method use hota hai. Abhi simple example ke liye chhor diya.
    //
    // public function broadcastOn(): array
    // {
    //     return [
    //         new PrivateChannel('orders.' . $this->order->id),
    //     ];
    // }
}


// ============================================================================
// YAAD RAKHO
// ============================================================================

// - Event = "Kya hua" (Naam hamesha PAST TENSE mein: OrderPlaced, UserRegistered, PaymentFailed)
// - Event mein sirf DATA hota hai, koi LOGIC nahi
// - Ek Event multiple Listeners ko trigger kar sakta hai
