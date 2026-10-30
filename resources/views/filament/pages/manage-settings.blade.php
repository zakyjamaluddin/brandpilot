<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
            <button type="submit" 
                    style="background: linear-gradient(135deg, #7c2a41 0%, #5b0e2d 100%); color: #fff; border: 1px solid #B76E79; padding: 10px 24px; border-radius: 10px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 15px rgba(91,14,45,0.4);">
                Simpan Perubahan
            </button>
        </div>
    </form>

    {{-- MODAL: Tutorial Apify Token --}}
    <x-filament::modal id="apify-tutorial" width="3xl" icon="heroicon-o-key" teleport="body">
        <x-slot name="heading">
            Cara Mendapatkan Apify Token
        </x-slot>

        <x-slot name="description">
            Gratis, tanpa kartu kredit. Butuh waktu sekitar 2 menit.
        </x-slot>

        <div style="font-size: 13.5px; line-height: 1.7; color: #44403c;">
            <ol style="margin: 0 0 16px 20px; padding: 0;">
                <li style="margin-bottom: 8px;">
                    Buka <a href="https://apify.com" target="_blank" rel="noopener" style="color:#7c2a41;font-weight:700;text-decoration:underline;">apify.com</a>,
                    klik <b>Sign up</b>, lalu daftar dengan email atau akun Google.
                </li>
                <li style="margin-bottom: 8px;">
                    Verifikasi email, lalu masuk ke <b>console.apify.com</b>.
                </li>
                <li style="margin-bottom: 8px;">
                    Di sidebar kiri, buka menu <b>Integrations</b> — atau langsung ke
                    <a href="https://console.apify.com/actors/integrations" target="_blank" rel="noopener" style="color:#7c2a41;font-weight:700;text-decoration:underline;">console.apify.com/actors/integrations</a>.
                </li>
                <li style="margin-bottom: 8px;">
                    Klik tombol <b>API</b> di <b>pojok kanan atas</b> halaman (sejajar judul "Integrations").
                    Lihat gambar di bawah.
                </li>
                <li style="margin-bottom: 8px;">
                    Dialog token muncul. Klik <b>Copy</b>, tempel di kolom <b>Apify Token</b> di halaman ini,
                    lalu tekan <b>Simpan Perubahan</b>. Sistem otomatis mengecek token ke Apify; kalau salah akan ditolak.
                </li>
            </ol>

            <figure style="margin: 0 0 16px;">
                <img src="{{ asset('images/tutorial/apify-token.jpg') }}"
                     alt="Halaman Integrations di Apify Console, tombol API ada di pojok kanan atas"
                     style="width:100%; border-radius:12px; border:1px solid #e7e5e4; box-shadow:0 6px 18px rgba(0,0,0,0.08);">
                <figcaption style="font-size:12px;color:#78716c;margin-top:8px;">
                    Tombol <b>API</b> di kanan atas halaman Integrations — tempat token diambil.
                </figcaption>
            </figure>

            <div style="padding:12px 14px;border-radius:10px;background:#fdf4f6;border:1px solid #e9c9d1;color:#5b0e2d;">
                <b>Kuota:</b> free plan Apify memberi kredit $5/bulan. BrandPilot memeriksa video baru 30 kali sehari,
                sekitar <b>$3,78/bulan</b> — masih di dalam kuota. Kalau Anda posting lebih sering atau menambah akun,
                pemakaian itu ditanggung akun Apify Anda sendiri.
            </div>

            <div style="margin-top:16px;">
                <a href="https://console.apify.com/actors/integrations" target="_blank" rel="noopener"
                   style="display:inline-block;padding:10px 18px;border-radius:9px;background:linear-gradient(135deg,#7c2a41 0%,#5b0e2d 100%);color:#fff;font-weight:700;text-decoration:none;border:1px solid #B76E79;">
                    Buka Apify Console
                </a>
            </div>
        </div>

        <x-slot name="footer">
            <x-filament::button color="gray" x-on:click="$dispatch('close-modal', { id: 'apify-tutorial' })">
                Tutup
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
</x-filament-panels::page>