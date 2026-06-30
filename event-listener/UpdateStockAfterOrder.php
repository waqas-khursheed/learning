<?php

// ============================================================================
// DOOSRA LISTENER — Same Event, ALAG kaam (EventServiceProvider.php mein
// is listener ka zikar tha, ab actual file yahan hai)
// ============================================================================

// Ye dikhata hai ke EK Event multiple, COMPLETELY UNRELATED Listeners ko
// trigger kar sakta hai. SendOrderConfirmationEmail email bhejta hai,
// ye Listener STOCK update karta hai — dono ek dusre se anjaan hain.


// ============================================================================
// REAL LARAVEL PROJECT MEIN YE FILE KAHAN HOTI HAI?
// ============================================================================

// app/Listeners/UpdateStockAfterOrder.php

// Banane ka artisan command:
// php artisan make:listener UpdateStockAfterOrder --event=OrderPlaced


// ============================================================================
// LISTENER CLASS - REAL CODE EXAMPLE
// ============================================================================

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateStockAfterOrder implements ShouldQueue
{
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;

        // DB Transaction use kiya — taake agar beech mein kuch fail ho
        // (jaise stock negative ho jaye), to PURA operation rollback ho jaye.
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);

                if ($product->stock < $item->quantity) {
                    // Stock kam hai — log karke aage badh jao
                    // (poora order fail mat karo, sirf is listener ka kaam ruke)
                    Log::warning("Stock kam hai product #{$product->id} ke liye");
                    return;
                }

                $product->decrement('stock', $item->quantity);
            }
        });
    }
}


// ============================================================================
// IMPORTANT CONCEPT — AGAR EK LISTENER FAIL HO TO BAAKI LISTENERS KA KYA HOGA?
// ============================================================================

// Agar SendOrderConfirmationEmail fail ho jaye (jaise mail server down ho),
// to UpdateStockAfterOrder par koi asar NAHI parega — Laravel har Listener
// ko INDEPENDENTLY chalata hai (especially jab dono ShouldQueue use kar rahe hon,
// to dono ALAG ALAG queue jobs ki tarah chalte hain).

// Ye Event/Listener pattern ka EK BARA FAYDA hai:
// "Ek kaam fail hone se baaki kaam ruk nahi jate" (loose coupling ka result)
