{{-- resources/views/filament/pwa-sidebar-button.blade.php --}}

<div x-data="{
    deferredPrompt: null,
    isInstalled: false,
    isPushEnabled: Notification.permission === 'granted',
    vapidPublicKey: '{{ config('webpush.vapid.public_key') ?: env('VAPID_PUBLIC_KEY') }}',

    init() {
        if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
            this.isInstalled = true;
        }

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
        });

        window.addEventListener('appinstalled', () => {
            this.isInstalled = true;
            this.deferredPrompt = null;
        });
    },

    async installApp() {
        if (this.deferredPrompt) {
            this.deferredPrompt.prompt();
            const { outcome } = await this.deferredPrompt.userChoice;
            if (outcome === 'accepted') this.isInstalled = true;
            this.deferredPrompt = null;
        }
    },

    async enableNotifications() {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            alert('Browser ini tidak mendukung Push Notification.');
            return;
        }

        if (!this.vapidPublicKey) {
            alert('Kunci VAPID belum terkonfigurasi di server (.env).');
            return;
        }

        const permission = await Notification.requestPermission();
        if (permission !== 'granted') {
            alert('Izin notifikasi ditolak di browser. Silakan klik ikon gembok di sebelah URL browser dan izinkan notifikasi.');
            return;
        }

        try {
            const reg = await navigator.serviceWorker.ready;
            
            // Berlangganan ke Push Service Browser
            const sub = await reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(this.vapidPublicKey)
            });

            // Kirim token ke backend Laravel
            const res = await fetch('{{ route('webpush.subscribe') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json', // KUNCI: Wajib minta format JSON
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'same-origin', // KUNCI: Bawa sesi login user
                body: JSON.stringify(sub)
            });

            const textResponse = await res.text();
            let json;
            try {
                json = JSON.parse(textResponse);
            } catch (err) {
                console.error('Server non-JSON response:', textResponse);
                alert('Server mengembalikan respon error (HTTP ' + res.status + '). Cek Console F12.');
                return;
            }

            if (json.success) {
                this.isPushEnabled = true;
                alert('🎉 Notifikasi Push BrandPilot AI Berhasil Diaktifkan!');
            } else {
                alert('Gagal mendaftar: ' + (json.error || json.message));
            }
        } catch (e) {
            console.error('Push error:', e);
            alert('Gagal mendaftarkan notifikasi: ' + e.message);
        }
    },

    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }
}" 
style="padding: 12px 14px; margin-top: auto; border-top: 1px solid var(--bp-border); background: inherit; display: flex; flex-direction: column; gap: 8px;">

    {{-- Tombol 1: Aktifkan Push Notification --}}
    <button type="button" 
            x-show="!isPushEnabled"
            @click="enableNotifications()"
            style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 9px 12px; border-radius: 10px; font-size: 11.5px; font-weight: 700; background: rgba(212, 175, 55, 0.15); color: #B76E79; border: 1px solid rgba(212, 175, 55, 0.4); cursor: pointer; transition: all 0.2s;">
        <span>🔔</span>
        <span>Aktifkan Notifikasi Push</span>
    </button>

    {{-- Tombol 2: Install Aplikasi PWA --}}
    <button type="button" 
            x-show="!isInstalled"
            @click="installApp()"
            style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 9px 12px; border-radius: 10px; font-size: 11.5px; font-weight: 700; background: linear-gradient(135deg, #7c2a41 0%, #5b0e2d 100%); color: #ffffff !important; border: 1px solid #B76E79; box-shadow: 0 4px 15px rgba(91,14,45,0.35); cursor: pointer; transition: all 0.2s;">
        <span>📲</span>
        <span>Install BrandPilot App</span>
    </button>
</div>