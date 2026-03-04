import React from 'react';
import { Head } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import RevealSection from '@/Components/RevealSection';

export default function PrivacyPolicy({ appName }) {
    return (
        <>
            <Head title={`Kebijakan Privasi - ${appName}`} />
            <div className="pt-40 pb-32 px-6 max-w-7xl mx-auto">
                <RevealSection className="text-center mb-16" direction="down">
                    <p className="text-[10px] font-black text-nu-teal uppercase tracking-[0.5em] mb-4">Legal Framework</p>
                    <h1 className="text-5xl lg:text-7xl font-serif font-black text-nu-indigo uppercase mb-8 tracking-tighter">Kebijakan Privasi.</h1>
                    <div className="w-20 h-1 bg-nu-teal mx-auto rounded-full"></div>
                </RevealSection>

                <div className="grid lg:grid-cols-2 gap-20">
                    <RevealSection className="bg-white p-12 lg:p-16 rounded-[4rem] border border-slate-100 shadow-[0_20px_50px_rgba(0,0,0,0.02)]" delay="100ms" direction="left">
                        <section className="space-y-6">
                            <h2 className="text-2xl font-serif font-black text-nu-indigo uppercase tracking-tight">1. Pengumpulan Informasi</h2>
                            <p className="text-slate-500 font-medium leading-relaxed">
                                Kami mengumpulkan jenis informasi berikut saat Anda menggunakan Aplikasi Muslim Level Up:
                            </p>
                            <ul className="list-disc list-inside space-y-2 text-sm text-slate-500 font-medium">
                                <li><strong>Informasi Akun:</strong> Alamat email dan nama pengguna saat pendaftaran.</li>
                                <li><strong>Data Penggunaan:</strong> Log aktivitas ibadah (salat, dzikir, dll) yang Anda catat.</li>
                                <li><strong>Informasi Perangkat:</strong> Jenis perangkat genggam yang digunakan.</li>
                                <li><strong>Data Lokasi:</strong> Estimasi lokasi sementara hanya untuk komputasi arah kiblat dan waktu salat.</li>
                            </ul>
                        </section>

                        <section className="mt-12 space-y-6">
                            <h2 className="text-2xl font-serif font-black text-nu-indigo uppercase tracking-tight">2. Penggunaan Informasi</h2>
                            <p className="text-slate-500 font-medium leading-relaxed">
                                Informasi yang dikumpulkan hanya digunakan secara eksklusif untuk:
                            </p>
                            <ul className="list-disc list-inside space-y-2 text-sm text-slate-500 font-medium">
                                <li>Menyediakan layanan inti aplikasi (pengingat waktu salat, pelacakan ibadah).</li>
                                <li>Menyinkronisasi data antar perangkat Anda.</li>
                                <li>Memperbaiki bug dan mengoptimalkan performa aplikasi.</li>
                            </ul>
                            <p className="text-slate-500 font-medium leading-relaxed">
                                Kami secara tegas <strong>TIDAK PERNAH</strong> menjual atau menyewakan data Anda kepada pengiklan atau pihak ketiga manapun.
                            </p>
                        </section>
                    </RevealSection>

                    <RevealSection className="space-y-12" delay="200ms" direction="right">
                        <div className="bg-nu-indigo p-12 lg:p-16 rounded-[4rem] text-white">
                            <h2 className="text-2xl font-serif font-black uppercase mb-6">3. Keamanan Data</h2>
                            <p className="text-white/70 font-medium leading-relaxed mb-8">
                                Privasi adalah prioritas tertinggi kami. Semua data Habit Tracker dan profil spiritual Anda dienkripsi secara end-to-end baik saat transit (TLS) maupun saat istirahat (Data at Rest) di server kami.
                            </p>
                            <div className="p-6 bg-white/5 rounded-3xl border border-white/10">
                                <p className="text-[10px] font-black uppercase tracking-widest text-nu-teal mb-2">Security Standard</p>
                                <p className="text-xs font-medium text-white/50">Industry Standard AES-256 Encryption.</p>
                            </div>
                        </div>

                        <div className="p-12 border-2 border-slate-100 rounded-[3rem] bg-white">
                            <h2 className="text-xl font-serif font-black text-nu-indigo uppercase mb-4">4. Hak Pengguna</h2>
                            <p className="text-slate-500 text-sm font-medium leading-relaxed mb-6">
                                Sesuai pedoman perlindungan data global, Anda memiliki hak penuh atas akun Anda. Anda dapat meminta penghapusan akun permanen langsung melalui menu Pengaturan di aplikasi kapan saja.
                            </p>
                            <a href="mailto:legal@muslimlevelup.com" className="text-nu-teal font-black uppercase text-[10px] tracking-widest hover:underline">Pertanyaan Legal? Hubungi Kami →</a>
                        </div>
                    </RevealSection>
                </div>
            </div>
        </>
    );
}

PrivacyPolicy.layout = page => <MainLayout children={page} />;
