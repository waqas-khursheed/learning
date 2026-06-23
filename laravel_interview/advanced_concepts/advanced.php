<?php

/**
 * ============================================================================
 *              ADVANCED CONCEPTS — INTERVIEW Q&A
 *                 (6 Years Experience Level)
 * ============================================================================
 */


// =============================================================================
// S1: Laravel Octane kya hai? Kab use karein?
// =============================================================================

/*
 * J: Octane Laravel ko SUPERCHARGE karta hai — app ko memory mein rakhta hai.
 *
 *   Normal Laravel:
 *   Har request → Framework boot → Service Providers load → Response → Sab bhool jao
 *   ↑ Har baar DUBARA boot hota hai
 *
 *   Octane ke sath:
 *   Pehli request → Framework boot → Memory mein rakh lo
 *   Baad ki requests → Framework PEHLE SE loaded hai → Seedha code chalao
 *   ↑ 2-10x TEZ!
 *
 *   Servers: Swoole ya RoadRunner
 *
 *   ⚠️ EHTIYAT:
 *   - Global state share hoti hai requests mein
 *   - Static properties reset nahi hoti
 *   - Singletons sab requests mein share hote hain
 *   - Memory leaks ka KHAAS khayal rakhho
 *
 *   KAB USE KAREIN:
 *   ✅ High-traffic APIs
 *   ✅ Real-time applications
 *   ❌ Simple apps (fayda kam, mushkil zyada)
 */


// =============================================================================
// S2: Contracts kya hain? Facades se kya farq?
// =============================================================================

/*
 * J:
 *   Contracts = INTERFACES hain (Illuminate\Contracts\...)
 *   Facades   = Static-like WRAPPERS hain (Illuminate\Support\Facades\...)
 *
 *   Dono ek hi kaam karte hain magar tareeqa mukhtalif:
 *
 *   Contract (Interface) — Dependency Injection se:
 */

use Illuminate\Contracts\Cache\Repository as CacheContract;

class ProductService
{
    public function __construct(
        private CacheContract $cache  // Interface inject kiya
    ) {}

    public function getFeatured(): Collection
    {
        return $this->cache->remember('featured', 3600, fn() => Product::featured()->get());
    }
}

/*
 *   Facade — Static-like call se:
 */

use Illuminate\Support\Facades\Cache;

class ProductServiceFacade
{
    public function getFeatured(): Collection
    {
        return Cache::remember('featured', 3600, fn() => Product::featured()->get());
    }
}

/*
 *   ⚠️ SENIOR LEVEL:
 *   - Contracts (DI) TAVSIYA hai — better testability, explicit dependencies
 *   - Facades AASAN hain — chhoti scripts, quick prototyping ke liye
 *   - Team standards follow karo — consistency zyada ahem hai
 */


// =============================================================================
// S3: Custom Artisan Commands kaise banayen?
// =============================================================================

// php artisan make:command CleanExpiredOrders

class CleanExpiredOrders extends Command
{
    protected $signature = 'orders:clean-expired
                           {--days=30 : Kitne din purane orders}
                           {--dry-run : Sirf count dikhao, delete mat karo}';

    protected $description = 'Purane incomplete orders delete karo';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        $query = Order::where('status', 'incomplete')
            ->where('created_at', '<', now()->subDays($days));

        $count = $query->count();

        if ($count === 0) {
            $this->info('Koi expired order nahi mila.');
            return Command::SUCCESS;
        }

        if ($dryRun) {
            $this->warn("{$count} orders delete honge (dry run — kuch nahi hua).");
            return Command::SUCCESS;
        }

        if (!$this->confirm("{$count} orders delete karein?")) {
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($count);
        $query->chunkById(100, function ($orders) use ($bar) {
            foreach ($orders as $order) {
                $order->delete();
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("{$count} expired orders delete ho gaye.");

        return Command::SUCCESS;
    }
}

// Schedule karo:
// app/Console/Kernel.php:
// $schedule->command('orders:clean-expired --days=60')->daily();


// =============================================================================
// S4: Laravel Pipelines kya hain?
// =============================================================================

/*
 * J: Pipeline = Data ko kai steps se guzaarein — har step kuch kaam kare.
 *    Middleware isi pattern par bana hai.
 */

// Real example: Order processing pipeline
$order = app(Pipeline::class)
    ->send($order)
    ->through([
        ValidateOrderItems::class,     // Step 1: Items valid hain?
        CalculateTotal::class,         // Step 2: Total nikalo
        ApplyDiscount::class,          // Step 3: Discount lagao
        CalculateTax::class,           // Step 4: Tax nikalo
        CalculateShipping::class,      // Step 5: Shipping cost
        ProcessPayment::class,         // Step 6: Payment karo
        UpdateInventory::class,        // Step 7: Stock update karo
        SendConfirmation::class,       // Step 8: Email bhejo
    ])
    ->thenReturn();

// Har step:
class ApplyDiscount
{
    public function handle(Order $order, Closure $next)
    {
        if ($coupon = $order->coupon) {
            $order->discount = $coupon->calculate($order->subtotal);
        }

        return $next($order); // Aglay step ko bhejo
    }
}


// =============================================================================
// S5: Enum kaise use karein Laravel mein?
// =============================================================================

// PHP 8.1+ Backed Enum:
enum OrderStatus: string
{
    case Pending    = 'pending';
    case Confirmed  = 'confirmed';
    case Processing = 'processing';
    case Shipped    = 'shipped';
    case Delivered  = 'delivered';
    case Cancelled  = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending    => 'Zayr e Ghour',
            self::Confirmed  => 'Tasdeeq Shuda',
            self::Processing => 'Tayari mein',
            self::Shipped    => 'Bhej Diya',
            self::Delivered  => 'Pahuncha Diya',
            self::Cancelled  => 'Mansookh',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending    => 'yellow',
            self::Confirmed  => 'blue',
            self::Processing => 'orange',
            self::Shipped    => 'purple',
            self::Delivered  => 'green',
            self::Cancelled  => 'red',
        };
    }
}

// Model mein cast:
class Order extends Model
{
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
        ];
    }
}

// Istemal:
$order->status = OrderStatus::Pending;
$order->status->label();   // "Zayr e Ghour"
$order->status->color();   // "yellow"

// Validation mein:
'status' => ['required', Rule::enum(OrderStatus::class)],


// =============================================================================
// S6: Livewire vs Inertia — kya farq hai? Kab kya?
// =============================================================================

/*
 * J:
 *   LIVEWIRE:
 *   - Server-side rendering + AJAX calls
 *   - Blade templates use karta hai
 *   - PHP developer ko JavaScript sikhne ki zaroorat NAHI
 *   - Real-time interactions (form validation, search, filters)
 *   - Bandwidth zyada (har interaction par server call)
 *
 *   INERTIA.JS:
 *   - SPA (Single Page App) bina API banayen
 *   - Vue/React/Svelte frontend
 *   - Laravel controllers seedha props bhejte hain
 *   - Tez UI transitions
 *   - JavaScript expertise chahiye
 *
 *   KAB KYA:
 *   - PHP team, simple interactivity → Livewire
 *   - JS team, SPA experience → Inertia + Vue/React
 *   - Dashboard/admin → Livewire (simple forms, tables)
 *   - Customer-facing SPA → Inertia (smooth experience)
 */
