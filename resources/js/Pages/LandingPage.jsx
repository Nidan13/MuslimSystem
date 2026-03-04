import React, { useState, useEffect } from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import RevealSection from '@/Components/RevealSection';

const HeroDownloadButton = ({ downloadUrl }) => {
    const [isDownloading, setIsDownloading] = useState(false);

    const handleDownload = () => {
        setIsDownloading(true);
        setTimeout(() => {
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.setAttribute('download', 'muslim-app.apk');
            document.body.appendChild(link);
            link.click();
            link.parentNode.removeChild(link);
            setIsDownloading(false);
        }, 1500); // Simulate processing time for UX
    };

    return (
        <button
            onClick={handleDownload}
            disabled={isDownloading}
            className={`px-10 py-5 rounded-2xl font-black uppercase text-xs tracking-widest transition-all shadow-xl flex items-center justify-center gap-3 w-full sm:w-auto ${isDownloading ? 'bg-slate-800 text-white/50 shadow-none cursor-not-allowed' : 'bg-nu-indigo text-white hover:bg-slate-900 shadow-black/20'}`}
        >
            {isDownloading ? (
                <>
                    <svg className="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                        <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses...
                </>
            ) : "Download APK"}
        </button>
    );
};

const SmartphoneLayered = () => (
    <div className="relative w-full h-[500px] flex items-center justify-center lg:justify-end perspective-1000" style={{ animation: 'floatComplex 8s ease-in-out infinite' }}>
        {/* Back Mockup */}
        <div className="absolute translate-x-12 -translate-y-12 rotate-[15deg] w-[240px] h-[480px] bg-slate-800 rounded-[2.5rem] border-[6px] border-slate-900 shadow-xl overflow-hidden hidden lg:block opacity-40">
            <div className="w-full h-full bg-nu-teal p-6 flex flex-col justify-end">
                <div className="h-4 w-3/4 bg-white/20 rounded-full mb-2"></div>
                <div className="h-2 w-1/2 bg-white/10 rounded-full"></div>
            </div>
        </div>
        {/* Front Mockup */}
        <div className="relative z-10 w-[260px] h-[520px] bg-slate-900 rounded-[3rem] border-[8px] border-slate-800 shadow-2xl overflow-hidden ring-1 ring-white/10">
            <div className="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-6 bg-slate-800 rounded-b-2xl z-20"></div>
            <div className="w-full h-full bg-white flex flex-col pt-12 p-6 space-y-6">
                <div className="flex justify-between items-center">
                    <div className="w-10 h-10 bg-nu-teal rounded-xl flex items-center justify-center text-white font-black text-[10px]">MLU</div>
                    <div className="w-8 h-8 rounded-full bg-slate-50"></div>
                </div>
                <div className="space-y-4">
                    <h4 className="text-nu-indigo font-black text-sm uppercase">Habit Tracker</h4>
                    <div className="grid grid-cols-7 gap-1">
                        {[...Array(28)].map((_, i) => (
                            <div key={i} className={`aspect-square rounded-md ${i < 18 ? 'bg-nu-teal' : 'bg-slate-50'}`}></div>
                        ))}
                    </div>
                </div>
                <div className="p-4 bg-nu-light rounded-2xl border border-slate-100">
                    <p className="text-[10px] text-nu-teal font-black uppercase">Statistik Ibadah</p>
                    <p className="text-xs text-slate-500 font-medium">Progress habit kamu meningkat 75% minggu ini!</p>
                </div>
            </div>
        </div>
    </div>
);

