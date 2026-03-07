import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';

// Premium Download Modal (Shared)
const DownloadModal = ({ isOpen, onClose, downloadUrl }) => {
    return (
        <div className={`fixed inset-0 z-[100] flex items-center justify-center px-6 transition-all duration-500 ${isOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'}`}>
            <div className="absolute inset-0 bg-nu-indigo/40 backdrop-blur-md" onClick={onClose}></div>
            <div className={`relative w-full max-w-sm bg-white rounded-[2rem] p-8 shadow-2xl transition-all duration-500 transform ${isOpen ? 'scale-100 translate-y-0' : 'scale-95 translate-y-4'} border border-slate-100`}>
                <div className="text-center">
                    <div className="w-16 h-16 bg-white rounded-full mx-auto mb-6 flex items-center justify-center shadow-md p-1 border border-slate-100">
                        <img src="/images/logo.png" alt="Logo" className="w-full h-full object-contain" />
                    </div>
                    <h3 className="text-xl font-serif font-black text-nu-indigo mb-2 uppercase">Muslim Level Up</h3>
                    <p className="text-[10px] text-slate-400 font-bold mb-8 uppercase tracking-widest">Siap untuk perubahan?</p>
                    <a
                        href={downloadUrl}
                        download
                        name="muslim-app.apk"
                        className="block w-full py-4 bg-nu-teal text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-nu-teal-dark transition-all shadow-lg shadow-nu-teal/20 mb-3 text-center"
                    >
                        Ya, Download APK
                    </a>
                    <button onClick={onClose} className="w-full py-4 bg-slate-50 text-nu-indigo rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-100 transition-colors">Batal</button>
                </div>
            </div>
        </div>
    );
};

