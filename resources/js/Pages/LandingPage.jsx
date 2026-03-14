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

const HeroSlider = ({ items }) => {
    const [active, setActive] = useState(0);
    useEffect(() => {
        const interval = setInterval(() => {
            setActive(prev => (prev + 1) % items.length);
        }, 5000);
        return () => clearInterval(interval);
    }, [items.length]);

    if (!items || items.length === 0) return null;

    return (
        <div className="w-full h-[500px] relative rounded-[3rem] overflow-hidden shadow-2xl group border-4 border-white/10">
            {items.map((item, i) => (
                <div key={i} className={`absolute inset-0 transition-all duration-1000 ${active === i ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-12 pointer-events-none'}`}>
                    {item.image_url ? (
                        <img src={item.image_url} alt={item.title} className="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-1000" />
                    ) : (
                        <div className="w-full h-full bg-slate-800 flex items-center justify-center text-white p-8 text-center text-4xl">{item.icon || '🚀'}</div>
                    )}
                    <div className="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent opacity-80 z-10"></div>
                    <div className="absolute bottom-0 left-0 right-0 p-10 z-20 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                        <div className="w-12 h-1 bg-nu-teal mb-6 rounded-full"></div>
                        <h3 className="text-3xl font-serif font-black uppercase tracking-tighter mb-3 text-white">{item.title}</h3>
                        <p className="font-medium text-white/80 line-clamp-2 md:text-lg mb-6 max-w-md">{item.description}</p>
                        {item.button_text && item.button_url && (
                            <Link href={item.button_url} className="px-8 py-4 bg-nu-teal text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-teal-500 transition-colors inline-block">{item.button_text}</Link>
                        )}
                    </div>
                </div>
            ))}
            <div className="absolute top-8 right-8 flex gap-2 z-30">
                {items.map((_, i) => (
                    <button key={i} onClick={() => setActive(i)} className={`h-1.5 rounded-full transition-all duration-500 ${active === i ? 'w-10 bg-nu-teal' : 'w-3 bg-white/40 hover:bg-white/80'}`}></button>
                ))}
            </div>
        </div>
    );
};

const FeatureCards = ({ items, isDark }) => {
    if (!items || items.length === 0) return null;
    return (
        <div className="grid md:grid-cols-3 gap-6 mt-16 w-full relative z-10 w-full mb-8">
            {items.map((item, i) => (
                <RevealSection key={i} delay={`${i * 200}ms`} direction="up">
                    <div className={`p-8 rounded-[2.5rem] border ${isDark ? 'bg-slate-800/80 backdrop-blur border-white/10 hover:border-nu-teal' : 'bg-white border-slate-100 shadow-xl shadow-slate-200/50 hover:border-nu-teal hover:shadow-cyan-100'} h-full transition-all hover:-translate-y-2 duration-500 group`}>
                        <div className={`w-16 h-16 rounded-2xl flex items-center justify-center text-3xl mb-8 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-6 ${isDark ? 'bg-white/5 text-nu-teal' : 'bg-nu-teal/10 text-nu-teal'}`}>
                            {item.icon || '✨'}
                        </div>
                        <h3 className={`text-xl font-serif font-black uppercase tracking-tight mb-4 ${isDark ? 'text-white' : 'text-nu-indigo'}`}>{item.title}</h3>
                        <p className={`font-medium leading-relaxed ${isDark ? 'text-white/60' : 'text-slate-500'}`}>{item.description}</p>
                    </div>
                </RevealSection>
            ))}
        </div>
    );
};

const HumanCentricGrid = ({ items, isDark }) => {
    if (!items || items.length === 0) return null;
    return (
        <div className="grid grid-cols-2 gap-4 mt-12 w-full max-w-lg relative z-20">
            {items.map((item, i) => (
                <RevealSection key={i} delay={`${i * 150}ms`} direction="up">
                    <div className={`p-6 rounded-[2rem] border ${isDark ? 'bg-slate-800/50 backdrop-blur border-white/5 hover:border-nu-teal' : 'bg-white border-slate-100 shadow-lg shadow-slate-200/50 hover:border-nu-teal hover:shadow-cyan-50'} transition-all duration-500 group`}>
                        <div className={`w-12 h-12 rounded-xl flex items-center justify-center text-xl mb-5 group-hover:scale-110 transition-transform duration-500 ${isDark ? 'bg-white/10 text-white' : 'bg-slate-50 text-nu-indigo'}`}>{item.icon || '🎯'}</div>
                        <h4 className={`text-sm font-black uppercase tracking-tight mb-2 ${isDark ? 'text-white' : 'text-nu-indigo'}`}>{item.title}</h4>
                        <p className={`text-[10px] font-bold leading-relaxed ${isDark ? 'text-white/50' : 'text-slate-400'}`}>{item.description}</p>
                    </div>
                </RevealSection>
            ))}
        </div>
    );
};

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
        <section className="py-64 px-6 bg-slate-50 text-nu-indigo overflow-hidden relative border-t border-slate-100">
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

