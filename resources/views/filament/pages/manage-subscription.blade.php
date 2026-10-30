<x-filament-panels::page>
    @php
        $team = $this->team;
        $isActive = $team->subscription_status === 'active';
        $expires = $team->subscription_expires_at ? $team->subscription_expires_at->format('d M Y') : 'Trial';
    @endphp

    <style>
        /* ================= LIGHT MODE ================= */
        :root:not(.dark) .sub-card-main {
            background: #ffffff;
            border: 1px solid rgba(183, 110, 121, 0.35);
            box-shadow: 0 10px 30px -5px rgba(91, 14, 45, 0.06);
            color: #1e293b;
        }
        :root:not(.dark) .sub-card-feature {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
            color: #334155;
        }
        :root:not(.dark) .sub-info-box {
            background: #fdf8f9;
            border: 1px solid rgba(183, 110, 121, 0.2);
        }
        :root:not(.dark) .modal-box {
            background: #ffffff;
            border: 1px solid rgba(183, 110, 121, 0.3);
            color: #1e293b;
        }
        :root:not(.dark) .plan-item {
            background: #fdfafb;
            border: 1px solid #e2e8f0;
        }
        :root:not(.dark) .plan-item-selected {
            background: #fff5f7 !important;
            border: 2px solid #5B0E2D !important;
        }
        :root:not(.dark) .sub-title { color: #5B0E2D; }
        :root:not(.dark) .sub-desc { color: #64748b; }
        :root:not(.dark) .sub-label { color: #64748b; }
        :root:not(.dark) .sub-value { color: #0f172a; }

        /* ================= DARK MODE ================= */
        .dark .sub-card-main {
            background: linear-gradient(135deg, rgba(48, 7, 19, 0.9) 0%, rgba(26, 4, 11, 0.9) 100%);
            border: 1px solid rgba(183, 110, 121, 0.4);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.4);
            color: #ffffff;
        }
        .dark .sub-card-feature {
            background: rgba(22, 8, 15, 0.7);
            border: 1px solid rgba(183, 110, 121, 0.25);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.3);
            color: #e2e8f0;
        }
        .dark .sub-info-box {
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .dark .modal-box {
            background: #160710;
            border: 1px solid rgba(183, 110, 121, 0.4);
            color: #ffffff;
        }
        .dark .plan-item {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .dark .plan-item-selected {
            background: rgba(91, 14, 45, 0.35) !important;
            border: 2px solid #B76E79 !important;
        }
        .dark .sub-title { color: #ffffff; }
        .dark .sub-desc { color: #cbd5e1; }
        .dark .sub-label { color: #94a3b8; }
        .dark .sub-value { color: #ffffff; }
    </style>

    {{-- WRAPPER DENGAN STATE ALPINE.JS --}}
    <div x-data="{
        showModal: false,
        selectedPlan: '12',
        plans: {
            '1': {
                name: 'Subscription 1 Bulan',
                price: 'Rp 39.000',
                url: 'https://lynk.id/zakyjamal/731woweop93d/checkout'
            },
            '6': {
                name: 'Subscription 6 Bulan',
                price: 'Rp 189.000',
                url: 'https://lynk.id/zakyjamal/xj9nkn994yeo/checkout'
            },
            '12': {
                name: 'Subscription 12 Bulan (1 Tahun)',
                price: 'Rp 297.000',
                url: 'https://lynk.id/zakyjamal/jokgvgpvlq9x/checkout'
            }
        }
    }">

        {{-- GRID UTAMA HALAMAN --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
            
            {{-- KARTU STATUS LANGGANAN --}}
            <div class="sub-card-main" style="border-radius: 20px; padding: 32px; position: relative; overflow: hidden;">
                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px; border-radius: 9999px; font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 20px; 
                            background: {{ $isActive ? 'rgba(16, 185, 129, 0.12)' : 'rgba(239, 68, 68, 0.12)' }}; 
                            color: {{ $isActive ? '#059669' : '#dc2626' }}; 
                            border: 1px solid {{ $isActive ? '#10b981' : '#ef4444' }};">
                    <span style="width: 7px; height: 7px; border-radius: 50%; background: {{ $isActive ? '#10b981' : '#ef4444' }};"></span>
                    STATUS: {{ strtoupper($team->subscription_status) }}
                </div>

                <h2 class="sub-title" style="font-size: 26px; font-weight: 800; margin: 0 0 10px 0; letter-spacing: -0.5px;">
                    BrandPilot Pilot Pro
                </h2>
                
                <p class="sub-desc" style="font-size: 14px; line-height: 1.6; margin: 0 0 24px 0;">
                    Workspace autopilot aktif untuk reposting multi-platform TikTok ke Facebook, Instagram, dan YouTube.
                </p>

                <div class="sub-info-box" style="border-radius: 14px; padding: 18px 20px; margin-bottom: 26px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <span class="sub-label" style="font-size: 13px;">Email Akun:</span>
                        <span class="sub-value" style="font-size: 13px; font-weight: 700;">{{ $team->customer_email ?? auth()->user()->email }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="sub-label" style="font-size: 13px;">Aktif Hingga:</span>
                        <span style="font-size: 14px; font-weight: 800; color: #5B0E2D;">{{ $expires }}</span>
                    </div>
                </div>

                {{-- TOMBOL PEMICU MODAL --}}
                <button type="button" @click="showModal = true"
                        style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; background: linear-gradient(135deg, #7c2a41 0%, #5b0e2d 100%); color: #ffffff; border: 1px solid #B76E79; padding: 14px 24px; border-radius: 12px; font-weight: 700; font-size: 14px; cursor: pointer; box-shadow: 0 6px 20px rgba(91,14,45,0.35); transition: all 0.2s;">
                    <span>⚡ Perpanjang Langganan via Lynk.id</span>
                </button>
            </div>

            {{-- KARTU FITUR INCLUDED --}}
            <div class="sub-card-feature" style="border-radius: 20px; padding: 32px;">
                <h3 class="sub-title" style="font-size: 18px; font-weight: 800; margin: 0 0 20px 0; display: flex; align-items: center; gap: 8px;">
                    <span>✨</span> Semua Fitur Termasuk:
                </h3>

                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 14px;">
                    @foreach([
                        'Auto Sindikasi TikTok ke FB Reels, IG Reels & YouTube Shorts',
                        'Dukungan Penuh Format Video & Carousel Multi-Foto',
                        'Multimodal AI Gemini 3.1 Flash Lite (Video & Photo Understanding)',
                        'Pemisahan Judul Click-Worthy & Deskripsi Ber-Hook Tinggi',
                        'Bebas Watermark TikTok Kualitas HD Asli',
                        'Pilihan Jadwal Fleksibel (Langsung Tayang vs Jam Tertentu)',
                        'Dashboard Terpadu Agregasi Views & Performa Konten',
                    ] as $feature)
                        <li style="display: flex; align-items: flex-start; gap: 12px; font-size: 13.5px; line-height: 1.5;">
                            <span style="color: #10b981; font-weight: 800; font-size: 15px;">✓</span>
                            <span>{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- ============================================================== --}}
        {{-- MODAL TELEPORT KE BODY (DEAD-CENTER & Z-INDEX PALING DEPAN) --}}
        {{-- ============================================================== --}}
        <template x-teleport="body">
            <div x-show="showModal" 
                 x-cloak
                 @keydown.escape.window="showModal = false"
                 style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 999999; display: flex; align-items: center; justify-content: center; padding: 20px; background: rgba(0, 0, 0, 0.72); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">

                {{-- Kotak Modal di Tengah --}}
                <div class="modal-box" 
                     @click.away="showModal = false"
                     style="width: 100%; max-width: 520px; border-radius: 24px; padding: 32px; box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.6); position: relative; margin: auto;"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">

                    {{-- Header Modal --}}
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 22px;">
                        <div>
                            <h3 class="sub-title" style="font-size: 20px; font-weight: 800; margin: 0 0 4px 0;">
                                Pilih Durasi Perpanjangan
                            </h3>
                            <p class="sub-desc" style="font-size: 13px; margin: 0;">
                                Pilih paket perpanjangan yang Anda inginkan:
                            </p>
                        </div>
                        <button type="button" @click="showModal = false" 
                                style="background: transparent; border: none; font-size: 24px; cursor: pointer; color: #94a3b8; padding: 4px; line-height: 1;">
                            &times;
                        </button>
                    </div>

                    {{-- OPSI 3 PILIHAN PAKET --}}
                    <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 26px;">
                        
                        {{-- 1. OPSI 1 BULAN --}}
                        <div class="plan-item" 
                             :class="{ 'plan-item-selected': selectedPlan === '1' }"
                             @click="selectedPlan = '1'"
                             style="border-radius: 14px; padding: 16px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: all 0.2s;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <input type="radio" name="plan" value="1" x-model="selectedPlan" style="accent-color: #5B0E2D; width: 18px; height: 18px; cursor: pointer;">
                                <div>
                                    <h4 style="font-size: 15px; font-weight: 700; margin: 0;">1 Bulan</h4>
                                    <p style="font-size: 12px; color: #64748b; margin: 2px 0 0 0;">Perpanjangan fleksibel bulanan</p>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <span style="font-size: 16px; font-weight: 800; color: #5B0E2D;">Rp 39.000</span>
                            </div>
                        </div>

                        {{-- 2. OPSI 6 BULAN (HEMAT 19%) --}}
                        <div class="plan-item" 
                             :class="{ 'plan-item-selected': selectedPlan === '6' }"
                             @click="selectedPlan = '6'"
                             style="border-radius: 14px; padding: 16px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; position: relative; transition: all 0.2s;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <input type="radio" name="plan" value="6" x-model="selectedPlan" style="accent-color: #5B0E2D; width: 18px; height: 18px; cursor: pointer;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <h4 style="font-size: 15px; font-weight: 700; margin: 0;">6 Bulan</h4>
                                        <span style="font-size: 10px; font-weight: 700; background: rgba(197, 143, 114, 0.2); color: #C58F72; border: 1px solid #C58F72; padding: 1px 6px; border-radius: 4px;">Hemat 19%</span>
                                    </div>
                                    <p style="font-size: 12px; color: #64748b; margin: 2px 0 0 0;">Rp 31.500 / bulan</p>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <span style="font-size: 16px; font-weight: 800; color: #5B0E2D;">Rp 189.000</span>
                            </div>
                        </div>

                        {{-- 3. OPSI 12 BULAN (1 TAHUN - BEST VALUE) --}}
                        <div class="plan-item" 
                             :class="{ 'plan-item-selected': selectedPlan === '12' }"
                             @click="selectedPlan = '12'"
                             style="border-radius: 14px; padding: 16px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; position: relative; transition: all 0.2s;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <input type="radio" name="plan" value="12" x-model="selectedPlan" style="accent-color: #5B0E2D; width: 18px; height: 18px; cursor: pointer;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <h4 style="font-size: 15px; font-weight: 800; margin: 0;">12 Bulan (1 Tahun)</h4>
                                        <span style="font-size: 10px; font-weight: 800; background: #5B0E2D; color: #ffffff; padding: 1px 8px; border-radius: 4px;">⭐ TERBAIK</span>
                                    </div>
                                    <p style="font-size: 12px; color: #10b981; font-weight: 600; margin: 2px 0 0 0;">Cuma Rp 800-an / hari</p>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <span style="font-size: 17px; font-weight: 900; color: #5B0E2D;">Rp 297.000</span>
                            </div>
                        </div>

                    </div>

                    {{-- FOOTER MODAL & TOMBOL DINAMIS KE LYNK.ID --}}
                    <div style="display: flex; gap: 12px;">
                        <button type="button" @click="showModal = false"
                                style="flex: 1; padding: 12px; border-radius: 10px; font-weight: 600; font-size: 13.5px; background: rgba(0,0,0,0.06); border: 1px solid rgba(0,0,0,0.1); cursor: pointer; color: inherit;">
                            Batal
                        </button>

                        {{-- Link Checkout Dinamis Menyesuaikan Opsi --}}
                        <a :href="plans[selectedPlan].url" target="_blank"
                           style="flex: 2; text-align: center; text-decoration: none; padding: 12px; border-radius: 10px; font-weight: 700; font-size: 14px; background: linear-gradient(135deg, #7c2a41 0%, #5b0e2d 100%); color: #ffffff !important; border: 1px solid #B76E79; box-shadow: 0 4px 15px rgba(91,14,45,0.4); display: flex; align-items: center; justify-content: center; gap: 6px;">
                            <span>Lanjut Bayar</span>
                            <span x-text="'(' + plans[selectedPlan].price + ')'"></span>
                            <span>→</span>
                        </a>
                    </div>

                </div>
            </div>
        </template>

    </div>
</x-filament-panels::page>