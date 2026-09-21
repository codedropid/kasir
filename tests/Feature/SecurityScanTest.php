<?php

namespace Tests\Feature;

use App\Livewire\Login;
use App\Livewire\PosComponent;
use App\Livewire\ProductManager;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SecurityScanTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $kasir;
    protected Category $category;
    protected Product $product;
    protected PaymentMethod $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Security Test',
            'email' => 'admin@kafe.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->kasir = User::create([
            'name' => 'Kasir Security Test',
            'email' => 'kasir@kafe.com',
            'password' => bcrypt('password'),
            'role' => 'kasir',
        ]);

        $this->category = Category::create([
            'name' => 'Kopi',
            'slug' => 'kopi',
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Espresso Single',
            'sku' => 'ESP-001',
            'price' => 20000,
            'cost_price' => 8000,
            'is_available' => true,
        ]);

        $this->paymentMethod = PaymentMethod::create([
            'name' => 'Tunai',
            'is_active' => true,
        ]);
    }

    public function test_login_brute_force_rate_limiting(): void
    {
        for ($i = 0; $i < 5; $i++) {
            Livewire::test(Login::class)
                ->set('email', 'attacker@kafe.com')
                ->set('password', 'wrong-pass')
                ->call('login')
                ->assertHasErrors(['email']);
        }

        // 6th attempt should be blocked by RateLimiter
        Livewire::test(Login::class)
            ->set('email', 'attacker@kafe.com')
            ->set('password', 'wrong-pass')
            ->call('login')
            ->assertSee('Terlalu banyak percobaan login gagal');
    }

    public function test_sql_injection_payload_in_pos_search(): void
    {
        $this->actingAs($this->kasir);

        // Standard SQL Injection payloads
        $payloads = [
            "' OR '1'='1",
            "'; DROP TABLE products; --",
            "' UNION SELECT null, null, null, null, null, null, null, null, null --",
            "1' AND 1=1 --",
        ];

        foreach ($payloads as $payload) {
            Livewire::test(PosComponent::class)
                ->set('search', $payload)
                ->assertStatus(200);
        }

        // Verify products table still intact and unchanged
        $this->assertDatabaseHas('products', ['sku' => 'ESP-001']);
    }

    public function test_xss_injection_in_pos_inputs_is_sanitized(): void
    {
        $this->actingAs($this->kasir);

        $xssPayload = '<script>alert("XSS Attack")</script><img src=x onerror=alert(1)>John Doe';

        Livewire::test(PosComponent::class)
            ->call('addToCart', $this->product->id)
            ->set('customerName', $xssPayload)
            ->set('tableNumber', '<b>05</b>')
            ->call('updateNotes', $this->product->id, '<script>alert("note")</script>Extra sugar')
            ->set('selectedPaymentMethodId', $this->paymentMethod->id)
            ->set('paidAmount', 50000)
            ->call('checkout')
            ->assertHasNoErrors();

        // Ensure database stored sanitized content without script tags
        $this->assertDatabaseMissing('orders', [
            'customer_name' => $xssPayload,
        ]);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'John Doe',
            'table_number' => '05',
        ]);

        $this->assertDatabaseHas('order_items', [
            'notes' => 'Extra sugar',
        ]);
    }

    public function test_cart_price_tampering_is_prevented(): void
    {
        $this->actingAs($this->kasir);

        // Attempting to modify product price in cart to Rp 100 via client manipulation
        $component = Livewire::test(PosComponent::class)
            ->call('addToCart', $this->product->id);

        $cart = $component->get('cart');
        $cart[$this->product->id]['price'] = 100; // Tampered price
        $cart[$this->product->id]['subtotal'] = 100;

        $component->set('cart', $cart)
            ->set('selectedPaymentMethodId', $this->paymentMethod->id)
            ->set('paidAmount', 50000)
            ->call('checkout');

        // Verify server verified real price from DB: 20000 + 10% tax = 22000
        $this->assertDatabaseHas('orders', [
            'total_amount' => 20000,
            'final_amount' => 22000,
        ]);
    }

    public function test_xss_in_product_manager_is_sanitized_and_validated(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ProductManager::class)
            ->set('name', '<script>alert("Hacked")</script>Cold Brew')
            ->set('sku', 'CB-001')
            ->set('category_id', $this->category->id)
            ->set('price', 25000)
            ->set('cost_price', 10000)
            ->set('image', 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38')
            ->call('saveProduct')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'name' => 'Cold Brew',
            'sku' => 'CB-001',
        ]);

        // Malicious javascript: URL payload should fail URL validation
        Livewire::test(ProductManager::class)
            ->set('name', 'Americano')
            ->set('sku', 'AMC-001')
            ->set('category_id', $this->category->id)
            ->set('price', 18000)
            ->set('image', 'javascript:alert("XSS")')
            ->call('saveProduct')
            ->assertHasErrors(['image']);
    }

    public function test_kasir_cannot_invoke_product_manager_mutations(): void
    {
        $this->actingAs($this->kasir);

        Livewire::test(ProductManager::class)
            ->set('name', 'Menu Ilegal')
            ->set('sku', 'ILE-001')
            ->set('category_id', $this->category->id)
            ->set('price', 50000)
            ->call('saveProduct')
            ->assertStatus(403);
    }

    public function test_http_security_headers_are_present(): void
    {
        $response = $this->get('/login');

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_cannot_delete_product_with_order_history_preserving_audit_trail(): void
    {
        // Perform a checkout first so the product has order items
        Livewire::actingAs($this->kasir)
            ->test(PosComponent::class)
            ->call('addToCart', $this->product->id)
            ->set('selectedPaymentMethodId', $this->paymentMethod->id)
            ->set('paidAmount', 50000)
            ->call('checkout')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('order_items', [
            'product_id' => $this->product->id,
        ]);

        // Attempt to delete product as admin
        Livewire::actingAs($this->admin)
            ->test(ProductManager::class)
            ->call('deleteProduct', $this->product->id)
            ->assertSee('tidak dapat dihapus');

        // Product and historical order item must remain intact
        $this->assertDatabaseHas('products', ['id' => $this->product->id]);
        $this->assertDatabaseHas('order_items', ['product_id' => $this->product->id]);
    }

    public function test_cannot_delete_category_containing_products(): void
    {
        // Category has $this->product inside it
        Livewire::actingAs($this->admin)
            ->test(ProductManager::class)
            ->call('deleteCategory', $this->category->id)
            ->assertSee('tidak dapat dihapus');

        $this->assertDatabaseHas('categories', ['id' => $this->category->id]);
    }

    public function test_locked_properties_tax_rate_and_latest_order_id_cannot_be_tampered(): void
    {
        $this->expectException(\Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException::class);

        Livewire::actingAs($this->kasir)
            ->test(PosComponent::class)
            ->set('taxRate', 0.0);
    }

    public function test_order_number_is_unique_and_collision_safe(): void
    {
        $this->actingAs($this->kasir);

        $orderNumbers = [];

        for ($i = 0; $i < 3; $i++) {
            Livewire::test(PosComponent::class)
                ->call('addToCart', $this->product->id)
                ->set('selectedPaymentMethodId', $this->paymentMethod->id)
                ->set('paidAmount', 50000)
                ->call('checkout')
                ->assertHasNoErrors();
        }

        $orders = \App\Models\Order::all();
        $this->assertCount(3, $orders);

        $orderNumbers = $orders->pluck('order_number')->toArray();
        $this->assertCount(3, array_unique($orderNumbers));
        foreach ($orderNumbers as $num) {
            $this->assertStringStartsWith('TRX-', $num);
        }
    }

    public function test_checkout_rate_limiter_protects_against_spamming(): void
    {
        $this->actingAs($this->kasir);
        $throttleKey = 'pos-checkout:' . $this->kasir->id;

        // Simulate 30 hits
        for ($i = 0; $i < 30; $i++) {
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);
        }

        // 31st checkout attempt should be blocked
        Livewire::actingAs($this->kasir)
            ->test(PosComponent::class)
            ->call('addToCart', $this->product->id)
            ->set('selectedPaymentMethodId', $this->paymentMethod->id)
            ->set('paidAmount', 50000)
            ->call('checkout')
            ->assertHasErrors(['checkout'])
            ->assertSee('Terlalu banyak permintaan transaksi');
    }
}
