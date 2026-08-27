<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Cafe POS System') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Tailwind CDN for robust fallback and fastest rendering -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        cafe: {
                            50: '#fdf8f6',
                            100: '#f2e8e5',
                            200: '#eaddd7',
                            300: '#e0a96d',
                            400: '#d18b47',
                            500: '#b86b28',
                            600: '#9c531d',
                            700: '#7c3f18',
                            800: '#5c2d13',
                            900: '#381c0d',
                            950: '#200f07',
                        },
                        amberPrimary: '#F59E0B',
                    }
                }
            }
        }
    </script>

    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbars */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.6);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(100, 116, 139, 0.4);
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 116, 139, 0.7);
        }

        /* Thermal Printer Styling */
        @media print {
            body * {
                visibility: hidden;
            }
            #printable-receipt, #printable-receipt * {
                visibility: visible;
            }
            #printable-receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 78mm;
                margin: 0 auto;
                padding: 10px;
                background: #fff !important;
                color: #000 !important;
                font-family: 'JetBrains Mono', 'Courier New', monospace;
                font-size: 11px;
                line-height: 1.3;
            }
            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body class="h-full font-sans antialiased bg-slate-950 text-slate-100 flex flex-col selection:bg-amber-500 selection:text-slate-950">

    @auth
    <!-- Header Navigation Bar -->
    <header x-data="{ mobileNavOpen: false }" class="sticky top-0 z-30 bg-slate-900/95 backdrop-blur border-b border-slate-800/80 px-4 lg:px-6 py-2.5 shadow-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <!-- Mobile Hamburger Button -->
                <button 
                    @click="mobileNavOpen = !mobileNavOpen" 
                    class="md:hidden p-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white border border-slate-700">
                    <i data-lucide="menu" class="w-5 h-5" x-show="!mobileNavOpen"></i>
                    <i data-lucide="x" class="w-5 h-5" x-show="mobileNavOpen" x-cloak></i>
                </button>

                <a href="{{ route('pos') }}" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-amber-600 via-amber-500 to-yellow-400 flex items-center justify-center shadow-lg shadow-amber-500/20 group-hover:scale-105 transition-transform duration-200">
                        <i data-lucide="coffee" class="w-4 h-4 sm:w-5 sm:h-5 text-slate-950 font-bold"></i>
                    </div>
                    <div>
                        <h1 class="text-base sm:text-lg font-black tracking-tight text-white flex items-center gap-1.5">
                            KAFE <span class="text-amber-400">POS</span>
                            <span class="text-[9px] sm:text-[10px] uppercase tracking-wider font-semibold px-1.5 py-0.2 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">v3.0</span>
                        </h1>
                        <p class="hidden sm:block text-xs text-slate-400 -mt-0.5">Sistem Kasir & Manajemen Kafe</p>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-1.5 ml-4 pl-4 border-l border-slate-800">
                    <a href="{{ route('pos') }}" 
                       class="flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-semibold transition-all duration-150 {{ request()->routeIs('pos') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                        <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                        Kasir (POS)
                    </a>

                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('products') }}" 
                       class="flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-semibold transition-all duration-150 {{ request()->routeIs('products') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                        <i data-lucide="package" class="w-4 h-4"></i>
                        Kelola Produk
                    </a>

                    <a href="{{ route('reports') }}" 
                       class="flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-semibold transition-all duration-150 {{ request()->routeIs('reports') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                        <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                        Laporan Omset
                    </a>
                    @endif
                </nav>
            </div>

            <!-- Right Side: Live Clock, Cashier Info, and Logout -->
            <div class="flex items-center gap-2.5 sm:gap-4">
                <!-- Live Digital Clock -->
                <div x-data="{ 
                        time: '', 
                        date: '',
                        updateClock() {
                            const now = new Date();
                            this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                            this.date = now.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short' });
                        } 
                     }" 
                     x-init="updateClock(); setInterval(() => updateClock(), 1000)"
                     class="hidden sm:flex flex-col text-right pr-3 border-r border-slate-800">
                    <span class="text-xs font-mono font-bold text-amber-400" x-text="time"></span>
                    <span class="text-[11px] text-slate-400" x-text="date"></span>
                </div>

                <!-- User Badge -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-bold text-slate-200 leading-tight">{{ auth()->user()->name }}</p>
                        <span class="inline-block text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded {{ auth()->user()->isAdmin() ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' }}">
                            {{ auth()->user()->role }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" 
                                title="Keluar / Logout"
                                class="p-2 sm:p-2.5 rounded-xl bg-slate-800/80 hover:bg-red-500/20 text-slate-400 hover:text-red-400 border border-slate-700/60 hover:border-red-500/40 transition-colors">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu Dropdown -->
        <div x-show="mobileNavOpen" x-cloak class="md:hidden pt-3 mt-2 border-t border-slate-800 space-y-1 pb-1">
            <div class="px-3 py-1.5 mb-2 bg-slate-950/60 rounded-xl flex items-center justify-between">
                <span class="text-xs font-bold text-slate-300">{{ auth()->user()->name }}</span>
                <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded {{ auth()->user()->isAdmin() ? 'bg-indigo-500/20 text-indigo-300' : 'bg-emerald-500/20 text-emerald-300' }}">
                    {{ auth()->user()->role }}
                </span>
            </div>

            <a href="{{ route('pos') }}" 
               @click="mobileNavOpen = false"
               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('pos') ? 'bg-amber-500 text-slate-950' : 'text-slate-300 hover:bg-slate-800' }}">
                <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                Kasir (POS)
            </a>

            @if(auth()->user()->isAdmin())
            <a href="{{ route('products') }}" 
               @click="mobileNavOpen = false"
               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('products') ? 'bg-amber-500 text-slate-950' : 'text-slate-300 hover:bg-slate-800' }}">
                <i data-lucide="package" class="w-4 h-4"></i>
                Kelola Produk
            </a>

            <a href="{{ route('reports') }}" 
               @click="mobileNavOpen = false"
               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-bold {{ request()->routeIs('reports') ? 'bg-amber-500 text-slate-950' : 'text-slate-300 hover:bg-slate-800' }}">
                <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                Laporan Omset
            </a>
            @endif
        </div>
    </header>
    @endauth

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col overflow-hidden relative">
        {{ $slot }}
    </main>

    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
        document.addEventListener('livewire:navigated', () => {
            lucide.createIcons();
        });
        document.addEventListener('livewire:load', () => {
            lucide.createIcons();
        });
        Livewire.hook('commit', ({ succeed }) => {
            succeed(() => {
                setTimeout(() => {
                    lucide.createIcons();
                }, 50);
            });
        });
    </script>
</body>
</html>