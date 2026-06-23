<?php

/**
 * ============================================================================
 *          SERVICE CONTAINER & DEPENDENCY INJECTION — INTERVIEW Q&A
 *                        (6 Years Experience Level)
 * ============================================================================
 */


// =============================================================================
// S1: Service Container kya hai? Aasan alfaazon mein samjhao.
// =============================================================================

/*
 * J: Service Container Laravel ka DIL hai — yeh ek DABBA hai jis mein
 *    application ki saari classes/services rakhhi hain.
 *
 *   Jab aap ko koi class chahiye, Container se maango — wo bana ke dega.
 *
 *   MISAAL:
 *   Aap restaurant mein ho:
 *   - Aap bolte ho "Biryani chahiye" (class maangi)
 *   - Kitchen (Container) biryani banata hai (object create karta hai)
 *   - Ingredients khud dhundta hai (dependencies resolve karta hai)
 *   - Aap ko tayar biryani milti hai (object milta hai)
 *
 *   Aap ko pata nahi hota kitchen mein kya hua — sirf result milta hai.
 *   ISI ko "Inversion of Control (IoC)" kehte hain.
 */


// =============================================================================
// S2: Dependency Injection (DI) kya hai? Kitni qismain hain?
// =============================================================================

/*
 * J: DI ka matlab hai — apni zarooraten (dependencies) bahar se receive karo,
 *    andar se khud mat banao.
 *
 *   ❌ GHALAT TAREEQA (Tightly Coupled):
 */

class OrderServiceBad
{
    public function createOrder(array $data): void
    {
        $payment = new StripePayment();    // ❌ Andar se bana raha — mushkil to test
        $mailer = new SendGridMailer();     // ❌ Andar se bana raha
        $payment->charge($data['amount']);
        $mailer->send($data['email'], 'Order Confirmed');
    }
}

/*
 *   ✅ SAHI TAREEQA (Dependency Injection):
 */

class OrderServiceGood
{
    public function __construct(
        private PaymentGatewayInterface $payment,  // ✅ Bahar se aaya
        private MailerInterface $mailer             // ✅ Bahar se aaya
    ) {}

    public function createOrder(array $data): void
    {
        $this->payment->charge($data['amount']);
        $this->mailer->send($data['email'], 'Order Confirmed');
    }
}

/*
 * DI KI 3 QISMAIN:
 *
 *   1) CONSTRUCTOR INJECTION (Sab se aam — TAVSIYA):
 *      - Constructor mein dependencies pass karo
 *      - Class banne ke waqt mil jaati hain
 */

class UserController extends Controller
{
    public function __construct(
        private UserService $userService  // Constructor Injection
    ) {}
}

/*
 *   2) METHOD INJECTION:
 *      - Method ke parameter mein pass karo
 *      - Route actions mein Laravel khud resolve karta hai
 */

class PostController extends Controller
{
    public function store(Request $request, PostService $service)  // Method Injection
    {
        $service->create($request->validated());
    }
}

/*
 *   3) SETTER INJECTION (Kam use hota hai):
 *      - Setter method se dependency set karo
 */

class ReportService
{
    private ExporterInterface $exporter;

    public function setExporter(ExporterInterface $exporter): void  // Setter
    {
        $this->exporter = $exporter;
    }
}


// =============================================================================
// S3: bind(), singleton(), aur instance() mein kya farq hai?
// =============================================================================

/*
 * J:
 *
 *   bind() → Har baar NAYI object banata hai
 *   singleton() → Sirf EK BAAR banata hai, phir wohi deta hai
 *   instance() → Pehle se bana hua object container mein rakh do
 */

// bind — har baar nayi object
$this->app->bind(PaymentGateway::class, function ($app) {
    return new StripePayment(config('services.stripe.key'));
});
// resolve karo → nayi object
// dubara resolve karo → NAYI object (doosri)

// singleton — sirf ek baar
$this->app->singleton(CartService::class, function ($app) {
    return new CartService($app->make(SessionManager::class));
});
// resolve karo → object banaya
// dubara resolve karo → WOHI object (same instance)

// instance — pehle se bani hui object
$apiClient = new ApiClient('https://api.example.com');
$this->app->instance(ApiClient::class, $apiClient);
// resolve karo → WOHI $apiClient milega

