<div class="flex-1 overflow-y-auto bg-slate-950 p-4 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Page Title & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900/60 p-6 rounded-3xl border border-slate-800/80">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2.5">
                    <i data-lucide="line-chart" class="w-7 h-7 text-amber-400"></i>
                    Laporan Omset & Analisis Penjualan
                </h1>
                <p class="text-xs sm:text-sm text-slate-400 mt-1">Pantau performa harian, bulanan, menu terlaris (best seller), dan riwayat transaksi kasir.</p>
            </div>

            <!-- Period Filter Selector -->
            <div class="flex items-center gap-1.5 p-1 bg-slate-950 rounded-2xl border border-slate-800 self-start sm:self-auto">
                <button 
                    wire:click="selectPeriod('today')" 
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $period === 'today' ? 'bg-amber-500 text-slate-950 shadow' : 'text-slate-400 hover:text-white' }}">
                    Hari Ini
                </button>
                <button 
                    wire:click="selectPeriod('this_week')" 
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $period === 'this_week' ? 'bg-amber-500 text-slate-950 shadow' : 'text-slate-400 hover:text-white' }}">
                    Minggu Ini
                </button>
                <button 
                    wire:click="selectPeriod('this_month')" 
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $period === 'this_month' ? 'bg-amber-500 text-slate-950 shadow' : 'text-slate-400 hover:text-white' }}">
                    Bulan Ini
                </button>
                <button 
                    wire:click="selectPeriod('all')" 
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $period === 'all' ? 'bg-amber-500 text-slate-950 shadow' : 'text-slate-400 hover:text-white' }}">
                    Semua
                </button>
            </div>
        </div>

        <!-- KPI Metric Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Omset Hari Ini -->
            <div class="bg-gradient-to-br from-slate-900 via-slate-900 to-slate-950 border border-slate-800/90 rounded-3xl p-5 relative overflow-hidden shadow-xl">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Omset Hari Ini</span>
                    <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                        <i data-lucide="coins" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-2xl font-black text-amber-400 font-mono tracking-tight">
                        Rp {{ number_format($todayRevenue, 0, ',', '.') }}
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-1 font-semibold flex items-center gap-1">
                        <i data-lucide="check-circle" class="w-3 h-3 text-emerald-400"></i>
                        {{ $todayOrdersCount }} Transaksi Selesai
                    </p>
                </div>
            </div>

            <!-- Omset Bulan Ini -->
            <div class="bg-gradient-to-br from-slate-900 via-slate-900 to-slate-950 border border-slate-800/90 rounded-3xl p-5 relative overflow-hidden shadow-xl">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Omset Bulan Ini</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-2xl font-black text-emerald-400 font-mono tracking-tight">
                        Rp {{ number_format($monthRevenue, 0, ',', '.') }}
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-1 font-semibold flex items-center gap-1">
                        <i data-lucide="shopping-cart" class="w-3 h-3 text-emerald-400"></i>
                        {{ $monthOrdersCount }} Transaksi Bulan Ini
                    </p>
                </div>
            </div>

            <!-- Omset Filter Terpilih -->
            <div class="bg-gradient-to-br from-slate-900 via-slate-900 to-slate-950 border border-slate-800/90 rounded-3xl p-5 relative overflow-hidden shadow-xl">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Periode Ini</span>
                    <div class="w-9 h-9 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                        <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-2xl font-black text-indigo-300 font-mono tracking-tight">
                        Rp {{ number_format($periodRevenue, 0, ',', '.') }}
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-1 font-semibold">
                        {{ $periodOrdersCount }} Pesanan ({{ ucfirst(str_replace('_', ' ', $period)) }})
                    </p>
                </div>
            </div>

            <!-- Rata-rata Nilai Pesanan (AOV) -->
            <div class="bg-gradient-to-br from-slate-900 via-slate-900 to-slate-950 border border-slate-800/90 rounded-3xl p-5 relative overflow-hidden shadow-xl">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Rata-rata / Pesanan</span>
                    <div class="w-9 h-9 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400">
                        <i data-lucide="receipt" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-4">
                    @php
                        $aov = $periodOrdersCount > 0 ? $periodRevenue / $periodOrdersCount : 0;
                    @endphp
                    <h3 class="text-2xl font-black text-purple-300 font-mono tracking-tight">
                        Rp {{ number_format($aov, 0, ',', '.') }}
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-1 font-semibold">Rata-rata keranjang per meja</p>
                </div>
            </div>

        </div>

        <!-- SECTION: Best Sellers & Analytics Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Best Selling Products (1 column) -->
            <div class="bg-slate-900/80 border border-slate-800/80 rounded-3xl p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div class="flex items-center gap-2">
                        <i data-lucide="flame" class="w-5 h-5 text-amber-500"></i>
                        <h3 class="text-base font-bold text-white">Produk Paling Laris</h3>
                    </div>
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">Top 6</span>
                </div>

                <div class="space-y-3.5">
                    @forelse($bestSellers as $index => $bs)
                        <div class="flex items-center gap-3 p-2.5 rounded-2xl bg-slate-950/70 border border-slate-800/60">
                            <!-- Rank Number -->
                            <div class="w-7 h-7 rounded-xl flex items-center justify-center font-black text-xs {{ $index === 0 ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : ($index === 1 ? 'bg-slate-300 text-slate-950' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-800 text-slate-400')) }}">
                                {{ $index + 1 }}
                            </div>

                            <!-- Product Info -->
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-bold text-slate-200 truncate">{{ $bs->product->name ?? 'Menu' }}</h4>
                                <div class="flex items-center gap-2 text-[10px] text-slate-400 mt-0.5">
                                    <span class="text-amber-400 font-bold font-mono">{{ $bs->total_qty }} porsi terjual</span>
                                    <span>•</span>
                                    <span class="font-mono">Rp {{ number_format($bs->total_sales, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-500">
                            <i data-lucide="coffee" class="w-8 h-8 mx-auto mb-2 text-slate-600"></i>
                            <p class="text-xs font-medium">Belum ada data penjualan tercatat.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Orders Log (2 columns) -->
            <div class="lg:col-span-2 bg-slate-900/80 border border-slate-800/80 rounded-3xl p-6 shadow-xl space-y-4 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                        <div class="flex items-center gap-2">
                            <i data-lucide="history" class="w-5 h-5 text-amber-500"></i>
                            <h3 class="text-base font-bold text-white">Riwayat Transaksi Terakhir</h3>
                        </div>
                        <span class="text-xs text-slate-400 font-mono">{{ $recentOrders->count() }} Data</span>
                    </div>

                    <div class="overflow-x-auto mt-3">
                        <table class="w-full text-left text-xs text-slate-300">
                            <thead class="text-[10px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800">
                                <tr>
                                    <th class="py-2.5 px-3">No. Order</th>
                                    <th class="py-2.5 px-3">Waktu</th>
                                    <th class="py-2.5 px-3">Kasir</th>
                                    <th class="py-2.5 px-3">Tipe</th>
                                    <th class="py-2.5 px-3">Metode</th>
                                    <th class="py-2.5 px-3 text-right">Total</th>
                                    <th class="py-2.5 px-3 text-center">Struk</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/50">
                                @forelse($recentOrders as $order)
                                    <tr class="hover:bg-slate-800/40 transition-colors">
                                        <td class="py-3 px-3 font-mono font-bold text-amber-400">{{ $order->order_number }}</td>
                                        <td class="py-3 px-3 text-slate-400">{{ $order->created_at->format('H:i, d M') }}</td>
                                        <td class="py-3 px-3 font-medium text-slate-200">{{ $order->user->name ?? 'Kasir' }}</td>
                                        <td class="py-3 px-3">
                                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold {{ $order->order_type === 'dine_in' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-slate-800 text-slate-300' }}">
                                                {{ $order->order_type === 'dine_in' ? 'Dine In' : 'Take Away' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-slate-300 font-medium">{{ $order->paymentMethod->name ?? 'Tunai' }}</td>
                                        <td class="py-3 px-3 text-right font-mono font-bold text-slate-100">
                                            {{ $order->formatted_final_amount }}
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <button 
                                                wire:click="viewOrderDetail({{ $order->id }})" 
                                                class="p-1.5 rounded-lg bg-slate-800 hover:bg-amber-500 hover:text-slate-950 text-slate-400 transition-colors"
                                                title="Lihat Detail & Struk">
                                                <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-slate-500">
                                            Belum ada transaksi pada periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- MODAL: Order Detail & Thermal Receipt View -->
    @if($selectedOrderForDetail && $detailOrder)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fade-in" x-cloak>
            <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-md shadow-2xl overflow-hidden flex flex-col max-h-[95vh]">
                
                <!-- Header -->
                <div class="p-4 border-b border-slate-800 flex items-center justify-between bg-slate-950/50">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i data-lucide="receipt" class="w-4 h-4 text-amber-400"></i>
                        Detail Transaksi #{{ $detailOrder->order_number }}
                    </h3>
                    <button wire:click="closeOrderDetail" class="text-slate-400 hover:text-white">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Printable Receipt Area -->
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
                                <span>No: {{ $detailOrder->order_number }}</span>
                                <span>{{ $detailOrder->created_at->format('d/m/y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Kasir: {{ $detailOrder->user->name ?? 'Kasir' }}</span>
                                <span class="uppercase font-bold">{{ $detailOrder->order_type === 'dine_in' ? 'Dine In' : 'Take Away' }}</span>
                            </div>
                            @if($detailOrder->customer_name || $detailOrder->table_number)
                                <div class="flex justify-between text-slate-700">
                                    <span>Plg: {{ $detailOrder->customer_name ?: '-' }}</span>
                                    @if($detailOrder->table_number)
                                        <span>Meja: {{ $detailOrder->table_number }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Items List -->
                        <div class="py-3 border-b border-dashed border-slate-400 space-y-2">
                            @foreach($detailOrder->items as $item)
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
                                <span>Rp {{ number_format($detailOrder->total_amount, 0, ',', '.') }}</span>
                            </div>
                            @if($detailOrder->discount_amount > 0)
                                <div class="flex justify-between text-slate-700">
                                    <span>Diskon</span>
                                    <span>- Rp {{ number_format($detailOrder->discount_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($detailOrder->tax_amount > 0)
                                <div class="flex justify-between">
                                    <span>PB1 (10%)</span>
                                    <span>Rp {{ number_format($detailOrder->tax_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between font-black text-xs pt-1 border-t border-slate-300">
                                <span>TOTAL</span>
                                <span>Rp {{ number_format($detailOrder->final_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-[10px] pt-1">
                                <span>Metode Bayar</span>
                                <span>{{ $detailOrder->paymentMethod->name ?? 'Tunai' }}</span>
                            </div>
                            <div class="flex justify-between text-[10px]">
                                <span>Bayar</span>
                                <span>Rp {{ number_format($detailOrder->paid_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-[10px] font-bold">
                                <span>Kembalian</span>
                                <span>Rp {{ number_format($detailOrder->change_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Footer Thank you -->
                        <div class="text-center pt-3 text-[10px] text-slate-600 space-y-0.5">
                            <p class="font-bold">TERIMA KASIH ATAS KUNJUNGANNYA</p>
                            <p class="text-[9px]">Follow kami di Instagram: @kafekita.id</p>
                        </div>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="p-4 border-t border-slate-800 bg-slate-900 flex items-center justify-between gap-3">
                    <button 
                        wire:click="closeOrderDetail" 
                        class="flex-1 py-2.5 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 text-slate-200 transition-colors">
                        Tutup
                    </button>
                    <button 
                        onclick="window.print()" 
                        class="flex-1 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider bg-amber-500 hover:bg-amber-400 text-slate-950 transition-colors flex items-center justify-center gap-1.5 shadow-lg shadow-amber-500/20">
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        Cetak Ulang Struk
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
