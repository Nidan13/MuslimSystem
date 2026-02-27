<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muslim System | Divine Access</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-login-fixed h-screen flex flex-col items-center justify-center p-4 relative overflow-hidden font-sans">

    <!-- Decorative Elements -->
    <div class="glass-blob animate-float w-64 h-64 -top-20 -left-20 opacity-30"></div>
    <div class="glass-blob animate-float w-48 h-48 -bottom-10 -right-10 opacity-20" style="animation-delay: -2s;"></div>

    <div class="w-full max-w-[380px] relative z-20">
        <!-- Main Layout Wrapper -->
        <div class="flex flex-col gap-5">
            <!-- Brand Identity -->
            <div class="text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 backdrop-blur-md mb-2 shadow-xl">
                     <span class="w-1.5 h-1.5 rounded-full bg-royal-gold animate-ping"></span>
                     <span class="text-[8px] font-black text-white/50 tracking-[0.4em] uppercase">Gateway Active</span>
                </div>
                <h1 class="text-3xl font-serif font-black text-white tracking-[0.15em] uppercase leading-none">
                    MUSLIM<br><span class="text-royal-gold text-xl tracking-[0.5em]">SYSTEM</span>
                </h1>
            </div>

            <!-- Login Card -->
            <div class="mihrab-card rounded-[28px] shadow-2xl overflow-hidden">
                <!-- Header -->
                <div class="mihrab-header h-24 flex flex-col items-center justify-center relative">
                    <div class="absolute inset-0 opacity-5 bg-[url('https://www.transparenttextures.com/patterns/arabesque.png')] scale-150 invert"></div>
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-8 h-8 rounded-lg bg-royal-gold/10 flex items-center justify-center mb-1 border border-royal-gold/20">
                            <svg class="w-4 h-4 text-royal-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <h2 class="text-imperial text-lg font-black text-white">LOGIN</h2>
                        <div class="ornament-line mt-1.5 opacity-30 w-20"></div>
                    </div>
                </div>

                <!-- Form Content -->
                <div class="p-6 pb-6">
                    @if($errors->any())
                        <div class="mb-4 p-2.5 bg-red-500/10 border border-red-500/20 rounded-xl flex items-center gap-3 text-red-400 text-[8px] font-bold">
                             <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                             {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <div class="space-y-1.5">
                            <label class="text-[8px] font-black text-white/30 uppercase tracking-[0.3em] ml-1">Identity Node</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-white/20">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path></svg>
                                </div>
                                <input type="email" name="email" required 
                                    class="input-divine w-full rounded-xl pl-10 pr-4 py-3 placeholder-white/10 text-[11px] font-semibold tracking-wide"
                                    placeholder="your-id@system.node">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex justify-between items-center px-1">
                                <label class="text-[8px] font-black text-white/30 uppercase tracking-[0.3em]">Security Key</label>
                                <a href="#" class="text-[7px] font-bold text-accent-cyan hover:text-cyan-300 tracking-wider">RECOVER</a>
                            </div>
                            <div class="relative">
                                 <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-white/20">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                </div>
                                <input type="password" name="password" required 
                                    class="input-divine w-full rounded-xl pl-10 pr-4 py-3 placeholder-white/10 text-[11px] font-semibold tracking-[0.3em]"
                                    placeholder="••••••••">
                            </div>
                        </div>

                        <div class="pt-1">
                            <button type="submit" 
                                class="btn-divine w-full py-3.5 rounded-xl relative overflow-hidden group shadow-2xl">
                                <span class="relative z-10 flex items-center justify-center gap-3 text-[9px] tracking-[0.5em]">
                                    INITIALIZE
                                    <svg class="w-3.5 h-3.5 text-primary-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </span>
                                <div class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                            </button>
                        </div>
                    </form>

                    <!-- Quote -->
                    <div class="mt-6 text-center border-t border-white/5 pt-4">
                        <p class="text-[8px] font-serif italic text-white/40 tracking-wider leading-relaxed px-4">
                            "And seek help through patience and prayer..."
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Branding Status Footer -->
            <div class="flex justify-between items-center px-4">
                <span class="text-[7px] font-black text-white/20 tracking-[0.4em] uppercase">Node CORE v4.0.2</span>
                <div class="flex gap-1">
                    <span class="w-1 h-1 rounded-full bg-accent-cyan opacity-20"></span>
                    <span class="w-1 h-1 rounded-full bg-accent-cyan opacity-20"></span>
                    <span class="w-1 h-1 rounded-full bg-accent-cyan opacity-20"></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