export default function MainLayout({ children }) {
    const { auth, downloadUrl } = usePage().props;
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);

    const toggleSidebar = () => setIsSidebarOpen(!isSidebarOpen);

    return (
        <div className="min-h-screen bg-nu-light font-sans text-nu-indigo antialiased">
            <DownloadModal isOpen={isModalOpen} onClose={() => setIsModalOpen(false)} downloadUrl={downloadUrl} />

            {/* Persistent Navbar */}
            <nav className="fixed top-0 w-full z-50 bg-nu-teal/95 backdrop-blur-md h-20 border-b border-white/10">
                <div className="max-w-7xl mx-auto px-6 h-full flex justify-between items-center text-white">
                    <Link href="/" className="flex items-center gap-3 group">
                        <div className="w-10 h-10 bg-white rounded-full flex items-center justify-center p-1 shadow-sm overflow-hidden group-hover:scale-110 transition-transform">
                            <img src="/images/logo.png" className="w-full h-full object-contain" alt="Logo" />
                        </div>
                        <span className="font-serif font-black text-xl uppercase tracking-tighter">MLU</span>
                    </Link>

                    {/* Desktop Links */}
                    <div className="hidden lg:flex gap-8 font-bold text-[10px] uppercase tracking-widest">
                        <Link href="/features" className="hover:text-white/60 transition-colors">Features</Link>
                        <Link href="/about" className="hover:text-white/60 transition-colors">About</Link>
                        <Link href="/faq" className="hover:text-white/60 transition-colors">FAQ</Link>
                        <Link href="/privacy" className="hover:text-white/60 transition-colors">Privacy Policy</Link>
                    </div>

                    <div className="flex items-center gap-4">
                        <button
                            onClick={() => setIsModalOpen(true)}
                            className="hidden md:block px-6 py-2.5 bg-white text-nu-teal rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-nu-light transition-all shadow-lg shadow-black/10"
                        >
                            Get APK
                        </button>

                        {auth.user ? (
                            <Link
                                href="/dashboard"
                                className="hidden md:block px-6 py-2.5 bg-nu-indigo text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 transition-all shadow-lg shadow-black/10"
                            >
                                Dashboard
                            </Link>
                        ) : (
                            <Link
                                href="/login"
                                className="hidden md:block px-6 py-2.5 bg-nu-indigo/20 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-white/10 transition-all border border-white/20 shadow-lg"
                            >
                                Login
                            </Link>
                        )}

                        {/* Mobile Side Toggle */}
                        <button
                            onClick={toggleSidebar}
                            className="lg:hidden w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center hover:bg-white/20 transition-all border border-white/10"
                        >
                            <svg className={`w-5 h-5 transition-transform duration-500 ${isSidebarOpen ? 'rotate-90' : '0'}`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {isSidebarOpen ? (
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M6 18L18 6M6 6l12 12" />
                                ) : (
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M4 6h16M4 12h16M4 18h16" />
                                )}
                            </svg>
                        </button>
                    </div>
                </div>
            </nav>

            {/* Mobile Sidebar (Premium Drawer) */}
            <div className={`fixed inset-0 z-[60] lg:hidden transition-all duration-700 ${isSidebarOpen ? 'visible' : 'invisible'}`}>
                {/* Backdrop */}
                <div
                    className={`absolute inset-0 bg-nu-indigo/40 backdrop-blur-sm transition-opacity duration-700 ${isSidebarOpen ? 'opacity-100' : 'opacity-0'}`}
                    onClick={() => setIsSidebarOpen(false)}
                ></div>

                {/* Drawer */}
                <aside className={`absolute top-0 right-0 w-[300px] h-full bg-nu-indigo border-l border-white/10 transition-transform duration-700 transform ${isSidebarOpen ? 'translate-x-0 shadow-[-20px_0_100px_rgba(0,0,0,0.5)]' : 'translate-x-full'}`}>
                    <div className="pt-24 px-8 flex flex-col h-full">
                        {/* RPG Badge Area */}
                        <div className="mb-12 p-6 bg-white/5 rounded-3xl border border-white/10">
                            <div className="flex items-center gap-4 mb-4">
                                <div className="w-12 h-12 bg-nu-teal rounded-xl flex items-center justify-center text-white font-black">Lvl.1</div>
                                <div>
                                    <p className="text-nu-teal font-black text-[10px] uppercase tracking-widest">Aspirant</p>
                                    <h4 className="text-white font-serif font-black uppercase text-sm">Guest Ummah</h4>
                                </div>
                            </div>
                            <div className="h-1.5 w-full bg-white/10 rounded-full overflow-hidden">
                                <div className="h-full bg-nu-teal w-1/3"></div>
                            </div>
                        </div>

                        <div className="space-y-4 flex flex-col">
                            {auth.user && (
                                <Link
                                    href="/dashboard"
                                    onClick={() => setIsSidebarOpen(false)}
                                    className="group flex items-center gap-4 p-4 rounded-2xl bg-nu-teal/10 border border-nu-teal/20"
                                >
                                    <span className="text-xl">📊</span>
                                    <span className="text-[10px] font-black uppercase tracking-[0.2em] text-nu-teal">
                                        Admin Panel
                                    </span>
                                </Link>
                            )}
                            {[
                                { name: 'Home', path: '/', icon: '🏠' },
                                { name: 'Features', path: '/features', icon: '⚔️' },
                                { name: 'About', path: '/about', icon: '💎' },
                                { name: 'FAQ', path: '/faq', icon: '❓' },
                                { name: 'Privacy', path: '/privacy', icon: '🛡️' }
                            ].map((link, i) => (
                                <Link
                                    key={link.path}
                                    href={link.path}
                                    onClick={() => setIsSidebarOpen(false)}
                                    className="group flex items-center gap-4 p-4 rounded-2xl hover:bg-white/5 transition-all"
                                    style={{ transitionDelay: `${i * 50}ms` }}
                                >
                                    <span className="text-xl group-hover:scale-120 transition-transform">{link.icon}</span>
                                    <span className="text-[10px] font-black uppercase tracking-[0.2em] text-white/60 group-hover:text-nu-teal transition-colors">
                                        {link.name}
                                    </span>
                                </Link>
                            ))}
                        </div>

                        {/* Bottom Action Area */}
                        <div className="mt-auto pb-12">
                            <button
                                onClick={() => {
                                    setIsSidebarOpen(false);
                                    setIsModalOpen(true);
                                }}
                                className="w-full py-4 bg-nu-teal text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-nu-teal-dark transition-all shadow-lg shadow-nu-teal/20"
                            >
                                Get The App Now
                            </button>
                            {!auth.user && (
                                <Link
                                    href="/login"
                                    onClick={() => setIsSidebarOpen(false)}
                                    className="w-full py-4 mt-3 bg-white/5 text-white/60 border border-white/10 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-white/10 transition-all text-center"
                                >
                                    Login Admin
                                </Link>
                            )}
                            <p className="text-center text-[8px] font-bold text-white/20 uppercase tracking-[0.3em] mt-6">Built for Eternal Glory</p>
                        </div>
                    </div>
                </aside>
            </div>

            {/* Page Content */}
            <main className="transition-all duration-500">
                {children}
            </main>

            {/* Persistent Premium Footer */}
            <footer className="bg-nu-indigo py-24 px-6 text-white border-t border-white/5 relative overflow-hidden mt-auto">
                <div className="absolute top-0 right-0 w-96 h-96 bg-nu-teal/10 rounded-full blur-[100px] pointer-events-none"></div>
                <div className="max-w-7xl mx-auto relative z-10">
                    <div className="grid lg:grid-cols-2 gap-20 mb-20 items-center">
                        <div>
                            <div className="flex items-center gap-4 mb-8">
                                <div className="w-12 h-12 bg-white rounded-full flex items-center justify-center p-1.5 shadow-lg overflow-hidden">
                                    <img src="/images/logo.png" className="w-full h-full object-contain" alt="Logo" />
                                </div>
                                <span className="font-serif font-black text-2xl uppercase tracking-widest text-nu-teal">Muslim Level Up</span>
                            </div>
                            <p className="text-white/60 text-sm font-medium leading-relaxed max-w-sm mb-8">
                                Infrastruktur spiritual digital masa depan untuk ummah. Ekosistem kebaikan tanpa distraksi iklan.
                            </p>
                            <div className="flex gap-4">
                                <a href="mailto:support@muslimlevelup.com" className="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center hover:bg-white/10 hover:border-white/30 transition-all text-xl">✉️</a>
                                <div className="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center hover:bg-white/10 hover:border-white/30 transition-all text-xl">📱</div>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-12 lg:gap-24 lg:justify-end">
                            {[{ t: 'Produk', l: ['Features', 'FAQ'] }, { t: 'Legal', l: ['Privacy', 'About'] }].map(g => (
                                <div key={g.t}>
                                    <h5 className="text-[10px] font-black uppercase tracking-widest mb-6 text-nu-teal">{g.t}</h5>
                                    <ul className="space-y-4">
                                        {g.l.map(link => (
                                            <li key={link}>
                                                <Link href={`/${link.toLowerCase()}`} className="text-[10px] font-black uppercase tracking-widest text-white/50 hover:text-white hover:translate-x-1 inline-block transition-all">
                                                    {link}
                                                </Link>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            ))}
                        </div>
                    </div>
                    <div className="pt-10 border-t border-white/10 flex flex-col md:flex-row justify-between items-center gap-6">
                        <p className="text-[9px] font-black text-white/40 uppercase tracking-[0.4em]">© {new Date().getFullYear()} Muslim Level Up. Built With Taqwa.</p>
                        <p className="text-[9px] font-black text-white/40 uppercase tracking-[0.4em]">Made in Indonesia</p>
                    </div>
                </div>
            </footer>

            <style dangerouslySetInnerHTML={{
                __html: `
                @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@900&family=Outfit:wght@300;500;900&display=swap');
                .font-serif { font-family: 'Cinzel', serif; }
                .font-sans { font-family: 'Outfit', sans-serif; }
            `}} />
        </div>
    );
}
