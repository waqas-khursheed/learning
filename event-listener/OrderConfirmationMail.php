<?php

// ============================================================================
// MAILABLE CLASS — SendOrderConfirmationEmail.php is class ko use karta hai
// ============================================================================

// Ye actual EMAIL ka "template + data" hota hai. Listener sirf itna kehta
// hai "ye mail bhejo", lekin email ka content/design yahan define hota hai.


// ============================================================================
// REAL LARAVEL PROJECT MEIN YE FILE KAHAN HOTI HAI?
// ============================================================================

// app/Mail/OrderConfirmationMail.php

// Banane ka artisan command:
// php artisan make:mail OrderConfirmationMail


// ============================================================================
// MAILABLE CLASS - REAL CODE EXAMPLE
// ============================================================================

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    // Email ka subject aur sender
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Order #{$this->order->id} Confirmed",
        );
    }

    // Email ka actual content — kaunsi Blade view use hogi
    public function content(): Content
    {
        return new Content(
            view: 'emails.order-confirmation',
            with: [
                'orderId' => $this->order->id,
                'total'   => $this->order->total,
            ],
        );
    }
}


// ============================================================================
// BLADE VIEW (Email ka HTML) — resources/views/emails/order-confirmation.blade.php
// ============================================================================

// <h1>Shukriya! Aapka order confirm ho gaya hai</h1>
// <p>Order #{{ $orderId }}</p>
// <p>Total: PKR {{ $total }}</p>


// ============================================================================
// YAAD RAKHO — PURI CHAIN AB COMPLETE HAI
// ============================================================================

// Order.php (booted())
//      │  Order::create() → OrderPlaced::dispatch($order)
//      ▼
// OrderPlaced.php (Event)
//      │  Data carry karta hai ($order)
//      ▼
// EventServiceProvider.php
//      │  Batata hai konse Listeners chalane hain
//      ▼
// SendOrderConfirmationEmail.php (Listener)
//      │  Mail::to(...)->send(new OrderConfirmationMail($order))
//      ▼
// OrderConfirmationMail.php (Mailable) — YE FILE
//      │  Email ka subject + Blade view decide karta hai
//      ▼
// Actual Email Customer ko chala jata hai
