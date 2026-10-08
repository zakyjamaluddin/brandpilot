<x-filament-widgets::widget>
    @php
        $team = $this->getActiveTeam();
        $user = auth()->user();

        $hour = (int) now()->format('H');
        if ($hour < 12) {
            $greeting = 'Selamat Pagi';
        } elseif ($hour < 15) {
            $greeting = 'Selamat Siang';
        } elseif ($hour < 18) {
            $greeting = 'Selamat Sore';
        } else {
            $greeting = 'Selamat Malam';
        }
    @endphp

    <style>
        .bp-greeting-card {
            position: relative;
            overflow: hidden;
            border-radius: 18px;
            padding: 26px 30px;
            background: linear-gradient(135deg, #4A0E17 0%, #5B0E2D 45%, #250311 100%);
            border: 1px solid rgba(183, 110, 121, 0.4);
            box-shadow: 0 12px 30px -5px rgba(74, 14, 23, 0.45);
            color: #ffffff;
            font-family: inherit;
        }

        .bp-glow-rose {
            position: absolute;
            top: -40px;
            right: -40px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(197, 143, 114, 0.28) 0%, rgba(197, 143, 114, 0) 70%);
            pointer-events: none;
        }

        .bp-glow-burgundy {
            position: absolute;
            bottom: -30px;
            left: -30px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(91, 14, 45, 0.7) 0%, rgba(91, 14, 45, 0) 70%);
            pointer-events: none;
        }

        .bp-container {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
        }

        .bp-left-content {
            flex: 1;
            min-width: 280px;
        }

        .bp-pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 14px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(231, 214, 200, 0.3);
            color: #E7D6C8;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 12px;
            letter-spacing: 0.3px;
        }

        .bp-live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #34d399;
            box-shadow: 0 0 8px #34d399;
        }

        .bp-headline {
            font-size: 26px;
            font-weight: 800;
            color: #ffffff;
            margin: 0 0 6px 0;
            line-height: 1.25;
            letter-spacing: -0.5px;
        }

        .bp-headline span {
            color: #E7D6C8;
        }

        .bp-subtext {
            color: #D1D5DB;
            font-size: 14px;
            margin: 0;
            max-width: 560px;
            line-height: 1.5;
        }

        .bp-right-box {
            display: flex;
            align-items: center;
            gap: 20px;
            background: rgba(0, 0, 0, 0.32);
            border: 1px solid rgba(183, 110, 121, 0.3);
            padding: 14px 20px;
            border-radius: 14px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .bp-info-group {
            display: flex;
            flex-direction: column;
        }

        .bp-label {
            font-size: 11px;
            color: #9CA3AF;
            font-weight: 500;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .bp-val-title {
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
            margin: 0;
        }

        .bp-val-tiktok {
            font-size: 12px;
            color: #C58F72;
            font-family: monospace;
            margin-top: 1px;
        }

        .bp-separator {
            width: 1px;
            height: 38px;
            background: rgba(255, 255, 255, 0.15);
        }

        .bp-status-badge {
            display: inline-block;
            margin-top: 3px;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            background: rgba(183, 110, 121, 0.22);
            color: #F4ECE5;
            border: 1px solid #B76E79;
        }
    </style>

    <div class="bp-greeting-card">
        {{-- Glow Effects --}}
        <div class="bp-glow-rose"></div>
        <div class="bp-glow-burgundy"></div>

        <div class="bp-container">
            {{-- Bagian Teks Kiri --}}
            <div class="bp-left-content">
                <div class="bp-pill-badge">
                    <span class="bp-live-dot"></span>
                    BrandPilot AI Copilot Active
                </div>

                <h1 class="bp-headline">
                    {{ $greeting }}, <span>{{ $user->name }}</span>! ✨
                </h1>

                <p class="bp-subtext">
                    Sistem autopilot media sosial Anda siap mendistribusikan konten TikTok ke FB, IG, dan YouTube.
                </p>
            </div>

            {{-- Bagian Info Kanan (Rose Gold Card) --}}
            <div class="bp-right-box">
                <div class="bp-info-group">
                    <span class="bp-label">Workspace Aktif</span>
                    <span class="bp-val-title">{{ $team->name ?? 'Default Brand' }}</span>
                    <span class="bp-val-tiktok">&#64;{{ $team->tiktok_handle ?? 'tiktok_belum_diatur' }}</span>
                </div>

                <div class="bp-separator"></div>

                <div class="bp-info-group">
                    <span class="bp-label">Langganan</span>
                    <span class="bp-status-badge">
                        {{ $team->subscription_status ?? 'TRIAL' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
