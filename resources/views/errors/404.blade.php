<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost in Hijrah | Muslim Level Up</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@900&family=Plus+Jakarta+Sans:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --cyan: #06b6d4;
            --emerald: #10b981;
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

        /* Islamic Geometric Background */
        .islamic-pattern {
            position: absolute;
            inset: -10%;
            background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMTAwIDEwMCI+PHBhdGggZD0iTTUwIDBMNjUuNDUgMzQuNTRMMTAwIDUwTDY1LjQ1IDY1LjQ2TDUwIDEwMEwzNC41NCA2NS40NkwwIDUwTDM0LjU0IDM0LjU0TDUwIDBaIiBmaWxsPSJub25lIiBzdHJva2U9InJnYmEoNiwgMTgyLCAyMTIsIDAuMDUpIiBzdHJva2Utd2lkdGg9IjEiLz48L3N2Zz4=');
            background-size: 150px 150px;
            opacity: 0.3;
            z-index: 1;
            animation: pattern-move 100s linear infinite;
        }
        @keyframes pattern-move {
            from { transform: rotate(0deg) scale(1); }
            to { transform: rotate(360deg) scale(1.1); }
        }

        /* Character Section */
        .char-box {
            position: relative;
            z-index: 20;
            animation: float 5s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); filter: drop-shadow(0 0 20px rgba(6, 182, 212, 0.2)); }
            50% { transform: translateY(-30px); filter: drop-shadow(0 0 50px rgba(6, 182, 212, 0.4)); }
        }

        /* System UI Elements */
        .system-card {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(25px);
            border: 1px solid rgba(6, 182, 212, 0.2);
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.5);
        }

        .btn-islamic-game {
            background: linear-gradient(135deg, #06b6d4, #10b981);
            color: white;
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 0 30px rgba(6, 182, 212, 0.3);
        }
        .btn-islamic-game:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(16, 185, 129, 0.5);
            letter-spacing: 0.2em;
        }

        .glow-portal {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.1) 0%, transparent 70%);
            filter: blur(100px);
            z-index: 10;
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center">
    <div class="islamic-pattern"></div>
    <div class="glow-portal"></div>

    <div class="relative z-30 flex flex-col items-center text-center px-6 max-w-4xl">
        <!-- Muslim Hunter Character -->
        <div class="char-box mb-8 relative">
            <img src="/assets/images/errors/error_404.png" alt="Muslim Hunter" class="w-64 md:w-80 drop-shadow-2xl">
            <!-- Floating Holographic Tags -->
            <div class="absolute -top-10 -right-20 px-6 py-3 system-card rounded-2xl text-[9px] font-black uppercase tracking-widest text-cyan-400">
                <i class="fas fa-compass animate-spin mr-2"></i> Qibla Sync: Searching...
            </div>
            <div class="absolute -bottom-5 -left-20 px-6 py-3 system-card rounded-2xl text-[9px] font-black uppercase tracking-widest text-emerald-400">
                <i class="fas fa-map-marked-alt mr-2"></i> Mission: Lost in Hijrah
            </div>
        </div>

        <!-- Info Card -->
        <div class="space-y-8">
            <div class="mb-4">
                <span class="px-8 py-3 rounded-full border border-cyan-500/30 bg-cyan-500/10 text-cyan-400 text-[10px] font-black uppercase tracking-[0.5em] animate-pulse">
                    Path Mismatch Detected
                </span>
            </div>

            <h1 class="header-font text-8xl md:text-9xl font-black italic tracking-tighter leading-none text-white select-none drop-shadow-[0_10px_30px_rgba(6,182,212,0.4)]">
                404
            </h1>

            <div class="space-y-6">
                <h2 class="text-4xl md:text-5xl font-extrabold uppercase tracking-tight italic text-cyan-300">Salah Jalan Ente, Bro?</h2>
                <p class="text-slate-400 font-medium text-lg md:text-xl leading-relaxed max-w-xl mx-auto backdrop-blur-sm">
                    Jangan khawatir, bahkan Hunter terbaik pun pernah salah arah. 
                    Sekarang waktunya "Istiqomah" dan kembali ke jalur yang benar.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-6 pt-10">
                <a href="/" class="btn-islamic-game px-16 py-6 rounded-[30px] text-xs font-black uppercase tracking-[0.4em] flex items-center gap-5 group">
                    <i class="fas fa-kaaba group-hover:scale-125 transition-transform duration-500"></i>
                    Balik ke Markas
                </a>
                <button onclick="window.location.href = (document.referrer && !document.referrer.includes(window.location.href)) ? document.referrer : '/'" class="px-12 py-6 rounded-[30px] border-2 border-white/10 bg-white/5 hover:bg-white/10 text-xs font-black uppercase tracking-[0.4em] transition-all backdrop-blur-md">
                    Ke Gerbang Sebelumnya
                </button>
            </div>
        </div>
        
        <div class="mt-20 flex items-center gap-16 opacity-30 grayscale hover:grayscale-0 transition-all duration-700">
            <div class="text-center">
                <i class="fas fa-mosque text-3xl mb-3 text-emerald-400"></i>
                <p class="text-[8px] font-black tracking-widest uppercase">System Region</p>
                <p class="text-[8px] font-bold">UMMAH_GATE</p>
            </div>
            <div class="text-center">
                <i class="fas fa-star-and-crescent text-3xl mb-3 text-cyan-400"></i>
                <p class="text-[8px] font-black tracking-widest uppercase">Coordinate</p>
                <p class="text-[8px] font-mono">0xUNDEFINED</p>
            </div>
            <div class="text-center">
                <i class="fas fa-hand-holding-heart text-3xl mb-3 text-red-400"></i>
                <p class="text-[8px] font-black tracking-widest uppercase">Health Status</p>
                <p class="text-[8px] font-bold">WAITING_ADZAN</p>
            </div>
        </div>
    </div>

    <!-- Mouse Interaction Script -->
    <script>
        document.addEventListener('mousemove', (e) => {
            const x = (e.clientX / window.innerWidth - 0.5) * 30;
            const y = (e.clientY / window.innerHeight - 0.5) * 30;
            document.querySelector('.char-box').style.transform = `translate(${x}px, ${y}px)`;
            document.querySelector('.islamic-pattern').style.transform = `scale(1.1) translate(${x * -1}px, ${y * -1}px)`;
        });
    </script>
</body>
</html>
