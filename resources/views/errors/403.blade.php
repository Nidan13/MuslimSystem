<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imam Rank Required | Muslim Level Up</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@900&family=Plus+Jakarta+Sans:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --warning: #f59e0b;
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

        /* Rotating Hex-Islamic Background */
        .islamic-grid {
            position: absolute;
            inset: -50%;
            background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgdmlld0JveD0iMCAwIDYwIDYwIj48cGF0aCBkPSJNMzAgMEw0NSA4LjVMNjAgMEw1MS41IDE1TDYwIDMwTDUxLjUgNDVMNjAgNjBMNDUgNTEuNUwzMCA2MEwxNSA1MS41TDAgNjBMOC41IDQ1TDAgMzBMOC41IDE1TDAgMEwxNSA4LjVMMzAgMFoiIGZpbGw9Im5vbmUiIHN0cm9rZT0icmdiYSgyNDUsIDE1OCwgMTEsIDAuMDUpIiBzdHJva2Utd2lkdGg9IjEiLz48L3N2Zz4=');
            background-size: 80px 80px;
            z-index: 0;
            animation: rotate-pattern 150s linear infinite;
        }
        @keyframes rotate-pattern {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Guardian Character Section */
        .guardian-container {
            position: relative;
            z-index: 20;
            animation: float-guardian 6s ease-in-out infinite;
        }
        @keyframes float-guardian {
            0%, 100% { transform: translateY(0); filter: drop-shadow(0 0 30px rgba(245, 158, 11, 0.2)); }
            50% { transform: translateY(-25px); filter: drop-shadow(0 0 60px rgba(245, 158, 11, 0.4)); }
        }

        /* Laser Scan Effect */
        .scanner-beam {
            position: absolute;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--warning), transparent);
            box-shadow: 0 0 20px var(--warning);
            z-index: 30;
            top: 10%;
            animation: scan-move 4s ease-in-out infinite;
            opacity: 0.8;
            pointer-events: none;
        }
        @keyframes scan-move {
            0%, 100% { top: 20%; opacity: 0; }
            50% { top: 80%; opacity: 1; }
        }

        .btn-guardian {
            background: var(--warning);
            color: black;
            box-shadow: 0 0 30px rgba(245, 158, 11, 0.4);
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .btn-guardian:hover {
            transform: scale(1.1) rotate(2deg);
            box-shadow: 0 0 60px rgba(245, 158, 11, 0.8);
            letter-spacing: 0.1em;
        }

        .marker-system {
            background: rgba(245, 158, 11, 0.05);
            border: 1px solid rgba(245, 158, 11, 0.2);
            backdrop-filter: blur(15px);
        }

        .mosque-glow {
            position: absolute;
            bottom: 0px;
            width: 100%;
            height: 300px;
            background: linear-gradient(to top, rgba(245, 158, 11, 0.1), transparent);
            mask-image: linear-gradient(to top, black, transparent);
            z-index: 1;
            pointer-events: none;
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center p-6">
    <div class="islamic-grid"></div>
    <div class="mosque-glow"></div>

    <div class="relative z-30 flex flex-col items-center text-center max-w-4xl w-full">
        <!-- Muslim Guardian Character Section -->
        <div class="guardian-container mb-12 relative group">
            <div class="scanner-beam"></div>
            <img src="/assets/images/errors/error_403.png" alt="Muslim Guardian" class="w-72 md:w-96 drop-shadow-2xl transition-transform duration-500">
            
            <!-- Security Status Tags -->
            <div class="absolute -top-12 -left-20 p-4 marker-system rounded-3xl text-[9px] font-black uppercase tracking-widest text-amber-500">
                <i class="fas fa-id-badge mr-2"></i> ID: UNKNOWN_HUNTER
            </div>
            <div class="absolute -bottom-8 -right-20 p-4 marker-system rounded-3xl text-[9px] font-black uppercase tracking-widest text-amber-500 animate-pulse">
                REQUIRED: IMAM_OR_ADMIN_RANK
            </div>
        </div>

        <!-- Info Card -->
        <div class="space-y-8">
            <div class="mb-4">
                <span class="px-12 py-4 rounded-2xl border-2 border-warning/50 bg-amber-500/10 text-amber-500 text-xs font-black uppercase tracking-[0.8em] backdrop-blur-md">
                    Access Denied: Restricted Sector
                </span>
            </div>

            <h1 class="header-font text-8xl md:text-9xl font-black italic tracking-tighter leading-none text-white drop-shadow-[0_0_30px_rgba(245,158,11,0.3)]">
                403
            </h1>

            <div class="space-y-6">
                <h2 class="text-4xl md:text-5xl font-black uppercase tracking-tight italic text-amber-500">Bukan Level Ente, Bro!</h2>
                <p class="text-slate-400 font-medium text-lg md:text-xl leading-relaxed max-w-2xl mx-auto backdrop-blur-sm px-4">
                    Gerbang ini dilindungi oleh Firewall Syariah tingkat tinggi. 
                    Ente butuh Rank **Imam** atau akses **Admin** buat bisa bypass portal ini.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-6 pt-10">
                <a href="/" class="btn-guardian px-16 py-6 rounded-full text-xs font-black uppercase tracking-[0.4em] flex items-center gap-5 group shadow-2xl">
                    <i class="fas fa-shield-halved group-hover:scale-125 transition-transform duration-500"></i>
                    Balik ke Zona Aman
                </a>
            </div>
            
            <p class="text-[10px] font-black text-slate-700 uppercase tracking-[0.6em] mt-16 animate-pulse">
                IP_ADDRESS LOGGED. SYSTEM_SECURITY: ACTIVE.
            </p>
        </div>
    </div>

    <!-- Mouse Parallax Script -->
    <script>
        document.addEventListener('mousemove', (e) => {
            const x = (e.clientX / window.innerWidth - 0.5) * 40;
            const y = (e.clientY / window.innerHeight - 0.5) * 40;
            document.querySelector('.guardian-container img').style.transform = `translate(${x}px, ${y}px)`;
            document.querySelector('.islamic-grid').style.transform = `scale(1.1) rotate(${x / 5}deg) translate(${x * -1}px, ${y * -1}px)`;
        });
    </script>
</body>
</html>
