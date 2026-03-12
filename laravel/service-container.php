
<?php
// Laravel Service Container

// 1. What is a Service Container?
// 🔹 Definition:

// Laravel ka Service Container ek powerful dependency injection system hai jo objects (classes) aur unki dependencies ko manage, create, 
// aur inject karta hai jab unki zarurat hoti hai.

// Yani agar koi class kisi aur class par depend karti hai, Laravel automatically us dependency ko supply kar deta hai — bina manually new likhe.

// 2. Why Do We Need It?

// Software architecture ka ek rule hota hai:

// “Classes should depend on abstractions, not concrete implementations.”

// Agar tum directly new PaymentService() likhte ho, to tumhara code tightly coupled ho jata hai.
// Service Container ye problem solve karta hai:

// ✅ Classes independent ho jati hain
// ✅ Code testable ho jata hai
// ✅ Implementation swap karna easy hota hai
// ✅ Performance optimized hoti hai


// ⚙️ 3. How It Works (Conceptual Flow)

// ┌──────────────────────────┐
// │  Controller (needs A)    │
// └────────────┬─────────────┘
//              │
//              ▼
// ┌──────────────────────────┐
// │  Service Container       │
// │  - Knows how to create A │
// └────────────┬─────────────┘
//              │
//              ▼
// ┌──────────────────────────┐
// │  Class A (has deps B, C) │
// └──────────────────────────┘
// Controller bole: “Mujhe A chahiye”

// Container check kare: “A ko B aur C chahiye”

// Container pehle B aur C banaye, phir A ko inject kare

// Laravel ye sab automatically karta hai

// 4. Basic Example (Automatic Injection)


class PaymentService {
    public function process() {
        return "Payment processed successfully!";
    }
}

class OrderController {
    protected $payment;

    // Laravel automatically injects PaymentService here
    public function __construct(PaymentService $payment)
    {
        $this->payment = $payment;
    }

    public function store() {
        return $this->payment->process();
    }
}

// Laravel automatically PaymentService ka object banata hai aur inject karta hai.
// Tumhe manually new PaymentService() likhne ki zarurat nahi.


// 5. What is “Binding”?

// “Binding” ka matlab hai container ko batana kaunsi dependency kis class se fulfill karni hai.

// Example:

app()->bind('PaymentService', function () {
    return new PaymentService();
});
// Ab jab bhi tum bolo:

$service = app('PaymentService');
// Laravel container automatically PaymentService return karega.


// 🧰 6. Types of Bindings
// 1️⃣ Normal Binding (bind)

// Har call par naya object banata hai

app()->bind('Logger', function() {
    return new FileLogger();
});
// 2️⃣ Singleton Binding

// Ek hi object memory me rakhta hai (reuse karta hai).

app()->singleton('Logger', function() {
    return new FileLogger();
});
// 7. Interface Binding (Best Practice)

// Yeh sabse powerful part hai — Laravel ka container Interfaces ko handle kar sakta hai.

// Example:
// Step 1: Define an Interface

interface PaymentGatewayInterface {
    public function pay($amount);
}

// Step 2: Create Implementations

class StripePayment implements PaymentGatewayInterface {
    public function pay($amount) {
        return "Stripe payment of $amount successful!";
    }
}

class PaypalPayment implements PaymentGatewayInterface {
    public function pay($amount) {
        return "PayPal payment of $amount successful!";
    }
}

// Step 3: Bind Interface → Implementation

// In AppServiceProvider@register():

$this->app->bind(PaymentGatewayInterface::class, StripePayment::class);
// Step 4: Inject Anywhere

class OrderController {
    protected $gateway;

    public function __construct(PaymentGatewayInterface $gateway)
    {
        $this->gateway = $gateway;
    }

    public function checkout() {
        return $this->gateway->pay(500);
    }
}
// Laravel automatically StripePayment inject karega
// (kyunke container me ye binding set hai)


// 8. Container Helper Functions
// | Method                          | Description                  | Example                                      |
// | ------------------------------- | ---------------------------- | -------------------------------------------- |
// | `app()->make(ClassName::class)` | Manually resolve dependency  | `$obj = app()->make(PaymentService::class);` |
// | `app()->bind()`                 | Bind normal service          | `app()->bind('key', fn() => new A);`         |
// | `app()->singleton()`            | Bind single instance         | `app()->singleton(A::class, fn() => new A);` |
// | `resolve()`                     | Shortcut for `app()->make()` | `$a = resolve(A::class);`                    |
// | `App::bind()`                   | Static method version        | `App::bind(Interface::class, Impl::class);`  |