/*
 * KAB KYA USE KAREIN:
 *
 *   bind()     → Jab har jagah nayi instance chahiye (stateless services)
 *   singleton() → Jab ek hi object share karna ho (DB connection, cache, config)
 *   instance() → Jab testing mein mock object daalna ho
 *
 * SCOPED:
 *   $this->app->scoped(CartService::class, fn() => new CartService());
 *   → Ek request mein singleton, magar aglay request mein nayi object
 *   → Queue workers aur Octane ke liye zaroori
 */


// =============================================================================
// S4: Interface ko Implementation se kaise bind karein?
// =============================================================================

/*
 * J: Yeh SENIOR level ka sawaal hai — aap ko pata hona chahiye.
 */

// Step 1: Interface banayen
interface PaymentGatewayInterface
{
    public function charge(float $amount): bool;
    public function refund(string $transactionId): bool;
}

// Step 2: Implementation banayen
class StripePayment implements PaymentGatewayInterface
{
    public function charge(float $amount): bool
    {
        // Stripe API call
        return true;
    }

    public function refund(string $transactionId): bool
    {
        // Stripe refund
        return true;
    }
}

class JazzCashPayment implements PaymentGatewayInterface
{
    public function charge(float $amount): bool
    {
        // JazzCash API call
        return true;
    }

    public function refund(string $transactionId): bool
    {
        // JazzCash refund
        return true;
    }
}

// Step 3: Service Provider mein bind karo
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            PaymentGatewayInterface::class,
            StripePayment::class               // Interface → Concrete class
        );
    }
}

// Step 4: Kahin bhi interface type-hint karo — Laravel khud resolve karega
class CheckoutController extends Controller
{
    public function __construct(
        private PaymentGatewayInterface $payment  // Stripe milega automatically!
    ) {}

    public function processPayment(Request $request)
    {
        $this->payment->charge($request->amount);
    }
}

/*
 * FAYDA: Kal JazzCash par switch karna ho toh SIRF ServiceProvider mein
 *        ek line badlo — baqi POORA code same rahega!
 *
 * CONTEXTUAL BINDING (Mukhtalif jagah mukhtalif implementation):
 */

// Checkout mein Stripe, Subscription mein JazzCash
$this->app->when(CheckoutController::class)
    ->needs(PaymentGatewayInterface::class)
    ->give(StripePayment::class);

$this->app->when(SubscriptionController::class)
    ->needs(PaymentGatewayInterface::class)
    ->give(JazzCashPayment::class);


// =============================================================================
// S5: Auto-Resolution (Automatic Dependency Injection) kya hai?
// =============================================================================

/*
 * J: Laravel KHUD dependencies resolve karta hai — aap ko bind karne ki
 *    zaroorat NAHI (agar concrete class hai, interface nahi).
 *
 *   Kaise kaam karta hai:
 *   1. Laravel constructor ke type-hints dekhta hai
 *   2. PHP Reflection API se class ki dependencies pata karta hai
 *   3. Recursively sab dependencies banata hai
 *   4. Object return karta hai
 */

class UserRepository
{
    // Koi dependency nahi
}

class UserService
{
    public function __construct(
        private UserRepository $repo  // Laravel khud banayega
    ) {}
}

class UserController extends Controller
{
    public function __construct(
        private UserService $service  // Laravel khud UserService banayega
                                      // UserService ke liye UserRepository bhi banayega
    ) {}
}

// Aap ne KUCH bind nahi kiya — Laravel ne sab KHUD resolve kar diya!
// ⚠️ Yeh sirf CONCRETE classes ke liye kaam karta hai
// Interface ke liye BIND karna zaruri hai


// =============================================================================
// S6: Service Container mein tagged bindings kya hain?
// =============================================================================

/*
 * J: Kai implementations ko ek tag de do, phir tag se sab ek sath resolve karo.
 */

// Service Provider mein:
$this->app->bind('report.csv', CsvReporter::class);
$this->app->bind('report.pdf', PdfReporter::class);
$this->app->bind('report.excel', ExcelReporter::class);

$this->app->tag(
    ['report.csv', 'report.pdf', 'report.excel'],
    'reporters'
);

// Use karo:
class ReportAggregator
{
    public function __construct(
        #[Tagged('reporters')]
        private iterable $reporters  // Teeno reporters mil jayein gi
    ) {}

    public function generateAll(array $data): array
    {
        $results = [];
        foreach ($this->reporters as $reporter) {
            $results[] = $reporter->generate($data);
        }
        return $results;
    }
}
