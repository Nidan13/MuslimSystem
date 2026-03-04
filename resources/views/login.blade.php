<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muslim System | Divine Access</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-accent: #14b8a6;
            --secondary-cyan: #22d3ee;
            --glass-card: rgba(0, 0, 0, 0.4);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            width: 100%;
            height: 100%;
            overflow: hidden;
            font-family: 'Outfit', sans-serif;
            background-color: #020b0d;
        }

        /* 1. Full Image Background */
        .viewport-bg {
            position: fixed;
            inset: 0;
            z-index: 1;
        }

        .img-canvas {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset('images/bg/login-bg-final.jpg') }}');
            background-size: cover;
            background-position: center bottom;
            background-repeat: no-repeat;
            filter: brightness(0.85) contrast(1.1);
        }

        /* 2. Premium Content Wrapper */
        .page-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: radial-gradient(circle at center, transparent 40%, rgba(2, 11, 13, 0.4) 100%);
        }

        /* 3. The "Ultra-Compact" Card */
        .compact-card {
            background: var(--glass-card);
            backdrop-filter: blur(30px) saturate(150%);
            border: 1px solid var(--glass-border);
            border-radius: 2.25rem;
            padding: 2.5rem 2.25rem;
            width: 100%;
            max-width: 380px; /* Reduced width even more */
            box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.7);
            animation: card-slide 1s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes card-slide {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .brand-header { text-align: center; margin-bottom: 3rem; }

        .logo-box {
            display: inline-flex;
            background: #fff;
            padding: 0.85rem;
            border-radius: 1.25rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .sys-logo { width: 55px; height: 55px; object-fit: contain; }

        .brand-title {
            font-family: 'Cinzel', serif;
            color: #fff;
            font-size: 1.8rem; /* Balanced size */
            font-weight: 700;
            letter-spacing: 0.3em;
            text-transform: uppercase;
        }

        .brand-subtitle {
            font-size: 0.65rem;
            font-weight: 800;
            color: var(--primary-accent);
            letter-spacing: 0.8em;
            margin-top: 0.4rem;
            opacity: 0.8;
            text-transform: uppercase;
        }

        /* Standard Input Styling */
        .form-unit { margin-bottom: 2.5rem; }

        .form-label {
            display: block;
            font-size: 0.65rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
            letter-spacing: 0.3em;
            margin-bottom: 1rem;
            padding-left: 0.5rem;
        }

        .input-wrap {
            position: relative;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 1.25rem;
            transition: all 0.3s ease;
        }

        .input-wrap:focus-within {
            border-color: var(--secondary-cyan);
            background: rgba(34, 211, 238, 0.04);
            box-shadow: 0 0 20px rgba(34, 211, 238, 0.1);
        }

        .form-input {
            width: 100%;
            background: transparent;
            border: none;
            padding: 1.15rem 1.75rem;
            color: white;
            font-size: 0.95rem;
            outline: none;
        }

        .form-input::placeholder { color: rgba(255, 255, 255, 0.15); }

        .icon-wrap {
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.15);
            font-size: 0.85rem;
        }

        .input-wrap:focus-within .icon-wrap { color: var(--secondary-cyan); }

        /* Sleek Submit Button */
        .btn-divine {
            width: 100%;
            padding: 1.4rem;
            background: linear-gradient(135deg, var(--primary-accent) 0%, #0d9488 100%);
            border: none;
            border-radius: 1.25rem;
            color: white;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.4em;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.4s ease;
            box-shadow: 0 15px 30px -5px rgba(20, 184, 166, 0.4);
        }

        .btn-divine:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px -5px rgba(20, 184, 166, 0.5);
            filter: brightness(1.1);
        }

        .footer-text {
            margin-top: 3.5rem;
            text-align: center;
            opacity: 0.2;
            letter-spacing: 0.5em;
            font-size: 0.6rem;
            font-weight: 700;
            color: #fff;
            text-transform: uppercase;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="viewport-bg">
        <div class="img-canvas"></div>
    </div>
    
    <div class="page-wrapper">
        <div class="compact-card">
            <div class="brand-header">
                <div class="logo-box">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="sys-logo">
                </div>
                <h1 class="brand-title">MUSLIM</h1>
                <p class="brand-subtitle">SYSTEM</p>
            </div>

            @if($errors->any())
                <div class="mb-8 p-4 bg-red-400/10 border border-red-400/20 rounded-2xl flex items-center gap-4 text-red-400 text-[10px] font-black uppercase">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div class="tracking-widest">{{ $errors->first() }}</div>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                
                <div class="form-unit">
                    <label class="form-label">Hunter Identity</label>
                    <div class="input-wrap">
                        <input type="email" name="email" required class="form-input" placeholder="Masukkan ID Hunter" autocomplete="email">
                        <div class="icon-wrap"><i class="fas fa-id-badge"></i></div>
                    </div>
                </div>

                <div class="form-unit">
                    <div class="flex justify-between items-center mb-1 px-3">
                        <label class="form-label m-0">Security Key</label>
                        <a href="#" class="text-[9px] font-bold text-white/30 hover:text-teal-400 transition-colors uppercase tracking-widest">Forgot?</a>
                    </div>
                    <div class="input-wrap">
                        <input type="password" name="password" required class="form-input" placeholder="••••••••" autocomplete="current-password">
                        <div class="icon-wrap"><i class="fas fa-lock"></i></div>
                    </div>
                </div>

                <div class="mb-10 flex justify-center">
                    <label class="flex items-center gap-4 group cursor-pointer">
                        <input type="checkbox" name="remember" class="hidden peer">
                        <div class="w-2.5 h-2.5 rounded-full border border-white/20 peer-checked:bg-teal-500 peer-checked:border-teal-500 transition-all"></div>
                        <span class="text-[10px] font-bold text-white/20 uppercase tracking-[0.2em] group-hover:text-white/40 transition-colors">Remember Session</span>
                    </label>
                </div>

                <button type="submit" class="btn-divine">
                    ACCESS LOGIN
                </button>
            </form>

            <div class="footer-text">
                FAITH • GROWTH • PRECISION
            </div>
        </div>
    </div>
</body>
</html>
