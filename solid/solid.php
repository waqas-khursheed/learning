<?php 

//  SOLID Principles – Definition
// SOLID object-oriented programming ke 5 design rules ka set hai jo code ko clean, maintainable, reusable, aur scalable banate hain.

// Kya hotay hain SOLID principles?
// SOLID ka matlab hai:
// S – Single Responsibility Principle:
// Har class ka sirf ek hi kaam (responsibility) hona chahiye.

// O – Open/Closed Principle:
// Code extend kiya ja sakta hai lekin modify nahi karna chahiye.

// L – Liskov Substitution Principle:
// Child class parent class ki jagah use ho sakti hai bina system tode.

// I – Interface Segregation Principle:
// Chhoti, focused interfaces banao; clients ko unnecessary methods implement na karna paday.

// D – Dependency Inversion Principle:
// Code abstractions (interfaces) par depend kare, concrete implementations par nahi.

//  Kyun use karte hain?

// Code ko samajhna aur maintain karna easy hota hai

// Reusability badhti hai

// Testing aur debugging asaan hoti hai

// System scalable aur flexible ban jata hai

// Team projects mein conflicts aur errors kam hotay hain

//  Kya ye zaroori hain?

// Haan, bohot zaroori hain.
// Ye principles ensure karte hain ke aapka code:

// Future changes ke liye ready ho

// Large projects mein stable rahe

// Bugs aur coupling se free ho

// In short:
//  SOLID = Professional, future-proof software design. -->



// S – Single Responsibility Principle (SRP)
//  Definition:

// “A class should have only one reason to change.”
// Matlab: Har class ka sirf ek hi kaam (responsibility) hona chahiye.

//  Kyun use karte hain:

// Code clean aur understandable ban jata hai.

// Agar ek kaam badalta hai to doosra kaam affect nahi hota.

// Testing aur maintenance easy ho jati hai.

//  Bad Example (Violation of SRP):

class OrderService {
    public function createOrder($data) {
        // Order create kar raha hai
    }

    public function sendOrderEmail($order) {
        // Email bhi bhej raha hai (extra responsibility)
    }
}

// Yahan OrderService do kaam kar raha hai:
// Order create karna
// Email bhejna
// Agar email ka process badla, to order ka code bhi modify hoga — violation of SRP.

// Good Example (Follows SRP):

class OrderService {
    public function createOrder($data) {
        // Sirf order create kare
    }
}

class OrderMailer {
    public function sendOrderEmail($order) {
        // Sirf email bheje
    }
}


// Ab dono classes ka ek specific kaam hai.
// Code reusable, maintainable aur testable ho gaya




// O – Open/Closed Principle (OCP)

// Definition:
// “Software entities (classes, modules, functions) should be open for extension, but closed for modification.”

// Matlab:
// Aap apne code mein naye features add kar sako (extend kar sako) bina purana code badle (modify kiye).

//  Kyun use karte hain:

// Code stable aur safe rehta hai (old code toot’tā nahi).

// Naye features add karna easy hota hai.

// Future changes ke liye system ready rehta hai.

//  Bad Example (Violation of OCP):


class PaymentProcessor {
    public function pay($type) {
        if ($type === 'paypal') {
            // PayPal payment logic
        } elseif ($type === 'stripe') {
            // Stripe payment logic
        }
    }
}

// Problem:
// Agar kal “RazorPay” add karni ho to ye class modify karni padegi — OCP break ho gaya 


// Good Example (Follows OCP):


interface PaymentMethod {
    public function pay();
}

class PayPalPayment implements PaymentMethod {
    public function pay() {
        // PayPal payment logic
    }
}

class StripePayment implements PaymentMethod {
    public function pay() {
        // Stripe payment logic
    }
}

class PaymentProcessor {
    public function process(PaymentMethod $method) {
        $method->pay();
    }
}


// Ab agar naya gateway (e.g. RazorPay) add karna ho to sirf nayi class likhni hai:

class RazorPayPayment implements PaymentMethod {
    public function pay() {
        // RazorPay logic
    }
}


// Step 1: Interface
app/Contracts/PaymentMethod.php
php<?php

namespace App\Contracts;

interface PaymentMethod {
    public function pay(float $amount): string;
}
// Step 2: Concrete Classes
app/Services/Payments/StripePayment.php
php<?php

