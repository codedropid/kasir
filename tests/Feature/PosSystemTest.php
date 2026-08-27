<?php

namespace Tests\Feature;

use App\Livewire\PosComponent;
use App\Livewire\ProductManager;
use App\Models\Category;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PosSystemTest extends TestCase
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
            'name' => 'Admin Test',
            'email' => 'admin@kafe.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->kasir = User::create([
            'name' => 'Kasir Test',
            'email' => 'kasir@kafe.com',
            'password' => bcrypt('password'),
            'role' => 'kasir',
        ]);

        $this->paymentMethod = PaymentMethod::create([
            'name' => 'Tunai',
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Kopi',
            'slug' => 'kopi',
            'icon' => 'coffee',
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Espresso Test',
            'sku' => 'MNM-001',
            'price' => 20000,
            'cost_price' => 8000,
            'is_available' => true,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');

        $posResponse = $this->get('/pos');
        $posResponse->assertRedirect('/login');
    }

    public function test_kasir_can_access_pos(): void
    {
        $response = $this->actingAs($this->kasir)->get('/pos');
        $response->assertStatus(200);
    }

    public function test_kasir_cannot_access_admin_product_manager(): void
    {
        $response = $this->actingAs($this->kasir)->get('/products');
        $response->assertRedirect('/pos');
    }

    public function test_admin_can_access_products_and_reports(): void
    {
        $responseProducts = $this->actingAs($this->admin)->get('/products');
        $responseProducts->assertStatus(200);

        $responseReports = $this->actingAs($this->admin)->get('/reports');
        $responseReports->assertStatus(200);
    }

    public function test_pos_livewire_can_add_to_cart_and_checkout(): void
    {
        Livewire::actingAs($this->kasir)
            ->test(PosComponent::class)
            ->call('addToCart', $this->product->id)
            ->assertSet('cart.' . $this->product->id . '.qty', 1)
            ->set('paidAmount', 25000)
            ->call('checkout')
            ->assertSet('isSuccessModalOpen', true)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('orders', [
            'total_amount' => 20000,
            'final_amount' => 22000, // 20000 + 10% tax (2000)
            'paid_amount' => 25000,
            'change_amount' => 3000,
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 20000,
            'subtotal' => 20000,
        ]);
    }

    public function test_product_manager_can_create_product(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ProductManager::class)
            ->set('name', 'Latte Baru')
            ->set('sku', 'MNM-999')
            ->set('category_id', $this->category->id)
            ->set('price', 25000)
            ->set('cost_price', 10000)
            ->set('is_available', true)
            ->call('saveProduct')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'name' => 'Latte Baru',
            'sku' => 'MNM-999',
            'price' => 25000,
        ]);
    }
}