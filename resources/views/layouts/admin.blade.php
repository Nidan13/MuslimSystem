<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Muslim Spirit</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;800&family=Outfit:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            background-color: #062d38; /* Slightly darker for depth */
            color: white;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.3);
            border-right: 1px solid rgba(0, 242, 255, 0.1);
        }
        
        .sidebar-link {
            position: relative;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            color: rgba(255, 255, 255, 0.5);
            margin: 0 16px;
            border-radius: 12px;
        }

        .sidebar-link:hover {
            background: rgba(0, 242, 255, 0.05);
            color: rgba(255, 255, 255, 0.9);
            transform: translateX(4px);
        }

        .sidebar-link.active {
            background: linear-gradient(90deg, rgba(0, 242, 255, 0.15) 0%, rgba(0, 242, 255, 0.05) 100%);
            color: #00F2FF;
            box-shadow: 0 4px 15px -3px rgba(0, 242, 255, 0.2);
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: -4px;
            top: 25%;
            height: 50%;
            width: 3px;
            background: #00F2FF;
            border-radius: 0 4px 4px 0;
            box-shadow: 0 0 10px #00F2FF;
        }
        
        .sidebar-group-label {
            padding: 24px 32px 8px 32px;
            font-family: 'Cinzel', serif;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.3em;
            color: rgba(0, 242, 255, 0.4);
        }

        .icon-glow {
            filter: drop-shadow(0 0 4px rgba(0, 242, 255, 0.4));
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 20px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(0, 242, 255, 0.3); }

        /* Text Utilities */
        .text-teal-main { color: #0E5F71; }
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
                        'nu-teal': '#008b76',
                        'nu-indigo': '#0a2f4c',
                        teal: { 
                            950: '#062d38',
                            900: '#0E5F71',
                            800: '#0f4c5c',
                        },
                        cyan: {
                            400: '#00F2FF',
                        },
                        gold: { 
                            400: '#F59E0B', 
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
            <div class="flex items-center gap-5">
                <div class="relative group cursor-pointer transition-all duration-500">
                    <!-- Soft Ambient Glow -->
                    <div class="absolute -inset-1 bg-cyan-400/10 rounded-full blur-lg opacity-40 group-hover:opacity-100 transition-opacity"></div>
                    
                    <!-- Soft Glass Container for Logo -->
                    <div class="w-16 h-16 rounded-2xl bg-white/5 backdrop-blur-md border border-white/10 p-2.5 flex items-center justify-center relative z-10 shadow-xl shadow-black/20 group-hover:border-cyan-400/30 transition-all duration-500">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500">
                    </div>
                </div>
                <div class="flex flex-col">
                    <h1 class="text-xl font-serif font-black text-white tracking-widest uppercase leading-none">Muslim</h1>
                    <p class="text-[8px] text-cyan-400 uppercase tracking-[0.5em] font-bold mt-1">System Node</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav id="sidebar-nav" class="flex-1 space-y-1 overflow-y-auto pb-6 custom-scrollbar">
            
            <div class="sidebar-group-label" style="letter-spacing: 0.2rem;">Pusat Kontrol</div>
            
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large w-5 {{ request()->routeIs('admin.dashboard') ? 'icon-glow' : '' }}"></i>
                <span>Dashboard Admin</span>
            </a>

            <a href="{{ route('admin.landing-page.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.landing-page.*') ? 'active' : '' }}">
                <i class="fas fa-laptop-code w-5 {{ request()->routeIs('admin.landing-page.*') ? 'icon-glow' : '' }}"></i>
                <span>Landing Page CMS</span>
            </a>

            <div class="sidebar-group-label" style="letter-spacing: 0.2rem;">Sistem Misi & Tugas</div>

            <a href="{{ route('admin.quests.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.quests.*') ? 'active' : '' }}">
                <i class="fas fa-scroll w-5 {{ request()->routeIs('admin.quests.*') ? 'icon-glow' : '' }}"></i>
                <span>Manajemen Misi</span>
            </a>

            <a href="{{ route('admin.dungeons.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.dungeons.*') ? 'active' : '' }}">
                <i class="fas fa-dungeon w-5 {{ request()->routeIs('admin.dungeons.*') ? 'icon-glow' : '' }}"></i>
                <span>Misi Circle (Raid)</span>
            </a>

            <a href="{{ route('admin.daily-tasks.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.daily-tasks.*') ? 'active' : '' }}">
                <i class="fas fa-tasks w-5 {{ request()->routeIs('admin.daily-tasks.*') ? 'icon-glow' : '' }}"></i>
                <span>Tugas Harian</span>
            </a>

            <div class="sidebar-group-label" style="letter-spacing: 0.2rem;">Komunitas & Informasi</div>

            <a href="{{ route('admin.hunters.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.hunters.*') ? 'active' : '' }}">
                <i class="fas fa-users w-5 {{ request()->routeIs('admin.hunters.*') ? 'icon-glow' : '' }}"></i>
                <span>Daftar Hunter</span>
            </a>

            <a href="{{ route('admin.circles.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.circles.*') ? 'active' : '' }}">
                <i class="fas fa-circle-nodes w-5 {{ request()->routeIs('admin.circles.*') ? 'icon-glow' : '' }}"></i>
                <span>Grup Circle</span>
            </a>

            <a href="{{ route('admin.landing-page.news.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.landing-page.news.*') ? 'active' : '' }}">
                <i class="fas fa-newspaper w-5 {{ request()->routeIs('admin.landing-page.news.*') ? 'icon-glow' : '' }}"></i>
                <span>Berita Utama</span>
            </a>

            <a href="{{ route('admin.islamic-videos.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.islamic-videos.*') ? 'active' : '' }}">
                <i class="fas fa-play-circle w-5 {{ request()->routeIs('admin.islamic-videos.*') ? 'icon-glow' : '' }}"></i>
                <span>Media Kajian</span>
            </a>

            <div class="sidebar-group-label" style="letter-spacing: 0.2rem;">Ekonomi & Finansial</div>

            <a href="{{ route('admin.shop.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.shop.*') ? 'active' : '' }}">
                <i class="fas fa-store w-5 {{ request()->routeIs('admin.shop.*') ? 'icon-glow' : '' }}"></i>
                <span>Toko Item</span>
            </a>

            <a href="{{ route('admin.payments.manual.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.payments.manual.*') ? 'active' : '' }}">
                <i class="fas fa-receipt w-5 {{ request()->routeIs('admin.payments.manual.*') ? 'icon-glow' : '' }}"></i>
                <span>Deposit Manual</span>
            </a>

            <a href="{{ route('admin.withdrawals.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.withdrawals.*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar w-5 {{ request()->routeIs('admin.withdrawals.*') ? 'icon-glow' : '' }}"></i>
                <span>Penarikan Dana</span>
            </a>

            <a href="{{ route('admin.affiliates.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.affiliates.*') ? 'active' : '' }}">
                <i class="fas fa-shuttle-space w-5 {{ request()->routeIs('admin.affiliates.*') ? 'icon-glow' : '' }}"></i>
                <span>Data Afiliasi</span>
            </a>

            <a href="{{ route('admin.reports.revenue') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.reports.revenue') ? 'active' : '' }}">
                <i class="fas fa-chart-pie w-5 {{ request()->routeIs('admin.reports.revenue') ? 'icon-glow' : '' }}"></i>
                <span>Laporan Revenue</span>
            </a>

            <a href="{{ route('admin.distribution-categories.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.distribution-categories.*') ? 'active' : '' }}">
                <i class="fas fa-layer-group w-5 {{ request()->routeIs('admin.distribution-categories.*') ? 'icon-glow' : '' }}"></i>
                <span>Master Alokasi SHU</span>
            </a>

            <div class="sidebar-group-label" style="letter-spacing: 0.2rem;">Manajemen Donasi</div>

            <a href="{{ route('admin.donations.submissions') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->is('admin/donations/submissions') ? 'active' : '' }}">
                <i class="fas fa-file-import w-5 {{ request()->is('admin/donations/submissions') ? 'icon-glow' : '' }}"></i>
                <span>Pengajuan Baru</span>
            </a>

            <a href="{{ route('admin.donations.my-campaigns') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->is('admin/donations/my-campaigns') ? 'active' : '' }}">
                <i class="fas fa-hand-holding-heart w-5 {{ request()->is('admin/donations/my-campaigns') ? 'icon-glow' : '' }}"></i>
                <span>Donasi Gua</span>
            </a>

            <a href="{{ route('admin.donations.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ (request()->routeIs('admin.donations.*') && !request()->is('admin/donations/submissions') && !request()->is('admin/donations/my-campaigns') && !request()->is('admin/donations/organizers')) ? 'active' : '' }}">
                <i class="fas fa-list-ul w-5 {{ (request()->routeIs('admin.donations.*') && !request()->is('admin/donations/submissions') && !request()->is('admin/donations/my-campaigns') && !request()->is('admin/donations/organizers')) ? 'icon-glow' : '' }}"></i>
                <span>Daftar Kampanye</span>
            </a>

            <a href="{{ route('admin.donation-reports.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.donation-reports.*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-check w-5 {{ request()->routeIs('admin.donation-reports.*') ? 'icon-glow' : '' }}"></i>
                <span>Laporan Penyaluran</span>
            </a>

            <a href="{{ route('admin.donations.organizers') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->is('admin/donations/organizers') ? 'active' : '' }}">
                <i class="fas fa-user-tie w-5 {{ request()->is('admin/donations/organizers') ? 'icon-glow' : '' }}"></i>
                <span>Penyelenggara</span>
            </a>

            <div class="sidebar-group-label" style="letter-spacing: 0.2rem;">Master Data</div>

            <a href="{{ route('admin.quest-types.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.quest-types.*') ? 'active' : '' }}">
                <i class="fas fa-layer-group w-5 {{ request()->routeIs('admin.quest-types.*') ? 'icon-glow' : '' }}"></i>
                <span>Tipe Misi</span>
            </a>

            <a href="{{ route('admin.daily-task-categories.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.daily-task-categories.*') ? 'active' : '' }}">
                <i class="fas fa-tags w-5 {{ request()->routeIs('admin.daily-task-categories.*') ? 'icon-glow' : '' }}"></i>
                <span>Kategori Tugas</span>
            </a>

            <a href="{{ route('admin.headline-categories.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.headline-categories.*') ? 'active' : '' }}">
                <i class="fas fa-folder-open w-5 {{ request()->routeIs('admin.headline-categories.*') ? 'icon-glow' : '' }}"></i>
                <span>Kategori Berita</span>
            </a>

            <a href="{{ route('admin.islamic-video-categories.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.islamic-video-categories.*') ? 'active' : '' }}">
                <i class="fas fa-film w-5 {{ request()->routeIs('admin.islamic-video-categories.*') ? 'icon-glow' : '' }}"></i>
                <span>Kategori Media</span>
            </a>

            <a href="{{ route('admin.dungeon-types.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.dungeon-types.*') ? 'active' : '' }}">
                <i class="fas fa-door-open w-5 {{ request()->routeIs('admin.dungeon-types.*') ? 'icon-glow' : '' }}"></i>
                <span>Jenis Raid</span>
            </a>

            <div class="sidebar-group-label" style="letter-spacing: 0.2rem;">Konfigurasi Sistem</div>

            <a href="{{ route('admin.settings.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fas fa-cogs w-5 {{ request()->routeIs('admin.settings.*') ? 'icon-glow' : '' }}"></i>
                <span>Sistem Pembayaran</span>
            </a>

            <a href="{{ route('admin.prayers.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.prayers.*') ? 'active' : '' }}">
                <i class="fas fa-clock w-5 {{ request()->routeIs('admin.prayers.*') ? 'icon-glow' : '' }}"></i>
                <span>Setting Sholat</span>
            </a>

            <a href="{{ route('admin.prayer-logs.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.prayer-logs.*') ? 'active' : '' }}">
                <i class="fas fa-square-poll-vertical w-5 {{ request()->routeIs('admin.prayer-logs.*') ? 'icon-glow' : '' }}"></i>
                <span>Log Ibadah</span>
            </a>

            <a href="{{ route('admin.rank-tiers.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.rank-tiers.*') ? 'active' : '' }}">
                <i class="fas fa-medal w-5 {{ request()->routeIs('admin.rank-tiers.*') ? 'icon-glow' : '' }}"></i>
                <span>Rank & Pangkat</span>
            </a>

            <a href="{{ route('admin.level-configs.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.level-configs.*') ? 'active' : '' }}">
                <i class="fas fa-chart-line w-5 {{ request()->routeIs('admin.level-configs.*') ? 'icon-glow' : '' }}"></i>
                <span>Skala Level</span>
            </a>

            <a href="{{ route('admin.activity-logs.index') }}" class="sidebar-link flex items-center gap-4 px-5 py-3 text-sm font-bold {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                <i class="fas fa-satellite-dish w-5 {{ request()->routeIs('admin.activity-logs.*') ? 'icon-glow' : '' }}"></i>
                <span>Log Aktivitas</span>
            </a>

        </nav>

        <!-- Logout Section -->
        <div class="px-6 py-8 border-t border-white/5 bg-teal-950/30">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="w-full flex items-center justify-center gap-3 px-4 py-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 font-black text-[10px] tracking-widest hover:bg-red-500 hover:text-white transition-all shadow-lg hover:shadow-red-500/40 group">
                    <i class="fas fa-power-off transition-transform group-hover:rotate-90"></i>
                    LOGOUT
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden relative">
        <!-- Top Header - Clean White -->
        <header class="h-24 bg-white border-b-2 border-slate-100 flex items-center justify-between px-10 z-40 sticky top-0 shadow-sm">
            <div>
                 <p class="text-[10px] font-bold text-teal-900/40 uppercase tracking-[0.2em] mb-1">Node Administratif</p>
                <h2 class="text-2xl font-serif font-black text-teal-900 tracking-tight uppercase">
                    @yield('title', 'Ringkasan Utama')
                </h2>
            </div>
            <div class="flex items-center gap-10">  

                <div class="flex items-center gap-6">
                    <button class="relative p-2 bg-slate-50 rounded-xl border-2 border-slate-100 hover:bg-slate-100 transition-colors">
                        <i class="fas fa-bell text-teal-900 text-xl icon-glow"></i>
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-cyan-400 rounded-full border-2 border-white"></span>
                    </button>
                    
                    <div class="flex items-center gap-3 pl-6 border-l-2 border-slate-100">
                         <div class="text-right hidden sm:block">
                            <p class="text-xs font-black text-teal-900 uppercase tracking-wider">{{ auth()->user()->username ?? 'Admin' }}</p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Tingkat Otoritas</p>
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
                <!-- Alert digantikan oleh SweetAlert Config di Layout -->

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

        // Global SweetAlert Toast Configuration
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: 'rgba(255, 255, 255, 0.98)',
            color: '#0f4c5c',
            customClass: {
                popup: 'rounded-[20px] shadow-2xl border-2 border-slate-100 !px-6 !py-4 backdrop-blur-md',
                title: '!text-sm !font-black !uppercase !tracking-widest !mt-1',
                icon: '!scale-75'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Trigger Success Alert
        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            });
        @endif

        // Trigger Error Alert
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal!',
                html: `
                    <ul class="space-y-1 mt-4 text-left">
                        @foreach ($errors->all() as $error)
                            <li class="text-[11px] font-bold text-rose-500 uppercase tracking-wider flex items-start gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400 mt-1 shrink-0"></span>
                                <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                `,
                customClass: {
                    popup: 'rounded-[32px] border-2 border-rose-100 shadow-2xl',
                    title: 'text-2xl font-serif font-black text-rose-600',
                    confirmButton: 'w-full py-4 bg-rose-500 hover:bg-rose-600 border border-rose-400 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg hover:shadow-rose-500/30'
                },
                buttonsStyling: false
            });
        @endif

        // Global SweetAlert2 Delete Confirmation
        function confirmDelete(button, entityName) {
            Swal.fire({
                html: `
                    <div class="flex flex-col items-center pb-6 mb-6 border-b border-slate-100">
                        <div class="w-20 h-20 rounded-full bg-rose-50 border-4 border-white shadow-[0_0_20px_rgba(244,63,94,0.2)] flex items-center justify-center mb-4 relative">
                            <div class="absolute inset-0 rounded-full border border-rose-200 animate-ping opacity-20"></div>
                            <i class="fas fa-trash-alt text-3xl text-rose-500"></i>
                        </div>
                        <h2 class="text-2xl font-serif font-black text-teal-900 tracking-widest uppercase mb-2">Hapus Entitas?</h2>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Tindakan ini permanen & tak dapat dipulihkan</p>
                    </div>
                    <div class="bg-rose-50/50 rounded-2xl p-5 border border-rose-100/50 text-center relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-16 h-16 bg-rose-500/5 rounded-full blur-xl"></div>
                        <p class="text-xs font-bold text-slate-500 mb-3 uppercase tracking-widest">Target Penghapusan:</p>
                        <div class="inline-flex items-center gap-3 bg-white px-5 py-3 rounded-xl border border-rose-100 shadow-sm relative z-10">
                            <div class="w-8 h-8 rounded-lg bg-teal-900 text-white flex items-center justify-center font-serif font-black text-sm shadow-md">
                                ${entityName ? entityName.charAt(0).toUpperCase() : '?'}
                            </div>
                            <span class="text-base font-black text-rose-600 tracking-widest uppercase">${entityName || 'Data Terpilih'}</span>
                        </div>
                    </div>
                `,
                showConfirmButton: true,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check-circle mr-2 opacity-70"></i> Konfirmasi',
                cancelButtonText: 'Batalkan',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-[40px] border border-slate-100 shadow-2xl pb-8 px-8 pt-8 overflow-hidden bg-white/95 backdrop-blur-xl',
                    actions: 'mt-8 flex gap-4 w-full justify-center',
                    confirmButton: 'flex-1 py-4 bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg hover:shadow-rose-500/40 hover:-translate-y-0.5',
                    cancelButton: 'flex-1 py-4 bg-slate-50 border-2 border-slate-100 text-slate-400 hover:bg-slate-100 hover:text-teal-900 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm hover:-translate-y-0.5'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            });
        }

        // Global SweetAlert2 Approve Confirmation
        function confirmApprove(button, username) {
            Swal.fire({
                html: `
                    <div class="flex flex-col items-center pb-6 mb-6 border-b border-slate-100">
                        <div class="w-20 h-20 rounded-full bg-emerald-50 border-4 border-white shadow-[0_0_20px_rgba(16,185,129,0.2)] flex items-center justify-center mb-4 relative">
                            <div class="absolute inset-0 rounded-full border border-emerald-200 animate-ping opacity-20"></div>
                            <i class="fas fa-shield-check text-3xl text-emerald-500"></i>
                        </div>
                        <h2 class="text-2xl font-serif font-black text-teal-900 tracking-widest uppercase mb-2">Setujui Pembayaran?</h2>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Saldo pengguna akan bertambah setelah disetujui</p>
                    </div>
                    <div class="bg-emerald-50/50 rounded-2xl p-5 border border-emerald-100/50 text-center relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-16 h-16 bg-emerald-500/5 rounded-full blur-xl"></div>
                        <p class="text-xs font-bold text-slate-500 mb-3 uppercase tracking-widest">Pembayaran dari:</p>
                        <div class="inline-flex items-center gap-3 bg-white px-5 py-3 rounded-xl border border-emerald-100 shadow-sm relative z-10">
                            <div class="w-8 h-8 rounded-lg bg-teal-900 text-white flex items-center justify-center font-serif font-black text-sm shadow-md">
                                ${username ? username.charAt(0).toUpperCase() : '?'}
                            </div>
                            <span class="text-base font-black text-emerald-600 tracking-widest uppercase">${username || 'User'}</span>
                        </div>
                    </div>
                `,
                showConfirmButton: true,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check-circle mr-2 opacity-70"></i> Setujui',
                cancelButtonText: 'Batalkan',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-[40px] border border-slate-100 shadow-2xl pb-8 px-8 pt-8 overflow-hidden bg-white/95 backdrop-blur-xl',
                    actions: 'mt-8 flex gap-4 w-full justify-center',
                    confirmButton: 'flex-1 py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg hover:shadow-emerald-500/40 hover:-translate-y-0.5',
                    cancelButton: 'flex-1 py-4 bg-slate-50 border-2 border-slate-100 text-slate-400 hover:bg-slate-100 hover:text-teal-900 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm hover:-translate-y-0.5'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            });
        }
    </script>
</body>
</html>