namespace App\Services\Payments;

use App\Contracts\PaymentMethod;

class StripePayment implements PaymentMethod {
    public function pay(float $amount): string {
        // Real Stripe logic yahan aayegi
        return "Paid $amount via Stripe";
    }
}
app/Services/Payments/PayPalPayment.php
php<?php

namespace App\Services\Payments;

use App\Contracts\PaymentMethod;

class PayPalPayment implements PaymentMethod {
    public function pay(float $amount): string {
        return "Paid $amount via PayPal";
    }
}
// app/Services/Payments/RazorPayPayment.php
php<?php

namespace App\Services\Payments;

use App\Contracts\PaymentMethod;

class RazorPayPayment implements PaymentMethod {
    public function pay(float $amount): string {
        return "Paid $amount via RazorPay";
    }
}
// Step 3: Factory Class (Best Practice — logic yahan rakhein, ServiceProvider mein nahi)
app/Factories/PaymentMethodFactory.php
php<?php

namespace App\Factories;

use App\Contracts\PaymentMethod;
use App\Services\Payments\StripePayment;
use App\Services\Payments\PayPalPayment;
use App\Services\Payments\RazorPayPayment;
use InvalidArgumentException;

class PaymentMethodFactory {
    public static function make(string $type): PaymentMethod {
        return match ($type) {
            'stripe'   => new StripePayment(),
            'paypal'   => new PayPalPayment(),
            'razorpay' => new RazorPayPayment(),
            default    => throw new InvalidArgumentException("Unsupported payment method: $type"),
        };
    }
}
// Step 4: Service Provider (Clean — bas Factory ko call kar raha hai)
app/Providers/AppServiceProvider.php
php<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\PaymentMethod;
use App\Factories\PaymentMethodFactory;

class AppServiceProvider extends ServiceProvider {
    public function register() {
        $this->app->bind(PaymentMethod::class, function ($app) {
            $type = request()->input('payment_type', 'stripe');

            return PaymentMethodFactory::make($type);
        });
    }
}
// Step 5: Controller (Clean — kuch bhi decide nahi kar raha, bas use kar raha hai)
app/Http/Controllers/PaymentController.php
php<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentMethod;
use Illuminate\Http\Request;

class PaymentController extends Controller {
    public function __construct(
        protected PaymentMethod $paymentMethod
    ) {}

    public function checkout(Request $request) {
        $amount = $request->input('amount', 100);

        $result = $this->paymentMethod->pay($amount);

        return response()->json(['message' => $result]);
    }
}
// Step 6: Route
// routes/web.php
// phpuse App\Http\Controllers\PaymentController;

Route::post('/checkout', [PaymentController::class, 'checkout']);
// Test karein
bashcurl -X POST http://your-app.test/checkout \
  -d "payment_type=paypal&amount=500"

# Response: {"message": "Paid 500 via PayPal"}
bashcurl -X POST http://your-app.test/checkout \
  -d "payment_type=razorpay&amount=250"

# Response: {"message": "Paid 250 via RazorPay"}
// Ye approach best kyun hai?
// FaidaWajahSingle ResponsibilityFactory sirf class banati hai, Controller sirf use karta hai, ServiceProvider sirf bind karta haiTestablePaymentMethodFactory::make('stripe') ko independently test kar sakte hainOpen/Closed PrincipleNaya gateway add karna ho to sirf Factory mein ek line add karni haiClean ControllerController ko pata hi nahi konsi class use ho rahi hai — sirf interface se kaam chalata haiReusableFactory ko kahin bhi (jobs, commands, events) use kar sakte hain, sirf HTTP request tak mehdood nahi
Agar naya gateway (e.g. EasyPaisa) add karna ho:
php// Bas ye 2 kaam karein:

// 1. Nayi class banayein
class EasyPaisaPayment implements PaymentMethod {
    public function pay(float $amount): string {
        return "Paid $amount via EasyPaisa";
    }
}

// 2. Factory mein ek line add karein
'easypaisa' => new EasyPaisaPayment(),
// Baaki koi bhi code touch nahi karna — na Controller, na ServiceProvider. Yehi asal SOLID

// Purana code untouched — system open for extension, closed for modification.


