@extends('legal.layout')

@section('title', 'Syarat & Ketentuan — BrandPilot AI')
@section('description', 'Ketentuan penggunaan layanan BrandPilot AI: langganan, distribusi otomatis, koneksi platform, dan batasan tanggung jawab.')
@section('eyebrow', 'Ketentuan Layanan')
@section('heading', 'Syarat & Ketentuan')
@section('lede', 'Dengan membuat akun atau menggunakan BrandPilot AI, Anda menyetujui ketentuan di bawah ini. Ketentuan berlaku untuk seluruh workspace yang Anda kelola.')
@section('updated', '22 Oktober 2026')

@section('toc')
    <a href="#penerimaan">1. Penerimaan ketentuan</a>
    <a href="#akun">2. Akun &amp; workspace</a>
    <a href="#langganan">3. Langganan &amp; pembayaran</a>
    <a href="#layanan">4. Cara kerja layanan</a>
    <a href="#satu-video">5. Satu video per siklus</a>
    <a href="#apify">6. Apify Token</a>
    <a href="#koneksi">7. Koneksi platform</a>
    <a href="#konten">8. Konten &amp; hak</a>
    <a href="#larangan">9. Penggunaan terlarang</a>
    <a href="#tanggung-jawab">10. Batasan tanggung jawab</a>
    <a href="#penghentian">11. Penghentian</a>
    <a href="#perubahan">12. Perubahan</a>
    <a href="#hukum">13. Hukum berlaku</a>
    <a href="#kontak">14. Kontak</a>
@endsection

