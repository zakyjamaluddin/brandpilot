{{-- PWA Manifest & Meta Tags --}}
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#5B0E2D">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="BrandPilot AI">
<link rel="apple-touch-icon" href="https://img.icons8.com/fluency/192/compass--v1.png">

{{-- Registrasi Service Worker --}}
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => console.log('BrandPilot PWA ServiceWorker aktif!'))
                .catch(err => console.error('PWA SW Gagal:', err));
        });
    }
</script>