// L – Liskov Substitution Principle (LSP)
//  Definition:

// “Objects of a child (subclass) should be replaceable for their parent (superclass) without breaking the system.”

// Matlab:
// Agar ek class doosri class se inherit karti hai, to wo parent ki jagah use ho sake bina code tod ke.

//  Kyun use karte hain:

// Code predictable aur reliable rehta hai.

// Inheritance sahi tarike se kaam karti hai.

// Bugs aur unexpected behavior kam hotay hain.

// ❌ Bad Example (Violation of LSP):

class Bird {
    public function fly() {
        return "Flying in the sky";
    }
}

class Penguin extends Bird {
    public function fly() {
        throw new Exception("Penguins can't fly!");
    }
}

// Problem:
// Penguin technically Bird hai, lekin wo fly nahi kar sakta, to jab hum Penguin ko Bird ki jagah use karte hain, system break ho jata hai.
//  LSP violated ❌


// Good Example (Follows LSP):

abstract class Bird {
    abstract public function move();
}

class Sparrow extends Bird {
    public function move() {
        return "Flying in the sky";
    }
}

class Penguin extends Bird {
    public function move() {
        return "Swimming in the water";
    }
}

// Ab dono subclasses (Sparrow, Penguin) apni movement define karte hain.
// System ab safe aur consistent hai

// Simple Words Mein:

// Child class parent ke behavior ko todni nahi chahiye,
// balki use karne walay code ke liye same tarah se kaam karni chahiye.


// ❌ LSP Violation

// Maan lo tumhari application mein payment system hai:

class PaymentMethod
{
    public function pay(float $amount)
    {
        return "Payment Successful";
    }
}

class CashOnDelivery extends PaymentMethod
{
    public function pay(float $amount)
    {
        throw new Exception("Cash on Delivery online pay nahi kar sakta");
    }
}

// Use:

function processPayment(PaymentMethod $payment)
{
    echo $payment->pay(1000);
}

processPayment(new CashOnDelivery());
// Problem

// processPayment() expect karta hai ke har PaymentMethod pay karega.

// Lekin CashOnDelivery exception throw kar raha hai.

// Yani child class parent ke contract ko break kar rahi hai.

// LSP Violation ❌

// ✅ Correct Design
interface OrderMethod
{
    public function placeOrder();
}

class CreditCardPayment implements OrderMethod
{
    public function placeOrder()
    {
        return "Order placed and paid online";
    }
}

class CashOnDelivery implements OrderMethod
{
    public function placeOrder()
    {
        return "Order placed with cash on delivery";
    }
}

// Use:

function checkout(OrderMethod $method)
{
    echo $method->placeOrder();
}

// Ab dono classes safely use ho sakti hain.

// LSP Followed ✅

// Laravel Real Example

// Maan lo tum repository pattern use kar rahe ho:

interface UserRepositoryInterface
{
    public function find(int $id);
}
// Child 1
class DatabaseUserRepository implements UserRepositoryInterface
{
    public function find(int $id)
    {
        return User::find($id);
    }
}
// Child 2
class CacheUserRepository implements UserRepositoryInterface
{
    public function find(int $id)
    {
        return Cache::get("user_$id");
    }
}

// Service:

class UserService
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    public function getUser($id)
    {
        return $this->repository->find($id);
    }
}

// Ab chahe DatabaseUserRepository inject karo ya CacheUserRepository, service ka code nahi tootega.

// Yeh real Laravel LSP example hai. Child classes parent contract (UserRepositoryInterface) ko follow kar rahi hain aur parent ki jagah replace ho sakti hain.

// Yaad Rakhne Ki Trick

// LSP = Replaceability

Agar:

Parent $obj = new Child();

// aur system normally kaam kare, to LSP follow ho rahi hai.


Real Scenario: E-commerce Discount System
Faraz karain aap ek e-commerce app bana rahe hain jisme different discount types hain: Percentage Discount, Fixed Amount Discount, aur Free Shipping.
❌ Galat Tareeqa (LSP Violation)
php<?php

namespace App\Contracts;

interface Discount {
    public function apply(float $totalAmount): float;
}
php<?php

namespace App\Services\Discounts;

use App\Contracts\Discount;

class PercentageDiscount implements Discount {
    public function __construct(protected float $percentage) {}

