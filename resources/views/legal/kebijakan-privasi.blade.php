@extends('legal.layout')

@section('title', 'Kebijakan Privasi — BrandPilot AI')
@section('description', 'Bagaimana BrandPilot AI mengumpulkan, menggunakan, menyimpan, dan melindungi data Anda.')
@section('eyebrow', 'Privasi & Data')
@section('heading', 'Kebijakan Privasi')
@section('lede', 'Dokumen ini menjelaskan data apa yang kami proses saat Anda menggunakan BrandPilot AI, mengapa data itu diperlukan, siapa saja yang terlibat, dan hak apa yang Anda miliki atas data tersebut.')
@section('updated', '22 Oktober 2026')

@section('toc')
    <a href="#ringkasan">1. Ringkasan</a>
    <a href="#data">2. Data yang kami kumpulkan</a>
    <a href="#penggunaan">3. Cara kami menggunakan data</a>
    <a href="#dasar-hukum">4. Dasar hukum</a>
    <a href="#pihak-ketiga">5. Pihak ketiga</a>
    <a href="#retensi">6. Penyimpanan &amp; retensi</a>
    <a href="#keamanan">7. Keamanan</a>
    <a href="#hak">8. Hak Anda</a>
    <a href="#cookie">9. Cookie</a>
    <a href="#anak">10. Anak di bawah umur</a>
    <a href="#perubahan">11. Perubahan</a>
    <a href="#kontak">12. Hubungi kami</a>
@endsection

