<?php

namespace Database\Seeders;

use App\Models\Headline;
use App\Models\LandingPageSection;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LandingPageSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Fitur Baru', 'slug' => 'fitur-baru', 'type' => 'berita'],
            ['name' => 'Ibadah Insight', 'slug' => 'ibadah-insight', 'type' => 'berita'],
            ['name' => 'Event Ummah', 'slug' => 'event-ummah', 'type' => 'berita'],
            ['name' => 'Ekonomi Syariah', 'slug' => 'ekonomi-syariah', 'type' => 'berita'],
            ['name' => 'Pertanian', 'slug' => 'pertanian', 'type' => 'berita'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 2. Landing Page Sections layout (Restructured for Storytelling)
        LandingPageSection::truncate();

        $sections = [
            [
                'title' => 'Ubah Kebiasaan, Tingkatkan Ibadah.',
                'subtitle' => 'Muslim Level Up',
                'content' => "Aplikasi gamifikasi Islami pertama di Indonesia. Selesaikan misi ibadah harianmu, naikkan level spiral spiritualitas, dan ikuti perjalanan ribuan Muslim lainnya.",
                'order' => 0,
                'is_active' => true,
                'type' => 'hero',
                'style' => 'default',
            ],
            [
                'title' => 'Kenapa Kami Ada.',
                'subtitle' => 'Latar Belakang',
                'content' => "Rata-rata pengguna media sosial Indonesia menghabiskan 5-6 jam setiap hari untuk scroll konten tanpa tujuan.\n\nBanyak dari kita sadar bahwa waktu itu seharusnya bisa dipakai untuk hal yang jauh lebih bermakna—sholat tepat waktu, membaca Al-Quran, atau sekadar berbincang dengan keluarga.\n\nMuslim Level Up lahir dari kegelisahan itu. Kami ingin menghadirkan sebuah sistem yang membuat ibadah terasa seperti sebuah petualangan. Bukan beban.",
                'order' => 1,
                'is_active' => true,
                'type' => 'background_story',
                'style' => 'default',
                'items' => [
                    ['title' => '5-6 Jam/Hari', 'description' => 'Waktu terbuang untuk konten tanpa nilai di media sosial.', 'icon' => '📱'],
                    ['title' => '80% Drop', 'description' => 'Pengguna aplikasi islami berhenti karena membosankan.', 'icon' => '📉'],
                    ['title' => 'Gamifikasi', 'description' => 'Solusi kami: mengubah ibadah menjadi quest yang memotivasi.', 'icon' => '🎮'],
                    ['title' => 'Komunitas', 'description' => 'Bergabung dalam circle ibadah bersama sesama Muslim.', 'icon' => '🤝'],
                ]
            ],
            [
                'title' => 'Berjuang Bersama.',
                'subtitle' => 'Circle & Community',
                'content' => "Di Muslim Level Up, Anda tidak berjuang sendiri. Bergabung dalam Circle untuk mengerjakan misi ibadah secara kolektif.\r\n\r\nSaling mendukung, saling mengingatkan, dan bertumbuh bersama dalam ketaatan.",
                'order' => 3,
                'is_active' => true,
                'type' => 'about',
                'style' => 'reversed',
                'image_url' => '/storage/landing-page/8tMacKedvQpKemPrbfxMPeYozTjVKAyiadniRr0X.jpg',
            ],
            [
                'title' => 'Tiga Fase Transformasi.',
                'subtitle' => 'Filosofi Perubahan',
                'content' => 'Perjalanan spiritual Anda dirancang dalam tiga tahap evolusi kebiasaan.',
                'order' => 4,
                'is_active' => true,
                'type' => 'feature_cards',
                'style' => 'dark',
                'items' => [
                    ['title' => 'Dipaksa', 'description' => 'Membangun disiplin di awal perjalanan yang berat.', 'icon' => '⚔️'],
                    ['title' => 'Terbiasa', 'description' => 'Ketaatan mulai menjadi bagian dari identitas harian.', 'icon' => '♻️'],
                    ['title' => 'Menikmati', 'description' => 'Merasakan ketenangan jiwa dalam ibadah yang istiqomah.', 'icon' => '💎']
                ]
            ],
            [
                'title' => 'Mekanik Sistem RPG.',
                'subtitle' => 'Gamified Devotion',
                'content' => 'Bagaimana kami membantu Anda memantau investasi akhirat secara visual dan interaktif.',
                'order' => 5,
                'is_active' => true,
                'type' => 'human_centric_grid',
                'style' => 'default',
                'items' => [
                    ['title' => 'EXP System', 'description' => 'Setiap salat & dzikir memberikan Experience Point.', 'icon' => '📈'],
                    ['title' => 'Health Point', 'description' => 'Lalai menurunkan HP spiritual. Jaga kondisi imanmu.', 'icon' => '❤️'],
                    ['title' => 'Level Up', 'description' => 'Transformasi dari Newbie menjadi Istiqomah Legend.', 'icon' => '🆙'],
                    ['title' => 'Streaks', 'description' => 'Bangun momentum ibadah harian tanpa terputus.', 'icon' => '🔥']
                ]
            ],
            [
                'title' => 'Dampak Nyata.',
                'subtitle' => 'Ummah Impact',
                'content' => 'Realisasi visi kami dalam membentuk generasi yang lebih baik.',
                'order' => 6,
                'is_active' => true,
                'type' => 'impact_gallery',
                'style' => 'default',
            ],
            [
                'title' => 'Join the Revolution.',
                'subtitle' => 'Mulai Perjalananmu',
                'content' => 'Jangan tunggu besok. Masa mudamu terlalu berharga untuk dihabiskan hanya untuk scrolling fyp.',
                'type' => 'cta',
                'style' => 'dark',
                'order' => 8,
                'is_active' => true,
                'button_text' => 'Download App Sekarang',
                'button_url' => '#download',
            ]
        ];

        foreach ($sections as $section) {
            LandingPageSection::create($section);
        }

        // 3. News
        $newsItems = [
            [
                'title' => 'Luncurkan Fitur "Tartil AI": Kini Belajar Makharijul Huruf Bisa Lewat Genggaman',
                'summary' => 'Update terbaru aplikasi kini menghadirkan fitur Tartil AI, sebuah teknologi pendeteksi suara canggih yang mampu mengoreksi pelafalan ayat suci Al-Qur\'an secara real-time untuk membantu pengguna menyempurnakan tajwid mereka',
                'content' => 'Kabar gembira bagi para pencinta Al-Qur\'an! Kami resmi memperkenalkan fitur revolusioner bernama Tartil AI. Fitur ini dirancang khusus untuk menjadi "guru ngaji privat" digital yang siap menemani kapan saja dan di mana saja. Menggunakan algoritma Deep Learning yang dilatih dengan ribuan sampel suara qari internasional, fitur ini mampu mendeteksi akurasi makharijul huruf dan panjang pendeknya bacaan (mad) dengan tingkat presisi yang sangat tinggi.',
                'image_url' => '/storage/headlines/covers/Ei86GOTslJxZe5qv9Leb5zBqxij1IiE8Qow9F7VM.jpg',
                'category_slug' => 'fitur-baru',
                'tag' => 'Warta'
            ],
            [
                'title' => 'Rahasia Keutamaan Tahsin: Mengapa Memperbaiki Bacaan Al-Qur\'an Adalah Investasi Akhirat Terbaik?',
                'summary' => 'Memahami pentingnya ilmu Tahsin dalam menyempurnakan ibadah harian. Bukan sekadar membaca, namun melafalkan kalam Allah dengan hak-hak huruf yang tepat sesuai sunnah Rasulullah ﷺ.',
                'content' => 'Membaca Al-Qur\'an adalah ibadah yang mulia, namun tahukah Anda bahwa ada tingkatan yang lebih tinggi yaitu membaca dengan Tartil? Ibadah Insight pekan ini mengupas tuntas urgensi Tahsin Al-Qur\'an.',
                'image_url' => '/storage/headlines/covers/vJVVG6mAqOIVyDOLRDnUnaQIZASxRLQl7ti6OYuJ.jpg',
                'category_slug' => 'ibadah-insight',
                'tag' => 'Warta'
            ],
            [
                'title' => 'Festival "Quranic Tech 2026": Ribuan Komunitas Muslim Berkumpul Bahas Masa Depan Dakwah Digital',
                'summary' => 'Gelaran akbar tahunan yang mempertemukan inovator teknologi, asatidz, dan komunitas hijrah untuk mengeksplorasi sinergi antara iman dan teknologi terkini dalam menyebarkan syiar Islam',
                'content' => 'Jakarta kembali menjadi saksi sejarah dengan digelarnya Festival Quranic Tech 2026 di Istora Senayan.',
                'image_url' => '/storage/headlines/covers/AYFCveqjMFXA4OCSFCtnnYhWcVyHOgjbZEvPtyUd.jpg',
                'category_slug' => 'event-ummah',
                'tag' => 'Warta'
            ],
            [
                'title' => 'Kebangkitan Ekonomi Syariah Digital: Transaksi Zakat dan Wakaf Tembus Rekor Baru di Kuartal I 2026',
                'summary' => 'Laporan terbaru menunjukkan pertumbuhan signifikan dalam sektor ekonomi syariah berbasis digital, di mana kemudahan akses melalui aplikasi mobile mendorong partisipasi generasi milenial dan Gen Z dalam berdonasi secara transparan',
                'content' => 'Ekonomi Syariah Indonesia menunjukkan taji luar biasa di awal tahun 2026.',
                'image_url' => '/storage/headlines/covers/9QydwWtfm7KDGXUctW1Eq8Qjj4fuTL1HOGCKKQzH.webp',
                'category_slug' => 'ekonomi-syariah',
                'tag' => 'Warta'
            ],
            [
                'title' => 'Swasembada Pangan 2026: Kolaborasi Petani Milenial dan Teknologi Smart Farming Mulai Membuahkan Hasil',
                'summary' => 'Pemerintah mengumumkan peningkatan produksi padi dan hortikultura nasional sebesar 25% berkat integrasi teknologi IoT di lahan pertanian rakyat yang dikelola oleh generasi muda',
                'content' => 'Kabar membanggakan datang dari sektor pangan nasional.',
                'image_url' => '/storage/headlines/covers/j6gSVf3qgkQaRU7e5tBSNN1JzpBR1Ofli2Vuiuct.webp',
                'category_slug' => 'pertanian',
                'tag' => 'Warta'
            ]
        ];

        foreach ($newsItems as $item) {
            $cat = Category::where('slug', $item['category_slug'])->first();
            Headline::updateOrCreate(
                ['title' => $item['title']],
                [
                    'slug' => Str::slug($item['title']),
                    'summary' => $item['summary'],
                    'content' => $item['content'],
                    'image_url' => $item['image_url'],
                    'is_active' => true,
                    'is_for_landing_page' => true,
                    'is_for_user' => false,
                    'category_id' => $cat ? $cat->id : null,
                    'tag' => $item['tag'],
                ]
            );
        }
    }
}
