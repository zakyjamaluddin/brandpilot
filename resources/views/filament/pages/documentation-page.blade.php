<x-filament-panels::page>
    <style>
        /* ================= LIGHT MODE ================= */
        :root:not(.dark) .doc-intro {
            background: linear-gradient(135deg, #fdf4f6 0%, #ffffff 100%);
            border: 1px solid rgba(183, 110, 121, 0.35);
            box-shadow: 0 8px 25px -5px rgba(91, 14, 45, 0.05);
            color: #1e293b;
        }
        :root:not(.dark) .doc-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 6px 20px -3px rgba(0, 0, 0, 0.03);
            color: #334155;
        }
        :root:not(.dark) .doc-title { color: #5B0E2D; }
        :root:not(.dark) .doc-text { color: #64748b; }
        :root:not(.dark) .doc-list { color: #334155; }
        :root:not(.dark) .doc-badge {
            background: #5B0E2D;
            color: #ffffff;
            border: 1px solid #7c2a41;
        }

        /* ================= DARK MODE ================= */
        .dark .doc-intro {
            background: linear-gradient(135deg, rgba(74, 14, 23, 0.8) 0%, rgba(43, 4, 20, 0.8) 100%);
            border: 1px solid rgba(183, 110, 121, 0.4);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.4);
            color: #ffffff;
        }
        .dark .doc-card {
            background: rgba(22, 8, 15, 0.7);
            border: 1px solid rgba(183, 110, 121, 0.25);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
            color: #cbd5e1;
        }
        .dark .doc-title { color: #E7D6C8; }
        .dark .doc-text { color: #94a3b8; }
        .dark .doc-list { color: #e2e8f0; }
        .dark .doc-badge {
            background: #5B0E2D;
            color: #E7D6C8;
            border: 1px solid #B76E79;
        }
    </style>

    <div style="display: flex; flex-direction: column; gap: 24px; max-width: 900px;">

        {{-- INTRO BANNER --}}
        <div class="doc-intro" style="border-radius: 18px; padding: 26px 30px;">
            <h2 class="doc-title" style="font-size: 22px; font-weight: 800; margin: 0 0 8px 0;">
                Selamat Datang di Panduan BrandPilot AI ✨
            </h2>
            <p class="doc-text" style="font-size: 14px; margin: 0; line-height: 1.6;">
                BrandPilot AI diciptakan untuk pebisnis yang ingin mendominasi visibilitas di seluruh media sosial secara konsisten tanpa kehilangan waktu untuk mengurus satu per satu.
            </p>
        </div>

        {{-- LANGKAH 1 --}}
        <div class="doc-card" style="border-radius: 16px; padding: 24px 28px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <span class="doc-badge" style="font-weight: 800; width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 14px;">1</span>
                <h3 class="doc-title" style="font-size: 17px; font-weight: 700; margin: 0;">Hubungkan Akun di Menu "Integrations"</h3>
            </div>
            <p class="doc-text" style="font-size: 13.5px; line-height: 1.6; margin: 0 0 0 44px;">
                Buka menu <b>Integrations</b> di panel sebelah kiri. Klik tombol <i>Hubungkan via Composio</i> untuk mengotorisasi Facebook Page, Instagram Professional, dan Channel YouTube Anda menggunakan jalur resmi OAuth tanpa membagikan kata sandi pribadi Anda.
            </p>
        </div>

        {{-- LANGKAH 2 --}}
        <div class="doc-card" style="border-radius: 16px; padding: 24px 28px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <span class="doc-badge" style="font-weight: 800; width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 14px;">2</span>
                <h3 class="doc-title" style="font-size: 17px; font-weight: 700; margin: 0;">Atur Jadwal & Multimodal AI di Menu "Setting"</h3>
            </div>
            <p class="doc-text" style="font-size: 13.5px; line-height: 1.6; margin: 0 0 10px 44px;">
                Tentukan perilaku otomatisasi konten Anda:
            </p>
            <ul class="doc-list" style="margin: 0 0 0 60px; font-size: 13.5px; line-height: 1.7;">
                <li><b>Profil TikTok:</b> Masukkan URL profil TikTok Anda dan centang konfirmasi hak cipta kepemilikan video.</li>
                <li><b>Distribusi otomatis:</b> Setiap video baru masuk antrean dan tayang pada pemeriksaan berikutnya — paling lambat ±30 menit pada 08.00–20.00 WIB, dan ±2 jam pada 20.00–08.00 WIB.</li>
                <li><b>Apify Token:</b> wajib diisi (lihat langkah 3) agar video baru bisa terdeteksi.</li>
                <li><b>Gemini Multimodal AI:</b> AI akan otomatis "menonton" video atau "mengamati" foto carousel Anda untuk menghasilkan <b>Judul</b> dan <b>Deskripsi</b> yang sangat relevan.</li>
            </ul>
        </div>

        {{-- LANGKAH 3 --}}
        <div class="doc-card" style="border-radius: 16px; padding: 24px 28px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <span class="doc-badge" style="font-weight: 800; width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 14px;">3</span>
                <h3 class="doc-title" style="font-size: 17px; font-weight: 700; margin: 0;">Ambil &amp; Pasang Apify Token</h3>
            </div>
            <p class="doc-text" style="font-size: 13.5px; line-height: 1.6; margin: 0 0 12px 44px;">
                BrandPilot memakai akun Apify milik Anda sendiri untuk memeriksa video baru di profil TikTok.
                Gratis, tanpa kartu kredit, dan kuotanya tidak dibatasi BrandPilot.
            </p>
            <ol class="doc-list" style="margin: 0 0 14px 60px; font-size: 13.5px; line-height: 1.7;">
                <li>Buka <a href="https://apify.com" target="_blank" rel="noopener" style="color:#7c2a41;font-weight:700;text-decoration:underline;">apify.com</a> → <b>Sign up</b> (email atau Google).</li>
                <li>Buka <a href="https://console.apify.com/actors/integrations" target="_blank" rel="noopener" style="color:#7c2a41;font-weight:700;text-decoration:underline;">console.apify.com/actors/integrations</a>.</li>
                <li>Klik tombol <b>API</b> di <b>pojok kanan atas</b> halaman, lalu <b>Copy</b> tokennya.</li>
                <li>Tempel di menu <b>Setting → Apify Token</b>, tekan <b>Simpan Perubahan</b>. Token langsung diverifikasi ke Apify.</li>
            </ol>
            <figure style="margin: 0 0 10px 44px;">
                <img src="{{ asset('images/tutorial/apify-token.jpg') }}"
                     alt="Halaman Integrations Apify dengan tombol API di pojok kanan atas"
                     style="width:100%; max-width:760px; border-radius:12px; border:1px solid #e7e5e4; box-shadow:0 6px 18px rgba(0,0,0,0.08);">
                <figcaption class="doc-text" style="font-size:12px;margin-top:8px;">
                    Tombol <b>API</b> di kanan atas halaman Integrations — tempat token diambil.
                </figcaption>
            </figure>
            <p class="doc-text" style="font-size:12.5px; line-height:1.6; margin: 0 0 0 44px;">
                Kuota free plan Apify $5/bulan. Pemakaian BrandPilot ±$3,78/bulan (30 pemeriksaan/hari), jadi masih di dalam kuota.
            </p>
        </div>

        {{-- LANGKAH 4 --}}
        <div class="doc-card" style="border-radius: 16px; padding: 24px 28px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <span class="doc-badge" style="font-weight: 800; width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 14px;">4</span>
                <h3 class="doc-title" style="font-size: 17px; font-weight: 700; margin: 0;">Pembayaran & Perpanjangan Otomatis via Lynk.id</h3>
            </div>
            <p class="doc-text" style="font-size: 13.5px; line-height: 1.6; margin: 0 0 0 44px;">
                Seluruh transaksi diproses instan melalui Lynk.id. Saat pembayaran Anda selesai, sistem secara otomatis membaca notifikasi transaksi dan memperpanjang masa aktif akun Anda tanpa perlu konfirmasi manual.
            </p>
        </div>

    </div>
</x-filament-panels::page>