@section('content')
    <h2 id="ringkasan"><span class="num">01</span>Ringkasan</h2>
    <p>
        BrandPilot AI adalah layanan autopilot yang memantau profil TikTok yang Anda daftarkan, lalu
        mendistribusikan video baru ke Instagram, Facebook, dan YouTube sesuai pengaturan workspace Anda.
        Untuk menjalankan fungsi itu, kami perlu memproses sejumlah data — namun hanya data yang benar-benar
        dibutuhkan untuk distribusi konten, keamanan akun, dan penagihan.
    </p>
    <div class="callout">
        <strong>Kami tidak menjual data Anda.</strong> Data hanya dibagikan kepada penyedia layanan yang
        diperlukan agar fitur berjalan (Composio, Google Gemini, Apify, Lynk.id, dan platform sosial yang
        Anda hubungkan), atau jika diwajibkan hukum.
    </div>

    <h2 id="data"><span class="num">02</span>Data yang kami kumpulkan</h2>
    <h3>a. Data akun dan workspace</h3>
    <ul>
        <li>Nama, alamat email, dan kata sandi (disimpan dalam bentuk hash) saat Anda mendaftar.</li>
        <li>Nama workspace/tenant, peran pengguna, dan pengaturan layanan.</li>
        <li>Riwayat langganan, paket aktif, dan status pembayaran.</li>
    </ul>

    <h3>b. Data konten dan integrasi sosial</h3>
    <ul>
        <li>Username TikTok yang Anda daftarkan, ID video, judul, caption, serta URL media hasil unduhan.</li>
        <li>Koneksi akun Facebook Page, Instagram Business, dan YouTube melalui Composio — berupa ID koneksi
            dan nama akun. Kami <strong>tidak</strong> menyimpan kata sandi akun sosial media Anda.</li>
        <li>ID postingan hasil distribusi di tiap platform, status, dan waktu tayang.</li>
    </ul>

    <h3>c. Token layanan pihak ketiga</h3>
    <p>
        Apify Token yang Anda isi sendiri di halaman Setting disimpan dalam keadaan <strong>terenkripsi</strong>
        (encrypted cast) dan hanya didekripsi saat layanan melakukan pemeriksaan video. Token tidak pernah
        ditampilkan kembali secara penuh di antarmuka.
    </p>

    <h3>d. Data pembayaran</h3>
    <p>
        Pembayaran diproses oleh Lynk.id. Kami hanya menerima status transaksi, nominal, dan referensi
        pesanan. <strong>Kami tidak menyimpan nomor kartu, CVV, atau kredensial perbankan Anda.</strong>
    </p>

    <h3>e. Data teknis</h3>
    <ul>
        <li>Alamat IP, jenis perangkat/browser, dan waktu akses untuk keamanan serta diagnostik.</li>
        <li>Log aplikasi (misalnya kegagalan API platform) yang dipakai untuk memperbaiki layanan.</li>
    </ul>

    <h2 id="penggunaan"><span class="num">03</span>Cara kami menggunakan data</h2>
    <ul>
        <li><strong>Menjalankan layanan</strong> — mendeteksi video TikTok baru dan mendistribusikannya ke platform yang Anda hubungkan.</li>
        <li><strong>Menyusun caption</strong> — video dan caption dikirim ke Google Gemini untuk diracik menjadi judul/deskripsi yang sesuai.</li>
        <li><strong>Menjaga keamanan</strong> — mendeteksi penyalahgunaan, mencegah duplikasi posting, dan memverifikasi kepemilikan workspace.</li>
        <li><strong>Menagih langganan</strong> — memverifikasi status pembayaran dan memperpanjang masa aktif.</li>
        <li><strong>Memberi notifikasi</strong> — mengirim pemberitahuan saat konten berhasil terbit atau ketika koneksi platform kedaluwarsa.</li>
        <li><strong>Meningkatkan layanan</strong> — menganalisis log kegagalan secara agregat, tanpa mengungkap konten pribadi Anda.</li>
    </ul>

    <h2 id="dasar-hukum"><span class="num">04</span>Dasar hukum pengolahan</h2>
    <p>
        Kami memproses data berdasarkan: (a) <strong>pelaksanaan perjanjian</strong> layanan yang Anda gunakan;
        (b) <strong>persetujuan</strong> Anda saat menghubungkan akun sosial media dan mengaktifkan distribusi otomatis;
        (c) <strong>kewajiban hukum</strong>, termasuk pencatatan transaksi; dan (d) <strong>kepentingan sah</strong>
        untuk menjaga keamanan serta keandalan layanan. Pengolahan mengacu pada Undang-Undang Perlindungan Data
        Pribadi Indonesia (UU No. 27 Tahun 2022).
    </p>

    <h2 id="pihak-ketiga"><span class="num">05</span>Pembagian data dengan pihak ketiga</h2>
    <p>Data hanya dibagikan sebatas yang diperlukan oleh masing-masing layanan:</p>
    <table>
        <tr><th>Pihak</th><th>Data yang diproses</th><th>Tujuan</th></tr>
        <tr><td>TikTok</td><td>Username publik, metadata video publik</td><td>Deteksi video baru</td></tr>
        <tr><td>Composio</td><td>ID koneksi akun sosial</td><td>Menerbitkan posting ke FB/IG/YouTube</td></tr>
        <tr><td>Google Gemini</td><td>Video/audio dan caption</td><td>Penyusunan judul &amp; deskripsi otomatis</td></tr>
        <tr><td>Apify</td><td>Username TikTok, token milik Anda</td><td>Pengambilan metadata video</td></tr>
        <tr><td>Lynk.id</td><td>Data pesanan &amp; status pembayaran</td><td>Pemrosesan transaksi langganan</td></tr>
        <tr><td>Penyedia server</td><td>Log teknis, basis data terenkripsi</td><td>Hosting dan keamanan layanan</td></tr>
    </table>
    <p>
        Setiap pihak ketiga memiliki kebijakan privasi sendiri. Kami memilih penyedia yang menyediakan
        standar keamanan wajar, namun tidak dapat mengendalikan sepenuhnya praktik internal mereka.
    </p>

    <h2 id="retensi"><span class="num">06</span>Penyimpanan dan retensi</h2>
    <ul>
        <li><strong>Data workspace &amp; konten</strong> disimpan selama langganan aktif, lalu dihapus atau dianonimkan paling lambat 30 hari setelah akun ditutup.</li>
        <li><strong>Token integrasi</strong> dihapus segera saat Anda memutus koneksi atau menghapus workspace.</li>
        <li><strong>Log teknis</strong> disimpan bergulir maksimal 30 hari untuk keperluan diagnostik dan keamanan.</li>
        <li><strong>Data transaksi</strong> disimpan sesuai kewajiban hukum perpajakan dan akuntansi di Indonesia.</li>
        <li><strong>File media sementara</strong> (hasil unduhan untuk distribusi) bersifat sementara dan tidak dijadikan arsip permanen.</li>
    </ul>

    <h2 id="keamanan"><span class="num">07</span>Keamanan data</h2>
    <p>
        Kami menerapkan enkripsi pada token sensitif, pembatasan akses berbasis peran, koneksi HTTPS,
        pencatatan aktivitas administrator, serta pemisahan data antar-tenant. Meski demikian, tidak ada
        sistem yang sepenuhnya bebas risiko; Anda juga bertanggung jawab menjaga kerahasiaan kata sandi
        dan mengaktifkan koneksi hanya pada akun yang Anda miliki.
    </p>

    <h2 id="hak"><span class="num">08</span>Hak Anda</h2>
    <ul>
        <li><strong>Akses</strong> — meminta salinan data akun dan riwayat konten yang kami simpan.</li>
        <li><strong>Koreksi</strong> — memperbaiki data yang tidak akurat melalui halaman pengaturan.</li>
        <li><strong>Penghapusan</strong> — meminta penghapusan akun beserta data terkait.</li>
        <li><strong>Pencabutan izin</strong> — memutus koneksi Facebook, Instagram, atau YouTube kapan saja.</li>
        <li><strong>Keberatan</strong> — menolak pengolahan tertentu, misalnya caption otomatis AI.</li>
        <li><strong>Portabilitas</strong> — meminta ekspor data dalam format yang wajar.</li>
    </ul>
    <p>
        Permintaan dapat dikirim ke <a href="mailto:info@brandpilot.my.id">info@brandpilot.my.id</a>.
        Kami menanggapi paling lambat 14 hari kerja setelah verifikasi identitas.
    </p>

    <h2 id="cookie"><span class="num">09</span>Cookie dan teknologi serupa</h2>
    <p>
        Kami menggunakan cookie esensial untuk menjaga sesi login, preferensi tampilan, dan keamanan formulir.
        Kami tidak memakai cookie iklan pihak ketiga untuk melacak Anda di luar layanan. Anda dapat menghapus
        cookie melalui browser, namun sebagian fitur (termasuk login) tidak akan berfungsi tanpanya.
    </p>

    <h2 id="anak"><span class="num">10</span>Anak di bawah umur</h2>
    <p>
        Layanan ini ditujukan untuk pengguna berusia 18 tahun ke atas atau badan usaha. Kami tidak dengan sengaja
        mengumpulkan data anak di bawah umur. Jika Anda mengetahui adanya data semacam itu, hubungi kami agar
        dapat segera dihapus.
    </p>

    <h2 id="perubahan"><span class="num">11</span>Perubahan kebijakan</h2>
    <p>
        Kebijakan ini dapat diperbarui seiring perubahan fitur atau regulasi. Versi terbaru selalu tersedia di
        halaman ini dengan tanggal pembaruan di bagian atas. Perubahan material akan diberitahukan melalui email
        atau notifikasi di dalam aplikasi.
    </p>

    <h2 id="kontak"><span class="num">12</span>Hubungi kami</h2>
    <p>
        Pengendali data: <strong>BrandPilot AI</strong> — Bojonegoro, Jawa Timur, Indonesia.<br>
        Email: <a href="mailto:info@brandpilot.my.id">info@brandpilot.my.id</a>
    </p>
    <p class="pill">Dokumen ini bukan nasihat hukum</p>
@endsection
