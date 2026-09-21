<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Kasir POS - Kafe POS')]
class PosComponent extends Component
{
    // Search & Filter
    public string $search = '';
    public string $selectedCategory = 'all';

    // Cart & Order details
    public array $cart = [];
    public string $customerName = '';
    public string $tableNumber = '';
    public string $orderType = 'dine_in'; // dine_in, take_away
    public $discountAmount = 0;
    public $discountPercent = 0;

    #[Locked]
    public float $taxRate = 10; // 10% PB1

    // Payment state
    public bool $isPaymentModalOpen = false;
    public ?int $selectedPaymentMethodId = null;
    public $paidAmount = 0;

    // Post-checkout receipt modal
    public bool $isSuccessModalOpen = false;

    #[Locked]
    public ?int $latestOrderId = null;

    // Mobile Drawer state
    public bool $mobileCartOpen = false;

    public function mount(): void
    {
        $defaultPayment = PaymentMethod::where('is_active', true)->first();
        if ($defaultPayment) {
            $this->selectedPaymentMethodId = $defaultPayment->id;
        }
    }

    public function selectCategory(string $categorySlug): void
    {
        $this->selectedCategory = $categorySlug;
    }

    public function addToCart(int $productId): void
    {
        $product = Product::find($productId);
        if (!$product || !$product->is_available) {
            return;
        }

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['qty'] += 1;
            $this->cart[$productId]['subtotal'] = $this->cart[$productId]['qty'] * $this->cart[$productId]['price'];
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => (float) $product->price,
                'qty' => 1,
                'subtotal' => (float) $product->price,
                'notes' => '',
                'image' => $product->image,
            ];
        }
    }

    public function incrementQty(int $productId): void
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['qty'] += 1;
            $this->cart[$productId]['subtotal'] = $this->cart[$productId]['qty'] * $this->cart[$productId]['price'];
        }
    }

    public function decrementQty(int $productId): void
    {
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['qty'] > 1) {
                $this->cart[$productId]['qty'] -= 1;
                $this->cart[$productId]['subtotal'] = $this->cart[$productId]['qty'] * $this->cart[$productId]['price'];
            } else {
                $this->removeFromCart($productId);
            }
        }
    }

    public function removeFromCart(int $productId): void
    {
        unset($this->cart[$productId]);
    }

    protected function sanitizeInput(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        $clean = preg_replace('/<(script|style)\b[^>]*>(.*?)<\/\1>/is', '', $value);
        return trim(strip_tags($clean));
    }

    public function updateNotes(int $productId, string $notes): void
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['notes'] = mb_substr($this->sanitizeInput($notes), 0, 255);
        }
    }

    public function openMobileCart(): void
    {
        $this->mobileCartOpen = true;
    }

    public function closeMobileCart(): void
    {
        $this->mobileCartOpen = false;
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->customerName = '';
        $this->tableNumber = '';
        $this->orderType = 'dine_in';
        $this->discountAmount = 0;
        $this->discountPercent = 0;
        $this->mobileCartOpen = false;
    }

    public function getSubtotalProperty(): float
    {
        return (float) array_reduce($this->cart, function ($carry, $item) {
            return $carry + $item['subtotal'];
        }, 0.0);
    }

    public function getCalculatedDiscountProperty(): float
    {
        $percent = max(0, min(100, (float) ($this->discountPercent ?: 0)));
        if ($percent > 0) {
            return round(($this->subtotal * $percent) / 100, 2);
        }
        $amount = max(0, (float) ($this->discountAmount ?: 0));
        return min($amount, $this->subtotal);
    }

    public function getTaxAmountProperty(): float
    {
        $taxable = max(0, $this->subtotal - $this->calculatedDiscount);
        return round(($taxable * $this->taxRate) / 100, 2);
    }

    public function getFinalAmountProperty(): float
    {
        return max(0, $this->subtotal - $this->calculatedDiscount + $this->taxAmount);
    }

    public function getChangeAmountProperty(): float
    {
        $paid = (float) ($this->paidAmount ?: 0);
        return max(0, $paid - $this->finalAmount);
    }

    public function openPaymentModal(): void
    {
        if (empty($this->cart)) {
            return;
        }

        $this->paidAmount = $this->finalAmount;
        $this->isPaymentModalOpen = true;
    }

    public function closePaymentModal(): void
    {
        $this->isPaymentModalOpen = false;
    }

    public function setPaidAmount($amount): void
    {
        $this->paidAmount = max(0, (float) $amount);
    }

    public function addPaidAmount($amount): void
    {
        $this->paidAmount = max(0, ((float) ($this->paidAmount ?: 0)) + (float) $amount);
    }

    public function checkout(): void
    {
        if (!Auth::check()) {
            abort(401, 'Silakan login terlebih dahulu untuk memproses pesanan.');
        }

        if (empty($this->cart)) {
            $this->addError('checkout', 'Keranjang pesanan masih kosong.');
            return;
        }

        // Throttle checkouts to prevent accidental double-submission or flood abuse (max 30 / minute)
        $throttleKey = 'pos-checkout:' . (Auth::id() ?? request()->ip());
        if (RateLimiter::tooManyAttempts($throttleKey, 30)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('checkout', 'Terlalu banyak permintaan transaksi dalam waktu singkat. Harap tunggu ' . $seconds . ' detik.');
            return;
        }
        RateLimiter::hit($throttleKey, 60);

        // Validate and sanitize customer and table inputs
        $this->customerName = mb_substr($this->sanitizeInput($this->customerName), 0, 100);
        $this->tableNumber = mb_substr($this->sanitizeInput($this->tableNumber), 0, 20);

        if (!in_array($this->orderType, ['dine_in', 'take_away'], true)) {
            $this->orderType = 'dine_in';
        }

        // Validate active payment method
        $paymentMethod = PaymentMethod::where('id', $this->selectedPaymentMethodId)
            ->where('is_active', true)
            ->first();

        if (!$paymentMethod) {
            $this->addError('payment_method', 'Metode pembayaran yang dipilih tidak valid atau tidak aktif.');
            return;
        }

        $isCash = strtolower($paymentMethod->name) === 'tunai';
        $paid = (float) ($this->paidAmount ?: 0);

        if ($isCash && $paid < $this->finalAmount) {
            $this->addError('paidAmount', 'Jumlah pembayaran tunai kurang dari total tagihan.');
            return;
        }

        // Server-side Cart Integrity & Price Re-verification
        $verifiedItems = [];
        $verifiedSubtotal = 0.0;

        foreach ($this->cart as $item) {
            $product = Product::where('id', $item['id'])->where('is_available', true)->first();
            if (!$product) {
                $this->addError('checkout', 'Salah satu menu dalam keranjang tidak ditemukan atau sedang habis.');
                return;
            }

            $qty = min(9999, max(1, (int) $item['qty']));
            $unitPrice = (float) $product->price;
            $lineSubtotal = $qty * $unitPrice;
            $verifiedSubtotal += $lineSubtotal;

            $verifiedItems[] = [
                'product_id' => $product->id,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'subtotal' => $lineSubtotal,
                'notes' => isset($item['notes']) ? mb_substr($this->sanitizeInput($item['notes']), 0, 255) : null,
            ];
        }

        // Recalculate discount & final amount server-side
        $discountPercentVal = (float) ($this->discountPercent ?: 0);
        $discountAmountVal = (float) ($this->discountAmount ?: 0);
        $discountVal = 0.0;
        if ($discountPercentVal > 0) {
            $percent = max(0, min(100, $discountPercentVal));
            $discountVal = round(($verifiedSubtotal * $percent) / 100, 2);
        } else {
            $discountVal = min(max(0, $discountAmountVal), $verifiedSubtotal);
        }

        $taxable = max(0, $verifiedSubtotal - $discountVal);
        $taxVal = round(($taxable * $this->taxRate) / 100, 2);
        $finalVal = max(0, $verifiedSubtotal - $discountVal + $taxVal);
        $changeVal = $isCash ? max(0, $paid - $finalVal) : 0.0;

        // Concurrency-Safe Order Number: TRX-YYYYMMDD-XXXXX (immune to race-condition collisions)
        $todayPrefix = 'TRX-' . date('Ymd') . '-';
        $orderNumber = $todayPrefix . strtoupper(Str::random(5));

        DB::beginTransaction();
        try {
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => Auth::id(),
                'customer_name' => $this->customerName ?: null,
                'table_number' => ($this->orderType === 'dine_in') ? ($this->tableNumber ?: null) : null,
                'order_type' => $this->orderType,
                'total_amount' => $verifiedSubtotal,
                'tax_amount' => $taxVal,
                'discount_amount' => $discountVal,
                'final_amount' => $finalVal,
                'paid_amount' => $isCash ? $paid : $finalVal,
                'change_amount' => $changeVal,
                'payment_method_id' => $paymentMethod->id,
                'status' => 'completed',
            ]);

            foreach ($verifiedItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                    'notes' => $item['notes'],
                ]);
            }

            DB::commit();

            $this->latestOrderId = $order->id;
            $this->isPaymentModalOpen = false;
            $this->isSuccessModalOpen = true;

            // Clear current working cart
            $this->clearCart();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('POS Checkout failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->addError('checkout', 'Terjadi kendala teknis pada sistem saat memproses pesanan. Silakan coba kembali beberapa saat lagi.');
        }
    }

    public function closeSuccessModal(): void
    {
        $this->isSuccessModalOpen = false;
        $this->latestOrderId = null;
    }

    public function render()
    {
        $categories = Category::withCount('products')->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        $productsQuery = Product::with('category')->where('is_available', true);

        if ($this->selectedCategory !== 'all') {
            $productsQuery->whereHas('category', function ($q) {
                $q->where('slug', $this->selectedCategory);
            });
        }

        if (!empty($this->search)) {
            $escapedSearch = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], trim($this->search));
            $productsQuery->where(function ($q) use ($escapedSearch) {
                $q->where('name', 'like', '%' . $escapedSearch . '%')
                  ->orWhere('sku', 'like', '%' . $escapedSearch . '%');
            });
        }

        $products = $productsQuery->orderBy('name')->get();

        $latestOrder = $this->latestOrderId ? Order::with(['items.product', 'user', 'paymentMethod'])->find($this->latestOrderId) : null;

        return view('livewire.pos-component', [
            'categories' => $categories,
            'products' => $products,
            'paymentMethods' => $paymentMethods,
            'latestOrder' => $latestOrder,
        ]);
    }
}