@section('content')
    <h2 id="penerimaan"><span class="num">01</span>Penerimaan ketentuan</h2>
    <p>
        BrandPilot AI (“Layanan”) disediakan untuk membantu Anda mendistribusikan konten TikTok ke Instagram,
        Facebook, dan YouTube. Dengan mendaftar, menghubungkan akun, atau menggunakan Layanan, Anda menyatakan
        telah membaca dan menyetujui ketentuan ini beserta <a href="/kebijakan-privasi">Kebijakan Privasi</a>.
        Jika Anda tidak setuju, mohon berhenti menggunakan Layanan.
    </p>

    <h2 id="akun"><span class="num">02</span>Akun &amp; workspace</h2>
    <ul>
        <li>Anda wajib memberikan informasi yang akurat dan menjaga kerahasiaan kredensial login.</li>
        <li>Satu workspace mewakili satu brand/tenant dengan pengaturan, koneksi, dan kuota tersendiri.</li>
        <li>Anda bertanggung jawab atas seluruh aktivitas yang terjadi melalui akun dan workspace Anda.</li>
        <li>Anda harus berusia minimal 18 tahun atau mewakili badan usaha yang sah.</li>
    </ul>

    <h2 id="langganan"><span class="num">03</span>Langganan &amp; pembayaran</h2>
    <p>
        Layanan bersifat berlangganan. Pembayaran diproses melalui Lynk.id dan mengaktifkan workspace selama
        periode yang dipilih.
    </p>
    <table>
        <tr><th>Paket</th><th>Harga</th><th>Masa aktif</th></tr>
        <tr><td>Bulanan</td><td>Rp39.000</td><td>30 hari</td></tr>
        <tr><td>6 Bulan</td><td>Rp189.000</td><td>180 hari</td></tr>
        <tr><td>12 Bulan</td><td>Rp297.000</td><td>365 hari</td></tr>
    </table>
    <ul>
        <li>Harga yang berlaku adalah harga yang tampil pada halaman checkout saat transaksi dilakukan.</li>
        <li>Langganan tidak diperpanjang otomatis; Anda akan diingatkan sebelum masa aktif berakhir.</li>
        <li>Kegagalan pembayaran menyebabkan workspace dibatasi hingga pembayaran berhasil.</li>
        <li>Permintaan pengembalian dana dipertimbangkan untuk kasus gangguan layanan yang terbukti dan dilaporkan maksimal 7 hari setelah transaksi.</li>
    </ul>

    <h2 id="layanan"><span class="num">04</span>Cara kerja layanan &amp; jeda tayang</h2>
    <div class="callout">
        <strong>BrandPilot tidak menjanjikan tayang seketika.</strong> Video baru diproses pada siklus
        pemeriksaan berikutnya, dengan jeda maksimum sebagai berikut.
    </div>
    <table>
        <tr><th>Jendela waktu (WIB)</th><th>Interval pemeriksaan</th><th>Jeda tayang maksimum</th></tr>
        <tr><td>08.00 – 20.00</td><td>Setiap 30 menit</td><td>± 30 menit</td></tr>
        <tr><td>20.00 – 08.00</td><td>Setiap 2 jam</td><td>± 2 jam</td></tr>
    </table>
    <p>
        Jeda dihitung dari waktu video tersedia di profil TikTok Anda, bukan dari waktu Anda mengunggah ke
        TikTok. Keterlambatan dapat bertambah apabila layanan pihak ketiga (TikTok, Facebook, Instagram,
        YouTube, atau penyedia AI) sedang mengalami gangguan atau membatasi permintaan.
    </p>

    <h2 id="satu-video"><span class="num">05</span>Satu video per siklus pemeriksaan</h2>
    <p>
        Pada setiap siklus pemeriksaan, BrandPilot hanya mengambil <strong>satu video terbaru</strong>
        (tidak termasuk video yang di-pin). Apabila dalam satu jendela pemeriksaan Anda mengunggah lebih dari
        satu video, <strong>hanya video terbaru yang diproses</strong>; video lain pada jendela tersebut tidak
        dideteksi dan tidak akan diposting otomatis.
    </p>
    <p>
        Video yang tidak terdeteksi tidak hilang dari TikTok Anda — video tersebut tetap ada di akun, namun
        tidak masuk antrean BrandPilot. Jika membutuhkan distribusinya, unggah ulang sebagai video baru atau
        posting secara manual.
    </p>

    <h2 id="apify"><span class="num">06</span>Apify Token &amp; kuota</h2>
    <p>
        Auto-posting <strong>wajib</strong> menggunakan Apify Token milik Anda sendiri yang didaftarkan pada
        halaman Setting workspace. BrandPilot tidak menyediakan token bersama dan tidak bertanggung jawab atas
        kuota, pembatasan, atau pemblokiran akun Apify Anda. Tanpa token yang valid, deteksi video baru tidak
        berjalan sehingga tidak ada posting otomatis.
    </p>
    <ul>
        <li>Kuota gratis Apify memiliki batas bulanan; jika habis, pemeriksaan berhenti sampai kuota pulih atau paket ditingkatkan.</li>
        <li>Penggunaan satu token untuk banyak workspace tidak didukung.</li>
        <li>BrandPilot dapat membatasi laju pemeriksaan untuk menjaga stabilitas layanan.</li>
    </ul>

    <h2 id="koneksi"><span class="num">07</span>Koneksi platform pihak ketiga</h2>
    <ul>
        <li>Koneksi Facebook, Instagram, dan YouTube dibuat melalui Composio dan dapat kedaluwarsa sewaktu-waktu sesuai kebijakan platform.</li>
        <li>Anda bertanggung jawab memastikan akun yang dihubungkan memiliki izin yang diperlukan — misalnya izin <em>MANAGE</em> dan <em>CREATE_CONTENT</em> pada Facebook Page, atau akun Instagram Business yang terhubung ke halaman.</li>
        <li>Ketersediaan fitur distribusi bergantung pada API platform. Perubahan kebijakan atau pembatasan dari platform dapat menghentikan distribusi tanpa pemberitahuan sebelumnya.</li>
        <li>BrandPilot akan mencatat kegagalan dan menandai koneksi yang tidak aktif; Anda perlu menghubungkan ulang untuk melanjutkan.</li>
    </ul>

    <h2 id="konten"><span class="num">08</span>Konten &amp; hak kekayaan intelektual</h2>
    <p>
        Anda menyatakan memiliki hak atas akun TikTok dan seluruh konten yang didaftarkan. Anda memberikan
        BrandPilot lisensi terbatas, non-eksklusif, dan dapat dicabut untuk memproses konten tersebut semata-mata
        guna keperluan distribusi ke kanal yang Anda hubungkan.
    </p>
    <p>
        BrandPilot, termasuk nama, logo, antarmuka, dan kodenya, dilindungi hak kekayaan intelektual dan tidak
        boleh disalin, dijual ulang, atau direkayasa balik tanpa izin tertulis.
    </p>

    <h2 id="larangan"><span class="num">09</span>Penggunaan terlarang</h2>
    <ul>
        <li>Mendistribusikan konten yang melanggar hukum, menyesatkan, mengandung kebencian, atau melanggar hak cipta pihak lain.</li>
        <li>Menggunakan Layanan untuk spam, manipulasi engagement, atau aktivitas yang melanggar ketentuan TikTok, Meta, dan YouTube.</li>
        <li>Mencoba mengakses data workspace lain, menembus sistem, atau mengganggu kestabilan Layanan.</li>
        <li>Menjual kembali, menyewakan, atau membagikan akses Layanan tanpa persetujuan tertulis.</li>
    </ul>
    <p>Pelanggaran dapat menyebabkan penangguhan akun tanpa pengembalian dana.</p>

    <h2 id="tanggung-jawab"><span class="num">10</span>Ketersediaan &amp; batasan tanggung jawab</h2>
    <p>
        Layanan disediakan “sebagaimana adanya” dan “sebagaimana tersedia”. Kami berupaya menjaga keandalan,
        namun tidak menjamin Layanan bebas gangguan, bebas kesalahan, atau sesuai ekspektasi tertentu.
    </p>
    <p>
        Sejauh diizinkan hukum, BrandPilot tidak bertanggung jawab atas kerugian tidak langsung, kehilangan
        keuntungan, kehilangan data, penurunan jangkauan, penghapusan konten, atau tindakan platform pihak
        ketiga. Tanggung jawab maksimum kami atas klaim apa pun dibatasi pada nilai langganan yang Anda bayarkan
        dalam 3 (tiga) bulan terakhir.
    </p>

    <h2 id="penghentian"><span class="num">11</span>Penangguhan &amp; penghentian</h2>
    <p>
        Kami dapat menangguhkan atau menghentikan akses apabila terjadi pelanggaran ketentuan, penyalahgunaan
        layanan, tunggakan pembayaran, atau kewajiban hukum. Anda dapat berhenti menggunakan Layanan kapan saja;
        penghapusan akun dan data mengikuti Kebijakan Privasi.
    </p>

    <h2 id="perubahan"><span class="num">12</span>Perubahan ketentuan</h2>
    <p>
        Ketentuan ini dapat diperbarui sewaktu-waktu. Versi terbaru selalu tersedia di halaman ini dengan
        tanggal pembaruan di bagian atas. Penggunaan Layanan setelah pembaruan berarti Anda menyetujui versi
        yang berlaku.
    </p>

    <h2 id="hukum"><span class="num">13</span>Hukum yang berlaku</h2>
    <p>
        Ketentuan ini diatur oleh hukum Republik Indonesia. Sengketa diselesaikan secara musyawarah terlebih
        dahulu; apabila tidak tercapai kesepakatan, diselesaikan melalui pengadilan yang berwenang di Indonesia.
    </p>

    <h2 id="kontak"><span class="num">14</span>Kontak</h2>
    <p>
        Pertanyaan mengenai ketentuan ini: <a href="mailto:info@brandpilot.my.id">info@brandpilot.my.id</a><br>
        BrandPilot AI — Bojonegoro, Jawa Timur, Indonesia.
    </p>
    <p class="pill">Terakhir diperbarui 22 Oktober 2026</p>
@endsection
