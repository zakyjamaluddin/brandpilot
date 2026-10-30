<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'BrandPilot AI')</title>
    <meta name="description" content="@yield('description', 'BrandPilot AI — autopilot sindikasi konten TikTok ke Instagram, Facebook, dan YouTube.')">
    <meta name="theme-color" content="#5B0E2D">

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

    <style>
        :root{
            --burgundy-950:#2B0414;
            --burgundy-900:#420B22;
            --burgundy-800:#5B0E2D;
            --rose:#B76E79;
            --rose-soft:#E7D6C8;
            --gold:#C9A227;
            --cream:#FAF7F5;
            --ink:#2C2320;
        }
        *{box-sizing:border-box}
        html{scroll-behavior:smooth}
        body{
            margin:0;
            font-family:'Plus Jakarta Sans',system-ui,-apple-system,'Segoe UI',sans-serif;
            color:var(--ink);
            background:var(--cream);
            line-height:1.75;
            -webkit-font-smoothing:antialiased;
        }
        a{color:#7C2A41;text-decoration:none}
        a:hover{text-decoration:underline}

        /* ---------- top bar ---------- */
        .topbar{
            position:relative;
            background:
                radial-gradient(1200px 380px at 12% -20%, rgba(183,110,121,.35), transparent 60%),
                linear-gradient(135deg, var(--burgundy-950), var(--burgundy-800) 65%, #6d1234);
            color:#fff;
            border-bottom:1px solid rgba(201,162,39,.35);
        }
        .topbar-inner{
            max-width:1120px;margin:0 auto;padding:22px 24px;
            display:flex;align-items:center;justify-content:space-between;gap:18px;flex-wrap:wrap;
        }
        .brand{display:flex;align-items:center;gap:12px;color:#fff;text-decoration:none}
        .brand img{width:40px;height:40px;border-radius:11px;box-shadow:0 8px 22px rgba(0,0,0,.35)}
        .brand-name{font-weight:800;letter-spacing:.2px;font-size:17px}
        .brand-sub{display:block;font-size:11px;letter-spacing:.16em;text-transform:uppercase;color:rgba(255,255,255,.62);font-weight:600}
        .nav{display:flex;gap:22px;font-size:14px;font-weight:600;flex-wrap:wrap}
        .nav a{color:rgba(255,255,255,.82)}
        .nav a:hover{color:#fff;text-decoration:none}
        .nav a.active{color:#F3DFC6}

        /* ---------- hero ---------- */
        .hero{
            background:
                radial-gradient(900px 320px at 80% 0%, rgba(201,162,39,.10), transparent 60%),
                linear-gradient(180deg, rgba(43,4,20,.06), transparent 70%);
            border-bottom:1px solid #EFE6E0;
        }
        .hero-inner{max-width:1120px;margin:0 auto;padding:56px 24px 40px}
        .eyebrow{
            display:inline-flex;align-items:center;gap:8px;
            font-size:11px;letter-spacing:.22em;text-transform:uppercase;font-weight:700;
            color:#8A5A3B;background:#F6EBE4;border:1px solid #EADACF;border-radius:999px;padding:7px 14px;
        }
        h1{
            font-family:'Playfair Display',Georgia,serif;
            font-size:clamp(30px,4.4vw,46px);
            line-height:1.15;margin:18px 0 10px;color:var(--burgundy-900);font-weight:700;
        }
        .lede{font-size:16px;color:#6B5A52;max-width:720px;margin:0}
        .updated{margin-top:18px;font-size:12.5px;color:#8B7A72;letter-spacing:.02em}

        /* ---------- layout ---------- */
        .shell{max-width:1120px;margin:0 auto;padding:44px 24px 72px;display:grid;grid-template-columns:250px minmax(0,1fr);gap:38px;align-items:start}
        @media (max-width:900px){ .shell{grid-template-columns:1fr;gap:22px;padding-top:30px} }

        .toc{
            position:sticky;top:24px;background:#fff;border:1px solid #EDE3DC;border-radius:18px;padding:20px 18px;
            box-shadow:0 18px 40px -32px rgba(43,4,20,.55);
        }
        .toc h3{
            font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:#9A7E70;margin:0 0 12px;font-weight:700;
        }
        .toc a{display:block;font-size:13.5px;color:#4E3C35;padding:5px 0;border-left:2px solid transparent;padding-left:10px}
        .toc a:hover{color:var(--burgundy-800);border-left-color:var(--rose);text-decoration:none}
        .toc a.active{color:var(--burgundy-800);font-weight:700;border-left-color:var(--gold)}
        @media (max-width:900px){ .toc{position:static} }

        .doc{
            background:#fff;border:1px solid #EEE4DE;border-radius:26px;
            padding:clamp(26px,4vw,52px);
            box-shadow:0 40px 80px -60px rgba(43,4,20,.55);
        }
        .doc h2{
            font-family:'Playfair Display',Georgia,serif;
            font-size:clamp(20px,2.4vw,26px);color:var(--burgundy-900);
            margin:42px 0 12px;line-height:1.3;scroll-margin-top:22px;
        }
        .doc h2:first-of-type{margin-top:8px}
        .doc h2 .num{
            display:inline-block;min-width:34px;font-family:'Plus Jakarta Sans',sans-serif;
            font-size:12px;font-weight:800;letter-spacing:.12em;color:var(--gold);
            vertical-align:middle;margin-right:6px;
        }
        .doc h3{font-size:15.5px;color:#4A0C24;margin:24px 0 6px}
        .doc p{margin:0 0 14px;font-size:15px;color:#4A3E39}
        .doc ul,.doc ol{margin:0 0 16px;padding-left:22px}
        .doc li{margin-bottom:8px;font-size:15px;color:#4A3E39}
        .doc strong{color:#3A0A1C}
        .doc hr{border:0;border-top:1px solid #F0E7E1;margin:34px 0}

        .rule{height:1px;background:linear-gradient(90deg,transparent,rgba(183,110,121,.55),transparent);margin:26px 0 6px}

        .callout{
            background:linear-gradient(180deg,#FDF8F5,#FBF2EC);
            border:1px solid #EEDFD5;border-left:4px solid var(--rose);
            border-radius:14px;padding:16px 18px;margin:18px 0;
            font-size:14.5px;color:#5A463D;
        }
        .callout strong{color:var(--burgundy-800)}

        table{width:100%;border-collapse:collapse;margin:12px 0 18px;font-size:14px;overflow:hidden;border-radius:12px}
        th,td{text-align:left;padding:11px 14px;border-bottom:1px solid #F0E7E1}
        th{background:#FAF1EC;color:var(--burgundy-800);font-weight:700;font-size:12.5px;letter-spacing:.04em;text-transform:uppercase}
        tr:last-child td{border-bottom:0}

        .pill{display:inline-block;padding:3px 10px;border-radius:999px;background:#F6EBE4;color:#8A5A3B;font-size:11.5px;font-weight:700;letter-spacing:.04em}

        .doc-footer{
            margin-top:38px;padding-top:22px;border-top:1px solid #F0E7E1;
            display:flex;flex-wrap:wrap;gap:12px;justify-content:space-between;align-items:center;
            font-size:13px;color:#8B7A72;
        }
        .btn{
            display:inline-flex;align-items:center;gap:8px;background:var(--burgundy-800);color:#fff;
            padding:10px 16px;border-radius:11px;font-size:13.5px;font-weight:700;text-decoration:none;
            box-shadow:0 12px 24px -14px rgba(91,14,45,.75);
        }
        .btn:hover{background:var(--burgundy-900);text-decoration:none}

        .site-footer{background:var(--burgundy-950);color:rgba(255,255,255,.72);border-top:1px solid rgba(201,162,39,.25)}
        .site-footer .inner{max-width:1120px;margin:0 auto;padding:30px 24px;display:flex;flex-wrap:wrap;gap:16px;justify-content:space-between;font-size:13px}
        .site-footer a{color:rgba(255,255,255,.85)}
    </style>
</head>
<body>
    <header class="topbar">
        <div class="topbar-inner">
            <a class="brand" href="/">
                <img src="{{ asset('icons/icon-192.png') }}" alt="BrandPilot AI">
                <span>
                    <span class="brand-name">BrandPilot AI</span>
                    <span class="brand-sub">Autopilot Social Media</span>
                </span>
            </a>
            <nav class="nav">
                <a href="/">Beranda</a>
                <a href="{{ url('/kebijakan-privasi') }}" class="{{ request()->is('kebijakan-privasi') ? 'active' : '' }}">Kebijakan Privasi</a>
                <a href="{{ url('/syarat-ketentuan') }}" class="{{ request()->is('syarat-ketentuan') ? 'active' : '' }}">Syarat &amp; Ketentuan</a>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="hero-inner">
            <span class="eyebrow">@yield('eyebrow', 'Dokumen Legal')</span>
            <h1>@yield('heading')</h1>
            <p class="lede">@yield('lede')</p>
            <div class="updated">Terakhir diperbarui: @yield('updated', '22 Oktober 2026')</div>
        </div>
    </section>

    <main class="shell">
        <aside class="toc">
            <h3>Isi Dokumen</h3>
            @yield('toc')
        </aside>

        <article class="doc">
            @yield('content')

            <div class="doc-footer">
                <span>Ada pertanyaan? <a href="mailto:info@brandpilot.my.id">info@brandpilot.my.id</a></span>
                <a class="btn" href="/">Kembali ke Beranda</a>
            </div>
        </article>
    </main>

    <footer class="site-footer">
        <div class="inner">
            <span>&copy; {{ date('Y') }} BrandPilot AI. Seluruh hak cipta dilindungi.</span>
            <span>
                <a href="/kebijakan-privasi">Kebijakan Privasi</a> &nbsp;·&nbsp;
                <a href="/syarat-ketentuan">Syarat &amp; Ketentuan</a> &nbsp;·&nbsp;
                <a href="mailto:info@brandpilot.my.id">Kontak</a>
            </span>
        </div>
    </footer>

    <script>
        // Scrollspy sederhana untuk daftar isi
        (function () {
            const links = Array.from(document.querySelectorAll('.toc a[href^="#"]'));
            if (!links.length) return;
            const targets = links
                .map(a => document.getElementById(a.getAttribute('href').slice(1)))
                .filter(Boolean);

            const setActive = (id) => {
                links.forEach(a => a.classList.toggle('active', a.getAttribute('href') === '#' + id));
            };

            const onScroll = () => {
                let current = targets[0];
                for (const t of targets) {
                    if (t.getBoundingClientRect().top <= 120) current = t;
                }
                if (current) setActive(current.id);
            };

            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        })();
    </script>
</body>
</html>
