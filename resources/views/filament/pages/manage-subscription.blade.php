<x-filament-panels::page>
    @php
        $team = $this->team;
        $isActive = $team->subscription_status === 'active';
        $expires = $team->subscription_expires_at ? $team->subscription_expires_at->format('d M Y') : 'Tidak Terbatas';
        // Ganti URL ini dengan tautan Lynk.id Anda
        $lynkidPaymentUrl = "https://lynk.id/brandpilot/checkout?email=" . urlencode($team->customer_email ?? auth()->user()->email);
    @endphp

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
        {{-- Kartu Status Aktif Saat Ini --}}
        <div style="background: linear-gradient(135deg, #300713 0%, #1A040B 100%); border: 1px solid rgba(183, 110, 121, 0.4); border-radius: 18px; padding: 28px; position: relative; overflow: hidden;">
            <div style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px; background: {{ $isActive ? 'rgba(52, 211, 153, 0.2)' : 'rgba(239, 68, 68, 0.2)' }}; color: {{ $isActive ? '#34d399' : '#f87171' }}; border: 1px solid {{ $isActive ? '#34d399' : '#f87171' }};">
                STATUS: {{ strtoupper($team->subscription_status) }}
            </div>

            <h2 style="font-size: 24px; font-weight: 800; color: #fff; margin: 0 0 8px 0;">BrandPilot Pilot Pro</h2>
            <p style="font-size: 14px; color: #D1D5DB; margin-bottom: 24px;">Workspace autopilot aktif untuk reposting multi-platform & asisten pintar Gemini AI.</p>

            <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 16px; margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="font-size: 13px; color: #9CA3AF;">Email Akun:</span>
                    <span style="font-size: 13px; color: #E7D6C8; font-weight: 600;">{{ $team->customer_email ?? auth()->user()->email }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="font-size: 13px; color: #9CA3AF;">Aktif Hingga:</span>
                    <span style="font-size: 13px; color: #fff; font-weight: 700;">{{ $expires }}</span>
                </div>
            </div>

            <a href="{{ $lynkidPaymentUrl }}" target="_blank"
               style="display: block; text-align: center; background: linear-gradient(135deg, #7c2a41 0%, #5b0e2d 100%); color: #fff; border: 1px solid #B76E79; padding: 14px 24px; border-radius: 12px; font-weight: 700; font-size: 14px; text-decoration: none; box-shadow: 0 6px 20px rgba(91,14,45,0.5);">
                {{ $isActive ? '⚡ Perpanjang Langganan via Lynk.id' : '🚀 Upgrade ke Pro Sekarang via Lynk.id' }}
            </a>
        </div>

        {{-- Kartu Fitur-Fitur Unggulan --}}
        <div style="background: rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.1); border-radius: 18px; padding: 28px;">
            <h3 style="font-size: 18px; font-weight: 700; color: #E7D6C8; margin-top: 0; margin-bottom: 16px;">Semua Fitur Termasuk:</h3>
            <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 14px;">
                @foreach([
                    'Auto Sindikasi TikTok ke FB Reels, IG Reels & YouTube Shorts',
                    'Dukungan Custom Delay (30 Menit) atau Jadwal Jam Tertentu',
                    'Smart Auto-Caption Gemini AI (Judul, Hook & Tagar Relevan)',
                    'Smart Inbox Auto-Reply (Balas DM FB/IG & Komentar YT Otomatis)',
                    'Mode Polling Tiap 5 Menit (Hemat Kuota API Composio)',
                    'Dashboard Agregasi Statistik Views & Komentar Real-time',
                ] as $feature)
                    <li style="display: flex; align-items: center; gap: 10px; font-size: 14px; color: #E5E7EB;">
                        <span style="color: #34d399; font-weight: bold;">✓</span>
                        {{ $feature }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</x-filament-panels::page>