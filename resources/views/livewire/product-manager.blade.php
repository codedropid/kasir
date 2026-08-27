<div class="flex-1 overflow-y-auto bg-slate-950 p-4 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Flash Alert -->
        @if(session()->has('message'))
            <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-2.5">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                    <span>{{ session('message') }}</span>
                </div>
            </div>
        @endif

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900/60 p-6 rounded-3xl border border-slate-800/80">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2.5">
                    <i data-lucide="package" class="w-7 h-7 text-amber-400"></i>
                    Kelola Menu & Produk Kafe
                </h1>
                <p class="text-xs sm:text-sm text-slate-400 mt-1">Atur katalog menu, harga jual, harga pokok (HPP), dan ketersediaan stok dapur.</p>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <button 
                    wire:click="openCategoryModal" 
                    class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold border border-slate-700 flex items-center gap-2 transition-all">
                    <i data-lucide="folder-plus" class="w-4 h-4 text-amber-400"></i>
                    Kelola Kategori
                </button>
                <button 
                    wire:click="openCreateModal" 
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 text-xs font-black uppercase tracking-wider flex items-center gap-2 hover:brightness-110 shadow-lg shadow-amber-500/20 transition-all">
                    <i data-lucide="plus-circle" class="w-4 h-4 font-bold"></i>
                    Tambah Menu Baru
                </button>
            </div>
        </div>

        <!-- Filters & Search Bar -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2 relative">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari berdasarkan nama menu atau SKU..." 
                    class="w-full bg-slate-900 border border-slate-800 text-sm text-slate-200 placeholder-slate-500 rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:border-amber-500">
            </div>

            <div>
                <select 
                    wire:model.live="selectedCategory" 
                    class="w-full bg-slate-900 border border-slate-800 text-sm text-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-amber-500">
                    <option value="all">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}">{{ $cat->name }} ({{ $cat->products_count }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-3xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/80 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="p-4">Menu</th>
                            <th class="p-4">SKU</th>
                            <th class="p-4">Kategori</th>
                            <th class="p-4">Harga Jual</th>
                            <th class="p-4">HPP (Modal)</th>
                            <th class="p-4">Margin</th>
                            <th class="p-4 text-center">Status Stok</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($products as $prod)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 rounded-xl bg-slate-800 overflow-hidden flex-shrink-0 border border-slate-700/60">
                                            @if($prod->image)
                                                <img src="{{ $prod->image }}" alt="{{ $prod->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-500">
                                                    <i data-lucide="coffee" class="w-5 h-5"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <span class="font-bold text-slate-100 text-sm">{{ $prod->name }}</span>
                                    </div>
                                </td>
                                <td class="p-4 font-mono text-slate-400">{{ $prod->sku }}</td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-lg bg-amber-500/10 text-amber-400 border border-amber-500/20 font-semibold text-[11px]">
                                        {{ $prod->category->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="p-4 font-mono font-bold text-amber-400 text-sm">
                                    {{ $prod->formatted_price }}
                                </td>
                                <td class="p-4 font-mono text-slate-400">
                                    Rp {{ number_format($prod->cost_price, 0, ',', '.') }}
                                </td>
                                <td class="p-4 font-mono">
                                    @php
                                        $margin = $prod->price - $prod->cost_price;
                                    @endphp
                                    <span class="text-emerald-400 font-semibold">+Rp {{ number_format($margin, 0, ',', '.') }}</span>
                                </td>
                                <td class="p-4 text-center">
                                    <button 
                                        wire:click="toggleAvailability({{ $prod->id }})" 
                                        class="px-3 py-1 rounded-full text-[11px] font-bold transition-all {{ $prod->is_available ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-red-500/20 text-red-300 border border-red-500/30' }}">
                                        {{ $prod->is_available ? '● Tersedia' : '○ Habis' }}
                                    </button>
                                </td>
                                <td class="p-4 text-right space-x-1">
                                    <button 
                                        wire:click="editProduct({{ $prod->id }})" 
                                        class="p-2 rounded-xl bg-slate-800 hover:bg-amber-500 hover:text-slate-950 text-slate-300 transition-colors"
                                        title="Edit">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>
                                    <button 
                                        wire:click="deleteProduct({{ $prod->id }})" 
                                        wire:confirm="Yakin ingin menghapus menu '{{ $prod->name }}'?" 
                                        class="p-2 rounded-xl bg-slate-800 hover:bg-red-500 hover:text-white text-slate-400 transition-colors"
                                        title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-slate-500">
                                    <i data-lucide="package-x" class="w-8 h-8 mx-auto mb-2 text-slate-600"></i>
                                    Tidak ada data produk yang cocok.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- MODAL: Form Tambah/Edit Produk -->
    @if($isFormOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fade-in" x-cloak>
            <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                
                <div class="p-5 border-b border-slate-800 flex items-center justify-between bg-slate-950/50">
                    <h3 class="text-base font-bold text-white">
                        {{ $productId ? 'Edit Menu Kafe' : 'Tambah Menu Baru' }}
                    </h3>
                    <button wire:click="$set('isFormOpen', false)" class="text-slate-400 hover:text-white">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form wire:submit.prevent="saveProduct" class="p-5 overflow-y-auto space-y-4">
                    <!-- Name -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Nama Menu / Produk</label>
                        <input 
                            type="text" 
                            wire:model="name" 
                            placeholder="Contoh: Iced Hazelnut Latte" 
                            class="w-full bg-slate-950 border border-slate-800 text-xs text-slate-100 rounded-xl px-3 py-2.5 focus:outline-none focus:border-amber-500">
                        @error('name') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                    </div>

                    <!-- Category & SKU -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Kategori</label>
                            <select 
                                wire:model="category_id" 
                                class="w-full bg-slate-950 border border-slate-800 text-xs text-slate-100 rounded-xl px-3 py-2.5 focus:outline-none focus:border-amber-500">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Kode SKU</label>
                            <input 
                                type="text" 
                                wire:model="sku" 
                                placeholder="MNM-101" 
                                class="w-full bg-slate-950 border border-slate-800 text-xs font-mono text-slate-100 rounded-xl px-3 py-2.5 focus:outline-none focus:border-amber-500">
                            @error('sku') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Prices -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-amber-400 mb-1">Harga Jual (Rp)</label>
                            <input 
                                type="number" 
                                wire:model="price" 
                                placeholder="30000" 
                                class="w-full bg-slate-950 border border-slate-800 text-xs font-mono text-slate-100 rounded-xl px-3 py-2.5 focus:outline-none focus:border-amber-500">
                            @error('price') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 mb-1">HPP / Modal (Rp)</label>
                            <input 
                                type="number" 
                                wire:model="cost_price" 
                                placeholder="12000" 
                                class="w-full bg-slate-950 border border-slate-800 text-xs font-mono text-slate-100 rounded-xl px-3 py-2.5 focus:outline-none focus:border-amber-500">
                            @error('cost_price') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Image URL -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">URL Gambar (Unsplash / CDN)</label>
                        <input 
                            type="url" 
                            wire:model="image" 
                            placeholder="https://images.unsplash.com/..." 
                            class="w-full bg-slate-950 border border-slate-800 text-xs text-slate-100 rounded-xl px-3 py-2.5 focus:outline-none focus:border-amber-500">
                    </div>

                    <!-- Availability Toggle -->
                    <div class="flex items-center gap-2 pt-2">
                        <input 
                            type="checkbox" 
                            id="is_available" 
                            wire:model="is_available" 
                            class="w-4 h-4 text-amber-500 bg-slate-900 border-slate-700 rounded focus:ring-amber-500">
                        <label for="is_available" class="text-xs font-bold text-slate-200">Status Tersedia (Dapat dipesan kasir)</label>
                    </div>

                    <!-- Footer -->
                    <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-2.5">
                        <button 
                            type="button" 
                            wire:click="$set('isFormOpen', false)" 
                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-300 hover:bg-slate-800">
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-lg shadow-amber-500/20">
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: Kelola Kategori -->
    @if($isCategoryModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fade-in" x-cloak>
            <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-md shadow-2xl overflow-hidden flex flex-col">
                
                <div class="p-5 border-b border-slate-800 flex items-center justify-between bg-slate-950/50">
                    <h3 class="text-base font-bold text-white">Kelola Kategori Menu</h3>
                    <button wire:click="$set('isCategoryModalOpen', false)" class="text-slate-400 hover:text-white">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="p-5 space-y-4">
                    <!-- Add Category Form -->
                    <form wire:submit.prevent="addCategory" class="flex gap-2">
                        <input 
                            type="text" 
                            wire:model="newCategoryName" 
                            placeholder="Nama kategori baru..." 
                            class="flex-1 bg-slate-950 border border-slate-800 text-xs text-slate-100 rounded-xl px-3 py-2 focus:outline-none focus:border-amber-500">
                        <button 
                            type="submit" 
                            class="px-4 py-2 rounded-xl text-xs font-bold bg-amber-500 text-slate-950 hover:bg-amber-400">
                            Tambah
                        </button>
                    </form>
                    @error('newCategoryName') <p class="text-xs text-red-400">{{ $message }}</p> @enderror

                    <!-- Categories List -->
                    <div class="space-y-2 max-h-60 overflow-y-auto">
                        @foreach($categories as $cat)
                            <div class="p-2.5 rounded-xl bg-slate-950/80 border border-slate-800 flex items-center justify-between">
                                <div>
                                    <span class="text-xs font-bold text-slate-200">{{ $cat->name }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $cat->products_count }} Menu</span>
                                </div>
                                <button 
                                    wire:click="deleteCategory({{ $cat->id }})" 
                                    wire:confirm="Hapus kategori '{{ $cat->name }}'? Produk di dalamnya juga akan terhapus."
                                    class="text-slate-500 hover:text-red-400 p-1">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    @endif

</div>
