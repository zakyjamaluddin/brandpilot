<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background: #fdfafb; color: #2d3748; margin: 0; padding: 20px; }
        .card { max-width: 560px; margin: 0 auto; background: #ffffff; border: 1px solid rgba(183,110,121,0.25); border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(91,14,45,0.08); }
        .header { background: linear-gradient(135deg, #5B0E2D 0%, #3B081C 100%); padding: 32px 24px; text-align: center; color: #ffffff; }
        .body { padding: 32px 28px; }
        .info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; margin: 20px 0; }
        .btn { display: inline-block; background: linear-gradient(135deg, #7C2A41 0%, #5B0E2D 100%); color: #ffffff !important; padding: 14px 28px; border-radius: 30px; text-decoration: none; font-weight: bold; margin-top: 10px; }
        .footer { font-size: 11px; color: #a0aec0; text-align: center; padding: 20px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1 style="margin:0; font-size: 22px; color: #E7D6C8;">BrandPilot AI ✨</h1>
            <p style="margin:6px 0 0; font-size: 13px; color: #ffffff; opacity: 0.9;">Perpanjangan Masa Aktif Berhasil</p>
        </div>
        <div class="body">
            <h2 style="font-size: 18px; color: #1a202c;">Halo, {{ $user->name }}!</h2>
            <p style="font-size: 14px; line-height: 1.6; color: #4a5568;">
                Pembayaran perpanjangan langganan <b>Paket {{ $durationMonths }} Bulan</b> via Lynk.id telah berhasil diverifikasi.
            </p>

            <div class="info-box">
                <p style="margin: 0 0 6px; font-size: 13px; color: #4a5568;">Workspace: <b>{{ $team->name }}</b></p>
                <p style="margin: 0; font-size: 13px; color: #2b6cb0;">Masa Aktif Baru Hingga: <b style="font-size: 15px; color: #5B0E2D;">{{ $team->subscription_expires_at->format('d M Y') }}</b></p>
            </div>

            <p style="font-size: 13px; color: #4a5568;">
                Seluruh sistem autopilot crossposting Anda tetap berjalan tanpa hambatan.
            </p>

            <div style="text-align: center; margin-top: 24px;">
                <a href="{{ url('/admin') }}" class="btn">Buka Panel BrandPilot →</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} BrandPilot AI. Semua Hak Cipta Dilindungi.
        </div>
    </div>
</body>
</html>