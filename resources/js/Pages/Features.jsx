import React from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import RevealSection from '@/Components/RevealSection';

export default function Features({ appName }) {
    const corePillars = [
        {
            title: "Jadwal Sholat",
            subtitle: "Precise Timing",
            description: "Waktu salat akurat berdasarkan lokasi Anda, lengkap dengan notifikasi adzan yang menenangkan jiwa.",
            icon: "🕌",
            color: "nu-teal"
        },
        {
            title: "Al-Qur'an Digital",
            subtitle: "Divine Guidance",
            description: "Baca, simpan, dan pelajari Al-Qur'an dengan mushaf yang jernih, tajwid berwarna, dan terjemahan lengkap.",
            icon: "📖",
            color: "nu-indigo"
        },
        {
            title: "Hadist Harian",
            subtitle: "Prophetic Wisdom",
            description: "Asupan ilmu setiap hari dari kumpulan hadits shahih yang relevan untuk kehidupan modern.",
            icon: "📜",
            color: "nu-teal"
        },
        {
            title: "Kisah Nabi & Sahabat",
            subtitle: "Islamic Legacy",
            description: "Pelajari sejarah hidup 25 Nabi dan para Sahabat pilihan sebagai teladan dalam beribadah dan berakhlak.",
            icon: "🐪",
            color: "nu-indigo"
        },
        {
            title: "Berita & Artikel",
            subtitle: "Umat Insights",
            description: "Update informasi terkini seputar dunia Islam, artikel keagamaan, dan newsletter eksklusif.",
            icon: "🗞️",
            color: "nu-teal"
        },
        {
            title: "Kajian Islam",
            subtitle: "Learning Path",
            description: "Akses video kajian dari ustadz-ustadz pilihan untuk memperdalam pemahaman ilmu agama Anda.",
            icon: "🎬",
            color: "nu-indigo"
        },
        {
            title: "Mitra & Penyelenggara",
            subtitle: "Ecosystem Partners",
            description: "Bekerja sama dengan berbagai penyelenggara donasi dan mitra terverifikasi untuk kebermanfaatan umat.",
            icon: "🤝",
            color: "nu-teal"
        },
        {
            title: "Kalkulator Zakat",
            subtitle: "Wealth Purity",
            description: "Hitung zakat maal, fitrah, hingga profesi secara otomatis sesuai nishab terkini.",
            icon: "⚖️",
            color: "nu-indigo"
        },
        {
            title: "Arah Kiblat",
            subtitle: "Always Aligned",
            description: "Kompas kiblat presisi tinggi yang memastikan Anda selalu menghadap Ka'bah di mana pun berada.",
            icon: "🕋",
            color: "nu-teal"
        },
        {
            title: "Kalender Hijriah",
            subtitle: "Lunar Cycle",
            description: "Pantau tanggal penting, hari libur Islam, dan jadwal puasa sunnah dalam satu kalender terintegrasi.",
            icon: "🌙",
            color: "nu-indigo"
        },
        {
            title: "Asmaul Husna",
            subtitle: "The 99 Names",
            description: "Pelajari dan hafalkan 99 nama Allah yang indah beserta makna dan faedahnya bagi kehidupan.",
            icon: "✨",
            color: "nu-teal"
        },
        {
            title: "Doa & Dzikir harian",
            subtitle: "Daily Devotion",
            description: "Kumpulan doa harian dari Al-Qur'an dan Sunnah untuk berbagai situasi, lengkap dengan audio.",
            icon: "🤲",
            color: "nu-indigo"
        }
    ];

    const extraModules = [
        {
            title: "Donasi & Zakat",
            subtitle: "Charity Hub",
            description: "Salurkan bantuan Anda melalui kampanye donasi yang terverifikasi dan transparan.",
            icon: "🎁",
            tag: "TRANSFORM"
        },
        {
            title: "Shop & Merchant",
            subtitle: "Halal Economy",
            description: "Dapatkan akses ke produk pilihan di marketplace yang mendukung gaya hidup Muslim Anda.",
            icon: "🛍️",
            tag: "MARKET"
        },
        {
            title: "Statistik Ibadah",
            subtitle: "Data Analytics",
            description: "Visualisasi mendalam grafik ibadah harian Anda untuk evaluasi diri yang terukur.",
            icon: "📊",
            tag: "INSIGHT"
        }
    ];

    const grindingSystem = [
        {
            title: "Daily Task",
            subtitle: "Priority Check",
            description: "Selesaikan daftar tugas harian Anda. Prioritaskan ibadah dan produktivitas dunia agar seimbang.",
            icon: "✅",
            rank: "Scribe"
        },
        {
            title: "Quest System",
            subtitle: "Challenges",
            description: "Selesaikan misi harian dan mingguan untuk melatih konsistensi dan mendapatkan reward spiritual.",
            icon: "⚔️",
            rank: "Warrior"
        },
        {
            title: "Dungeon System",
            subtitle: "Knowledge Raid",
            description: "Uji pemahaman agama Anda melalui tantangan beruntun dengan tingkat kesulitan yang meningkat.",
            icon: "🏰",
            rank: "Grand Master"
        },
        {
            title: "Habit & EXP",
            subtitle: "Progression",
            description: "Lihat visualisasi pertumbuhan iman Anda melalui bar EXP dan streak habit yang tidak boleh terputus.",
            icon: "🔥",
            rank: "Master"
        },
        {
            title: "Rank Tiers",
            subtitle: "Prestige Status",
            description: "Gapai peringkat tertinggi dari Bronze ke Diamond hingga Challenger berdasarkan keistiqomahan Anda.",
            icon: "🏆",
            rank: "Legend"
        },
        {
            title: "Activity Log",
            subtitle: "History Trace",
            description: "Catat setiap progres dan aktivitas spiritual Anda untuk evaluasi diri yang lebih baik.",
            icon: "📜",
            rank: "Chronicler"
        }
    ];

    const futureBlueprint = [
        { title: "Vendor Umroh & Kurban", desc: "Integrasi layanan ibadah besar langsung di genggaman dengan transparansi penuh.", icon: "🕋", glow: "from-amber-400" },
        { title: "2D Character Avatar", desc: "kostumasi avatar Anda dengan kostum yang beragam.", icon: "👤", glow: "from-blue-400" },
        { title: "Pelafalan AI", desc: "Cek tajwid dan kelancaran pelafalan Al-Quran Anda dengan teknologi AI tercanggih.", icon: "🎙️", glow: "from-emerald-400" },
        { title: "Sirah Journey", desc: "Edo-tainment interaktif sejarah perjuangan Rasulullah dengan visual yang memukau.", icon: "🐪", glow: "from-orange-400" },
        { title: "Marketplace Syariah", desc: "Ekosistem belanja produk halal dan kebutuhan Muslim yang terkurasi ketat.", icon: "🛍️", glow: "from-purple-400" },
    ];

    return (
        <>
            <Head title={`Features - ${appName}`} />

            <div className="bg-white overflow-x-hidden">
                {/* HERO SECTION */}
                <section className="pt-48 pb-32 px-6 relative">
                    <div className="absolute top-0 right-0 w-[600px] h-[600px] bg-nu-teal/10 rounded-full blur-[120px] -translate-y-1/2 translate-x-1/2"></div>
                    <div className="max-w-7xl mx-auto text-center relative z-10">
                        <RevealSection direction="down" className="max-w-4xl mx-auto">
                            <p className="text-nu-teal font-black text-[12px] uppercase tracking-[0.5em] mb-8">The Ultimate Toolkit</p>
                            <h1 className="text-5xl lg:text-8xl font-serif font-black uppercase tracking-tighter leading-[0.9] mb-12 text-nu-indigo">
                                Discover Our <br /><span className="text-nu-teal italic-reset">Universe.</span>
                            </h1>
                            <div className="w-24 h-1.5 bg-nu-teal mx-auto rounded-full mb-12"></div>
                            <p className="text-xl lg:text-2xl font-medium leading-relaxed text-slate-500 max-w-2xl mx-auto tracking-tight">
                                Jelajahi ekosistem fitur lengkap yang kami bangun untuk mendampingi setiap langkah hijrah dan pertumbuhan iman Anda.
                            </p>
                        </RevealSection>
                    </div>
                </section>

                {/* SECTION 1: CORE PILLARS (BASICS) */}
                <section className="py-32 px-6">
                    <div className="max-w-7xl mx-auto">
                        <RevealSection className="flex items-center gap-6 mb-20" direction="left">
                            <div className="w-16 h-[2px] bg-nu-teal"></div>
                            <h2 className="text-3xl lg:text-5xl font-serif font-black uppercase text-nu-indigo tracking-tighter">Core Pillars <span className="text-nu-teal italic-reset ml-2">Basics.</span></h2>
                        </RevealSection>

                        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                            {corePillars.map((f, i) => (
                                <RevealSection key={i} delay={`${i * 50}ms`} direction="up" className="group">
                                    <div className="h-full bg-slate-50 p-10 rounded-[3rem] border border-transparent shadow-[0_15px_40px_-20px_rgba(0,0,0,0.05)] hover:bg-white hover:border-nu-teal lg:hover:-translate-y-4 transition-all duration-500 relative overflow-hidden group">
                                        <div className={`absolute -top-10 -right-10 w-32 h-32 bg-${f.color}/5 rounded-full blur-3xl group-hover:bg-nu-teal/10 transition-colors`}></div>
                                        <div className="text-5xl mb-8 transform group-hover:scale-110 group-hover:rotate-6 transition-transform duration-500">{f.icon}</div>
                                        <p className="text-[10px] text-nu-teal font-black uppercase tracking-widest mb-3">{f.subtitle}</p>
                                        <h3 className="text-2xl font-serif font-black uppercase mb-5 text-nu-indigo group-hover:text-nu-teal transition-colors">{f.title}</h3>
                                        <p className="text-slate-500 font-medium text-sm leading-relaxed">{f.description}</p>
                                    </div>
                                </RevealSection>
                            ))}
                        </div>
                    </div>
                </section>

                {/* SECTION 2: CIRCLE & COMMUNITY HIGHLIGHT */}
                <section className="py-32 bg-nu-indigo text-white relative overflow-hidden">
                    <div className="absolute top-0 right-0 w-full h-full opacity-10 pointer-events-none" style={{ backgroundImage: 'url("/images/mosque-pattern.png")', backgroundSize: '600px' }}></div>
                    <div className="max-w-7xl mx-auto px-6 relative z-10">
                        <div className="grid lg:grid-cols-2 gap-20 items-center">
                            <RevealSection direction="right">
                                <p className="text-nu-teal font-black text-[12px] uppercase tracking-[0.5em] mb-8">Umat Collective</p>
                                <h2 className="text-4xl lg:text-7xl font-serif font-black uppercase leading-[0.9] tracking-tighter mb-12">
                                    Circle & <br /><span className="text-nu-teal italic-reset">Misi Bersama.</span>
                                </h2>
                                <p className="text-xl text-white/70 font-medium leading-relaxed mb-12 max-w-lg">
                                    Circle bukan sekadar grup chat biasa. Disini user berkumpul untuk mengerjakan quest dan misi ibadah secara kolektif. Saling menyemangati, saling mengingatkan, dan tumbuh bersama.
                                </p>
                                <div className="space-y-6">
                                    <div className="flex items-center gap-6 p-6 rounded-[2rem] bg-white/5 border border-white/10 hover:bg-white/10 transition-colors group">
                                        <div className="w-16 h-16 rounded-2xl bg-nu-teal/20 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">🎯</div>
                                        <div>
                                            <h4 className="text-lg font-black uppercase">Shared Missions</h4>
                                            <p className="text-white/50 text-xs font-medium">Selesaikan target ibadah grup secara bersama-sama.</p>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-6 p-6 rounded-[2rem] bg-white/5 border border-white/10 hover:bg-white/10 transition-colors group">
                                        <div className="w-16 h-16 rounded-2xl bg-nu-teal/20 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">📈</div>
                                        <div>
                                            <h4 className="text-lg font-black uppercase">Collective Growth</h4>
                                            <p className="text-white/50 text-xs font-medium">Lihat kontribusi setiap anggota terhadap level komunitas.</p>
                                        </div>
                                    </div>
                                </div>
                            </RevealSection>
                            <RevealSection direction="up" delay="300ms">
                                <div className="relative">
                                    <div className="aspect-square bg-nu-teal rounded-[4rem] flex items-center justify-center p-12 overflow-hidden shadow-2xl relative group">
                                        <div className="absolute inset-0 bg-black/20 group-hover:bg-transparent transition-colors duration-500"></div>
                                        <div className="text-center relative z-10">
                                            <div className="text-9xl mb-8 group-hover:scale-110 transition-transform duration-700">🤝</div>
                                            <h3 className="text-3xl font-serif font-black uppercase text-nu-indigo">Join the Circle</h3>
                                            <p className="text-nu-indigo/70 font-bold mt-4">10,000+ Umat Berjuang Bersama</p>
                                        </div>
                                    </div>
                                    <div className="absolute -bottom-10 -right-10 w-48 h-48 bg-white rounded-[3rem] p-8 shadow-2xl rotate-12 flex items-center justify-center text-center">
                                        <p className="text-nu-indigo font-black uppercase leading-tight text-sm">Real-time Unity</p>
                                    </div>
                                </div>
                            </RevealSection>
                        </div>
                    </div>
                </section>

                {/* EXTRA MODULE STORIES SECTION */}
                <section className="py-32 bg-slate-50">
                    <div className="max-w-7xl mx-auto px-6">
                        <RevealSection className="text-center mb-24">
                            <p className="text-nu-teal font-black text-[12px] uppercase tracking-[0.5em] mb-4">Wider Ecosystem</p>
                            <h2 className="text-4xl lg:text-6xl font-serif font-black uppercase tracking-tighter text-nu-indigo">Beyond <span className="text-nu-teal italic-reset">Basics.</span></h2>
                        </RevealSection>
                        <div className="grid md:grid-cols-3 gap-8">
                            {extraModules.map((f, i) => (
                                <RevealSection key={i} delay={`${i * 100}ms`} direction="up">
                                    <div className="p-10 bg-white rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/50 hover:border-nu-teal transition-all group h-full">
                                        <div className="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-3xl mb-8 group-hover:bg-nu-teal group-hover:text-white transition-all duration-500">
                                            {f.icon}
                                        </div>
                                        <div className="flex items-center gap-3 mb-3">
                                            <p className="text-[10px] text-slate-400 font-black uppercase tracking-widest">{f.subtitle}</p>
                                            <span className="px-2 py-0.5 bg-nu-teal/10 text-nu-teal text-[8px] font-black rounded">{f.tag}</span>
                                        </div>
                                        <h4 className="text-xl font-black uppercase text-nu-indigo mb-5 group-hover:text-nu-teal transition-colors leading-none">{f.title}</h4>
                                        <p className="text-slate-500 text-sm font-medium leading-relaxed">{f.description}</p>
                                    </div>
                                </RevealSection>
                            ))}
                        </div>
                    </div>
                </section>

                {/* GRINDING SYSTEM SECTION */}
                <section className="py-48 px-6">
                    <div className="max-w-7xl mx-auto">
                        <RevealSection className="text-center mb-24">
                            <p className="text-nu-teal font-black text-[12px] uppercase tracking-[0.5em] mb-4">Progression Mechanics</p>
                            <h2 className="text-4xl lg:text-6xl font-serif font-black uppercase tracking-tighter text-nu-indigo">Grinding <span className="text-nu-teal italic-reset">System.</span></h2>
                            <p className="text-slate-500 font-medium max-w-2xl mx-auto mt-6">Metode gamifikasi modern untuk menjaga keistiqomahan Anda dalam setiap ritme ibadah harian.</p>
                        </RevealSection>

                        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                            {grindingSystem.map((f, i) => (
                                <RevealSection key={i} delay={`${i * 100}ms`} direction="up">
                                    <div className="bg-white p-10 rounded-[3rem] border border-slate-100 group hover:shadow-2xl hover:shadow-nu-indigo/5 transition-all duration-700 h-full">
                                        <div className="flex items-center gap-4 mb-8">
                                            <div className="w-16 h-16 bg-nu-indigo text-white rounded-2xl flex items-center justify-center text-3xl group-hover:bg-nu-teal group-hover:text-nu-indigo transition-colors duration-500">
                                                {f.icon}
                                            </div>
                                            <div className="h-[2px] flex-grow bg-slate-100"></div>
                                            <span className="px-4 py-1.5 bg-nu-teal/10 text-nu-teal text-[9px] font-black uppercase rounded-full">{f.rank}</span>
                                        </div>
                                        <p className="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-2">{f.subtitle}</p>
                                        <h3 className="text-2xl font-serif font-black uppercase mb-4 text-nu-indigo group-hover:text-nu-teal transition-colors leading-none">{f.title}</h3>
                                        <p className="text-slate-500 font-medium text-sm leading-relaxed">{f.description}</p>

                                        <div className="mt-8 space-y-2">
                                            <div className="flex justify-between text-[8px] font-black uppercase tracking-widest">
                                                <span>Mastery Progress</span>
                                                <span className="text-nu-teal">85%</span>
                                            </div>
                                            <div className="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                                <div className="h-full bg-nu-indigo w-4/5 group-hover:bg-nu-teal group-hover:w-full transition-all duration-1000"></div>
                                            </div>
                                        </div>
                                    </div>
                                </RevealSection>
                            ))}
                        </div>
                    </div>
                </section>

                {/* FUTURE BLUEPRINT (COMING SOON) */}
                <section className="py-48 px-6 bg-nu-indigo relative overflow-hidden">
                    <div className="absolute -top-40 -left-40 w-96 h-96 bg-nu-teal/10 rounded-full blur-[100px]"></div>
                    <div className="absolute -bottom-40 -right-40 w-[500px] h-[500px] bg-sky-500/10 rounded-full blur-[120px]"></div>

                    <div className="max-w-7xl mx-auto relative z-10">
                        <RevealSection className="text-center mb-32">
                            <div className="inline-block px-6 py-2 bg-white/5 border border-white/10 rounded-full mb-8 backdrop-blur-md">
                                <span className="text-[10px] font-black uppercase text-nu-teal tracking-[0.3em]">Project Roadmap 2024-2025</span>
                            </div>
                            <h2 className="text-5xl lg:text-8xl font-serif font-black uppercase tracking-tighter text-white mb-10 leading-[0.85]">
                                Future <br /><span className="text-nu-teal italic-reset">Blueprint.</span>
                            </h2>
                            <p className="text-white/60 font-medium max-w-xl mx-auto text-lg leading-relaxed">
                                Inovasi kami berkembang pesat. Inilah sekilas fitur masa depan yang sedang kami sempurnakan untuk Anda.
                            </p>
                        </RevealSection>

                        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                            {futureBlueprint.map((f, i) => (
                                <RevealSection key={i} delay={`${i * 100}ms`} direction="up">
                                    <div className="bg-white/5 border border-white/10 p-10 rounded-[3rem] backdrop-blur-xl group hover:border-nu-teal transition-all duration-500 relative overflow-hidden h-full">
                                        <div className={`absolute top-0 right-0 w-24 h-24 bg-gradient-to-br ${f.glow} to-transparent opacity-0 group-hover:opacity-20 transition-opacity blur-2xl`}></div>

                                        <div className="flex justify-between items-start mb-10">
                                            <div className="text-5xl transform group-hover:scale-110 group-hover:-rotate-6 transition-transform duration-700">{f.icon}</div>
                                            <span className="text-[8px] font-black uppercase text-nu-teal bg-nu-teal/10 px-3 py-1.5 rounded-full border border-nu-teal/20">Coming Soon</span>
                                        </div>

                                        <h4 className="text-xl font-serif font-black uppercase text-white mb-4 tracking-tight group-hover:text-nu-teal transition-colors">{f.title}</h4>
                                        <p className="text-white/50 text-sm font-medium leading-relaxed group-hover:text-white/70 transition-colors">{f.desc}</p>

                                        <div className="mt-8 flex items-center gap-3">
                                            <div className="w-2 h-2 rounded-full bg-nu-teal animate-pulse"></div>
                                            <span className="text-[9px] font-black text-nu-teal uppercase tracking-widest">In Development Phase</span>
                                        </div>
                                    </div>
                                </RevealSection>
                            ))}
                        </div>
                    </div>
                </section>

                {/* FINAL CALL TO ACTION */}
                <section className="py-48 px-6 text-center">
                    <RevealSection direction="up">
                        <div className="w-24 h-24 bg-nu-teal/10 rounded-full flex items-center justify-center text-4xl mx-auto mb-10 text-nu-teal">🚀</div>
                        <h2 className="text-4xl lg:text-7xl font-serif font-black text-nu-indigo uppercase mb-12 tracking-tighter leading-none">
                            Tunggu Apa Lagi? <br /><span className="text-nu-teal italic-reset">Level Up</span> Sekarang.
                        </h2>
                        <div className="flex justify-center gap-6 flex-wrap">
                            <a href="/downloads/muslim-app.apk" download="muslim-app.apk" className="px-12 py-6 bg-nu-indigo text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-slate-900 transition-all shadow-2xl shadow-nu-indigo/20 flex items-center gap-4 group">
                                <span>Download APK Android</span>
                                <span className="w-5 h-5 bg-nu-teal text-nu-indigo rounded-full flex items-center justify-center transform group-hover:scale-110 transition-transform">↓</span>
                            </a>
                        </div>
                        <p className="mt-12 text-slate-400 text-xs font-bold uppercase tracking-widest">Compatible with Android 8.0 & Above</p>
                    </RevealSection>
                </section>
            </div>
        </>
    );
}

Features.layout = page => <MainLayout children={page} />;