// 9. Where Laravel Uses Service Container Internally
// Laravel har major feature ke andar service container ka use karta hai:

// | Area                  | Example                             |
// | --------------------- | ----------------------------------- |
// | 🧱 Controllers        | Constructor Injection               |
// | ⚙️ Middleware         | Dependencies injected automatically |
// | 🧠 Jobs               | Dependencies auto-resolved          |
// | 📨 Events & Listeners | Automatically injected              |
// | 🔧 Service Providers  | Bindings defined                    |
// | 🧰 Commands           | CLI dependencies injected           |


// 10. Service Providers — The Heart of Binding

// Service Providers wo jagah hain jahan tum apne custom bindings register karte ho.

// Example:

// app/Providers/AppServiceProvider.php

public function register()
{
    $this->app->bind(PaymentGatewayInterface::class, StripePayment::class);
}

public function boot()
{
    // run-time logic here
}
// Laravel boot hone ke waqt ye provider load karta hai, aur binding active ho jati hai.

// 11. Dependency Injection Types

// | Type                      | Example                              | Description             |
// | ------------------------- | ------------------------------------ | ----------------------- |
// | **Constructor Injection** | via `__construct()`                  | Most common & preferred |
// | **Method Injection**      | Inject directly in method parameters | Used in routes, jobs    |
// | **Property Injection**    | via setter or public property        | Rare, manual            |

// Example — Method Injection
Route::get('/pay', function (PaymentService $service) {
    return $service->process();
});


// 12. Why Service Container is Important

// | Advantage                     | Description                                   |
// | ----------------------------- | --------------------------------------------- |
// | 🧩 **Loose Coupling**         | Classes independent hoti hain                 |
// | 🧠 **Testability**            | Mocking aur testing easy hoti hai             |
// | 🚀 **Scalability**            | Easily swap implementations                   |
// | ⚙️ **Automation**             | Laravel automatic dependency inject karta hai |
// | 🧱 **Organized Architecture** | Clean separation of logic & configuration     |

// 13. Common Mistakes
// | Mistake                          | Problem                             |
// | -------------------------------- | ----------------------------------- |
// | Direct `new Class()`             | Bypasses container — no DI benefits |
// | Using `env()` inside controllers | Breaks caching, not testable        |
// | Not binding interfaces           | Makes code tightly coupled          |
// | Manual object creation           | Reduces flexibility                 |


// 14. Example Workflow Diagram (Explained)

// [Controller] → needs → [PaymentGatewayInterface]
//        ↓
// [Service Container]
//        ↓
// binds to
//        ↓
// [StripePayment]
//        ↓
// resolved automatically → used in controller

// 15. Summary Table

// | Concept                  | Description                         |
// | ------------------------ | ----------------------------------- |
// | **Service Container**    | Dependency manager                  |
// | **Binding**              | Registering a class or interface    |
// | **Resolution**           | Getting an instance from container  |
// | **Service Provider**     | Place where bindings are registered |
// | **Dependency Injection** | Automatic injection of dependencies |
// | **Singleton**            | Same instance reused                |
// | **Bind**                 | New instance each time              |


// 16. Real-World Analogy
// Socho Laravel ka Service Container ek factory hai:

// Tum kehte ho: “Mujhe ek car chahiye.”

// Laravel factory dekhti hai: “Car kis engine se bind hai?”

// Agar binding hai EngineInterface → TeslaEngine, to Tesla engine wali car bana deta hai.

// Agar kal tumne bind badal diya
$this->app->bind(EngineInterface::class, BMWEngine::class);
// To Laravel automatically BMW wali car dene lagega 🚗
// Tumhara controller same rahega — no code change!

// 17. Summary

// | Term                  | Meaning                           |
// | --------------------- | --------------------------------- |
// | Service Container     | Laravel’s dependency manager      |
// | Binding               | Container me class register karna |
// | Resolution            | Object create karna               |
// | Service Provider      | Bindings register karne ki jagah  |
// | Interface Binding     | Loose coupling achieve karna      |
// | Singleton             | Same instance reuse karna         |
// | Constructor Injection | Most common DI method             |
