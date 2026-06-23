<?php

/**
 * ============================================================================
 *           QUEUES, JOBS & EVENTS — INTERVIEW Q&A
 *                 (6 Years Experience Level)
 * ============================================================================
 */


// =============================================================================
// S1: Queue kya hai? Kab use karein?
// =============================================================================

/*
 * J: Queue = Kaam ki line. Time-consuming tasks ko background mein bhejo
 *    taake user ko intezar na karna pare.
 *
 *   ❌ Bina Queue: User "Order Place" dabaata hai → 10 second hang →
 *      Email, Invoice PDF, SMS sab synchronous → "Kya ho raha hai?!" 😤
 *
 *   ✅ Queue ke sath: User "Order Place" dabaata hai → Foran response →
 *      Background mein: Email, Invoice, SMS → User khush! 😊
 *
 *   KAB USE KAREIN:
 *   - Email/SMS bhejna
 *   - PDF generate karna
 *   - Image resize/processing
 *   - External API calls
 *   - Data import/export
 *   - Notification bhejna
 *   - Report generate karna
 *   - Koi bhi kaam jo 1-2 second se zyada le
 */


// =============================================================================
// S2: Job banana aur dispatch karna dikhao.
// =============================================================================

// php artisan make:job ProcessPayment

class ProcessPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;          // 3 baar try karo
    public int $backoff = 60;       // Retries ke darmiyan 60 seconds
    public int $timeout = 120;      // 2 minute se zyada na chale
    public int $maxExceptions = 2;  // 2 exceptions ke baad band

    public function __construct(
        private Order $order
    ) {}

    public function handle(PaymentGateway $gateway): void
    {
        $result = $gateway->charge($this->order->total);

        $this->order->update([
            'payment_status' => $result->successful ? 'paid' : 'failed',
            'transaction_id' => $result->transaction_id,
        ]);
    }

    // Job fail hone par kya karo:
    public function failed(Throwable $exception): void
    {
        Log::error('Payment failed for order: ' . $this->order->id, [
            'error' => $exception->getMessage(),
        ]);

        $this->order->update(['payment_status' => 'failed']);
        Notification::send($this->order->user, new PaymentFailedNotification($this->order));
    }

    // Retry kab karna hai (exponential backoff):
    public function backoff(): array
    {
        return [30, 60, 120]; // 30s, 60s, 120s wait karo retries mein
    }

    // Unique job (same order ke liye dobara queue mein na jaye):
    public function uniqueId(): string
    {
        return 'payment-' . $this->order->id;
    }
}

// Dispatch karein:
ProcessPayment::dispatch($order);                         // Default queue
ProcessPayment::dispatch($order)->onQueue('payments');    // Specific queue
ProcessPayment::dispatch($order)->delay(now()->addMinutes(5)); // 5 min baad


// =============================================================================
// S3: Events aur Listeners kya hain? Observer se kya farq?
// =============================================================================

/*
 * J:
 *   Events   = "Kuch hua!" (signal)
 *   Listeners = "Kuch hua? Toh yeh karo!" (reaction)
 *
 *   Observer = Model-specific events (created, updated, deleted)
 *   Events   = Application-level events (order placed, payment received)
 */

// EVENT:
class OrderPlaced
{
    public function __construct(
        public readonly Order $order,
        public readonly User $user
    ) {}
}

// LISTENERS (kai listeners ek event par):
class SendOrderConfirmationEmail implements ShouldQueue
{
    public function handle(OrderPlaced $event): void
    {
        Mail::to($event->user)->send(new OrderConfirmationMail($event->order));
    }
}

class UpdateInventory implements ShouldQueue
{
    public function handle(OrderPlaced $event): void
    {
        foreach ($event->order->items as $item) {
            $item->product->decrement('stock', $item->quantity);
        }
    }
}

class NotifyAdminAboutOrder implements ShouldQueue
{
    public function handle(OrderPlaced $event): void
    {
        Notification::send(User::admins()->get(), new NewOrderNotification($event->order));
    }
}

// EventServiceProvider mein register karo:
/*
 *   protected $listen = [
 *       OrderPlaced::class => [
 *           SendOrderConfirmationEmail::class,
 *           UpdateInventory::class,
 *           NotifyAdminAboutOrder::class,
 *       ],
 *   ];
 */

// Fire karo:
OrderPlaced::dispatch($order, $user);
// Ya: event(new OrderPlaced($order, $user));


// =============================================================================
// S4: Job Batching kya hai? Real example do.
// =============================================================================

/*
 * J: Kai jobs ko ek group mein chalao aur track karo.
 */

// 10,000 users ko email bhejna:
$users = User::all();

$jobs = $users->map(function ($user) {
    return new SendMarketingEmail($user);
});

$batch = Bus::batch($jobs->toArray())
    ->then(function (Batch $batch) {
        Log::info("Sab emails bhej diye! Batch: {$batch->id}");
    })
    ->catch(function (Batch $batch, Throwable $e) {
        Log::error("Batch mein error: {$e->getMessage()}");
    })
    ->finally(function (Batch $batch) {
        Log::info("Batch khatam. Processed: {$batch->processedJobs()}, Failed: {$batch->failedJobs}");
    })
    ->allowFailures()  // Kuch fail hon toh bhi baqi chalen
    ->onQueue('emails')
    ->dispatch();

// Progress track karo:
$batch = Bus::findBatch($batchId);
$batch->progress();      // 75 (percent)
$batch->processedJobs(); // 7500
$batch->failedJobs;      // 12
$batch->finished();      // true/false


// =============================================================================
// S5: Queue workers production mein kaise manage karein?
// =============================================================================

/*
 * J: Supervisor use karo — worker band ho toh khud restart kare.
 *
 *   Commands:
 *   php artisan queue:work redis --queue=high,default,low --tries=3
 *   php artisan queue:restart     → Sab workers gracefully restart
 *   php artisan queue:retry all   → Failed jobs dubara try karo
 *   php artisan queue:failed      → Failed jobs dekho
 *   php artisan queue:flush       → Sab failed jobs delete karo
 *
 *   ⚠️ php artisan queue:work vs queue:listen:
 *   work   → Memory mein rehta hai, tez (TAVSIYA)
 *   listen → Har job ke liye framework dubara boot (dheema)
 *
 *   ⚠️ Deploy ke baad HAMESHA: php artisan queue:restart
 *      (Workers purana code use kar rahe hote hain)
 *
 *   HORIZON (Redis queues ke liye — TAVSIYA):
 *   composer require laravel/horizon
 *   - Dashboard: /horizon
 *   - Auto-scaling workers
 *   - Job metrics aur monitoring
 *   - Failed jobs retry UI
 */
