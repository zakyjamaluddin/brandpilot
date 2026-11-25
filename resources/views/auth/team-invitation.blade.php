<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Undangan Mengelola Workspace {{ $team->name }} — BrandPilot AI</title>
    <meta name="description" content="Bergabunglah untuk mengelola workspace {{ $team->name }} di BrandPilot AI. Autopilot sindikasi konten TikTok ke Instagram, Facebook, dan YouTube.">
    <meta name="theme-color" content="#5B0E2D">

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/apple-touch-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

    <style>
        :root {
            --burgundy-950: #1a030d;
            --burgundy-900: #2e0618;
            --burgundy-800: #5b0e2d;
            --rose: #b76e79;
            --rose-light: #f3cebf;
            --cream: #faf7f5;
            --slate-400: #94a3b8;
            --slate-300: #cbd5e1;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: radial-gradient(circle at top center, #3d091e 0%, #17020b 60%, #0d0106 100%);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient Glow Effect */
        .ambient-glow {
            position: absolute;
            width: 480px;
            height: 480px;
            background: radial-gradient(circle, rgba(183, 110, 121, 0.15) 0%, rgba(91, 14, 45, 0) 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .card-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 460px;
            background: rgba(43, 6, 23, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(243, 206, 191, 0.16);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.05);
            border-radius: 28px;
            padding: 40px 32px;
            text-align: center;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(243, 206, 191, 0.1);
            border: 1px solid rgba(243, 206, 191, 0.25);
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--rose-light);
            margin-bottom: 24px;
        }

        .brand-badge span.dot {
            width: 6px;
            height: 6px;
            background-color: #10b981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 8px #10b981;
        }

        .logo-title {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.02em;
            margin-bottom: 8px;
        }

        .logo-title span {
            color: var(--rose-light);
            font-style: italic;
        }

        .team-box {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(243, 206, 191, 0.12);
            border-radius: 20px;
            padding: 20px 16px;
            margin: 24px 0;
        }

        .team-subtitle {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--slate-400);
            margin-bottom: 6px;
            font-weight: 600;
        }

        .team-name {
            font-size: 20px;
            font-weight: 800;
            color: #ffffff;
            word-break: break-word;
        }

        .invitation-desc {
            font-size: 14px;
            line-height: 1.6;
            color: var(--slate-300);
            margin-bottom: 28px;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 14px;
            font-size: 13px;
            margin-bottom: 20px;
            text-align: left;
        }

        /* Tombol Google Sign In */
        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            padding: 14px 20px;
            background-color: #ffffff;
            color: #1f2937;
            font-size: 15px;
            font-weight: 700;
            border-radius: 16px;
            text-decoration: none;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .btn-google:hover {
            background-color: #f9fafb;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(243, 206, 191, 0.25);
            color: #111827;
        }

        .btn-google svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 22px 0;
            color: var(--slate-400);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }

        .login-link {
            display: inline-block;
            color: var(--rose-light);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .login-link:hover {
            text-decoration: underline;
            opacity: 0.85;
        }

        .footer-note {
            margin-top: 24px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.4);
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="ambient-glow"></div>

    <div class="card-container">
        <!-- Badge Status Undangan -->
        <div class="brand-badge">
            <span class="dot"></span>
            Tautan Undangan Resmi
        </div>

        <!-- Logo & Brand Header -->
        <h1 class="logo-title">BrandPilot <span>AI</span></h1>

        <!-- Detail Workspace yang Diundang -->
        <div class="team-box">
            <div class="team-subtitle">Anda Diundang Mengelola Workspace</div>
            <div class="team-name">{{ $team->name }}</div>
        </div>

        <p class="invitation-desc">
            Dapatkan akses penuh untuk mengelola integrasi sosial media, pemantauan TikTok, dan jadwal publikasi konten autopilot di workspace ini.
        </p>

        @if(session('error'))
            <div class="alert-error">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tombol Utama: Sign In by Google -->
        <a href="{{ route('auth.google.redirect') }}" class="btn-google">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17Z"/>
                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24Z"/>
                <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15Z"/>
                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98Z"/>
            </svg>
            Lanjutkan dengan Google
        </a>

        <div class="divider">atau</div>

        <div>
            <a href="{{ url('/admin/login') }}" class="login-link">
                Sudah punya akun? Masuk dengan Email & Password &rarr;
            </a>
        </div>

        <p class="footer-note">
            Akun baru otomatis dibuat saat pertama kali masuk dengan Google. Aman & terenkripsi tanpa perlu membuat password manual.
        </p>
    </div>
</body>
</html>
