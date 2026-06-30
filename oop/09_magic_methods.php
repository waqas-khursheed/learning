<?php

// ============================================================================
// 09 — MAGIC METHODS (PHP ke special __methods)
// ============================================================================

// Magic Methods wo special methods hain jo PHP AUTOMATICALLY call karta hai
// kisi KHAAS situation par — tumhe inhe manually call karne ki zaroorat nahi.
// Sab __ (double underscore) se start hote hain.


// ============================================================================
// __construct() / __destruct() — object banne/khatam hone par
// ============================================================================

class Logger
{
    public function __construct()
    {
        echo "Logger object bana";   // "new Logger()" par chalta hai
    }

    public function __destruct()
    {
        echo "Logger object destroy hua";  // script khatam ya unset() par chalta hai
    }
}


// ============================================================================
// __get() / __set() — Jab koi UNDEFINED/PRIVATE property access ki jaye
// ============================================================================

class DynamicAttributes
{
    private array $attributes = [];

    // Jab bhi koi non-existent/private property READ ki jaye
    public function __get(string $name): mixed
    {
        echo "[__get called for: {$name}]";
        return $this->attributes[$name] ?? null;
    }

    // Jab bhi koi non-existent/private property WRITE ki jaye
    public function __set(string $name, mixed $value): void
    {
        echo "[__set called for: {$name}]";
        $this->attributes[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    public function __unset(string $name): void
    {
        unset($this->attributes[$name]);
    }
}

$obj = new DynamicAttributes();
$obj->title = 'Hello';   // __set call hota hai (kyunke $title property exist nahi karti)
echo $obj->title;        // __get call hota hai
echo isset($obj->title) ? 'yes' : 'no';  // __isset call hota hai


// ============================================================================
// __call() / __callStatic() — Jab UNDEFINED method call ki jaye
// ============================================================================

class QueryBuilder
{
    private array $wheres = [];

    // Jab koi method call ho jo class mein DEFINE nahi hai
    public function __call(string $method, array $arguments): mixed
    {
        if (str_starts_with($method, 'where')) {
            $field = strtolower(substr($method, 5));   // "whereName" → "name"
            $this->wheres[$field] = $arguments[0];
            return $this;   // method chaining ke liye $this return
        }

        throw new BadMethodCallException("Method {$method} exist nahi karta");
    }

    public static function __callStatic(string $method, array $arguments): mixed
    {
        if ($method === 'create') {
            return new static();
        }

        throw new BadMethodCallException("Static method {$method} exist nahi karta");
    }

    public function getWheres(): array
    {
        return $this->wheres;
    }
}

$query = QueryBuilder::create()   // __callStatic
    ->whereName('Ali')            // __call — magic mein "name" => "Ali" set hua
    ->whereAge(25);                // __call — magic mein "age" => 25 set hua

print_r($query->getWheres());     // ['name' => 'Ali', 'age' => 25]

// YE EXACT WOHI TAREEQA HAI jo Laravel Eloquent ke "whereXxx()" methods
// ke peeche use hota hai!


// ============================================================================
// __toString() — Jab object ko STRING ki tarah use kiya jaye
// ============================================================================

class Money
{
    public function __construct(private float $amount, private string $currency = 'PKR') {}

    public function __toString(): string
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }
}

$price = new Money(1500.5);
echo $price;              // "1,500.50 PKR" — echo automatically __toString() call karta hai
echo "Price: {$price}";   // String interpolation mein bhi kaam karta hai


// ============================================================================
// __invoke() — Jab object ko FUNCTION ki tarah call kiya jaye
// ============================================================================

class Multiplier
{
    public function __construct(private int $factor) {}

    public function __invoke(int $number): int
    {
        return $number * $this->factor;
    }
}

$double = new Multiplier(2);
echo $double(5);   // 10 — object ko seedha function ki tarah call kar diya!

// Real use: array_map(), Laravel Middleware classes, Action classes
// $numbers = array_map(new Multiplier(3), [1, 2, 3]);  // [3, 6, 9]


// ============================================================================
// __clone() — Jab object clone ho (clone $obj)
// ============================================================================

class Order
{
    public array $items = [];

    public function __construct(public int $id) {}

    // clone hone par customize karne ka mauka milta hai
    public function __clone(): void
    {
        $this->id = $this->id . '-copy';
        // Deep clone ki zaroorat ho to nested objects yahan clone karo
    }
}

$original = new Order(101);
$copy = clone $original;
echo $copy->id;   // "101-copy"


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. Magic methods PERFORMANCE COST rakhte hain — __get/__set/__call
//    normal property/method access se SLOWER hote hain (PHP ko extra
//    lookup karna parta hai). Sirf tab use karo jab genuinely DYNAMIC
//    behavior chahiye ho (jaise Eloquent, fluent query builders).

// 2. __get()/__set() ZYADA use karna "magic" wala anti-pattern bana
//    sakta hai — IDE autocomplete bhi kaam nahi karta aur bugs
//    runtime tak pata nahi chalte. Senior devs explicit properties
//    ko prefer karte hain jab tak DYNAMIC fields ki real zaroorat na ho.

// 3. __call() ka real production use: Fluent Query Builders, API SDK
//    wrappers (jahan method names dynamically generate hote hain),
//    Laravel Eloquent ke magic where methods.

// 4. __toString() exceptions throw NAHI kar sakta PHP 8 se pehle —
//    PHP 8+ mein ye restriction hata di gayi hai.
