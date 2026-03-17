import React from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import RevealSection from '@/Components/RevealSection';

const Milestone = ({ year, title, description, isLast }) => {
    const phaseNumber = year.split(' ')[1];
    return (
        <div className="flex gap-10 group">
            <div className="flex flex-col items-center shrink-0">
                <div className="w-16 h-16 rounded-full bg-nu-indigo flex flex-col items-center justify-center text-white z-10 group-hover:bg-nu-teal group-hover:text-nu-indigo transition-all duration-500 border-4 border-white shadow-2xl overflow-hidden active:scale-95">
                    <span className="text-[7px] font-black uppercase tracking-[0.2em] mb-0.5 opacity-60">Phase</span>
                    <span className="text-xl font-black leading-none">{phaseNumber}</span>
                </div>
                {!isLast && <div className="w-1 flex-grow bg-slate-100 group-hover:bg-nu-teal/20 transition-colors"></div>}
            </div>
            <div className="pb-20 pt-[22px]">
                <h4 className="text-2xl font-serif font-black text-nu-indigo uppercase mb-4 tracking-tighter group-hover:text-nu-teal transition-colors leading-none">{title}</h4>
                <p className="text-slate-500 font-medium leading-relaxed max-w-lg text-sm">{description}</p>
            </div>
        </div>
    );
};

const FutureCard = ({ title, status, desc, icon, glow }) => (
    <RevealSection direction="up">
        <div className="bg-white/5 border border-white/10 p-10 rounded-[3rem] backdrop-blur-xl group hover:border-nu-teal transition-all duration-500 relative overflow-hidden h-full">
            <div className={`absolute top-0 right-0 w-32 h-32 bg-gradient-to-br ${glow} to-transparent opacity-0 group-hover:opacity-20 transition-opacity blur-3xl`}></div>
            <div className="flex justify-between items-start mb-10">
                <div className="text-5xl transform group-hover:scale-110 transition-transform duration-700">{icon}</div>
                <span className="text-[8px] font-black uppercase text-nu-teal bg-nu-teal/10 px-3 py-1.5 rounded-full border border-nu-teal/20">{status}</span>
            </div>
            <h4 className="text-xl font-serif font-black text-white mb-4 tracking-tight group-hover:text-nu-teal transition-colors uppercase">{title}</h4>
            <p className="text-white/50 text-sm font-medium leading-relaxed group-hover:text-white/70 transition-colors">{desc}</p>
        </div>
    </RevealSection>
);

const Architect = ({ name, role, icon, delay }) => (
    <RevealSection delay={delay} direction="up">
        <div className="relative group p-10 bg-white rounded-[3rem] border border-slate-100 hover:border-nu-teal transition-all duration-500 shadow-xl shadow-slate-200/50">
            <div className="w-24 h-24 bg-nu-indigo/5 rounded-2xl flex items-center justify-center text-4xl mb-8 group-hover:bg-nu-indigo group-hover:text-white transition-all duration-500">
                {icon}
            </div>
            <h4 className="text-xl font-serif font-black text-nu-indigo uppercase tracking-tighter mb-1">{name}</h4>
            <p className="text-[10px] font-black text-nu-teal uppercase tracking-widest">{role}</p>
        </div>
    </RevealSection>
);

