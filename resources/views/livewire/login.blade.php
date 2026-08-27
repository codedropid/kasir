<div class="min-h-screen flex items-center justify-center bg-[#18171f] p-4 sm:p-6 lg:p-8 selection:bg-indigo-500 selection:text-white" x-data="{ showPassword: false }">
    
    <!-- Main Two-Column Card Container -->
    <div class="w-full max-w-5xl bg-[#23222d] border border-slate-800/80 rounded-[2.25rem] shadow-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[620px] transition-all">
        
        <!-- LEFT COLUMN: Cafe Photo Showcase (5 cols on lg) -->
        <div class="lg:col-span-6 relative p-4 sm:p-5 flex flex-col justify-between overflow-hidden">
            <!-- Background Image with Overlay -->
            <div class="relative w-full h-full min-h-[380px] lg:min-h-full rounded-[1.75rem] overflow-hidden shadow-inner flex flex-col justify-between p-6 sm:p-8">
                <!-- Cafe Photo -->
                <img 
                    src="https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?q=80&w=1200&auto=format&fit=crop" 
                    alt="Suasana Cafe Estetik" 
                    class="absolute inset-0 w-full h-full object-cover transform hover:scale-105 transition-transform duration-700 ease-out">
                
                <!-- Dark Gradient Overlays for Readability -->
                <div class="absolute inset-0 bg-gradient-to-t from-[#14131a]/95 via-[#181720]/40 to-[#181720]/30 backdrop-blur-[0.5px]"></div>

                <!-- Top Row: Brand & Status Pill -->
                <div class="relative z-10 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-amber-400 shadow-lg">
                            <i data-lucide="coffee" class="w-5 h-5"></i>
                        </div>
                        <span class="text-lg font-black tracking-wider text-white uppercase">KAFE<span class="text-amber-400">POS</span></span>
                    </div>

                    <div class="px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-[11px] font-semibold text-white/90 flex items-center gap-1.5 shadow-sm">
                        <span>POS System</span>
                        <i data-lucide="sparkles" class="w-3 h-3 text-amber-400"></i>
                    </div>
                </div>

                <!-- Bottom Row: Title Quote & Indicator Dots -->
                <div class="relative z-10 space-y-5">
                    <div class="space-y-1.5">
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight tracking-tight drop-shadow-md">
                            Menyeduh Kualitas,<br>
                            Mengelola Setiap Pesanan
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-300/90 font-medium">
                            Solusi kasir cepat, pencatatan otomatis, dan efisiensi pesanan kafe Anda.
                        </p>
                    </div>

                    <!-- Carousel Indicator Dots -->
                    <div class="flex items-center gap-2 pt-1">
                        <div class="w-7 h-1.5 rounded-full bg-amber-400"></div>
                        <div class="w-1.5 h-1.5 rounded-full bg-white/40"></div>
                        <div class="w-1.5 h-1.5 rounded-full bg-white/40"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Login Form (6 cols on lg) -->
        <div class="lg:col-span-6 p-6 sm:p-10 lg:p-12 flex flex-col justify-center bg-[#23222d]">
            <div class="w-full max-w-md mx-auto space-y-6">
                
                <!-- Heading -->
                <div class="space-y-1.5">
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        Masuk ke Akun
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-400">
                        Silakan masukkan kredensial akun kasir atau admin Anda.
                    </p>
                </div>

                <!-- Form -->
                <form wire:submit.prevent="login" class="space-y-4">
                    
                    <!-- Email Field -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-300">Email Kasir / Admin</label>
                        <div class="relative">
                            <input 
                                type="email" 
                                wire:model="email" 
                                placeholder="name@kafe.com" 
                                class="w-full bg-[#2f2e3d] border border-slate-700/60 focus:border-indigo-500 focus:bg-[#343343] text-sm text-slate-100 placeholder-slate-500 rounded-2xl px-4 py-3 focus:outline-none transition-all">
                        </div>
                        @error('email') 
                            <p class="text-xs text-red-400 font-semibold mt-1">{{ $message }}</p> 
                        @enderror
                    </div>

                    <!-- Password Field with Show/Hide Toggle -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-semibold text-slate-300">Kata Sandi</label>
                        </div>
                        <div class="relative">
                            <input 
                                :type="showPassword ? 'text' : 'password'" 
                                wire:model="password" 
                                placeholder="Masukkan password Anda" 
                                class="w-full bg-[#2f2e3d] border border-slate-700/60 focus:border-indigo-500 focus:bg-[#343343] text-sm text-slate-100 placeholder-slate-500 rounded-2xl pl-4 pr-11 py-3 focus:outline-none transition-all">
                            
                            <!-- Toggle Button -->
                            <button 
                                type="button" 
                                @click="showPassword = !showPassword" 
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200 transition-colors p-1"
                                title="Tampilkan/Sembunyikan password">
                                <template x-if="!showPassword">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </template>
                                <template x-if="showPassword">
                                    <i data-lucide="eye-off" class="w-4 h-4"></i>
                                </template>
                            </button>
                        </div>
                        @error('password') 
                            <p class="text-xs text-red-400 font-semibold mt-1">{{ $message }}</p> 
                        @enderror
                    </div>

                    <!-- Remember Me Checkbox -->
                    <div class="flex items-center justify-between text-xs pt-0.5">
                        <label class="flex items-center gap-2.5 text-slate-300 cursor-pointer select-none">
                            <input 
                                type="checkbox" 
                                wire:model="remember" 
                                class="w-4 h-4 rounded-md text-indigo-600 bg-[#2f2e3d] border-slate-700 focus:ring-0 focus:ring-offset-0 transition-colors">
                            <span class="text-xs">Ingat saya pada perangkat ini</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full py-3.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/50 active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                        <span>Masuk ke Sistem</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative flex items-center justify-center py-1">
                    <div class="border-t border-slate-700/60 w-full"></div>
                    <span class="bg-[#23222d] px-3 text-[11px] font-medium text-slate-400 uppercase tracking-wider whitespace-nowrap">
                        Atau akses cepat demo
                    </span>
                    <div class="border-t border-slate-700/60 w-full"></div>
                </div>

                <!-- Quick 1-Click Demo Logins -->
                <div class="grid grid-cols-2 gap-3">
                    <button 
                        type="button" 
                        wire:click="quickLogin('kasir')" 
                        class="py-2.5 px-4 rounded-2xl bg-[#2f2e3d] hover:bg-[#383748] border border-slate-700/60 text-slate-200 text-xs font-bold flex items-center justify-center gap-2 transition-all hover:border-emerald-500/40 hover:text-emerald-300">
                        <i data-lucide="user" class="w-4 h-4 text-emerald-400"></i>
                        <span>Kasir Demo</span>
                    </button>

                    <button 
                        type="button" 
                        wire:click="quickLogin('admin')" 
                        class="py-2.5 px-4 rounded-2xl bg-[#2f2e3d] hover:bg-[#383748] border border-slate-700/60 text-slate-200 text-xs font-bold flex items-center justify-center gap-2 transition-all hover:border-amber-500/40 hover:text-amber-300">
                        <i data-lucide="shield" class="w-4 h-4 text-amber-400"></i>
                        <span>Admin Demo</span>
                    </button>
                </div>

            </div>
        </div>

    </div>

</div>