const DynamicSection = ({ section, downloadUrl }) => {
    const isReversed = section.style === 'reversed';
    const isDark = section.style === 'dark';
    const isCards = section.style === 'cards';

    const bgClass = isDark ? 'bg-slate-900 text-white border-t border-white/5' : 'bg-white text-nu-indigo border-t border-slate-100';
    const textIndigoClass = isDark ? 'text-white' : 'text-nu-indigo';
    const textMutedClass = isDark ? 'text-white/70' : 'text-slate-500';

    if (section.type === 'hero') {
        return (
            <section className={`px-6 relative overflow-hidden ${isDark ? 'bg-slate-900' : 'bg-nu-teal text-white'}`} style={{ padding: '160px 0' }}>
                {!isDark && (
                    <div
                        className="absolute inset-0 pointer-events-none opacity-[0.2] z-0"
                        style={{ backgroundImage: 'url(/images/mosque-pattern.png)', backgroundSize: '800px', backgroundPosition: 'center bottom', backgroundRepeat: 'no-repeat' }}
                    ></div>
                )}
                <div className="max-w-7xl mx-auto grid lg:grid-cols-2 gap-20 items-center relative z-10">
                    <RevealSection className={`${isReversed ? 'lg:order-2' : 'lg:order-1'}`} direction={isReversed ? "right" : "left"}>
                        {section.subtitle && <p className={`text-[10px] font-black uppercase tracking-[0.5em] mb-4 ${isDark ? 'text-nu-teal' : 'text-white/80'}`}>{section.subtitle}</p>}
                        <h1 className={`text-5xl lg:text-7xl font-serif font-black leading-[0.95] mb-8 uppercase tracking-tighter ${isDark ? 'text-white' : 'text-white'}`}>
                            {section.title}
                        </h1>
                        {section.content && <div className={`text-lg font-medium mb-10 max-w-lg leading-relaxed whitespace-pre-line ${isDark ? 'text-white/70' : 'text-white/80'}`}>{section.content}</div>}

                        <div className="flex flex-col sm:flex-row gap-5">
                            {section.button_text && section.button_url ? (
                                <Link href={section.button_url} className={`px-10 py-5 rounded-2xl font-black uppercase text-xs tracking-widest transition-all text-center ${isDark ? 'bg-nu-teal text-white hover:bg-teal-600' : 'bg-white text-nu-teal hover:bg-slate-50'}`}>
                                    {section.button_text}
                                </Link>
                            ) : (
                                <HeroDownloadButton downloadUrl={downloadUrl} />
                            )}
                        </div>
                    </RevealSection>
                    <RevealSection delay="400ms" direction="up" className={isReversed ? 'lg:order-1' : 'lg:order-2'}>
                        {section.items && section.items.length > 0 ? (
                            <HeroSlider items={section.items} />
                        ) : section.image_url ? (
                            <div className="w-full h-[500px] rounded-[3rem] overflow-hidden shadow-2xl relative group border-4 border-white/10">
                                <img src={section.image_url} alt={section.title} className="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-1000" />
                            </div>
                        ) : (
                            <SmartphoneLayered />
                        )}
                    </RevealSection>
                </div>
            </section>
        );
    }

    if (section.type === 'background_story') {
        return (
            <section className={`${bgClass} px-6 relative overflow-hidden`} style={{ padding: '160px 0' }}>
                <div className="max-w-7xl mx-auto">
                    <div className="grid lg:grid-cols-2 gap-20 items-center">
                        <RevealSection direction="right">
                            {section.subtitle && <p className="text-[10px] font-black uppercase tracking-[0.5em] mb-4 text-nu-teal">{section.subtitle}</p>}
                            <h2 className={`text-4xl lg:text-6xl font-serif font-black uppercase leading-[0.9] tracking-tighter mb-10 ${textIndigoClass}`}>
                                {section.title}
                            </h2>
                            <div className={`${textMutedClass} text-lg font-medium leading-relaxed mb-12 whitespace-pre-line`}>
                                {section.content}
                            </div>
                        </RevealSection>
                        <div className="grid grid-cols-2 gap-6">
                            {section.items && section.items.map((item, i) => (
                                <RevealSection key={i} delay={`${i * 100}ms`} direction="up">
                                    <div className={`p-8 rounded-[2.5rem] border transition-all duration-500 hover:shadow-xl ${isDark ? 'bg-white/5 border-white/10' : 'bg-slate-50 border-slate-100'}`}>
                                        <div className="text-4xl mb-6">{item.icon || '✨'}</div>
                                        <h4 className={`text-sm font-black uppercase tracking-widest mb-3 ${textIndigoClass}`}>{item.title}</h4>
                                        <p className={`text-xs font-medium leading-relaxed ${textMutedClass}`}>{item.description}</p>
                                    </div>
                                </RevealSection>
                            ))}
                        </div>
                    </div>
                </div>
            </section>
        );
    }

    if (section.type === 'about' || section.type === 'feature_cards' || section.type === 'human_centric_grid' || section.type === 'features') {
        const isCardsGrid = section.type === 'feature_cards';
        const isHumanGrid = section.type === 'human_centric_grid';

        return (
            <section className={`${bgClass} px-6 relative overflow-hidden animate-fade-slide`} style={{ padding: '160px 0' }}>
                {isDark && (
                    <div className="absolute top-0 right-0 w-96 h-96 bg-nu-teal/10 rounded-full blur-3xl pointer-events-none -translate-y-1/2 translate-x-1/3"></div>
                )}
                <div className="max-w-7xl mx-auto flex flex-col items-center">
                    <div className={`grid lg:grid-cols-2 gap-20 items-center w-full ${isCardsGrid ? 'flex flex-col text-center items-center' : ''}`}>
                        <RevealSection className={isCardsGrid ? 'w-full text-center max-w-3xl mx-auto' : (isReversed ? 'lg:order-2' : 'lg:order-1')} direction={isCardsGrid ? "up" : (isReversed ? "left" : "right")}>
                            {section.subtitle && <p className={`text-[10px] font-black uppercase tracking-[0.5em] mb-4 ${isDark ? 'text-nu-teal' : 'text-nu-teal'}`}>{section.subtitle}</p>}
                            <h2 className={`text-4xl lg:text-6xl font-serif font-black uppercase leading-[0.9] tracking-tighter mb-10 ${textIndigoClass}`}>
                                {section.title}
                            </h2>
                            {section.content && <div className={`${textMutedClass} text-lg font-medium leading-relaxed mb-12 max-w-lg whitespace-pre-line ${isCardsGrid ? 'mx-auto' : ''}`}>{section.content}</div>}

                            {isHumanGrid && <HumanCentricGrid items={section.items} isDark={isDark} />}

                            {section.button_text && section.button_url && !isCardsGrid && (
                                <Link href={section.button_url} className={`px-8 py-4 mt-8 rounded-2xl font-black uppercase text-[10px] tracking-widest transition-all inline-block ${isDark ? 'bg-nu-teal text-white hover:bg-teal-600' : 'bg-nu-indigo text-white hover:bg-slate-900'}`}>
                                    {section.button_text}
                                </Link>
                            )}
                        </RevealSection>

                        {!isCardsGrid && (
                            <RevealSection delay="400ms" direction="up" className={isReversed ? 'lg:order-1' : 'lg:order-2'}>
                                {section.image_url ? (
                                    <div className={`w-full h-[500px] rounded-[3rem] overflow-hidden shadow-2xl relative group ${isDark ? 'border-4 border-white/5' : ''}`}>
                                        <div className="absolute inset-0 bg-nu-indigo/20 group-hover:bg-transparent transition-colors duration-700 z-10 pointer-events-none"></div>
                                        <img src={section.image_url} alt={section.title} className="w-full h-full object-cover transform hover:scale-110 transition-transform duration-1000" />
                                    </div>
                                ) : (
                                    <div className={`w-full h-[500px] rounded-[3rem] border-2 border-dashed flex items-center justify-center ${isDark ? 'bg-slate-800/50 border-white/10' : 'bg-slate-200/50 border-slate-300'}`}>
                                        <p className={`${textMutedClass} font-medium italic`}>Visual Content Area</p>
                                    </div>
                                )}
                            </RevealSection>
                        )}
                    </div>

                    {isCardsGrid && <FeatureCards items={section.items} isDark={isDark} />}
                </div>
            </section>
        );
    }

    if (section.type === 'impact_gallery') {
        return <ImpactGallery />;
    }

    if (section.type === 'cta') {
        return (
            <section className={`px-6 relative overflow-hidden text-center ${isDark ? 'bg-slate-900 border-t border-white/5' : 'bg-nu-teal text-white'}`} style={{ padding: '160px 0' }}>
                <div className="max-w-3xl mx-auto relative z-10">
                    <RevealSection>
                        {section.subtitle && <p className={`text-[10px] font-black uppercase tracking-[0.5em] mb-4 ${isDark ? 'text-nu-teal' : 'opacity-70'}`}>{section.subtitle}</p>}
                        <h2 className={`text-4xl lg:text-6xl font-serif font-black uppercase leading-[0.9] tracking-tighter mb-8 ${isDark ? 'text-white' : ''}`}>{section.title}</h2>
                        {section.content && <div className={`text-lg font-medium mb-10 whitespace-pre-line leading-relaxed ${isDark ? 'text-white/70' : 'opacity-90'}`}>{section.content}</div>}

                        {section.button_text && section.button_url ? (
                            <Link href={section.button_url} className={`px-10 py-5 rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl inline-block transition-all hover:scale-105 ${isDark ? 'bg-nu-teal text-white shadow-nu-teal/20' : 'bg-nu-indigo text-white hover:bg-slate-900 shadow-nu-indigo/20'}`}>
                                {section.button_text}
                            </Link>
                        ) : (
                            <HeroDownloadButton downloadUrl={downloadUrl} />
                        )}
                    </RevealSection>
                </div>
            </section>
        );
    }
};