    public function apply(float $totalAmount): float {
        return $totalAmount - ($totalAmount * $this->percentage / 100);
    }
}
php<?php

namespace App\Services\Discounts;

use App\Contracts\Discount;

class FixedAmountDiscount implements Discount {
    public function __construct(protected float $amount) {}

    public function apply(float $totalAmount): float {
        return $totalAmount - $this->amount;
    }
}
Ab yahan problem shuru hoti hai — Free Shipping Discount add karte waqt:
php<?php

namespace App\Services\Discounts;

use App\Contracts\Discount;

class FreeShippingDiscount implements Discount {
    public function apply(float $totalAmount): float {
        // ❌ Problem: Free shipping ka price se koi lena dena hi nahi!
        // Ye "apply" method ka concept yahan fit hi nahi hota
        throw new \Exception("Free shipping doesn't work on amount!");
    }
}
Ye LSP Violation Kyun Hai?
phpfunction applyDiscount(Discount $discount, float $total) {
    return $discount->apply($total);
}

applyDiscount(new PercentageDiscount(10), 1000); // ✅ 900
applyDiscount(new FixedAmountDiscount(100), 1000); // ✅ 900
applyDiscount(new FreeShippingDiscount(), 1000); // ❌ CRASH — Exception aa jayega
FreeShippingDiscount, Discount interface ki jagah safely use nahi ho sakti — kyunke uska nature hi alag hai (wo price discount nahi deti, shipping cost hataati hai). Ye LSP violation hai.
// ✅ Sahi Tareeqa (LSP Follow Karte Hue)
// Solution: Interface ko sahi tareeqe se design karein — jo cheez sab discounts mein common hai, wahi interface mein rakhein.
php<?php

namespace App\Contracts;

interface Discount {
    public function getDescription(): string;
    public function calculateDiscount(float $totalAmount): float; // Kitna discount milega (amount)
}
php<?php

namespace App\Services\Discounts;

use App\Contracts\Discount;

class PercentageDiscount implements Discount {
    public function __construct(protected float $percentage) {}

    public function getDescription(): string {
        return "{$this->percentage}% Off";
    }

    public function calculateDiscount(float $totalAmount): float {
        return $totalAmount * ($this->percentage / 100);
    }
}
php<?php

namespace App\Services\Discounts;

use App\Contracts\Discount;

class FixedAmountDiscount implements Discount {
    public function __construct(protected float $amount) {}

    public function getDescription(): string {
        return "Flat Rs. {$this->amount} Off";
    }

    public function calculateDiscount(float $totalAmount): float {
        // Agar discount amount se zyada hai to poora total hi discount ho jaye
        return min($this->amount, $totalAmount);
    }
}
// Ab FreeShipping ko alag interface do — kyunke uska nature different hai:
php<?php

namespace App\Contracts;

interface ShippingDiscount {
    public function getDescription(): string;
    public function isShippingFree(): bool;
}
php<?php

namespace App\Services\Discounts;

use App\Contracts\ShippingDiscount;

class FreeShippingDiscount implements ShippingDiscount {
    public function getDescription(): string {
        return "Free Shipping";
    }

    public function isShippingFree(): bool {
        return true;
    }
}
// Ab Use Karke Dekhein — Sab Safely Chalega
phpfunction applyPriceDiscount(Discount $discount, float $total): float {
    return $total - $discount->calculateDiscount($total);
}

applyPriceDiscount(new PercentageDiscount(10), 1000);   // ✅ 900
applyPriceDiscount(new FixedAmountDiscount(200), 1000); // ✅ 800

// FreeShipping alag jagah use hoga — koi crash nahi
function applyShippingDiscount(ShippingDiscount $discount): bool {
    return $discount->isShippingFree();
}

applyShippingDiscount(new FreeShippingDiscount()); // ✅ true
// Ab dono interfaces apni jagah bilkul theek kaam kar rahe hain — koi exception, koi crash nahi. Yehi LSP ka sahi implementation hai.
// Cart Mein Poora Real-World Use
php<?php

namespace App\Services;

use App\Contracts\Discount;
use App\Contracts\ShippingDiscount;

class CartService {
    protected float $subtotal = 1000;
    protected float $shippingCost = 150;

