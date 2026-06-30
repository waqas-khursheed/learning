<?php

// ============================================================================
// 12 — DESIGN PATTERNS QUICK REFERENCE (OOP ka "Next Level")
// ============================================================================

// Design Patterns = COMMON PROBLEMS ke PROVEN, REUSABLE solutions.
// Ye saare patterns 4 Pillars (Encapsulation, Inheritance, Polymorphism,
// Abstraction) ko hi use karke bante hain — inhe samajhne ke baad ye
// patterns easily samajh aa jate hain.

// NOTE: SOLID Principles ke liye dekho: solid/solid.php
//       Laravel mein Service Container/Provider ke liye dekho: laravel/service_provider.php
//       Event/Listener (Observer Pattern ka real example) ke liye dekho: event-listener/


// ============================================================================
// 1. SINGLETON PATTERN — Class ka SIRF EK object pure app mein
// ============================================================================

// Use case: Database connection, Config Manager, Logger
// (Poori detail: 08_static_late_static_binding.php mein)

class Configuration
{
    private static ?Configuration $instance = null;
    private array $settings = [];

    private function __construct() {}   // bahar se "new" nahi ho sakta

    public static function getInstance(): static
    {
        return self::$instance ??= new static();
    }

    public function set(string $key, mixed $value): void
    {
        $this->settings[$key] = $value;
    }

    public function get(string $key): mixed
    {
        return $this->settings[$key] ?? null;
    }
}

Configuration::getInstance()->set('app_name', 'MyApp');
echo Configuration::getInstance()->get('app_name');  // MyApp (same instance)


// ============================================================================
// 2. FACTORY PATTERN — Object create karne ka logic ALAG class mein
// ============================================================================

// Use case: Jab object banane ka decision RUNTIME par hona ho
// (jaise Payment gateway, Notification type, etc.)

interface Notification
{
    public function send(string $message): void;
}

class EmailNotification implements Notification
{
    public function send(string $message): void { echo "Email: {$message}"; }
}

class SmsNotification implements Notification
{
    public function send(string $message): void { echo "SMS: {$message}"; }
}

class NotificationFactory
{
    public static function create(string $type): Notification
    {
        return match ($type) {
            'email' => new EmailNotification(),
            'sms'   => new SmsNotification(),
            default => throw new InvalidArgumentException("Unknown type: {$type}"),
        };
    }
}

$notification = NotificationFactory::create('email');
$notification->send('Hello!');

// FAYDA: Caller ko "new EmailNotification()" likhne ki zaroorat nahi —
// Factory decide karta hai KONSI class banani hai.


// ============================================================================
// 3. STRATEGY PATTERN — Runtime par ALGORITHM/BEHAVIOR badalna
// ============================================================================

// Use case: Discount calculation, Sorting algorithm, Payment processing
// (Bohot similar hai Polymorphism ke real-world example se — 04_polymorphism.php)

interface DiscountStrategy
{
    public function calculate(float $price): float;
}

class NoDiscount implements DiscountStrategy
{
    public function calculate(float $price): float { return $price; }
}

class PercentageDiscount implements DiscountStrategy
{
    public function __construct(private float $percent) {}

    public function calculate(float $price): float
    {
        return $price - ($price * $this->percent / 100);
    }
}

class ShoppingCart
{
    public function __construct(private DiscountStrategy $strategy) {}

    public function checkout(float $price): float
    {
        return $this->strategy->calculate($price);
    }
}

$cart = new ShoppingCart(new PercentageDiscount(20));
echo $cart->checkout(1000);   // 800 — strategy "inject" hui hai, hardcoded nahi


// ============================================================================
// 4. OBSERVER PATTERN — Ek object change hone par DOOSRE objects ko inform karna
// ============================================================================

// Ye EXACT WOHI PATTERN hai jo Laravel ka Events/Listeners system use
// karta hai! Poora real-world Laravel example dekho: event-listener/ folder

interface Observer
{
    public function update(string $event): void;
}

class EmailObserver implements Observer
{
    public function update(string $event): void
    {
        echo "Email Observer: '{$event}' event ka email bheja";
    }
}

class Subject
{
    private array $observers = [];

    public function attach(Observer $observer): void
    {
        $this->observers[] = $observer;
    }

    public function notify(string $event): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($event);
        }
    }
}

$subject = new Subject();
$subject->attach(new EmailObserver());
$subject->notify('OrderPlaced');   // Saare attached observers ko inform hota hai


// ============================================================================
// 5. REPOSITORY PATTERN — Database access ko ABSTRACT karna
// ============================================================================

// (Poora example: 06_interfaces_multiple.php mein UserRepositoryInterface)
// Use case: Controller ko ye pata na ho ke data MySQL se aa raha hai ya
// MongoDB se, ya API se — sirf Interface ke through baat kare.


// ============================================================================
// SENIOR-LEVEL NOTES
// ============================================================================

// 1. Design Pattern "use karne ke liye use" mat karo — sirf TAB use karo
//    jab WAQAI woh problem exist kare jo pattern solve karta hai.
//    Over-engineering (bewajah patterns thoosna) bhi ek bura sign hai.

// 2. Senior interviews mein sabse zyada puche jane wale patterns:
//    Singleton, Factory, Strategy, Observer, Repository, Decorator, Adapter

// 3. Laravel khud in patterns ka MASSIVE use karta hai:
//    - Service Container  → Dependency Injection + Factory
//    - Service Provider    → Registry Pattern
//    - Events/Listeners    → Observer Pattern
//    - Facades             → Facade Pattern
//    - Eloquent             → Active Record Pattern
//    - Middleware           → Chain of Responsibility Pattern

// 4. Pattern seekhne ka best tareeqa: PEHLE problem samjho (kyun zaroorat
//    pari), phir solution dekho. Pattern ka naam yaad rakhna zaroori
//    nahi, "kis situation mein kya use karna hai" — ye samajhna zaroori hai.
