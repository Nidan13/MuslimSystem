import React from 'react';
import { Head } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import RevealSection from '@/Components/RevealSection';

const FAQItem = ({ question, answer }) => {
    const [isOpen, setIsOpen] = React.useState(false);
    return (
        <div className="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-[0_20px_50px_rgba(0,0,0,0.02)] hover:border-nu-teal transition-all group">
            <button onClick={() => setIsOpen(!isOpen)} className="w-full flex justify-between items-start text-left">
                <span className="text-xl font-serif font-black text-nu-indigo uppercase tracking-tight pr-8">{question}</span>
                <span className={`w-8 h-8 rounded-full bg-nu-light flex items-center justify-center text-nu-teal text-xl transform transition-transform duration-300 ${isOpen ? 'rotate-45 bg-nu-teal text-white' : ''}`}>+</span>
            </button>
            <div className={`overflow-hidden transition-all duration-300 ${isOpen ? 'max-h-96 mt-6' : 'max-h-0'}`}>
                <p className="text-slate-500 leading-relaxed font-medium">{answer}</p>
            </div>
        </div>
    );
};

export default function FAQ({ appName }) {
    const faqs = [
        {
            group: "Umum",
            items: [
                { question: "Bagaimana cara aplikasi ini merubah habit?", answer: "Muslim Level Up menggunakan framework 'Spiritual Gamification' di mana setiap ibadah yang Anda catat akan memberikan insight visual yang memotivasi Anda untuk tetap konsisten." },
                { question: "Apakah aplikasi ini gratis selamanya?", answer: "Ya, fitur utama seperti Al-Quran dan Habit Tracker tersedia secara gratis. Fokus kami adalah kebermanfaatan umat." }
            ]
        },
        {
            group: "Teknis",
            items: [
                { question: "Bagaimana keamanan data saya?", answer: "Data Anda adalah amanah. Kami mengenkripsi data sinkronisasi akun Anda dan tidak pernah membagi informasi pribadi kepada pihak ketiga." },
                { question: "Dapatkah digunakan tanpa internet?", answer: "Tentu. Fitur Al-Quran, Dzikir, dan Jadwal Salat dapat berjalan offline setelah data awal berhasil diunduh." }
            ]
        },
        {
            group: "Fitur",
            items: [
                { question: "Apakah ada versi untuk iOS?", answer: "Saat ini kami sedang mengembangkan integrasi untuk iOS agar Anda bisa mengakses aplikasi kami di perangkat iOS." },
                { question: "Bagaimana cara lapor bug?", answer: "Anda bisa mengirim pesan melalui menu 'Support' di dalam aplikasi atau melalui email kami." }
            ]
        }
    ];

    return (
        <>
            <Head title={`FAQ - ${appName}`} />
            <div className="pt-40 pb-32 px-6 max-w-7xl mx-auto">
                <RevealSection className="text-center mb-24" direction="down">
                    <p className="text-[10px] font-black text-nu-teal uppercase tracking-[0.5em] mb-4">Support Center</p>
                    <h1 className="text-5xl lg:text-7xl font-serif font-black text-nu-indigo uppercase mb-8 tracking-tighter">Pertanyaan Umum.</h1>
                    <div className="w-20 h-1 bg-nu-teal mx-auto rounded-full"></div>
                </RevealSection>

                <div className="grid lg:grid-cols-2 gap-x-12 gap-y-20">
                    {faqs.map((group, idx) => (
                        <RevealSection delay={`${idx * 150}ms`} direction="up" key={idx} className="space-y-10">
                            <h2 className="text-[10px] font-black text-nu-indigo opacity-30 uppercase tracking-[0.5em] pl-4">{group.group}</h2>
                            <div className="grid gap-6">
                                {group.items.map((faq, i) => (
                                    <FAQItem key={i} question={faq.question} answer={faq.answer} />
                                ))}
                            </div>
                        </RevealSection>
                    ))}
                </div>
            </div>
        </>
    );
}

FAQ.layout = page => <MainLayout children={page} />;
