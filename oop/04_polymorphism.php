<?php

// ============================================================================
// 04 — POLYMORPHISM (3rd Pillar of OOP)
// ============================================================================

// Polymorphism (Greek: "many forms") ka matlab hai: SAME method/function
// call, lekin OBJECT ke hisab se ALAG ALAG behavior.

// Real Life Example:
// "Pay Karo" (pay() method) — Chahe Credit Card ho, PayPal ho, ya Cash ho,
// command SAME hai "pay()", lekin har payment method apna ALAG kaam karega.
// Caller ko fikar nahi karni parti ke andar konsa method chal raha hai.


// ============================================================================
// REAL EXAMPLE — PAYMENT GATEWAYS (Most common interview/real-world example)
// ============================================================================

interface PaymentGateway
{
    public function pay(float $amount): string;
}

class StripePayment implements PaymentGateway
{
    public function pay(float $amount): string
    {
        return "Stripe ke through PKR {$amount} charge ho gaya";
    }
}

class PayPalPayment implements PaymentGateway
{
    public function pay(float $amount): string
    {
        return "PayPal ke through PKR {$amount} charge ho gaya";
    }
}

class CashPayment implements PaymentGateway
{
    public function pay(float $amount): string
    {
        return "Cash mein PKR {$amount} receive ho gaya";
    }
}


// ============================================================================
// POLYMORPHISM IN ACTION — Ek hi function, alag alag objects
// ============================================================================

// Ye function NAHI janta ke konsa gateway hai — bas itna janta hai ke
// "jo bhi PaymentGateway interface follow karta hai, uske pass pay() hai"
function processOrder(PaymentGateway $gateway, float $amount): string
{
    return $gateway->pay($amount);
}

echo processOrder(new StripePayment(), 1500);   // Stripe ke through...
echo processOrder(new PayPalPayment(), 2000);   // PayPal ke through...
echo processOrder(new CashPayment(), 500);      // Cash mein...

// Kal agar "JazzCashPayment" naya gateway add karna ho:
// class JazzCashPayment implements PaymentGateway { public function pay(...) {...} }
// processOrder() FUNCTION KO CHHEDNA TAK NAHI PARA — ye Open/Closed Principle hai!


// ============================================================================
// POLYMORPHISM KE 2 MAIN TAREEQE
// ============================================================================

// 1. METHOD OVERRIDING (Runtime Polymorphism) — Inheritance ke through
//    Child class, Parent class ke method ko apne hisab se redefine karti hai.

class Shape
{
    public function area(): float
    {
        return 0;
    }
}

class Circle extends Shape
{
    public function __construct(private float $radius) {}

    public function area(): float
    {
        return pi() * $this->radius ** 2;
    }
}

class Rectangle extends Shape
{
    public function __construct(private float $width, private float $height) {}

    public function area(): float
    {
        return $this->width * $this->height;
    }
}

// Polymorphic loop — array mein DIFFERENT shapes hain, lekin SAME method call:
$shapes = [
    new Circle(5),
    new Rectangle(4, 6),
];

foreach ($shapes as $shape) {
    echo get_class($shape) . " ka area: " . $shape->area();
    // Circle ka area: 78.54
    // Rectangle ka area: 24
}


// 2. METHOD OVERLOADING — PHP TRADITIONAL OVERLOADING SUPPORT NAHI KARTA
//    (Java/C++ ki tarah same naam multiple parameters ke sath define nahi kar sakte)
//
//    PHP mein iska alternative:
//    a) Default/Optional parameters
//    b) Variadic arguments (...$args)
//    c) __call() magic method (09_magic_methods.php mein)

class Calculator
{
    // Variadic — kitne bhi numbers pass karo
    public function add(int ...$numbers): int
    {
        return array_sum($numbers);
    }
}

$calc = new Calculator();
echo $calc->add(1, 2);        // 3
echo $calc->add(1, 2, 3, 4);  // 10


// ============================================================================
// TYPE-HINTING + POLYMORPHISM (Real Production Pattern)
// ============================================================================

// Senior developers function/method parameters mein hamesha INTERFACE
// type-hint karte hain, CONCRETE class nahi — taake polymorphism kaam kare:

// ❌ Tight coupling — sirf StripePayment hi chal sakta hai:
// function checkout(StripePayment $gateway) {...}

// ✅ Flexible — koi bhi PaymentGateway implement karne wala class chalega:
// function checkout(PaymentGateway $gateway) {...}


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. Polymorphism ka asal fayda: NEW behavior add karne ke liye EXISTING
//    code modify nahi karna parta — sirf NEW class banao jo same
//    interface/parent implement kare. (Open/Closed Principle)

// 2. instanceof se type check karke if/else chain banana POLYMORPHISM
//    KA ULTA hai (anti-pattern):
//
//    ❌ if ($gateway instanceof StripePayment) { ... }
//       elseif ($gateway instanceof PayPalPayment) { ... }
//
//    ✅ $gateway->pay($amount);  // Polymorphism se khud decide ho jata hai

// 3. Laravel mein polymorphism: Eloquent "Polymorphic Relationships"
//    (morphTo/morphMany) bhi isi concept par based hain — Comment model
//    Post ya Video, dono se "morph" (jur) sakta hai bina alag-alag
//    relationship method likhe.
