<?php

// ============================================================================
// 06 — INTERFACES (Multiple Implementation + Real Patterns)
// ============================================================================

// Interface ek "contract" hota hai — ye batata hai ke class mein KONSE
// methods HONE CHAHIYE, lekin ye nahi batata ke wo methods KAISE kaam karenge.

// Real Life Example:
// "Driving License" ek contract hai — jis ke pass license hai, wo:
// - Gaari start kar sakta hai
// - Gaari rok sakta hai
// License ye nahi batata ke TUM KAISE drive karoge — har driver ka style alag hota hai.


// ============================================================================
// BASIC INTERFACE
// ============================================================================

interface Drivable
{
    public function start(): void;
    public function stop(): void;
}

class Car implements Drivable
{
    public function start(): void
    {
        echo "Car start ho gayi";
    }

    public function stop(): void
    {
        echo "Car ruk gayi";
    }
}

// Agar Car class koi bhi interface method implement NA kare,
// PHP FATAL ERROR dega: "Class Car must implement method stop()"


// ============================================================================
// MULTIPLE INTERFACES — Ek class, KAI contracts (PHP ki taqat)
// ============================================================================

// PHP mein ek class MULTIPLE interfaces implement kar sakti hai
// (lekin sirf EK class extend kar sakti hai — koi multiple class inheritance nahi)

interface Loggable
{
    public function log(string $message): void;
}

interface Cacheable
{
    public function getCacheKey(): string;
}

interface Searchable
{
    public function toSearchableArray(): array;
}

// Ek class teeno contracts ko implement kar rahi hai:
class Product implements Loggable, Cacheable, Searchable
{
    public function __construct(
        private int $id,
        private string $name,
        private float $price
    ) {}

    public function log(string $message): void
    {
        echo "[Product #{$this->id}] {$message}";
    }

    public function getCacheKey(): string
    {
        return "product:{$this->id}";
    }

    public function toSearchableArray(): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'price' => $this->price,
        ];
    }
}


// ============================================================================
// INTERFACE KA REAL FAYDA — DEPENDENCY INJECTION KE SATH
// ============================================================================

// Function/class sirf interface janta hai, CONCRETE implementation nahi:

function cacheItem(Cacheable $item): string
{
    return "Caching with key: " . $item->getCacheKey();
}

$product = new Product(1, 'Laptop', 150000);
echo cacheItem($product);  // Caching with key: product:1

// Kal "Order implements Cacheable" bhi ban sakta hai —
// cacheItem() function ko CHHEDNE ki zaroorat nahi.


// ============================================================================
// INTERFACE INHERITANCE (Interface khud doosre Interface ko extend kar sakta hai)
// ============================================================================

interface Animal
{
    public function eat(): void;
}

// Interface "extends" keyword use karta hai (implements nahi)
interface Pet extends Animal
{
    public function play(): void;
}

class Dog implements Pet
{
    // Dono methods implement karne ZAROORI hain (eat() aur play())
    public function eat(): void { echo "Dog kha raha hai"; }
    public function play(): void { echo "Dog khel raha hai"; }
}


// ============================================================================
// INTERFACE CONSTANTS
// ============================================================================

interface PaymentStatus
{
    const PENDING   = 'pending';
    const COMPLETED = 'completed';
    const FAILED    = 'failed';
}

class Order implements PaymentStatus
{
    public function checkStatus(): string
    {
        return self::COMPLETED;  // Interface ke constant ko self:: se access kiya
    }
}


// ============================================================================
// REAL LARAVEL EXAMPLE — Repository Pattern (Interface ka sabse common use)
// ============================================================================

interface UserRepositoryInterface
{
    public function find(int $id): ?array;
    public function all(): array;
}

class EloquentUserRepository implements UserRepositoryInterface
{
    public function find(int $id): ?array
    {
        // return User::find($id)->toArray();
        return ['id' => $id, 'name' => 'Ali'];
    }

    public function all(): array
    {
        // return User::all()->toArray();
        return [];
    }
}

// Kal agar Eloquent se hatke "MongoUserRepository" banana ho,
// jahan jahan UserRepositoryInterface type-hint hai, wahan
// kuch badalne ki zaroorat nahi — bas Service Provider mein
// binding change karni hai. (Dekho: laravel/service_provider.php)


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. Interface Segregation Principle (SOLID ka "I") — Ek BARA interface
//    banane ke bajaye, CHHOTE focused interfaces banao (Loggable,
//    Cacheable, Searchable alag alag — ek "DoEverything" interface nahi)

// 2. instanceof check se interface bhi verify ho sakta hai:
//    if ($product instanceof Cacheable) { ... }

// 3. Interface mein PHP 8 tak method BODY nahi de sakte (sirf signature).
//    Agar shared CODE chahiye (na sirf contract), to Trait ya Abstract
//    Class use karo (05_abstraction.php, 07_traits.php).

// 4. Real projects mein convention: Interface ka naam "XxxInterface" ya
//    "Contracts\Xxx" namespace mein rakha jata hai (Laravel khud
//    Illuminate\Contracts namespace use karta hai).
