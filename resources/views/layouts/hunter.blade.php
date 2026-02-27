<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The System | System Interface</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #020617; }
        .system-font { font-family: 'Outfit', sans-serif; }
        .glow-orange { box-shadow: 0 0 20px rgba(249, 115, 22, 0.3); }
        .glow-blue { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
        .text-glow { text-shadow: 0 0 10px rgba(255, 255, 255, 0.5); }
        .progress-bar { transition: width 1s ease-in-out; }
    </style>
</head>
<body class="text-slate-200 min-h-screen overflow-x-hidden">

    <!-- Top Navigation -->
    <nav class="fixed top-0 w-full z-50 px-6 py-4 flex justify-between items-center bg-slate-950/80 backdrop-blur-xl border-b border-slate-800">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-orange-500 rounded flex items-center justify-center font-black text-white italic">S</div>
            <h1 class="font-black italic uppercase tracking-widest text-orange-500">System Interface</h1>
        </div>
        
        <div class="flex items-center gap-6">
            <div class="hidden md:flex items-center gap-4 text-[10px] font-bold uppercase tracking-widest text-slate-500">
                <a href="{{ route('hunter.dashboard') }}" class="hover:text-orange-500 transition-colors">Status</a>
                <a href="#" class="hover:text-orange-500 transition-colors">Quests</a>
                <a href="#" class="hover:text-orange-500 transition-colors">Dungeons</a>
                <a href="#" class="hover:text-orange-500 transition-colors">Shop</a>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="text-[10px] font-bold border border-red-500/30 bg-red-500/10 text-red-400 px-3 py-1.5 rounded hover:bg-red-500/20 transition-all uppercase italic">Emergency Exit</button>
            </form>
        </div>
    </nav>

    <main class="pt-24 pb-12 px-6 max-w-7xl mx-auto">
        @yield('content')
    </main>

    <!-- Global Sound FX Simulation & Micro-animations could be added here -->
</body>
</html>
