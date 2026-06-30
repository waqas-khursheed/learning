<?php

// ============================================================================
// EVENT KO FIRE (DISPATCH) KAISE KARTE HAIN?
// ============================================================================

// Jahan bhi tumhe batana ho "ye action ho chuka hai" — Controller, Job,
// Model, kahin se bhi — wahan bas event() helper ya Event::class::dispatch() call karo.


// ============================================================================
// CONTROLLER MEIN USAGE - REAL CODE EXAMPLE
// ============================================================================

namespace App\Http\Controllers;

use App\Events\OrderPlaced;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // Step 1: Order create karo (normal Laravel kaam)
        $order = Order::create([
            'user_id' => $request->user()->id,
            'total'   => $request->total,
        ]);

        // Step 2: Event fire karo — bas itna! Baki sab automatic hoga.
        // (Laravel khud EventServiceProvider check karega ke is Event
        //  par konse Listeners chalane hain, aur unko call kar dega)
        OrderPlaced::dispatch($order);

        // ----- Dispatch karne ke alternative tareeqe -----
        // event(new OrderPlaced($order));      // helper function se
        // Event::dispatch(new OrderPlaced($order)); // facade se

        return response()->json(['message' => 'Order placed successfully']);
    }
}


// ============================================================================
// PURA FLOW (DIAGRAM)
// ============================================================================

//   Controller
//       │
//       │  OrderPlaced::dispatch($order)
//       ▼
//   Event: OrderPlaced  ───────────────►  "Order ho gaya hai" ka elaan
//       │
//       │  Laravel EventServiceProvider check karta hai:
//       │  "OrderPlaced ke listeners kaun se hain?"
//       ▼
//   ┌─────────────────────────────┬─────────────────────────────┐
//   │ Listener 1:                 │ Listener 2:                 │
//   │ SendOrderConfirmationEmail  │ UpdateStockAfterOrder        │
//   │ (ShouldQueue → background)  │ (turant chal jata hai)       │
//   └─────────────────────────────┴─────────────────────────────┘
//       │
//       ▼
//   Controller ko response wapas — user ko wait nahi karna para!


// ============================================================================
// ARTISAN COMMANDS (REVISION KE LIYE)
// ============================================================================

// php artisan make:event OrderPlaced
// php artisan make:listener SendOrderConfirmationEmail --event=OrderPlaced
// php artisan event:list          → saare registered events/listeners dikhata hai
// php artisan queue:work          → queued listeners ko process karne ke liye
//                                    (zaroori hai agar Listener ShouldQueue use kare)


// ============================================================================
// EVENT/LISTENER KYUN USE KARTE HAIN? (WHY)
// ============================================================================

// 1. Separation of Concerns
//    Controller sirf "Order create karna" janta hai.
//    Email/SMS/Stock update ka logic Controller mein nahi, alag files mein hai.

// 2. Loose Coupling
//    Kal agar SMS bhejna band karna ho, bas us Listener ko EventServiceProvider
//    se hata do. Controller ka code touch tak nahi karna parega.

// 3. Multiple Actions, Ek Trigger
//    Ek hi "OrderPlaced" event par 5 alag kaam ho sakte hain
//    (email, SMS, stock update, invoice generate, analytics log)
//    bina Controller ko bhari-bharkam banaye.

// 4. Queue ke sath Performance
//    ShouldQueue lagao to time-consuming kaam (email/SMS/PDF) background
//    mein chale jate hain — user ko fast response milta hai.

// 5. Testability
//    Event::fake() use karke test mein check kar sakte ho ke event
//    sahi se dispatch hua ya nahi, bina real email/SMS bheje.


// ============================================================================
// QUICK REVISION TABLE
// ============================================================================

// | File                          | Kaam                                    |
// |--------------------------------|------------------------------------------|
// | OrderPlaced.php (Event)        | "Kya hua" ka elaan, sirf data carry karta |
// | SendOrderConfirmationEmail.php | "Ab kya karna hai" — actual kaam (handle) |
// | EventServiceProvider.php       | Event ko Listener(s) se jorta hai         |
// | UsageExample.php (Controller)  | Event fire/dispatch karta hai             |
