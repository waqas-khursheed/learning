
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



// bind() — Har baar naya object banata hai
// Jab bhi aap container se ye class maango, naya instance milega (fresh object har dafa).
php// AppServiceProvider.php mein register() ke andar
public function register()
{
    $this->app->bind(PaymentGateway::class, function ($app) {
        return new StripePaymentGateway();
    });
}

// Test karo:
php$obj1 = app(PaymentGateway::class);
$obj2 = app(PaymentGateway::class);

var_dump($obj1 === $obj2); // false — dono alag alag objects hain


// singleton() — Ek hi baar banega, phir wahi reuse hoga
// Jab bhi aap container se ye class maango, hamesha wahi ek instance milega (same object).
phppublic function register()
{
    $this->app->singleton(PaymentGateway::class, function ($app) {
        return new StripePaymentGateway();
    });
}
// Test karo:
php$obj1 = app(PaymentGateway::class);
$obj2 = app(PaymentGateway::class);

var_dump($obj1 === $obj2); // true — dono same object hain
// Kab use karna hai: Jab aapko database connection, config, ya cache jaisi cheezein chahiye ho jo poori request mein ek hi baar create ho aur sab jagah wahi use ho.



// make() — Container se manually object nikalna
// Ye method container ko keh raha hai "mujhe ye class resolve kar ke do".
php// Kahin bhi controller/route mein
$gateway = app()->make(PaymentGateway::class);

// Ya shortcut helper function se
$gateway = app(PaymentGateway::class);

$gateway->processPayment(100);
Real example — Controller mein:
phpclass OrderController extends Controller
{
    public function store()
    {
        // Container se manually resolve kar rahe hain
        $gateway = app()->make(PaymentGateway::class);
        $gateway->processPayment(500);

        return "Payment done!";
    }
}


// resolve() — make() jaisa hi kaam, alag helper function
// Laravel mein resolve() ek global helper function hai jo app()->make() ka shortcut hai.
php$gateway = resolve(PaymentGateway::class);
$gateway->processPayment(200);
Ye bilkul waisa hi hai jaise:
php$gateway = app()->make(PaymentGateway::class);



// Full Example — Sab kuch ek saath
php// Interface
interface PaymentGateway
{
    public function processPayment($amount);
}

// Implementation
class StripePaymentGateway implements PaymentGateway
{
    public function processPayment($amount)
    {
        return "Processing $amount via Stripe";
    }
}

// Service Provider
class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // singleton use kiya — pura app mein ek hi instance chalega
        $this->app->singleton(PaymentGateway::class, function ($app) {
            return new StripePaymentGateway();
        });
    }
}

// Controller mein use karna
class OrderController extends Controller
{
    public function store()
    {
        // Method 1: make()
        $gateway1 = app()->make(PaymentGateway::class);

        // Method 2: resolve()
        $gateway2 = resolve(PaymentGateway::class);

        // Method 3: Dependency Injection (best practice!)
        // Constructor mein automatically inject ho jayega
    }

    // Best practice: Constructor Injection
    public function __construct(PaymentGateway $gateway)
    {
        $this->gateway = $gateway; // Container khud resolve kar ke de dega
    }
}



// Chaliye make() ke practical fayde dekhte hain — kab aur kyun use karna chahiye.
// make() kyun use karte hain? (Real Benefits)
// 1️⃣ Jab Constructor Injection possible na ho
// Constructor Injection sirf classes (controllers, jobs, commands) mein automatic kaam karta hai. Lekin agar aap kisi normal function, helper, ya closure ke andar ho, to wahan Laravel khud-ba-khud inject nahi kar sakta — wahan make() kaam aata hai.
php// Ek route closure ke andar

Route::get('/test', function () {
    $gateway = app()->make(PaymentGateway::class);
    return $gateway->processPayment(100);
});

// 2️⃣ Runtime pe dynamically decide karna kya resolve karna hai
// Agar aapko condition ke hisaab se alag alag class resolve karni ho, to make() bohat useful hai.

phppublic function pay($method)
{
    if ($method === 'stripe') {
        $gateway = app()->make(StripePaymentGateway::class);
    } else {
        $gateway = app()->make(PaypalPaymentGateway::class);
    }

    return $gateway->processPayment(500);
}
// Constructor Injection mein ye flexibility nahi milti — wahan class fixed hoti hai.

// 3️⃣ Parameters ke saath object resolve karna
// make() ka ek bohot bada faida ye hai ke aap extra parameters pass kar sakte ho jo container khud resolve nahi kar sakta (jaise strings, numbers).

phpclass ReportGenerator
{
    public function __construct(protected string $title, protected Database $db)
    {
        // $db to container khud resolve kar lega
        // lekin $title ek plain string hai, ye container ko pata nahi
    }
}

// make() se hum manually string pass kar sakte hain

$report = app()->make(ReportGenerator::class, [
    'title' => 'Monthly Sales Report'
]);

// Ye Constructor Injection se possible nahi, kyunke Laravel controller mein khud call karta hai aur aap manually string pass nahi kar sakte.

// 4️⃣ Testing aur Debugging mein easy hota hai
// Jab aap manually test kar rahe ho (tinker ya kisi script mein), to make() seedha object nikaal deta hai bina poori class banaye.
php// php artisan tinker ke andar

$gateway = app()->make(PaymentGateway::class);
$gateway->processPayment(100);

// 5️⃣ Lazy Loading — Object sirf tab bane jab zaroorat ho
// Agar aap object ko conditionally use karna chahte ho (har waqt nahi), to make() se aap sirf us waqt resolve karte ho jab actually zaroorat ho — performance behtar hoti hai.

phppublic function handle()
{
    if ($this->needsPayment) {
        // Sirf tab object banega jab condition true ho
        $gateway = app()->make(PaymentGateway::class);
        $gateway->processPayment(100);
    }
}

// Agar aap Constructor Injection use karte, to object hamesha ban jata — chahe use ho ya na ho.


// Constructor Injection = Best Practice (default choice)
// make() = Special cases ke liye (jab constructor injection possible ya practical na ho)
// Agar aap normal controller/class likh rahe ho, to hamesha Constructor Injection use karo — clean aur testable code milta hai. 
// make() sirf tab use karo jab genuinely zaroorat ho (jaise upar ke scenarios).