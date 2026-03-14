import React from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import RevealSection from '@/Components/RevealSection';

export default function News({ news, categories = [], selectedCategory = null }) {
    return (
        <>
            <Head title="Warta Terbaru - Muslim Level Up" />
            
            <section className="bg-nu-teal pt-40 pb-24 px-6 relative overflow-hidden">
                <div className="absolute inset-0 pointer-events-none opacity-[0.1] z-0"
                    style={{
                        backgroundImage: 'url(/images/mosque-pattern.png)',
                        backgroundSize: '400px',
                        backgroundPosition: 'center bottom',
                        backgroundRepeat: 'no-repeat'
                    }}
                ></div>
                <div className="max-w-7xl mx-auto relative z-10 text-center text-white">
                    <RevealSection direction="down">
                        <p className="text-[10px] font-black uppercase tracking-[0.5em] mb-4 opacity-60">Insight & Updates</p>
                        <h1 className="text-5xl lg:text-7xl font-serif font-black uppercase tracking-tighter mb-6">Warta Ummah.</h1>
                        <p className="text-lg font-medium opacity-80 max-w-2xl mx-auto">
                            Dapatkan kabar terbaru seputar fitur, tips ibadah, dan perkembangan ekosistem Muslim Level Up.
                        </p>
                    </RevealSection>
                </div>
            </section>

            {/* Categories Filter */}
            <section className="py-12 border-b border-slate-100 bg-white sticky top-0 z-30">
                <div className="max-w-7xl mx-auto px-6">
                    <div className="flex flex-wrap items-center justify-center gap-4">
                        <Link 
                            href="/news" 
                            className={`px-6 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all ${!selectedCategory ? 'bg-nu-indigo text-white shadow-lg shadow-nu-indigo/20' : 'bg-slate-50 text-slate-400 hover:bg-slate-100'}`}
                        >
                            Semua
                        </Link>
                        {categories.map(cat => (
                            <Link 
                                key={cat.id} 
                                href={`/news?category=${cat.slug}`}
                                className={`px-6 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all ${selectedCategory === cat.slug ? 'bg-nu-indigo text-white shadow-lg shadow-nu-indigo/20' : 'bg-slate-50 text-slate-400 hover:bg-slate-100'}`}
                            >
                                {cat.name} <span className="ml-1 opacity-40">({cat.news_count})</span>
                            </Link>
                        ))}
                    </div>
                </div>
            </section>

            <section className="py-24 px-6">
                <div className="max-w-7xl mx-auto">
                    {news?.data?.length > 0 ? (
                        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
                            {news.data.map((article, i) => (
                                <RevealSection key={article.id} delay={`${i * 100}ms`} direction="up">
                                    <Link href={`/news/${article.slug}`} className="group bg-white rounded-[2.5rem] border border-slate-100 overflow-hidden hover:shadow-2xl hover:shadow-nu-indigo/5 transition-all duration-500 h-full flex flex-col cursor-pointer">
                                        <div className="aspect-[16/10] overflow-hidden relative">
                                            {article.category && (
                                                <div className="absolute top-6 left-6 z-10">
                                                    <span className="px-4 py-1.5 bg-white/90 backdrop-blur-md text-[8px] font-black uppercase tracking-widest text-nu-indigo rounded-full shadow-sm">
                                                        {article.category.name}
                                                    </span>
                                                </div>
                                            )}
                                            {article.images && article.images.length > 0 && (
                                                <div className="absolute top-6 right-6 z-10">
                                                    <span className="px-3 py-1.5 bg-nu-teal/90 backdrop-blur-md text-[7px] font-black uppercase tracking-widest text-white rounded-full shadow-sm flex items-center gap-1.5">
                                                        <i className="fas fa-images text-[8px]"></i>
                                                        +{article.images.length}
                                                    </span>
                                                </div>
                                            )}
                                            <img 
                                                src={article.image_url || 'https://images.unsplash.com/photo-1542831371-29b0f74f9713?q=80&w=800&auto=format&fit=crop'} 
                                                alt={article.title} 
                                                className="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700"
                                                loading="lazy"
                                            />
                                        </div>
                                        <div className="p-10 flex-grow">
                                            <p className="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                                <span className="w-1 h-1 rounded-full bg-nu-teal"></span>
                                                {new Date(article.published_at || article.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                                            </p>
                                            <h3 className="text-2xl font-serif font-black text-nu-indigo mb-5 group-hover:text-nu-teal transition-colors leading-tight line-clamp-2">
                                                {article.title}
                                            </h3>
                                            <p className="text-slate-500 text-base leading-relaxed line-clamp-3 mb-8">
                                                {article.summary}
                                            </p>
                                        </div>
                                        <div className="p-8 pt-0 mt-auto">
                                            <div className="w-full py-4 bg-slate-50 group-hover:bg-nu-indigo text-nu-indigo group-hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] flex items-center justify-center gap-2 transition-all duration-300">
                                                Selengkapnya <span className="transform group-hover:translate-x-1 transition-transform">→</span>
                                            </div>
                                        </div>
                                    </Link>
                                </RevealSection>
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-32 bg-slate-50 rounded-[4rem] border border-dashed border-slate-200">
                            <p className="text-slate-400 font-medium">Belum ada warta yang dipublikasikan.</p>
                        </div>
                    )}

                    {/* Pagination */}
                    {news.links && news.links.length > 3 && (
                        <div className="mt-20 flex justify-center gap-3">
                            {news.links.map((link, i) => (
                                <Link
                                    key={i}
                                    href={link.url}
                                    className={`px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all ${link.active ? 'bg-nu-teal text-white shadow-lg shadow-nu-teal/20' : 'bg-white text-nu-indigo border border-slate-100 hover:bg-slate-50'}`}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            ))}
                        </div>
                    )}
                </div>
            </section>
        </>
    );
}

News.layout = page => <MainLayout children={page} />;
