<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#14131a] text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Terjadi Kendala') - {{ config('app.name', 'Kafe POS') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Tailwind CDN for instant full styling -->
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
                        }
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        pulseGlow: {
                            '0%, 100%': { opacity: '0.4', transform: 'scale(1)' },
                            '50%': { opacity: '0.8', transform: 'scale(1.05)' },
                        }
                    },
                    animation: {
                        float: 'float 4s ease-in-out infinite',
                        pulseGlow: 'pulseGlow 3s ease-in-out infinite',
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-[#14131a] text-slate-100 flex flex-col justify-between p-4 sm:p-6 lg:p-8 font-sans selection:bg-amber-500 selection:text-slate-950 relative overflow-x-hidden">

    <!-- Ambient Glowing Background Accents -->
    <div class="fixed -top-48 -left-48 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none animate-pulseGlow"></div>
    <div class="fixed -bottom-48 -right-48 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none animate-pulseGlow"></div>
    <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-amber-600/5 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Top Branding Navbar -->
    <header class="w-full max-w-5xl mx-auto flex items-center justify-between z-10 py-2">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-gradient-to-tr from-amber-600 via-amber-500 to-yellow-400 flex items-center justify-center shadow-lg shadow-amber-500/20 group-hover:scale-105 transition-transform duration-200">
                <i data-lucide="coffee" class="w-5 h-5 text-slate-950 font-bold"></i>
            </div>
            <div>
                <span class="text-base sm:text-lg font-black tracking-wider text-white uppercase">KAFE<span class="text-amber-400">POS</span></span>
                <span class="text-[10px] hidden sm:inline-block uppercase tracking-wider font-semibold px-2 py-0.5 ml-2 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">System Status</span>
            </div>
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('pos') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 text-xs font-semibold border border-slate-700 flex items-center gap-1.5 transition-all">
                <i data-lucide="shopping-cart" class="w-3.5 h-3.5 text-amber-400"></i>
                <span class="hidden sm:inline">Kasir POS</span>
            </a>
        </div>
    </header>

    <!-- Main Centered Content Container -->
    <main class="w-full max-w-2xl mx-auto my-auto py-8 sm:py-12 z-10">
        <div class="bg-[#22212c]/90 border border-slate-800/90 rounded-[2.25rem] p-6 sm:p-10 lg:p-12 shadow-2xl backdrop-blur-xl text-center relative overflow-hidden">
            
            <!-- Background Watermark Pattern -->
            <div class="absolute -right-8 -bottom-8 opacity-5 pointer-events-none text-white select-none font-black text-9xl font-mono">
                @yield('code', 'ERR')
            </div>

            <!-- Error Icon & Visual Area -->
            <div class="relative mb-6">
                <!-- Glowing Circle Backdrop -->
                <div class="w-24 h-24 sm:w-28 sm:h-28 mx-auto rounded-3xl @yield('glow_color', 'bg-amber-500/10 border-amber-500/20 text-amber-400') border flex items-center justify-center shadow-xl shadow-amber-500/5 animate-float">
                    @yield('icon')
                </div>

                <!-- Error Code Pill Badge -->
                <div class="inline-block mt-4 px-3.5 py-1 rounded-full @yield('badge_color', 'bg-amber-500/15 text-amber-400 border-amber-500/30') border text-xs font-mono font-bold uppercase tracking-widest">
                    ERROR @yield('code', '500')
                </div>
            </div>

            <!-- Error Title & Description -->
            <div class="space-y-3 mb-8">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-white">
                    @yield('heading', 'Terjadi Kesalahan')
                </h1>
                <p class="text-xs sm:text-sm text-slate-400 max-w-lg mx-auto leading-relaxed">
                    @yield('message', 'Maaf, terjadi kendala saat memproses permintaan Anda.')
                </p>
            </div>

            <!-- Helpful Tips Card -->
            <div class="mb-8 p-3.5 sm:p-4 rounded-2xl bg-[#1a1922] border border-slate-800/80 text-left flex items-start gap-3">
                <div class="p-1.5 rounded-lg bg-amber-500/10 text-amber-400 flex-shrink-0 mt-0.5">
                    <i data-lucide="help-circle" class="w-4 h-4"></i>
                </div>
                <div class="text-xs text-slate-300 space-y-0.5">
                    <span class="font-bold text-white block">Saran untuk Anda:</span>
                    <p class="text-slate-400">@yield('suggestion', 'Silakan muat ulang halaman atau kembali ke sistem kasir utama.')</p>
                </div>
            </div>

            <!-- Action Buttons Grid -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('pos') }}" 
                   class="w-full sm:w-auto px-6 py-3.5 rounded-2xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-xs uppercase tracking-wider shadow-lg shadow-amber-500/25 flex items-center justify-center gap-2 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                    <span>Kembali ke Kasir (POS)</span>
                </a>

                <button onclick="window.location.reload()" 
                        type="button"
                        class="w-full sm:w-auto px-5 py-3.5 rounded-2xl bg-slate-800/90 hover:bg-slate-750 border border-slate-700 text-slate-200 font-bold text-xs flex items-center justify-center gap-2 transition-all hover:text-white">
                    <i data-lucide="rotate-cw" class="w-4 h-4"></i>
                    <span>Muat Ulang Halaman</span>
                </button>

                <button onclick="window.history.back()" 
                        type="button"
                        class="w-full sm:w-auto px-4 py-3.5 rounded-2xl bg-transparent hover:bg-slate-800/50 text-slate-400 hover:text-slate-200 font-semibold text-xs transition-all">
                    <span>Halaman Sebelumnya</span>
                </button>
            </div>

        </div>
    </main>

    <!-- Footer Information -->
    <footer class="w-full max-w-5xl mx-auto text-center text-[11px] text-slate-600 py-3 z-10">
        <p>&copy; {{ date('Y') }} Kafe POS. Sistem Kasir & Manajemen Kafe Terintegrasi.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>