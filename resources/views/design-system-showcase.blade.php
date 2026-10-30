<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BrandPilot AI — Design System Showcase</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,400;1,600;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        burgundy: {
                            50: '#FBF2F4',
                            100: '#F6E4E8',
                            200: '#ECCDD4',
                            300: '#DDA6B4',
                            400: '#CA768D',
                            500: '#B24C68',
                            600: '#963650',
                            700: '#7C2A41',
                            800: '#5B0E2D',
                            900: '#420B22',
                            950: '#2B0414',
                        },
                        rosegold: {
                            50: '#FAF6F3',
                            100: '#F4ECE5',
                            200: '#E7D6C8',
                            300: '#D5B9A4',
                            400: '#C0987E',
                            500: '#B76E79',
                            600: '#A06E54',
                            700: '#855844',
                            800: '#6D4839',
                            900: '#593C31',
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
            border: 1px solid rgba(192, 152, 126, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-burgundy:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(91, 14, 45, 0.5);
        }
        .btn-rosegold {
            background: linear-gradient(135deg, #b76e79 0%, #c0987e 100%);
            box-shadow: 0 10px 25px -5px rgba(183, 110, 121, 0.3);
            color: #ffffff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-brandpilot {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(183, 110, 121, 0.18);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
        }
        .dark .card-brandpilot {
            background: rgba(22, 8, 15, 0.72);
            border: 1px solid rgba(183, 110, 121, 0.28);
            box-shadow: 0 12px 30px -5px rgba(0, 0, 0, 0.35);
        }
    </style>
</head>
<body class="bg-[#FCFCFC] dark:bg-[#0C0307] text-slate-800 dark:text-slate-100 font-sans antialiased transition-colors duration-300 selection:bg-rosegold-200 selection:text-burgundy-950">

    {{-- Toast Notification --}}
    <div id="toast" class="fixed bottom-6 right-6 z-50 transform translate-y-16 opacity-0 transition-all duration-300 pointer-events-none">
        <div class="flex items-center gap-2 px-4 py-2.5 rounded-full bg-burgundy-950 text-white text-xs font-semibold shadow-2xl border border-rosegold-400/30">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span id="toast-text">Tersalin ke clipboard!</span>
        </div>
    </div>

    {{-- Sticky Navbar --}}
    <header class="sticky top-0 z-50 backdrop-blur-md bg-white/80 dark:bg-[#0F050A]/80 border-b border-slate-100 dark:border-rosegold-500/20">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-burgundy-800 to-rosegold-500 flex items-center justify-center text-white font-bold text-xl shadow-md shadow-burgundy-800/25">
                    B
                </div>
                <div>
                    <span class="text-2xl font-extrabold tracking-tight text-burgundy-950 dark:text-white">
                        BrandPilot<span class="text-rosegold-500 font-serif italic text-lg ml-1">AI</span>
                    </span>
                    <span class="ml-2 text-xs font-semibold px-2.5 py-0.5 rounded-full bg-burgundy-50 dark:bg-burgundy-900/50 text-burgundy-800 dark:text-rosegold-300 border border-burgundy-200/50">
                        Design System v1.0
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button onclick="toggleDarkMode()" class="px-3.5 py-1.5 rounded-full border border-slate-200 dark:border-rosegold-500/30 text-xs font-bold bg-white dark:bg-burgundy-950/80 hover:bg-slate-50 dark:hover:bg-burgundy-900 transition flex items-center gap-1.5">
                    <span id="theme-icon">🌓</span> <span id="theme-label">Mode Tema</span>
                </button>
                <a href="/" class="text-xs font-bold text-burgundy-800 dark:text-rosegold-300 hover:underline">
                    &larr; Beranda BrandPilot
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-12 space-y-20">

        {{-- Intro Hero --}}
        <section class="text-center max-w-4xl mx-auto space-y-5 relative">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[250px] bg-gradient-to-tr from-burgundy-800/15 via-rosegold-400/20 to-transparent blur-3xl pointer-events-none rounded-full -z-10"></div>
            
            <x-ui.badge variant="burgundy" :pulse="true">
                Living Design System & Component Library
            </x-ui.badge>

            <x-ui.heading level="h1" accent="Executive Luxury Tech" align="center">
                Sistem Desain
            </x-ui.heading>

            <p class="text-slate-600 dark:text-slate-300 text-lg md:text-xl leading-relaxed max-w-2xl mx-auto">
                Spesifikasi resmi palet warna, tipografi, efek elevasi, dan komponen antarmuka yang konsisten serta <em>reusable</em> untuk ekosistem BrandPilot AI.
            </p>

            <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                <a href="#colors" class="text-xs font-semibold px-4 py-2 rounded-full bg-slate-100 dark:bg-burgundy-950/60 hover:bg-slate-200 text-slate-700 dark:text-rosegold-200 transition">🎨 Palet Warna</a>
                <a href="#typography" class="text-xs font-semibold px-4 py-2 rounded-full bg-slate-100 dark:bg-burgundy-950/60 hover:bg-slate-200 text-slate-700 dark:text-rosegold-200 transition">✍️ Tipografi</a>
                <a href="#buttons" class="text-xs font-semibold px-4 py-2 rounded-full bg-slate-100 dark:bg-burgundy-950/60 hover:bg-slate-200 text-slate-700 dark:text-rosegold-200 transition">🔘 Tombol & Badge</a>
                <a href="#cards" class="text-xs font-semibold px-4 py-2 rounded-full bg-slate-100 dark:bg-burgundy-950/60 hover:bg-slate-200 text-slate-700 dark:text-rosegold-200 transition">🃏 Card Presets</a>
                <a href="#code-tokens" class="text-xs font-semibold px-4 py-2 rounded-full bg-slate-100 dark:bg-burgundy-950/60 hover:bg-slate-200 text-slate-700 dark:text-rosegold-200 transition">💻 Kode & Konfigurasi</a>
                <a href="#motion-system" class="text-xs font-semibold px-4 py-2 rounded-full bg-gradient-to-r from-burgundy-800 to-rosegold-500 text-white shadow-sm transition">🎬 Motion Video Engine</a>
            </div>
        </section>

        {{-- 1. COLOR PALETTE --}}
        <section id="colors" class="space-y-8 scroll-mt-24">
            <div class="border-b border-slate-200 dark:border-rosegold-500/20 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-2xl font-extrabold text-burgundy-950 dark:text-white">1. Palet Warna Resmi</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Klik warna mana pun untuk menyalin kode HEX langsung ke clipboard.</p>
                </div>
                <span class="text-xs font-medium text-slate-400">Format: HEX & Tailwind Class</span>
            </div>

            {{-- Burgundy Scale --}}
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-burgundy-900 dark:text-rosegold-300 uppercase tracking-wider">
                        Primary Scale &mdash; Burgundy (Deep Wine)
                    </h3>
                    <span class="text-xs text-rosegold-500 font-semibold">Brand Primary Core</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-11 gap-2.5 text-center text-xs">
                    <div onclick="copyHex('#FBF2F4', 'burgundy-50')" class="group cursor-pointer p-3 rounded-xl bg-burgundy-50 border border-burgundy-200 text-burgundy-900 font-bold hover:scale-105 transition-transform shadow-sm">
                        50<br><span class="text-[10px] font-normal group-hover:underline">#FBF2F4</span>
                    </div>
                    <div onclick="copyHex('#F6E4E8', 'burgundy-100')" class="group cursor-pointer p-3 rounded-xl bg-burgundy-100 text-burgundy-900 font-bold hover:scale-105 transition-transform shadow-sm">
                        100<br><span class="text-[10px] font-normal group-hover:underline">#F6E4E8</span>
                    </div>
                    <div onclick="copyHex('#ECCDD4', 'burgundy-200')" class="group cursor-pointer p-3 rounded-xl bg-burgundy-200 text-burgundy-900 font-bold hover:scale-105 transition-transform shadow-sm">
                        200<br><span class="text-[10px] font-normal group-hover:underline">#ECCDD4</span>
                    </div>
                    <div onclick="copyHex('#DDA6B4', 'burgundy-300')" class="group cursor-pointer p-3 rounded-xl bg-burgundy-300 text-burgundy-950 font-bold hover:scale-105 transition-transform shadow-sm">
                        300<br><span class="text-[10px] font-normal group-hover:underline">#DDA6B4</span>
                    </div>
                    <div onclick="copyHex('#CA768D', 'burgundy-400')" class="group cursor-pointer p-3 rounded-xl bg-burgundy-400 text-white font-bold hover:scale-105 transition-transform shadow-sm">
                        400<br><span class="text-[10px] font-normal group-hover:underline">#CA768D</span>
                    </div>
                    <div onclick="copyHex('#B24C68', 'burgundy-500')" class="group cursor-pointer p-3 rounded-xl bg-burgundy-500 text-white font-bold hover:scale-105 transition-transform shadow-sm">
                        500<br><span class="text-[10px] font-normal group-hover:underline">#B24C68</span>
                    </div>
                    <div onclick="copyHex('#963650', 'burgundy-600')" class="group cursor-pointer p-3 rounded-xl bg-burgundy-600 text-white font-bold hover:scale-105 transition-transform shadow-sm">
                        600<br><span class="text-[10px] font-normal group-hover:underline">#963650</span>
                    </div>
                    <div onclick="copyHex('#7C2A41', 'burgundy-700')" class="group cursor-pointer p-3 rounded-xl bg-burgundy-700 text-white font-bold hover:scale-105 transition-transform shadow-sm">
                        700<br><span class="text-[10px] font-normal group-hover:underline">#7C2A41</span>
                    </div>
                    <div onclick="copyHex('#5B0E2D', 'burgundy-800')" class="group cursor-pointer p-3 rounded-xl bg-burgundy-800 text-white font-bold ring-2 ring-gold-500 hover:scale-105 transition-transform shadow-md">
                        800 ★<br><span class="text-[10px] font-normal group-hover:underline">#5B0E2D</span>
                    </div>
                    <div onclick="copyHex('#420B22', 'burgundy-900')" class="group cursor-pointer p-3 rounded-xl bg-burgundy-900 text-white font-bold hover:scale-105 transition-transform shadow-sm">
                        900<br><span class="text-[10px] font-normal group-hover:underline">#420B22</span>
                    </div>
                    <div onclick="copyHex('#2B0414', 'burgundy-950')" class="group cursor-pointer p-3 rounded-xl bg-burgundy-950 text-white font-bold hover:scale-105 transition-transform shadow-sm">
                        950<br><span class="text-[10px] font-normal group-hover:underline">#2B0414</span>
                    </div>
                </div>
            </div>

            {{-- Rose Gold Scale --}}
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-rosegold-600 dark:text-rosegold-300 uppercase tracking-wider">
                        Secondary Scale &mdash; Rose Gold & Antique Copper
                    </h3>
                    <span class="text-xs text-rosegold-500 font-semibold">Luxury Accent Core</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-10 gap-2.5 text-center text-xs">
                    <div onclick="copyHex('#FAF6F3', 'rosegold-50')" class="group cursor-pointer p-3 rounded-xl bg-rosegold-50 border border-rosegold-200 text-rosegold-900 font-bold hover:scale-105 transition-transform shadow-sm">
                        50<br><span class="text-[10px] font-normal group-hover:underline">#FAF6F3</span>
                    </div>
                    <div onclick="copyHex('#F4ECE5', 'rosegold-100')" class="group cursor-pointer p-3 rounded-xl bg-rosegold-100 text-rosegold-900 font-bold hover:scale-105 transition-transform shadow-sm">
                        100<br><span class="text-[10px] font-normal group-hover:underline">#F4ECE5</span>
                    </div>
                    <div onclick="copyHex('#E7D6C8', 'rosegold-200')" class="group cursor-pointer p-3 rounded-xl bg-rosegold-200 text-rosegold-900 font-bold hover:scale-105 transition-transform shadow-sm">
                        200<br><span class="text-[10px] font-normal group-hover:underline">#E7D6C8</span>
                    </div>
                    <div onclick="copyHex('#D5B9A4', 'rosegold-300')" class="group cursor-pointer p-3 rounded-xl bg-rosegold-300 text-rosegold-950 font-bold hover:scale-105 transition-transform shadow-sm">
                        300<br><span class="text-[10px] font-normal group-hover:underline">#D5B9A4</span>
                    </div>
                    <div onclick="copyHex('#C0987E', 'rosegold-400')" class="group cursor-pointer p-3 rounded-xl bg-rosegold-400 text-white font-bold hover:scale-105 transition-transform shadow-sm">
                        400<br><span class="text-[10px] font-normal group-hover:underline">#C0987E</span>
                    </div>
                    <div onclick="copyHex('#B76E79', 'rosegold-500')" class="group cursor-pointer p-3 rounded-xl bg-rosegold-500 text-white font-bold ring-2 ring-gold-500 hover:scale-105 transition-transform shadow-md">
                        500 ★<br><span class="text-[10px] font-normal group-hover:underline">#B76E79</span>
                    </div>
                    <div onclick="copyHex('#A06E54', 'rosegold-600')" class="group cursor-pointer p-3 rounded-xl bg-rosegold-600 text-white font-bold hover:scale-105 transition-transform shadow-sm">
                        600<br><span class="text-[10px] font-normal group-hover:underline">#A06E54</span>
                    </div>
                    <div onclick="copyHex('#855844', 'rosegold-700')" class="group cursor-pointer p-3 rounded-xl bg-rosegold-700 text-white font-bold hover:scale-105 transition-transform shadow-sm">
                        700<br><span class="text-[10px] font-normal group-hover:underline">#855844</span>
                    </div>
                    <div onclick="copyHex('#6D4839', 'rosegold-800')" class="group cursor-pointer p-3 rounded-xl bg-rosegold-800 text-white font-bold hover:scale-105 transition-transform shadow-sm">
                        800<br><span class="text-[10px] font-normal group-hover:underline">#6D4839</span>
                    </div>
                    <div onclick="copyHex('#593C31', 'rosegold-900')" class="group cursor-pointer p-3 rounded-xl bg-rosegold-900 text-white font-bold hover:scale-105 transition-transform shadow-sm">
                        900<br><span class="text-[10px] font-normal group-hover:underline">#593C31</span>
                    </div>
                </div>
            </div>

            {{-- Gold & Accent Scale --}}
            <div class="space-y-3">
                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                    Metallic Gold & Functional Status
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-3 text-center text-xs">
                    <div onclick="copyHex('#D4AF37', 'gold-500')" class="group cursor-pointer p-4 rounded-xl bg-[#D4AF37] text-white font-bold hover:scale-105 transition shadow-sm">
                        Gold 500<br><span class="text-[10px] font-normal">#D4AF37 (Metallic Accent)</span>
                    </div>
                    <div onclick="copyHex('#AA820A', 'gold-600')" class="group cursor-pointer p-4 rounded-xl bg-[#AA820A] text-white font-bold hover:scale-105 transition shadow-sm">
                        Gold 600<br><span class="text-[10px] font-normal">#AA820A (Deep Gold)</span>
                    </div>
                    <div onclick="copyHex('#10B981', 'emerald-500')" class="group cursor-pointer p-4 rounded-xl bg-emerald-500 text-white font-bold hover:scale-105 transition shadow-sm">
                        Emerald 500<br><span class="text-[10px] font-normal">#10B981 (Success / Active)</span>
                    </div>
                    <div onclick="copyHex('#FCFCFC', 'canvas-light')" class="group cursor-pointer p-4 rounded-xl bg-[#FCFCFC] border border-slate-300 text-slate-800 font-bold hover:scale-105 transition shadow-sm">
                        Light Canvas<br><span class="text-[10px] font-normal">#FCFCFC (Background)</span>
                    </div>
                    <div onclick="copyHex('#0C0307', 'canvas-dark')" class="group cursor-pointer p-4 rounded-xl bg-[#0C0307] border border-burgundy-900 text-rosegold-200 font-bold hover:scale-105 transition shadow-sm">
                        Obsidian Wine<br><span class="text-[10px] font-normal">#0C0307 (Dark Theme)</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- 2. TYPOGRAPHY --}}
        <section id="typography" class="space-y-8 scroll-mt-24">
            <div class="border-b border-slate-200 dark:border-rosegold-500/20 pb-4">
                <h2 class="text-2xl font-extrabold text-burgundy-950 dark:text-white">2. Sistem Tipografi</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Harmonisasi kontras tinggi antara Plus Jakarta Sans (Modern Clean UI) dan Playfair Display Italic (Luxury Editorial Accent).</p>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <x-ui.card>
                    <span class="text-xs font-bold text-rosegold-500 uppercase tracking-widest">Primary UI Font</span>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1">Plus Jakarta Sans</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-1">font-family: 'Plus Jakarta Sans', sans-serif;</p>
                    <p class="text-sm text-slate-600 dark:text-slate-300 mt-3 leading-relaxed">
                        Dipakai untuk 90% hierarki visual antarmuka: navigasi, isi teks, tombol, form input, dan card content.
                    </p>
                    <div class="mt-5 space-y-2 font-sans border-t border-slate-100 dark:border-rosegold-500/20 pt-4 text-sm">
                        <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                            <span>Regular 400</span>
                            <span class="font-normal text-slate-900 dark:text-white">Desain sistematis untuk skala produk</span>
                        </div>
                        <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                            <span>Medium 500</span>
                            <span class="font-medium text-slate-900 dark:text-white">Desain sistematis untuk skala produk</span>
                        </div>
                        <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                            <span>SemiBold 600</span>
                            <span class="font-semibold text-slate-900 dark:text-white">Desain sistematis untuk skala produk</span>
                        </div>
                        <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                            <span>Bold 700</span>
                            <span class="font-bold text-slate-900 dark:text-white">Desain sistematis untuk skala produk</span>
                        </div>
                        <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                            <span>ExtraBold 800</span>
                            <span class="font-extrabold text-slate-900 dark:text-white">Desain sistematis untuk skala produk</span>
                        </div>
                    </div>
                </x-ui.card>

                <x-ui.card>
                    <span class="text-xs font-bold text-rosegold-500 uppercase tracking-widest">Editorial Accent Font</span>
                    <h3 class="text-3xl font-serif italic text-burgundy-900 dark:text-rosegold-300 mt-1">Playfair Display</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-1">font-family: 'Playfair Display', Georgia, serif;</p>
                    <p class="text-sm text-slate-600 dark:text-slate-300 mt-3 leading-relaxed">
                        Digunakan secara selektif (1-3 kata kunci) di dalam headline untuk menghasilkan kesan editorial mewah dan magnet perhatian.
                    </p>
                    <div class="mt-5 p-5 rounded-2xl bg-gradient-to-br from-burgundy-50 via-white to-rosegold-50/30 dark:from-burgundy-950/60 dark:to-burgundy-900/20 border border-rosegold-400/20">
                        <span class="text-[11px] font-bold text-burgundy-800 dark:text-rosegold-400 uppercase tracking-widest">Contoh Implementasi Headline:</span>
                        <p class="text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white mt-2 leading-snug">
                            Capek Bikin Konten, Tapi <br/>
                            <span class="gradient-text font-serif italic font-normal">Nggak Sempat Repost</span> ke Semua Sosmed?
                        </p>
                    </div>
                </x-ui.card>
            </div>

            {{-- Interactive Font Specimen Tester --}}
            <x-ui.card variant="glass">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <span class="text-xs font-bold text-burgundy-900 dark:text-rosegold-300 uppercase tracking-wider">
                        ⚡ Interactive Font Tester
                    </span>
                    <span class="text-xs text-slate-500">Ketik di bawah untuk menguji visual font:</span>
                </div>
                <input
                    type="text"
                    id="specimen-input"
                    value="BrandPilot AI mendistribusikan konten Anda secara otomatis"
                    oninput="document.getElementById('specimen-output').innerText = this.value"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-rosegold-500/30 bg-white dark:bg-burgundy-950/50 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800 mb-4"
                />
                <div class="p-6 rounded-2xl bg-slate-50 dark:bg-burgundy-950/30 border border-slate-200/60 dark:border-rosegold-500/20">
                    <p id="specimen-output" class="text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white">
                        BrandPilot AI mendistribusikan konten Anda secara otomatis
                    </p>
                </div>
            </x-ui.card>
        </section>

        {{-- 3. BUTTONS & ACTIONS --}}
        <section id="buttons" class="space-y-8 scroll-mt-24">
            <div class="border-b border-slate-200 dark:border-rosegold-500/20 pb-4">
                <h2 class="text-2xl font-extrabold text-burgundy-950 dark:text-white">3. Tombol & Actions (&lt;x-ui.button&gt;)</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Varian tombol konsisten dengan elevasi lembut, shadow berwarna, dan interaksi mikro.</p>
            </div>

            <div class="p-8 rounded-3xl bg-slate-50 dark:bg-burgundy-950/20 border border-slate-200/80 dark:border-rosegold-500/20 space-y-6">
                <div>
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-3">Varian Tombol Standar (Medium):</span>
                    <div class="flex flex-wrap gap-4 items-center">
                        <x-ui.button variant="primary" size="md">
                            Primary Burgundy
                        </x-ui.button>

                        <x-ui.button variant="rosegold" size="md">
                            Rose Gold Accent
                        </x-ui.button>

                        <x-ui.button variant="secondary" size="md">
                            Secondary White
                        </x-ui.button>

                        <x-ui.button variant="outline" size="md">
                            Outline Burgundy
                        </x-ui.button>

                        <x-ui.button variant="ghost" size="md">
                            Ghost Link &rarr;
                        </x-ui.button>
                    </div>
                </div>

                <div class="border-t border-slate-200/60 dark:border-rosegold-500/20 pt-6">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-3">Skala Ukuran (Size sm, md, lg):</span>
                    <div class="flex flex-wrap gap-4 items-center">
                        <x-ui.button variant="primary" size="sm">
                            Small (sm)
                        </x-ui.button>

                        <x-ui.button variant="primary" size="md">
                            Medium (md)
                        </x-ui.button>

                        <x-ui.button variant="primary" size="lg">
                            Large Hero (lg)
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </section>

        {{-- 4. STATUS BADGES --}}
        <section class="space-y-8 scroll-mt-24">
            <div class="border-b border-slate-200 dark:border-rosegold-500/20 pb-4">
                <h2 class="text-2xl font-extrabold text-burgundy-950 dark:text-white">4. Status Badges & Pills (&lt;x-ui.badge&gt;)</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Pill badges dengan live radar ping indicator untuk status sistem dan filter kategori.</p>
            </div>

            <div class="p-8 rounded-3xl bg-slate-50 dark:bg-burgundy-950/20 border border-slate-200/80 dark:border-rosegold-500/20 flex flex-wrap gap-4 items-center">
                <x-ui.badge variant="burgundy" :pulse="true">
                    Live TikTok Sync
                </x-ui.badge>
                <x-ui.badge variant="rosegold" :pulse="true">
                    AI Auto-Caption Ready
                </x-ui.badge>
                <x-ui.badge variant="emerald" :pulse="true">
                    100% Autopilot Active
                </x-ui.badge>
                <x-ui.badge variant="gold">
                    ★ Paket Rekomendasi
                </x-ui.badge>
                <x-ui.badge variant="neutral">
                    v1.0.0 Production
                </x-ui.badge>
            </div>
        </section>

        {{-- 5. CARDS & SECTIONS --}}
        <section id="cards" class="space-y-8 scroll-mt-24">
            <div class="border-b border-slate-200 dark:border-rosegold-500/20 pb-4">
                <h2 class="text-2xl font-extrabold text-burgundy-950 dark:text-white">5. Card Presets (&lt;x-ui.card&gt;)</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Pola kartu standar untuk fitur, paket harga unggulan, dan hero callout.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6 items-stretch">
                {{-- Card 1: Glass Standard --}}
                <x-ui.card variant="glass">
                    <div class="w-10 h-10 rounded-xl bg-burgundy-50 dark:bg-burgundy-900/50 text-burgundy-800 dark:text-rosegold-300 flex items-center justify-center font-bold text-xl mb-4">
                        💎
                    </div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Starter Pilot</span>
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mt-1 mb-2">Glassmorphism Card</h3>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-6">
                        Border rose gold lembut dengan backdrop blur untuk elevasi elegan dan modern.
                    </p>
                    <div class="text-2xl font-extrabold text-slate-900 dark:text-white mb-4">
                        Rp 39.000 <span class="text-xs font-normal text-slate-500">/ bulan</span>
                    </div>
                    <x-ui.button variant="secondary" size="sm" class="w-full">
                        Pilih Paket
                    </x-ui.button>
                </x-ui.card>

                {{-- Card 2: Featured Recommended --}}
                <x-ui.card variant="featured" class="relative">
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-burgundy-800 to-rosegold-500 text-white text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-widest shadow-md">
                        ⭐ PALING POPULER
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-rosegold-100 text-rosegold-700 flex items-center justify-center font-bold text-xl mb-4 mt-1">
                        🚀
                    </div>
                    <span class="text-xs font-bold text-rosegold-500 uppercase tracking-wider">Growth Pilot</span>
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mt-1 mb-2">Featured Card</h3>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-6">
                        Border highlight ganda dengan subtle rose gold tint untuk paket yang direkomendasikan.
                    </p>
                    <div class="text-2xl font-extrabold text-burgundy-950 dark:text-rosegold-300 mb-4">
                        Rp 189.000 <span class="text-xs font-normal text-slate-500">/ 6 bulan</span>
                    </div>
                    <x-ui.button variant="primary" size="sm" class="w-full">
                        Mulai 6 Bulan
                    </x-ui.button>
                </x-ui.card>

                {{-- Card 3: Burgundy Executive Hero --}}
                <x-ui.card variant="burgundy">
                    <div class="w-10 h-10 rounded-xl bg-white/10 text-rosegold-300 flex items-center justify-center font-bold text-xl mb-4">
                        👑
                    </div>
                    <span class="text-xs font-bold text-rosegold-300 uppercase tracking-widest">Executive Callout</span>
                    <h3 class="text-xl font-extrabold text-white mt-1 mb-2">Deep Wine Signature</h3>
                    <p class="text-rosegold-100 text-sm leading-relaxed mb-6">
                        Latar belakang gradient wine mewah untuk banner prioritas tinggi atau statistik akun.
                    </p>
                    <div class="text-2xl font-extrabold text-white mb-4">
                        Rp 297.000 <span class="text-xs font-normal text-rosegold-200">/ 1 tahun</span>
                    </div>
                    <x-ui.button variant="rosegold" size="sm" class="w-full">
                        Upgrade Ultimate
                    </x-ui.button>
                </x-ui.card>
            </div>
        </section>

        {{-- 6. FORM INPUTS --}}
        <section class="space-y-8 scroll-mt-24">
            <div class="border-b border-slate-200 dark:border-rosegold-500/20 pb-4">
                <h2 class="text-2xl font-extrabold text-burgundy-950 dark:text-white">6. Form Inputs (&lt;x-ui.input&gt;)</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Komponen form dengan focus ring burgundy kustom dan penataan error/hint bawaan.</p>
            </div>

            <x-ui.card class="max-w-2xl">
                <form class="space-y-5" onsubmit="event.preventDefault()">
                    <x-ui.input
                        label="Email Pengguna"
                        name="email"
                        type="email"
                        placeholder="nama@brandpilot.my.id"
                        :required="true"
                        hint="Gunakan email yang terdaftar pada akun TikTok / Instagram Anda."
                    />

                    <x-ui.input
                        label="Nama Brand / Bisnis"
                        name="brand_name"
                        placeholder="Contoh: BrandPilot Agency"
                    />

                    <div class="pt-2">
                        <x-ui.button variant="primary" size="md" type="submit">
                            Simpan Perubahan
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </section>

        {{-- 7. CODE & TOKENS EXPORT --}}
        <section id="code-tokens" class="space-y-8 scroll-mt-24">
            <div class="border-b border-slate-200 dark:border-rosegold-500/20 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-2xl font-extrabold text-burgundy-950 dark:text-white">7. Kode & Konfigurasi Siap Salin</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Gunakan konfigurasi di bawah langsung pada project baru Anda.</p>
                </div>
            </div>

            {{-- Code Box with Tabs --}}
            <div class="rounded-3xl border border-slate-200 dark:border-rosegold-500/20 bg-slate-900 text-slate-200 overflow-hidden shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-800 px-6 py-3 bg-slate-950/50">
                    <div class="flex gap-2">
                        <button onclick="switchTab('tw3')" id="tab-btn-tw3" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-burgundy-800 text-white">
                            Tailwind CSS v3
                        </button>
                        <button onclick="switchTab('tw4')" id="tab-btn-tw4" class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-400 hover:text-white">
                            Tailwind CSS v4 (@theme)
                        </button>
                        <button onclick="switchTab('css')" id="tab-btn-css" class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-400 hover:text-white">
                            CSS Variables (:root)
                        </button>
                    </div>
                    <button onclick="copyActiveCode()" class="text-xs font-bold px-3 py-1.5 rounded-md bg-white/10 hover:bg-white/20 text-white transition flex items-center gap-1.5">
                        📋 Salin Kode
                    </button>
                </div>

                {{-- Tab 1: Tailwind v3 --}}
                <div id="tab-tw3" class="p-6 overflow-x-auto text-xs font-mono">
<pre><code id="code-tw3">export default {
  theme: {
    extend: {
      fontFamily: {
        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
        serif: ['"Playfair Display"', 'Georgia', 'serif'],
      },
      colors: {
        burgundy: {
          50: '#FBF2F4', 100: '#F6E4E8', 200: '#ECCDD4', 300: '#DDA6B4',
          400: '#CA768D', 500: '#B24C68', 600: '#963650', 700: '#7C2A41',
          800: '#5B0E2D', /* Primary Brand Core */
          900: '#420B22', 950: '#2B0414',
        },
        rosegold: {
          50: '#FAF6F3', 100: '#F4ECE5', 200: '#E7D6C8', 300: '#D5B9A4',
          400: '#C0987E', 500: '#B76E79', /* Accent Core */
          600: '#A06E54', 700: '#855844', 800: '#6D4839', 900: '#593C31',
        },
        gold: { 500: '#D4AF37', 600: '#AA820A' }
      }
    }
  }
}</code></pre>
                </div>

                {{-- Tab 2: Tailwind v4 --}}
                <div id="tab-tw4" class="hidden p-6 overflow-x-auto text-xs font-mono">
<pre><code id="code-tw4">@import "tailwindcss";

@theme {
  --font-sans: 'Plus Jakarta Sans', system-ui, sans-serif;
  --font-serif: 'Playfair Display', Georgia, serif;

  --color-burgundy-50: #fbf2f4;
  --color-burgundy-100: #f6e4e8;
  --color-burgundy-200: #eccdd4;
  --color-burgundy-300: #dda6b4;
  --color-burgundy-400: #ca768d;
  --color-burgundy-500: #b24c68;
  --color-burgundy-600: #963650;
  --color-burgundy-700: #7c2a41;
  --color-burgundy-800: #5b0e2d;
  --color-burgundy-900: #420b22;
  --color-burgundy-950: #2b0414;

  --color-rosegold-50: #faf6f3;
  --color-rosegold-100: #f4ece5;
  --color-rosegold-200: #e7d6c8;
  --color-rosegold-300: #d5b9a4;
  --color-rosegold-400: #c0987e;
  --color-rosegold-500: #b76e79;
  --color-rosegold-600: #a06e54;
  --color-rosegold-700: #855844;

  --color-gold-500: #d4af37;
  --color-gold-600: #aa820a;
}</code></pre>
                </div>

                {{-- Tab 3: CSS Variables --}}
                <div id="tab-css" class="hidden p-6 overflow-x-auto text-xs font-mono">
<pre><code id="code-css">:root {
  --font-sans: 'Plus Jakarta Sans', sans-serif;
  --font-serif: 'Playfair Display', Georgia, serif;

  --brand-burgundy: #5B0E2D;
  --brand-rosegold: #B76E79;
  --brand-gold: #D4AF37;

  --gradient-brand-text: linear-gradient(135deg, #5B0E2D 0%, #B76E79 50%, #D4AF37 100%);
  --btn-burgundy-bg: linear-gradient(135deg, #5B0E2D 0%, #7C2A41 100%);
  --btn-burgundy-shadow: 0 10px 25px -5px rgba(91, 14, 45, 0.35);
}</code></pre>
                </div>
            </div>
        </section>

        {{-- 8. VIDEO MOTION EFFECTS ENGINE --}}
        <section id="motion-system" class="space-y-8 scroll-mt-24">
            <div class="border-b border-slate-200 dark:border-rosegold-500/20 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-2xl font-extrabold text-burgundy-950 dark:text-white">8. Video Motion Effects Engine (Remotion)</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Sistem animasi video kinetik berbasis Safe Zone Layout, Glassmorphism, Animated Dashed Lines, dan transisi B-Roll sinematik.</p>
                </div>
                <div class="flex gap-2">
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-300/40">
                        ✓ 100% Medsos Safe Zone
                    </span>
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-rosegold-100 dark:bg-burgundy-900 text-burgundy-900 dark:text-rosegold-300">
                        Remotion 4.0
                    </span>
                </div>
            </div>

            {{-- Safe Zone Rules Warning Banner --}}
            <div class="p-6 rounded-3xl bg-gradient-to-r from-burgundy-950/90 via-[#260614] to-burgundy-950/90 border border-rosegold-400/40 text-white shadow-xl space-y-4">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">📐</span>
                    <div>
                        <h3 class="text-lg font-extrabold text-white">Aturan Baku Tata Letak Safe Zone Video (1080 &times; 1920)</h3>
                        <p class="text-xs text-rosegold-200">Mencegah teks tertutup judul / caption medsos dan menjaga ekspresi wajah pembicara tetap terlihat penuh.</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4 pt-2 text-xs">
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                        <span class="font-extrabold text-amber-300 uppercase tracking-wider block">1. Face Protection Zone (Y 280–750px)</span>
                        <p class="text-slate-300 leading-relaxed">Area wajah pembicara <strong>dilarang keras</strong> ditutupi kartu tebal atau teks solid. Ekspresi pembicara harus terlihat natural dan berwibawa.</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-gradient-to-b from-emerald-950/50 to-transparent border border-emerald-500/40 space-y-1">
                        <span class="font-extrabold text-emerald-400 uppercase tracking-wider block">2. Golden Safe Band (Y 860–1420px)</span>
                        <p class="text-slate-200 leading-relaxed">Pita tengah (dada ke pinggang) adalah <strong>lokasi ideal</strong> seluruh teks kinetik, angka berjalan, dan kartu glassmorphism.</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-rose-950/40 border border-rose-500/30 space-y-1">
                        <span class="font-extrabold text-rose-300 uppercase tracking-wider block">3. Danger Zone Dasar (Y 1480–1920px)</span>
                        <p class="text-slate-300 leading-relaxed">Dasar video 450px <strong>wajib dikosongkan</strong> karena pasti tertutup caption, username akun, sound marquee, dan tombol like/share medsos.</p>
                    </div>
                </div>
            </div>

            {{-- Video Preview & Technique Cards --}}
            <div class="grid lg:grid-cols-12 gap-8 items-start">
                {{-- Video Player Column (5 cols) --}}
                <div class="lg:col-span-5 flex flex-col items-center">
                    <div class="relative w-full max-w-[320px] aspect-[9/16] rounded-3xl overflow-hidden shadow-2xl border-2 border-rosegold-400/40 bg-black">
                        <video
                            controls
                            playsinline
                            poster="/videos/test-f175.png"
                            class="w-full h-full object-cover"
                        >
                            <source src="/videos/motion-showcase.mp4" type="video/mp4">
                            Browser Anda tidak mendukung tag video HTML5.
                        </video>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 text-center mt-3">
                        Vertical 1080x1920 &bull; Safe Zone Centered &bull; B-Roll Switch &bull; Marching Dashed Lines
                    </p>
                </div>

                {{-- Techniques Breakdown (7 cols) --}}
                <div class="lg:col-span-7 space-y-4">
                    <div class="p-5 rounded-2xl bg-white dark:bg-burgundy-950/40 border border-slate-200 dark:border-rosegold-500/20 shadow-sm flex gap-4 items-start">
                        <div class="w-10 h-10 rounded-xl bg-burgundy-50 dark:bg-burgundy-900/60 text-burgundy-800 dark:text-rosegold-300 flex items-center justify-center font-bold text-lg shrink-0">
                            ⏱️
                        </div>
                        <div>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">1. Angka Berjalan (Rolling Numbers) &amp; Timer 2D Glassmorphism</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                                Diletakkan di tengah video (`top: 980px`), menyajikan counter kinetik <code class="font-mono text-burgundy-800 dark:text-rosegold-300 font-bold">0 &rarr; 30 MIN</code> di dalam panel kaca buram (*glassmorphism*) beraksen garis putus-putus berputar (*marching dashed border*) dan efek suara <code class="font-mono text-emerald-600 font-bold">tick.wav</code>.
                            </p>
                        </div>
                    </div>

                    <div class="p-5 rounded-2xl bg-white dark:bg-burgundy-950/40 border border-slate-200 dark:border-rosegold-500/20 shadow-sm flex gap-4 items-start">
                        <div class="w-10 h-10 rounded-xl bg-rosegold-100 dark:bg-burgundy-900/60 text-rosegold-800 dark:text-rosegold-300 flex items-center justify-center font-bold text-lg shrink-0">
                            🖍️
                        </div>
                        <div>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">2. Highlight Teks Marker &amp; Vektor Kelelahan (Mager)</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                                Garis penekanan <code class="font-mono text-burgundy-800 dark:text-rosegold-300 font-bold">4 TEMPAT</code> dengan sapuan kuas gradien burgundy-gold dan ilustrasi baterai drop berkedip, terpusat di area aman tanpa menutupi wajah pembicara, dipicu bersamaan efek suara <code class="font-mono text-emerald-600 font-bold">thump.wav</code>.
                            </p>
                        </div>
                    </div>

                    <div class="p-5 rounded-2xl bg-white dark:bg-burgundy-950/40 border border-slate-200 dark:border-rosegold-500/20 shadow-sm flex gap-4 items-start">
                        <div class="w-10 h-10 rounded-xl bg-burgundy-800 text-rosegold-300 flex items-center justify-center font-bold text-lg shrink-0">
                            🎬
                        </div>
                        <div>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">3. Transisi B-Roll Sinematik &amp; Mockup HP Multi-Platform</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                                Saat menjelaskan TikTok &rarr; IG &rarr; FB &rarr; YouTube, video bertransisi ke rekaman <strong>B-Roll meja kerja ponsel</strong>. Mockup HP titanium rose-gold berganti feed secara dinamis didampingi diagram laser bergaris putus-putus berpendar (<code class="font-mono text-emerald-600 font-bold">whoosh.wav</code> &amp; <code class="font-mono text-emerald-600 font-bold">pop.wav</code>).
                            </p>
                        </div>
                    </div>

                    <div class="p-5 rounded-2xl bg-white dark:bg-burgundy-950/40 border border-slate-200 dark:border-rosegold-500/20 shadow-sm flex gap-4 items-start">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-burgundy-900/60 text-amber-800 dark:text-rosegold-300 flex items-center justify-center font-bold text-lg shrink-0">
                            ✨
                        </div>
                        <div>
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white">4. Animasi Garis Putus-Putus (Marching Dashed Lines)</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                                Animasi garis putus-putus SVG dengan pergeseran <code class="font-mono text-burgundy-800 dark:text-rosegold-300 font-bold">strokeDashoffset</code> dinamis pada setiap frame, memberikan kesan futuristik, mengalirkan transmisi data antar simpul sosmed, dan membingkai tombol CTA.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4-Scene Gallery Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4">
                <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-rosegold-500/20 bg-slate-100 dark:bg-burgundy-950/40 p-2">
                    <img src="/videos/test-f35.png" alt="Scene 1 Frame 35" class="rounded-xl w-full aspect-[9/16] object-cover mb-2 shadow">
                    <div class="text-[11px] font-extrabold text-slate-900 dark:text-white">Scene 1 (Center Safe)</div>
                    <div class="text-[10px] text-slate-500">Angka Berjalan &amp; Dashed Ring</div>
                </div>

                <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-rosegold-500/20 bg-slate-100 dark:bg-burgundy-950/40 p-2">
                    <img src="/videos/test-f95.png" alt="Scene 2 Frame 95" class="rounded-xl w-full aspect-[9/16] object-cover mb-2 shadow">
                    <div class="text-[11px] font-extrabold text-slate-900 dark:text-white">Scene 2 (Center Safe)</div>
                    <div class="text-[10px] text-slate-500">Highlight Marker &amp; Mager Vector</div>
                </div>

                <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-rosegold-500/20 bg-slate-100 dark:bg-burgundy-950/40 p-2">
                    <img src="/videos/test-f175.png" alt="Scene 3 Frame 175" class="rounded-xl w-full aspect-[9/16] object-cover mb-2 shadow">
                    <div class="text-[11px] font-extrabold text-slate-900 dark:text-white">Scene 3 (Cinematic B-Roll)</div>
                    <div class="text-[10px] text-slate-500">HP Mockup &amp; Dashed Network</div>
                </div>

                <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-rosegold-500/20 bg-slate-100 dark:bg-burgundy-950/40 p-2">
                    <img src="/videos/test-f265.png" alt="Scene 4 Frame 265" class="rounded-xl w-full aspect-[9/16] object-cover mb-2 shadow">
                    <div class="text-[11px] font-extrabold text-slate-900 dark:text-white">Scene 4 (Center Safe)</div>
                    <div class="text-[10px] text-slate-500">Autopilot Wings &amp; Dashed CTA</div>
                </div>
            </div>
        </section>

    </main>

    <footer class="mt-20 py-10 border-t border-slate-200 dark:border-rosegold-500/20 text-center text-xs text-slate-500">
        <p>BrandPilot AI &mdash; Living Design System Specification &amp; Reusable Components</p>
        <p class="mt-1">Dibuat untuk arsitektur visual resmi <strong>brandpilot.my.id</strong></p>
    </footer>

    <script>
        // Copy Hex helper
        function copyHex(hex, name) {
            navigator.clipboard.writeText(hex).then(() => {
                showToast(`Warna ${name} (${hex}) tersalin!`);
            });
        }

        function showToast(message) {
            const toast = document.getElementById('toast');
            const toastText = document.getElementById('toast-text');
            toastText.innerText = message;
            toast.classList.remove('translate-y-16', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');

            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-16', 'opacity-0');
            }, 2500);
        }

        // Tab Switcher
        let activeTab = 'tw3';
        function switchTab(tab) {
            activeTab = tab;
            ['tw3', 'tw4', 'css'].forEach(t => {
                const tabEl = document.getElementById('tab-' + t);
                const btnEl = document.getElementById('tab-btn-' + t);
                if (t === tab) {
                    tabEl.classList.remove('hidden');
                    btnEl.classList.add('bg-burgundy-800', 'text-white');
                    btnEl.classList.remove('text-slate-400');
                } else {
                    tabEl.classList.add('hidden');
                    btnEl.classList.remove('bg-burgundy-800', 'text-white');
                    btnEl.classList.add('text-slate-400');
                }
            });
        }

        function copyActiveCode() {
            const codeEl = document.getElementById('code-' + activeTab);
            navigator.clipboard.writeText(codeEl.innerText).then(() => {
                showToast('Snippet kode berhasil disalin!');
            });
        }

        // Dark Mode Toggle
        function toggleDarkMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            updateThemeUI(isDark);
        }

        function updateThemeUI(isDark) {
            const icon = document.getElementById('theme-icon');
            const label = document.getElementById('theme-label');
            if (isDark) {
                icon.innerText = '☀️';
                label.innerText = 'Mode Terang';
            } else {
                icon.innerText = '🌙';
                label.innerText = 'Mode Gelap';
            }
        }

        // Init theme
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            updateThemeUI(true);
        } else {
            document.documentElement.classList.remove('dark');
            updateThemeUI(false);
        }
    </script>
</body>
</html>
