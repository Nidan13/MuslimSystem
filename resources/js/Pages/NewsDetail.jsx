import React from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import RevealSection from '@/Components/RevealSection';

export default function NewsDetail({ article, relatedNews = [] }) {
    if (!article) return null;

    return (
        <>
            <Head title={`${article.title} - Warta Ummah`} />

            <section className="bg-nu-indigo pt-48 pb-24 px-6 relative overflow-hidden">
                <div className="absolute inset-0 pointer-events-none opacity-[0.1] z-0"
                    style={{
                        backgroundImage: 'url(/images/mosque-pattern.png)',
                        backgroundSize: '400px',
                        backgroundPosition: 'center bottom',
                        backgroundRepeat: 'no-repeat'
                    }}
                ></div>
                <div className="max-w-4xl mx-auto relative z-10 text-white">
                    <RevealSection direction="down">
                        <div className="flex items-center gap-4 mb-8">
                            <Link href="/news" className="w-10 h-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center hover:bg-nu-teal transition-all text-white shadow-lg">
                                <i className="fas fa-arrow-left text-[10px]"></i>
                            </Link>
                            <span className="text-[10px] font-black uppercase tracking-[0.4em] opacity-60">Kembali ke Warta</span>
                        </div>
                        <p className="text-[10px] font-bold text-nu-teal uppercase tracking-[0.5em] mb-4">
                            {article.category ? article.category.name : 'Warta Updates'}
                        </p>
                        <h1 className="text-4xl lg:text-6xl font-serif font-black uppercase tracking-tighter mb-8 leading-tight">
                            {article.title}
                        </h1>
                        <div className="flex items-center gap-6 opacity-60 text-[10px] font-bold uppercase tracking-widest">
                            <div className="flex items-center gap-2">
                                📅 {new Date(article.published_at || article.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                            </div>
                            <div className="flex items-center gap-2">
                                👤 Admin Muslim Level Up
                            </div>
                        </div>
                    </RevealSection>
                </div>
            </section>

            <article className="py-24 px-6">
                <div className="max-w-7xl mx-auto grid lg:grid-cols-12 gap-16">
                    <div className="lg:col-span-8">
                        <RevealSection className="mb-16">
                            <div className="rounded-[3rem] overflow-hidden shadow-2xl border border-slate-100 aspect-[16/9] relative group">
                                <img
                                    src={article.image_url}
                                    alt={article.title}
                                    className="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-1000"
                                    loading="lazy"
                                />
                            </div>
                        </RevealSection>

                        <RevealSection className="mb-12">
                            <div className="prose prose-lg prose-slate max-w-none">
                                <p className="text-2xl font-serif font-bold text-nu-indigo mb-12 leading-relaxed italic border-l-4 border-nu-teal pl-8">
                                    {article.summary}
                                </p>

                                {(() => {
                                    const paragraphs = article.content.split('\n\n');
                                    const extraImages = article.images || [];

                                    return paragraphs.map((para, i) => (
                                        <React.Fragment key={i}>
                                            <div className="text-slate-600 leading-relaxed whitespace-pre-line text-lg mb-12">
                                                {para}
                                            </div>
                                            {extraImages[i] && (
                                                <div className="my-16 rounded-[3rem] overflow-hidden shadow-xl border border-slate-50 aspect-[16/10] relative group">
                                                    <img
                                                        src={extraImages[i]}
                                                        className="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-1000"
                                                        alt={`Article illustration ${i + 1}`}
                                                        loading="lazy"
                                                    />
                                                </div>
                                            )}
                                        </React.Fragment>
                                    ));
                                })()}
                            </div>
                        </RevealSection>
                    </div>

                    {/* Sidebar / Related News */}
                    <div className="lg:col-span-4 lg:sticky lg:top-32 h-fit">
                        <RevealSection direction="left">
                            <h4 className="text-xs font-black uppercase tracking-[0.3em] text-nu-indigo mb-8 pb-4 border-b border-slate-100">Warta Terkait</h4>
                            <div className="space-y-8">
                                {relatedNews.map(rel => (
                                    <Link key={rel.id} href={`/news/${rel.slug}`} className="group block">
                                        <div className="flex gap-4 p-4 rounded-3xl hover:bg-slate-50 transition-colors">
                                            <div className="w-20 h-20 rounded-2xl overflow-hidden flex-shrink-0">
                                                <img src={rel.image_url} className="w-full h-full object-cover grayscale opacity-50 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-500" />
                                            </div>
                                            <div>
                                                <h5 className="text-xs font-serif font-black text-nu-indigo group-hover:text-nu-teal transition-colors line-clamp-2 mb-2 uppercase leading-tight">{rel.title}</h5>
                                                <p className="text-[8px] font-bold text-slate-400 uppercase tracking-widest">{new Date(rel.published_at || rel.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })}</p>
                                            </div>
                                        </div>
                                    </Link>
                                ))}
                                {relatedNews.length === 0 && (
                                    <p className="text-[10px] text-slate-400 italic font-medium">Belum ada warta terkait dalam kategori ini.</p>
                                )}
                            </div>

                            <div className="mt-12 p-8 bg-nu-light rounded-[2.5rem] border border-slate-100">
                                <h4 className="text-sm font-serif font-black text-nu-indigo mb-3 uppercase">Gabung Circle</h4>
                                <p className="text-[10px] text-slate-500 font-medium mb-6 leading-relaxed">Dapatkan tantangan ibadah eksklusif di aplikasi Muslim Level Up.</p>
                                <Link href="/" className="w-full py-4 bg-nu-indigo text-white rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-slate-900 transition-all text-center block">Download APK</Link>
                            </div>
                        </RevealSection>
                    </div>
                </div>
            </article>
        </>
    );
}

NewsDetail.layout = page => <MainLayout children={page} />;
