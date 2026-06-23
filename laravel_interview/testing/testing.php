<?php

/**
 * ============================================================================
 *                    TESTING — INTERVIEW Q&A
 *                  (6 Years Experience Level)
 * ============================================================================
 */


// =============================================================================
// S1: Testing ki qismain kya hain? Kaunsi kab use karein?
// =============================================================================

/*
 * J:
 *   1) UNIT TEST:
 *      - Ek chhoti class/method test karo ISOLATION mein
 *      - Database/HTTP nahi — sirf logic
 *      - Sab se tez chalte hain
 *      - Maslan: Service class ki calculation test karo
 *
 *   2) FEATURE TEST (Integration):
 *      - Poora flow test karo (route → controller → database → response)
 *      - HTTP request bhejo aur response check karo
 *      - Database use hota hai (in-memory SQLite ya transaction rollback)
 *      - Maslan: API endpoint test karo
 *
 *   3) BROWSER TEST (E2E — Dusk):
 *      - Real browser mein test (Chrome)
 *      - UI click, form fill, JavaScript
 *      - Sab se dheema magar sab se realistic
 *
 *   TESTING PYRAMID:
 *
 *       /\       ← Kam: Browser/E2E Tests
 *      /  \
 *     /────\     ← Darmiyan: Feature Tests
 *    / ──── \
 *   /────────\   ← Zyada: Unit Tests
 */


// =============================================================================
// S2: Feature Test ka example dikhao.
// =============================================================================

class OrderApiTest extends TestCase
{
    use RefreshDatabase; // Har test ke baad database saaf

    public function test_user_can_create_order(): void
    {
        // ARRANGE — tayyari
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 1000, 'stock' => 10]);

        // ACT — kaam karo
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', [
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 2],
                ],
            ]);

        // ASSERT — natija check karo
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'total', 'status', 'items'],
            ])
            ->assertJson([
                'data' => [
                    'total' => 2000,
                    'status' => 'confirmed',
                ],
            ]);

        // Database mein check karo
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total' => 2000,
        ]);

        // Stock kam hua?
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8, // 10 - 2
        ]);
    }

    public function test_unauthenticated_user_cannot_create_order(): void
    {
        $response = $this->postJson('/api/orders', [
            'items' => [['product_id' => 1, 'quantity' => 1]],
        ]);

        $response->assertStatus(401);
    }

    public function test_order_fails_with_insufficient_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 2]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', [
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 5], // 5 maangay, 2 hain
                ],
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.quantity']);
    }
}


// =============================================================================
// S3: Unit Test ka example dikhao.
// =============================================================================

class ShippingCalculatorTest extends TestCase
{
    public function test_standard_shipping_calculates_correctly(): void
    {
        $calculator = new StandardShippingCalculator();

        $order = new Order(['weight' => 5.0, 'total' => 5000]);

        $cost = $calculator->calculate($order);

        $this->assertEquals(50.0, $cost); // 5kg × Rs.10/kg
    }

    public function test_free_shipping_for_orders_above_threshold(): void
    {
        $calculator = new ConditionalShippingCalculator(freeAbove: 3000);

        $order = new Order(['weight' => 5.0, 'total' => 5000]);
        $this->assertEquals(0, $calculator->calculate($order));

        $cheapOrder = new Order(['weight' => 5.0, 'total' => 2000]);
        $this->assertGreaterThan(0, $calculator->calculate($cheapOrder));
    }
}


// =============================================================================
// S4: Mocking kya hai? Kab use karein?
// =============================================================================

/*
 * J: Mock = Nakli object jo asli object ki jagah kaam kare.
 *    External services (payment, email, API) ko test mein call NAHI karte
 *    — Mock se nakli jawab dete hain.
 */

class PaymentTest extends TestCase
{
    public function test_order_processes_payment_successfully(): void
    {
        // Payment Gateway ko MOCK karo — asli Stripe call nahi hogi
        $mockGateway = Mockery::mock(PaymentGatewayInterface::class);
        $mockGateway->shouldReceive('charge')
            ->once()
            ->with(5000)
            ->andReturn(new PaymentResult(
                successful: true,
                transaction_id: 'txn_123'
            ));

        $this->app->instance(PaymentGatewayInterface::class, $mockGateway);

        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'total' => 5000]);

        $service = app(OrderService::class);
        $result = $service->processPayment($order);

        $this->assertTrue($result);
        $this->assertEquals('paid', $order->fresh()->payment_status);
    }
}

// Laravel Facades Mock:
Mail::fake();   // Emails NAHI bhejega
Event::fake();  // Events fire NAHI honge
Queue::fake();  // Jobs queue mein NAHI jayein gi

// Test ke baad check karo:
Mail::assertSent(OrderConfirmationMail::class, function ($mail) use ($user) {
    return $mail->hasTo($user->email);
});

Queue::assertPushed(ProcessPayment::class, function ($job) use ($order) {
    return $job->order->id === $order->id;
});

Event::assertDispatched(OrderPlaced::class);


// =============================================================================
// S5: Factory kya hai? Kaise use karein?
// =============================================================================

// database/factories/UserFactory.php:
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'     => fake()->name(),
            'email'    => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'is_admin' => false,
        ];
    }

    // STATE — khaas haalat:
    public function admin(): static
    {
        return $this->state(['is_admin' => true]);
    }

    public function unverified(): static
    {
        return $this->state(['email_verified_at' => null]);
    }
}

// Istemal:
$user = User::factory()->create();                  // 1 user
$users = User::factory()->count(50)->create();      // 50 users
$admin = User::factory()->admin()->create();        // Admin user

// Relationships ke sath:
$user = User::factory()
    ->has(Post::factory()->count(5))        // 5 posts
    ->has(Order::factory()->count(3))       // 3 orders
    ->create();


// =============================================================================
// S6: Testing best practices kya hain?
// =============================================================================

/*
 * J:
 *   ✅ Har test INDEPENDENT hona chahiye (doosre test par depend na kare)
 *   ✅ AAA pattern follow karo: Arrange → Act → Assert
 *   ✅ RefreshDatabase trait use karo (clean slate har test mein)
 *   ✅ Factories use karo test data ke liye
 *   ✅ External services MOCK karo (payment, email, APIs)
 *   ✅ Edge cases test karo (empty input, wrong type, boundary values)
 *   ✅ Test names descriptive rakhho (test_user_cannot_delete_others_post)
 *
 *   ❌ Database state par depend mat karo (seeders se mat chalao)
 *   ❌ Tests ka order matter mat karne do
 *   ❌ Production APIs test mein call mat karo
 *   ❌ Sleep/delay test mein mat use karo
 *
 *   COMMANDS:
 *   php artisan test                    → Sab tests
 *   php artisan test --parallel         → Parallel mein (tez)
 *   php artisan test --filter=OrderTest → Sirf order tests
 *   php artisan test --coverage         → Code coverage report
 */
