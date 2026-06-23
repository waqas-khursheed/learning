<?php

/**
 * ============================================================================
 *              DESIGN PATTERNS IN LARAVEL — INTERVIEW Q&A
 *                    (6 Years Experience Level)
 * ============================================================================
 */


// =============================================================================
// S1: Laravel mein kaunse design patterns use hote hain?
// =============================================================================

/*
 * J: Laravel mein yeh design patterns built-in hain:
 *
 *   1. Repository Pattern     → Database logic alag karna
 *   2. Service Pattern        → Business logic alag karna
 *   3. Observer Pattern       → Model events handle karna
 *   4. Strategy Pattern       → Runtime par algorithm badalna
 *   5. Factory Pattern        → Objects banana (Eloquent Factories)
 *   6. Singleton Pattern      → Ek hi instance (Service Container)
 *   7. Facade Pattern         → Static-like interface for services
 *   8. Builder Pattern        → Query Builder, Eloquent queries
 *   9. Decorator Pattern      → Middleware pipeline
 *  10. Provider Pattern       → Service Providers
 *
 *   Interview mein sab se zyada poochhe jaate hain:
 *   Repository, Service Layer, aur Strategy Pattern
 */


// =============================================================================
// S2: Repository Pattern kya hai? Laravel mein kaise implement karein?
// =============================================================================

/*
 * J: Repository Pattern database logic ko controller se ALAG karta hai.
 *    Controller ko pata nahi hota data kahan se aa raha — MySQL, API, ya cache.
 */

// Step 1: Interface
interface UserRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?User;
    public function create(array $data): User;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findByEmail(string $email): ?User;
}

// Step 2: Implementation
class EloquentUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private User $model
    ) {}

    public function all(): Collection
    {
        return $this->model->with('profile')->get();
    }

    public function find(int $id): ?User
    {
        return $this->model->find($id);
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->model->where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return $this->model->destroy($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }
}

// Step 3: Bind karo
// AppServiceProvider:
$this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);

// Step 4: Controller mein use karo:
class UserController extends Controller
{
    public function __construct(
        private UserRepositoryInterface $users
    ) {}

    public function index()
    {
        return $this->users->all();
    }

    public function show(int $id)
    {
        return $this->users->find($id) ?? abort(404);
    }
}

/*
 * FAYDA:
 *   - Kal MySQL se MongoDB par shift karna ho → sirf naya Repository banayen
 *   - Testing mein mock Repository inject karo
 *   - Controller saaf aur simple rehta hai
 *
 * ⚠️ SENIOR LEVEL NUQTA:
 *   Chhoti apps mein Repository OVERKILL ho sakta hai.
 *   Service Layer kaafi hota hai zyada-tar cases mein.
 */


// =============================================================================
// S3: Service Layer Pattern kya hai?
// =============================================================================

/*
 * J: Business logic ko controller se ALAG karo — Service class mein rakhho.
 *    Controller sirf request/response handle kare.
 *    Yeh SABSE ZYADA use hone wala pattern hai senior level par.
 */

class OrderService
{
    public function __construct(
        private PaymentGatewayInterface $payment,
        private InventoryService $inventory,
        private NotificationService $notification
    ) {}

    public function placeOrder(User $user, array $items): Order
    {
        // 1. Inventory check
        $this->inventory->checkAvailability($items);

        // 2. Total calculate
        $total = collect($items)->sum(fn ($item) => $item['price'] * $item['quantity']);

        // 3. Payment process
        $paymentResult = $this->payment->charge($total);

        if (!$paymentResult->successful) {
            throw new PaymentFailedException($paymentResult->error);
        }

        // 4. Order create
        $order = $user->orders()->create([
            'total' => $total,
            'payment_id' => $paymentResult->transaction_id,
            'status' => 'confirmed',
        ]);

        // 5. Order items save
        foreach ($items as $item) {
            $order->items()->create($item);
        }

        // 6. Inventory update
        $this->inventory->decrementStock($items);

        // 7. Notification
        $this->notification->sendOrderConfirmation($user, $order);

        return $order;
    }
}

// Controller SIMPLE rehta hai:
class OrderController extends Controller
{
    public function store(StoreOrderRequest $request, OrderService $service)
    {
        $order = $service->placeOrder(
            auth()->user(),
            $request->validated()['items']
        );

        return response()->json($order, 201);
    }
}


// =============================================================================
// S4: Strategy Pattern kya hai? Example do.
// =============================================================================

/*
 * J: Runtime par algorithm/behavior badalna — interface ke through.
 *    Laravel mein yeh BOHOT use hota hai (mail drivers, cache drivers, etc.)
 */

// Interface define karo:
interface ShippingCalculatorInterface
{
    public function calculate(Order $order): float;
}

// Mukhtalif strategies:
class StandardShipping implements ShippingCalculatorInterface
{
    public function calculate(Order $order): float
    {
        return $order->weight * 10; // Rs 10 per kg
    }
}

class ExpressShipping implements ShippingCalculatorInterface
{
    public function calculate(Order $order): float
    {
        return $order->weight * 25; // Rs 25 per kg
    }
}

class FreeShipping implements ShippingCalculatorInterface
{
    public function calculate(Order $order): float
    {
        return 0;
    }
}

// Factory se sahi strategy do:
class ShippingFactory
{
    public static function make(string $type): ShippingCalculatorInterface
    {
        return match ($type) {
            'standard' => new StandardShipping(),
            'express'  => new ExpressShipping(),
            'free'     => new FreeShipping(),
            default    => throw new InvalidArgumentException("Unknown shipping: {$type}"),
        };
    }
}

// Controller mein:
$calculator = ShippingFactory::make($request->shipping_type);
$shippingCost = $calculator->calculate($order);


// =============================================================================
// S5: DTO (Data Transfer Object) Pattern kya hai?
// =============================================================================

/*
 * J: DTO ek simple class hai jo data carry karti hai — koi logic nahi.
 *    Arrays ki jagah DTOs use karo — type-safe aur clear.
 */

// ❌ Array se data pass karna (kya keys hain pata nahi):
$service->createUser(['name' => 'Ali', 'email' => 'ali@test.com', 'age' => 30]);

// ✅ DTO se data pass karna (clear hai kya chahiye):
class CreateUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly int $age,
        public readonly ?string $phone = null,
    ) {}

    public static function fromRequest(StoreUserRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            email: $request->validated('email'),
            age: $request->validated('age'),
            phone: $request->validated('phone'),
        );
    }
}

// Service mein:
class UserService
{
    public function create(CreateUserDTO $dto): User
    {
        return User::create([
            'name'  => $dto->name,
            'email' => $dto->email,
            'age'   => $dto->age,
            'phone' => $dto->phone,
        ]);
    }
}

// Controller mein:
$dto = CreateUserDTO::fromRequest($request);
$user = $this->userService->create($dto);


// =============================================================================
// S6: Action Pattern kya hai? (Single Action Controllers)
// =============================================================================

/*
 * J: Ek controller mein sirf EK action (method). __invoke use karo.
 *    Jab controller mein sirf ek kaam ho.
 */

class GenerateInvoiceAction
{
    public function __construct(
        private InvoiceService $invoices,
        private PdfGenerator $pdf
    ) {}

    public function __invoke(Order $order): string
    {
        $invoice = $this->invoices->generateForOrder($order);
        return $this->pdf->generate('invoices.template', $invoice);
    }
}

// Route:
Route::post('/orders/{order}/invoice', GenerateInvoiceAction::class);
