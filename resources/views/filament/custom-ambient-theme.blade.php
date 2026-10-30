<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/apple-touch-icon.png') }}">
<link rel="manifest" href="{{ asset('manifest.json') }}">
<meta name="theme-color" content="#5B0E2D">
<style>
    /* ==========================================================================
       BRANDPILOT AI: LUXURY AMBIENT GLOW THEME (LIGHT & DARK MODE)
       ========================================================================== */

    /* 1. TEMA TERANG (LIGHT MODE) */
    :root:not(.dark) {
        --bp-bg: #FCF9FA;
        --bp-card-bg: rgba(255, 255, 255, 0.88);
        --bp-border: rgba(183, 110, 121, 0.18);
        --bp-glow-1: rgba(197, 143, 114, 0.08); /* Rose Gold Glow */
        --bp-glow-2: rgba(91, 14, 45, 0.04);   /* Burgundy Glow */
        --bp-glow-3: rgba(212, 175, 55, 0.03);  /* Gold */
    }

    /* 2. TEMA GELAP (DARK MODE) */
    .dark {
        --bp-bg: #0C0307;                      /* Obsidian Wine */
        --bp-card-bg: rgba(22, 8, 15, 0.72);   /* Glass Deep Burgundy */
        --bp-border: rgba(183, 110, 121, 0.28);
        --bp-glow-1: rgba(183, 110, 121, 0.16); /* Rose Gold Aurora */
        --bp-glow-2: rgba(91, 14, 45, 0.26);   /* Deep Wine Nebula */
        --bp-glow-3: rgba(212, 175, 55, 0.07);  /* Gold Ambient */
    }

    /* ==========================================================================
       AMBIENT GLOW BACKGROUND
       ========================================================================== */
    body {
        background-color: var(--bp-bg) !important;
        position: relative;
        min-height: 100vh;
    }

    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        pointer-events: none;
        z-index: 0;
        background:
            radial-gradient(1100px 600px at 85% -5%, var(--bp-glow-1), transparent 65%),
            radial-gradient(900px 550px at 10% 25%, var(--bp-glow-2), transparent 60%),
            radial-gradient(750px 450px at 50% 105%, var(--bp-glow-3), transparent 65%);
        transition: background 0.5s ease-in-out;
    }

    /* Modal harus di atas sidebar & topbar.
       .fi-main sengaja diberi z-index:1 (membentuk stacking context), sehingga
       modal yang dirender di dalamnya bisa tertindih sidebar/topbar.
       JANGAN menaikkan .fi-modal-close-overlay: overlay default z-40, window z-50;
       kalau overlay dinaikkan melewati window, isi modal tertutup lapisan hitam. */
    .fi-modal {
        z-index: 9999 !important;
    }

    .fi-layout, .fi-main {
        position: relative;
        z-index: 1;
    }

    /* ==========================================================================
       POIN 1 & 2: SELARASKAN TOPBAR DI DARK MODE & HILANGKAN CELAH DENGAN SIDEBAR
       ========================================================================== */
    /* Pastikan Sidebar dan Topbar menyatu tanpa celah */
    .fi-topbar {
        margin: 0 !important;
        border-top: none !important;
        border-radius: 0 !important;
    }

    .fi-sidebar {
        margin: 0 !important;
        border-radius: 0 !important;
    }

    /* iOS Safari (WebKit) mengecilkan flex-child tanpa min-height:0, sehingga
       footer tombol PWA bisa terdorong keluar layar / mengecil jadi 0.
       Kunci tinggi nav agar scroll di dalam nav, footer tetap terlihat. */
    .fi-sidebar-nav {
        min-height: 0 !important;
    }

    .fi-sidebar-footer {
        flex: 0 0 auto !important;
    }

    /* Topbar di Light Theme */
    :root:not(.dark) .fi-topbar,
    :root:not(.dark) .fi-topbar > nav {
        background: rgba(255, 255, 255, 0.9) !important;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom: 1px solid var(--bp-border) !important;
    }

    :root:not(.dark) .fi-sidebar {
        background: rgba(255, 255, 255, 0.9) !important;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-right: 1px solid var(--bp-border) !important;
    }

    /* POIN 1: Topbar di Dark Theme (Selaras dengan Obsidian Wine) */
    .dark .fi-topbar,
    .dark .fi-topbar > nav {
        background: rgba(15, 5, 10, 0.92) !important;
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border-bottom: 1px solid var(--bp-border) !important;
    }

    .dark .fi-sidebar {
        background: rgba(15, 5, 10, 0.92) !important;
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border-right: 1px solid var(--bp-border) !important;
    }

    /* Item Navigasi Aktif */
    .fi-sidebar-item-active > a {
        background: linear-gradient(135deg, rgba(91, 14, 45, 0.12), rgba(183, 110, 121, 0.18)) !important;
        border: 1px solid var(--bp-border) !important;
        border-radius: 10px;
    }

    .dark .fi-sidebar-item-active > a {
        background: linear-gradient(135deg, rgba(91, 14, 45, 0.4), rgba(183, 110, 121, 0.25)) !important;
        border: 1px solid rgba(183, 110, 121, 0.45) !important;
    }

    /* ==========================================================================
       POIN 3: KEMBALIKAN KEMEWAHAN WIDGET SAMBUTAN (JANGAN DITIMPA CSS LAIN)
       ========================================================================== */
    /* Proteksi khusus agar Widget Sambutan tetap berwarna Burgundy Mewah */
    .bp-greeting-card {
        background: linear-gradient(135deg, #4A0E17 0%, #5B0E2D 45%, #250311 100%) !important;
        border: 1px solid rgba(183, 110, 121, 0.4) !important;
        box-shadow: 0 12px 30px -5px rgba(74, 14, 23, 0.45) !important;
        color: #ffffff !important;
        position: relative;
        overflow: hidden;
        border-radius: 18px;
        padding: 26px 30px;
    }

    /* Kembalikan warna teks judul dan deskripsi di dalam widget */
    .bp-greeting-card h1,
    .bp-greeting-card .bp-headline {
        color: #ffffff !important;
    }

    .bp-greeting-card p,
    .bp-greeting-card .bp-subtext {
        color: #D1D5DB !important;
    }

    /* ==========================================================================
       KARTU SECTION & TABEL (HANYA UNTUK KARTU BIASA, BUKAN WIDGET CUSTOM)
       ========================================================================== */
    .fi-section,
    .fi-ta-ctn {
        background: var(--bp-card-bg) !important;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid var(--bp-border) !important;
        border-radius: 16px !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04) !important;
    }

    .dark .fi-section,
    .dark .fi-ta-ctn {
        box-shadow: 0 12px 30px -5px rgba(0, 0, 0, 0.35) !important;
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 7px;
        height: 7px;
    }
    ::-webkit-scrollbar-track {
        background: transparent;
    }
    ::-webkit-scrollbar-thumb {
        background: rgba(183, 110, 121, 0.35);
        border-radius: 999px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: rgba(91, 14, 45, 0.65);
    }
</style>