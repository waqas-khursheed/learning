<?php

// ============================================================================
// 11 — ADVANCED OOP CONCEPTS (Senior-level topics)
// ============================================================================


// ============================================================================
// 1. OBJECT CLONING — SHALLOW vs DEEP COPY
// ============================================================================

// Normal assignment object ko COPY NAHI karta — dono variables SAME
// object ko point karte hain (reference):

class Engine
{
    public function __construct(public int $horsepower) {}
}

class Car
{
    public function __construct(public string $model, public Engine $engine) {}
}

$car1 = new Car('Civic', new Engine(150));
$car2 = $car1;   // ❌ Copy NAHI hui — $car2 aur $car1 SAME object hain
$car2->model = 'Corolla';
echo $car1->model;   // "Corolla" — original bhi badal gaya! (DANGEROUS)

// SHALLOW CLONE — "clone" keyword:
$car3 = clone $car1;
$car3->model = 'City';
echo $car1->model;   // "Corolla" — original safe hai (top-level properties copy hue)

// LEKIN PROBLEM: nested OBJECTS (Engine) ab bhi SHARED hain!
$car3->engine->horsepower = 999;
echo $car1->engine->horsepower;   // 999 — original ka engine bhi badal gaya! ❌

// DEEP CLONE — __clone() magic method use karke nested objects bhi clone karo:
class CarDeep
{
    public function __construct(public string $model, public Engine $engine) {}

    public function __clone(): void
    {
        // Jab is object ko clone kiya jaye, iske andar ka Engine bhi clone karo
        $this->engine = clone $this->engine;
    }
}

$carA = new CarDeep('Civic', new Engine(150));
$carB = clone $carA;
$carB->engine->horsepower = 999;
echo $carA->engine->horsepower;   // 150 — ab original SAFE hai (TRUE deep clone)


// ============================================================================
// 2. instanceof — RUNTIME TYPE CHECKING
// ============================================================================

interface Shape {}
class Circle implements Shape {}
class Square implements Shape {}

function describe(Shape $shape): string
{
    if ($shape instanceof Circle) {
        return "Ye ek Circle hai";
    }

    if ($shape instanceof Square) {
        return "Ye ek Square hai";
    }

    return "Unknown shape";
}

// NOTE: instanceof chains POLYMORPHISM ka ULTA hai (dekho 04_polymorphism.php)
// Production code mein zyada instanceof if/else chains = code smell.
// Behtar: har Shape apna khud ka describe() method implement kare.


// ============================================================================
// 3. ANONYMOUS CLASSES (PHP 7+) — Bina naam ki class, on-the-fly
// ============================================================================

interface Logger
{
    public function log(string $message): void;
}

function createLogger(): Logger
{
    // Quick, one-off implementation — jab poori class file banana
    // overkill ho (testing mein bohot common, ya simple callback ke liye)
    return new class implements Logger {
        public function log(string $message): void
        {
            echo "[ANON LOG] {$message}";
        }
    };
}

$logger = createLogger();
$logger->log('Test message');

// REAL USE CASE: Unit Testing mein FAKE/MOCK dependency banana bina
// alag test-double class file banaye.


// ============================================================================
// 4. SPL INTERFACES — PHP ke BUILT-IN interfaces jo objects ko
//    "array jaisa" ya "loopable" banate hain
// ============================================================================

// Countable — count($obj) kaam karne deta hai
class ShoppingCart implements Countable, IteratorAggregate, ArrayAccess
{
    private array $items = [];

    // Countable — count($cart) call hone par
    public function count(): int
    {
        return count($this->items);
    }

    // IteratorAggregate — foreach ($cart as $item) chalane ke liye
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->items);
    }

    // ArrayAccess — $cart['key'] jaisa access dene ke liye
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }
}

$cart = new ShoppingCart();
$cart[] = 'Laptop';     // offsetSet call hota hai (ArrayAccess ki wajah se)
$cart[] = 'Mouse';

echo count($cart);       // 2 — Countable ki wajah se
foreach ($cart as $item) {  // IteratorAggregate ki wajah se
    echo $item;
}
echo $cart[0];            // "Laptop" — ArrayAccess ki wajah se

// YE EXACT WOHI TAREEQA HAI jo Laravel Collection class use karta hai!


// ============================================================================
// 5. REFLECTION — PHP Runtime mein class ke baare mein "introspect" karna
// ============================================================================

class SampleClass
{
    public function __construct(public string $name) {}

    public function greet(): string
    {
        return "Hello {$this->name}";
    }
}

$reflection = new ReflectionClass(SampleClass::class);

echo $reflection->getName();                              // SampleClass
foreach ($reflection->getMethods() as $method) {
    echo "Method: " . $method->getName();                 // __construct, greet
}

// REAL USE: Laravel ka Service Container Reflection use karta hai
// taake automatically pata laga sake constructor mein KONSI dependencies
// chahiye, aur unhe automatically inject kar sake (Dependency Injection).
// Dekho: laravel/service-container.php


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. Cloning ke time hamesha socho: kya nested objects bhi independent
//    hone chahiye? Agar haan, __clone() mein unhe explicitly clone karo.

// 2. instanceof checks acceptable hain jab tum THIRD-PARTY/EXTERNAL
//    classes ke sath kaam kar rahe ho jinke design par tumhara control
//    nahi. Apne khud ke classes mein polymorphism ko prefer karo.

// 3. Anonymous classes mostly TESTING aur QUICK PROTOTYPES ke liye
//    use hoti hain — production business logic ke liye named classes hi best hain.

// 4. Reflection POWERFUL hai lekin SLOW hai (performance cost). Laravel
//    jaisa framework ise CACHE karta hai (bootstrap cache) taake har
//    request par reflection dobara na karni pare.
