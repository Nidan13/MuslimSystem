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
            title: "Kalkulator Zakat",
            subtitle: "Wealth Purity",
            description: "Hitung zakat maal, fitrah, hingga profesi secara otomatis sesuai nishab terkini.",
            icon: "⚖️",
            color: "nu-indigo"
        },
        {
            title: "Tasbih Digital",
            subtitle: "Heart Constant",
            description: "Sistem counter dzikir yang intuitif dengan feedback haptic untuk membantu Anda tetap fokus.",
            icon: "📿",
            color: "nu-teal"
        },
        {
            title: "Arah Kiblat",
            subtitle: "Always Aligned",
            description: "Kompas kiblat presisi tinggi yang memastikan Anda selalu menghadap Ka'bah di mana pun berada.",
            icon: "🕋",
            color: "nu-indigo"
        }
    ];

    const grindingSystem = [
        {
            title: "Daily Journal",
            subtitle: "Memory Log",
            description: "Catat perjalanan spiritual, refleksi ayat, hingga doa-doa yang ingin Anda panjatkan hari ini.",
            icon: "📓",
            rank: "Scribe"
        },
        {
            title: "To-Do List",
            subtitle: "Main Quest",
            description: "Susun agenda harian Anda. prioritaskan ibadah dan tugas dunia agar seimbang (Life-Balance).",
            icon: "✅",
            rank: "Strategist"
        },
        {
            title: "Quest System",
            subtitle: "Challenges",
            description: "Selesaikan misi harian dan mingguan untuk melatih konsistensi dan mendapatkan reward spiritual.",
            icon: "⚔️",
            rank: "Warrior"
        },
        {
            title: "Habit & EXP",
            subtitle: "Progression",
            description: "Lihat visualisasi pertumbuhan iman Anda melalui bar EXP dan streak habit yang tidak boleh terputus.",
            icon: "🔥",
            rank: "Master"
        }
    ];

    return (
        <>
            <Head title={`Features - ${appName}`} />

            <main className="pt-40 pb-32 px-6 max-w-7xl mx-auto">
                {/* HEADER SECTION */}
                <RevealSection className="text-center mb-32" direction="down">
                    <p className="text-[10px] font-black text-nu-teal uppercase tracking-[0.5em] mb-4">The Toolkit</p>
                    <h1 className="text-5xl lg:text-8xl font-serif font-black uppercase tracking-tighter mb-8 leading-[0.9] text-nu-indigo">
                        Level Up <br /><span className="text-nu-teal italic-reset">Ecosystem.</span>
                    </h1>
                    <div className="w-20 h-1 bg-nu-teal mx-auto rounded-full mb-12"></div>
                    <p className="text-lg lg:text-xl font-medium text-slate-500 max-w-2xl mx-auto leading-relaxed">
                        Kami menyediakan semua "peralatan" yang Anda butuhkan untuk membangun karakter Muslim yang kuat di era digital.
                    </p>
                </RevealSection>

                {/* CORE PILLARS SECTION */}
                <div className="mb-40">
                    <RevealSection className="flex items-center gap-6 mb-16" direction="left">
                        <div className="w-16 h-[2px] bg-nu-teal"></div>
                        <h2 className="text-3xl font-serif font-black uppercase text-nu-indigo tracking-tight">Core Pillars <span className="text-nu-teal italic-reset ml-2">Basics.</span></h2>
                    </RevealSection>

                    <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        {corePillars.map((f, i) => (
                            <RevealSection key={i} delay={`${i * 50}ms`} direction="up" className="group">
                                <div className="h-full bg-white p-10 rounded-[3rem] border border-slate-100 shadow-[0_15px_40px_-20px_rgba(0,0,0,0.05)] hover:border-nu-teal hover:-translate-y-3 transition-all duration-500 relative overflow-hidden">
                                    {/* Icon Background Blur */}
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

                {/* GRINDING SYSTEM SECTION (GAMIFIED) */}
                <div className="relative">
                    <div className="absolute inset-0 bg-nu-indigo rounded-[5rem] -rotate-1 scale-105 pointer-events-none opacity-5"></div>
                    <RevealSection className="flex items-center justify-end gap-6 mb-16 text-right" direction="right">
                        <h2 className="text-3xl font-serif font-black uppercase text-nu-indigo tracking-tight">Grinding <span className="text-nu-teal italic-reset ml-2">System.</span></h2>
                        <div className="w-16 h-[2px] bg-nu-teal"></div>
                    </RevealSection>

                    <div className="grid md:grid-cols-2 gap-8">
                        {grindingSystem.map((f, i) => (
                            <RevealSection key={i} delay={`${i * 100}ms`} direction={i % 2 === 0 ? "left" : "right"}>
                                <div className="bg-nu-indigo p-10 lg:p-14 rounded-[4rem] text-white group hover:shadow-2xl hover:shadow-nu-indigo/30 transition-all duration-700 relative overflow-hidden group">
                                    {/* EXP Accent */}
                                    <div className="absolute top-0 right-0 w-32 h-32 bg-white/5 rotate-45 translate-x-12 -translate-y-12"></div>

                                    <div className="flex flex-col lg:flex-row lg:items-center gap-8 relative z-10">
                                        <div className="w-20 h-20 bg-white/10 rounded-[2rem] flex items-center justify-center text-4xl group-hover:bg-nu-teal group-hover:text-nu-indigo transition-all duration-500 rotate-2 group-hover:rotate-0">
                                            {f.icon}
                                        </div>
                                        <div>
                                            <div className="flex items-center gap-3 mb-2">
                                                <span className="px-3 py-1 bg-nu-teal text-nu-indigo text-[9px] font-black uppercase rounded-full">{f.rank}</span>
                                                <p className="text-nu-teal/60 font-black text-[10px] uppercase tracking-widest leading-none">{f.subtitle}</p>
                                            </div>
                                            <h3 className="text-3xl font-serif font-black uppercase mb-4 group-hover:text-nu-teal transition-colors">{f.title}</h3>
                                            <p className="text-white/60 font-medium leading-relaxed group-hover:text-white/80 transition-colors">{f.description}</p>

                                            {/* Progress Bar Placeholder for Theme */}
                                            <div className="mt-8 h-1 w-32 bg-white/10 rounded-full overflow-hidden">
                                                <div className="h-full bg-nu-teal w-1/2 group-hover:w-full transition-all duration-1000"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </RevealSection>
                        ))}
                    </div>
                </div>

                {/* BOTTOM CALL TO ACTION */}
                <RevealSection className="mt-40 text-center" direction="up">
                    <div className="bg-slate-50 p-12 lg:p-20 rounded-[5rem] border border-slate-100 flex flex-col items-center">
                        <div className="text-5xl mb-8">🚀</div>
                        <h2 className="text-3xl lg:text-5xl font-serif font-black text-nu-indigo uppercase mb-8 tracking-tighter">Ready to Grinding <br />For Akhirat?</h2>
                        <a href="/downloads/muslim-app.apk" download="muslim-app.apk" className="bg-nu-indigo text-white px-12 py-5 rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-slate-900 transition-all shadow-xl shadow-black/10 inline-flex items-center gap-3">
                            🚀 Download APK Now
                        </a>
                    </div>
                </RevealSection>
            </main>
        </>
    );
}

Features.layout = page => <MainLayout children={page} />;
