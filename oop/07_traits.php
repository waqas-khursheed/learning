<?php

// ============================================================================
// 07 — TRAITS (PHP ka "Multiple Inheritance" solution)
// ============================================================================

// Problem: PHP mein ek class sirf EK class extend kar sakti hai
// (multiple class inheritance support nahi hai, jaise C++ mein hota hai).

// Lekin kabhi kabhi 2 unrelated classes mein SAME CODE chahiye hota hai —
// is situation mein Trait kaam aata hai.

// Trait = Reusable code ka "chunk" jise multiple classes mein "use" kiya ja sakta hai.
// (Interface se farq: Trait actual CODE deta hai, sirf contract nahi)


// ============================================================================
// BASIC TRAIT EXAMPLE
// ============================================================================

trait Loggable
{
    protected array $logs = [];

    public function log(string $message): void
    {
        $this->logs[] = date('Y-m-d H:i:s') . " — {$message}";
    }

    public function getLogs(): array
    {
        return $this->logs;
    }
}

trait Cacheable
{
    protected ?string $cacheKey = null;

    public function setCacheKey(string $key): void
    {
        $this->cacheKey = $key;
    }

    public function getCacheKey(): ?string
    {
        return $this->cacheKey;
    }
}


// ============================================================================
// USAGE — "use" keyword se trait ko class mein "import" karte hain
// ============================================================================

class Order
{
    use Loggable, Cacheable;   // Multiple traits ek sath use ho sakte hain!

    public function __construct(private int $id) {}

    public function place(): void
    {
        $this->log("Order #{$this->id} place hua");
        $this->setCacheKey("order:{$this->id}");
    }
}

class Product
{
    use Loggable;   // Sirf Loggable chahiye, Cacheable nahi

    public function update(): void
    {
        $this->log("Product update hua");
    }
}

$order = new Order(101);
$order->place();
print_r($order->getLogs());           // ["2024-... — Order #101 place hua"]
echo $order->getCacheKey();           // order:101

$product = new Product();
$product->update();
print_r($product->getLogs());         // Order aur Product DONO independent logs rakhte hain


// ============================================================================
// TRAIT CONFLICT RESOLUTION — Jab 2 traits mein SAME method name ho
// ============================================================================

trait English
{
    public function greet(): string
    {
        return "Hello!";
    }
}

trait Urdu
{
    public function greet(): string
    {
        return "Assalam-o-Alaikum!";
    }
}

class Greeter
{
    use English, Urdu {
        // Conflict resolve karna ZAROORI hai, warna PHP Fatal Error dega
        English::greet insteadof Urdu;     // English wala greet() use hoga
        Urdu::greet as greetInUrdu;        // Urdu wale ko naya naam de diya
    }
}

$greeter = new Greeter();
echo $greeter->greet();         // Hello!
echo $greeter->greetInUrdu();   // Assalam-o-Alaikum!


// ============================================================================
// ABSTRACT METHODS IN TRAITS — Trait force kar sakta hai ke USING class
// kuch method KHUD implement kare
// ============================================================================

trait Sortable
{
    abstract public function getSortValue(): int;

    public function isGreaterThan(self $other): bool
    {
        return $this->getSortValue() > $other->getSortValue();
    }
}

class Task
{
    use Sortable;

    public function __construct(private int $priority) {}

    // Ye method implement karna ZAROORI hai, warna PHP Fatal Error dega
    public function getSortValue(): int
    {
        return $this->priority;
    }
}


// ============================================================================
// REAL LARAVEL EXAMPLE — Traits Laravel mein bohot use hote hain
// ============================================================================

// Laravel ke khud apne built-in traits:
// - HasFactory      → Model::factory() method deta hai (database/factories)
// - SoftDeletes     → delete() ko "soft" bana deta hai (deleted_at column)
// - Notifiable      → $user->notify() method deta hai

// class User extends Model
// {
//     use HasFactory, SoftDeletes, Notifiable;
// }
//
// Ye teeno ALAG ALAG capabilities User class mein "mix" ho rahi hain
// bina User ko kisi specific parent class se inherit karaye.


// ============================================================================
// TRAIT vs INTERFACE vs ABSTRACT CLASS — QUICK COMPARISON
// ============================================================================

// | Feature                | Trait                  | Interface              | Abstract Class          |
// |-------------------------|--------------------------|---------------------------|----------------------------|
// | Code/Body deta hai?     | ✅ Haan                  | ❌ Nahi (sirf signature)   | ✅ Haan                   |
// | Multiple use ho sakte?  | ✅ Multiple traits        | ✅ Multiple interfaces     | ❌ Sirf ek extend          |
// | Object directly banta?  | ❌ Nahi                   | ❌ Nahi                    | ❌ Nahi                    |
// | Constructor allowed?    | ✅ (lekin risky/avoid)    | ❌ Nahi                    | ✅ Haan                    |
// | Purpose                 | Code reuse (horizontal)  | Contract/Capability        | Shared base + "IS-A"      |


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. Trait ko "Horizontal Code Reuse" kehte hain (Inheritance "Vertical"
//    hota hai — Parent → Child). Traits unrelated classes ke beech
//    code share karte hain.

// 2. OVERUSE se bacho — agar 5-6 traits ek class mein "use" ho rahe hain,
//    to ye sign hai ke class bohot zyada responsibilities sambhal rahi
//    hai (Single Responsibility Principle violate ho raha hai).

// 3. Trait properties HAR using class mein ALAG copy hoti hain
//    (shared/static nahi hoti) — jaise Order aur Product ke $logs
//    bilkul independent hain, ek dusre ko affect nahi karte.

// 4. Trait mein constructor define karna POSSIBLE hai lekin generally
//    AVOID kiya jata hai — isse "constructor collision" ho sakta hai
//    agar class khud ka bhi constructor define kare.