export default function About({ appName }) {
    const milestones = [
        { year: "Phase 1", title: "The Core", description: "Membangun pondasi spiritual digital dengan fitur utama: Jadwal Sholat presisi, Al-Qur'an, dan sistem Daily Task Ibadah." },
        { year: "Phase 2", title: "The Connection", description: "Menghubungkan antar individu melalui sistem Circle, Misi Bersama, serta integrasi data Masjid yang transparan." },
        { year: "Phase 3", title: "The Ecosystem", description: "Mewujudkan ekosistem lengkap dengan Marketplace Syariah, Vendor Ibadah terverifikasi, dan pemberdayaan ekonomi Ummah." },
    ];

    const futureRoadmap = [
        { title: "Pelafalan AI", status: "Roadmap", desc: "Cek tajwid dan kelancaran pelafalan Al-Quran Anda dengan teknologi AI tercanggih.", icon: "🎙️", glow: "from-emerald-400" },
        { title: "Vendor Umroh & Kurban", status: "Planning", desc: "Integrasi layanan ibadah besar langsung di genggaman dengan transparansi penuh.", icon: "🕋", glow: "from-amber-400" },
        { title: "Sirah Journey Evolution", status: "In R&D", desc: "Digitalisasi sejarah perjuangan Rasulullah dengan visualisasi interaktif yang memukau.", icon: "🐪", glow: "from-orange-400" },
    ];

    return (
        <div className="bg-white overflow-x-hidden">
            <Head title={`About - The Strategy - ${appName}`} />

            {/* HERO: The Strategic Vision */}
            <section className="pt-48 pb-32 px-6 bg-nu-indigo text-white relative overflow-hidden">
                <div className="absolute top-0 right-0 w-[800px] h-[800px] bg-nu-teal/15 rounded-full blur-[180px] -translate-y-1/2 translate-x-1/2"></div>
                <div className="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] translate-y-1/2 -translate-x-1/2"></div>
                
                <div className="max-w-7xl mx-auto relative z-10 text-center">
                    <RevealSection direction="down" className="max-w-4xl mx-auto">
                        <div className="inline-block px-6 py-2 bg-white/5 border border-white/10 rounded-full mb-10 backdrop-blur-md">
                            <span className="text-[10px] font-black uppercase text-nu-teal tracking-[0.4em]">Future Blueprint 2025</span>
                        </div>
                        <h1 className="text-5xl lg:text-8xl font-serif font-black uppercase tracking-tighter leading-[0.85] mb-12">
                            The Future <br /><span className="text-nu-teal italic-reset text-white">Of Deen.</span>
                        </h1>
                        <p className="text-lg lg:text-2xl font-medium leading-relaxed text-white/70 max-w-3xl mx-auto tracking-tight">
                            Kami tidak sekadar membangun aplikasi. Kami membangun infrastruktur spiritual di era digital melalui teknologi, data, dan ketaatan.
                        </p>
                    </RevealSection>
                </div>
            </section>

            {/* THE JOURNEY: Milestones (Phases) */}
            <section className="py-32 px-6 bg-white overflow-hidden">
                <div className="max-w-7xl mx-auto grid lg:grid-cols-2 gap-24">
                    <RevealSection direction="right">
                        <p className="text-nu-teal font-black text-[10px] uppercase tracking-[0.5em] mb-4">Strategic Evolution</p>
                        <h2 className="text-4xl lg:text-6xl font-serif font-black text-nu-indigo uppercase tracking-tighter leading-none mb-10">Product <br />Phases.</h2>
                        <div className="w-20 h-2 bg-nu-indigo rounded-full mb-12"></div>
                        <p className="text-slate-500 font-medium text-lg leading-relaxed mb-12">
                            Peta jalan kami dirancang untuk memastikan setiap fitur memberikan dampak nyata bagi kestabilan iman dan produktivitas umat.
                        </p>
                        <div className="p-10 bg-slate-50 rounded-[3rem] border-l-8 border-nu-teal">
                            <h5 className="text-nu-indigo font-black uppercase mb-2">Platform Focus</h5>
                            <div className="text-5xl font-serif font-black text-nu-indigo">Gamified Deen</div>
                            <p className="text-nu-teal font-bold text-xs uppercase tracking-widest mt-2">Built for Consistency & Istiqomah</p>
                        </div>
                    </RevealSection>

                    <div className="space-y-0">
                        {milestones.map((m, i) => (
                            <RevealSection key={i} delay={`${i * 200}ms`} direction="up">
                                <Milestone {...m} isLast={i === milestones.length - 1} />
                            </RevealSection>
                        ))}
                    </div>
                </div>
            </section>

            {/* THE HUB: Integrated Ecosystem */}
            <section className="py-32 bg-slate-50 px-6 relative overflow-hidden">
                <div className="absolute top-0 right-0 w-full h-full opacity-5 pointer-events-none" style={{ backgroundImage: 'url("/images/mosque-pattern.png")', backgroundSize: '600px' }}></div>
                <div className="max-w-7xl mx-auto relative z-10">
                    <RevealSection className="text-center mb-24">
                        <p className="text-nu-teal font-black text-[10px] uppercase tracking-[0.5em] mb-4">Strategic Architecture</p>
                        <h2 className="text-4xl lg:text-6xl font-serif font-black text-nu-indigo uppercase tracking-tighter mb-8">The Ecosystem Hub.</h2>
                        <p className="text-slate-400 font-medium max-w-2xl mx-auto text-lg leading-relaxed">
                            MLU dirancang sebagai hub pusat yang menghubungkan setiap elemen penting dalam kehidupan Muslim.
                        </p>
                    </RevealSection>

                    <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                        {[
                            { title: "Individu", desc: "Peningkatan spiritualitas personal melalui gamifikasi.", icon: "🎯" },
                            { title: "Komunitas", desc: "Circle ibadah kolektif dan misi bersama secara real-time.", icon: "🤝" },
                            { title: "Masjid", desc: "Integrasi sistem manajemen Masjid dan donasi transparan.", icon: "🕌" },
                            { title: "Ekonomi", desc: "Ekosistem marketplace syariah dan pemberdayaan umat.", icon: "⚖️" }
                        ].map((item, i) => (
                            <RevealSection key={i} delay={`${i * 150}ms`} direction="up">
                                <div className="p-10 rounded-[4rem] bg-white border border-slate-100 hover:border-nu-teal hover:-translate-y-4 transition-all duration-500 group shadow-xl shadow-slate-200/50">
                                    <div className="text-5xl mb-8 transform group-hover:scale-110 transition-transform">{item.icon}</div>
                                    <h4 className="text-xl font-serif font-black text-nu-indigo uppercase mb-4 tracking-tighter">{item.title}</h4>
                                    <p className="text-sm text-slate-500 font-medium leading-relaxed">{item.desc}</p>
                                </div>
                            </RevealSection>
                        ))}
                    </div>
                </div>
            </section>

            {/* FUTURE: The Roadmap */}
            <section className="py-48 px-6 bg-nu-indigo text-white relative overflow-hidden">
                <div className="absolute bottom-0 right-0 w-1/2 h-1/2 bg-nu-teal/5 rounded-full blur-[150px]"></div>
                <div className="max-w-7xl mx-auto relative z-10">
                    <div className="flex flex-col lg:flex-row justify-between items-end gap-12 mb-32">
                        <RevealSection direction="right" className="max-w-2xl">
                            <p className="text-nu-teal font-black text-[10px] uppercase tracking-[0.5em] mb-8">Conceptual Roadmap</p>
                            <h2 className="text-5xl lg:text-8xl font-serif font-black uppercase tracking-tighter leading-[0.85] mb-0">Building <br /><span className="text-nu-teal italic-reset text-white">The Future.</span></h2>
                        </RevealSection>
                        <RevealSection direction="left" className="lg:text-right">
                            <p className="text-white/50 font-medium text-lg leading-relaxed max-w-sm ml-auto">
                                Inovasi kami tidak pernah berhenti. Inilah masa depan yang sedang kami sempurnakan.
                            </p>
                        </RevealSection>
                    </div>

                    <div className="grid lg:grid-cols-3 gap-10">
                        {futureRoadmap.map((roadmap, i) => (
                            <FutureCard key={i} {...roadmap} />
                        ))}
                    </div>
                </div>
            </section>

            {/* THE VALUES: Engineering values */}
            <section className="py-32 px-6 max-w-7xl mx-auto">
                <RevealSection className="flex flex-col md:flex-row justify-between items-end gap-12 mb-24 border-b border-slate-100 pb-12">
                    <div>
                        <p className="text-nu-teal font-black text-[10px] uppercase tracking-[0.5em] mb-4">Engineering Culture</p>
                        <h2 className="text-4xl lg:text-6xl font-serif font-black text-nu-indigo uppercase tracking-tighter leading-none mb-0">Core Values.</h2>
                    </div>
                    <p className="text-slate-400 font-medium text-lg italic max-w-sm">"Standard of Ihsaan dalam setiap baris kode."</p>
                </RevealSection>

                <div className="grid md:grid-cols-4 gap-8">
                    <Architect name="Precision" role="Akurasid dalam Data" icon="🎯" delay="0ms" />
                    <Architect name="Integrity" role="Keamanan & Privasi" icon="🔒" delay="150ms" />
                    <Architect name="Aesthetics" role="Visual Experience" icon="🎨" delay="300ms" />
                    <Architect name="Impact" role="Manfaat Berkelanjutan" icon="🌍" delay="450ms" />
                </div>
            </section>

            {/* FINAL CTA */}
            <section className="pb-48 pt-24 px-6 text-center">
                <RevealSection direction="up">
                    <div className="w-24 h-24 bg-nu-teal/10 rounded-full flex items-center justify-center text-4xl mx-auto mb-10 text-nu-teal">🚀</div>
                    <h2 className="text-4xl lg:text-7xl font-serif font-black text-nu-indigo uppercase mb-12 tracking-tighter leading-none">
                        Join The <br /><span className="text-nu-teal italic-reset">Revolution.</span>
                    </h2>
                    <div className="flex justify-center gap-6 flex-wrap">
                        <Link href="/" className="px-12 py-6 bg-nu-indigo text-white rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] hover:bg-slate-900 transition-all shadow-2xl shadow-nu-indigo/20 flex items-center gap-4 group">
                            <span>Mulai Ibadah Sekarang</span>
                            <span className="w-5 h-5 bg-nu-teal text-nu-indigo rounded-full flex items-center justify-center transform group-hover:scale-110 transition-transform">→</span>
                        </Link>
                    </div>
                    <p className="mt-16 text-[8px] font-bold text-slate-300 uppercase tracking-[0.8em]">Muslim Level Up © 2024 — Modernity for Deen</p>
                </RevealSection>
            </section>
        </div>
    );
}

About.layout = page => <MainLayout children={page} />;