    public function checkout(?Discount $priceDiscount = null, ?ShippingDiscount $shippingDiscount = null): array {
        $finalTotal = $this->subtotal;
        $finalShipping = $this->shippingCost;

        if ($priceDiscount) {
            $finalTotal -= $priceDiscount->calculateDiscount($this->subtotal);
        }

        if ($shippingDiscount && $shippingDiscount->isShippingFree()) {
            $finalShipping = 0;
        }

        return [
            'subtotal' => $this->subtotal,
            'discount_applied' => $priceDiscount?->getDescription() ?? 'None',
            'final_total' => $finalTotal,
            'shipping' => $finalShipping,
        ];
    }
}
// Controller mein:
php<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\Discounts\PercentageDiscount;
use App\Services\Discounts\FreeShippingDiscount;

class CheckoutController extends Controller {
    public function checkout(CartService $cart) {
        $result = $cart->checkout(
            priceDiscount: new PercentageDiscount(15),
            shippingDiscount: new FreeShippingDiscount()
        );

        return response()->json($result);
    }
}
Output:
json{
    "subtotal": 1000,
    "discount_applied": "15% Off",
    "final_total": 850,
    "shipping": 0
}
// Ek Aur Real Example: Notification System (Bohat Common Laravel Projects Mein)
php<?php

namespace App\Contracts;

interface Notifiable {
    public function send(string $to, string $message): bool;
}
phpclass EmailNotification implements Notifiable {
    public function send(string $to, string $message): bool {
        // Mail::to($to)->send(...);
        return true;
    }
}

class SmsNotification implements Notifiable {
    public function send(string $to, string $message): bool {
        // Twilio ya kisi SMS API se bhejo
        return true;
    }
}
// LSP Violation yahan kaise ho sakti hai:
phpclass WhatsAppNotification implements Notifiable {
    public function send(string $to, string $message): bool {
        if (strlen($message) > 1000) {
            throw new \Exception("WhatsApp doesn't support long messages!"); // ❌
        }
        return true;
    }
}
// Agar WhatsAppNotification kabhi kabhi crash kar sakta hai jabke EmailNotification aur SmsNotification hamesha kaam karte hain, to ye halka LSP violation hai. Behtar tareeqa hoga message ko andar hi truncate/split kar dena, exception phenkne ki bajaye — taake behavior consistent rahe.
phpclass WhatsAppNotification implements Notifiable {
    public function send(string $to, string $message): bool {
        $message = substr($message, 0, 1000); // Handle gracefully, crash nahi
        // Send logic...
        return true;
    }
}
// Summary — Real Work Mein LSP Kaise Yaad Rakhein
// Sawal Apne Aap Se PoochainAgar "Haan" To LSP ViolationKya meri child class kabhi Exception phenkti hai jo parent nahi phenkti?✅ ViolationKya meri child class ka koi method "empty" ya "do nothing" hai sirf interface follow karne ke liye?✅ ViolationKya main interface ko is liye tor raha hoon kyunke ek class "fit nahi ho rahi"?✅ Ye batata hai interface design mein masla hai
// Ye Discount System wala example bilkul aapke PaymentMethod jaisa hi hai — bas ek level upar. Agar aap chahain to main aapko Payment System mein hi ek "Refund" feature add karke dikha sakta hoon jahan LSP ka role clearly nazar aaye — wo aur bhi practical hoga aapke liye.




// I – Interface Segregation Principle (ISP)
//  Definition:

// “No client should be forced to depend on methods it does not use.”

// Matlab:
// Interfaces ko chhota aur specific rakho —
// har class ko sirf wahi methods implement karne chahiye jo uske kaam ke hain.

//  Kyun use karte hain:

// Code clean aur flexible rehta hai

// Unnecessary methods implement karne ki majboori nahi hoti

// Classes focused aur reusable banti hain

// Bad Example (Violation of ISP):

interface Worker {
    public function work();
    public function eat();
}

class Robot implements Worker {
    public function work() {
        // Robot works
    }

    public function eat() {
        // ❌ Robots don't eat!
        throw new Exception("Robots don't eat!");
    }
}

// Problem:
// Robot ko eat() method implement karna force kiya gaya, jabke wo uske kaam ka nahi.
// Ye ISP violate karta hai.

// Good Example (Follows ISP):

