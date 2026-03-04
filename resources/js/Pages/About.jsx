import React from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import RevealSection from '@/Components/RevealSection';

export default function About({ appName }) {
    return (
        <>
            <Head title={`About - ${appName}`} />

            {/* HERO SECTION: The Mission */}
            <div className="pt-40 pb-20 px-6 max-w-7xl mx-auto">
                <RevealSection className="text-center mb-32" direction="down">
                    <p className="text-[10px] font-black text-nu-teal uppercase tracking-[0.5em] mb-4">The Real Life RPG</p>
                    <h1 className="text-5xl lg:text-8xl font-serif font-black uppercase tracking-tighter mb-8 leading-[0.9] text-nu-indigo">
                        Level Up <br /><span className="text-nu-teal">Your Deen.</span>
                    </h1>
                    <div className="w-20 h-1 bg-nu-teal mx-auto rounded-full mb-12"></div>
                    <p className="text-xl lg:text-2xl font-medium leading-relaxed text-slate-500 max-w-3xl mx-auto tracking-tight">
                        "Kami mengubah smartphone dari sumber distraksi menjadi alat grinding pahala paling efektif dalam hidup Anda."
                    </p>
                </RevealSection>

                {/* PHILOSOPHY SECTION: Dipaksa > Terbiasa > Menikmati */}
                <div className="grid lg:grid-cols-3 gap-8 mb-32">
                    {[
                        { step: "01", label: "Dipaksa", title: "Hard Mode Start", desc: "Menjadi taat itu berat di awal. Kami memberikan sistem 'Force' agar Anda konsisten meski sedang malas.", icon: "⚔️" },
                        { step: "02", label: "Terbiasa", title: "Habit Farming", desc: "Setelah melewati masa paksa, sistem EXP akan mencatat setiap progress hingga ketaatan menjadi kebutuhan.", icon: "♻️" },
                        { step: "03", label: "Menikmati", title: "End-Game Peace", desc: "Nikmati hasil dari disiplin Anda: Ketenangan jiwa dan statistik spiritual yang terus meningkat.", icon: "💎" }
                    ].map((item, i) => (
                        <RevealSection key={i} delay={`${i * 200}ms`} className="bg-slate-50 p-10 rounded-[3rem] border border-slate-100 hover:bg-white hover:shadow-xl hover:-translate-y-2 transition-all duration-500">
                            <div className="text-4xl mb-6">{item.icon}</div>
                            <p className="text-nu-teal font-black text-[10px] uppercase tracking-widest mb-2">{item.label}</p>
                            <h3 className="text-2xl font-serif font-black text-nu-indigo mb-4 uppercase">{item.title}</h3>
                            <p className="text-slate-500 text-sm leading-relaxed">{item.desc}</p>
                        </RevealSection>
                    ))}
                </div>

                {/* GAME MECHANICS SECTION: HP & EXP */}
                <div className="grid lg:grid-cols-2 gap-12 items-stretch mb-32">
                    <RevealSection className="bg-nu-indigo p-12 lg:p-16 rounded-[4rem] text-white relative overflow-hidden" direction="left">
                        <div className="absolute top-0 right-0 p-8 opacity-10 text-9xl">📈</div>
                        <h2 className="text-3xl font-serif font-black uppercase mb-6 text-nu-teal text-left">Mechanics: EXP System</h2>
                        <p className="text-white/70 text-lg leading-relaxed mb-8 text-left">
                            Setiap salat tepat waktu, setiap lembar Quran yang dibaca, dan setiap dzikir yang terucap akan memberikan **EXP**. Kami ingin Anda melihat bahwa investasi waktu untuk akhirat bisa dipantau secara visual.
                        </p>
                        <div className="flex items-center gap-4 bg-white/5 p-6 rounded-3xl border border-white/10">
                            <div className="w-12 h-12 bg-nu-teal rounded-xl flex items-center justify-center font-black">XP</div>
                            <p className="text-sm font-medium">Naikkan level spiritualmu dari Newbie menjadi Istiqomah Legend.</p>
                        </div>
                    </RevealSection>

                    <RevealSection className="bg-white p-12 lg:p-16 rounded-[4rem] shadow-xl border border-slate-100 relative overflow-hidden text-left" direction="right">
                        <div className="absolute top-0 right-0 p-8 opacity-5 text-9xl">❤️</div>
                        <h2 className="text-3xl font-serif font-black uppercase mb-6 text-nu-indigo">Mechanics: Health Point</h2>
                        <p className="text-slate-500 text-lg leading-relaxed mb-8">
                            Hati yang lalai akan menurunkan **Health Point (HP)** spiritual Anda. Fitur ini dirancang bukan untuk menakuti, tapi sebagai alarm bahwa jiwa Anda butuh asupan nutrisi ibadah segera.
                        </p>
                        <div className="flex items-center gap-4 bg-slate-50 p-6 rounded-3xl border border-slate-100">
                            <div className="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center font-black text-white">HP</div>
                            <p className="text-sm font-medium text-slate-500">Jangan biarkan HP menyentuh nol. Segera 'Refill' dengan taubat dan amal shalih.</p>
                        </div>
                    </RevealSection>
                </div>

                {/* TARGET AUDIENCE: Kids to Elders */}
                <RevealSection className="bg-nu-teal/10 p-12 lg:p-20 rounded-[5rem] text-center border-2 border-dashed border-nu-teal/20">
                    <h2 className="text-3xl lg:text-5xl font-serif font-black text-nu-indigo uppercase mb-8 tracking-tighter">
                        Cross-Generation <br />Party Members.
                    </h2>
                    <p className="text-lg text-slate-600 max-w-2xl mx-auto font-medium mb-12 italic">
                        "Dari anak kecil yang baru belajar salat hingga orang tua. Interface kami didesain intuitif—cukup simpel untuk dipahami pemula, cukup mendalam untuk para veteran kehidupan."
                    </p>
                    <div className="flex flex-wrap justify-center gap-12 opacity-50">
                        <div className="flex flex-col items-center"><span className="text-4xl">👦</span><span className="text-[10px] font-black uppercase mt-2 tracking-widest">Kids</span></div>
                        <div className="flex flex-col items-center"><span className="text-4xl">👨‍💻</span><span className="text-[10px] font-black uppercase mt-2 tracking-widest">Adults</span></div>
                        <div className="flex flex-col items-center"><span className="text-4xl">🧓</span><span className="text-[10px] font-black uppercase mt-2 tracking-widest">Elders</span></div>
                    </div>
                </RevealSection>
            </div>
        </>
    );
}

About.layout = page => <MainLayout children={page} />;