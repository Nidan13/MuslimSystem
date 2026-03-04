<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Sabr Required | Muslim Level Up</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@900&family=Plus+Jakarta+Sans:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --danger: #ef4444;
            --dark: #020617;
        }
        body {
            background-color: var(--dark);
            color: white;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow: hidden;
            height: 100vh;
        }
        .header-font { font-family: 'Outfit', sans-serif; }

        /* Character Stress/Sabr Animation */
        .char-sabr {
            position: relative;
            z-index: 20;
            animation: vibrate 0.3s cubic-bezier(.36,.07,.19,.97) infinite;
        }
        @keyframes vibrate {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(1px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-2px, 0, 0); }
            40%, 60% { transform: translate3d(2px, 0, 0); }
        }

        /* Calligraphy Sparks Overlay */
        .calligraphy-storm {
            position: absolute;
            inset: 0;
            z-index: 10;
            opacity: 0.15;
            pointer-events: none;
            overflow: hidden;
        }
        .calligraphy-text {
            position: absolute;
            font-size: 30px;
            color: var(--danger);
            font-family: serif;
            animation: fall-storm linear infinite;
        }
        @keyframes fall-storm {
            from { transform: translateY(-100px) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            to { transform: translateY(110vh) rotate(360deg); opacity: 0; }
        }

        /* Alert Effects */
        .emergency-strobe {
            position: absolute;
            inset: 0;
            background: rgba(239, 68, 68, 0.05);
            z-index: 5;
            animation: strobe 0.1s infinite;
        }
        @keyframes strobe {
            0%, 100% { opacity: 0; }
            50% { opacity: 1; }
        }

        .btn-sabr {
            background: var(--danger);
            color: white;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 0 30px rgba(239, 68, 68, 0.4);
        }
        .btn-sabr:hover {
            transform: scale(1.1);
            background: #f87171;
            box-shadow: 0 0 60px rgba(239, 68, 68, 0.7);
        }

        .system-marker {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center p-6">
    <div class="emergency-strobe"></div>
    <div id="calligraphy-container" class="calligraphy-storm"></div>

    <div class="relative z-30 flex flex-col items-center text-center max-w-4xl w-full">
        <!-- Character Overload Section -->
        <div class="char-sabr mb-12 relative group">
            <img src="/assets/images/errors/error_500.png" alt="Muslim Hunter Sabr" class="w-64 md:w-80 transition-transform duration-300">
            <!-- Floating System Logs -->
            <div class="absolute -top-10 -right-10 p-4 system-marker rounded-2xl text-[9px] font-black uppercase tracking-widest text-red-500 animate-pulse">
                STATUS: SABR_MODE_ACTIVE
            </div>
            <div class="absolute -bottom-5 -left-10 p-4 system-marker rounded-2xl text-[9px] font-black uppercase tracking-widest text-red-300">
                MANA_LEAK: CRITICAL_ERR
            </div>
        </div>

        <!-- Info Section -->
        <div class="space-y-8">
            <div class="mb-4">
                <span class="px-10 py-4 rounded-full border-2 border-red-500 bg-red-500/20 text-red-500 text-xs font-black uppercase tracking-[0.6em] animate-bounce">
                    CRITICAL SYSTEM IBTILA
                </span>
            </div>

            <h1 class="header-font text-8xl md:text-9xl font-black italic tracking-tighter leading-none text-white drop-shadow-[0_0_30px_rgba(239,68,68,0.5)]">
                500
            </h1>

            <div class="space-y-6">
                <h2 class="text-4xl md:text-5xl font-black uppercase tracking-tight italic text-red-500">Aduuh, Ujian Bro!</h2>
                <p class="text-slate-400 font-medium text-lg md:text-xl leading-relaxed max-w-2xl mx-auto backdrop-blur-sm px-4">
                    Sistem lagi kena "Ibtila" (ujian) di pusat data. 
                    Tunggu sebentar ya, kita lagi coba perbaiki koordinat dunianya. Sabar itu bagian dari Iman.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-6 pt-10">
                <button onclick="window.location.reload()" class="btn-sabr px-16 py-6 rounded-full text-xs font-black uppercase tracking-[0.4em] flex items-center gap-5 group shadow-2xl">
                    <i class="fas fa-redo-alt group-hover:rotate-180 transition-transform duration-700"></i>
                    Re-Sync Iman & Sistem
                </button>
                <a href="/" class="px-12 py-6 rounded-full border-2 border-white/20 hover:bg-white/5 text-xs font-black uppercase tracking-[0.4em] transition-all backdrop-blur-md">
                    Istighfar & Balik
                </a>
            </div>
        </div>
        
        <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-6 opacity-30">
            <div class="p-6 system-marker rounded-3xl">
                <p class="text-[9px] font-black tracking-widest text-red-500 uppercase">Iman Load</p>
                <div class="h-1 bg-red-500 w-full mt-2 animate-pulse"></div>
            </div>
            <div class="p-6 system-marker rounded-3xl">
                <p class="text-[9px] font-black tracking-widest text-red-500 uppercase">Syukur</p>
                <p class="text-xs font-bold font-mono">ENABLD</p>
            </div>
            <div class="p-6 system-marker rounded-3xl">
                <p class="text-[9px] font-black tracking-widest text-red-500 uppercase">Tawakal</p>
                <p class="text-xs font-bold font-mono">SYNCING</p>
            </div>
            <div class="p-6 system-marker rounded-3xl">
                <p class="text-[9px] font-black tracking-widest text-red-500 uppercase">Region</p>
                <p class="text-xs font-bold font-mono italic">S-RANK</p>
            </div>
        </div>
    </div>

    <!-- Generate Calligraphy Rain Script -->
    <script>
        const container = document.getElementById('calligraphy-container');
        const phrases = ["صبر", "إيمان", "الله", "توكل", "هجرة", "أمل"];
        
        setInterval(() => {
            const el = document.createElement('div');
            el.className = 'calligraphy-text';
            el.innerText = phrases[Math.floor(Math.random() * phrases.length)];
            el.style.left = Math.random() * 100 + 'vw';
            el.style.animationDuration = (Math.random() * 3 + 2) + 's';
            el.style.fontSize = Math.random() * 40 + 20 + 'px';
            container.appendChild(el);
            setTimeout(() => el.remove(), 5000);
        }, 150);
    </script>
</body>
</html>