interface Workable {
    public function work();
}

interface Eatable {
    public function eat();
}

class Human implements Workable, Eatable {
    public function work() {
        // Human works
    }

    public function eat() {
        // Human eats
    }
}

class Robot implements Workable {
    public function work() {
        // Robot works
    }
}

// Ab har class sirf wahi interface implement karti hai jo relevant hai 
// Koi bhi class unnecessary method implement nahi karti.

// Simple Words Mein:

// "Ek hi bada interface sab pe mat thopo —
// specific chhoti interfaces banao taake har class sirf apna kaam kare."


// D – Dependency Inversion Principle (DIP)
//  Definition:

// “High-level modules should not depend on low-level modules.
// Both should depend on abstractions.”

// Aur:

// “Abstractions should not depend on details.
// Details should depend on abstractions.”

//  Simple Words Mein:

// Apna code direct concrete classes (details) pe depend na kare,
// balki interfaces ya abstractions pe depend kare.

// Matlab:
// Agar kal aap dependency change karo (e.g., MySQL se MongoDB),
// to high-level code ko change na karna pade.

// ❌ Bad Example (Violation of DIP):

class MySQLDatabase {
    public function connect() {
        // Connect to MySQL
    }
}

class UserService {
    private $db;

    public function __construct() {
        $this->db = new MySQLDatabase(); // ❌ Direct dependency
    }

    public function getUser() {
        $this->db->connect();
        // Get user
    }
}


// Problem:
// Agar kal aapko PostgreSQL ya MongoDB use karna ho,
// to aapko UserService class change karni padegi.
// Ye tight coupling hai

// Good Example (Follows DIP):

interface DatabaseConnection {
    public function connect();
}

class MySQLDatabase implements DatabaseConnection {
    public function connect() {
        // Connect to MySQL
    }
}

class PostgreSQLDatabase implements DatabaseConnection {
    public function connect() {
        // Connect to PostgreSQL
    }
}

class UserService {
    private $db;

    public function __construct(DatabaseConnection $db) {
        $this->db = $db; // ✅ Depends on abstraction
    }

    public function getUser() {
        $this->db->connect();
        // Get user
    }
}


// Usage:

$service = new UserService(new MySQLDatabase());
$service->getUser();


// Ab agar aapko DB change karni ho:

$service = new UserService(new PostgreSQLDatabase());


// Aapko UserService class me koi change nahi karna padta 
// Yehi hai Dependency Inversion Principle.

// Short Summary of DIP:

// High-level code should depend on interfaces, not concrete classes.

// Ye code ko modular, testable, aur scalable banata hai.


// SOLID Principles Summary

// | No.   | Principle                           | Full Form                                                                 | Definition (Simple Words Mein)                       | Example Summary                                                                  |
// | ----- | ----------------------------------- | ------------------------------------------------------------------------- | ---------------------------------------------------- | -------------------------------------------------------------------------------- |
// | **S** | **Single Responsibility Principle** | A class should have **only one reason to change**                         | Har class sirf **ek kaam** kare                      | `InvoicePrinter` aur `InvoiceSaver` alag classes hon                             |
// | **O** | **Open/Closed Principle**           | Classes should be **open for extension**, but **closed for modification** | Naye features **add karo**, purana code **na badlo** | Naye discount rules add karne ke liye naye class banao, purani ko change na karo |
// | **L** | **Liskov Substitution Principle**   | Subclasses should be **replaceable** for their base class                 | Child class **base jesa hi behave** kare             | `Penguin` ko `Bird` ke jesa treat kar sakte ho bina error ke                     |
// | **I** | **Interface Segregation Principle** | Clients should not depend on **methods they don’t use**                   | **Chhoti specific interfaces** banao                 | `Worker` aur `Eater` alag interfaces hon, Robot sirf `Worker` use kare           |
// | **D** | **Dependency Inversion Principle**  | Depend on **abstractions**, not **concretions**                           | High-level code **interface** pe depend kare         | `UserService` → `DatabaseInterface`, na ke `MySQLDatabase`                       |


// Why SOLID is Important:
// Code becomes modular (easily changeable)

// Reusability and scalability increase

// Fewer bugs when you extend or refactor

// Professional-level clean architecture (senior-level interviews love this)




