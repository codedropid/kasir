<div class="flex-1 flex flex-col lg:flex-row h-[calc(100vh-61px)] overflow-hidden bg-slate-950 relative" x-data="{ mobileCartOpen: @entangle('mobileCartOpen') }">
    
    <!-- LEFT SIDE: Catalog & Filter Section (Full width on mobile, 60% on desktop) -->
    <section class="flex-1 flex flex-col min-w-0 border-r border-slate-800/80 bg-slate-950 overflow-hidden h-full">
        
        <!-- Search and Category Filters -->
        <div class="p-3 sm:p-4 border-b border-slate-800/80 bg-slate-900/60 backdrop-blur space-y-2.5 sm:space-y-3">
            <div class="flex items-center gap-2.5 sm:gap-3">
                <!-- Search Box -->
                <div class="relative flex-1">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Cari menu (nama / SKU)..." 
                        class="w-full bg-slate-800/90 border border-slate-700/80 text-xs sm:text-sm text-slate-100 placeholder-slate-400 rounded-xl pl-10 pr-9 py-2 sm:py-2.5 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
                    
                    @if(!empty($search))
                        <button 
                            wire:click="$set('search', '')" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </button>
                    @endif
                </div>

                <!-- Total Count Badge (Desktop) -->
                <div class="hidden sm:flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-800/60 border border-slate-700/50 text-xs font-semibold text-slate-300 whitespace-nowrap">
                    <i data-lucide="layout-grid" class="w-3.5 h-3.5 text-amber-400"></i>
                    <span>{{ $products->count() }} Menu</span>
                </div>
            </div>

            <!-- Category Pills -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                <button 
                    wire:click="selectCategory('all')" 
                    class="px-3.5 sm:px-4 py-1.5 sm:py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all duration-150 flex items-center gap-1.5 sm:gap-2 {{ $selectedCategory === 'all' ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 shadow-md shadow-amber-500/20' : 'bg-slate-800/80 text-slate-300 hover:bg-slate-700/80 border border-slate-700/60' }}">
                    <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                    <span>Semua</span>
                </button>

                @foreach($categories as $cat)
                    <button 
                        wire:click="selectCategory('{{ $cat->slug }}')" 
                        class="px-3.5 sm:px-4 py-1.5 sm:py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all duration-150 flex items-center gap-1.5 sm:gap-2 {{ $selectedCategory === $cat->slug ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 shadow-md shadow-amber-500/20' : 'bg-slate-800/80 text-slate-300 hover:bg-slate-700/80 border border-slate-700/60' }}">
                        @if($cat->slug === 'makanan')
                            <i data-lucide="utensils" class="w-3.5 h-3.5"></i>
                        @elseif($cat->slug === 'minuman')
                            <i data-lucide="cup-soda" class="w-3.5 h-3.5"></i>
                        @else
                            <i data-lucide="cookie" class="w-3.5 h-3.5"></i>
                        @endif
                        <span>{{ $cat->name }}</span>
                        <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $selectedCategory === $cat->slug ? 'bg-slate-950/20 text-slate-950 font-black' : 'bg-slate-700/80 text-slate-400' }}">
                            {{ $cat->products_count }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Product Grid Catalog -->
        <div class="flex-1 overflow-y-auto p-3 sm:p-4 lg:p-6 {{ count($cart) > 0 ? 'pb-24 lg:pb-6' : '' }}">
            @if($products->isEmpty())
                <div class="h-full flex flex-col items-center justify-center text-center p-8 text-slate-500">
                    <div class="w-16 h-16 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center mb-3">
                        <i data-lucide="package-x" class="w-8 h-8 text-slate-600"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-300">Menu Tidak Ditemukan</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm">Coba kata kunci pencarian lain atau pilih kategori yang berbeda.</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 sm:gap-4">
                    @foreach($products as $product)
                        <div 
                            wire:click="addToCart({{ $product->id }})" 
                            class="group relative bg-slate-900/90 hover:bg-slate-850 border border-slate-800/90 hover:border-amber-500/50 rounded-2xl p-2.5 sm:p-3 flex flex-col justify-between cursor-pointer transition-all duration-200 hover:-translate-y-1 hover:shadow-xl hover:shadow-amber-500/5 select-none overflow-hidden {{ isset($cart[$product->id]) ? 'ring-2 ring-amber-500/80 border-transparent bg-slate-900' : '' }}">
                            
                            <!-- Cart Quantity Indicator Badge -->
                            @if(isset($cart[$product->id]))
                                <div class="absolute top-2.5 right-2.5 z-10 w-6 h-6 rounded-full bg-amber-500 text-slate-950 font-black text-xs flex items-center justify-center shadow-lg">
                                    {{ $cart[$product->id]['qty'] }}
                                </div>
                            @endif

                            <!-- Product Thumbnail -->
                            <div class="aspect-square w-full rounded-xl overflow-hidden bg-slate-800 mb-2.5 relative group-hover:opacity-95 transition-opacity">
                                @if($product->image)
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-600">
                                        <i data-lucide="coffee" class="w-8 h-8 sm:w-10 sm:h-10"></i>
                                    </div>
                                @endif
                                
                                <div class="absolute bottom-1 left-1 px-1.5 py-0.5 rounded-md bg-slate-950/80 backdrop-blur text-[9px] sm:text-[10px] font-mono text-slate-400 border border-slate-700/50">
                                    {{ $product->sku }}
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="flex-1 flex flex-col justify-between">
                                <div>
                                    <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-amber-400/90">
                                        {{ $product->category->name ?? 'Kafe' }}
                                    </span>
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-100 group-hover:text-amber-400 transition-colors line-clamp-2 leading-tight mt-0.5">
                                        {{ $product->name }}
                                    </h4>
                                </div>
                                <div class="mt-2 flex items-center justify-between pt-1.5 sm:pt-2 border-t border-slate-800/80">
                                    <span class="text-xs sm:text-sm font-extrabold text-amber-400">
                                        {{ $product->formatted_price }}
                                    </span>
                                    <span class="p-1 rounded-lg bg-slate-800 group-hover:bg-amber-500 group-hover:text-slate-950 text-slate-400 transition-colors">
                                        <i data-lucide="plus" class="w-3 h-3 sm:w-3.5 sm:h-3.5"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- MOBILE FLOATING BOTTOM ACTION BAR (Shown only on Mobile when cart has items) -->
    @if(count($cart) > 0)
        <div class="lg:hidden fixed bottom-3 left-3 right-3 z-40 animate-slide-up">
            <div class="bg-gradient-to-r from-slate-900 via-slate-900 to-slate-950 border border-amber-500/40 rounded-2xl p-3 shadow-2xl backdrop-blur-lg flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center font-black text-sm shadow-md">
                        <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="text-[11px] font-semibold text-slate-400 block">{{ count($cart) }} Menu dipilih</span>
                        <span class="text-sm font-black text-amber-400 font-mono">
                            Rp {{ number_format($this->finalAmount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <button 
                    type="button"
                    wire:click="openMobileCart"
                    class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-black text-xs uppercase tracking-wider flex items-center gap-1.5 shadow-lg shadow-amber-500/20 active:scale-95 transition-all">
                    <span>Lihat Pesanan</span>
                    <i data-lucide="chevron-up" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- RIGHT SIDE: Shopping Cart & Checkout Panel (Desktop Sidebar & Mobile Drawer) -->
    <aside 
        class="w-full lg:w-[400px] xl:w-[450px] flex flex-col bg-slate-900/95 border-l border-slate-800/80 shadow-2xl overflow-hidden
               {{ $mobileCartOpen ? 'fixed inset-0 z-50 flex' : 'hidden lg:flex' }}">
        
        <!-- Cart Header -->
        <div class="p-3.5 sm:p-4 border-b border-slate-800/80 bg-slate-900 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-100">Pesanan Aktif</h3>
                    <p class="text-[11px] text-slate-400">{{ count($cart) }} Menu dipilih</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if(count($cart) > 0)
                    <button 
                        wire:click="clearCart" 
                        wire:confirm="Yakin ingin mengosongkan keranjang belanja?"
                        class="text-xs font-semibold text-red-400 hover:text-red-300 hover:bg-red-500/10 px-2 py-1 sm:px-2.5 sm:py-1.5 rounded-lg border border-red-500/20 transition-all flex items-center gap-1">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        <span>Reset</span>
                    </button>
                @endif

                <!-- Close Button on Mobile Drawer -->
                <button 
                    type="button"
                    wire:click="closeMobileCart" 
                    class="lg:hidden p-1.5 rounded-lg bg-slate-800 text-slate-300 hover:text-white border border-slate-700">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

        <!-- Order Options: Dine-in / Take Away & Customer Details -->
        <div class="p-3 sm:p-3.5 border-b border-slate-800/80 bg-slate-950/40 space-y-2.5">
            <!-- Order Type Toggle -->
            <div class="grid grid-cols-2 gap-2 p-1 bg-slate-950 rounded-xl border border-slate-800">
                <button 
                    wire:click="$set('orderType', 'dine_in')" 
                    type="button" 
                    class="py-1.5 text-xs font-bold rounded-lg transition-all flex items-center justify-center gap-1.5 {{ $orderType === 'dine_in' ? 'bg-amber-500 text-slate-950 shadow' : 'text-slate-400 hover:text-slate-200' }}">
                    <i data-lucide="utensils-crossed" class="w-3.5 h-3.5"></i>
                    Dine In (Makan Sini)
                </button>
                <button 
                    wire:click="$set('orderType', 'take_away')" 
                    type="button" 
                    class="py-1.5 text-xs font-bold rounded-lg transition-all flex items-center justify-center gap-1.5 {{ $orderType === 'take_away' ? 'bg-amber-500 text-slate-950 shadow' : 'text-slate-400 hover:text-slate-200' }}">
                    <i data-lucide="baggage-claim" class="w-3.5 h-3.5"></i>
                    Take Away (Bungkus)
                </button>
            </div>

            <!-- Customer & Table Number Inputs -->
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <input 
                        type="text" 
                        wire:model="customerName" 
                        placeholder="Nama Pelanggan (opsional)" 
                        class="w-full bg-slate-950 border border-slate-800 text-xs text-slate-200 placeholder-slate-500 rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>
                @if($orderType === 'dine_in')
                    <div>
                        <input 
                            type="text" 
                            wire:model="tableNumber" 
                            placeholder="No. Meja (contoh: 05)" 
                            class="w-full bg-slate-950 border border-slate-800 text-xs text-slate-200 placeholder-slate-500 rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    </div>
                @else
                    <div class="flex items-center px-2.5 py-1.5 rounded-lg bg-slate-950/60 border border-slate-800/60 text-xs text-slate-500">
                        <i data-lucide="package-check" class="w-3.5 h-3.5 mr-1.5 text-amber-500/70"></i>
                        Kemasan Bungkus
                    </div>
                @endif
            </div>
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto p-3 sm:p-3.5 space-y-2.5">
            @if(empty($cart))
                <div class="h-full flex flex-col items-center justify-center text-center p-6 text-slate-500">
                    <div class="w-12 h-12 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-center mb-2.5">
                        <i data-lucide="shopping-cart" class="w-5 h-5 text-slate-600"></i>
                    </div>
                    <p class="text-xs font-semibold text-slate-400">Keranjang masih kosong</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Pilih menu dari katalog untuk memulai pesanan.</p>
                </div>
            @else
                @foreach($cart as $id => $item)
                    <div class="bg-slate-950/80 border border-slate-800/90 rounded-xl p-2.5 sm:p-3 flex flex-col gap-2 transition-all hover:border-slate-700">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1">
                                <h4 class="text-xs font-bold text-slate-200 leading-snug">{{ $item['name'] }}</h4>
                                <span class="text-[11px] font-medium text-slate-400">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                            </div>
                            <span class="text-xs font-extrabold text-amber-400 font-mono">
                                Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                            </span>
                        </div>

                        <!-- Notes & Quantity controls -->
                        <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-900">
                            <!-- Notes input -->
                            <div class="flex-1 relative">
                                <input 
                                    type="text" 
                                    value="{{ $item['notes'] }}" 
                                    wire:change="updateNotes({{ $id }}, $event.target.value)"
                                    placeholder="Catatan (Less sugar, dll)..." 
                                    class="w-full bg-slate-900 border border-slate-800 text-[11px] text-slate-300 placeholder-slate-600 rounded-md px-2 py-1 focus:outline-none focus:border-amber-500">
                            </div>

                            <!-- Qty Buttons -->
                            <div class="flex items-center bg-slate-900 border border-slate-800 rounded-lg p-0.5">
                                <button 
                                    wire:click="decrementQty({{ $id }})" 
                                    class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                                    <i data-lucide="minus" class="w-3 h-3"></i>
                                </button>
                                <span class="w-7 text-center text-xs font-bold text-slate-100 font-mono">{{ $item['qty'] }}</span>
                                <button 
                                    wire:click="incrementQty({{ $id }})" 
                                    class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                                    <i data-lucide="plus" class="w-3 h-3"></i>
                                </button>
                            </div>

                            <!-- Delete button -->
                            <button 
                                wire:click="removeFromCart({{ $id }})" 
                                class="text-slate-500 hover:text-red-400 p-1 transition-colors">
                                <i data-lucide="trash" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Cart Summary & Calculation -->
        <div class="p-3.5 sm:p-4 border-t border-slate-800/80 bg-slate-950/90 backdrop-blur space-y-2.5 sm:space-y-3">
            <div class="space-y-1.5 text-xs">
                <!-- Subtotal -->
                <div class="flex justify-between text-slate-400">
                    <span>Subtotal</span>
                    <span class="font-mono font-semibold text-slate-200">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                </div>

                <!-- Discount Input -->
                <div class="flex items-center justify-between text-slate-400">
                    <span class="flex items-center gap-1">
                        Diskon
                        <span class="text-[10px] text-slate-500">(Rp / %)</span>
                    </span>
                    <div class="flex items-center gap-1.5 w-36">
                        <input 
                            type="number" 
                            wire:model.live="discountPercent" 
                            min="0" 
                            max="100" 
                            placeholder="%" 
                            class="w-14 bg-slate-900 border border-slate-800 text-[11px] text-right text-slate-200 rounded px-1.5 py-0.5 focus:outline-none focus:border-amber-500 font-mono">
                        <span class="text-[10px] text-slate-500">%</span>
                        <input 
                            type="number" 
                            wire:model.live="discountAmount" 
                            min="0" 
                            placeholder="Nominal" 
                            class="w-20 bg-slate-900 border border-slate-800 text-[11px] text-right text-slate-200 rounded px-1.5 py-0.5 focus:outline-none focus:border-amber-500 font-mono">
                    </div>
                </div>
                @if($this->calculatedDiscount > 0)
                    <div class="flex justify-between text-emerald-400 text-xs">
                        <span>Potongan Diskon</span>
                        <span class="font-mono font-semibold">- Rp {{ number_format($this->calculatedDiscount, 0, ',', '.') }}</span>
                    </div>
                @endif

                <!-- Tax 10% PB1 -->
                <div class="flex justify-between text-slate-400">
                    <span>Pajak Restoran (PB1 {{ $taxRate }}%)</span>
                    <span class="font-mono font-semibold text-slate-200">Rp {{ number_format($this->taxAmount, 0, ',', '.') }}</span>
                </div>

                <!-- Grand Total -->
                <div class="flex justify-between items-baseline pt-2 border-t border-slate-800 text-slate-100">
                    <span class="text-xs sm:text-sm font-black">Total Pembayaran</span>
                    <span class="text-lg sm:text-xl font-black text-amber-400 font-mono tracking-tight">
                        Rp {{ number_format($this->finalAmount, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Action Buttons on Mobile & Desktop -->
            <div class="flex items-center gap-2">
                <button 
                    type="button"
                    wire:click="closeMobileCart" 
                    class="lg:hidden flex-1 py-3 rounded-xl font-bold text-xs bg-slate-800 hover:bg-slate-700 text-slate-200 transition-all text-center">
                    + Tambah Menu Lain
                </button>
                
                <button 
                    wire:click="openPaymentModal" 
                    @if(empty($cart)) disabled @endif
                    class="flex-1 lg:w-full py-3 rounded-xl font-black text-xs sm:text-sm uppercase tracking-wider flex items-center justify-center gap-1.5 sm:gap-2 transition-all duration-200 {{ empty($cart) ? 'bg-slate-800 text-slate-600 cursor-not-allowed' : 'bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-500 text-slate-950 hover:brightness-110 shadow-lg shadow-amber-500/25 active:scale-[0.98]' }}">
                    <i data-lucide="credit-card" class="w-4 h-4 font-bold"></i>
                    <span>Bayar Sekarang</span>
                </button>
            </div>
        </div>
    </aside>

    <!-- MODAL 1: Quick Payment Modal -->
    @if($isPaymentModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fade-in" x-cloak>
            <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                
                <!-- Modal Header -->
                <div class="p-5 border-b border-slate-800 flex items-center justify-between bg-slate-950/50">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                            <i data-lucide="wallet" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white">Pembayaran Pesanan</h3>
                            <p class="text-xs text-slate-400">Pilih metode bayar & masukkan nominal</p>
                        </div>
                    </div>
                    <button wire:click="closePaymentModal" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-5 overflow-y-auto space-y-4">
                    
                    <!-- Total Bill Banner -->
                    <div class="p-4 rounded-2xl bg-gradient-to-r from-slate-950 to-slate-900 border border-amber-500/30 flex items-center justify-between">
                        <div>
                            <span class="text-xs text-slate-400 font-medium">Total yang harus dibayar:</span>
                            <h2 class="text-2xl font-black text-amber-400 font-mono tracking-tight">
                                Rp {{ number_format($this->finalAmount, 0, ',', '.') }}
                            </h2>
                        </div>
                        <div class="text-right text-[11px] text-slate-400">
                            <p>{{ count($cart) }} Menu</p>
                            <p class="font-semibold uppercase text-amber-400">{{ $orderType === 'dine_in' ? 'Dine In' : 'Take Away' }}</p>
                        </div>
                    </div>

                    <!-- Payment Method Selector -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Metode Pembayaran</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @foreach($paymentMethods as $pm)
                                <button 
                                    type="button" 
                                    wire:click="$set('selectedPaymentMethodId', {{ $pm->id }})" 
                                    class="p-2.5 rounded-xl text-xs font-bold flex flex-col items-center gap-1.5 transition-all border {{ $selectedPaymentMethodId === $pm->id ? 'bg-amber-500 text-slate-950 border-amber-400 shadow-md shadow-amber-500/20' : 'bg-slate-950 text-slate-300 border-slate-800 hover:border-slate-700 hover:bg-slate-800/50' }}">
                                    @if(strtolower($pm->name) === 'tunai')
                                        <i data-lucide="banknote" class="w-4 h-4"></i>
                                    @elseif(strtolower($pm->name) === 'qris')
                                        <i data-lucide="qr-code" class="w-4 h-4"></i>
                                    @elseif(strtolower($pm->name) === 'kartu debit' || strtolower($pm->name) === 'debit')
                                        <i data-lucide="credit-card" class="w-4 h-4"></i>
                                    @else
                                        <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                                    @endif
                                    <span>{{ $pm->name }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @php
                        $currentPaymentMethod = $paymentMethods->firstWhere('id', $selectedPaymentMethodId);
                        $isCashSelected = $currentPaymentMethod && strtolower($currentPaymentMethod->name) === 'tunai';
                        $isQrisSelected = $currentPaymentMethod && strtolower($currentPaymentMethod->name) === 'qris';
                    @endphp

                    <!-- Cash Specific Inputs -->
                    @if($isCashSelected)
                        <div class="space-y-3 p-4 rounded-2xl bg-slate-950/70 border border-slate-800">
                            <div>
                                <label class="block text-xs font-bold text-slate-300 mb-1.5">Jumlah Uang Diterima (Rp)</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-amber-400 font-mono">Rp</span>
                                    <input 
                                        type="number" 
                                        wire:model.live="paidAmount" 
                                        class="w-full bg-slate-900 border border-slate-700 text-base font-black font-mono text-white rounded-xl pl-12 pr-4 py-2.5 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                </div>
                                @error('paidAmount')
                                    <p class="text-xs text-red-400 mt-1 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Quick Cash Nominal Buttons -->
                            <div>
                                <span class="text-[11px] font-semibold text-slate-400 mb-1.5 block">Nominal Cepat:</span>
                                <div class="grid grid-cols-3 gap-2">
                                    <button 
                                        type="button" 
                                        wire:click="setPaidAmount({{ $this->finalAmount }})" 
                                        class="px-2 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-amber-400 text-xs font-bold border border-slate-700 font-mono">
                                        Uang Pas
                                    </button>
                                    <button 
                                        type="button" 
                                        wire:click="setPaidAmount(50000)" 
                                        class="px-2 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold border border-slate-700 font-mono">
                                        50.000
                                    </button>
                                    <button 
                                        type="button" 
                                        wire:click="setPaidAmount(100000)" 
                                        class="px-2 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold border border-slate-700 font-mono">
                                        100.000
                                    </button>
                                    <button 
                                        type="button" 
                                        wire:click="setPaidAmount(150000)" 
                                        class="px-2 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold border border-slate-700 font-mono">
                                        150.000
                                    </button>
                                    <button 
                                        type="button" 
                                        wire:click="setPaidAmount(200000)" 
                                        class="px-2 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold border border-slate-700 font-mono">
                                        200.000
                                    </button>
                                    <button 
                                        type="button" 
                                        wire:click="setPaidAmount(500000)" 
                                        class="px-2 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold border border-slate-700 font-mono">
                                        500.000
                                    </button>
                                </div>
                            </div>

                            <!-- Change Display -->
                            <div class="pt-2 border-t border-slate-800 flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-300">Kembalian:</span>
                                <span class="text-lg font-black font-mono {{ $this->paidAmount >= $this->finalAmount ? 'text-emerald-400' : 'text-red-400' }}">
                                    Rp {{ number_format($this->changeAmount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @elseif($isQrisSelected)
                        <!-- QRIS Code Simulator -->
                        <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 text-center space-y-3">
                            <div class="inline-block p-3 bg-white rounded-2xl shadow-xl">
                                <!-- QR Mockup SVG -->
                                <svg class="w-36 h-36 mx-auto text-black" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm13-2h3v3h-3v-3zm0 5h3v3h-3v-3zm-3-5h2v2h-2v-2zm0 4h2v2h-2v-2zm-2-4h1v1h-1v-1zm0 2h1v1h-1v-1zm0 2h1v1h-1v-1zm6-6h2v2h-2v-2zM6 6h2v2H6V6zm10 0h2v2h-2V6zM6 16h2v2H6v-2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-200">KAFE POS INDONESIA (QRIS STATIS/DINAMIS)</p>
                                <p class="text-[11px] text-slate-400">Scan dengan BCA, Mandiri, GoPay, OVO, ShopeePay, Dana</p>
                            </div>
                        </div>
                    @else
                        <!-- Card/Transfer Info -->
                        <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 text-center space-y-2">
                            <i data-lucide="credit-card" class="w-8 h-8 text-amber-400 mx-auto"></i>
                            <p class="text-xs font-bold text-slate-200">Gunakan Mesin EDC / Transfer Kasir</p>
                            <p class="text-[11px] text-slate-400">Pastikan status pembayaran pada mesin EDC telah Approved/Success.</p>
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-slate-800 bg-slate-950/60 flex items-center justify-end gap-3">
                    <button 
                        wire:click="closePaymentModal" 
                        class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-300 hover:text-white hover:bg-slate-800 transition-colors">
                        Batal
                    </button>
                    <button 
                        wire:click="checkout" 
                        class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 hover:brightness-110 shadow-lg shadow-amber-500/25 flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        Konfirmasi Bayar & Cetak
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 2: Receipt & Success Modal -->
    @if($isSuccessModalOpen && $latestOrder)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fade-in" x-cloak>
            <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-md shadow-2xl overflow-hidden flex flex-col max-h-[95vh]">
                
                <!-- Header -->
                <div class="p-4 border-b border-slate-800 bg-emerald-500/10 border-emerald-500/20 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-emerald-400">
                        <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                        <span class="text-sm font-bold">Transaksi Sukses!</span>
                    </div>
                    <button wire:click="closeSuccessModal" class="text-slate-400 hover:text-white">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Printable Receipt Slip Area -->
                <div class="p-4 overflow-y-auto bg-slate-950/60">
                    <div id="printable-receipt" class="bg-white text-slate-900 p-5 rounded-2xl font-mono text-xs shadow-xl mx-auto max-w-[320px] leading-relaxed">
                        
                        <!-- Cafe Header -->
                        <div class="text-center pb-3 border-b border-dashed border-slate-400 space-y-1">
                            <h2 class="text-base font-black uppercase tracking-wider">KAFE KITA</h2>
                            <p class="text-[10px] text-slate-600">Jl. Kopi Harapan No. 88, Jakarta</p>
                            <p class="text-[10px] text-slate-600">Telp: 0812-3456-7890</p>
                        </div>

                        <!-- Order Meta -->
                        <div class="py-2.5 border-b border-dashed border-slate-400 text-[10px] space-y-0.5">
                            <div class="flex justify-between">
                                <span>No: {{ $latestOrder->order_number }}</span>
                                <span>{{ $latestOrder->created_at->format('d/m/y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Kasir: {{ $latestOrder->user->name ?? 'Kasir' }}</span>
                                <span class="uppercase font-bold">{{ $latestOrder->order_type === 'dine_in' ? 'Dine In' : 'Take Away' }}</span>
                            </div>
                            @if($latestOrder->customer_name || $latestOrder->table_number)
                                <div class="flex justify-between text-slate-700">
                                    <span>Plg: {{ $latestOrder->customer_name ?: '-' }}</span>
                                    @if($latestOrder->table_number)
                                        <span>Meja: {{ $latestOrder->table_number }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Items List -->
                        <div class="py-3 border-b border-dashed border-slate-400 space-y-2">
                            @foreach($latestOrder->items as $item)
                                <div>
                                    <div class="flex justify-between font-bold">
                                        <span class="flex-1 pr-2">{{ $item->product->name ?? 'Item' }}</span>
                                        <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-[10px] text-slate-600">
                                        <span>{{ $item->quantity }} x {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                                    </div>
                                    @if($item->notes)
                                        <p class="text-[9px] italic text-slate-500 -mt-0.5">* {{ $item->notes }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Totals & Payment -->
                        <div class="py-2.5 border-b border-dashed border-slate-400 space-y-1 text-[11px]">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($latestOrder->total_amount, 0, ',', '.') }}</span>
                            </div>
                            @if($latestOrder->discount_amount > 0)
                                <div class="flex justify-between text-slate-700">
                                    <span>Diskon</span>
                                    <span>- Rp {{ number_format($latestOrder->discount_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($latestOrder->tax_amount > 0)
                                <div class="flex justify-between">
                                    <span>PB1 (10%)</span>
                                    <span>Rp {{ number_format($latestOrder->tax_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between font-black text-xs pt-1 border-t border-slate-300">
                                <span>TOTAL</span>
                                <span>Rp {{ number_format($latestOrder->final_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-[10px] pt-1">
                                <span>Metode Bayar</span>
                                <span>{{ $latestOrder->paymentMethod->name ?? 'Tunai' }}</span>
                            </div>
                            <div class="flex justify-between text-[10px]">
                                <span>Bayar</span>
                                <span>Rp {{ number_format($latestOrder->paid_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-[10px] font-bold">
                                <span>Kembalian</span>
                                <span>Rp {{ number_format($latestOrder->change_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Footer Thank you -->
                        <div class="text-center pt-3 text-[10px] text-slate-600 space-y-0.5">
                            <p class="font-bold">TERIMA KASIH ATAS KUNJUNGANNYA</p>
                            <p class="text-[9px]">Follow kami di Instagram: @kafekita.id</p>
                            <p class="text-[8px] text-slate-400 mt-1">Wifi Pass: kopienak123</p>
                        </div>
                    </div>
                </div>

                <!-- Receipt Actions -->
                <div class="p-4 border-t border-slate-800 bg-slate-900 flex items-center justify-between gap-3">
                    <button 
                        wire:click="closeSuccessModal" 
                        class="flex-1 py-2.5 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 text-slate-200 transition-colors flex items-center justify-center gap-1.5">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        Transaksi Baru
                    </button>
                    <button 
                        onclick="window.print()" 
                        class="flex-1 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider bg-amber-500 hover:bg-amber-400 text-slate-950 transition-colors flex items-center justify-center gap-1.5 shadow-lg shadow-amber-500/20">
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        Cetak Struk
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
