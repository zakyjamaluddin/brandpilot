<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BrandPilot AI — Autopilot Sindikasi Konten TikTok ke Semua Sosmed</title>
    <meta name="description" content="Fokus bikin konten di TikTok. Biarkan BrandPilot AI yang merepost otomatis ke Instagram Reels, Facebook, dan YouTube Shorts dengan caption cerdas AI.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif'],
                    },
                    colors: {
                        burgundy: {
                            800: '#5B0E2D',
                            900: '#420B22',
                            950: '#2B0414',
                        },
                        rosegold: {
                            300: '#E7D6C8',
                            400: '#C58F72',
                            500: '#B76E79',
                        },
                        gold: {
                            500: '#D4AF37',
                            600: '#AA820A',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-text {
            background: linear-gradient(135deg, #5B0E2D 0%, #B76E79 50%, #D4AF37 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .btn-burgundy {
            background: linear-gradient(135deg, #5B0E2D 0%, #7C2A41 100%);
            box-shadow: 0 10px 25px -5px rgba(91, 14, 45, 0.35);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-burgundy:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(91, 14, 45, 0.5);
        }
    </style>
</head>
<body class="bg-[#FCFCFC] text-slate-800 font-sans antialiased selection:bg-rosegold-300 selection:text-burgundy-950">

    {{-- NAVBAR --}}
    <nav class="sticky top-0 z-50 backdrop-blur-md bg-white/80 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-burgundy-800 to-rosegold-500 flex items-center justify-center text-white font-bold text-xl shadow-md shadow-burgundy-800/20">
                    B
                </div>
                <span class="text-2xl font-extrabold tracking-tight text-burgundy-950">
                    BrandPilot<span class="text-rosegold-500 font-serif italic text-lg ml-1">AI</span>
                </span>
            </div>

            <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="#fitur" class="hover:text-burgundy-800 transition-colors">Fitur Unggulan</a>
                <a href="#cara-kerja" class="hover:text-burgundy-800 transition-colors">Cara Kerja</a>
                <a href="#harga" class="hover:text-burgundy-800 transition-colors">Paket Harga</a>
                <a href="#faq" class="hover:text-burgundy-800 transition-colors">Tanya Jawab</a>
            </div>

            <div class="flex items-center gap-4">
                <a href="/admin/login" class="text-sm font-bold text-burgundy-800 hover:text-burgundy-900 px-4 py-2">
                    Masuk
                </a>
                <a href="#harga" class="btn-burgundy text-white text-sm font-bold px-6 py-2.5 rounded-full border border-rosegold-400/40">
                    Mulai Autopilot
                </a>
            </div>
        </div>
    </nav>

    {{-- HERO SECTION --}}
    <section class="relative overflow-hidden pt-12 pb-24 lg:pt-20 lg:pb-32">
        {{-- Glow Background Accents --}}
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[350px] bg-gradient-to-tr from-burgundy-800/10 via-rosegold-400/15 to-transparent blur-3xl pointer-events-none rounded-full -z-10"></div>

        <div class="max-w-7xl mx-auto px-6 text-center">
            {{-- Hook Badge --}}
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-50 border border-slate-200 shadow-sm text-xs md:text-sm font-bold text-burgundy-900 mb-8 animate-fade-in">
                <span class="flex h-2 w-2 rounded-full bg-emerald-500"></span>
                Autopilot Cross-Posting TikTok #1 di Indonesia
            </div>

            {{-- Deep Relatable Headline --}}
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold tracking-tight text-slate-900 max-w-5xl mx-auto leading-[1.15]">
                Capek Bikin Konten di TikTok, Tapi <br class="hidden md:block"/>
                <span class="gradient-text font-serif italic font-normal">Nggak Sempat Repost</span> ke IG, FB & YouTube?
            </h1>

            {{-- Relatable Subheadline --}}
            <p class="mt-6 text-lg md:text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
                Sudah habis energi rekam & edit video di TikTok, giliran mau upload ke Instagram Reels, Facebook, dan YouTube Shorts rasanya <b>mager banget</b>. Sekarang cukup posting di TikTok seperti biasa, <b>BrandPilot AI</b> yang otomatis mendistribusikannya ke semua sosmed Anda!
            </p>

            {{-- CTA Action Buttons --}}
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="#harga" class="w-full sm:w-auto btn-burgundy text-white font-bold text-base px-8 py-4 rounded-full border border-rosegold-400/50 flex items-center justify-center gap-2">
                    <span>Mulai Autopilot Sekarang</span>
                    <svg class="w-5 h-5 text-rosegold-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <a href="#cara-kerja" class="w-full sm:w-auto bg-white hover:bg-slate-50 text-slate-700 font-bold text-base px-8 py-4 rounded-full border border-slate-200 shadow-sm transition-all">
                    Lihat Cara Kerja (1 Menit)
                </a>
            </div>

            <p class="mt-4 text-xs text-slate-600 flex items-center justify-center gap-4">
                <span>✓ Tanpa Watermark TikTok</span>
                <span>•</span>
                <span>✓ Dukung Video & Foto Carousel</span>
                <span>•</span>
                <span>✓ Mulai Rp 1.000-an / hari</span>
            </p>

            {{-- HERO IMAGE DUMMY / MOCKUP CONTAINER --}}
            <div class="mt-16 relative max-w-5xl mx-auto">
                <div class="relative rounded-3xl p-3 bg-gradient-to-b from-slate-200 via-slate-100 to-white shadow-2xl shadow-burgundy-950/10 border border-slate-200">
                    <div class="relative overflow-hidden rounded-2xl bg-white aspect-[16/9] border border-slate-100 flex flex-col items-center justify-center text-center p-8 group">
                        
                        {{-- Visual Diagram Dummy Mockup --}}
                        <div class="w-full max-w-3xl flex flex-col md:flex-row items-center justify-between gap-6 py-6">
                            {{-- Sumber TikTok --}}
                            <div class="flex flex-col items-center p-6 bg-slate-50 rounded-2xl border-2 border-slate-900 shadow-sm w-full md:w-60">
                                <span class="text-4xl mb-2">🎵</span>
                                <h4 class="font-extrabold text-slate-900">1x Post di TikTok</h4>
                                <p class="text-xs text-slate-500 mt-1">Video atau Carousel Foto</p>
                                <span class="mt-3 px-3 py-1 bg-black text-white text-[10px] font-bold rounded-full">Satu-satunya Pekerjaan Anda</span>
                            </div>

                            {{-- Mesin AI Tengah --}}
                            <div class="flex flex-col items-center text-center">
                                <div class="w-14 h-14 rounded-full bg-gradient-to-tr from-burgundy-800 to-rosegold-500 text-white flex items-center justify-center text-xl font-bold shadow-lg shadow-burgundy-800/30 animate-pulse">
                                    AI
                                </div>
                                <span class="text-xs font-bold text-burgundy-900 mt-2">BrandPilot Copilot</span>
                                <span class="text-[10px] text-slate-600 max-w-[140px]">Gemini nonton video & bikin caption viral</span>
                            </div>

                            {{-- Hasil Sindikasi 3 Platform --}}
                            <div class="flex flex-col gap-3 w-full md:w-60">
                                <div class="flex items-center gap-3 p-3 bg-rose-50 border border-rose-200 rounded-xl">
                                    <span class="text-xl">📸</span>
                                    <div class="text-left">
                                        <p class="text-xs font-bold text-slate-900">Instagram Reels / Carousel</p>
                                        <p class="text-[10px] text-emerald-600 font-bold">● Auto Published</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                                    <span class="text-xl">📘</span>
                                    <div class="text-left">
                                        <p class="text-xs font-bold text-slate-900">Facebook Page Video/Album</p>
                                        <p class="text-[10px] text-emerald-600 font-bold">● Auto Published</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 p-3 bg-red-50 border border-red-200 rounded-xl">
                                    <span class="text-xl">▶️</span>
                                    <div class="text-left">
                                        <p class="text-xs font-bold text-slate-900">YouTube Shorts</p>
                                        <p class="text-[10px] text-emerald-600 font-bold">● Auto Published</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Panduan Pengisian Gambar Asli Nanti --}}
                        <div class="mt-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 max-w-xl">
                            📌 <b>PANDUAN MOCKUP GAMBAR:</b> Nanti ganti area ini dengan gambar tangkapan layar <i>(screenshot)</i> laptop yang menampilkan Dashboard BrandPilot AI di tengah, lalu ada ilustrasi panah animasi dari logo TikTok menuju ke logo Instagram Reels, Facebook, dan YouTube Shorts.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- PAIN POINT SECTION (RELATABLE PROBLEM) --}}
    <section class="py-20 bg-slate-50 border-y border-slate-200/60">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-xs font-bold tracking-widest uppercase text-rosegold-500 mb-2">Realita Pebisnis & Kreator</h2>
                <h3 class="text-3xl md:text-4xl font-extrabold text-slate-900">Pernahkah Anda Mengalami Hal Ini?</h3>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-2xl border border-slate-200/80 shadow-sm">
                    <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-2xl mb-6">😫</div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Habis Energi di TikTok</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Take video berkali-kali, edit efek, potong klip di TikTok sampai 2 jam. Pas mau upload ke Reels dan YouTube, rasanya sudah lelah dan akhirnya ditinggal tidur.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-2xl border border-slate-200/80 shadow-sm">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl mb-6">📉</div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Kehilangan Ribuan Calon Pembeli</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Audiens Facebook dan YouTube beda dengan TikTok. Konten Anda yang biasa-biasa saja di TikTok bisa jadi sangat viral di Facebook Page dan YouTube Shorts.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-2xl border border-slate-200/80 shadow-sm">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl mb-6">⏳</div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Buntu Mikir Caption Baru</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Gaya caption TikTok beda dengan Instagram dan YouTube. Memikirkan ulang judul dan hashtag untuk 3 platform lain memakan waktu hingga 15 jam per minggu.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- CARA KERJA (3 LANGKAH MUDAH) --}}
    <section id="cara-kerja" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold tracking-widest uppercase text-burgundy-800">Sangat Praktis & Instan</span>
                <h3 class="text-3xl md:text-5xl font-extrabold text-slate-900 mt-2">3 Langkah Menuju Autopilot Sosial Media</h3>
            </div>

            <div class="grid md:grid-cols-3 gap-12 relative">
                {{-- Langkah 1 --}}
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-2xl bg-burgundy-800 text-white font-extrabold text-2xl flex items-center justify-center shadow-lg shadow-burgundy-800/30 mb-6 border border-rosegold-400">
                        1
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-2">Hubungkan Sekali Saja</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Masukkan tautan akun TikTok Anda, lalu hubungkan Facebook Page, Instagram, dan YouTube Anda melalui protokol resmi yang aman.
                    </p>
                </div>

                {{-- Langkah 2 --}}
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-burgundy-800 to-rosegold-500 text-white font-extrabold text-2xl flex items-center justify-center shadow-lg shadow-rosegold-500/30 mb-6 border border-rosegold-400">
                        2
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-2">Bikin Konten di TikTok</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Berkreasilah seperti biasa! Manfaatkan filter, musik viral, dan editor canggih bawaan TikTok, lalu klik Publish di TikTok Anda.
                    </p>
                </div>

                {{-- Langkah 3 --}}
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-600 text-white font-extrabold text-2xl flex items-center justify-center shadow-lg shadow-emerald-600/30 mb-6">
                        3
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-2">Duduk Manis & Beres!</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        BrandPilot AI mengunduh video tanpa watermark, membiarkan AI Gemini memahami videonya, lalu mempublikasikan ke IG, FB, dan YT secara rapi.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- FITUR UNGGULAN --}}
    <section id="fitur" class="py-20 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold tracking-widest uppercase text-rosegold-500">Kecerdasan Buatan Generasi Baru</span>
                <h3 class="text-3xl md:text-4xl font-extrabold text-slate-900 mt-2">Fitur Canggih yang Bekerja Untuk Anda</h3>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white p-7 rounded-2xl border border-slate-200 shadow-sm">
                    <span class="text-3xl">🤖</span>
                    <h4 class="text-lg font-bold text-slate-900 mt-4 mb-2">Multimodal AI Video Understanding</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Gemini 3.1 Flash Lite benar-benar "menonton" video Anda untuk meracik judul click-worthy dan deskripsi ber-hook tinggi yang sesuai dengan isi video.
                    </p>
                </div>

                <div class="bg-white p-7 rounded-2xl border border-slate-200 shadow-sm">
                    <span class="text-3xl">🎞️</span>
                    <h4 class="text-lg font-bold text-slate-900 mt-4 mb-2">Dukungan TikTok Carousel Foto</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Bukan cuma video! TikTok Photo Mode (Carousel) otomatis diunggah menjadi Instagram Carousel yang bisa di-swipe dan Facebook Multi-Photo Album.
                    </p>
                </div>

                <div class="bg-white p-7 rounded-2xl border border-slate-200 shadow-sm">
                    <span class="text-3xl">⚡</span>
                    <h4 class="text-lg font-bold text-slate-900 mt-4 mb-2">Bebas Watermark TikTok</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Video diunduh dengan kualitas original HD jernih tanpa logo atau watermark TikTok, sehingga algoritma Instagram Reels & YouTube tidak akan membatasi jangkauan Anda.
                    </p>
                </div>

                <div class="bg-white p-7 rounded-2xl border border-slate-200 shadow-sm">
                    <span class="text-3xl">📅</span>
                    <h4 class="text-lg font-bold text-slate-900 mt-4 mb-2">Jadwal Dinamis atau Jam Tertentu</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Pilih apakah video langsung diposting segera setelah Anda upload di TikTok, atau diantrekan secara teratur mengisi jam-jam ramai audiens Anda.
                    </p>
                </div>

                <div class="bg-white p-7 rounded-2xl border border-slate-200 shadow-sm">
                    <span class="text-3xl">📈</span>
                    <h4 class="text-lg font-bold text-slate-900 mt-4 mb-2">Statistik Terpadu</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Pantau jumlah views dan performa komentar seluruh media sosial Anda dalam satu layar dashboard mewah tanpa harus buka aplikasi satu per satu.
                    </p>
                </div>

                <div class="bg-white p-7 rounded-2xl border border-slate-200 shadow-sm">
                    <span class="text-3xl">🔒</span>
                    <h4 class="text-lg font-bold text-slate-900 mt-4 mb-2">100% Aman Protokol Resmi</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Menggunakan integrasi API resmi Meta dan Google via Composio. Akun media sosial Anda aman, tanpa perlu membagikan password pribadi Anda.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- PRICING SECTION --}}
    <section id="harga" class="py-24 bg-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold tracking-widest uppercase text-burgundy-800">Investasi Paling Masuk Akal</span>
                <h3 class="text-3xl md:text-5xl font-extrabold text-slate-900 mt-2">Pilih Paket Autopilot Anda</h3>
                <p class="text-base text-slate-600 mt-4">
                    Biaya langganan jauh lebih murah daripada menggaji 1 orang admin konten bulanan.
                </p>
            </div>

            <div class="grid lg:grid-cols-3 gap-8 items-stretch max-w-6xl mx-auto">
                
                {{-- PAKET 1: 1 BULAN --}}
                <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-lg flex flex-col justify-between relative hover:border-slate-300 transition-all">
                    <div>
                        <div class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2">Starter Pilot</div>
                        <h4 class="text-2xl font-extrabold text-slate-900 mb-4">1 Bulan</h4>
                        <div class="flex items-baseline gap-2 mb-6">
                            <span class="text-4xl md:text-5xl font-extrabold text-slate-900">Rp 39.000</span>
                            <span class="text-sm text-slate-600 font-semibold">/ bulan</span>
                        </div>
                        <p class="text-xs text-slate-600 mb-6">Cocok untuk kreator pemula yang ingin membuktikan kehebatan autopilot konten.</p>

                        <div class="border-t border-slate-100 pt-6">
                            <ul class="space-y-3 text-sm text-slate-600">
                                <li class="flex items-center gap-3">
                                    <span class="text-emerald-500 font-bold">✓</span> Auto-repost TikTok ke FB, IG, YT
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="text-emerald-500 font-bold">✓</span> Dukungan Video & Carousel Foto
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="text-emerald-500 font-bold">✓</span> Gemini 3.1 Flash Lite Auto-Caption
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="text-emerald-500 font-bold">✓</span> Tanpa Watermark TikTok
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="text-emerald-500 font-bold">✓</span> Dashboard Agregasi Statistik
                                </li>
                            </ul>
                        </div>
                    </div>

                    <a href="https://lynk.id/zakyjamal/731woweop93d/" target="_blank" class="mt-8 block w-full py-3.5 px-6 rounded-full font-bold text-center text-sm text-burgundy-900 bg-slate-100 hover:bg-slate-200 transition-colors">
                        Pilih 1 Bulan
                    </a>
                </div>

                {{-- PAKET 2: 6 BULAN (HEMAT 19%) --}}
                <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-lg flex flex-col justify-between relative hover:border-slate-300 transition-all">
                    <div class="absolute -top-3.5 right-8 bg-rosegold-500 text-white text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        Hemat 19%
                    </div>

                    <div>
                        <div class="text-sm font-bold text-rosegold-500 uppercase tracking-wider mb-2">Growth Pilot</div>
                        <h4 class="text-2xl font-extrabold text-slate-900 mb-4">6 Bulan</h4>
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-4xl md:text-5xl font-extrabold text-slate-900">Rp 189.000</span>
                        </div>
                        <p class="text-xs text-slate-600 line-through mb-4">Normal: Rp 234.000</p>
                        <p class="text-xs text-slate-600 mb-6">Pilihan tepat untuk membangun branding bisnis yang konsisten selama setengah tahun.</p>

                        <div class="border-t border-slate-100 pt-6">
                            <ul class="space-y-3 text-sm text-slate-600">
                                <li class="flex items-center gap-3">
                                    <span class="text-emerald-500 font-bold">✓</span> <b>Semua Fitur Paket 1 Bulan</b>
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="text-emerald-500 font-bold">✓</span> Masa Aktif 6 Bulan Penuh
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="text-emerald-500 font-bold">✓</span> Prioritas Pemrosesan Antrean
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="text-emerald-500 font-bold">✓</span> Update Fitur Baru Lebih Awal
                                </li>
                            </ul>
                        </div>
                    </div>

                    <a href="http://lynk.id/zakyjamal/xj9nkn994yeo/" target="_blank" class="mt-8 block w-full py-3.5 px-6 rounded-full font-bold text-center text-sm text-burgundy-900 bg-slate-100 hover:bg-slate-200 transition-colors">
                        Pilih 6 Bulan
                    </a>
                </div>

                {{-- PAKET 3: 1 TAHUN (BEST VALUE / PALING DIREKOMENDASIKAN) --}}
                <div class="bg-gradient-to-b from-[#FCF7F8] via-white to-white rounded-3xl p-8 border-2 border-burgundy-800 shadow-2xl shadow-burgundy-800/15 flex flex-col justify-between relative transform lg:-translate-y-2">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-gradient-to-r from-burgundy-800 to-rosegold-500 text-white text-[11px] font-extrabold px-4 py-1.5 rounded-full uppercase tracking-widest shadow-md border border-rosegold-300/40">
                        ⭐ PALING DIREKOMENDASIKAN
                    </div>

                    <div>
                        <div class="text-sm font-bold text-burgundy-800 uppercase tracking-wider mb-2 flex items-center justify-between">
                            <span>Ultimate Pilot</span>
                            <span class="text-emerald-600 text-xs font-bold">Cuma Rp 800-an / hari</span>
                        </div>
                        <h4 class="text-2xl font-extrabold text-slate-900 mb-4">1 Tahun Penuh</h4>
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-4xl md:text-5xl font-extrabold text-burgundy-950">Rp 297.000</span>
                        </div>
                        <p class="text-xs text-slate-600 line-through mb-4">Normal: Rp 468.000 (Hemat 36%)</p>
                        <p class="text-xs text-slate-600 mb-6">Investasi terbaik untuk bisnis autopilot Anda sepanjang tahun tanpa pusing.</p>

                        <div class="border-t border-slate-200/80 pt-6">
                            <ul class="space-y-3.5 text-sm text-slate-700">
                                <li class="flex items-center gap-3">
                                    <span class="text-emerald-600 font-bold text-base">✓</span> <b>Semua Fitur Pro Lengkap</b>
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="text-emerald-600 font-bold text-base">✓</span> Kuota Sindikasi Video & Foto Maksimal
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="text-emerald-600 font-bold text-base">✓</span> Multimodal Gemini Video & Carousel
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="text-emerald-600 font-bold text-base">✓</span> Dukungan Prioritas WhatsApp
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="text-emerald-600 font-bold text-base">✓</span> Bebas Kenaikan Harga Langganan
                                </li>
                            </ul>
                        </div>
                    </div>

                   <a href="http://lynk.id/zakyjamal/jokgvgpvlq9x/" target="_blank" class="mt-8 block w-full py-4 px-6 rounded-full font-bold text-center text-sm text-white btn-burgundy border border-rosegold-400/50 shadow-lg shadow-burgundy-800/30">
                        ⚡ Ambil Penawaran 1 Tahun
                    </a>
                </div>

            </div>
        </div>
    </section>

    {{-- FAQ SECTION --}}
    <section id="faq" class="py-20 bg-slate-50 border-t border-slate-200/60">
        <div class="max-w-4xl mx-auto px-6">
            <div class="text-center mb-16">
                <span class="text-xs font-bold tracking-widest uppercase text-rosegold-500">Pertanyaan Populer</span>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-2">Kerap Ditanyakan (FAQ)</h3>
            </div>

            <div class="space-y-4">
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h4 class="font-bold text-slate-900 text-base mb-2">Apakah akun media sosial saya aman?</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Sangat aman. BrandPilot AI tidak pernah meminta ataupun menyimpan password Facebook, Instagram, maupun YouTube Anda. Koneksi dilakukan menggunakan protokol OAuth resmi dari Meta dan Google.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h4 class="font-bold text-slate-900 text-base mb-2">Apakah video yang di-repost ada watermark TikTok-nya?</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Sama sekali tidak ada. Mesin pengunduh kami mengambil berkas asli berkualitas tinggi tanpa watermark TikTok, sehingga postingan Anda tetap dinilai organik dan direkomendasikan oleh algoritma Instagram & YouTube.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h4 class="font-bold text-slate-900 text-base mb-2">Bagaimana cara AI Gemini membuat caption?</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        AI Gemini menggunakan teknologi multimodal yang benar-benar "menonton" video atau "melihat" slide foto carousel Anda. AI kemudian meracik judul yang click-worthy dan deskripsi lengkap beserta hashtag yang relevan secara otomatis.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h4 class="font-bold text-slate-900 text-base mb-2">Berapa lama jeda waktu dari TikTok sampai tayang di sosmed lain?</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Sistem kami memeriksa akun TikTok Anda secara berkala setiap 30 menit untuk menghemat kuota dan menjaga keamanan akun. Begitu terdeteksi, konten langsung diproses dan dipublikasikan ke FB, IG, dan YT.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-white border-t border-slate-200 py-12">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-burgundy-800 text-white font-bold flex items-center justify-center text-sm">
                    B
                </div>
                <span class="text-lg font-bold text-burgundy-950">BrandPilot AI</span>
            </div>

            <p class="text-xs text-slate-600 text-center md:text-right">
                &copy; {{ date('Y') }} BrandPilot AI. Semua Hak Cipta Dilindungi.<br/>
                Fokus bikin konten di TikTok, biarkan kami yang mengurus sisanya.
            </p>
        </div>
    </footer>

</body>
</html>