# Redis Setup & Laravel Integration Guide

## 1. Redis Install Karna (Ubuntu / AWS EC2)

```bash
sudo apt update
sudo apt install redis-server -y

# Service enable aur start karna
sudo systemctl enable redis-server
sudo systemctl start redis-server

# Status check karna
sudo systemctl status redis-server

# Test karna ke Redis chal raha hai
redis-cli ping
# Response: PONG
```

### Redis Config (Security)
Config file: `/etc/redis/redis.conf`

```bash
sudo nano /etc/redis/redis.conf
```

Yeh settings change/check karein:

```
requirepass YourStrongPassword   # password lagana
bind 127.0.0.1                   # sirf localhost se access (security)
```

Save karne ke baad restart karein:

```bash
sudo systemctl restart redis-server
```

---

## 2. Laravel Project Mein Connect Karna

```bash
composer require predis/predis
```

`.env` file mein add karein:

```env
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=YourStrongPassword
REDIS_PORT=6379

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

Config cache clear karein:

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 3. Laravel Code Examples

### Caching
```php
// Data store karna (60 minute ke liye)
Cache::put('key_name', 'value', 600);

// Data nikalna
$value = Cache::get('key_name');

// Agar exist nahi to query chala kar cache karo
$users = Cache::remember('all_users', 600, function () {
    return DB::table('users')->get();
});

// Cache delete
Cache::forget('key_name');
```

### Session (.env mein SESSION_DRIVER=redis set hone ke baad automatic kaam karega)

### Queue Example
```php
// Job dispatch karna
ProcessOrder::dispatch($order);
```

Queue worker chalana:
```bash
php artisan queue:work redis
```

---

## 4. Redis CLI - Important Commands

Redis CLI open karna:
```bash
redis-cli
# Agar password set hai to:
redis-cli -a YourStrongPassword
```

### General Commands
```bash
PING                     # Connection check
SET key value            # Value store karna
GET key                  # Value nikalna
DEL key                  # Key delete karna
EXISTS key                # Check karna key hai ya nahi
EXPIRE key seconds        # Key ka expiry time set karna
TTL key                   # Bacha hua time check karna
KEYS *                    # Sari keys dekhna (production mein avoid karein)
FLUSHALL                  # Sara data delete (DANGEROUS)
FLUSHDB                   # Current DB ka data delete
```

### String Commands
```bash
SET name "Ali"
GET name
INCR counter               # value +1
DECR counter                # value -1
APPEND name "Khan"          # string add karna
```

### List Commands
```bash
LPUSH mylist "item1"        # list ke start mein add
RPUSH mylist "item2"        # list ke end mein add
LRANGE mylist 0 -1           # poori list dekhna
LPOP mylist                  # start se remove
```

### Hash Commands (objects store karne ke liye)
```bash
HSET user:1 name "Ali" age "25"
HGET user:1 name
HGETALL user:1
HDEL user:1 age
```

### Set Commands
```bash
SADD myset "value1"
SMEMBERS myset
SREM myset "value1"
```

### Server/Monitoring Commands
```bash
INFO                       # Server info
DBSIZE                     # Total keys count
MONITOR                    # Real-time commands dekhna
CONFIG GET maxmemory       # Config check karna
```

---

## 5. AWS ElastiCache (Managed Option - Recommended for Production)

1. AWS Console → ElastiCache → Create Cluster
2. Engine: Redis select karein
3. Node type chunein (e.g. `cache.t3.micro` chote projects ke liye)
4. VPC wahi select karein jis mein EC2/Laravel app hai
5. Cluster create hone ke baad **endpoint** milega

`.env` mein endpoint use karein:
```env
REDIS_HOST=your-cluster-endpoint.cache.amazonaws.com
```

ElastiCache mein AWS khud backups, failover aur maintenance handle karta hai.

---

## Quick Summary
| Kaam | Command/Setting |
|------|------------------|
| Redis install | `sudo apt install redis-server` |
| Service start | `sudo systemctl start redis-server` |
| Test connection | `redis-cli ping` |
| Laravel package | `composer require predis/predis` |
| Cache driver | `CACHE_DRIVER=redis` |
| Session driver | `SESSION_DRIVER=redis` |
| Queue driver | `QUEUE_CONNECTION=redis` |