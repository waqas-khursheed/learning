<?php

declare(strict_types=1);

// ============================================================================
// 10 — MODERN PHP 8+ OOP FEATURES (Senior devs ko ye sab pata hona chahiye)
// ============================================================================


// ============================================================================
// 1. CONSTRUCTOR PROPERTY PROMOTION (PHP 8.0+)
// ============================================================================

// PURANA TAREEQA (verbose):
class OldUser
{
    private string $name;
    private string $email;

    public function __construct(string $name, string $email)
    {
        $this->name  = $name;
        $this->email = $email;
    }
}

// NAYA TAREEQA — constructor mein hi property declare + assign ho jati hai:
class User
{
    public function __construct(
        private string $name,
        private string $email,
        private int $age = 18    // default value bhi de sakte ho
    ) {}
}

// Bohot saari files mein ye pattern already use ho chuka hai (BankAccount,
// Manager, Product, etc.) — ye AB PHP mein STANDARD practice hai.


// ============================================================================
// 2. READONLY PROPERTIES (PHP 8.1+)
// ============================================================================

class Money
{
    public function __construct(
        public readonly float $amount,
        public readonly string $currency
    ) {}
}

$price = new Money(1500, 'PKR');
echo $price->amount;        // Read kar sakte ho
// $price->amount = 2000;   // ❌ FATAL ERROR — readonly property change nahi ho sakti

// Real use: DTOs (Data Transfer Objects), Value Objects, Immutable models
// jahan object banne ke baad data kabhi change NAHI hona chahiye.


// ============================================================================
// 3. ENUMS (PHP 8.1+) — Pehle PHP mein enums nahi hote the, constants se kaam chalta tha
// ============================================================================

// PURANA TAREEQA (error-prone — koi bhi string pass ho sakti thi):
class OldOrderStatus
{
    const PENDING   = 'pending';
    const COMPLETED = 'completed';
}

// NAYA TAREEQA — Pure Enum (sirf cases, koi value nahi):
enum Direction
{
    case North;
    case South;
    case East;
    case West;
}

// Backed Enum (har case ke sath ek value attached hoti hai — string/int)
enum OrderStatus: string
{
    case PENDING    = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED  = 'completed';
    case CANCELLED  = 'cancelled';

    // Enums mein METHODS bhi ho sakte hain!
    public function label(): string
    {
        return match ($this) {
            self::PENDING    => 'Pending Order',
            self::PROCESSING => 'Processing Order',
            self::COMPLETED  => 'Completed Order',
            self::CANCELLED  => 'Cancelled Order',
        };
    }

    public function isFinal(): bool
    {
        return $this === self::COMPLETED || $this === self::CANCELLED;
    }
}

function updateOrderStatus(OrderStatus $status): void
{
    echo $status->label();
}

updateOrderStatus(OrderStatus::PENDING);   // "Pending Order"
echo OrderStatus::COMPLETED->isFinal() ? 'yes' : 'no';   // yes
echo OrderStatus::from('processing')->label();   // string value se enum case banaya


// ============================================================================
// 4. MATCH EXPRESSION (PHP 8.0+) — switch ka modern, safer alternative
// ============================================================================

function getDiscount(string $userType): float
{
    // switch ki tarah, lekin:
    // - strict comparison (===) use karta hai, "==" nahi
    // - return value deta hai (statement nahi, EXPRESSION hai)
    // - "break" likhne ki zaroorat nahi, fall-through nahi hota
    // - agar koi case match na ho aur "default" na ho, to UnhandledMatchError throw hota hai
    return match ($userType) {
        'vip'     => 0.30,
        'regular' => 0.10,
        'new'     => 0.05,
        default   => 0.0,
    };
}

echo getDiscount('vip');   // 0.30


// ============================================================================
// 5. NULLSAFE OPERATOR (PHP 8.0+) — ?->
// ============================================================================

class Address
{
    public function __construct(public ?string $city = null) {}
}

class Customer
{
    public function __construct(public ?Address $address = null) {}
}

$customer = new Customer();

// PURANA TAREEQA:
// $city = $customer->address !== null ? $customer->address->city : null;

// NAYA TAREEQA — agar koi bhi step null ho to poori chain null return karegi (error nahi):
$city = $customer->address?->city;
echo $city ?? 'City not set';   // "City not set" — exception nahi aaya


// ============================================================================
// 6. NAMED ARGUMENTS (PHP 8.0+)
// ============================================================================

class Product
{
    public function __construct(
        public string $name,
        public float $price,
        public int $stock = 0,
        public bool $featured = false
    ) {}
}

// Order ki fikar kiye bina, sirf jo chahiye wo naam se pass karo:
$product = new Product(
    name: 'Laptop',
    price: 150000,
    featured: true   // 'stock' skip kar diya, default (0) use hoga
);


// ============================================================================
// 7. FIRST-CLASS CALLABLE SYNTAX (PHP 8.1+)
// ============================================================================

class StringHelper
{
    public static function upper(string $s): string
    {
        return strtoupper($s);
    }
}

// PURANA TAREEQA:
// $fn = [StringHelper::class, 'upper'];
// $fn = 'StringHelper::upper';

// NAYA TAREEQA — clean aur IDE-friendly:
$fn = StringHelper::upper(...);
echo $fn('hello');   // HELLO


// ============================================================================
// 8. INTERSECTION TYPES (PHP 8.1+) aur UNION TYPES (PHP 8.0+)
// ============================================================================

interface Countable2 { public function count(): int; }
interface Iterable2  { public function current(): mixed; }

// Union Type — int YA string accept karo
function formatId(int|string $id): string
{
    return "ID: {$id}";
}

// Intersection Type — Object DONO interfaces implement karta ho
function processCollection(Countable2&Iterable2 $collection): void
{
    echo $collection->count();
}


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. readonly + constructor promotion = best combo for DTOs/Value Objects:
//    class CreateOrderDTO {
//        public function __construct(
//            public readonly int $userId,
//            public readonly array $items,
//        ) {}
//    }

// 2. Enums ab CONSTANTS ki jagah le chuke hain modern PHP projects mein —
//    type-safety milti hai (sirf valid cases accept hote hain, koi bhi
//    random string nahi).

// 3. declare(strict_types=1) file ke top par lagana senior teams mein
//    STANDARD hai — bina iske PHP automatically types convert kar deta
//    hai ("5" string ko 5 int bana dega), jo subtle bugs create karta hai.

// 4. match() ko if/switch se zyada prefer karo jab simple value-comparison
//    karni ho — cleaner, safer (strict comparison), aur expression hai.