export default function LandingPage({ appName, downloadUrl, latestNews = [], sections = [] }) {
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

            {/* Dynamic Sections from CMS */}
            {sections && sections.length > 0 && (
                sections.map((section, index) => (
                    <DynamicSection key={section.id || index} section={section} downloadUrl={downloadUrl} />
                ))
            )}




            {/* News Section */}
            {latestNews && latestNews.length > 0 && (
                <section className="bg-slate-50 border-t border-slate-100" style={{ padding: '160px 0' }}>
                    <div className="max-w-7xl mx-auto">
                        <RevealSection className="mb-16">
                            <p className="text-[10px] font-black text-nu-teal uppercase tracking-[0.5em] mb-4">Latest Updates</p>
                            <h2 className="text-4xl lg:text-6xl font-serif font-black text-nu-indigo uppercase tracking-tighter leading-none">Warta <span className="text-nu-teal italic-reset">Terbaru.</span></h2>
                        </RevealSection>

                        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                            {latestNews.map((news, i) => (
                                <RevealSection key={i} delay={`${i * 200}ms`} direction="up">
                                    <div className="group bg-white rounded-[2.5rem] border border-slate-100 overflow-hidden hover:shadow-2xl hover:shadow-nu-indigo/5 transition-all duration-500">
                                        <div className="aspect-[16/10] overflow-hidden relative">
                                            <div className="absolute top-6 left-6 z-10 flex gap-2">
                                                <span className="px-4 py-1.5 bg-white/90 backdrop-blur-md text-[10px] font-black uppercase tracking-widest text-nu-indigo rounded-full shadow-sm">
                                                    {news.category ? news.category.name : 'Warta'}
                                                </span>
                                            </div>
                                            <img
                                                src={news.image_url || 'https://images.unsplash.com/photo-1584281723358-132d431f47f9?q=80&w=800&auto=format&fit=crop'}
                                                alt={news.title}
                                                className="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700"
                                            />
                                        </div>
                                        <div className="p-8">
                                            <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">{new Date(news.published_at || news.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</p>
                                            <h3 className="text-xl font-serif font-black text-nu-indigo mb-4 group-hover:text-nu-teal transition-colors leading-tight line-clamp-2">
                                                {news.title}
                                            </h3>
                                            <p className="text-slate-500 text-sm leading-relaxed line-clamp-2 mb-8">
                                                {news.summary}
                                            </p>
                                            <Link href={`/news/${news.slug}`} className="text-[10px] font-black uppercase tracking-[0.2em] text-nu-indigo flex items-center gap-2 group/link">
                                                Baca Selengkapnya <span className="transform group-hover/link:translate-x-1 transition-transform">→</span>
                                            </Link>
                                        </div>
                                    </div>
                                </RevealSection>
                            ))}
                        </div>

                        <div className="mt-16 text-center">
                            <Link href="/news" className="px-10 py-5 bg-nu-indigo text-white rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-slate-900 transition-all shadow-xl shadow-nu-indigo/20 inline-block">Lihat Semua Berita</Link>
                        </div>
                    </div>
                </section>
            )}

        </>
    );
}



LandingPage.layout = page => <MainLayout children={page} />;
