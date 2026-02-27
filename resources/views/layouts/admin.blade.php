<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Muslim Spirit</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;800&family=Outfit:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Light Theme Background with Geometric Hint */
        .bg-islamic-light {
            background-color: #f8fafc; /* Slate 50 */
            background-image: 
                radial-gradient(circle at 0% 0%, rgba(15, 76, 92, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(212, 175, 55, 0.05) 0%, transparent 50%);
        }
        
        /* White Glass Panel for Mechanics */
        .glass-panel {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(16px);
            border: 2px solid #e2e8f0;
            box-shadow: 0 4px 20px -5px rgba(15, 76, 92, 0.05);
        }

        /* Sidebar: Deep Deep Teal */
        .sidebar {
            background-color: #093b48;
            color: white;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.3);
            border-right: 1px solid rgba(34, 211, 238, 0.1);
        }
        
        .sidebar-link {
            position: relative;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            color: rgba(255, 255, 255, 0.5);
            margin: 0 16px;
            border-radius: 12px;
        }

        .sidebar-link:hover {
            background: rgba(34, 211, 238, 0.05);
            color: rgba(255, 255, 255, 0.9);
            transform: translateX(4px);
        }

        .sidebar-link.active {
            background: linear-gradient(90deg, rgba(34, 211, 238, 0.15) 0%, rgba(34, 211, 238, 0.05) 100%);
            color: #22d3ee;
            box-shadow: 0 4px 15px -3px rgba(34, 211, 238, 0.2);
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: -4px;
            top: 25%;
            height: 50%;
            width: 3px;
            background: #22d3ee;
            border-radius: 0 4px 4px 0;
            box-shadow: 0 0 10px #22d3ee;
        }
        
        .sidebar-group-label {
            padding: 24px 32px 8px 32px;
            font-family: 'Cinzel', serif;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.3em;
            color: rgba(34, 211, 238, 0.4);
        }

        .icon-glow {
            filter: drop-shadow(0 0 4px rgba(34, 211, 238, 0.4));
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 20px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(34, 211, 238, 0.3); }

        /* Text Utilities */
        .text-teal-main { color: #0f4c5c; }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        serif: ['Cinzel', 'serif'],
                    },
                    colors: {
                        teal: { 
                            950: '#062d38',
                            900: '#093b48',
                            800: '#0f4c5c',
                        },
                        cyan: {
                            400: '#22d3ee',
                        },
                        gold: { 
                            400: '#fbbf24', 
                            500: '#d4a373',
                            600: '#b45309'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-islamic-light text-slate-900 flex overflow-hidden h-screen selection:bg-teal-900 selection:text-white">

    <!-- Sidebar -->
    <aside class="w-72 sidebar flex flex-col h-full z-50 relative">
        <!-- Logo Area -->
        <div class="px-8 py-10 relative z-10 border-b border-white/5">
            <div class="absolute -top-10 -left-10 w-40 h-40 bg-cyan-400/5 rounded-full blur-3xl -z-10"></div>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-400 to-teal-600 flex items-center justify-center shadow-lg shadow-cyan-400/20 group cursor-pointer transition-transform hover:scale-105 active:scale-95">
                    <i class="fas fa-mosque text-white text-xl icon-glow"></i>
                </div>
                <div>
                    <h1 class="text-xl font-serif font-black text-white tracking-widest uppercase leading-none">Muslim</h1>
                    <p class="text-[8px] text-cyan-400 uppercase tracking-[0.5em] font-bold mt-1">System Node</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav id="sidebar-nav" class="flex-1 space-y-1 overflow-y-auto pb-6 custom-scrollbar">
            
            <div class="sidebar-group-label">Core Access</div>
            
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large w-5 {{ request()->routeIs('admin.dashboard') ? 'icon-glow' : '' }}"></i>
                <span>Command Center</span>
            </a>

            <div class="sidebar-group-label">Operations</div>

            <a href="{{ route('admin.quests.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.quests.*') ? 'active' : '' }}">
                <i class="fas fa-scroll w-5 {{ request()->routeIs('admin.quests.*') ? 'icon-glow' : '' }}"></i>
                <span>Active Missions</span>
            </a>

            <a href="{{ route('admin.dungeons.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.dungeons.*') ? 'active' : '' }}">
                <i class="fas fa-dungeon w-5 {{ request()->routeIs('admin.dungeons.*') ? 'icon-glow' : '' }}"></i>
                <span>Rift Gates</span>
            </a>

            <a href="{{ route('admin.shop.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.shop.*') ? 'active' : '' }}">
                <i class="fas fa-store w-5 {{ request()->routeIs('admin.shop.*') ? 'icon-glow' : '' }}"></i>
                <span>Marketplace</span>
            </a>

            <a href="{{ route('admin.islamic-videos.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.islamic-videos.*') ? 'active' : '' }}">
                <i class="fas fa-video w-5 {{ request()->routeIs('admin.islamic-videos.*') ? 'icon-glow' : '' }}"></i>
                <span>Media Archives</span>
            </a>

            <div class="sidebar-group-label">Community</div>

            <a href="{{ route('admin.hunters.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.hunters.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog w-5 {{ request()->routeIs('admin.hunters.*') ? 'icon-glow' : '' }}"></i>
                <span>Hunter Registry</span>
            </a>

            <a href="{{ route('admin.circles.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.circles.*') ? 'active' : '' }}">
                <i class="fas fa-circle-nodes w-5 {{ request()->routeIs('admin.circles.*') ? 'icon-glow' : '' }}"></i>
                <span>Circles Management</span>
            </a>

            <a href="{{ route('admin.daily-tasks.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.daily-tasks.*') ? 'active' : '' }}">
                <i class="fas fa-tasks w-5 {{ request()->routeIs('admin.daily-tasks.*') ? 'icon-glow' : '' }}"></i>
                <span>Discipline Matrix</span>
            </a>

            <div class="sidebar-group-label">Economic Flow</div>

            <a href="{{ route('admin.affiliates.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.affiliates.*') ? 'active' : '' }}">
                <i class="fas fa-hand-holding-heart w-5 {{ request()->routeIs('admin.affiliates.*') ? 'icon-glow' : '' }}"></i>
                <span>Affiliate Registry</span>
            </a>

            <a href="{{ route('admin.withdrawals.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.withdrawals.*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar w-5 {{ request()->routeIs('admin.withdrawals.*') ? 'icon-glow' : '' }}"></i>
                <span>Withdrawal Requests</span>
            </a>

            <div class="sidebar-group-label">Divine Protocols</div>

            <a href="{{ route('admin.prayers.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.prayers.*') ? 'active' : '' }}">
                <i class="fas fa-pray w-5 {{ request()->routeIs('admin.prayers.*') ? 'icon-glow' : '' }}"></i>
                <span>Salat Config</span>
            </a>

            <a href="{{ route('admin.prayer-logs.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.prayer-logs.*') ? 'active' : '' }}">
                <i class="fas fa-history w-5 {{ request()->routeIs('admin.prayer-logs.*') ? 'icon-glow' : '' }}"></i>
                <span>Divine Logs</span>
            </a>

             <div class="sidebar-group-label">System Architecture</div>

            <a href="{{ route('admin.rank-tiers.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.rank-tiers.*') ? 'active' : '' }}">
                <i class="fas fa-id-badge w-5 {{ request()->routeIs('admin.rank-tiers.*') ? 'icon-glow' : '' }}"></i>
                <span>Rank Protocols</span>
            </a>

            <a href="{{ route('admin.dungeon-types.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.dungeon-types.*') ? 'active' : '' }}">
                <i class="fas fa-tags w-5 {{ request()->routeIs('admin.dungeon-types.*') ? 'icon-glow' : '' }}"></i>
                <span>Gate Taxonomy</span>
            </a>

            <a href="{{ route('admin.quest-types.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.quest-types.*') ? 'active' : '' }}">
                <i class="fas fa-layer-group w-5 {{ request()->routeIs('admin.quest-types.*') ? 'icon-glow' : '' }}"></i>
                <span>Quest Archives</span>
            </a>

            <a href="{{ route('admin.level-configs.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.level-configs.*') ? 'active' : '' }}">
                <i class="fas fa-bolt w-5 {{ request()->routeIs('admin.level-configs.*') ? 'icon-glow' : '' }}"></i>
                <span>Power Scaling</span>
            </a>

            <a href="{{ route('admin.activity-logs.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                <i class="fas fa-terminal w-5 {{ request()->routeIs('admin.activity-logs.*') ? 'icon-glow' : '' }}"></i>
                <span>System Logs</span>
            </a>

        </nav>

        <!-- Logout Section -->
        <div class="px-6 py-8 border-t border-white/5 bg-teal-950/30">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="w-full flex items-center justify-center gap-3 px-4 py-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 font-black text-[10px] tracking-widest hover:bg-red-500 hover:text-white transition-all shadow-lg hover:shadow-red-500/40 group">
                    <i class="fas fa-power-off transition-transform group-hover:rotate-90"></i>
                    TERMINATE SESSION
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden relative">
        <!-- Top Header - Clean White -->
        <header class="h-24 bg-white border-b-2 border-slate-100 flex items-center justify-between px-10 z-40 sticky top-0 shadow-sm">
            <div>
                 <p class="text-[10px] font-bold text-teal-900/40 uppercase tracking-[0.2em] mb-1">Administrative Node</p>
                <h2 class="text-2xl font-serif font-black text-teal-900 tracking-tight uppercase">
                    @yield('title', 'Sanctuary Overview')
                </h2>
            </div>
            <div class="flex items-center gap-10">
                
                <!-- Search Mockup -->
                <div class="hidden xl:flex items-center gap-3 bg-slate-50 px-5 py-3 rounded-2xl border-2 border-slate-100 w-80 group focus-within:border-cyan-400 transition-all">
                    <i class="fas fa-search text-slate-300"></i>
                    <span class="text-sm text-slate-400 font-medium">Find a node...</span>
                </div>

                <div class="flex items-center gap-6">
                    <button class="relative p-2 bg-slate-50 rounded-xl border-2 border-slate-100 hover:bg-slate-100 transition-colors">
                        <i class="fas fa-bell text-teal-900 text-xl icon-glow"></i>
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-cyan-400 rounded-full border-2 border-white"></span>
                    </button>
                    
                    <div class="flex items-center gap-3 pl-6 border-l-2 border-slate-100">
                         <div class="text-right hidden sm:block">
                            <p class="text-xs font-black text-teal-900 uppercase tracking-wider">{{ auth()->user()->username ?? 'Admin' }}</p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Authority Level</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-teal-900 text-white flex items-center justify-center font-serif font-bold text-xl shadow-lg shadow-teal-950/20">
                            {{ substr(auth()->user()->username ?? 'A', 0, 1) }}
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dynamic Content -->
        <main class="flex-1 overflow-y-auto p-12 relative bg-slate-50/50">
            <div class="relative z-10 max-w-7xl mx-auto pb-20">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')

    <script>
        // Persist Sidebar Scroll Position
        const sidebarNav = document.getElementById('sidebar-nav');
        if (sidebarNav) {
            // Restore position
            const scrollPos = localStorage.getItem('sidebar-scroll');
            if (scrollPos) {
                sidebarNav.scrollTop = scrollPos;
            }

            // Save position on scroll (throttled)
            let timeout;
            sidebarNav.addEventListener('scroll', () => {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    localStorage.setItem('sidebar-scroll', sidebarNav.scrollTop);
                }, 100);
            });
        }
    </script>
</body>
</html>