const ImpactGallery = () => {
    const [active, setActive] = useState(0);
    const slides = [
        { title: "Komunitas Ibadah", value: "Active", desc: "Bergabung bersama ribuan Muslim lainnya dalam Circle produktivitas spiritual.", image: "/images/slider_community.png" },
        { title: "Khatam Al-Quran", value: "Daily", desc: "Konsisten membaca ayat suci setiap hari dengan target yang bisa disesuaikan.", image: "/images/slider_quran.png" },
        { title: "Zakat & Sedekah", value: "Impact", desc: "Perhitungan zakat otomatis dan fitur pencatatan sedekah untuk membersihkan harta.", image: "/images/slider_charity.png" }
    ];

    useEffect(() => {
        const interval = setInterval(() => {
            setActive(prev => (prev + 1) % slides.length);
        }, 5000);
        return () => clearInterval(interval);
    }, [slides.length]);

    return (
        <section className="py-32 px-6 bg-slate-50 text-nu-indigo overflow-hidden relative border-t border-slate-100">
            <div className="max-w-7xl mx-auto grid lg:grid-cols-2 gap-20 items-center">
                <RevealSection>
                    <p className="text-nu-teal font-black text-[10px] uppercase tracking-[0.5em] mb-8">Digital Impact</p>
                    <h2 className="text-4xl lg:text-6xl font-serif font-black uppercase leading-none mb-10 text-nu-indigo">Dampak Nyata Untuk <span className="text-nu-teal italic-reset">Ummah.</span></h2>
                    <div className="flex gap-4">
                        {slides.map((_, i) => (
                            <button key={i} onClick={() => setActive(i)} className={`h-1 rounded-full transition-all duration-500 ${active === i ? 'w-12 bg-nu-teal' : 'w-4 bg-slate-200'}`}></button>
                        ))}
                    </div>
                </RevealSection>
                <div className="relative h-[450px]">
                    {slides.map((s, i) => (
                        <div key={i} className={`absolute inset-0 transition-all duration-1000 flex flex-col justify-center ${active === i ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-12 pointer-events-none'}`}>
                            <div className="w-full h-64 rounded-3xl overflow-hidden mb-8 shadow-2xl relative group">
                                <div className="absolute inset-0 bg-nu-indigo/20 group-hover:bg-transparent transition-colors duration-700 z-10 pointer-events-none"></div>
                                <img src={s.image} alt={s.title} className="w-full h-full object-cover transform hover:scale-110 transition-transform duration-1000" />
                            </div>
                            <h3 className="text-3xl font-serif font-black uppercase mb-3 text-nu-indigo">{s.title}</h3>
                            <p className="text-slate-500 text-lg font-medium max-w-sm leading-relaxed">{s.desc}</p>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
};

export default function LandingPage({ appName, downloadUrl }) {
    return (
        <>
            <Head title={`Muslim Level Up - Ubah Kebiasaan, Tingkatkan Ibadah`} />
            <style dangerouslySetInnerHTML={{
                __html: `
                @keyframes float {
                    0% { transform: translateY(0px); }
                    50% { transform: translateY(-20px); }
                    100% { transform: translateY(0px); }
                }
                @keyframes floatComplex {
                    0% { transform: translateY(0px) rotate(0deg) scale(1); }
                    33% { transform: translateY(-15px) rotate(1.5deg) scale(1.02); }
                    66% { transform: translateY(-5px) rotate(-1.5deg) scale(0.98); }
                    100% { transform: translateY(0px) rotate(0deg) scale(1); }
                }
            `}} />
            {/* Animation CSS for Aesthetic & Utility Section */}
            <style>{`@keyframes fadeSlide{0%{opacity:0;transform:translateY(20px);}100%{opacity:1;transform:translateY(0);}} .animate-fade-slide{animation:fadeSlide 0.8s ease-out forwards;}`}</style>

            {/* Hero Section */}
            <section className="bg-nu-teal pt-34 pb-20 px-6 relative overflow-hidden">
                <div
                    className="absolute inset-0 pointer-events-none opacity-[0.2] z-0"
                    style={{
                        backgroundImage: 'url(/images/mosque-pattern.png)',
                        backgroundSize: '800px',
                        backgroundPosition: 'center bottom',
                        backgroundRepeat: 'no-repeat'
                    }}
                ></div>
                <div className="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.1),transparent)] pointer-events-none"></div>
                <div className="max-w-7xl mx-auto grid lg:grid-cols-2 gap-20 items-center">
                    <RevealSection className="text-white relative z-10" delay="0ms" direction="left">
                        <h1 className="text-5xl lg:text-8xl font-serif font-black leading-[0.95] mb-8 uppercase tracking-tighter">
                            Ubah Kebiasaan,<br />
                            <span className="opacity-40">Tingkatkan Ibadah.</span>
                        </h1>
                        <p className="text-lg lg:text-xl font-medium text-white/80 mb-10 max-w-lg leading-relaxed">
                            Upgrade kualitas spiritual Anda dengan teknologi yang dirancang untuk membangun disiplin ibadah harian. Tanpa iklan, fokus pada perkembangan.
                        </p>
                        <div className="flex flex-col sm:flex-row gap-5">
                            <HeroDownloadButton downloadUrl={downloadUrl} />
                            <Link href="/features" className="px-10 py-5 border-2 border-white/20 text-white rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-white/10 transition-all flex items-center justify-center gap-2">Explore Flow →</Link>
                        </div>
                    </RevealSection>
                    <RevealSection delay="400ms" direction="up">
                        <SmartphoneLayered />
                    </RevealSection>
                </div>
            </section>

            {/* Habit Transformation Flow Section */}
            <section className="py-32 px-6 bg-slate-50/30">
                <div className="max-w-7xl mx-auto text-center mb-24">
                    <RevealSection>
                        <p className="text-[10px] font-black text-nu-teal uppercase tracking-[0.5em] mb-4">The Evolution</p>
                        <h2 className="text-4xl lg:text-6xl font-serif font-black text-nu-indigo uppercase tracking-tighter leading-none mb-6">Proses Menjadi <br />Versi Terbaik.</h2>
                        <div className="w-20 h-1 bg-nu-teal mx-auto rounded-full"></div>
                    </RevealSection>

                    <div className="grid md:grid-cols-3 gap-8 mt-20">
                        {[
                            { phase: "Phase I", title: "Kesadaran Diri", desc: "Mulai mengenali pola ibadah harian Anda. Kejujuran dalam mencatat adalah langkah awal menuju konsistensi.", icon: "🌱" },
                            { phase: "Phase II", title: "Keteguhan Niat", desc: "Membangun disiplin di tengah kesibukan. Kami hadir sebagai pengingat setia saat motivasi Anda sedang goyah.", icon: "🌳" },
                            { phase: "Phase III", title: "Ketenangan Jiwa", desc: "Melihat grafik spiritual yang stabil memberikan kepuasan batin dan kedekatan yang lebih dalam.", icon: "✨" }
                        ].map((s, i) => (
                            <RevealSection key={i} delay={`${i * 300}ms`} direction="up" className="h-full">
                                <div className="relative p-10 bg-white rounded-[2.5rem] border border-slate-100 group hover:-translate-y-3 hover:shadow-2xl hover:shadow-nu-indigo/10 transition-all duration-500 h-full text-left overflow-hidden">
                                    {/* Aksen nomor fase yang soft */}
                                    <div className="text-[10px] font-black text-nu-teal/40 uppercase tracking-[0.3em] mb-6">{s.phase}</div>

                                    <div className="text-5xl mb-8 transform group-hover:scale-110 transition-transform duration-500">{s.icon}</div>

                                    <h3 className="text-2xl font-serif font-black mb-4 uppercase text-nu-indigo group-hover:text-nu-teal transition-colors">{s.title}</h3>

                                    <p className="text-slate-500 leading-relaxed font-medium text-sm">{s.desc}</p>

                                    {/* Background Decor */}
                                    <div className="absolute -bottom-10 -right-10 w-32 h-32 bg-slate-50 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-700 scale-50 group-hover:scale-100"></div>
                                </div>
                            </RevealSection>
                        ))}
                    </div>
                </div>
            </section>

            {/* New Section: Modern Mukmin Aesthetic */}
            <section className="py-32 px-6 bg-slate-900 text-white relative overflow-hidden border-t border-white/5 animate-fade-slide">
                {/* Decorative background elements (blurred circles) */}
                <div className="absolute top-0 right-0 w-[500px] h-[500px] bg-nu-teal/10 blur-[120px] rounded-full -translate-y-1/2 translate-x-1/2 select-none pointer-events-none"></div>
                <div className="absolute bottom-0 left-0 w-[300px] h-[300px] bg-nu-indigo/20 blur-[100px] rounded-full translate-y-1/2 -translate-x-1/2 select-none pointer-events-none"></div>

                <div className="max-w-7xl mx-auto relative z-10">
                    <div className="grid lg:grid-cols-2 gap-20 items-center">

                        {/* Sisi Kiri: Copywriting */}
                        <RevealSection direction="left">
                            <p className="text-[10px] font-black text-nu-teal uppercase tracking-[0.5em] mb-4">Aesthetic & Utility</p>
                            <h2 className="text-4xl lg:text-7xl font-serif font-black uppercase leading-[0.9] tracking-tighter mb-10 text-white">
                                Desain <span className="text-white/40">Tanpa</span> Kompromi.<br />
                                Ibadah <span className="text-nu-teal italic-reset">Lebih Fokus.</span>
                            </h2>
                            <p className="text-lg text-white/70 font-medium leading-relaxed mb-12 max-w-lg">
                                Kami percaya estetika dapat meningkatkan fokus. Muslim Level Up menghadirkan antarmuka modern yang bersih, intuitif, dan bebas iklan, memastikan setiap interaksi Anda menjadi pengalaman spiritual yang menyenangkan. Desain yang dirancang untuk generasi tech-savvy.
                            </p>

                            <div className="space-y-6">
                                <div className="flex gap-4 items-center">
                                    <div className="w-8 h-8 rounded-full bg-nu-teal/20 flex items-center justify-center text-nu-teal">✓</div>
                                    <span className="font-bold text-sm uppercase tracking-wider text-white">Modern & Clean UI</span>
                                </div>
                                <div className="flex gap-4 items-center">
                                    <div className="w-8 h-8 rounded-full bg-nu-teal/20 flex items-center justify-center text-nu-teal">✓</div>
                                    <span className="font-bold text-sm uppercase tracking-wider text-white">Dark Mode Optimized</span>
                                </div>
                                <div className="flex gap-4 items-center">
                                    <div className="w-8 h-8 rounded-full bg-nu-teal/20 flex items-center justify-center text-nu-teal">✓</div>
                                    <span className="font-bold text-sm uppercase tracking-wider text-white">Ad-Free Experience (No Ads)</span>
                                </div>
                            </div>
                        </RevealSection>

                        {/* Sisi Kanan: Visual Mockups (Fokus ke UI Estetik) */}
                        <RevealSection direction="up" delay="400ms">
                            <div className="relative w-full h-[600px] flex items-center justify-center perspective-1000">

                                {/* Glassmorphism Card (Floating Element) */}
                                <div className="absolute top-10 -left-10 w-64 p-6 bg-white/5 backdrop-blur-xl rounded-3xl border border-white/10 shadow-2xl z-20 animate-float-slow transform rotate-[-5deg]">
                                    <p className="text-[10px] font-black text-nu-teal uppercase mb-3">Statistik Ibadah</p>
                                    <div className="h-4 w-full bg-slate-700 rounded-full mb-2 overflow-hidden">
                                        <div className="w-[75%] h-full bg-nu-teal"></div>
                                    </div>
                                    <p className="text-xs text-white/60 font-medium">Progress habit meningkat 75% minggu ini!</p>
                                </div>

                                {/* Main Smartphone Mockup (Close-up UI) */}
                                <div className="relative z-10 w-[280px] h-[560px] bg-slate-800 rounded-[3rem] border-[10px] border-slate-900 shadow-2xl overflow-hidden group">
                                    {/* Camera Notch */}
                                    <div className="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-6 bg-slate-900 rounded-b-2xl z-20"></div>

                                    {/* Internal UI Simulation (Dark Mode) */}
                                    <div className="w-full h-full bg-slate-900 p-6 flex flex-col pt-12 space-y-8">
                                        <div className="flex justify-between items-center">
                                            <div className="w-10 h-10 bg-nu-teal rounded-full flex items-center justify-center text-white font-black text-[12px]">MLU</div>
                                            <div className="w-8 h-8 rounded-full bg-slate-700"></div>
                                        </div>

                                        <div className="space-y-4">
                                            <h4 className="text-white font-serif font-black text-xl uppercase tracking-tighter leading-none">Habit Tracker</h4>
                                            <div className="grid grid-cols-7 gap-1">
                                                {[...Array(28)].map((_, i) => (
                                                    <div key={i} className={`aspect-square rounded-md ${i < 18 ? 'bg-nu-teal' : 'bg-slate-700'}`}></div>
                                                ))}
                                            </div>
                                        </div>

                                        <div className="p-4 bg-white/5 rounded-2xl border border-white/10">
                                            <p className="text-[10px] text-nu-teal font-black uppercase">Daily Quest</p>
                                            <p className="text-sm text-white font-medium">Sholat Tepat Waktu (+500 EXP)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </RevealSection>
                    </div>
                </div>
            </section>

            {/* New Impact Gallery Replacement for Quote */}
            <ImpactGallery />

            {/* New Section 1: Community Support */}
            <section className="py-32 px-6 bg-nu-light relative overflow-hidden">
                <div className="max-w-7xl mx-auto grid lg:grid-cols-2 gap-20 items-center">
                    <RevealSection className="lg:order-2">
                        <p className="text-[10px] font-black text-nu-teal uppercase tracking-[0.5em] mb-4">The Ummah</p>
                        <h2 className="text-4xl lg:text-7xl font-serif font-black text-nu-indigo uppercase leading-[0.9] tracking-tighter mb-8">Tumbuh Bersama <span className="text-nu-teal italic-reset">Komunitas.</span></h2>
                        <p className="text-lg text-slate-500 font-medium leading-relaxed mb-10">Bergabunglah dengan ribuan pengguna lain yang saling menyemangati dalam kebaikan. Fitur Circle memungkinkan Anda membuat grup ibadah dengan teman dan keluarga.</p>
                        <div className="grid grid-cols-2 gap-6">
                            <div className="p-8 bg-white rounded-3xl border border-slate-100 shadow-sm">
                                <div className="text-3xl font-serif font-black text-nu-indigo mb-2">500+</div>
                                <p className="text-[9px] font-black text-slate-400 uppercase tracking-widest">Active Circles</p>
                            </div>
                            <div className="p-8 bg-white rounded-3xl border border-slate-100 shadow-sm">
                                <div className="text-3xl font-serif font-black text-nu-indigo mb-2">10k+</div>
                                <p className="text-[9px] font-black text-slate-400 uppercase tracking-widest">Global Members</p>
                            </div>
                        </div>
                    </RevealSection>
                    <RevealSection className="lg:order-1">
                        <div className="relative group">
                            <div className="absolute -inset-4 bg-nu-teal/5 rounded-[4rem] group-hover:scale-110 transition-transform duration-1000"></div>
                            <div className="relative grid grid-cols-2 gap-6">
                                {[1, 2, 3, 4].map(i => (
                                    <div key={i} className={`aspect-square bg-slate-100 rounded-[2rem] overflow-hidden ${i % 2 === 0 ? 'mt-12' : ''} shadow-lg relative group/item`}>
                                        <div className="absolute inset-0 bg-nu-indigo/20 group-hover/item:bg-transparent transition-colors duration-500 z-10 pointer-events-none"></div>
                                        <img src={`/images/ummah_${i}.png`} alt={`Ummah Community ${i}`} className="w-full h-full object-cover transform scale-105 group-hover/item:scale-110 transition-transform duration-700" />
                                    </div>
                                ))}
                            </div>
                        </div>
                    </RevealSection>
                </div>
            </section>



            {/* New Section 2: Human design */}
            <section className="py-32 px-6 border-t border-slate-100 bg-white">
                <div className="max-w-7xl mx-auto text-center">
                    <RevealSection>
                        {/* Badge Konsep */}
                        <p className="text-[10px] font-black text-nu-teal uppercase tracking-[0.5em] mb-4">
                            Human-Centric Design
                        </p>

                        {/* Headline yang fokus pada kenyamanan tangan/akses */}
                        <h2 className="text-4xl lg:text-6xl font-serif font-black text-nu-indigo uppercase tracking-tighter leading-[0.9] mb-12">
                            Didesain untuk <br />Kemudahan Jangkauan.
                        </h2>

                        {/* Grid Visual: Fokus pada Interaksi yang Mudah */}
                        <div className="flex flex-wrap justify-center gap-10 lg:gap-20">
                            {[
                                { name: 'One-Handed', detail: 'Akses Mudah', icon: '🖐️' },
                                { name: 'Intuitive', detail: 'Alur Cerdas', icon: '🧠' },
                                { name: 'Instant', detail: 'Respon Cepat', icon: '⚡' },
                                { name: 'Zero Curve', detail: 'Tanpa Bingung', icon: '🎯' }
                            ].map((item) => (
                                <div key={item.name} className="group flex flex-col items-center">
                                    <div className="w-20 h-20 mb-4 bg-slate-50 rounded-3xl flex items-center justify-center text-3xl group-hover:bg-nu-indigo group-hover:text-white group-hover:-translate-y-2 transition-all duration-500 shadow-sm border border-slate-100">
                                        {item.icon}
                                    </div>
                                    <span className="text-[10px] font-black uppercase tracking-widest text-nu-indigo">
                                        {item.name}
                                    </span>
                                    <span className="text-[9px] font-medium text-slate-400 uppercase mt-1">
                                        {item.detail}
                                    </span>
                                </div>
                            ))}
                        </div>

                        {/* Copywriting Penutup */}
                        <div className="mt-20 max-w-2xl mx-auto">
                            <p className="text-slate-500 font-medium leading-relaxed">
                                Kami percaya teknologi seharusnya tidak membingungkan. Setiap elemen dalam aplikasi ini diletakkan dengan presisi agar mudah dijangkau oleh ibu jari Anda, memastikan navigasi yang <span className="text-nu-teal font-bold text-lg">mulus</span> bahkan saat Anda sedang sibuk berpindah tempat.
                            </p>

                            {/* Penegasan aksesibilitas Android */}
                            <p className="mt-6 text-[10px] font-bold text-slate-300 uppercase tracking-widest">
                                Optimized for natural android gestures
                            </p>
                        </div>
                    </RevealSection>
                </div>
            </section>
        </>
    );
}



LandingPage.layout = page => <MainLayout children={page} />;
