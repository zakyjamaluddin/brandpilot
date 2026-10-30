<x-filament-panels::page>
<div x-data="{ ytOpen: false }" x-on:keydown.escape.window="ytOpen = false">
    <p style="font-size: 12.5px; color: #9CA3AF; margin: 0 0 16px;">
        Status diambil langsung dari Composio setiap halaman ini dibuka — bukan tombol yang bisa dinyalakan manual.
    </p>

    @if(! $hasComposioKey)
        <div style="background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.45); border-radius: 12px; padding: 14px 16px; margin-bottom: 16px;">
            <strong style="color: #fca5a5; font-size: 13px;">Composio API Key belum di-assign</strong>
            <p style="color: #fecaca; font-size: 12.5px; margin: 4px 0 0; line-height: 1.5;">
                Workspace ini belum punya Composio API Key, jadi koneksi platform tidak bisa dijalankan.
                Assign dulu di menu <strong>API Keys</strong> → Teams → pilih Composio Key.
            </p>
        </div>
    @endif
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        @foreach(['facebook' => ['name' => 'Facebook Page', 'color' => '#1877F2', 'desc' => 'Auto repost video ke postingan Page FB Anda.'],
                  'instagram' => ['name' => 'Instagram Reels', 'color' => '#E1306C', 'desc' => 'Auto publish ke Instagram Reels & feed.'],
                  'youtube'   => ['name' => 'YouTube Shorts', 'color' => '#FF0000', 'desc' => 'Auto upload video ke YouTube Shorts channel.']] as $key => $meta)
            
            @php $status = $integrations[$key] ?? ['is_connected' => false, 'account_name' => 'Belum Terhubung']; @endphp

            <div style="background: linear-gradient(135deg, #2D0713 0%, #1A040B 100%); border: 1px solid rgba(183, 110, 121, 0.35); border-radius: 16px; padding: 24px; position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 44px; height: 44px; border-radius: 10px; background: {{ $meta['color'] }}20; border: 1px solid {{ $meta['color'] }}60; display: flex; align-items: center; justify-content: center; font-weight: bold; color: {{ $meta['color'] }}; font-size: 18px;">
                            {{ strtoupper(substr($key, 0, 2)) }}
                        </div>
                        <div>
                            <h3 style="font-size: 16px; font-weight: 700; color: #fff; margin: 0;">{{ $meta['name'] }}</h3>
                            <span style="font-size: 12px; color: {{ $status['is_connected'] ? '#34d399' : '#9CA3AF' }};">
                                {{ $status['is_connected'] ? '● Terhubung' : '○ Terputus' }}
                            </span>
                        </div>
                    </div>
                </div>

                <p style="font-size: 13px; color: #D1D5DB; margin-bottom: 20px; line-height: 1.4;">{{ $meta['desc'] }}</p>

                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 16px;">
                    <span style="font-size: 12px; color: #C58F72;">{{ $status['account_name'] }}</span>

                    @if($status['is_connected'])
                        <button wire:click="disconnectPlatform('{{ $key }}')"
                                style="background: rgba(239, 68, 68, 0.2); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.4); padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;">
                            Disconnect
                        </button>
                    @elseif($hasComposioKey)
                        @if($key === 'youtube')
                            <button type="button" x-on:click="ytOpen = true"
                                    style="background: linear-gradient(135deg, #7c2a41 0%, #5b0e2d 100%); color: #fff; border: 1px solid #B76E79; padding: 6px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(91,14,45,0.4);">
                                Hubungkan via Composio
                            </button>
                        @else
                            <button wire:click="connectPlatform('{{ $key }}')"
                                    style="background: linear-gradient(135deg, #7c2a41 0%, #5b0e2d 100%); color: #fff; border: 1px solid #B76E79; padding: 6px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(91,14,45,0.4);">
                                Hubungkan via Composio
                            </button>
                        @endif
                    @else
                        <span title="Assign Composio API Key dulu di menu API Keys"
                              style="background: rgba(255,255,255,0.06); color: #6B7280; border: 1px dashed rgba(255,255,255,0.2); padding: 6px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: not-allowed;">
                            Butuh API Key
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- ================= MODAL TUTORIAL YOUTUBE ================= --}}
    <style>
        [x-cloak] { display: none !important; }
        .yt-modal-backdrop {
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(8, 2, 5, 0.78);
            backdrop-filter: blur(6px);
            display: flex; align-items: center; justify-content: center;
            padding: 18px;
        }
        .yt-modal-panel {
            width: 100%; max-width: 720px; max-height: 90vh; overflow: hidden;
            display: flex; flex-direction: column;
            background: linear-gradient(165deg, #2D0713 0%, #170309 100%);
            border: 1px solid rgba(183, 110, 121, 0.45);
            border-radius: 18px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.65);
            color: #E9DDE0;
        }
        .yt-modal-scroll { overflow-y: auto; flex: 1 1 auto; min-height: 0; }
        .yt-modal-head {
            padding: 22px 24px 0;
        }
        .yt-eyebrow {
            display: inline-block; font-size: 10.5px; letter-spacing: .18em; text-transform: uppercase;
            font-weight: 700; color: #E3B98F; background: rgba(201, 162, 39, 0.12);
            border: 1px solid rgba(201, 162, 39, 0.35); border-radius: 999px; padding: 4px 10px;
        }
        .yt-title {
            font-size: 21px; font-weight: 700; color: #fff; margin: 10px 0 6px; line-height: 1.3;
        }
        .yt-sub { font-size: 13px; color: #C9B4B9; margin: 0; line-height: 1.55; }
        .yt-body { padding: 18px 24px 20px; }
        .yt-step { display: flex; gap: 14px; margin-bottom: 20px; }
        .yt-step-num {
            flex: 0 0 28px; width: 28px; height: 28px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12.5px; font-weight: 800; color: #2D0713;
            background: linear-gradient(135deg, #E9C77E, #C9A227);
            box-shadow: 0 4px 10px rgba(201, 162, 39, .28);
        }
        .yt-step-body { flex: 1; min-width: 0; }
        .yt-step-title { font-size: 13.5px; font-weight: 700; color: #fff; margin: 3px 0 5px; }
        .yt-step-text { font-size: 12.8px; color: #CBB8BD; margin: 0 0 10px; line-height: 1.6; }
        .yt-figure { margin: 0; border: 1px solid rgba(255,255,255,0.10); border-radius: 12px; overflow: hidden; background: #0E0207; }
        .yt-figure img { display: block; width: 100%; max-height: 260px; object-fit: contain; background: #0E0207; }
        .yt-figure-zoom img { max-height: 150px; }
        .yt-figure figcaption { font-size: 11px; color: #9C858C; padding: 7px 10px; border-top: 1px solid rgba(255,255,255,0.07); }
        .yt-warn {
            background: rgba(239, 68, 68, 0.10); border: 1px solid rgba(239, 68, 68, 0.32);
            border-left: 3px solid #ef4444; border-radius: 10px; padding: 11px 13px;
            font-size: 12.3px; color: #F3C7C7; line-height: 1.55; margin: 0 0 10px;
        }
        .yt-note {
            background: rgba(52, 211, 153, 0.08); border: 1px solid rgba(52, 211, 153, 0.28);
            border-radius: 10px; padding: 11px 13px; font-size: 12.3px; color: #B9E7D3; line-height: 1.55;
        }
        .yt-modal-foot {
            display: flex; gap: 10px; justify-content: flex-end; align-items: center;
            padding: 16px 24px 22px; border-top: 1px solid rgba(255,255,255,0.08); margin-top: 0;
            background: #170309; flex-shrink: 0;
        }
        .yt-btn-ghost {
            background: transparent; color: #E9DDE0; border: 1px solid rgba(233,221,224,0.45);
            padding: 9px 16px; border-radius: 9px; font-size: 12.5px; font-weight: 600; cursor: pointer;
        }
        .yt-btn-primary {
            background: linear-gradient(135deg, #8A2E48 0%, #5B0E2D 100%); color: #fff;
            border: 1px solid #B76E79; padding: 10px 18px; border-radius: 9px;
            font-size: 12.8px; font-weight: 700; cursor: pointer;
            box-shadow: 0 10px 24px rgba(91, 14, 45, 0.5);
        }
        .yt-btn-primary:hover { filter: brightness(1.08); }
        @media (max-width: 640px) {
            .yt-modal-head { padding: 18px 16px 0; }
            .yt-body { padding: 14px 16px 16px; }
            .yt-title { font-size: 18px; }
            .yt-step { gap: 10px; margin-bottom: 16px; }
            .yt-figure img { max-height: 170px; }
            .yt-figure-zoom img { max-height: 105px; }
            .yt-modal-foot { flex-direction: column-reverse; padding: 12px 16px 16px; }
            .yt-btn-primary, .yt-btn-ghost { width: 100%; }
        }
    </style>

    <template x-teleport="body">
    <div x-cloak x-show="ytOpen" x-transition.opacity class="yt-modal-backdrop"
         x-on:keydown.escape.window="ytOpen = false">
        <div class="yt-modal-panel" x-on:click.outside="ytOpen = false" role="dialog" aria-modal="true" aria-labelledby="yt-modal-title">

            <div class="yt-modal-scroll">
            <div class="yt-modal-head">
                <span class="yt-eyebrow">Tutorial YouTube</span>
                <h2 class="yt-title" id="yt-modal-title">Sambungkan YouTube dengan aman</h2>
                <p class="yt-sub">Ikuti 3 langkah ini. Peringatan dari Google adalah hal normal — bukan tanda akun Anda bermasalah.</p>
            </div>

            <div class="yt-body">
                <div class="yt-step">
                    <div class="yt-step-num">1</div>
                    <div class="yt-step-body">
                        <h3 class="yt-step-title">Pastikan akun Google Anda sudah punya channel YouTube</h3>
                        <p class="yt-step-text">
                            Login ke akun Google yang <strong>sudah memiliki channel</strong> (bukan sekadar akun email).
                            Buka <a href="https://www.youtube.com" target="_blank" rel="noopener" style="color:#E3B98F;text-decoration:underline;">youtube.com</a>
                            — jika diminta membuat channel, buat dulu. Tanpa channel, Composio akan menolak dengan pesan
                            <em>“Channel not found”</em> dan video tidak akan pernah terunggah.
                        </p>
                    </div>
                </div>

                <div class="yt-step">
                    <div class="yt-step-num">2</div>
                    <div class="yt-step-body">
                        <h3 class="yt-step-title">Abaikan peringatan “Google belum memverifikasi aplikasi ini”</h3>
                        <p class="yt-step-text">
                            Setelah menekan tombol lanjut, Google menampilkan halaman peringatan seperti ini.
                            <strong>Jangan klik tombol biru “Kembali ke tempat aman”.</strong>
                            Klik teks <strong>“Lanjutkan”</strong> / <strong>“Advanced”</strong> di bagian bawah kiri halaman
                            untuk membuka opsi lanjutan.
                        </p>
                        <figure class="yt-figure">
                            <img src="{{ asset('images/tutorial/youtube-step2-continue.jpg') }}" alt="Peringatan Google: klik Lanjutan, bukan tombol biru">
                            <figcaption>Bahan 1 — klik “Lanjutan” (dilingkari), jangan tombol biru “Kembali ke tempat aman”.</figcaption>
                        </figure>
                    </div>
                </div>

                <div class="yt-step">
                    <div class="yt-step-num">3</div>
                    <div class="yt-step-body">
                        <h3 class="yt-step-title">Klik link “Buka composio.dev (tidak aman)”</h3>
                        <p class="yt-step-text">
                            Di bagian bawah halaman akan muncul tautan <strong>“Buka composio.dev (tidak aman)”</strong>.
                            Klik tautan itu untuk melanjutkan ke halaman izin Composio, lalu setujui akses
                            <em>upload video</em> ke channel Anda. Setelah selesai, Anda otomatis kembali ke BrandPilot.
                        </p>
                        <figure class="yt-figure">
                            <img src="{{ asset('images/tutorial/youtube-warning.jpg') }}" alt="Link Buka composio.dev (tidak aman)">
                            <figcaption>Bahan 2 — tautan “Buka composio.dev (tidak aman)” yang dilingkari pada halaman peringatan.</figcaption>
                        </figure>
                        <figure class="yt-figure yt-figure-zoom" style="margin-top: 10px;">
                            <img src="{{ asset('images/tutorial/youtube-advanced-link.jpg') }}" alt="Perbesaran link Buka composio.dev (tidak aman)">
                            <figcaption>Perbesaran — posisi tautan yang harus diklik.</figcaption>
                        </figure>
                    </div>
                </div>

                <div class="yt-warn">
                    <strong>Penting:</strong> pastikan browser sedang login ke akun Google yang benar. Jika sebelumnya Anda
                    membuka akun lain, keluar dulu atau gunakan jendela privat — kalau tidak, channel yang terhubung bisa
                    tertukar dengan channel tenant lain.
                </div>

                <div class="yt-note">
                    Halaman peringatan muncul karena aplikasi masih dalam proses verifikasi Google. Izin yang diminta hanya
                    untuk mengunggah video; BrandPilot tidak pernah menerima kata sandi akun Google Anda.
                </div>
            </div>
            </div>

            <div class="yt-modal-foot">
                <button type="button" class="yt-btn-ghost" x-on:click="ytOpen = false">Batal</button>
                <button type="button" class="yt-btn-primary"
                        wire:click="connectPlatform('youtube')"
                        x-on:click="ytOpen = false">
                    Saya faham dan bisa melanjutkan
                </button>
            </div>
        </div>
    </div>
    </template>
</div>
</x-filament-panels::page>