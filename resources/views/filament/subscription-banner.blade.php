@php
    $team = \Filament\Facades\Filament::getTenant();
@endphp

@if($team && $team->subscription_expires_at)
    @php
        $now = now();
        $isExpired = $team->subscription_status === 'expired' || $team->subscription_expires_at->isPast();
        $daysLeft = (int) $now->diffInDays($team->subscription_expires_at, false);
        $subscriptionUrl = route('filament.admin.pages.manage-subscription', ['tenant' => $team->slug]);
    @endphp

    {{-- KONDISI 1: LANGGANAN SUDAH HABIS (MERAH BURGUNDY DARURAT) --}}
    @if($isExpired)
        <div style="margin-bottom: 24px; padding: 16px 20px; border-radius: 14px; background: linear-gradient(135deg, #4A0E17 0%, #2B0414 100%); border: 1px solid #B76E79; box-shadow: 0 4px 20px rgba(91,14,45,0.3); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; color: #ffffff;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 22px;">🚨</span>
                <div>
                    <h4 style="font-size: 14px; font-weight: 800; margin: 0; color: #F4ECE5;">Masa Langganan Anda Telah Habis!</h4>
                    <p style="font-size: 12.5px; margin: 2px 0 0 0; color: #D1D5DB;">Seluruh sistem autopilot crossposting dan menu fitur dinonaktifkan sementara.</p>
                </div>
            </div>
            <a href="{{ $subscriptionUrl }}" 
               style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; border-radius: 9999px; font-size: 12px; font-weight: 800; background: linear-gradient(135deg, #B76E79 0%, #C58F72 100%); color: #ffffff !important; text-decoration: none; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
                ⚡ Perpanjang Sekarang via Lynk.id →
            </a>
        </div>

    {{-- KONDISI 2: SISA 7 HARI LAGI (KUNING EMAS / AMBER PERINGATAN) --}}
    @elseif($daysLeft <= 7 && $daysLeft >= 0)
        <div style="margin-bottom: 24px; padding: 14px 20px; border-radius: 14px; background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%); border: 1px solid #F59E0B; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.15); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; color: #92400E;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 20px;">⚠️</span>
                <div>
                    <h4 style="font-size: 13.5px; font-weight: 800; margin: 0; color: #78350F;">
                        Pengingat: Masa Aktif Tersisa {{ $daysLeft == 0 ? 'Hari Ini Terakhir' : $daysLeft . ' Hari Lagi' }}!
                    </h4>
                    <p style="font-size: 12px; margin: 2px 0 0 0; color: #92400E;">Masa aktif Anda berakhir pada {{ $team->subscription_expires_at->format('d M Y, H:i') }}. Perpanjang agar postingan tidak terhenti.</p>
                </div>
            </div>
            <a href="{{ $subscriptionUrl }}" 
               style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 9999px; font-size: 11.5px; font-weight: 800; background: #D97706; color: #ffffff !important; text-decoration: none; box-shadow: 0 2px 8px rgba(217,119,6,0.3);">
                Perpanjang Langganan →
            </a>
        </div>
    @endif
@endif